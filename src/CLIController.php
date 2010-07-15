<?php
/**
 * Cli controller
 *
 * PHP Version 5.3.2
 *
 * Copyright (c) 2007-2010, Mayflower GmbH
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Mayflower GmbH nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  PHP_CodeBrowser
 * @package   PHP_CodeBrowser
 * @author    Elger Thiele <elger.thiele@mayflower.de>
 * @author    Simon Kohlmeyer <simon.kohlmeyer@mayflower.de>
 * @copyright 2007-2010 Mayflower GmbH
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.phpunit.de/
 * @since     File available since  0.1.0
 */

if (strpos('@php_dir@', '@php_dir') === false) {
    if (!defined('PHPCB_ROOT_DIR')) {
        define('PHPCB_ROOT_DIR', '@php_dir@/PHP_CodeBrowser');
    }
    if (!defined('PHPCB_TEMPLATE_DIR')) {
        define('PHPCB_TEMPLATE_DIR', '@data_dir@/PHP_CodeBrowser/templates');
    }
} else {
    if (!defined('PHPCB_ROOT_DIR')) {
        define('PHPCB_ROOT_DIR', dirname(__FILE__) . '/../');
    }
    if (!defined('PHPCB_TEMPLATE_DIR')) {
        define('PHPCB_TEMPLATE_DIR', dirname(__FILE__) . '/../templates');
    }
}

require_once dirname(__FILE__) . '/Util/Autoloader.php';
require_once 'PHP/Timer.php';
require_once 'Console/CommandLine.php';
require_once 'Log.php';
require_once 'File/Iterator/Factory.php';

/**
 * CbCLIController
 *
 * @category  PHP_CodeBrowser
 * @package   PHP_CodeBrowser
 * @author    Elger Thiele <elger.thiele@mayflower.de>
 * @author    Michel Hartmann <michel.hartmann@mayflower.de>
 * @author    Simon Kohlmeyer <simon.kohlmeyer@mayflower.de>
 * @copyright 2007-2010 Mayflower GmbH
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.phpunit.de/
 * @since     Class available since  0.1.0
 */
class CbCLIController
{
    /**
     * Path to the Cruise Control input xml file
     *
     * @var string
     */
    private $_logDir;

    /**
     * Path to the code browser html output folder
     *
     * @var string
     */
    private $_htmlOutputDir;

    /**
     * Path to the project source code files
     *
     * @var string
     */
    private $_projectSourceDir;

    /**
     * Array of PCREs. Matching files will not appear in the output.
     *
     * @var Array
     */
    private $_excludeExpressions;

    /**
     * The error plugin classes
     *
     * @var array
     */
    private $_registeredPlugins;

    /**
     * The IOHelper used for filesystem interaction.
     *
     * @var CbIOHelper
     */
    private $_ioHelper;

    /**
     * The pear Log object used for logging.
     *
     * @var Log
     */
    protected $_log;

    /**
     * The constructor
     *
     * Standard setters are initialized
     *
     * @param string $logPath          The (path-to) xml log files
     * @param string $projectSourceDir The project source directory
     * @param string $htmlOutputDir    The html output dir, where new files will
     *                                 be created
     * @param Array  $excludeExpressions
     *                                 A list of PCREs. Files matching will not
     *                                 appear in the output.
     * @param CbIOHelper $ioHelper     The CbIOHelper object to be used for
     *                                 filesystem interaction.
     * @param Log    $log              The pear Log object to use for logging.
     */
    public function __construct($logPath,       $projectSourceDir,
                                $htmlOutputDir, Array $excludeExpressions,
                                $ioHelper, $log)
    {
        $this->_logDir = $logPath;
        $this->_projectSourceDir = $projectSourceDir;
        $this->_htmlOutputDir = $htmlOutputDir;
        $this->_excludeExpressions = $excludeExpressions;
        $this->_ioHelper = $ioHelper;
        $this->_log      = $log;
    }

    /**
     * Setter/adder method for the used plugin classes.
     * For each plugin to use, add it to this array
     *
     * @param mixed $classNames Definition of plugin classes
     *
     * @return void
     */
    public function addErrorPlugins($classNames)
    {
        foreach ((array) $classNames as $className) {
            $this->_registeredPlugins[] = $className;
        }
    }

