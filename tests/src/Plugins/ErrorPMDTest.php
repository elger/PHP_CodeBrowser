<?php
/**
 * Test case
 *
 * Copyright (c) 2007-2010, Mayflower GmbH
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
 * @author     Simon Kohlmeyer <simon.kohlmeyer@mayflower.de
 * @copyright  2007-2010 Mayflower GmbH
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.phpunit.de/
 * @since      File available since  0.9.0
 */

require_once realpath(dirname(__FILE__) . '/../../AbstractTests.php');

/**
 * CbErrorPMDTest
 *
 * @category   PHP_CodeBrowser
 * @package    PHP_CodeBrowser
 * @subpackage PHPUnit
 * @author     Simon Kohlmeyer <simon.kohlmeyer@mayflower.de>
 * @copyright  2007-2010 Mayflower GmbH
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since  0.9.0
 */
class CbErrorPMDTest extends CbAbstractTests
{
    /**
     * The object to test.
     *
     * @var CbErrorPMD
     */
    protected $_cbErrorPmd;

    /**
     * The xml string to test the plugin against.
     *
     * @var String
     */
    protected $_testXml = <<<HERE
<?xml version="1.0" encoding="UTF-8" ?>
<pmd version="0.2.6" timestamp="2010-07-17T02:38:00-07:00">
  <file name="/some/file">
    <violation beginline="3"
               endline="4"
               rule="Rule1"
               ruleset="Ruleset 1"
               priority="1">Description 1</violation>
    <violation beginline="5"
               endline="5"
               rule="Rule2"
               ruleset="Ruleset 1"
               class="SomeClass"
               method="someMethod"
               priority="3">Description 2</violation>
  </file>
  <file name="/other/file">
    <violation beginline="15"
               endline="15"
               rule="The third rule"
               ruleset="Ruleset two"
               priority="3">Description 3</violation>
  </file>
  <file name="/has/no/violation">
  </file>
</pmd>
HERE;

    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#setUp()
     */
    protected function setUp()
    {
        parent::setUp();
        $issueXML = new CbIssueXML();
        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->loadXML($this->_testXml);
        $issueXML->addXMLFile($xml);
        $this->_cbErrorPmd = new CbErrorPMD($issueXML);
    }

    /**
     * Test getFilelist
     *
     * @return  void
     */
    public function test__getFilelist()
    {
        $expected = array(
            new CbFile(
                '/some/file',
                array(
                    new CbIssue(
                        '/some/file',
                        3,
                        4,
                        'PMD',
                        'Description 1',
                        'error'
                    ),
                    new CbIssue(
                        '/some/file',
                        5,
                        5,
                        'PMD',
                        'Description 2',
                        'error'
                    )
                )
            ),
            new CbFile(
                '/other/file',
                array(
                    new CbIssue(
                        '/other/file',
                        15,
                        15,
                        'PMD',
                        'Description 3',
                        'error'
                    )
                )
            ),
            new CbFile(
                '/has/no/violation',
                array()
            )
        );
        $actual = $this->_cbErrorPmd->getFilelist();
        $this->assertEquals($expected, $actual);
    }

}
