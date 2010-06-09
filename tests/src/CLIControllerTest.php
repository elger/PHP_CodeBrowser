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

require_once realpath(dirname( __FILE__ ) . '/../AbstractTests.php');

/**
 * CbCLIControllerTest
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
class CbCLIControllerTest extends CbAbstractTests
{
    /**
     * CLIController object to test
     *
     * @var CbCLIController
     */
    protected $_cbCLIController;

    /**
     * The log path used for testing.
     *
     * @var string
     */
    protected $_logDir;

    /**
     * The project source dir used for testing.
     *
     * @var string
     */
    protected $_projectSourceDir;

    /**
     * The output dir used for testing.
     *
     * @var string
     */
    protected $_outputDir;

    /**
     * Initialize values.
     */
    public function __construct()
    {
        $this->_logDir = realpath(dirname(__FILE__) . '/../testData/testLogs/');
        if (!$this->_logDir) {
            $this->fail('Could not find testData/testLogs directory');
        }
        $this->_projectSourceDir = realpath($this->_logDir . '/../src/');
        if (!$this->_projectSourceDir) {
            $this->fail('Could not find dummy source directory testData/src');
        }
        $this->_outputDir = $this->_logDir  . '/tmpdir';

        // Try not to get our terminal cluttered
        CbLogger::setLogFile($this->_logDir . '/tmpfile');
    }

    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#setUp()
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_cbCLIController = new CbCLIController($this->_logDir,
                                                      $this->_projectSourceDir,
                                                      $this->_outputDir);
        mkdir($this->_outputDir);
        $this->_cbCLIController->addErrorPlugins(array('CbErrorCoverage'));
    }

    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#tearDown()
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->delTree($this->_outputDir);
    }

    /**
     * Test the run method
     *
     * @return void
     */
    public function test__run()
    {
        $this->_cbCLIController->run();

        $this->assertTrue(is_dir($this->_outputDir . '/css/'));
        $this->assertTrue(is_dir($this->_outputDir . '/js/'));
        $this->assertTrue(is_dir($this->_outputDir . '/img/'));
        $this->assertTrue(file_exists($this->_outputDir . '/index.html'));
    }

    /**
     * Test the run method without source directory.
     *
     * @return void
     */
    public function test__runWithSourceDirNull()
    {
        $this->_cbCLIController->setProjectSourceDir(null);
        $this->_cbCLIController->run();

        $this->assertTrue(is_dir($this->_outputDir . '/css/'));
        $this->assertTrue(is_dir($this->_outputDir . '/js/'));
        $this->assertTrue(is_dir($this->_outputDir . '/img/'));
        $this->assertTrue(file_exists($this->_outputDir . '/index.html'));
    }

    /**
     * Test the main method
     *
     * @return void
     */
    public function test__main()
    {
        $_SERVER['argv'] = array(
            '--log', $this->_logDir,
            '--source', $this->_projectSourceDir,
            '--output', $this->_outputDir
        );
        
        CbCLIController::main();
    }

    /**
     * Helper method to recursively delete a directory. Use with care.
     */
    private function delTree($dir)
    {
        $files = glob($dir . '*', GLOB_MARK );
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->delTree($file);
            } else {
                unlink($file);
            }
        }
        if (is_dir($dir)) {
            rmdir( $dir );
        }
    }
}