    /**
     * Main execute function for PHP_CodeBrowser.
     *
     * Following steps are resolved:
     * 1. Clean-up output directory
     * 2. Merge xml log files
     * 3. Generate cbXML file via errorlist from plugins
     * 4. Save the cbErrorList as XML file
     * 5. Generate HTML output from cbXML
     * 6. Copy ressources (css, js, images) from template directory to output
     *
     * @return void
     */
    public function run()
    {
        // clear and create output directory
        if (is_dir($this->_htmlOutputDir)) {
            $this->_ioHelper->deleteDirectory($this->_htmlOutputDir);
        } else if (is_file($this->_htmlOutputDir)) {
            $this->_ioHelper->deleteFile($this->_htmlOutputDir);
        }
        $this->_ioHelper->createDirectory($this->_htmlOutputDir);

        // init needed classes
        $cbViewReview  = new CbViewReview(
            PHPCB_TEMPLATE_DIR,
            $this->_htmlOutputDir,
            $this->_ioHelper
        );

        $sourceHandler = new CbSourceHandler();

        if (isset($this->_logDir)) {
            $cbIssueXml    = new CbIssueXml($this->_log);
            $this->_log->log('Load XML files', PEAR_LOG_DEBUG);

            // merge xml files
            $cbIssueXml->addDirectory($this->_logDir);

            $this->_log->log('Load Plugins', PEAR_LOG_DEBUG);

            // conversion of XML file cc to cb format
            foreach ($this->_registeredPlugins as $className) {
                $sourceHandler->addPlugin(new $className($cbIssueXml));
            }

            $this->_log->log(
                sprintf(
                    'Found %d files with issues.',
                    count($sourceHandler->getFiles())
                ),
                PEAR_LOG_INFO
            );
        }

        if (isset($this->_projectSourceDir)) {
            $sourceHandler->addSourceFiles(
                File_Iterator_Factory::getFileIterator(
                    $this->_projectSourceDir, 'php'
                )
            );
        }

        foreach ($this->_excludeExpressions as $expr) {
            $sourceHandler->excludeMatching($expr);
        }

        $files = $sourceHandler->getFiles();

        // Get the path prefix all files have in common
        $commonPathPrefix = $sourceHandler->getCommonPathPrefix();

        foreach ($files as $file) {
            $this->_log->log(
                sprintf(
                    'Get issues for "...%s"',
                    substr($file->name(), strlen($commonPathPrefix))
                ),
                PEAR_LOG_DEBUG
            );
            $issues = $file->getIssues();

            // @TODO Timer::start() only for logging check performance
            // and remove if neccessary
            PHP_Timer::start();
            $this->_log->log(
                sprintf('Generating source view for [...%s]', $file->name()),
                PEAR_LOG_DEBUG
            );

            $cbViewReview->generate($issues, $file->name(), $commonPathPrefix);

            $this->_log->log(
                sprintf('completed in %s', PHP_Timer::stop()),
                PEAR_LOG_DEBUG
            );
        }

        // Copy needed ressources (eg js libraries) to output directory
        $cbViewReview->copyRessourceFolders();
        $cbViewReview->generateIndex($files);
    }

    /**
     * Main method called by script
     *
     * @return void
     */
    public static function main()
    {
        PHP_Timer::start();

        // register autoloader
        spl_autoload_register(array(new CbAutoloader(), 'autoload'));

        $parser = self::createCommandLineParser();

        try {
            $opts = $parser->parse()->options;
        } catch (Exception $e) {
            $parser->displayError($e->getMessage());
        }

        if (isset($opts['logfile'])) {
            $log = Log::factory('file', $opts['logfile']);
        } else {
            $log = Log::factory('console');
        }

        $errors = self::errorsForOpts($opts);
        if ($errors) {
            foreach ($errors as $e) {
                error_log("Error: $e\n");
            }
            exit(1);
        }

        // init new CLIController
        $controller = new CbCLIController(
            $opts['log'],
            $opts['source'],
            $opts['output'],
            isset($opts['exclude']) ? $opts['exclude'] : array(),
            new CbIOHelper(),
            $log
        );

        $controller->addErrorPlugins(
            array(
                'CbErrorCheckstyle',
                'CbErrorPMD',
                'CbErrorCPD',
                'CbErrorPadawan',
                'CbErrorCoverage',
                'CbErrorCRAP'
            )
        );

        try {
            $controller->run();
        } catch (Exception $e) {
            $log->log(
                sprintf(
                    "PHP-CodeBrowser Error: \n%s\n\n%s",
                    $e->getMessage(),
                    $e->getTraceAsString()
                ),
                PEAR_LOG_ERR
            );
        }

        $log->log(PHP_Timer::resourceUsage(), PEAR_LOG_INFO);
    }

