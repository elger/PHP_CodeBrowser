<?php

class cbAbstractTests extends PHPUnit_Framework_TestCase
{
    protected static $ccXMLFile;
    protected static $cbXMLBasic;
    protected static $cbTestFile;
    protected static $cbTestXML;
    protected static $cbGeneratedXMLTest;
    
    protected function setUp() {
        
        parent::setUp();
        
        self::$ccXMLFile = PHPCB_TEST_DIR . 'validMerge.xml';
        self::$cbXMLBasic = PHPCB_TEST_DIR . 'basic.xml';
        self::$cbTestFile = PHPCB_TEST_OUTPUT . 'cbTestFile.txt';
        self::$cbTestXML = PHPCB_TEST_OUTPUT . 'cbTestXML.xml';
        self::$cbGeneratedXMLTest = PHPCB_TEST_DIR . 'GeneratedXMLTest.xml';
    }

    protected function tearDown() 
    {
        parent::tearDown();
        $this->cleanUp(PHPCB_TEST_OUTPUT);
    }
    
    protected function getMockXMLHandler() 
    {
        $functions = array('loadXML', 'countItems');
        $params = array($this->getMockFDHandler());
        $mockXMLHandler = $this->getMock('cbXMLHandler', $functions, $params);
        
        return $mockXMLHandler;
    }
    
    protected function getMockFDHandler() 
    {
        $functions = array('createFile', 'loadFile');
        $mockFDHandler = $this->getMock('cbFDHandler', $functions);
        
        return $mockFDHandler;
    }
    
    protected function getMockJSGenerator()
    {
        $functions = array('getJSTree', 'getHighlightedSource');
        $params = array($this->getMockFDHandler());
        $mockJSGenerator = $this->getMock('cbJSGenerator', $functions, $params);
        
        return $mockJSGenerator;
    }
    
    protected function getMockErrorHandler()
    {
        $functions = array('getErrorsByFile');
        $params = array($this->getMockXMLHandler());
        $mockErrorHandler = $this->getMock('cbErrorHandler', $functions, $params);
        
        return $mockErrorHandler;
    }
    
    protected function cleanUp($dir)
    {
        $iterator = new DirectoryIterator($dir);
        while ($iterator->valid()) {

            // delete file
            if ($iterator->isFile()) unlink($dir . '/' . $iterator->current());
            
            // delete folder recursive
            if (! $iterator->isDot() && $iterator->isDir()) {
                $this->cleanUp($dir . '/' . $iterator->current());
                rmdir($dir . '/' . $iterator->current());
            }
            $iterator->next();
        }
        unset($iterator);
    }
}