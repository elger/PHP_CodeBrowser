<?php
/**
 * HTML Generator
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
 * CbHTMLGenerator
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
class CbHTMLGenerator
{
    /**
     * Template directory
     *
     * @var string
     */
    private $_templateDir;

    /**
     * Output directory
     *
     * @var string
     */
    private $_outputDir;

    /**
     * Available ressource folders
     *
     * @var array
     */
    private $_ressourceFolders = array('css' , 'js' , 'img');

    /**
     * File handler object
     *
     * @var cbIOHelper
     */
    private $_cbIOHelper;

    /**
     * Error handler object
     *
     * @var cbIssueHandler
     */
    private $_cbIssueHandler;

    /**
     * JS / HTML generator object
     *
     * @var cbJSGenerator
     */
    private $_cbJSGenerator;

    /**
     * Constructor
     *
     * @param CbIOHelper    $cbIOHelper    File handler object
     * @param CbIssueHandler $cbIssueHandler Error handler object
     * @param CbJsGenerator  $cbJSGenerator  JS / HTML generator object
     */
    public function __construct(CbIOHelper $cbIOHelper, CbIssueHandler $cbIssueHandler, CbJSGenerator $cbJSGenerator)
    {
        $this->_cbIOHelper    = $cbIOHelper;
        $this->_cbIssueHandler = $cbIssueHandler;
        $this->_cbJSGenerator  = $cbJSGenerator;
    }

    /**
     * Setter method
     *
     * @param string $templateDir Path to template diretory
     *
     * @return void
     */
    public function setTemplateDir ($templateDir)
    {
        $this->_templateDir = $templateDir;
    }

    /**
     * Setter mothod
     * Path where generated view-files should be saved.
     *
     * @param string $outputDir Path to output directory
     *
     * @return void
     */
    public function setOutputDir($outputDir)
    {
        $this->_outputDir = $outputDir;
    }

    /**
     * Default start page
     *
     * @param array $errors List of all PHP_CodeBrowser errors
     *
     * @return void
     * @throws Exception
     */
    public function generateViewFlat($errors)
    {
        if (!is_array($errors)) {
            throw new Exception('Wrong data format for errorlist!');
        }

        $data['title']   = 'Code Browser - Overview (flat view mode)';
        $data['files']   = $errors;
        $data['csspath'] = '';

        $dataGenrate['title']   = $data['title'];
        $dataGenrate['csspath'] = '';
        $dataGenrate['content'] = $this->_render('flatView', $data);

        $this->_generateView($dataGenrate, 'flatView.html');
    }

    /**
     * JS Tree view page
     *
     * @param array $errors List of all PHP_CodeBrowser errors
     *
     * @return void
     * @throws Exception
     */
    public function generateViewTree($errors)
    {
        if (!is_array($errors)) {
            throw new Exception('Wrong data format for errorlist!');
        }

        $data['title']   = 'Code Browser - Tree View';
        $data['files']   = $errors;
        $data['csspath'] = '';
        $data['tree']    = $this->_cbJSGenerator->getJSTree($errors);

        $dataGenrate['title']   = $data['title'];
        $dataGenrate['csspath'] = '';
        $dataGenrate['content'] = $this->_render('tree', $data);

        $this->_generateView($dataGenrate, 'tree.html');
    }

    /**
     * Code Browser for each file with errors
     *
     * @param array  $errors        List of all PHP_CodeBrowser errors
     * @param string $cbXMLFile     Name of the PHP_CodeBrowser error XML file
     * @param string $projectSource Path to project source files
     *
     * @return void
     * @throws Exception
     * @see cbIssueHandler::getIssuesByFile
     * @see cbJSGenerator::getHighlightedSource
     */
    public function generateViewReview($errors, $cbXMLFile, $projectSource)
    {
        if (!is_array($errors)) {
            throw new Exception('Wrong data format for errorlist!');
        }

        $data['title'] = 'Code Browser - Review View';
        foreach ($errors as $file) {
            $data['errors']   = $this->_cbIssueHandler->getIssuesByFile(
                $cbXMLFile,
                $file['complete']
            );
            $data['source']   = $this->_cbJSGenerator->getHighlightedSource(
                $file['complete'],
                $data['errors'],
                $projectSource
            );
            $data['filepath'] = $file['complete'];
            $data['csspath']  = '';
            $depth            = substr_count($file['complete'], DIRECTORY_SEPARATOR);
            for ($i = 1; $i <= $depth; $i ++) {
                $data['csspath'] .= '../';
            }

            $dataGenrate['title']   = $data['title'];
            $dataGenrate['csspath'] = $data['csspath'];
            $dataGenrate['content'] = $this->_render('reviewView', $data);

            $this->_generateView($dataGenrate, $file['complete'] . '.html');
        }
    }

    /**
     * Copy needed resources to output directory
     *
     * @param boolean $hasErrors Flag to define which index.html will be generated.
     *
     * @return void
     * @throws Exception
     * @see cbIOHelper::copyFile
     */
    public function copyRessourceFolders($hasErrors = true)
    {
        if (!isset($this->_outputDir)) {
            throw new Exception('Output directory is not set!');
        }

        foreach ($this->_ressourceFolders as $folder) {
            $this->_cbIOHelper->copyDirectory(
                $this->_templateDir . DIRECTORY_SEPARATOR . $folder,
                $this->_outputDir . DIRECTORY_SEPARATOR . $folder
            );
        }

        $template = ($hasErrors) ?  'index.tpl' : 'noErrors.tpl';

        $content = $this->_cbIOHelper->loadFile(
            $this->_templateDir . DIRECTORY_SEPARATOR . $template
        );
        $this->_cbIOHelper->createFile(
            $this->_outputDir . DIRECTORY_SEPARATOR . 'index.html', $content
        );
    }

    /**
     * Render different template types
     *
     * @param string $templateName Template file to use for rendering
     * @param array  $data         Given dataset to use for rendering
     *
     * @return string              HTML files as string from output buffer
     */
    private function _render($templateName, $data)
    {
        $filePath = realpath($this->_templateDir)
            . DIRECTORY_SEPARATOR
            . $templateName
            . '.tpl';

        if (!count($data)) {
            return '';
        }

        extract($data, EXTR_SKIP);
        ob_start();
        include realpath($this->_templateDir)
            . DIRECTORY_SEPARATOR
            . $templateName
            . '.tpl';
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    /**
     * Save rendered file to output directory
     *
     * @param array  $data     Dataset information used for rendering
     * @param string $fileName The filename of analyzed file
     *
     * @return void
     * @see cbIOHelper::createFile
     */
    private function _generateView($data, $fileName)
    {
        $this->_cbIOHelper->createFile(
            $this->_outputDir
            . DIRECTORY_SEPARATOR
            . $fileName,
            $this->_render('page', $data)
        );
    }
}
