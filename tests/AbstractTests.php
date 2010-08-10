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
 * @since      File available since  0.1.0
 */

/**
 * CbAbstractTests
 *
 * @category   PHP_CodeBrowser
 * @package    PHP_CodeBrowser
 * @subpackage PHPUnit
 * @author     Elger Thiele <elger.thiele@mayflower.de>
 * @copyright  2007-2009 Mayflower GmbH
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since  0.1.0
 */
class CbAbstractTests extends PHPUnit_Framework_TestCase
{
    /**
     * Merged cruisecontrol XML error file
     *
     * @var string
     */
    protected static $_ccXMLFile;

    /**
     * PHP_CodeBrowser error file
     *
     * @var string
     */
    protected static $_cbXMLFile;

    /**
     * Basic XML file with valid headers
     *
     * @var string
     */
    protected static $_cbXMLBasic;

    /**
     * Path information for a dummy TXT file
     *
     * @var string
     */
    protected static $_cbTestFile;

    /**
     * Path information for a dummy XML file
     *
     * @var string
     */
    protected static $_cbTestXML;

    /**
     * File of serialized cb error list
     *
     * @var string
     */
    protected static $_serializedErrors;

    /**
     * Path information for generated XML test file
     *
     * @var string
     */
    protected static $_cbGeneratedXMLTest;

    /**
     * Global setup method for all test cases. Basic variables are initalized.
     *
     * @return void
     */
    protected function setUp()
    {

        parent::setUp();

        self::$_cbXMLBasic         = PHPCB_TEST_LOGS . '/basic.xml';

        if (is_dir(PHPCB_TEST_OUTPUT)) {
            $this->_cleanUp(PHPCB_TEST_OUTPUT);
            rmdir(PHPCB_TEST_OUTPUT);
        }
        mkdir(PHPCB_TEST_OUTPUT);
    }

    /**
     * Global tear down method for all test cases.
     * Cleaning up generated data and output.
     *
     * @return void
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->_cleanUp(PHPCB_TEST_OUTPUT);
        rmdir(PHPCB_TEST_OUTPUT);
    }

    /**
     * Setup a mock object for cbXMLHandler class, and provide
     * a list of functions that should be mockable.
     *
     * @return object
     */
    protected function _getMockXMLHandler()
    {
        $functions      = array('loadXML', 'countItems', 'saveXML');
        $params         = array($this->_getMockFDHandler());
        $mockXMLHandler = $this->getMock('CbXMLHandler', $functions, $params);

        return $mockXMLHandler;
    }

    /**
     * Setup a mock object for cbFDHandler class, and provide
     * a list of functions that should be mockable.
     *
     * @return object
     */
    protected function _getMockFDHandler()
    {
        $functions = array(
            'createFile',
            'loadFile',
            'copyFile',
            'copyDirectory'
        );
        $mockFDHandler = $this->getMock('CbFDHandler', $functions);

        return $mockFDHandler;
    }

    /**
     * Setup a mock object for cbJSGenerator class, and provide
     * a list of functions that should be mockable.
     *
     * @return object
     */
    protected function _getMockJSGenerator()
    {
        $functions = array('getJSTree', 'getHighlightedSource');
        $params = array($this->_getMockFDHandler());
        $mockJSGenerator = $this->getMock('CbJSGenerator', $functions, $params);

        return $mockJSGenerator;
    }

    /**
     * Setup a mock object for cbErrorHandler class, and provide
     * a list of functions that should be mockable.
     *
     * @return object
     */
    protected function _getMockErrorHandler()
    {
        $functions = array('getErrorsByFile');
        $params = array($this->_getMockXMLHandler());
        $mockErrorHandler = $this->getMock(
            'CbErrorHandler',
            $functions,
            $params
        );

        return $mockErrorHandler;
    }

    /**
     * Load the cb error list
     *
     * @return array List of cb errors
     */
    protected function _getSerializedErrors()
    {
        return unserialize(file_get_contents(self::$_serializedErrors));
    }

    /**
     * Cleanup the test directory output folder
     *
     * @param string $dir The directory to clean up
     *
     * @return void
     */
    protected function _cleanUp($dir)
    {
        $iterator = new DirectoryIterator($dir);
        while ($iterator->valid()) {

            // delete file
            if ($iterator->isFile()) unlink($dir . '/' . $iterator->current());

            // delete folder recursive
            if (! $iterator->isDot() && $iterator->isDir()) {
                $this->_cleanUp($dir . '/' . $iterator->current());
                rmdir($dir . '/' . $iterator->current());
            }
            $iterator->next();
        }
        unset($iterator);
    }
}
