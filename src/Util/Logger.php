<?php
/**
 * Logger
 *
 * PHP Version 5.3.0
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
 * @author    Michel Hartmann <michel.hartmann@mayflower.de>
 * @copyright 2007-2010 Mayflower GmbH
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.phpunit.de/
 * @since     File available since  0.1.2
 */

/**
 * CbLogger
 *
 * @category  PHP_CodeBrowser
 * @package   PHP_CodeBrowser
 * @author    Elger Thiele <elger.thiele@mayflower.de>
 * @author    Michel Hartmann <michel.hartmann@mayflower.de>
 * @copyright 2007-2010 Mayflower GmbH
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://github.com/mayflowergmbh
 * @since     Class available since  0.1.2
 */
class CbLogger
{

    /**
     * Constant for loglevel
     *
     * @var Integer
     */
    const PRIORITY_DEBUG  = 1;

    /**
     * Constant for loglevel
     *
     * @var Integer
     */
    const PRIORITY_INFO   = 2;

    /**
     * Constant for loglevel
     *
     * @var Integer
     */
    const PRIORITY_WARN   = 3;

    /**
     * Constant for loglevel
     *
     * @var Integer
     */
    const PRIORITY_ERROR  = 4;

    /**
     * Priority definition for logging.
     *
     * @var Array
     */
    protected static $priorities = array(
        self::PRIORITY_DEBUG => 'DEBUG',
        self::PRIORITY_INFO => 'INFO',
        self::PRIORITY_WARN => 'WARN',
        self::PRIORITY_ERROR => 'ERROR',
    );

    /**
     * Defined loglevel for this application
     *
     * @var Integer
     */
    protected static $logLevel = -1;

    /**
     * Optional option logfile
     *
     * If is set loglevel output will be saved to $logFile
     *
     * @var String
     */
    protected static $logFile;

    /**
     * Setter for log file.
     *
     * @param String $filename The logfile
     *
     * @return void
     */
    public static function setLogFile($filename)
    {
        self::$logFile = fopen($filename, 'w+');
    }

    /**
     * Setter for application loglevel.
     *
     * @param mixed $priority The priority of loglevl, e.g. CbLogger::DEBUG
     *                        or a string like 'debug' (case-insensitive)
     *
     * @return void
     */
    public static function setLogLevel($priority)
    {
        $levelsByString = array(
            'DEBUG' => self::PRIORITY_DEBUG,
            'INFO'  => self::PRIORITY_INFO,
            'WARN'  => self::PRIORITY_WARN,
            'ERROR' => self::PRIORITY_ERROR
        );

        if (is_string($priority)
                && array_key_exists(strtoupper($priority), $levelsByString)) {
            $priority = strtoupper($priority);
            self::$logLevel = $levelsByString[strtoupper($priority)];
        } else if (is_integer($priority)
                   && self::PRIORITY_DEBUG <= $priority
                   && self::PRIORITY_ERROR >= $priority) {
            self::$logLevel = $priority;
        } else {
            throw new InvalidArgumentException(
                "Invalid log level '$priority' given."
            );
        }
    }

    /**
     * Method for logging and formatting log information.
     *
     * In case log file option is set, logging information will be written to log file,
     * else it will be echoed.
     *
     * @param String  $message  The message to log
     * @param Integer $priority The priority of log level, default CbLogger::PRIORITY_INFO
     *
     * @return void
     */
    public static function log($message, $priority = CbLogger::PRIORITY_INFO)
    {
        if ($priority < self::$logLevel) {
            return;
        }
        $message = sprintf(
            '%s - %s: %s',
            date('Y-m-d h:i:s'),
            self::$priorities[$priority],
            $message
        );

        $logMessage = sprintf('%s%s', $message, PHP_EOL);

        // In case logFile is set write log output to file else echo it
        if (self::$logFile) {
            fwrite(self::$logFile, $logMessage);
        } else {
            echo $logMessage;
        }
    }

    /**
     * Destructor
     *
     * Log file handle will be closed if option log file is set.
     */
    public function __destruct()
    {
        if (self::$logFile) {
            fclose(self::$logFile);
        }
    }

}
