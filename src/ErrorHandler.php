<?php
/**
 * Error handler
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
 * cbErrorHandler
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
            if ($file['name'] == $fileName) {
                return $file->children();
            }
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
            $tmp['count_errors']  = $this->cbXMLHandler->countItems(
                $file->children(), 
                'severity', 
                'error'
            );
            $tmp['count_notices'] = $this->cbXMLHandler->countItems(
                $file->children(), 
                'severity', 
                'notice'
            );
            $tmp['count_notices'] += $this->cbXMLHandler->countItems(
                $file->children(), 
                'severity', 
                'warning'
            );
            $files[]              = $tmp;
        }
        return $files;
    }
}
