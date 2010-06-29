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
     * Mock IOHelper
     */
    protected $_ioMock;

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
        $this->_projectSourceDir = realpath($this->_logDir . '/../src/');
        $this->_outputDir = realpath($this->_logDir . '/../');

        if (!$this->_logDir) {
            $this->fail('Could not find testData/testLogs directory');
        }
        if (!$this->_projectSourceDir) {
            $this->fail('Could not find dummy source directory testData/src');
        }

        // Try not to get our terminal cluttered
        CbLogger::setLogFile($this->_logDir . '/tmpfile');
    }

    /**
     * Clean up afterwards.
     */
    public function __destruct()
    {
        if (file_exists($this->_logDir . '/tmpfile')) {
            unlink($this->_logDir . '/tmpfile');
        }
    }

    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#setUp()
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_ioMock = $this->getMock('CbIOHelper',
                                        array('deleteDirectory',
                                              'createDirectory',
                                              'createFile',
                                              'copyDirectory'
                                        )
        );
        $this->_cbCLIController = new CbCLIController($this->_logDir,
                                                      $this->_projectSourceDir,
                                                      $this->_outputDir,
                                                      array(),
                                                      $this->_ioMock);
        $this->_cbCLIController->addErrorPlugins(array('CbErrorCheckstyle'));
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
     * Test the run method
     *
     * @return void
     */
    public function test__run()
    {
        // We can't parse logs with filenames that don't exist on this system.
        $this->markTestIncomplete();
        $this->_cbCLIController = new CbCLIController($this->_logDir,
                                                      $this->_projectSourceDir,
                                                      $this->_outputDir,
                                                      array(),
                                                      $this->_ioMock);
        $this->_cbCLIController->addErrorPlugins(array('CbErrorCheckstyle'));

        $this->_ioMock->expects($this->once())
                      ->method('deleteDirectory')
                      ->with($this->equalTo($this->_outputDir));
        $this->_ioMock->expects($this->once())
                      ->method('createDirectory')
                      ->with($this->equalTo($this->_outputDir));
        $this->_ioMock->expects($this->exactly(3))
                      ->method('createFile')
                      ->with($this->stringContains($this->_outputDir, false));

        $this->_cbCLIController->run();
    }

    /**
     * Test the run method without source directory.
     *
     * @return void
     */
    public function test__runWithSourceDirNull()
    {
        // This doesn't work, as we can't highlight non-existant files
        $this->markTestIncomplete();

        $this->_cbCLIController = new CbCLIController($this->_logDir,
                                                      null,
                                                      $this->_outputDir,
                                                      array(),
                                                      $this->_ioMock);
        $this->_cbCLIController->addErrorPlugins(array('CbErrorCheckstyle'));

        $this->_ioMock->expects($this->once())
                      ->method('deleteDirectory')
                      ->with($this->equalTo($this->_outputDir));
        $this->_ioMock->expects($this->once())
                      ->method('createDirectory')
                      ->with($this->equalTo($this->_outputDir));
        $this->_ioMock->expects($this->exactly(3))
                      ->method('createFile')
                      ->with($this->stringContains($this->_outputDir, false));

        $this->_cbCLIController->run();
    }

    /**
     * Test if excluding files works correctly.
     *
     * @return void
     */
    public function test__runWithExcludes()
    {
        // We can't parse logs with filenames that don't exist on this system.
        $this->markTestIncomplete();
        $this->_cbCLIController = new CbCLIController($this->_logDir,
                                                      null,
                                                      $this->_outputDir,
                                                      array('/JSGenerator/'),
                                                      $this->_ioMock);
        $this->_cbCLIController->addErrorPlugins(array('CbErrorCheckstyle'));

        $this->_ioMock->expects($this->once())
                      ->method('deleteDirectory')
                      ->with($this->equalTo($this->_outputDir));
        $this->_ioMock->expects($this->once())
                      ->method('createDirectory')
                      ->with($this->equalTo($this->_outputDir));
        $this->_ioMock->expects($this->exactly(2))
                      ->method('createFile')
                      ->with(
                          $this->logicalAnd(
                              $this->stringContains($this->_outputDir, false),
                              $this->logicalOr(
                                  $this->stringEndsWith('AnotherFile.php.html'),
                                  $this->stringEndsWith('index.html')
                              )
                          )
                      );

        $this->_cbCLIController->run();
    }
}
