<?php
/**
 * Test case
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
 * @subpackage PHPUnit
 * @author     Elger Thiele <elger.thiele@mayflower.de>
 * @copyright  2007-2009 Mayflower GmbH
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.phpunit.de/
 * @since      File available since 1.0
 */

require_once realpath(dirname( __FILE__ ) . '/../AbstractTests.php');

/**
 * cbFDHandlertTests
 *
 * @category   PHP_CodeBrowser
 * @package    PHP_CodeBrowser
 * @subpackage PHPUnit
 * @author     Elger Thiele <elger.thiele@mayflower.de>
 * @copyright  2007-2009 Mayflower GmbH
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since 1.0
 */
class cbFDHandlerTest extends cbAbstractTests 
{
    /**
     * FDHandler object to test
     * 
     * @var cbDFHandler
     */
    private $_cbFDHandler;
    
    /**
     * Dummy folder path for creation test
     * 
     * @var string
     */
    protected $_createDir = 'my/test/directory';
    
    /**
     * Dummy folder path for creation test
     * 
     * @var string
     */
    protected $_fileDir = 'my/new/folder';
    
    /**
     * Dummy folder file for creation test
     * 
     * @var string
     */
    protected $_createFile = 'my/new/folder/myFile.txt';
	
    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#setUp()
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_cbFDHandler = new cbFDHandler();
    }

    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#tearDown()
     */
    protected function tearDown()
    {
        $this->_cbFDHandler = null;
        parent::tearDown();
    }
    
    /**
     * Test if given directory is copied recursive and corectly
     * 
     * @return void
     */
    public function testCopyDirectory()
    {
        $this->assertFalse(is_dir(PHPCB_TEST_OUTPUT . '/css'));
        $this->assertFileNotExists(PHPCB_TEST_OUTPUT . '/js/side-bar.js');
        $this->_cbFDHandler->copyDirectory(PHPCB_SOURCE . '/../templates', PHPCB_TEST_OUTPUT, array('.svn'));
        $this->assertTrue(is_dir(PHPCB_TEST_OUTPUT . '/css'));
        $this->assertFileExists(PHPCB_TEST_OUTPUT . '/js/side-bar.js');
    }
    
    /**
     * Test if given file ist copied correctly
     * 
     * @return void
     */
    public function testCopyFile()
    {
        $this->assertFalse(is_dir(PHPCB_TEST_OUTPUT . '/foo/bar'));
        $this->_cbFDHandler->copyFile(PHPCB_SOURCE . '/../bin/phpcb.php', PHPCB_TEST_OUTPUT . '/foo/bar');
        $this->assertFileExists(PHPCB_TEST_OUTPUT . '/foo/bar/phpcb.php');
    }
    
    /**
     * Test exception
     * 
     * @return void
     * 
     * @expectedException Exception
     */
    public function testExceptionCopyFile()
    {
        $this->_cbFDHandler->copyFile('foo.bar', '');
    }
    
    /**
     * Test if given directory is created
     *  
     * @return void
     */
    public function testCreateDirectory()
    {
        $this->_cbFDHandler->createDirectory(PHPCB_TEST_OUTPUT . '/' . $this->_createDir);
        $this->assertTrue(is_dir(PHPCB_TEST_OUTPUT . '/' . $this->_createDir));
    }
    
    /**
     * Test if given file is created
     * 
     * @return void
     */
    public function testCreateFile()
    {
        $file    = PHPCB_TEST_OUTPUT . '/' . $this->_createFile;
        $content = 'This is a Test';
        
        $this->_cbFDHandler->createFile($file, $content);
        $this->assertFileExists($file);
        $this->assertTrue(is_dir(PHPCB_TEST_OUTPUT . '/' . $this->_fileDir));
        $this->assertSame($content, file_get_contents($file));
    }
    
    /**
     * Directories should be deleted recursively within all files
     * 
     * @return void
     */
    public function testDeleteDirectory()
    {
        if (!is_dir(PHPCB_TEST_OUTPUT . '/foo')) mkdir(PHPCB_TEST_OUTPUT . '/foo');
        if (!is_dir(PHPCB_TEST_OUTPUT . '/foo/bar')) mkdir(PHPCB_TEST_OUTPUT . '/foo/bar');
        file_put_contents(PHPCB_TEST_OUTPUT . '/foo/bar/foo.txt', 'somecontent');
        
        $this->assertTrue(file_exists(PHPCB_TEST_OUTPUT . '/foo/bar/foo.txt'));
        
        $this->_cbFDHandler->deleteDirectory(PHPCB_TEST_OUTPUT . '/foo');
        
        $this->assertFileNotExists(PHPCB_TEST_OUTPUT . '/foo/bar/foo.txt');
        $this->assertFalse(is_dir(PHPCB_TEST_OUTPUT . '/foo'));
    }
    
    /**
     * Test if file is deleted correctly
     * 
     * @return void
     */
    public function testDeleteFile()
    {
        file_put_contents(PHPCB_TEST_OUTPUT . '/deleteFile.txt', 'SomeContent');
        $this->assertFileExists(PHPCB_TEST_OUTPUT . '/deleteFile.txt');
        $this->_cbFDHandler->deleteFile(PHPCB_TEST_OUTPUT . '/deleteFile.txt');
        $this->assertFileNotExists(PHPCB_TEST_OUTPUT . '/deleteFile.txt');
    }
    
    /**
     * Test if content of a loaded file is correct initialized
     * 
     * @return void
     */
    public function testLoadFile ()
    {
        $content = $this->_cbFDHandler->loadFile(self::$_cbXMLBasic);
        
        $this->assertSame('<?xml version="1.0" encoding="utf-8"?><codebrowser/>', $content);
    }
    
    /**
     * Check if Exception is thrown probably
     * 
     * @return void
     * 
     * @expectedException Exception
     */
    public function testLoadFileException()
    {
        $this->_cbFDHandler->loadFile('foo.xml');
    }
}

