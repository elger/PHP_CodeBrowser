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
 * cbXMLHandlerTests
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
class cbXMLHandlerTest extends cbAbstractTests 
{
    /**
     * XMLHandler object to test
     * 
     * @var cbXMLHandler
     */
    private $_cbXMLHandler;
    
    /**
     * MockObject for cbFDHandler
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
        
        $this->_mockFDHandler = $this->_getMockFDHandler();
        $this->_cbXMLHandler = new cbXMLHandler($this->_mockFDHandler);
    }

    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#tearDown()
     */
    protected function tearDown ()
    {
        $this->_cbXMLHandler = null;
        parent::tearDown();
    }
    
    
    /**
     * Test if needed objects (mock in this case) are initialized properly
     * 
     * @return void
     * 
     * @group XMLHandler
     */
    public function test__construct ()
    {
        $this->_cbXMLHandler->__construct($this->_getMockFDHandler());
        $this->assertNotNull($this->_cbXMLHandler->cbFDHandler);
        $this->assertTrue($this->_cbXMLHandler->cbFDHandler instanceof cbFDHandler );
    }
    
    /**
     * Tests cbXMLHandler->countItems()
     * 
     * @return void
     * 
     * @group XMLHandler
     */
    public function testCountItems ()
    {
        $errors = $this->_cbXMLHandler->countItems(
            simplexml_load_file(self::$_cbXMLFile)->file->children(),
            'severity', 
            'error'
        );
        $this->assertEquals($errors, 55);
    }
    
    /**
     * Test xml loader
     * 
     * @return void
     * 
     * @group XMLHandler
     */
    public function testLoadXML ()
    {
        $xml = $this->_cbXMLHandler->loadXML(PHPCB_TEST_DIR . '/basic.xml');
        $this->assertEquals($xml, simplexml_load_file(PHPCB_TEST_DIR . '/basic.xml'));
    }
    
    /**
     * Test xml loader exception
     * 
     * @return void
     * 
     * @group XMLHandler
     * 
     * @expectedException Exception
     */
    public function testExceptionLoadXML ()
    {
        $this->_cbXMLHandler->loadXML('foo.bar');
    }
    
    /**
     * Test load xml from string
     * 
     * @return void
     * 
     * @group XMLHandler
     */
    public function testLoadXMLFromString ()
    {
        $xml = $this->_cbXMLHandler->loadXMLFromString(
            '<?xml version="1.0" encoding="utf-8"?><codebrowser></codebrowser>'
        );
        $this->assertEquals($xml, simplexml_load_file(PHPCB_TEST_DIR . '/basic.xml'));
    }
    
    /**
     * Test if saving an XML file works properly.
     * Checks are done for content and file exists.
     * 
     * @return void
     * 
     * @group XMLHandler
     */
    public function testSaveXML ()
    {
        $tmpXML = simplexml_load_string('<?xml version="1.0" encoding="utf-8"?><codebrowser></codebrowser>');
        
        $this->assertFileNotExists(self::$_cbTestXML);
        
        $this->_mockFDHandler
            ->expects($this->once())
            ->method('createFile')
            ->with($this->equalTo(self::$_cbTestXML))
            ->will($this->returnValue(file_put_contents(self::$_cbTestXML, $tmpXML->asXML())));
        
        $this->_cbXMLHandler->saveXML(self::$_cbTestXML, $tmpXML);
        $this->assertFileExists(self::$_cbTestXML);
        $this->assertXmlFileEqualsXmlFile(self::$_cbTestXML, PHPCB_TEST_DIR . '/basic.xml');
    }
    
    /**
     * Test if non DOMDocument will throw an Exception
     * 
     * @return void
     * 
     * @group XMLHandler
     * @group xmlmerge
     * @expectedException Exception
     */
    public function testAddXMLFile()
    {
        $this->_cbXMLHandler->addXMLFile(new stdClass());
    }
    
    /**
     * Test if all xml files in testData directory are read in and initialised as DOMDocuments 
     * 
     * @return void
     * 
     * @group XMLHandler
     * @group xmlmerge
     */
    public function testAddDirectory()
    {
        $files = $this->_cbXMLHandler->addDirectory(PHPCB_TEST_DIR);    
        
        $this->assertEquals(5, count($files));
        $this->assertTrue($files[1] instanceof DOMDocument);
        $this->assertTrue($files[4] instanceof DOMDocument);
    }
    
    /**
     * Test exception in case of invalid xml files 
     * 
     * @return void
     * 
     * @group XMLHandler
     * @group xmlmerge
     * 
     * @expectedException Exception
     */
    public function testExceptionAddDirectory()
    {
        $this->_cbXMLHandler->addDirectory(PHPCB_TEST_OUTPUT);    
    }

    /**
     * Test if several xml files are merge in the proper way and the result is a single 
     * DOMDocument with all nodes.
     * 
     * @return void
     * 
     * @group XMLHandler
     * @group xmlmerge
     */
    public function testMergeFiles()
    {
        $this->_prepareXML();
        $xml = $this->_cbXMLHandler->mergeFiles();

        $this->assertEquals(3, $xml->firstChild->childNodes->length);
        $this->assertEquals(
            'That is a small description', 
            $xml->firstChild->childNodes->item(1)->nodeValue
        );
    }
    
    /**
     * If no xml files are defined an empty DOMDocument will be returned.
     * 
     * @return void
     * 
     * @group XMLHandler
     * @group xmlmerge
     */
    public function testMergeEmptyFiles()
    {
        $xml = $this->_cbXMLHandler->mergeFiles();
        $this->assertNull($xml->firstChild);
    }
    
    /**
     * Prepare some test data for mergin
     * 
     * @return void
     */
    protected function _prepareXML()
    {
        $files = array();
        $nodes = array('cpd', 'checkstyle', 'coverage');
        
        foreach ($nodes as $node) {
            $xml = new DOMDocument('1.0', 'UTF-8');
            $xml_parent = $xml->createElement($node);
            $xml_parent->setAttribute('generated', '3.222');
            $xml_parent->setAttribute('phpunit', '3.4.0');
            $xml_child  = $xml->createElement('childNode', 'That is a small description');
            $xml_child->setAttribute('name', 'foobar');
            $xml_parent->appendChild($xml_child);
            $xml->appendChild($xml_parent);
            
            $xml->preserveWhiteSpace = false;
            $xml->formatOutput       = true;
            
            $this->_cbXMLHandler->addXMLFile($xml);
        }
    }
}

