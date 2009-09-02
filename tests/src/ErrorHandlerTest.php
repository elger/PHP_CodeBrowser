<?php

require_once realpath(dirname( __FILE__ ) . '/../AbstractTests.php');

class cbErrorHandlerTest extends cbAbstractTests 
{
    
    private $cbErrorHandler;
    
    protected $mockXMLHandler;
    protected $fileName = '/opt/cruisecontrol/projects/testPagckage/source/src/cbTestClass.php';
    protected $neededKeys = array('line', 'to-line', 'source', 'severity', 'description');
    
    
    protected function setUp()
    {
        parent::setUp();
        $this->mockXMLHandler = $this->getMockXMLHandler();
        $this->cbErrorHandler = new cbErrorHandler($this->mockXMLHandler);
    }
    
    protected function tearDown()
    {
        parent::tearDown();
    }
    
    public function test__construct()
    {
        $this->assertTrue($this->cbErrorHandler->cbXMLHandler instanceof cbXMLHandler);
    }
    
    public function testGetErrorsByFile()
    {   
        $this->mockXMLHandler
            ->expects($this->once())
            ->method('loadXML')
            ->with($this->equalTo(self::$cbGeneratedXMLTest))
            ->will($this->returnValue(simplexml_load_file(self::$cbGeneratedXMLTest)));
            
        $list = $this->cbErrorHandler->getErrorsByFile(self::$cbGeneratedXMLTest, $this->fileName);
        
        $this->assertType('SimpleXMLElement', $list);
        
        foreach ($list as $item) foreach($item->attributes() as $key => $value) {
            $this->assertTrue(in_array($key, $this->neededKeys));
            $this->assertNotNull($value);
        }
    }
    
    public function testGetFilesWithErrors()
    {
        $this->mockXMLHandler
            ->expects($this->once())
            ->method('loadXML')
            ->with($this->equalTo(self::$cbGeneratedXMLTest))
            ->will($this->returnValue(simplexml_load_file(self::$cbGeneratedXMLTest)));
            
        $this->mockXMLHandler
            ->expects($this->atLeastOnce())
            ->method('countItems')
            ->will($this->returnValue(rand(1,100)));
        
        $files = $this->cbErrorHandler->getFilesWithErrors(self::$cbGeneratedXMLTest);
        
        $this->assertTrue(5 === count($files[0]));
        $this->assertEquals($this->fileName, $files[0]['complete']);
    }
}