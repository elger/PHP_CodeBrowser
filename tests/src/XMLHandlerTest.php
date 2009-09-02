<?php

require_once realpath(dirname( __FILE__ ) . '/../AbstractTests.php');

/**
 * cbXMLHandler test case.
 */
class cbXMLHandlerTest extends cbAbstractTests 
{
    /**
     * @var cbXMLHandler
     */
    private $cbXMLHandler;
    
    protected $mockFDHandler;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        
        $this->mockFDHandler = $this->getMockFDHandler();
        $this->cbXMLHandler = new cbXMLHandler($this->mockFDHandler);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->cbXMLHandler = null;
        parent::tearDown();
    }
    
    
    /**
     * Tests cbXMLHandler->__construct()
     */
    public function test__construct ()
    {
        $this->cbXMLHandler->__construct($this->getMockFDHandler());
        $this->assertNotNull($this->cbXMLHandler->cbFDHandler);
        $this->assertTrue($this->cbXMLHandler->cbFDHandler instanceof cbFDHandler );
    }
    
    /**
     * Tests cbXMLHandler->countItems()
     */
    public function testCountItems ()
    {
        // TODO Auto-generated cbXMLHandlerTest->testCountItems()
        $this->markTestIncomplete("countItems test not implemented");
        $this->cbXMLHandler->countItems(/* parameters */);
    }
    
    /**
     * Tests cbXMLHandler->loadXML()
     */
    public function testLoadXML ()
    {
        $xml = $this->cbXMLHandler->loadXML(PHPCB_TEST_DIR . 'basic.xml');
        $this->assertEquals($xml, simplexml_load_file(PHPCB_TEST_DIR . 'basic.xml'));
    }
    
    /**
     * Tests cbXMLHandler->loadXMLFromString()
     */
    public function testLoadXMLFromString ()
    {
        $xml = $this->cbXMLHandler->loadXMLFromString('<?xml version="1.0" encoding="utf-8"?><codebrowser></codebrowser>');
        $this->assertEquals($xml, simplexml_load_file(PHPCB_TEST_DIR . 'basic.xml'));
    }
    
    /**
     * Tests cbXMLHandler->saveXML()
     */
    public function testSaveXML ()
    {
        $tmpXML = simplexml_load_string('<?xml version="1.0" encoding="utf-8"?><codebrowser></codebrowser>');
        
        $this->assertFileNotExists(self::$cbTestXML);
        
        $this->mockFDHandler
            ->expects($this->once())
            ->method('createFile')
            ->with($this->equalTo(self::$cbTestXML, $tmpXML))
            ->will($this->returnValue(file_put_contents(self::$cbTestXML, $tmpXML->asXML())));
        
        $this->cbXMLHandler->saveXML(self::$cbTestXML, $tmpXML);
        $this->assertFileExists(self::$cbTestXML);
        $this->assertXmlFileEqualsXmlFile(self::$cbTestXML, PHPCB_TEST_DIR . 'basic.xml');
    }
}

