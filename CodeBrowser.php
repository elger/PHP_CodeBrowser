<?php

/**
 * Start File
 * PHP file called from the command line with arguments
 * 
 * Arguments:
 * --source	Projekts PHP Source folder
 * --output HTML Output folder 
 * --xml    Cruise Control XML Source File
 *
 * @package     CodeBrowser
 * @version     CVS: $Id: CodeBrowser.php,v 1.2 2008/02/26 11:06:05 christopher Exp $
 * @author      Christopher Weckerle <christopher.weckerle@mayflower.de>
 * @copyright   2007-2008 Mayflower GmbH
 */

// Installation directory
define('PHPCB_INSTALL_DIR', dirname(__FILE__));

// required files
require_once PHPCB_INSTALL_DIR . "/src/Util/Autoloader.php";

// register autoloader
spl_autoload_register(array(
    new cbAutoloader() , 'autoload'
));

$timeStart = microtime(true);
ini_set("memory_limit", "1024M");

// XML file merged by xmlint, inherit all cruise control XML log files 
$ccXMLFile = null;

// Path to the code source folder
$sourceFolder = null;

// Path to the code browser html output folder
$htmlOutput = null;

// Path to the new generated code browser xml file
$xmlFileName = 'ccCodeBrowser.xml';

// Check (command line) arguments
$argv = $_SERVER['argv'];
foreach ($argv as $key => $argument) {
    switch ($argument) {
        case '--xml':
            $ccXMLFile = $argv[$key + 1];
            break;
        case '--source':
            $sourceFolder = $argv[$key + 1];
            break;
        case '--output':
            $htmlOutput = $argv[$key + 1];
            break;
    }
}

// CLIController
if (file_exists($ccXMLFile) && is_dir($sourceFolder) && is_dir($htmlOutput)) {
    
    echo "Generating PHP_CodeBrowser files\n";
    
    // init new CLIController
    $controller = new cbCLIController($ccXMLFile, $sourceFolder, $htmlOutput, $htmlOutput . '/' . $xmlFileName);
    $controller->addErrorPlugins(array(
        'cbErrorCheckstyle' , 'cbErrorPMD' , 'cbErrorCPD'
    ));
    try {
        $controller->run();
    } catch (Exception $e) {
        echo 'PHP-CodeBrowser Error: ' . $e->getMessage() . "\n\n";
    }
} else {
    if (! file_exists($ccXMLFile)) print "XML file could not be found\n";
    if (! is_dir($sourceFolder)) print "Source folder not found!\n";
    if (! is_dir($htmlOutput)) print "Ouput folder not found!\n";
    print "\nError: please check arguments\n\n --xml \t\t[/path/to/xml/]\t$ccXMLFile \n --source \t[/path/to/source/]\t$sourceFolder \n --out \t\t[/path/to/html/output]\t$htmlOutput";
    exit();
}

$timeEnd = microtime(true);
$time = $timeEnd - $timeStart;
print "\nScript took " . $time . " seconds to execute\n\n";
