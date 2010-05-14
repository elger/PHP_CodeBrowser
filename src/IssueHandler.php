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
 * CbIssueHandler
 *
 * This class is providing a lists of errors as well lists of filenames that have
 * related errors.
 * For providing this lists the prior generated PHP_CodeBrowser error xml file is
 * parsed.
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
class CbIssueHandler
{
    /**
     * CbIssueXml object
     *
     * @var CbIssueXml
     */
    public $cbIssueXml;

    /**
     * Supported file types for code browsing
     *
     * @var array
     */
    private $_supportedFileTypes = array('php');

    protected $plugins = array();

    /**
     * Default constructor
     *
     * @param CbIssueXml $cbIssueXml The cbXMLHandler object
     */
    public function __construct (CbIssueXml $cbIssueXml, array $plugins)
    {
        $this->cbIssueXml = $cbIssueXml;
        $this->plugins = $plugins;
    }

    /**
     * Get the path all errors have in common.
     *
     * @param array $errors List of all errors and its attributes
     *
     * @return string
     */
    public function getCommonSourcePath($errors)
    {
        $path = '';
        foreach ($errors as $error) {
            $path = $this->_getCommonErrorPath($error['path'], $path);
        }
        return $path;
    }

    /**
     * Substitude the path all errors have in common.
     *
     * @param array $errors The error list
     *
     * @return array
     */
    public function replaceCommonSourcePath($errors)
    {
        $commonSourcePath = $this->getCommonSourcePath($errors);

        if (!strlen($commonSourcePath)) {
            return $errors;
        }

        foreach ($errors as $key => &$error) {
            $error['complete'] = preg_replace(
                array(
                    sprintf(
                        '(.*%s\%s)',
                        $commonSourcePath,
                        DIRECTORY_SEPARATOR
                    )
                ),
                '',
                $error['complete']
            );
            $error['path'] = $commonSourcePath;
        }
        return $errors;
    }

    /**
     * Parse directory to get all files. Merging existing error list.
     *
     * @param string $sourceDir The source directory to parse
     * @param array  $errors    The existing error list
     *
     * @return array
     */
    public function parseSourceDirectory($sourceDir, $errors)
    {
        if (!isset($sourceDir)) {
            return $errors;
        }

        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceDir),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $fileList = array();
        foreach ($items as $name => $item) {

            // only use supported files
            $boolean = !in_array(
                substr($item->getFilename(), - 3),
                $this->_supportedFileTypes
            );

            if ($boolean) {
                continue;
            }

            // set proper error format for files without errors
            $tmp['complete']      = preg_replace(
                array(
                    sprintf(
                        '(.*%s\%s)',
                        basename(realpath($sourceDir)),
                        DIRECTORY_SEPARATOR
                    )
                ),
                '',
                realpath($item->getPath() . DIRECTORY_SEPARATOR . $item->getFilename())
            );
            $tmp['file']          = $item->getFilename();
            $tmp['path']          = rtrim(realpath($sourceDir), DIRECTORY_SEPARATOR);
            $tmp['count_errors']  = 0;
            $tmp['count_notices'] = 0;

            $exists = false;
            foreach ($errors as $error) {

                // check if file already exists in error list
                if ($error['file'] == $tmp['file']) {
                    $cPath = rtrim(
                        str_replace($error['complete'], '', $tmp['complete']),
                        DIRECTORY_SEPARATOR
                    );

                    // add error only if relative path and filename match
                    $comp = substr_compare(
                        $error['path'],
                        $cPath,
                        -1 * strlen($cPath)
                    );

                    if (0 === $comp || $error['complete'] === $tmp['complete']) {
                        $error['complete'] = sprintf(
                            '%s%s',
                            ($cPath) ? $cPath . DIRECTORY_SEPARATOR : '',
                            $error['complete']
                        );
                        $error['path']     = $sourceDir;
                        $exists            = true;
                        $fileList[$tmp['complete']] = $error;
                    }
                }
            }
            if (!$exists) {
                $fileList[$tmp['complete']] = $tmp;
            }
        }
        ksort($fileList);
        return $fileList;
    }

    /**
     * Get the related error elements for given $fileName.
     *
     * @param string $fileName  The $fileName to search for, could be a mixe of path
     *                          with filename as well (e.g.
     *                          relative/path/filename.php)
     *
     * @return SimpleXMLElement
     */
    public function getIssuesByFile($fileName)
    {
        $list = array();
        foreach ($this->plugins as $plugin) {
            $list = $this->buildIssueTree($plugin->parseXMLError($fileName), $list);
        }
        return $list;
    }

    protected function buildIssueTree($newIssues, $oldIssues)
    {
        foreach ($newIssues as $issue) {
            if (!isset($oldIssues[$issue->lineStart])) {
                $oldIssues[$issue->lineStart] = array();
            }
            $oldIssues[$issue->lineStart][] = $issue;
        }
        return $oldIssues;
    }

    /**
     * Get all the filenames with errors.
     *
     * @return array
     */
    public function getFilesWithIssues()
    {
        $files = array();
        foreach ($this->plugins as $plugin) {
            $files = array_merge($files, $plugin->getFilesWithErrors());
        }
        return array_unique($files);
    }
}
