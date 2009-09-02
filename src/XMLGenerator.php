<?php

class cbXMLGenerator extends cbXMLHandler
{
    /**
     * The PHP_CodeBrowser XML filename including path-to-file
     * e.g. project/build/PHP_CodeBrowser/cbXML.xml
     * 
     * @var string
     */
    public $cbXMLName;
    
    /**
     * Basic PHP_CodeBrowser XML file syntax
     *
     * @var string
     */
    public $cbXMLBasic = '<?xml version="1.0" encoding="utf-8"?><codebrowser></codebrowser>';
    
	/**
     * Setter method for PHP_CodeBrowser XML filename
     *
     * @param string $name The XML filename
     * 
     * @return void
     */
    public function setXMLName ($name)
    {
        $this->cbXMLName = $name;
    }
    
	/**
     * Generates a PHP_CodeBrowser XML base on given error list.
     * The generated XML file is saved.
     *
     * @param array $errors The cp generated error list
     * 
     * @return SimpleXMLElement
     */
    public function generateXMLFromErrors ($errors)
    {
        $cbXML = $this->loadXMLFromString($this->cbXMLBasic);
        $sortedErrors = $this->sortErrorList($errors);
        
        foreach ($sortedErrors as $key => $name) {
            
            $xmlFileNode = $errors[$key];
            $file = $cbXML->addChild('file');
            $file->addAttribute('name', $name);
            
            foreach ($xmlFileNode as $xmlItemNode) {
                
                // add childs to root file node
                $item = $file->addChild('item');
                
                $item->addAttribute('description', $xmlItemNode['description']);
                $item->addAttribute('line', $xmlItemNode['line']);
                $item->addAttribute('to-line', $xmlItemNode['to-line']);
                $item->addAttribute('source', $xmlItemNode['source']);
                $item->addAttribute('severity', $xmlItemNode['severity']);
            }
        }
        
        return $cbXML;
    }
    
    /**
     * Write the cb xml errors to file.
     * 
     * @param SimpleXmlElement $cbXMLElement The error elements
     * 
     * @return void
     */
    public function saveCbXML($cbXMLElement)
    {
        // save the SimpleXMLElement errors as XML file
        $this->saveXML($this->cbXMLName, $cbXMLElement);
    }
    
	/**
     * Sort an error list by its key and name, filtering all duplicates.
     *
     * @param array $errorList The error list 
     * 
     * @return array
     */
    public function sortErrorList ($errorList)
    {
        $list = array();
        $keys = array_unique(array_keys($errorList));
        foreach ($keys as $key) $list[$key] = $errorList[$key][0]['name'];
        
        asort($list);
        return $list;
    }
}