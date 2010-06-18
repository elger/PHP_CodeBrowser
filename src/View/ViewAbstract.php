<?php
/**
 * View Abtract
 *
 * PHP Version 5.3.0
 *
 * Copyright (c) 2007-2010, Mayflower GmbH
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
 * @author    Jan Mergler <jan.mergler@mayflower.de>
 * @copyright 2007-2010 Mayflower GmbH
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.phpunit.de/
 * @since     File available since 1.0
 */

/**
 * CbViewAbstract
 *
 * This class is generating the highlighted and formatted html view for file.
 *
 * @category  PHP_CodeBrowser
 * @package   PHP_CodeBrowser
 * @author    Elger Thiele <elger.thiele@mayflower.de>
 * @author    Jan Mergler <jan.mergler@mayflower.de>
 * @copyright 2007-2010 Mayflower GmbH
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.phpunit.de/
 * @since     Class available since 1.0
 * 
 * @TODO check for valid path information, throw error if path does not exists
 */
class CbViewAbstract
{
    /**
     * Template directory
     *
     * @var string
     */
    protected $_templateDir;

    /**
     * Output directory
     *
     * @var string
     */
    protected $_outputDir;

    /**
     * Available ressource folders
     *
     * @var array
     */
    protected $_ressourceFolders = array('css', 'js', 'img');

    /**
     * IOHelper for filesystem interaction.
     *
     * @var CbIOHelper
     */
    protected $_ioHelper;

    /**
     * Default Constructor
     *
     * @param CbIOHelper $ioHelper The CbIOHelper used for filesystem interaction.
     */
    public function __construct($ioHelper)
    {
        $this->_ioHelper = $ioHelper;
    }

    /**
     * Setter method
     *
     * @param String $templateDir Path to template diretory
     *
     * @return void
     */
    public function setTemplateDir ($templateDir)
    {
        $this->_templateDir = realpath($templateDir);
    }

    /**
     * Setter mothod
     * Path where generated view-files should be saved.
     *
     * @param String $outputDir Path to output directory
     *
     * @return void
     */
    public function setOutputDir($outputDir)
    {
        $this->_outputDir = realpath($outputDir);
        if (!$this->_outputDir) {
            throw new Exception("Specified output directory '$outputDir' doesn't exist");
        }
    }


    /**
     * Copy needed resources to output directory
     *
     * @param boolean $hasErrors Flag to define which index.html will be generated.
     *
     * @return void
     * @throws Exception
     * @see cbIOHelper->copyFile
     * 
     * @TODO Refactor this method
     */
    public function copyRessourceFolders($hasErrors = true)
    {
        if (!isset($this->_outputDir)) {
            throw new Exception('Output directory is not set!');
        }

        foreach ($this->_ressourceFolders as $folder) {
            $this->_ioHelper->copyDirectory(
                $this->_templateDir . DIRECTORY_SEPARATOR . $folder,
                $this->_outputDir . DIRECTORY_SEPARATOR . $folder
            );
        }

        //TODO: Move this intelligently to generateIndex
        //$template = ($hasErrors) ?  'index.tpl' : 'noErrors.tpl';
        //$content  = $this->_ioHelper->loadFile(
        //    $this->_templateDir . '/' . $template
        //);
        //$this->_ioHelper->createFile(
        //    $this->_outputDir . '/index.html', $content
        //);
    }

    /**
     * Creates a javascript-filled index.html
     *
     * @param Array $files The files to show in the sidebar
     *
     * @return void
     */
    public function generateIndex(Array $issueCounts)
    {
        $this->_ioHelper->createFile(
            $this->_outputDir . '/index.html',
            $this->_render(
                'index',
                array(
                    'files' => $this->fileListToDirTree(
                                            array_keys($issueCounts)))
                )
        );
    }

    /**
     * Gets a list of complete Filenames and returns a tree-is structure
     * representing the directories.
     *
     * @param Array $files The list of files.
     *
     * @return Array A treeish structure representing the directory structure.
     */
    protected function fileListToDirTree(Array $files)
    {
        $prefix = $this->_ioHelper->getCommonPathPrefix($files);

        $shortFiles = array();
        foreach ($files as $f) {
            $shortFiles[] = substr($f, strlen($prefix) + 1);
        }

        $fileTree = array();
        foreach ($shortFiles as $f) {
            $pos = strpos($f, DIRECTORY_SEPARATOR);
            $parent =& $fileTree;
            while ($pos !== false) {
                $dir = substr($f, 0, $pos);
                $f = substr($f, $pos + 1);

                if (!array_key_exists($dir, $parent)) {
                    $parent[$dir] = array();
                }
                $parent =& $parent[$dir];
                $pos = strpos($f, DIRECTORY_SEPARATOR);
            }
            $parent[] = $f;
        }
        return $fileTree;
    }

    /**
     * Render a template.
     * 
     * Defined template is parsed and filled with data.
     * Rendered content is read from output buffer.
     *
     * @param String $templateName Template file to use for rendering
     * @param Array  $data         Given dataset to use for rendering
     *
     * @return String              HTML files as string from output buffer
     */
    protected function _render($templateName, $data)
    {
        $filePath = $this->_templateDir . DIRECTORY_SEPARATOR . $templateName . '.tpl';

        if (!count($data)) {
            return '';
        }

        extract($data, EXTR_SKIP);
        
        ob_start();
        include($filePath);
        $contents = ob_get_contents();
        ob_end_clean();
        
        return $contents;
    }
}
