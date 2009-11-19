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
 * cbPluginErrorTests
 * 
 * As the cbPluginError is abstract an mock proxy is setup. 
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
 * @see        cbMockPluginError
 */
class cbPluginErrorTest extends cbAbstractTests 
{
    /**
     * MockPluginError object to test.
     * In order to test the abstract class, it is mocked
     *  
     * @var cbMockPluginError
     */
    protected $_cbPluginError;
    
    /**
     * Mock object of cbXMLHandler
     * 
     * @var object
     */
    protected $_mockXMLHandler;
    
    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#setUp()
     */
    public function setUp() 
    {
        parent::setUp();        
        $this->_mockXMLHandler = $this->_getMockXMLHandler();
        $this->_cbPluginError = new cbMockPluginError(PHPCB_SOURCE, $this->_mockXMLHandler);
    }
    
    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#tearDown()
     */
    public function tearDown()
    {
        parent::tearDown();
        $this->_mockXMLHandler = null;
        $this->_cbPluginError = null;
    }
    
    /**
     * Test construct if objects are initalized properly
     * 
     * @return void
     */
    public function test__construct() 
    {
        $this->assertEquals(PHPCB_SOURCE, $this->_cbPluginError->projectSourceDir);
    }
    
    /**
     * Test exception
     * 
     * @return void
     * @expectedException Exception
     */
    public function testParseXMLErrorException() 
    {
        $this->_cbPluginError->parseXMLError();
    }
    
    /**
     * Test empty node
     * 
     * @return void
     */
    public function testParseXMLErrorEmpty() 
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($dom->createElement('foo', 'bar'));
        
        $this->_cbPluginError->setXML($dom);
        $list = $this->_cbPluginError->parseXMLError();
        
        $this->assertEquals(array(), $list);
        $this->assertTrue(0 === count($list));
    }
    
    /**
     * Test if file iteration and hashing works correctly.
     * As class itself abstract it mock with static return values in mock object
     * 
     * @return void
     * @see cbMockPluginError::mapError
     */
    public function testParseXMLError() 
    {
        $domDoc = new DOMDocument('1.0', 'UTF-8');
        $domDoc->load(self::$_ccXMLFile);      
                        
        $this->_cbPluginError->setXML($domDoc);
        
        $result = $this->_cbPluginError->parseXMLError();
        
        $this->assertArrayHasKey('eae7d2bf1565776108392eeba1e3dc44', $result);
        $this->assertTrue(count($result['eae7d2bf1565776108392eeba1e3dc44']) == 1);
    }
    
    /**
     * Test function that should cut off relative matching path definitions.
     * 
     * @return void
     */
    public function testGetRelativePath()
    {
        $absPath = '/my/path/to/source/code/myFile.php';
        $relPath = '../../source';
        $dirPath = '/another/path/to/path/source/source';
        $noPath  = '';

        $this->assertEquals('code/myFile.php', $this->_cbPluginError->getRelativeFilePath($absPath, $relPath));
        $this->assertEquals('code/myFile.php', $this->_cbPluginError->getRelativeFilePath($absPath, $dirPath));
        $this->assertEquals($absPath, $this->_cbPluginError->getRelativeFilePath($absPath, $noPath));
    }
    
    /**
     * Test exception in case of different source path as defined in log files
     * 
     * @return void
     * 
     * @expectedException Exception
     */
    public function testExceptionGetRelativePath()
    {
        $this->_cbPluginError->getRelativeFilePath(
            '/path/to/src/in/log/files', 
            'source/'
        );
    }  
}