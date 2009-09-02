<?php

require_once realpath(dirname( __FILE__ ) . '/../AbstractTests.php');

/**
 * cbXMLGenerator test case.
 */
class cbXMLGeneratorTest extends cbAbstractTests 
{
    /**
     * @var cbXMLGenerator
     */
    private $cbXMLGenerator;
    
    private $xmlName = 'TestXMLGenerator.xml';
    
    protected $mockFDHandler;
    
    protected function setUp ()
    {
        parent::setUp();
        $this->mockFDHandler = $this->getMockFDHandler();
        $this->cbXMLGenerator = new cbXMLGenerator($this->mockFDHandler);
    }
    
    protected function tearDown ()
    {
        $this->cbXMLGenerator = null;
        parent::tearDown();
    }
    
    /**
     * @dataProvider parsedValues
     */
    public function testGenerateXMLFromErrors ($errors)
    {
        $this->cbXMLGenerator->cbXMLName = $this->xmlName;    
        $element = $this->cbXMLGenerator->generateXMLFromErrors($errors);
        
        $this->assertEquals($element, simplexml_load_file(self::$cbGeneratedXMLTest));
    }
    
    /**
     * Tests cbXMLGenerator->setXMLName()
     */
    public function testSetXMLName ()
    {
        $this->cbXMLGenerator->setXMLName($this->xmlName);
        $this->assertSame($this->xmlName, $this->cbXMLGenerator->cbXMLName);
    }
    
    /**
     * @dataProvider parsedValues
     */
    public function testSortErrorList($values) 
    {
        $sorted = $this->cbXMLGenerator->sortErrorList($values);
        $this->assertArrayHasKey('5d9801a4b38d4f8b21994064df52f0e9', $sorted);
        $this->assertTrue(count($sorted) == 1);
        $this->assertType('string', array_shift($sorted));
    }
    
    public function testSaveCbXML() 
    {
        $this->markTestSkipped("Save functionality is resolved in cbXMLHandler and cbFDHandler");
    }
    
    public function parsedValues() 
    {
        return array(array(unserialize('a:1:{s:32:"5d9801a4b38d4f8b21994064df52f0e9";a:2:{i:0;a:6:{s:4:"name";s:67:"/opt/cruisecontrol/projects/testPagckage/source/src/cbTestClass.php";s:4:"line";i:85;s:7:"to-line";i:196;s:6:"source";s:15:"NPathComplexity";s:8:"severity";s:5:"error";s:11:"description";s:242:"The NPath complexity is 1848. The NPath complexity of a function or method is the number of acyclic execution paths through that method. A threshold of 200 is generally considered the point where measures should be taken to reduce complexity.";}i:1;a:6:{s:4:"name";s:67:"/opt/cruisecontrol/projects/testPagckage/source/src/cbTestClass.php";s:4:"line";i:77;s:7:"to-line";i:88;s:6:"source";s:12:"CodeCoverage";s:8:"severity";s:5:"error";s:11:"description";s:50:"The code coverage is 0.00 which is considered low.";}}}')));
    }
}

