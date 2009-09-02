<?php

require_once realpath(dirname( __FILE__ ) . '/../AbstractTests.php');

class cbPluginErrorTest extends cbAbstractTests 
{
    /**
     * @var cbXMLGenerator
     */
    private $cbPluginError;
    
    protected $_mockXMLHandler;
    
    public function setUp() 
    {
        parent::setUp();        
        $this->_mockXMLHandler = $this->getMockXMLHandler();
        $this->cbPluginError = new cbMockPluginError(PHPCB_SOURCE, $this->_mockXMLHandler);
    }
    
    public function tearDown()
    {
        parent::tearDown();
        $this->_mockXMLHandler = null;
        $this->cbPluginError = null;
    }
    
    public function test__construct() 
    {
        $this->assertEquals(PHPCB_SOURCE, $this->cbPluginError->projectSourceDir);
    }
    
    /**
     * @expectedException Exception
     */
    public function testParsXMLErrorException() 
    {
        $this->cbPluginError->parseXMLError();
    }
    
    public function testParseXMLError() 
    {
        $this->_mockXMLHandler->expects($this->once())
                              ->method('loadXML')
                              ->with($this->equalTo(self::$ccXMLFile))
                              ->will($this->returnValue(simplexml_load_file(self::$ccXMLFile)));
                              
        $this->cbPluginError->setXML(self::$ccXMLFile);
        $result = $this->cbPluginError->parseXMLError();
        
        $this->assertArrayHasKey('5d9801a4b38d4f8b21994064df52f0e9', $result);
        $this->assertTrue(count($result['5d9801a4b38d4f8b21994064df52f0e9']) == 2);
    }
    
    public function testGetRelativePath()
    {
       $this->markTestIncomplete("getRelativePath test not implemented");
    }
}