<?php

require_once realpath(dirname( __FILE__ ) . '/../AbstractTests.php');

class cbHTMLGeneratorTest extends cbAbstractTests 
{
    /**
     * @var cbHTMLGenerator
     */
    private $cbHTMLGenerator;
    
    protected $mockFDHandler;
    protected $mockErrorHandler;
    protected $mockJSGenerator;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        
        $this->mockFDHandler = $this->getMockFDHandler();
        $this->mockErrorHandler = $this->getMockErrorHandler();
        $this->mockJSGenerator = $this->getMockJSGenerator();
        $this->cbHTMLGenerator = new cbHTMLGenerator(
            $this->mockFDHandler, 
            $this->mockErrorHandler, 
            $this->mockJSGenerator);
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->cbHTMLGenerator = null;
        parent::tearDown();
    }
    
    /**
     * Tests cbHTMLGenerator->copyRessourceFolders()
     */
    public function testCopyRessourceFolders ()
    {
        $this->markTestSkipped('Tested in cbFDHandler class. In this case only the mock object would be tested.');
    }
    
    /**
     * @dataProvider providedErrors
     */
    public function testGenerateViewFlat ($errors)
    {
        $this->mockFDHandler
            ->expects($this->once())
            ->method('createFile')
            ->will($this->returnCallback(array($this, 'callbackMockMethod')));
            
        $this->cbHTMLGenerator->setOutputDir(PHPCB_TEST_OUTPUT);
        $this->cbHTMLGenerator->setTemplateDir(PHPCB_ROOT_DIR . 'templates');
        
        $this->cbHTMLGenerator->generateViewFlat($errors);
        $this->assertFileExists(PHPCB_TEST_OUTPUT . 'flatView.html');
        
        $this->markTestIncomplete('TODO: need content checks');
    }
    
    /**
     * @dataProvider providedErrors
     */
    public function testGenerateViewReview ($errors)
    {
        // mockFDHandler would need the path structure to create, so in 
        // this case we only check for method calls
        $this->mockFDHandler
            ->expects($this->atLeastOnce())
            ->method('createFile')
            ->will($this->returnvalue(true));
        $this->mockJSGenerator
            ->expects($this->atLeastOnce())
            ->method('getHighlightedSource')
            ->will($this->returnValue('<!-- js highlight mock replacemend -->"'));
        $this->mockErrorHandler
            ->expects($this->atLeastOnce())
            ->method('getErrorsByFile')
            ->will($this->returnValue(array()));  
            
        $this->cbHTMLGenerator->setOutputDir(PHPCB_TEST_OUTPUT);
        $this->cbHTMLGenerator->setTemplateDir(PHPCB_ROOT_DIR . 'templates');
            
        $this->cbHTMLGenerator->generateViewReview($errors, self::$cbGeneratedXMLTest);
        
        $this->markTestIncomplete('No asserts defined, need to implement mock directory creation');
    }
    
    /**
     * @dataProvider providedErrors
     */
    public function testGenerateViewTree ($errors)
    {
        $this->mockFDHandler
            ->expects($this->once())
            ->method('createFile')
            ->will($this->returnCallback(array($this, 'callbackMockMethod')));
            
        $this->mockJSGenerator
            ->expects($this->once())
            ->method('getJSTree')
            ->with($this->equalTo($errors))
            ->will($this->returnValue('<script type="text/javascript">Javascript Dummy</script>'));
            
        $this->cbHTMLGenerator->setOutputDir(PHPCB_TEST_OUTPUT);
        $this->cbHTMLGenerator->setTemplateDir(PHPCB_ROOT_DIR . 'templates');
        
        $this->cbHTMLGenerator->generateViewTree($errors);
        $this->assertFileExists(PHPCB_TEST_OUTPUT . 'tree.html');
        $this->markTestIncomplete('TODO: further content checks are needed');
    }
    
    /**
     * Tests cbHTMLGenerator->setOutputDir()
     */
    public function testSetOutputDir ()
    {
        $this->markTestSkipped('TODO: Implement setter / getter tester');
    }
    
    /**
     * Tests cbHTMLGenerator->setTemplateDir()
     */
    public function testSetTemplateDir ()
    {
        $this->markTestSkipped('TODO: Implement setter / getter tester');
    }
    
    
    public function providedErrors()
    {
        return array(array(
            array(0 => array(
                'complete' => '/opt/cruisecontrol/projects/testPagckage/source/src/cbTestClass.php',
                'file' => 'cbTestClass.php',
                'path' => '/opt/cruisecontrol/projects/testPagckage/source/src',
                'count_errors' => 18,
                'count_notices' => 18))));
    }
    
    public function callbackMockMethod($args) 
    {
        $params = func_get_args();
        file_put_contents($params[0], $params[1]);
    }
}

