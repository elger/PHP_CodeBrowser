<?php
/**
 * PHP_CodeBrowser
 *
 * Copyright (c) 2007-2009, Mayflower GmbH
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
 * @category   PHP_CodeBrowser
 * @package    PHP_CodeBrowser
 * @author     Elger Thiele <elger.thiele@mayflower.de>
 * @copyright  2007-2009 Mayflower GmbH
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.phpunit.de/
 * @since      File available since 1.0
 */

define('PHPCB_INSTALL_DIR', q);

require_once PHPCB_INSTALL_DIR . "/src/Util/Autoloader.php";

/**
 * Start File
 * PHP file called from the command line with arguments
 * 
 * Arguments:
 * --source Projekts PHP Source folder
 * --output HTML Output folder 
 * --xml    Cruise Control XML Source File
 *
 * @category   Testing
 * @package    PHPUnit
 * @author     Elger Thiele <elger.thiele@mayflower.de>
 * @author     Christopher Weckerle <christopher.weckerle@mayflower.de>
 * @copyright  2007-2009 Mayflower GmbH
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since 1.0
 */

// register autoloader
spl_autoload_register(array(
    new cbAutoloader() , 'autoload'
));

$timeStart = microtime(true);
ini_set("memory_limit", "1024M");

// XML file merged by xmlint, inherit all cruise control XML log files 
$ccXMLFile = null;

// Path to the code source folder
$sourceFolder = null;

// Path to the code browser html output folder
$htmlOutput = null;

// Path to the new generated code browser xml file
$xmlFileName = 'ccCodeBrowser.xml';

// Check (command line) arguments
$argv = $_SERVER['argv'];
foreach ($argv as $key => $argument) {
    switch ($argument) {
        case '--xml':
            $ccXMLFile = $argv[$key + 1];
            break;
        case '--source':
            $sourceFolder = $argv[$key + 1];
            break;
        case '--output':
            $htmlOutput = $argv[$key + 1];
            break;
    }
}

// CLIController
if (file_exists($ccXMLFile) && is_dir($sourceFolder) && is_dir($htmlOutput)) {
    
    echo "Generating PHP_CodeBrowser files\n";
    
    // init new CLIController
    $controller = new cbCLIController($ccXMLFile, $sourceFolder, $htmlOutput, $htmlOutput . '/' . $xmlFileName);
    $controller->addErrorPlugins(array(
        'cbErrorCheckstyle' , 'cbErrorPMD' , 'cbErrorCPD'
    ));
    try {
        $controller->run();
    } catch (Exception $e) {
        echo 'PHP-CodeBrowser Error: ' . $e->getMessage() . "\n\n";
    }
} else {
    if (! file_exists($ccXMLFile)) print "XML file could not be found\n";
    if (! is_dir($sourceFolder)) print "Source folder not found!\n";
    if (! is_dir($htmlOutput)) print "Ouput folder not found!\n";
    print "\nError: please check arguments\n\n --xml \t\t[/path/to/xml/]\t$ccXMLFile \n --source \t[/path/to/source/]\t$sourceFolder \n --out \t\t[/path/to/html/output]\t$htmlOutput";
    exit();
}

$timeEnd = microtime(true);
$time = $timeEnd - $timeStart;
print "\nScript took " . $time . " seconds to execute\n\n";
