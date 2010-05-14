<?php
/**
 * Issue handler
 *
 * PHP Version 5.2.6
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
 * @copyright 2007-2010 Mayflower GmbH
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
 * For providing these lists the prior generated CbIssueXml is parsed.
 *
 * @category  PHP_CodeBrowser
 * @package   PHP_CodeBrowser
 * @author    Elger Thiele <elger.thiele@mayflower.de>
 * @author    Christopher Weckerle <christopher.weckerle@mayflower.de>
 * @author    Michel Hartmann <michel.hartmann@mayflower.de>
 * @copyright 2007-2010 Mayflower GmbH
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
     * Plugins to use for parsing the xml.
     * @var array
     */
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
     * Get the related error elements for given $fileName.
     *
     * @param string $fileName  The $fileName to search for, could be a mixe of
     *                          path with filename as well (e.g.
     *                          relative/path/filename.php)
     *
     * @return SimpleXMLElement
     */
    public function getIssuesByFile($fileName)
    {
        $list = array();
        foreach ($this->plugins as $plugin) {
            $list = $this->buildIssueTree($plugin->getIssuesByFile($fileName), $list);
        }
        return $list;
    }

    /**
     * Build a tree of issues to be able to get issues by line number.
     * @param array $newIssues  Issues to add
     * @param array $oldIssues  Exisiting issues as tree.
     * @return array
     */
    protected function buildIssueTree(array $newIssues, array $oldIssues)
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
     * Get all the filenames with issues.
     *
     * @return array
     */
    public function getFilesWithIssues()
    {
        $files = array();
        foreach ($this->plugins as $plugin) {
            $files = array_merge($files, $plugin->getFilesWithIssues());
        }
        return array_unique($files);
    }

    /**
     * Filters the string from the beginning the two input strings have in common.
     *
     * Example:
     * /path/to/source/folder/myFile.php
     * /path/to/source/other/folder/otherFile.php
     * will return
     * /path/to/source
     *
     * @param string $path1  String for comparing
     * @param string $path2  String for comparing
     * @param int    $length The length for comparing the strings
     *
     * @return string
     */
    private function _getCommonErrorPath($path1, $path2, $length = 0)
    {
        $length = (strlen($path1) < strlen($path2))
        ?
        strlen($path1)
        :
        strlen($path2);

        if (!$length && 0 === ($length = strlen($path2))) {
            return $path1;
        }

        switch (strncmp($path1, $path2, $length)) {
        case 0 :
            $path = substr($path1, 0, $length);
            break;
        default :
            $path = $this->_getCommonErrorPath(
                substr($path1, 0, $length-1), substr($path2, 0, $length-1)
            );
            break;
        }
        return (DIRECTORY_SEPARATOR == substr($path, -1))
            ?
            substr($path, 0, -1)
            :
            $path;
    }
}
