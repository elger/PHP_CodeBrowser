<?php

require_once realpath(dirname( __FILE__ ) . '/../AbstractTests.php');

class cbJSGeneratorTest extends cbAbstractTests 
{
    /**
     * @var cbJSGenerator
     */
    private $cbJSGenerator;
    
    protected $mockFDHandler;
    
    protected function setUp ()
    {
        parent::setUp();
        
        $this->mockFDHandler = $this->getMockFDhandler();   
        $this->cbJSGenerator = new cbJSGenerator($this->mockFDHandler);
    }
    
    protected function tearDown ()
    {
        $this->cbJSGenerator = null;
        parent::tearDown();
    }
   
    /**
     * @dataProvider parsedValues
     */
    public function testGetHighlightedSource ($errors)
    {
        $this->mockFDHandler
            ->expects($this->once())
            ->method('loadFile')
            ->will($this->returnValue(trim(file_get_contents(self::$cbGeneratedXMLTest))));
            
        $bufferedContent = $this->cbJSGenerator->getHighlightedSource(self::$cbGeneratedXMLTest, $errors['5d9801a4b38d4f8b21994064df52f0e9']);
        
        $this->markTestSkipped("TODO: not checks are implementes yet!");
        
    }
    
    public function testGetJSTree ()
    {
        // TODO Auto-generated cbJSGeneratorTest->testGetJSTree()
        $this->markTestIncomplete("getJSTree test not implemented");
        $this->cbJSGenerator->getJSTree(/* parameters */);
    }
    
    public function parsedValues() 
    {
        return array(array(unserialize('a:1:{s:32:"5d9801a4b38d4f8b21994064df52f0e9";a:2:{i:0;a:6:{s:4:"name";s:67:"/opt/cruisecontrol/projects/testPagckage/source/src/cbTestClass.php";s:4:"line";i:85;s:7:"to-line";i:196;s:6:"source";s:15:"NPathComplexity";s:8:"severity";s:5:"error";s:11:"description";s:242:"The NPath complexity is 1848. The NPath complexity of a function or method is the number of acyclic execution paths through that method. A threshold of 200 is generally considered the point where measures should be taken to reduce complexity.";}i:1;a:6:{s:4:"name";s:67:"/opt/cruisecontrol/projects/testPagckage/source/src/cbTestClass.php";s:4:"line";i:77;s:7:"to-line";i:88;s:6:"source";s:12:"CodeCoverage";s:8:"severity";s:5:"error";s:11:"description";s:50:"The code coverage is 0.00 which is considered low.";}}}')));
    }
}

