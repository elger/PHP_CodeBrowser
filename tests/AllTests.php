<?php
/**
 * Test suite
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

if (!defined('PHPCB_ROOT_DIR')) define('PHPCB_ROOT_DIR',    dirname( __FILE__ ) . '/../');
if (!defined('PHPCB_SOURCE')) define('PHPCB_SOURCE',      realpath(PHPCB_ROOT_DIR) . '/src');
if (!defined('PHPCB_TEST_DIR')) define('PHPCB_TEST_DIR',    realpath(PHPCB_ROOT_DIR) . '/tests/testData');
if (!defined('PHPCB_TEST_OUTPUT')) define('PHPCB_TEST_OUTPUT', PHPCB_TEST_DIR . '/output');

require_once PHPCB_SOURCE . '/Util/Autoloader.php';

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Filter.php';

require_once dirname( __FILE__ ) . '/src/JSGeneratorTest.php';
require_once dirname( __FILE__ ) . '/src/HTMLGeneratorTest.php';
require_once dirname( __FILE__ ) . '/src/ErrorHandlerTest.php';
require_once dirname( __FILE__ ) . '/src/XMLGeneratorTest.php';
require_once dirname( __FILE__ ) . '/src/XMLHandlerTest.php';
require_once dirname( __FILE__ ) . '/src/FDHandlerTest.php';
require_once dirname( __FILE__ ) . '/src/PluginErrorTest.php';

require_once dirname( __FILE__ ) . '/src/Plugins/ErrorPMDTest.php';
require_once dirname( __FILE__ ) . '/src/Plugins/ErrorCPDTest.php';
require_once dirname( __FILE__ ) . '/src/Plugins/ErrorCheckstyleTest.php';
require_once dirname( __FILE__ ) . '/src/Plugins/ErrorPadawanTest.php';

PHPUnit_Util_Filter::addDirectoryToWhitelist(realpath(PHPCB_SOURCE));

/**
 * CbAlltests
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
class CbAllTests extends PHPUnit_Framework_TestSuite
{
    /**
     * Construct
     */
    public function __construct ()
    {
        spl_autoload_register( array( new CbAutoloader(), 'autoload' ) );

        $this->setName('AllTests');

        // source
        $this->addTestSuite('CbFDHandlerTest');
        $this->addTestSuite('CbXMLHandlerTest');
        $this->addTestSuite('CbXMLGeneratorTest');
        $this->addTestSuite('CbErrorHandlerTest');
        $this->addTestSuite('CbPluginErrorTest');
        $this->addTestSuite('CbHTMLGeneratorTest');
        $this->addTestSuite('CbJSGeneratorTest');

        // plugins
        $this->addTestSuite('CbErrorPMDTest');
        $this->addTestSuite('CbErrorCPDTest');
        $this->addTestSuite('CbErrorCheckstyleTest');
        $this->addTestSuite('CbErrorPadawanTest');
    }

    /**
     * Suite
     *
     * @return void
     */
    public static function suite ()
    {
        return new self();
    }
}

