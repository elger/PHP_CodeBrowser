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

require_once realpath(dirname( __FILE__ ) . '/../../AbstractTests.php');

/**
 * CbErrorPMDTest
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
class CbErrorPMDTest extends CbAbstractTests
{
    /**
     * cbErrorPMD object to test
     * 
     * @var cbErrorPMD
     */
    protected $_cbErrorPMD;
    
    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#setUp()
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_cbErrorPMD = new CbErrorPMD('source/', $this->_getMockXMLHandler());
    }
    
    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#tearDown()
     */
    protected function tearDown()
    {
        parent::tearDown();
    }
    
    /**
     * Test error parsing / mapping
     * 
     * @return void
     * 
     * @dataProvider pmdElement
     */
    public function testMapError($element)
    {
        $list = $this->_cbErrorPMD->mapError(simplexml_load_string($element));
        
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
     * Test empty error file expected emtpy array
     * 
     * @return void
     * 
     * @dataProvider pmdEmptyElement
     */
    public function testMapErrorEmpty($element)
    {
        $list = $this->_cbErrorPMD->mapError(simplexml_load_string($element));
        
        $this->assertType('array', $list);
        $this->assertEquals(0, count($list));
    }
    
    /**
     * Test expected mapping fail
     * 
     * @return void
     * 
     * @expectedException PHPUnit_Framework_Error
     */
    public function testMapErrorFail()
    {
        $this->cbErrorPMD->mapError(simplexml_load_string(array()));
        $this->cbErrorPMD->mapError(simplexml_load_string('foo'));
    }
    
    /**
     * Test setter method
     * 
     * @return void
     */
    public function testSetPluginName()
    {
        $this->assertEquals('pmd', $this->_cbErrorPMD->pluginName);

        $this->_cbErrorPMD->setPluginName('foo');
        
        $this->assertEquals('pmd', $this->_cbErrorPMD->pluginName);
    }
    
    /**
     * Data provider for an error elements of a certain file
     * 
     * @return array
     */
    public function pmdElement()
    {
        return array(array('<file name="/opt/cruisecontrol/projects/testPagckage/source/src/cbTestClass.php">
                    <violation rule="NPathComplexity" priority="1" line="85" to-line="196" package="cbTestPackage" class="cbTestClass" method="validate">The NPath complexity is 1848. The NPath complexity of a function or method is the number of acyclic execution paths through that method. A threshold of 200 is generally considered the point where measures should be taken to reduce complexity.</violation>
                    <violation rule="CodeCoverage" priority="1" line="77" to-line="88" package="cbTestPackage" class="cbTestClass" method="__construct">The code coverage is 0.00 which is considered low.</violation>
                </file>'));
    }
    
    /**
     * Data provider for an empty error element of a certain file
     */
    public function pmdEmptyElement()
    {
        return array(array('<file name="/opt/cruisecontrol/projects/testPagckage/source/src/cbTestClass.php"></file>'));
    }
}

