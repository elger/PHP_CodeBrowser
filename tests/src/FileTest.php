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

require_once realpath(dirname(__FILE__) . '/../AbstractTests.php');

/**
 * CbFileTest
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
class CbFileTest extends CbAbstractTests
{
    /**
     * File object to test
     *
     * @var CbFile
     */
    protected $_cbFile;

    /**
     * Some issues to work with.
     *
     * @var Array of CbIssue
     */
    protected $_issues;

    /**
     * Constructor. Initialize some values.
     */
    public function __construct()
    {
        $this->_issues = array(
            new CbIssue(
                '/some/file/name.php',
                39, 39, 'Checkstyle',
                'm3', 'error'
            ),
            new CbIssue(
                '/some/file/name.php',
                50, 52, 'Checkstyle',
                'm4', 'warning'
            ),
            new CbIssue(
                '/some/file/name.php',
                40, 40, 'Checkstyle',
                'm4', 'error'
            )
        );
    }

    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#setUp()
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_cbFile = new CbFile('/some/file/name.php');
    }

    /**
     * Test constructor if variables are stored properly
     *
     * @return void
     */
    public function test__construct()
    {
        $this->assertEquals('/some/file/name.php', $this->_cbFile->name());

        $this->_cbFile = new CbFile('/some/file/name.php', $this->_issues);

        $this->assertEquals('/some/file/name.php', $this->_cbFile->name());
        $this->assertEquals($this->_issues, $this->_cbFile->getIssues());
    }

    /**
     * Test if adding issues works.
     *
     * @return void
     */
    public function test__addIssue()
    {
        $this->_cbFile->addIssue($this->_issues[0]);
        $this->assertEquals(
            array($this->_issues[0]),
            $this->_cbFile->getIssues()
        );
    }

    /**
     * Tries to add invalid issue to file.
     *
     * @return void
     */
    public function test__addIssueToWrongFile()
    {
        $issue = new CbIssue(
            '/the/wrong/file/name.php',
            39, 39, 'Checkstyle',
            'm3', 'error'
        );
        try {
            $this->_cbFile->addIssue($issue);
            $this->fail();
        } catch (InvalidArgumentException $e) {
            // Expected
        }
    }


    /**
     * Test the basename function
     *
     * @return void
     */
    public function test__basename()
    {
        $this->assertEquals('name.php', $this->_cbFile->basename());
    }

    /**
     * Test the dirname function
     *
     * @return void
     */
    public function test__dirname()
    {
        $this->assertEquals('/some/file', $this->_cbFile->dirname());
    }

    /**
     * Test if the issue count is returned correctly
     *
     * @return void
     */
    public function test__issueCount()
    {
        $this->assertEquals(0, $this->_cbFile->getIssueCount());

        $this->_cbFile->addIssue($this->_issues[0]);
        $this->assertEquals(1, $this->_cbFile->getIssueCount());

        $this->_cbFile = new CbFile(
            '/some/file/name.php',
            array($this->_issues[0])
        );
        $this->assertEquals(1, $this->_cbFile->getIssueCount());

        $this->_cbFile->addIssue($this->_issues[1]);
        $this->assertEquals(2, $this->_cbFile->getIssueCount());
    }

    /**
     * Test the errorCount function
     *
     * @return void
     */
    public function test__errorCount()
    {
        $this->_cbFile = new CbFile('/some/file/name.php', $this->_issues);
        $this->assertEquals(2, $this->_cbFile->getErrorCount());
    }

    /**
     * Test the warningCount function
     *
     * @return void
     */
    public function test__warningCount()
    {
        $this->_cbFile = new CbFile('/some/file/name.php', $this->_issues);
        $this->assertEquals(1, $this->_cbFile->getWarningCount());
    }

    /**
     * Test the mergeWith function
     *
     * @return void
     */
    public function test__mergeWith()
    {
        $this->_cbFile = new CbFile(
            '/some/file/name.php',
            array($this->_issues[0], $this->_issues[1])
        );
        $otherFile = new CbFile(
            '/some/file/name.php',
            array($this->_issues[2])
        );
        $this->_cbFile->mergeWith($otherFile);

        $this->assertEquals(2, $this->_cbFile->getErrorCount());
        $this->assertEquals(1, $this->_cbFile->getWarningCount());
        $this->assertEquals(
            array_values($this->_issues),
            array_values($this->_cbFile->getIssues())
        );
    }

    /**
     * Try to merge with a non-compatible file.
     *
     * @return void
     */
    public function test__mergeWithDifferentFile()
    {
        try {
            $this->_cbFile->mergeWith(new CbFile('/the/wrong/file/name.php'));
            $this->fail();
        } catch (InvalidArgumentException $e) {
            // Expected
        }
    }

    /**
     * Test the sort function.
     *
     * @return void.
     */
    public function test__sort()
    {
        $sorted = array(
            new CbFile("src/Helper/IOHelper.php"),
            new CbFile("src/Plugins/ErrorCPD.php"),
            new CbFile("src/Plugins/ErrorCheckstyle.php"),
            new CbFile("src/Plugins/ErrorCoverage.php"),
            new CbFile("src/Plugins/ErrorPMD.php"),
            new CbFile("src/Plugins/ErrorPadawan.php"),
            new CbFile("src/Util/Autoloader.php"),
            new CbFile("src/Util/Logger.php"),
            new CbFile("src/View/ViewAbstract.php"),
            new CbFile("src/View/ViewReview.php"),
            new CbFile("src/CLIController.php"),
            new CbFile("src/File.php"),
            new CbFile("src/Issue.php"),
            new CbFile("src/IssueXml.php"),
            new CbFile("src/PluginsAbstract.php"),
            new CbFile("src/SourceHandler.php"),
            new CbFile("src/SourceIterator.php")
        );

        $mixed = array(
            new CbFile("src/PluginsAbstract.php"),
            new CbFile("src/Plugins/ErrorCheckstyle.php"),
            new CbFile("src/CLIController.php"),
            new CbFile("src/Plugins/ErrorPadawan.php"),
            new CbFile("src/SourceIterator.php"),
            new CbFile("src/SourceHandler.php"),
            new CbFile("src/Issue.php"),
            new CbFile("src/View/ViewReview.php"),
            new CbFile("src/File.php"),
            new CbFile("src/Util/Autoloader.php"),
            new CbFile("src/Helper/IOHelper.php"),
            new CbFile("src/IssueXml.php"),
            new CbFile("src/Plugins/ErrorCoverage.php"),
            new CbFile("src/View/ViewAbstract.php"),
            new CbFile("src/Util/Logger.php"),
            new CbFile("src/Plugins/ErrorPMD.php"),
            new CbFile("src/Plugins/ErrorCPD.php"),
        );

        CbFile::sort($mixed);
        $mixed = array_values($mixed);
        $this->assertEquals($sorted, $mixed);
    }
}
