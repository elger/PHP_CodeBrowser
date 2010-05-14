<?php
/**
 * Copy paste detection
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
 * @category   PHP_CodeBrowser
 * @package    PHP_CodeBrowser
 * @subpackage Plugins
 * @author     Elger Thiele <elger.thiele@mayflower.de>
 * @author     Michel Hartmann <michel.hartmann@mayflower.de>
 * @copyright  2007-2010 Mayflower GmbH
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.phpunit.de/
 * @since      File available since 1.0
 */

/**
 * CbErrorCPD
 *
 * @category   PHP_CodeBrowser
 * @package    PHP_CodeBrowser
 * @subpackage Plugins
 * @author     Elger Thiele <elger.thiele@mayflower.de>
 * @author     Christopher Weckerle <christopher.weckerle@mayflower.de>
 * @author     Michel Hartmann <michel.hartmann@mayflower.de>
 * @copyright  2007-2010 Mayflower GmbH
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since 1.0
 */
class CbErrorCPD extends CbPluginsAbstract
{
    /**
     * Name of this plugin.
     * Used to read issues from XML.
     * @var String
     */
    public $pluginName = 'pmd-cpd';

    /**
     * Name of the attribute that holds the number of the first line
     * of the issue.
     * @var String
     */
    protected $lineStartAttr = 'line';

    /**
     * Name of the attribute that holds the number of the last line
     * of the issue.
     * @var String
     */
    protected $lineEndAttr = 'line';

    /**
     * Get all issues of a file for this plugin as a DOMNodeList.
     * Overloaded to use different structure.
     *
     * @param String $file  Name of the file to search issues for.
     * @return DOMNodeList  All DOMNodes defining issues on a file.
     */
    protected function getIssueNodes($file)
    {
        return $this->issueXml->query(
            '/cruisecontrol/'.$this->pluginName.'/*/file[@path="'.$file.'"]'
        );
    }

    /**
     * Mapper method for this plugin.
     * Overloaded to use special structure.
     *
     * @param SingleXMLElement $element The XML plugin node with its errors
     * @return array
     */
    public function mapIssues(DOMNode $element, $filename)
    {
        $parentNode = $element->parentNode;
        $files = $this->issueXml->query('file[@path="'.$filename.'"]', $parentNode);
        $lineCount = (int)$parentNode->getAttribute('lines');

        $result = array();
        foreach ($files as $file) {
            $result[] = new CbIssue(
                $file->getAttribute('path'),
                $this->getLineStart($file),
                $this->getLineStart($file) + $lineCount,
                $this->getSource($file),
                $this->getDescription($parentNode->childNodes, $file),
                'notice'
            );
        }
        return $result;
    }

    /**
     * Get an array with all files that have issues.
     *
     * @return array
     */
    public function getFilesWithIssues()
    {
        $filenames = array();

        foreach ($this->issueXml->query('/cruisecontrol/'.$this->pluginName.'/*/file[@path]') as $node) {
            $filenames[] = $node->getAttribute('path');
        }

        return array_unique($filenames);
    }

    /**
     * Get the description for an issue.
     * Overloaded to support special structure.
     *
     * @param DOMNodeList $allNodes
     * @param DOMNode $currentNode
     * @return String
     */
    protected function getDescription(DOMNodeList $allNodes, DOMNode $currentNode)
    {
        $source = array();
        foreach ($allNodes as $node) {
            if ($node instanceof DOMElement && !$node->isSameNode($currentNode)) {
                $source[] = sprintf(
                    '%s (%d)',
                    $node->getAttribute('path'),
                    $node->getAttribute('line')
                );
            }
        }
        return htmlentities("Copy paste from:\n".implode("\n", $source));
    }

    /**
     * Default string to use as source for issue.
     * @var String
     */
    protected $source = 'Duplication';

}