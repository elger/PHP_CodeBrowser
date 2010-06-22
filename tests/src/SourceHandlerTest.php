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
 * @since      File available since 1.0
 */

require_once realpath(dirname( __FILE__ ) . '/../AbstractTests.php');

/**
 * CbSourceHandlerTest
 *
 * @category   PHP_CodeBrowser
 * @package    PHP_CodeBrowser
 * @subpackage PHPUnit
 * @author     Simon Kohlmeyer <simon.kohlmeyer@mayflower.de>
 * @copyright  2007-2010 Mayflower GmbH
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since 1.0
 */
class CbSourceHandlerTest extends CbAbstractTests
{
    /**
     * SourceHandler object to test
     *
     * @var cbSourceHandler
     */
    protected $_cbSourceHandler;

    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#setUp()
     */
    protected function setUp()
    {
        parent::setUp();

        $issueXML = new CbIssueXml();
        $xml      = new DOMDocument('1.0', 'UTF-8');
        $xml->validateOnParse = true;
        $xml->load(realpath(dirname(__FILE__) . '/../testData/shortCheckstyle.xml'));
        $issueXML->addXMLFile($xml);
        $plugins = array(new CbErrorCheckstyle($issueXML));
        $this->_cbSourceHandler = new CbSourceHandler($issueXML, $plugins);
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
     * Test getIssuesByFile for a file with issues.
     *
     * @return void
     */
    public function test__getIssuesByFile()
    {
        $expectedIssues = array(
            37 => array(new CbIssue(
                '/opt/cruisecontrol/projects/phpcb/source/src/JSGenerator.php',
                37,
                37,
                'Checkstyle',
                'm1',
                'error'
            )),
            38 => array(new CbIssue(
                '/opt/cruisecontrol/projects/phpcb/source/src/JSGenerator.php',
                38,
                38,
                'Checkstyle',
                'm2',
                'error'
            ))
        );
        $filename = '/opt/cruisecontrol/projects/phpcb/source/src/JSGenerator.php';
        $actualIssues = $this->_cbSourceHandler->getIssuesByFile($filename);
        $this->assertEquals($expectedIssues, $actualIssues);
    }

    /**
     * Test getIssuesByFile for a file that doesn't have any Issues.
     *
     * @return void
     */
    public function test__getIssuesByFileNonexisting()
    {
        $issues = $this->_cbSourceHandler->getIssuesByFile('/nonExistingFile');
        $this->assertEquals(array(), $issues);
    }

    /**
     * Test getFilesWithIssues
     *
     * @return void
     */
    public function test__getFilesWithIssues()
    {
        $expectedFiles = array (
            '/opt/cruisecontrol/projects/phpcb/source/src/JSGenerator.php',
            '/opt/cruisecontrol/projects/phpcb/source/src/AnotherFile.php'
        );
        $actualFiles = $this->_cbSourceHandler->getFilesWithIssues();
        $this->assertEquals($expectedFiles, $actualFiles);
    }
}
