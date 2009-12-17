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
 * CbXMLGeneratorrTests
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
class CbXMLGeneratorTest extends CbAbstractTests 
{
    /**
     * XMLGenerator object to test
     * 
     * @var cbXMLGenerator
     */
    protected $_cbXMLGenerator;
    
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
    protected function setUp()
    {
        parent::setUp();
        $this->_mockFDHandler = $this->_getMockFDHandler();
        $this->_cbXMLGenerator = new CbXMLGenerator($this->_mockFDHandler);
    }
    
    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#tearDown()
     */
    protected function tearDown()
    {
        $this->_cbXMLGenerator = null;
        parent::tearDown();
    }
    
    /**
     * Test if expected XML generation from errors match stored XML file.
     * 
     * @return void
     */
    public function testGenerateXMLFromErrors()
    {
        $this->_cbXMLGenerator->cbXMLName = 'TestXMLGenerator.xml';    
        $this->assertEquals($this->_cbXMLGenerator->generateXMLFromErrors($this->_getSerializedErrors()), 
                            simplexml_load_file(self::$_cbXMLFile));
    }
    
    /**
     * Test setter method
     * 
     * @return void
     */
    public function testSetXMLName()
    {
        $this->_cbXMLGenerator->setXMLName('TestXMLGenerator.xml');
        $this->assertSame('TestXMLGenerator.xml', $this->_cbXMLGenerator->cbXMLName);
    }
    
    /**
     * Test error sorting by hash key
     * 
     * @return void
     */
    public function testSortErrorList() 
    {
        $sorted = $this->_cbXMLGenerator->sortErrorList($this->_getSerializedErrors());
        $this->assertArrayHasKey('b0456446720360d02791c1a3d143f703', $sorted);
        $this->assertTrue(count($sorted) == 1);
        $this->assertType('string', array_shift($sorted));
    }
    
    /**
     * This method is just a wrapper on parent class.
     * Functionality is tested in parent class.
     *
     * @return void
     */
    public function testSaveCbXML() 
    {
        $tmpXML = simplexml_load_string('<?xml version="1.0" encoding="utf-8"?><codebrowser></codebrowser>');
        $this->assertFileNotExists(self::$_cbTestXML);
        $this->_cbXMLGenerator->saveCbXML($tmpXML);
    }
}

