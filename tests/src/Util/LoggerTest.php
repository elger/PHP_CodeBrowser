<?php
/**
 * Test case
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
 * @category   PHP_CodeBrowser
 * @package    PHP_CodeBrowser
 * @subpackage PHPUnit
 * @author     Simon Kohlmeyer <simon.kohlmeyer@mayflower.de
 * @copyright  2007-2010 Mayflower GmbH
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.phpunit.de/
 * @since      File available since 1.0
 */

require_once realpath(dirname( __FILE__ ) . '/../../AbstractTests.php');

/**
 * CbLoggerTest
 *
 * @category   PHP_CodeBrowser
 * @package    PHP_CodeBrowser
 * @subpackage PHPUnit
 * @author     Simon Kohlmeyer <simon.kohlmeyer@mayflower.de>
 * @copyright  2007-2010 Mayflower GmbH
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since 1.0
 */
class CbLoggerTest extends CbAbstractTests
{
    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#setUp()
     */
    protected function setUp()
    {
        parent::setUp();

        // We need to reset the protected static members of CbLogger...
        $rc = new ReflectionClass('CbLogger');

        $logLevel = $rc->getProperty('logLevel');
        $logLevel->setAccessible(true);
        $logLevel->setValue(-1);

        $logFile = $rc->getProperty('logFile');
        $logFile->setAccessible(true);
        $logFile->setValue(null);
    }

    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#tearDown()
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Test if logging to stdout works
     *
     * @return void
     */
    public function test__log()
    {
        $logData = 'Test log data';

        ob_start();
        CbLogger::log($logData);
        $logged = ob_get_clean();

        $this->assertTrue(strpos($logged, $logData) !== false);
    }

    /**
     * Test if logging to a file works
     *
     * @return void
     */
    public function test__logToFile()
    {
        $logData = 'Test log data';
        $filename = realpath(dirname(__FILE__) . '/../../testData/') . '/tmpfile';

        if (file_exists($filename)) {
            unlink($filename);
        }

        CbLogger::setLogFile($filename);
        CbLogger::log($logData);
        
        $logged = file_get_contents($filename);
        $this->assertTrue(strpos($logged, $logData) !== false);

        unlink($filename);
    }

    /**
     * Test if priority filtering works.
     *
     * @return void
     */
    public function test__priorityFiltering()
    {
        $logData = 'Test log data';
        $filtered = 'Should not appear in log';

        CbLogger::setLogLevel(CbLogger::PRIORITY_WARN);
        ob_start();
        CbLogger::log($filtered, CbLogger::PRIORITY_DEBUG);
        CbLogger::log($logData, CbLogger::PRIORITY_ERROR);
        $logged = ob_get_clean();

        $this->assertFalse(strpos($logged, $filtered));
        $this->assertTrue(strpos($logged, $logData) !== false);
    }

}