    /**
     * Checks the given options array for errors.
     *
     * @param Array Options as returned by Console_CommandLine->parse()
     *
     * @return Array of String Errormessages.
     */
    private static function errorsForOpts($opts)
    {
        $errors = array();

        if (!isset($opts['log'])) {
            if (!isset($opts['source'])) {
                $errors[] = 'Missing log or source argument.';
            }
        } else if (!file_exists($opts['log'])) {
            $errors[] = 'Log directory does not exist.';
        } else if (!is_dir($opts['log'])) {
            $errors[] = 'Log argument must be a directory, a file was given.';
        }

        if (!isset($opts['output'])) {
            $errors[] = 'Missing output argument.';
        } else if (file_exists($opts['output']) && !is_dir($opts['output'])) {
            $errors[] = 'Ouput argument must be a directory, a file was given.';
        }

        if (isset($opts['source']) && !is_dir($opts['source'])) {
            $errors[] = 'Source argument must be a directory, file given.';
        }

        return $errors;
    }

    /**
     * Creates a Console_CommandLine object to parse options.
     *
     * @return Console_CommandLine
     */
    private static function createCommandLineParser()
    {
        $parser = new Console_CommandLine(
            array(
                'description' => 'A Code browser for PHP files with syntax '
                                    . 'highlighting and colored error-sections '
                                    . 'found by quality assurance tools like '
                                    . 'PHPUnit or PHP_CodeSniffer.',
                'version'     => (strpos('@package_version@', '@') === false)
                                    ? '@package_version@'
                                    : 'from Git'
            )
        );

        $parser->addOption(
            'log',
            array(
                'description' => 'The path to the xml log files, e.g. generated'
                                    . ' from PHPUnit. Either this or --source '
                                    . 'must be given',
                'short_name'  => '-l',
                'long_name'   => '--log'
            )
        );

        $parser->addOption(
            'output',
            array(
                'description' => 'Path to the output folder where generated '
                                    . 'files should be stored.',
                'short_name'  => '-o',
                'long_name'   => '--output'
            )
        );

        $parser->addOption(
            'source',
            array(
                'description' => 'Path to the project source code. Parse '
                                    . 'complete source directory if set, else '
                                    . 'only files found in logs. Either this or'
                                    . ' --log must be given.',
                'short_name'  => '-s',
                'long_name'   => '--source'
            )
        );

        $parser->addOption(
            'exclude',
            array(
                'description' => 'Excludes all files matching the given PCRE. '
                                    . 'This is done after pulling the files in '
                                    . 'the source dir in if one is given. Can '
                                    . 'be given multiple times. Note that the '
                                    . 'match is run against '
                                    . 'absolute filenames.',
                'short_name'   => '-e',
                'long_name'    => '--exclude',
                'action'       => 'StoreArray'
            )
        );

        $parser->addOption(
            'logfile',
            array(
                'description' => 'Path of the file to use for logging the '
                                    . 'output. If not given, stdout '
                                    . 'will be used.',
                'long_name'   => '--logfile'
            )
        );

        $parser->addOption(
            'loglevel',
            array(
                'description'     => 'Specify the log level. Defaults to DEBUG '
                                    . 'in this release.',
                'long_name'       => '--loglevel',
                'choices'         => array('debug', 'info', 'warn', 'error'),
                'add_list_option' => true
            )
        );

        return $parser;
    }
}
