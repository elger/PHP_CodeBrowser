<?php
/**
 * Source handler
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
 * @author    Michel Hartmann <michel.hartmann@mayflower.de>
 * @author    Simon Kohlmeyer <simon.kohlmeyer@mayflower.de>
 * @copyright 2007-2010 Mayflower GmbH
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.phpunit.de/
 * @since     File available since  0.2.0
 */

/**
 * CbSourceHandler
 *
 * This class manages lists of source files and their issues.
 * For providing these lists the prior generated CbIssueXml is parsed.
 *
 * @category  PHP_CodeBrowser
 * @package   PHP_CodeBrowser
 * @author    Elger Thiele <elger.thiele@mayflower.de>
 * @author    Christopher Weckerle <christopher.weckerle@mayflower.de>
 * @author    Michel Hartmann <michel.hartmann@mayflower.de>
 * @author    Simon Kohlmeyer <simon.kohlmeyer@mayflower.de>
 * @copyright 2007-2010 Mayflower GmbH
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.phpunit.de/
 * @since     Class available since  0.2.0
 */
class CbSourceHandler
{

    /**
     * Plugins to use for parsing the xml.
     * @var array
     */
    protected $_plugins = array();

    /**
     * Files to be included in the report
     *
     * @var Array of CbFile
     */
    protected $_files = array();

    /**
     * Default constructor
     *
     * @param Array $plugins The plugins to get issues from.
     */
    public function __construct (array $plugins = array())
    {
        $this->_plugins    = $plugins;
        array_walk($plugins, array($this, 'addPlugin'));
    }

    /**
     * Add a new plugin to the handler.
     *
     * @param CbPluginsAbstract $plugin The plugin to add.
     */
    public function addPlugin(CbPluginsAbstract $plugin) {
        foreach ($plugin->getFilelist() as $file) {
            if (array_key_exists($file->name(), $this->_files)) {
                $this->_files[$file->name()]->mergeWith($file);
            } else {
                $this->_files[$file->name()] = $file;
            }
        }
    }

    /**
     * Add source files to the list.
     *
     * @param Array of SplFileInfo $files The files to add
     */
    public function addSourceFiles($files)
    {
        foreach ($files as $f) {
            if (!$f->isFile()) {
                throw new Exception(
                    "{$f->getPathname()} is no regular file"
                );
            }
            $name = $f->getRealPath();

            if (!array_key_exists($name, $this->_files)) {
                $this->_files[$name] = new CbFile($name);
            }
        }
    }

    /**
     * Retrieves the parent directory all files have in common.
     *
     * @return String
     */
    public function getCommonPathPrefix()
    {
        return CbIOHelper::getCommonPathPrefix(array_keys($this->_files));
    }

    /**
     * Returns a sorted array of the files that should be in the report.
     *
     * @return Array of CbFile
     */
    public function getFiles()
    {
        CbFile::sort($this->_files);
        return $this->_files;
    }

    /**
     * Get a unique list of all filenames with issues.
     *
     * @return Array
     */
    public function getFilesWithIssues()
    {
        return array_keys($this->_files);
    }

    /**
     * Remove all files that match the given PCRE.
     *
     * @param String $expr The PCRE specifying which files to remove.
     * @return void.
     */
    public function excludeMatching($expr)
    {
        foreach (array_keys($this->_files) as $filename) {
            if (preg_match($expr, $filename)) {
                unset($this->_files[$filename]);
            }
        }
    }
}
