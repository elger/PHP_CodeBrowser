<?php

abstract class cbPluginError
{
    /**
     * The name of the plugin.
     * This should be the name that is written to the XML error files by 
     * cruisecontrol.
     *
     * @var string
     */
    public $pluginName;
    
    /**
     * The path to the source directory
     *
     * @var string
     */
    public $projectSourceDir;
    
    /**
     * The loaded XML file 
     * 
     * @var SimpleXMLElement
     */
    private $_ccXMLFile;
    
    /**
     * The cbXMLHandler object
     * 
     * @var cbXMLHandler
     */
    private $_cbXMLHandler;
    
    /**
     * Constructor
     *
     * @param string       $sourcePath The project source path
     * @param cbXMLHandler $cbXMLHandler xbXMLHandler object
     */
    public function __construct ($projectSourceDir, cbXMLHandler $cbXMLHandler)
    {
        $this->setPluginName();
        $this->setSourcePath($projectSourceDir);
        $this->_cbXMLHandler = $cbXMLHandler;
    }
    
    /**
     * Setter method for cruisecontrol XML File
     *
     * @param string $ccXMLFile The cruisecontrol XML File
     * 
     * @return void
     */
    public function setXML ($ccXMLFile)
    {
        $this->_ccXMLFile = $this->_cbXMLHandler->loadXML($ccXMLFile);
    }
    
    /**
     * Setter method for the project source directory
     *
     * @param string $projectSourceDir The project source directory
     * 
     * @return void
     */
    public function setSourcePath ($projectSourceDir)
    {
        $this->projectSourceDir = $projectSourceDir;
    }
    
    /**
     * Parse the cc XML file for defined error type, e.g. "pmd" and map this 
     * error to the needed PHP_CodeBrowser format.
     *
     * @return array
     */
    public function parseXMLError ()
    {
        if (! isset($this->_ccXMLFile)) throw new Exception('XML file not loaded!');
        if (! isset($this->_ccXMLFile->{$this->pluginName}) 
        || ! is_object($children = $this->_ccXMLFile->{$this->pluginName}->children())) return array();
        
        $errors = array();
        foreach ($children as $child) $errors[] = $this->mapError($child);
        
        $errorList = array();
        foreach ($errors as $list) foreach ($list as $error)  {
            $errorList[hash('md5', $error['name'])][] = $error;
        }
        
        return $errorList;
    }
    
	/**
     * Cut off the difference between absolute/relative path.
     * e.g.
     * /home/www/htdocs/myProject/source/index.php
     * /myProject
     * will leave source/index.php
     *
     * @param string $absolutePath The absolute path to file
     * @param string $relativePath The relative path
     * 
     * @return string|null
     */
    public function getRelativeFilePath ($absolutePath, $relativePath)
    {
        return preg_replace(array(sprintf('(.*%s/)', $relativePath)), '', $absolutePath);
    }
    
    /**
     * Setter method for the plugin name.
     * This name should be the one used by cruisecontrol.
     *
     * @return void
     */
    abstract function setPluginName ();
    
    /**
     * The detailed mapper method for each single plugin, returning an errorlist.
     *
     * @param SimpleXMLElement $element The errorlist for each plugin node
     * 
     * @return array
     */
    abstract function mapError (SimpleXMLElement $element);
}
