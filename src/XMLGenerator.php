<?php
/**
 * XML Generator
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
 * cbXMLGenerator
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
    public $cbXMLBasic 
        = '<?xml version="1.0" encoding="utf-8"?><codebrowser></codebrowser>';
    
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