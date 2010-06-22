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
 * @since     File available since 1.0
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
 * @since     Class available since 1.0
 */
class CbSourceHandler
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
     * @param CbIssueXml $cbIssueXml The CbIssueXml object providing all known issues
     */
    public function __construct (CbIssueXml $cbIssueXml, array $plugins)
    {
        $this->cbIssueXml = $cbIssueXml;
        $this->plugins    = $plugins;
    }

    /**
     * Get the related error elements for given $fileName.
     *
     * @param String $fileName  The $fileName to search for, could be a mixe of
     *                          path with filename as well (e.g.
     *                          relative/path/filename.php)
     *
     * @return Array    Array containing all the issues for the given file.
     *                  If none exist, an empty array will be returned.
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
     * Returns how many issues are in each file.
     * The returned Array has the following structure:
     *  array(
     *      'filename' => array(
     *          'notSoSevere' => 3,
     *          'quiteSevere' => 1
     *      ),
     *      ...
     *  )
     *
     *  @return array
     */
    public function getIssueCounts() {
        $total = array();
        foreach ($this->plugins as $plugin) {
            foreach ($plugin->getIssueCounts() as $file => $counts) {
                if (!array_key_exists($file, $total)) {
                    // If we don't already have any issues for this file, just
                    // copy the ones from the plugin.
                    $total[$file] = $counts;
                } else {
                    // If we already have some, we have to merge them.
                    foreach ($counts as $severity => $c) {
                        if (!array_key_exists($severity, $total[$file])) {
                            $total[$file][$severity] = $c;
                        } else {
                            $total[$file][$severity] += $c;
                        }
                    }
                }
            }
        }
        return $total;
    }

    /**
     * Build a tree of issues to be able to get issues by line number.
     *
     * As a file could have several issues in the same line number, the
     * structure is provided as an array of arrays.
     *
     * @param Array $newIssues Issues to add
     * @param Array $oldIssues Exisiting issues as tree.
     *
     * @return Array
     */
    protected function buildIssueTree(Array $newIssues, Array $oldIssues)
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
     * Get a unique list of all filenames with issues.
     *
     * @return Array
     */
    public function getFilesWithIssues()
    {
        $files = array();
        foreach ($this->plugins as $plugin) {
            $files = array_merge($files, $plugin->getFilesWithIssues());
        }
        return array_unique($files);
    }
}
