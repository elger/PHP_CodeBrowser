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
 * CbJSGeneratorTest
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
class CbJSGeneratorTest extends CbAbstractTests 
{
    /**
     * cbJSGenerator object to test
     * 
     * @var cbJSGenerator
     */
    protected $_cbJSGenerator;
    
    /**
     * Mock object for cbFDHandler
     * 
     * @var object
     */
    protected $_mockFDHandler;
    
    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#setUp()
     */
    protected function setUp ()
    {
        parent::setUp();
        
        $this->_mockFDHandler = $this->_getMockFDhandler();   
        $this->_cbJSGenerator = new CbJSGenerator($this->_mockFDHandler);
    }
    
    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#tearDown()
     */
    protected function tearDown ()
    {
        $this->_cbJSGenerator = null;
        parent::tearDown();
    }
   
    /**
     * Test if proper content is generated.
     * It is expeceted that source code from file is present and error Highlighting
     * is resolved the right way.
     * 
     * @return void
     */
    public function testGetHighlightedSource()
    {
        $mockErrors = unserialize(file_get_contents(self::$_serializedErrors));
        
        $this->_mockFDHandler
            ->expects($this->once())
            ->method('loadFile')
            ->with($this->equalTo(PHPCB_TEST_DIR . '/src/MyJSGenerator.php'))
            ->will($this->returnValue(trim(file_get_contents(PHPCB_TEST_DIR . '/src/JSTestGenerator.php'))));
            
        $content = $this->_cbJSGenerator
                                ->getHighlightedSource('MyJSGenerator.php', 
                                                       $mockErrors['b0456446720360d02791c1a3d143f703'], 
                                                       PHPCB_TEST_DIR . '/src');
        $this->assertNotNull($content);   
        $this->assertContains('<li id="line-249" class="white"><a name="line-249"></a><code><span class="comment">', $content);  
        $this->assertContains('<li id="line-250-254" class="moreErrors" ><ul><li id="line-250" class="transparent"><a name="line-250"></a><code><span class="comment">    </span><span class="keyword">private function </span><span class="default">getFoldersFilesTree </span><span class="keyword">(</span><span class="default">$files</span><span class="keyword">)</span></code></li>', $content);
        $this->assertContains('<li id="line-251" class="transparent"><a name="line-251"></a><code><span class="keyword">', $content);
        $this->assertTrue((int)substr_count($content, '<ul>', 0) == (int)substr_count($content, '</ul>', 0));  
        $this->assertTrue((int)substr_count($content, '<li ', 0) == (int)substr_count($content, '</li>', 0));                                        
    }
    
    /**
     * Test if expected javascript source is generated.
     * Using data provider getErrorsFromFile for getting files with errors.
     * 
     * @return void
     * 
     * @dataProvider getErrorsFromFile
     */
    public function testGetJSTree($errors)
    {
        $bufferedContent = $this->_cbJSGenerator->getJSTree($errors);
        $this->assertContains('a.add(2,1,\'JSGenerator.php ( <span class="errors">29E</span> | <span class="notices">29N</span> )\',\'./src/JSGenerator.php.html\',\'\',\'reviewView\')', $bufferedContent);
    }
    
    /**
     * Data provider for file errors
     * 
     * @return array
     */
    public function getErrorsFromFile()
    {
        return array(array(array (0 => 
                                  array (
                                    'complete' => 'src/JSGenerator.php',
                                    'file' => 'JSGenerator.php',
                                    'path' => 'src',
                                    'count_errors' => 29,
                                    'count_notices' => 29,
                                  ),
                                )));
    }
}

