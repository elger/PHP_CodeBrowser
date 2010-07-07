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
 * @since      File available since  0.1.0
 */

require_once realpath(dirname( __FILE__ ) . '/../../AbstractTests.php');

/**
 * CbIOHelperTest
 *
 * @category   PHP_CodeBrowser
 * @package    PHP_CodeBrowser
 * @subpackage PHPUnit
 * @author     Simon Kohlmeyer <simon.kohlmeyer@mayflower.de>
 * @copyright  2007-2010 Mayflower GmbH
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since  0.1.0
 */
class CbIOHelperTest extends CbAbstractTests
{
    protected $_testDir;

    /**
     * The CbIOHelper object under test.
     *
     * @var CbIOHelper
     */
    protected $_ioHelper;

    /**
     * Define variables
     */
    public function __construct() {
        $this->_testDir = realpath(dirname(__FILE__) . '/../../testData/');
        if (!$this->_testDir) {
            $this->fail('Could not find tests/testData directory.');
        }
    }

    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#setUp()
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_ioHelper = new CbIOHelper();
    }

    /**
     * Test createFile function without creating a path
     *
     * @return void
     */
    public function test__createFile()
    {
        $filename = $this->_testDir . '/tmpfile';
        $content  = 'Lorem ipsum';

        if (file_exists($filename)) {
            unlink($filename);
        }

        $this->_ioHelper->createFile($filename, $content);
        $this->assertTrue(file_exists($filename));
        $this->assertEquals($content, file_get_contents($filename));

        unlink($filename);
    }

    /**
     * Test createFile function with creating a path
     *
     * @return void
     */
    public function test__createFileWithPath()
    {
        $dirname = $this->_testDir . '/tmpdir';
        $filename = $dirname . '/tmpfile';
        $content  = 'Lorem ipsum';

        if (file_exists($filename)) {
            unlink($filename);
            rmdir($dirname);
        } else if (file_exists($dirname)) {
            rmdir($dirname);
        }

        $this->_ioHelper->createFile($filename, $content);
        $this->assertTrue(file_exists($dirname));
        $this->assertTrue(file_exists($filename));
        $this->assertEquals($content, file_get_contents($filename));

        unlink($filename);
        rmdir($dirname);
    }

    /**
     * Test deleteFile function
     *
     * @return void
     */
    public function test__deleteFile()
    {
        $filename = $this->_testDir . '/tmpfile';

        if (!file_exists($filename)) {
            file_put_contents($filename, 'Lorem ipsum');
        }

        $this->_ioHelper->deleteFile($filename);
        $this->assertFalse(file_exists($filename));
    }

    /**
     * Test copyFile function
     *
     * @return void
     */
    public function test__copyFile()
    {
        $srcFile = $this->_testDir . '/tmpfile';
        $dstDir = $this->_testDir . '/tmpdir';
        $dstFile = $dstDir . '/tmpfile';
        $content = 'Lorem ipsum';

        if (file_exists($srcFile)) {
            unlink($srcFile);
        }
        if (file_exists($dstFile)) {
            rmdir($dstFile);
        }

        file_put_contents($srcFile, $content);

        $this->_ioHelper->copyFile($srcFile, $dstDir);
        $this->assertTrue(file_exists($srcFile));
        $this->assertTrue(file_exists($dstDir));
        $this->assertTrue(file_exists($dstFile));
        $this->assertEquals($content, file_get_contents($dstFile));
        $this->assertEquals($content, file_get_contents($srcFile));

        unlink($dstFile);
        rmdir($dstDir);
        unlink($srcFile);
    }

    /**
     * Test copyFile function for nonexisting source file
     *
     * @return void
     */
    public function test__copyFileNonexisting()
    {
        $file = $this->_testDir . '/tmpfile';
        $dstDir = $this->_testDir . '/tmpdir';

        if (file_exists($file)) {
            unlink($file);
        }

        try {
            $this->_ioHelper->copyFile($file, $dstDir);
        } catch (Exception $e) {
            return;
        }
        $this->fail('Expected exception was not thrown.');
    }
}
