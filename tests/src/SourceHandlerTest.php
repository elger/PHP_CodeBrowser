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
 * @since      File available since  0.1.0
 */

require_once 'Log.php';

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
 * @since      Class available since  0.1.0
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
     * Pear Log object.
     *
     * @var Log
     */
    protected $_log;

    /**
     * Initializes common values.
     */
    public function __construct()
    {
        $this->_log = Log::singleton('null');
    }

    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#setUp()
     */
    protected function setUp()
    {
        parent::setUp();

        $xmlString = <<<HERE
<?xml version="1.0" encoding="UTF-8"?>
<checkstyle version="1.2.0RC3">
 <file name="/a/dir/source.php">
  <error line="37" column="1" severity="error" message="m1" source="PEAR.Commenting.FileCommentSniff"/>
 </file>
 <file name="/a/nother/dir/src.php">
  <error line="39" column="1" severity="error" message="m3" source="PEAR.Commenting.FileCommentSniff"/>
  <error line="40" column="1" severity="error" message="m4" source="PEAR.Commenting.FileCommentSniff"/>
 </file>
</checkstyle>
HERE;
        $issueXML = new CbIssueXml($this->_log);
        $xml      = new DOMDocument('1.0', 'UTF-8');
        $xml->validateOnParse = true;
        $xml->loadXML($xmlString);
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
     * Test getFiles.
     *
     * @return void
     */
    public function test__getFiles()
    {
        $name1 = '/a/dir/source.php';
        $name1 = '/a/nother/dir/src.php';
        $expected = array(
            '/a/nother/dir/src.php' => new CbFile(
                '/a/nother/dir/src.php',
                array(
                    new CbIssue(
                        '/a/nother/dir/src.php',
                        39, 39, 'Checkstyle',
                        'm3', 'error'
                    ),
                    new CbIssue(
                        '/a/nother/dir/src.php',
                        40, 40, 'Checkstyle',
                        'm4', 'error'
                    )
                )
            ),
            '/a/dir/source.php' => new CbFile(
                '/a/dir/source.php',
                array(
                    new CbIssue(
                        '/a/dir/source.php',
                        37, 37, 'Checkstyle',
                        'm1', 'error'
                    )
                )
            )
        );
        CbFile::sort($expected);

        $actual = $this->_cbSourceHandler->getFiles();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test getFilesWithIssues
     *
     * @return void
     */
    public function test__getFilesWithIssues()
    {
        $expectedFiles = array (
            '/a/dir/source.php',
            '/a/nother/dir/src.php'
        );
        $actualFiles = $this->_cbSourceHandler->getFilesWithIssues();
        $this->assertEquals($expectedFiles, $actualFiles);
    }
}
