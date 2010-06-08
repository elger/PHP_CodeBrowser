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

require_once realpath(dirname( __FILE__ ) . '/../../AbstractTests.php');

/**
 * CbViewReviewTest
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
class CbViewReviewTest extends CbAbstractTests
{
    /**
     * The CbViewReview object to test.
     *
     * @var CbViewReview
     */
    protected $_cbViewReview;

    /**
     * The test output directory
     *
     * @var string
     */
    protected $_outDir;

    /**
     * Initialize common variables.
     */
    public function __construct()
    {
        $this->_outDir = realpath(dirname(__FILE__) . '/../../testData/');
        if (!$this->_outDir) {
            $this->fail('Could not find testData directory');
        }
        $this->_outDir = $this->_outDir . '/tmpdir/';
    }

    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#setUp()
     */
    protected function setUp()
    {
        parent::setUp();

        $templateDir = dirname(__FILE__) . '/../../../templates/';
        $this->_cbViewReview = new CbViewReview();
        $this->_cbViewReview->setTemplateDir($templateDir);

        $this->delTree($this->_outDir);
        mkdir($this->_outDir);
        $this->_cbViewReview->setOutputDir($this->_outDir);
    }

    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#tearDown()
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->delTree($this->_outDir);
    }

    /**
     * Test the generate method without any issues
     *
     * @return void
     */
    public function test__generateNoIssues()
    {
        $this->_cbViewReview->generate(array(), __FILE__, dirname(__FILE__));

        $expectedFile = $this->_outDir . '/' . basename(__FILE__) . '.html';
        $this->assertTrue(file_exists($expectedFile));
    }

    /**
     * Test the generate method with an issue
     *
     * @return void
     */
    public function test__generate()
    {
        $issueList = array(
            80 => array(
                new CbIssue(
                    __FILE__,
                    80,
                    85,
                    'finder',
                    'description',
                    'severe'
                )
            )
        );

        $this->_cbViewReview->generate($issueList, __FILE__, dirname(__FILE__));
        $expectedFile = $this->_outDir . '/' . basename(__FILE__) . '.html';
        $this->assertTrue(file_exists($expectedFile));
    }

    /**
     * Test the generate method with mutliple errors on one line.
     *
     * @return void
     */
    public function test__generateMultiple()
    {
        $issueList = array(
            80 => array(
                new CbIssue(
                    __FILE__,
                    80,
                    85,
                    'finder',
                    'description',
                    'severe'
                    ),
                new CbIssue(
                    __FILE__,
                    80,
                    83,
                    'other finder',
                    'other description',
                    'more severe'
                )
            )
        );
        $this->_cbViewReview->generate($issueList, __FILE__, dirname(__FILE__));
        $expectedFile = $this->_outDir . '/' . basename(__FILE__) . '.html';
        $this->assertTrue(file_exists($expectedFile));
    }

    /**
     * Try to test highlighting with Text_Highlighter
     *
     * @return void
     */
    public function test__generateWithTextHighlighter()
    {
    }

    /**
     * Test highlighting of unknown code files.
     *
     * @return void
     */
    public function test__generateUnknownType()
    {
        //asdasdfbsdaffffffffffffffffffffffffffffffffffffffff ffffffffffffffo ybwefosbadfbsa dofybsadfybsaoeyb rfebfyualsdfbal sdyhbfowureybfo sakldhybfvzxkl jchvbapoyeiwrg fqoweyfhbsaldvc ubasodfybsaeyofb wqeyrfbqwodfuiyb xcavoukyhadbpwey rfqweifubansdlfiyb
        $prefix = realpath(dirname(__FILE__) . '/../../testData/');
        $file = realpath($prefix . '/basic.xml');
        if (!$file || !$prefix) {
            $this->fail('Could not find test file.');
        }

        $issueList = array(
            5 => array(
                new CbIssue(
                    $file, 10,
                    10, 'finder',
                    'description',
                    'severity'
                )
            )
        );

        $this->_cbViewReview->generate($issueList, $file, $prefix);
        $this->assertTrue(file_exists($this->_outDir . '/basic.xml.html'));
    }

    /**
     * Helper method to recursively delete a directory. Use with care.
     */
    private function delTree($dir)
    {
        $files = glob($dir . '*', GLOB_MARK );
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->delTree($file);
            } else {
                unlink($file);
            }
        }
        if (is_dir($dir)) {
            rmdir( $dir );
        }
    }
}
