<?php
/**
 * CruiseControlErrors
 * 
 * PHP version 5
 *
 * @package     CodeBrowser
 * @version     CVS: $Id: CruiseControlErrors.php,v 1.1.1.1 2008/02/21 13:12:18 christopher Exp $
 * @author      Christopher Weckerle <christopher.weckerle@mayflower.de>
 * @copyright   2007 Mayflower GmbH
 */

/**
 * CruiseControlErrors Class 
 * 
 * Manage the new generated XML File 
 * 
 * @author     Christopher Weckerle <christopher.weckerle@mayflower.de>
 * @copyright  2007 Mayflower GmbH
 */
class cbErrorHandler
{
    /**
     * cbXMLHandler object
     * 
     * @var cbXMLHandler
     */
    public $cbXMLHandler;
    
    /**
     * Default constructor
     * 
     * @param cbXMLHandler $cbXMLHandler The cbXMLHandler object
     */
    public function __construct (cbXMLHandler $cbXMLHandler)
    {
        $this->cbXMLHandler = $cbXMLHandler;
    }
    
    /**
     * Get the error according to a defined file.
     * 
     * @param string $cbXMLFile The XML file to read in
     * @param string $fileName  The filename to search for
     * 
     * @return SimpleXMLElement
     */
    public function getErrorsByFile ($cbXMLFile, $fileName)
    {
        $element = $this->cbXMLHandler->loadXML($cbXMLFile);
        foreach ($element as $file) {
            if ($file['name'] == $fileName) return $file->children();
        }
    }
    
    /**
     * Get all the filenames with errors.
     * 
     * @param string $cbXMLFileName The XML file with all information
     * 
     * @return array
     */
    public function getFilesWithErrors ($cbXMLFileName)
    {
        $element = $this->cbXMLHandler->loadXML($cbXMLFileName);
        $files   = null;
        
        foreach ($element->children() as $file) {
            $tmp['complete']      = (string)$file['name'];
            $tmp['file']          = basename($file['name']);
            $tmp['path']          = dirname($file['name']);
            $tmp['count_errors']  = $this->cbXMLHandler->countItems($file->children(), 'severity', 'error');
            $tmp['count_notices'] = $this->cbXMLHandler->countItems($file->children(), 'severity', 'notice');
            $files[]              = $tmp;
        }
        return $files;
    }
}
