<?php
/**
 * XML Handler
 * 
 * PHP Version 5.2.6
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
 * @category  PHP_CodeBrowser
 * @package   PHP_CodeBrowser
 * @author    Elger Thiele <elger.thiele@mayflower.de>
 * @copyright 2007-2009 Mayflower GmbH
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.phpunit.de/
 * @since     File available since 1.0
 */

/**
 * cbXMLHandler
 * 
 * This class provides basic functionality according xml handling, like saving
 * xml structs to storage, reading, parsing or mergin xml structs.
 *
 * @category  PHP_CodeBrowser
 * @package   PHP_CodeBrowser
 * @author    Elger Thiele <elger.thiele@mayflower.de>
 * @author    Christopher Weckerle <christopher.weckerle@mayflower.de>
 * @copyright 2007-2009 Mayflower GmbH
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.phpunit.de/
 * @since     Class available since 1.0
 */
class cbXMLHandler
{
    /**
     * File handler object
     * 
     * @var cbFDHandler
     */
    public $cbFDHandler;
    
    /**
     * A list of xml files to merge
     * 
     * @var array
     */
    protected $xmlFiles;
    
    /**
     * Constructor
     * 
     * @param cbFDHandler $cbFDHandler File handler object
     */
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
    public function loadXML($filename)
    {
        if (! file_exists($filename)) {
            throw new Exception('Error: Cannot open ' . $filename);
        }
        
        return simplexml_load_file($filename);
    }
    
    /**
     * Load a XML from a string definition
     *
     * @param string $xmlString The XML string
     * 
     * @return SimpleXMLElement
     */
    public function loadXMLFromString($xmlString)
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
    public function saveXML($fileName, SimpleXMLElement $resource)
    {
        $domSXE = dom_import_simplexml($resource);
        $dom    = new DOMDocument('1.0');
        
        $dom->appendChild($dom->importNode($domSXE, true));
        $dom->formatOutput = true;
        
        $this->cbFDHandler->createFile($fileName, $dom->saveXML());
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
            if ($attributes[$itemName] == $type) {
                $amount ++;
            }
        }
        return $amount;
    }
    
    /**
     * Set a directory with xml files to merge
     * 
     * @param string $directory The path to directory where xml files are stored
     * 
     * @return array Array object of DOMDocument elements
     */
    public function addDirectory($directory)
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );
        
        while ($iterator->valid()) {
            
            $current = $iterator->current();
            if ($current->isFile() 
                && ($current->getFilename() !== $current->getBasename('.xml'))
            ) {
                $xml = new DOMDocument('1.0', 'UTF-8');
                $xml->validateOnParse = true;
                if (@$xml->load(realpath($current))) {
                    $this->addXMLFile($xml);
                }
            }
            $iterator->next();
        }
        
        if (!isset($this->xmlFiles)) {
            throw new Exception(
                sprintf('Valid xml log files could not be found in "%s"', $directory)
            );
        }
        
        return $this->xmlFiles;
    }
    
    /**
     * Add xml files to merge 
     * 
     * @param DOMDocument $domDocument The files to merge, eather as string 
     *                                 (single) or array (multiple)
     *  
     * @return void
     */
    public function addXMLFile(DOMDocument $domDocument)
    {
        $this->xmlFiles[] = $domDocument;
    }
    
    /**
     * Merges present files to a single DOMDocument
     * 
     * @return DOMDocument
     */
    public function mergeFiles()
    {
        $xml    = new DOMDocument('1.0', 'UTF-8');
        $cruise = $xml->createElement('cruisecontrol');
        
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput       = true;
        
        if (!isset($this->xmlFiles)) {
            return $xml;
        }
        
        foreach($this->xmlFiles as $xmlFile) foreach($xmlFile->childNodes as $node) {
            $cruise->appendChild($xml->importNode($node, true));
        }
        $xml->appendChild($cruise);
        return $xml;
    }
}