<?php

require_once realpath(dirname( __FILE__ ) . '/../../AbstractTests.php');

class cbErrorPMDTest extends cbAbstractTests
{
    /**
     * @var cbErrorPMD
     */
    private $cbErrorPMD;
    
    protected function setUp ()
    {
        parent::setUp();
        $this->cbErrorPMD = new cbErrorPMD('source/', $this->getMockXMLHandler());
    }
    
    protected function tearDown()
    {
        parent::tearDown();
    }
    
    /**
     * @dataProvider pmdElement
     */
    public function testMapError ($element)
    {
        $list = $this->cbErrorPMD->mapError(simplexml_load_string($element));
        $this->assertType('array', $list);
        $this->assertEquals(2, count($list));
        foreach ($list as $error) {
            $this->assertArrayHasKey('source', $error);
            $this->assertArrayHasKey('line', $error);
            $this->assertArrayHasKey('to-line', $error);
            $this->assertArrayHasKey('severity', $error);
            $this->assertArrayHasKey('description', $error);
            $this->assertArrayHasKey('name', $error);
        }
        $tmp = (array) $list[0]['source'];
        $this->assertEquals('NPathComplexity', $tmp[0]);
    }
    
    /**
     * @dataProvider pmdEmptyElement
     */
    public function testMapErrorEmpty ($element)
    {
        $list = $this->cbErrorPMD->mapError(simplexml_load_string($element));
        $this->assertType('array', $list);
        $this->assertEquals(0, count($list));
    }
    
    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testMapErrorFail ()
    {
        $this->cbErrorPMD->mapError(simplexml_load_string(array()));
        $this->cbErrorPMD->mapError(simplexml_load_string('foo'));
    }
    
    public function testSetPluginName ()
    {
        $this->assertEquals('pmd', $this->cbErrorPMD->pluginName);
        $this->cbErrorPMD->setPluginName('foo');
        $this->assertEquals('pmd', $this->cbErrorPMD->pluginName);
    }
    
    /**
     * data provider
     */
    public function pmdElement ()
    {
        return array(array('<file name="/opt/cruisecontrol/projects/testPagckage/source/src/cbTestClass.php">
                    <violation rule="NPathComplexity" priority="1" line="85" to-line="196" package="cbTestPackage" class="cbTestClass" method="validate">The NPath complexity is 1848. The NPath complexity of a function or method is the number of acyclic execution paths through that method. A threshold of 200 is generally considered the point where measures should be taken to reduce complexity.</violation>
                    <violation rule="CodeCoverage" priority="1" line="77" to-line="88" package="cbTestPackage" class="cbTestClass" method="__construct">The code coverage is 0.00 which is considered low.</violation>
                </file>'));
    }
    
    /**
     * data provider
     */
    public function pmdEmptyElement ()
    {
        return array(array('<file name="/opt/cruisecontrol/projects/testPagckage/source/src/cbTestClass.php"></file>'));
    }
}

