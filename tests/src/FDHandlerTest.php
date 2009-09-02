<?php

require_once realpath(dirname( __FILE__ ) . '/../AbstractTests.php');

class cbFDHandlerTest extends cbAbstractTests 
{
    
    private $_cbFDHandler;
    
    protected $_createDir = 'my/test/directory';
    protected $_fileDir = 'my/new/folder';
    protected $_createFile = 'my/new/folder/myFile.txt';
	
    protected function setUp ()
    {
        parent::setUp();
        $this->_cbFDHandler = new cbFDHandler();
    }

    protected function tearDown ()
    {
        $this->_cbFDHandler = null;
        parent::tearDown();
    }
    
    public function testCopyDirectory ()
    {
        $this->assertFalse(is_dir(PHPCB_TEST_OUTPUT . '/css'));
        $this->assertFileNotExists(PHPCB_TEST_OUTPUT . '/js/side-bar.js');
        $this->_cbFDHandler->copyDirectory(PHPCB_SOURCE . '/../templates', PHPCB_TEST_OUTPUT);
        $this->assertTrue(is_dir(PHPCB_TEST_OUTPUT . '/css'));
        $this->assertFileExists(PHPCB_TEST_OUTPUT . '/js/side-bar.js');
    }
    
    public function testCopyFile ()
    {
        $this->assertFalse(is_dir(PHPCB_TEST_OUTPUT . '/foo/bar'));
        $this->_cbFDHandler->copyFile(PHPCB_SOURCE . '/../CodeBrowser.php', PHPCB_TEST_OUTPUT . '/foo/bar');
        $this->assertFileExists(PHPCB_TEST_OUTPUT . '/foo/bar/CodeBrowser.php');
    }
    
    public function testCreateDirectory ()
    {
        $this->_cbFDHandler->createDirectory(PHPCB_TEST_OUTPUT . '/' . $this->_createDir);
        $this->assertTrue(is_dir(PHPCB_TEST_OUTPUT . '/' . $this->_createDir));
    }
    
    public function testCreateFile ()
    {
        $file = PHPCB_TEST_OUTPUT . '/' . $this->_createFile;
        $content = 'This is a Test';
        
        $this->_cbFDHandler->createFile($file, $content);
        $this->assertFileExists($file);
        $this->assertTrue(is_dir(PHPCB_TEST_OUTPUT . '/' . $this->_fileDir));
        $this->assertSame($content, file_get_contents($file));
    }
    
    /**
     * directories should be deleted recursively within all files
     */
    public function testDeleteDirectory ()
    {
        if (!is_dir(PHPCB_TEST_OUTPUT . '/foo')) mkdir(PHPCB_TEST_OUTPUT . '/foo');
        if (!is_dir(PHPCB_TEST_OUTPUT . '/foo/bar')) mkdir(PHPCB_TEST_OUTPUT . '/foo/bar');
        file_put_contents(PHPCB_TEST_OUTPUT . '/foo/bar/foo.txt', 'somecontent');
        
        $this->assertTrue(file_exists(PHPCB_TEST_OUTPUT . '/foo/bar/foo.txt'));
        
        $this->_cbFDHandler->deleteDirectory(PHPCB_TEST_OUTPUT . '/foo');
        
        $this->assertFileNotExists(PHPCB_TEST_OUTPUT . '/foo/bar/foo.txt');
        $this->assertFalse(is_dir(PHPCB_TEST_OUTPUT . '/foo'));
    }
    
    public function testDeleteFile ()
    {
        file_put_contents(PHPCB_TEST_OUTPUT . '/deleteFile.txt', 'SomeContent');
        $this->assertFileExists(PHPCB_TEST_OUTPUT . '/deleteFile.txt');
        $this->_cbFDHandler->deleteFile(PHPCB_TEST_OUTPUT . '/deleteFile.txt');
        $this->assertFileNotExists(PHPCB_TEST_OUTPUT . '/deleteFile.txt');
    }
    
    public function testLoadFile ()
    {
        $content = $this->_cbFDHandler->loadFile(self::$cbXMLBasic);
        $this->assertSame('<?xml version="1.0" encoding="utf-8"?><codebrowser></codebrowser>', $content);
    }
    
    /**
     * @expectedException Exception
     */
    public function testLoadFileException()
    {
        $this->_cbFDHandler->loadFile('foo.xml');
    }
}

