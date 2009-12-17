<?php
/**
 * JS and HTML Generator
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
 * CbJSGenerator
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
class CbJSGenerator
{
    /**
     * File handler object
     * 
     * @var CbFDHandler
     */
    private $_cbFDHandler;
    
    /**
     * Constructor
     * 
     * @param CbFDHandler $cbFDHandler File handler object
     */
    public function __construct(CbFDHandler $cbFDHandler) 
    {
        $this->_cbFDHandler = $cbFDHandler;    
    }
    
    /**
     * Generate javascript source tree for HTML files.
     *
     * @param array $errors The PHP_CodeBrowser error list
     * 
     * @return string
     */
    public function getJSTree($errors)
    {
        ob_start();
        echo "<script type=\"text/javascript\">";
        echo "a = new dTree('a');";
        echo "a.config.folderLinks=false;";
        echo "a.config.useSelection=false;";
        echo "a.config.useCookies=false;";
        echo "a.add(0,-1,'Code Browser','./flatView.html','','reviewView');";
        $this->_echoJSTreeNodes($this->_getFoldersFilesTree($errors), 0, $errors);
        echo "document.write(a);";
        echo "</script>";
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
    
    /**
     * Generate javascript highlighted source for HTML files. 
     *
     * @param string $fileName      The filename where source should be highlighted
     * @param arry   $errors        The error list for this files as highlight base
     * @param string $projectSource Path to project source directory
     * 
     * @return string
     * @see cbFDHandler::loadFile
     */
    public function getHighlightedSource($fileName, $errors, $projectSource)
    {
        ob_start();
        
        $code = $this->_cbFDHandler->loadFile(
            $projectSource . DIRECTORY_SEPARATOR . $fileName
        );
        ini_set('highlight.comment', 'comment');
        ini_set('highlight.default', 'default');
        ini_set('highlight.keyword', 'keyword');
        ini_set('highlight.string', 'string');
        ini_set('highlight.html', 'html');
        
        $code     = highlight_string($code, true);
        $code     = str_replace(
            array('&nbsp;' , '&amp;' , '<br />' , '<span style="color: '), 
            array(' ' , '&#38;' , "\n" , '<span class="'), 
            substr($code, 33, - 15)
        );
        $code     = trim($code);
        $code     = str_replace("\r", "", $code);
        $lines    = explode("\n", $code);
        $previous = false;
        $openTag  = 0;
        
        // Output Listing 
        echo " <ol class=\"code\">\n";
        foreach ($lines as $key => $line) {
            if (substr($line, 0, 7) == '</span>') {
                $previous = false;
                $line     = substr($line, 7);
            }
            
            if (empty($line)) {
                $line = '&#160;';
            }   
            if ($previous) {
                $line = "<span class=\"$previous\">" . $line;
            }
            
            // Set Previous Style
            if (strpos($line, '<span') !== false) {
                switch (substr($line, strrpos($line, '<span') + 13, 1)) {
                case 'c': $previous = 'comment';
                    break;
                case 'd': $previous = 'default';
                    break;
                case 'k': $previous = 'keyword';
                    break;
                case 's': $previous = 'string';
                    break;
                }
            }
            
            // Unset Previous Style Unless Span Continues 
            if (substr($line, - 7) == '</span>') {
                $previous = false;
            } elseif ($previous) {
                $line .= '</span>';
            }
            
            $num           = $key + 1;
            $classname     = 'white';
            $classnameEven = 'even';
            $prefix        = '';
            $suffix        = '';
            $max           = 0;
            $min           = count($lines);
            $amountOfErr   = 0;
            
            foreach ($errors as $error) {
                
                if (($error['line'] <= $num) && ($error['to-line'] >= $num)) {
                    
                    if ($max <= (int)$error['to-line']) {
                        $max = (int)$error['to-line'];
                    }    
                    if ($min >= (int)$error['line']) {
                        $min = (int)$error['line'];
                    }   
                    
                    $classnameEven = 'transparent';
                    $classname     = 'transparent';
                    
                    if ((int)$error['line'] == (int)$num) { 
                        $prefix = sprintf(
                            '<li id="line-%s-%s" class="%s" ><ul>', 
                            $error['line'], 
                            $error['to-line'], 
                            ($openTag > 0 ) 
                            ? 'transparent' 
                            : (($prefix != '') ? 'moreErrors' : $error['source'])
                        );
                    }
                    
                    if ((int)$error['to-line'] == (int)$num) {
                        if ($min != $max && $suffix != '') {
                            $amountOfErr++;
                        }
                        $suffix = "</ul></li>";
                    }
                }
            }
            
            if ($prefix != '') {
                $openTag++;    
            }
            if (($num < $max && $min == $num) || $openTag == 0) {
                $suffix = '';
            }
            if ($suffix != '' && $openTag > 0) {
                $openTag--;
            }
            if ($amountOfErr > 0 && $suffix != '' && $openTag > 0) {
                    $openTag--;
                    $suffix .= '</ul></li>';    
            }
            echo sprintf(
                '%s<li id="line-%d" class="%s"><a name="line-%d"></a><code>%s</code></li>%s' . "\n", 
                $prefix, 
                $num, 
                (($key % 2) ? $classnameEven : $classname), 
                $num, 
                $line, 
                $suffix
            );
        }
        echo "</ol>\n";
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
    
    /**
     * Echos out the Tree recursive. 
     * This function should be only called in a buffered output. 
     *
     * @param array   $tree     Tree array of folders/files
     * @param integer $parentId Id of the parent object
     * @param array   $errors   List of errors
     * 
     * @return void
     */
    private function _echoJSTreeNodes($tree, $parentId, $errors)
    {
        static $id = 0;
        foreach ($tree as $key => $value) {
            $id ++;
            if (is_array($value)) {
                echo sprintf("a.add(%d,%d,'%s', '');", $id, $parentId, $key);
                $this->_echoJSTreeNodes($value, $id, $errors);
            } else {
                $key = sprintf(
                    '%s ( <span class="errors">%sE</span> | <span class="notices">%sN</span> )', 
                    $key, 
                    $errors[$value]['count_errors'], 
                    $errors[$value]['count_notices']
                );
                echo sprintf(
                    "a.add(%d,%d,'%s','./%s.html','','reviewView');", 
                    $id, 
                    $parentId, 
                    $key, 
                    str_replace(DIRECTORY_SEPARATOR, '/', $errors[$value]['complete'])
                );
            }
        }
    }
    
    /**
     * Get folders and files in an tree array
     * 
     * @param array $files Array of files with errors
     *
     * @return array
     */
    private function _getFoldersFilesTree($files)
    {
        $result = array();
        if (is_array($files)) {
            foreach ($files as $fileId => $file) {
                $folders = explode(DIRECTORY_SEPARATOR, $file['complete']);
                
                $folders[count($folders)] = $fileId;
                krsort($folders);
                $tree = null;
                
                foreach ($folders as $folder) {
                    if (is_numeric($folder)) {
                        $tree = $folder;
                    } elseif ($folder != '') {
                        $tree = array($folder => $tree);
                    }
                }
                $result = array_merge_recursive($tree, $result);
            }
        }
        return $this->_sortFolders($result);
    }
    
    /**
     * Sort a array recursive with folders first.
     * 
     * @param array $folders The folder file array
     * 
     * @return array
     */
    private function _sortFolders($folders)
    {
        $tmpFiles = array();
        $tmpFolders = array();
        foreach ($folders as $key => $value) {
            if (is_array($value)) {
                $tmpFolders[$key] = $this->_sortFolders($value);
            } else {
                $tmpFiles[$key] = $value;
            }
        }
        ksort($tmpFolders);
        ksort($tmpFiles);
        
        return $tmpFolders + $tmpFiles;
    }
}