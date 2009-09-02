<?php

class cbXMLHandler
{
    public $cbFDHandler;
    
    public function __construct(cbFDHandler $cbFDHandler) 
    {
        $this->cbFDHandler = $cbFDHandler;
    }
    
    /**
     * Load a XML file.
     *
     * @param string $filename The (path-to) file
     * 
     * @return SimpleXMLElement
     */
    public function loadXML ($filename)
    {
        if (! file_exists($filename)) throw new Exception('Error: Cannot open ' . $filename);
        
        return simplexml_load_file($filename);
    }
    
    /**
     * Load a XML from a string definition
     *
     * @param string $xmlString The XML string
     * 
     * @return SimpleXMLElement
     */
    public function loadXMLFromString ($xmlString)
    {
        return simplexml_load_string($xmlString);
    }
    
    /**
     * Save a SimpleXMLElement as XML file.
     *
     * @param string           $fileName The filename of XML file
     * @param SimpleXMLElement $resource The XML resource
     * 
     * @return void 
     */
    public function saveXML ($fileName, SimpleXMLElement $resource)
    {
        $this->cbFDHandler->createFile($fileName, $resource->asXML());
    }
    
    /**
     * Count specified items in a given XML node.
     *
     * @param SimpleXMLElement $element  The XML element node
     * @param string           $itemName The item name to find
     * @param string           $type     The type of the item to count
     * 
     * @return int
     */
    public function countItems (SimpleXMLElement $element, $itemName, $type)
    {
        $amount = 0;
        foreach ($element as $item) {
            $attributes = $item->attributes();
            if ($attributes[$itemName] == $type) $amount ++;
        }
        return $amount;
    }
}