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
 * CbXMLHandler
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
class CbIssueXml extends DOMDocument
{

    public $preserveWhiteSpace = false;
    public $formatOutput = true;

    protected $_xpath;

    /**
     * Constructor
     *
     * @param CbFDHandler $cbFDHandler File handler object
     */
    public function __construct($version = '1.0', $encoding = 'UTF-8')
    {
        parent::__construct($version, $encoding);
        $this->appendChild($this->createElement('cruisecontrol'));
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
     * @return CbXMLHandler     This object
     */
    public function addDirectory($directory)
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );

        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->validateOnParse = true;

        foreach ($iterator as $current) {
            if ($current->isFile()
                && ($current->getFilename() !== $current->getBasename('.xml'))
            ) {
                echo "START Read File: ".$current->getFilename()." ".PHP_Timer::resourceUsage()."\n";

                if (@$xml->load(realpath($current))) {
                    echo "END Read File: ".$current->getFilename()." ".PHP_Timer::resourceUsage()."\n";
                    $this->addXMLFile($xml);
                    echo "ADDED File: ".$current->getFilename()." ".PHP_Timer::resourceUsage()."\n";
                }
            }
        }

        echo 'START unset $xml '.PHP_Timer::resourceUsage()."\n";
        $xml = null;
        echo 'END unset $xml '.PHP_Timer::resourceUsage()."\n";

        if (!$this->documentElement->hasChildNodes()) {
            throw new Exception(
                sprintf('Valid xml log files could not be found in "%s"', $directory)
            );
        }
        echo 'QUIT addDirectory '.PHP_Timer::resourceUsage()."\n";
        return $this;
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
        foreach ($domDocument->childNodes as $node) {
            $this->documentElement->appendChild($this->importNode($node, true));
        }
    }

    public function query($xpath, DOMNode $contextNode = null)
    {
        $start = microtime(true);
        if (!isset($this->_xpath)) {
            $this->_xpath = new DOMXPath($this);
        }

        if ($contextNode) {
            $result = $this->_xpath->query($xpath, $contextNode);
        } else {
            $result = $this->_xpath->query($xpath);
        }
        if (microtime(true)-$start > 2) {
            echo 'XPATH: '.$xpath
                .($contextNode ? ' on '.$contextNode->getNodePath() : '')
                .' '.round((microtime(true)-$start), 2)."ms\n";
        }
        return $result;
    }
}