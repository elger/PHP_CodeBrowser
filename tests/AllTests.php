<?php

define('PHPCB_ROOT_DIR', dirname( __FILE__ ) . '/../');
define('PHPCB_SOURCE', PHPCB_ROOT_DIR . 'src/');
define('PHPCB_TEST_DIR', PHPCB_ROOT_DIR . 'tests/testData/');
define('PHPCB_TEST_OUTPUT', PHPCB_TEST_DIR . 'output/');

require_once PHPCB_SOURCE . '/Util/Autoloader.php';

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once dirname( __FILE__ ) . '/src/JSGeneratorTest.php';
require_once dirname( __FILE__ ) . '/src/HTMLGeneratorTest.php';
require_once dirname( __FILE__ ) . '/src/ErrorHandlerTest.php';
require_once dirname( __FILE__ ) . '/src/XMLGeneratorTest.php';
require_once dirname( __FILE__ ) . '/src/XMLHandlerTest.php';
require_once dirname( __FILE__ ) . '/src/FDHandlerTest.php';
require_once dirname( __FILE__ ) . '/src/PluginErrorTest.php';

require_once dirname( __FILE__ ) . '/src/Plugins/ErrorPMDTest.php';


class cbAllTests extends PHPUnit_Framework_TestSuite
{
    public function __construct ()
    {
        spl_autoload_register( array( new cbAutoloader(), 'autoload' ) );
        
        $this->setName('AllTests');
        
        // plugins
        $this->addTestSuite('cbErrorPMDTest');
        
        // src
        $this->addTestSuite('cbPluginErrorTest');
   	    $this->addTestSuite('cbFDHandlerTest');
        $this->addTestSuite('cbXMLHandlerTest');
        $this->addTestSuite('cbXMLGeneratorTest');
        $this->addTestSuite('cbErrorHandlerTest');
        $this->addTestSuite('cbHTMLGeneratorTest');
        $this->addTestSuite('cbJSGeneratorTest');
        
    }
    
    public static function suite ()
    {
        return new self();
    }
}

