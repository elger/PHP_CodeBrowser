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
 * cbHTMLGeneratorTest
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
class cbHTMLGeneratorTest extends cbAbstractTests 
{
    /**
     * cbHTMLGenerator object to test
     * 
     * @var cbHTMLGenerator
     */
    protected $_cbHTMLGenerator;
    
    /**
     * Mock object for cbFDHandler
     * 
     * @var object
     */
    protected $_mockFDHandler;
    
    /**
     * Mock object for cbErrorHandler
     * 
     * @var object
     */
    protected $_mockErrorHandler;
    
    /**
     * Mock object for cbJSGenerator
     * 
     * @var object
     */
    protected $_mockJSGenerator;
    
    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#setUp()
     */
    protected function setUp ()
    {
        parent::setUp();
        
        $this->_mockFDHandler = $this->_getMockFDHandler();
        $this->_mockErrorHandler = $this->_getMockErrorHandler();
        $this->_mockJSGenerator = $this->_getMockJSGenerator();
        $this->_cbHTMLGenerator = new cbHTMLGenerator(
            $this->_mockFDHandler, 
            $this->_mockErrorHandler, 
            $this->_mockJSGenerator);
    }
    
    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#tearDown()
     */
    protected function tearDown ()
    {
        $this->_cbHTMLGenerator = null;
        parent::tearDown();
    }
    
    /**
     * Test if needed folder/file creation functions are called properly.
     * Used parameters are defined by setters.
     * The creation function itself are tested in FBHandler test case.
     * 
     * @return void
     */
    public function testCopyRessourceFolders ()
    {
        $this->_mockFDHandler
            ->expects($this->once())
            ->method('copyFile')
            ->with($this->equalTo('foo/treeView.html'), $this->equalTo('output'));
            
        $this->_mockFDHandler
            ->expects($this->exactly(3))
            ->method('copyDirectory');
            
        $this->_cbHTMLGenerator->setOutputDir('output');
        $this->_cbHTMLGenerator->setTemplateDir('foo');
        $this->_cbHTMLGenerator->copyRessourceFolders();
    }
    
    /**
     * Test if flatView.html file is generated properly and if expected content
     * is written to this file. Files and folders are created by callback method
     * with original values.
     * Errors are read in by data provider
     * 
     * @return void
     * 
     * @dataProvider providedErrors
     */
    public function testGenerateViewFlat ($errors)
    {
        $this->_mockFDHandler
            ->expects($this->once())
            ->method('createFile')
            ->will($this->returnCallback(array($this, 'callbackMockMethod')));
            
        $this->_cbHTMLGenerator->setOutputDir(PHPCB_TEST_OUTPUT);
        $this->_cbHTMLGenerator->setTemplateDir(PHPCB_ROOT_DIR . 'templates');
        $this->_cbHTMLGenerator->generateViewFlat($errors);

        $this->assertFileExists(PHPCB_TEST_OUTPUT . '/flatView.html');
        
        $content = file_get_contents(PHPCB_TEST_OUTPUT . '/flatView.html');
        
        $this->assertContains('<a href="./src/JSGenerator.php.html">src/JSGenerator.php</a>', $content);
        $this->assertTrue(0 == substr_count($content, '<script language="javascript">', 0));
        $this->assertTrue(0 == substr_count($content, '<script type="text/javascript">', 0));
    }
    
    /**
     * Test if <em>source/file.php</em>.html file is generated properly and if expected content
     * is written to this file. Files and folders are created by callback method
     * with original values.
     * Errors are read in by data provider
     * 
     * @return void
     * 
     * @dataProvider providedErrors
     */
    public function testGenerateViewReview ($errors)
    {
        // mockFDHandler would need the path structure to create, so in 
        // this case we only check for method calls
        $mockErrors = unserialize(file_get_contents(self::$_serializedErrors));
        $this->_mockFDHandler
            ->expects($this->once())
            ->method('createFile')
            ->will($this->returnCallback(array($this, 'callbackMockMethod')));
        $this->_mockJSGenerator
            ->expects($this->once())
            ->method('getHighlightedSource')
            ->will($this->returnValue('<!-- js highlight mock replacemend -->'));
        $this->_mockErrorHandler
            ->expects($this->atLeastOnce())
            ->method('getErrorsByFile')
            ->will($this->returnValue($mockErrors['b0456446720360d02791c1a3d143f703']));  
            
        $this->_cbHTMLGenerator->setOutputDir(PHPCB_TEST_OUTPUT);
        $this->_cbHTMLGenerator->setTemplateDir(PHPCB_ROOT_DIR . 'templates');
        $this->_cbHTMLGenerator->generateViewReview($errors, self::$_cbXMLFile, PHPCB_TEST_DIR . '/src');

        $this->assertFileExists(PHPCB_TEST_OUTPUT . '/src/JSGenerator.php.html');
        
        $content = file_get_contents(PHPCB_TEST_OUTPUT . '/src/JSGenerator.php.html');
        
        $this->assertContains('onclick="new Effect.Highlight', $content);
        $this->assertContains('new Tip(', $content);
        $this->assertContains("if ($('line-", $content);
        $this->assertContains("<!-- js highlight mock replacemend -->", $content);
    }
    
    /**
     * Test if tree.html file is generated properly and if expected content
     * is written to this file. Files and folders are created by callback method
     * with original values.
     * Errors are read in by data provider
     * 
     * @dataProvider providedErrors
     */
    public function testGenerateViewTree ($errors)
    {
        $this->_mockFDHandler
            ->expects($this->once())
            ->method('createFile')
            ->will($this->returnCallback(array($this, 'callbackMockMethod')));
            
        $this->_mockJSGenerator
            ->expects($this->once())
            ->method('getJSTree')
            ->with($this->equalTo($errors))
            ->will($this->returnValue('<script type="text/javascript">Javascript Dummy</script>'));
            
        $this->_cbHTMLGenerator->setOutputDir(PHPCB_TEST_OUTPUT);
        $this->_cbHTMLGenerator->setTemplateDir(PHPCB_ROOT_DIR . 'templates');
        $this->_cbHTMLGenerator->generateViewTree($errors);

        $this->assertFileExists(PHPCB_TEST_OUTPUT . '/tree.html');
        
        $content = file_get_contents(PHPCB_TEST_OUTPUT . '/tree.html');
        
        $this->assertContains('<script type="text/javascript">Javascript Dummy</script></div>', $content);
        $this->assertTrue(0 == substr_count($content, '<a href=', 0));
    }
    
    /**
     * Dataprovider for PHP_CodeBrowser errors
     * 
     * @return array
     */
    public function providedErrors()
    {
        return array(array(
            array(0 => array(
                'complete' => 'src/JSGenerator.php',
                'file' => 'JSGenerator.php',
                'path' => 'src',
                'count_errors' => 18,
                'count_notices' => 18))));
    }
    
    /**
     * Callback method for file / folder creation
     * Creates folder path and given file.
     * 
     * @param mixed $args Mixed function arguments
     * 
     * @return void
     */
    public function callbackMockMethod($args) 
    {
        $params = func_get_args();
        
        $realName = basename(($params[0]));
        $target   = substr(($params[0]), 0, - 1 * (strlen($realName)));
        
        if (!empty($target)) {
            if ('\/' == substr($target, - 1, 1)) $target = substr($target, - 1, 1);
            $dirs = explode('/', $target);
            $path = '';
            foreach ($dirs as $folder) {
                if (! is_dir($path = $path . $folder . '/')) {
                    mkdir($path);
                }
            }
        }
        file_put_contents($params[0], $params[1]);
    }
}

