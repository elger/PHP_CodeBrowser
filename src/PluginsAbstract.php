<?php
/**
 * Plugin Abstract
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
 * @author    Michel Hartmann <michel.hartmann@mayflower.de>
 * @copyright 2007-2010 Mayflower GmbH
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.phpunit.de/
 * @since     File available since 1.0
 */

/**
 * CbPluginsAbstract
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
abstract class CbPluginsAbstract
{
    /**
     * The name of the plugin.
     * This should be the name that is written to the XML error files by
     * cruisecontrol.
     *
     * @var string
     */
    public $pluginName;

    /**
     * The CbIssueXml object
     *
     * @var CbIssueXml
     */
    protected $issueXml;

    /**
     * Name of the attribute that holds the number of the first line
     * of the issue.
     * @var String
     */
    protected $lineStartAttr;
    /**
     * Name of the attribute that holds the number of the last line
     * of the issue.
     * @var String
     */
    protected $lineEndAttr;
    /**
     * Name of the attribute that holds message of the issue.
     * @var String
     */
    protected $descriptionAttr;
    /**
     * Name of the attribute that holds severity of the issue.
     * @var String
     */
    protected $severityAttr;

    /**
     * Constructor
     *
     * @param CbIssueXml $issueXml     The cc XML document.
     */
    public function __construct(CbIssueXml $issueXml)
    {
        $this->issueXml = $issueXml;
    }

    /**
     * Parse the cc XML file for defined error type, e.g. "pmd" and map this
     * error to the Issue objects format.
     *
     * @param String $filename  Name of the file to parse the errors for.
     * @return array
     */
    public function getIssuesByFile($filename)
    {
        $issues = array();
        foreach ($this->getIssueNodes($filename) as $issueNode) {
            $issues = array_merge($issues, $this->mapIssues($issueNode, $filename));
        }
        return $issues;
    }

    /**
     * Get all DOMNodes that represent issues for a specific file.
     *
     * @param String $filename      Name of the file to get nodes for.
     * @return DOMNodeList
     */
    protected function getIssueNodes($filename)
    {
        return $this->issueXml->query(
            '/cruisecontrol/'.$this->pluginName.'/file[@name="'.$filename.'"]'
        );
    }

    /**
     * Get an array with all files that have issues.
     *
     * @return Array
     */
    public function getFilesWithIssues()
    {
        $filenames = array();

        $issueNodes = $this->issueXml->query(
            '/cruisecontrol/'.$this->pluginName.'/file[@name]'
        );
        foreach ($issueNodes as $node) {
            $filenames[] = $node->getAttribute('name');
        }

        return array_unique($filenames);
    }

    /**
     * The detailed mapper method for each single plugin, returning an array
     * of Issue objects.
     * This method provides a default behaviour an can be overloaded to
     * implement special behavior for other plugins.
     *
     * @param DomNode $element  The XML plugin node with its errors
     * @param filename          Name of the file to return issues for.
     *
     * @return array            Array of issue objects.
     */
    public function mapIssues(DomNode $element, $filename)
    {
        $errorList = array();
        foreach($element->childNodes as $child) {
            if (!($child instanceof DOMElement)){
                continue;
            }
            $errorList[] = new CbIssue(
                $filename,
                $this->getLineStart($child),
                $this->getLineEnd($child),
                $this->getSource($child),
                $this->getDescription($child),
                $this->getSeverity($child)
            );
        }
        return $errorList;
    }

    /**
     * Default method for retrieving the first line of an issue.
     * @see self::mapIssues
     *
     * @param DOMElement $element
     *
     * @return Integer
     */
    protected function getLineStart(DOMElement $element)
    {
        return (int) $element->getAttribute($this->lineStartAttr);
    }

    /**
     * Default method for retrieving the last line of an issue.
     * @see self::mapIssues
     *
     * @param DOMElement $element
     *
     * @return Integer
     */
    protected function getLineEnd(DOMElement $element)
    {
        return (int) $element->getAttribute($this->lineEndAttr);
    }

    /**
     * Default method for retrieving the source of an issue.
     * @see self::mapIssues
     *
     * @param DOMElement $element
     *
     * @return String
     */
    protected function getSource(DOMElement $element)
    {
        return $this->pluginName;
    }

    /**
     * Default method for retrieving the description of an issue.
     * @see self::mapIssues
     *
     * @param DOMElement $element
     *
     * @return String
     */
    protected function getDescription(DOMElement $element)
    {
        return htmlentities($element->getAttribute($this->descriptionAttr));
    }

    /**
     * Default method for retrieving the severity of an issue.
     * @see self::mapIssues
     *
     * @param DOMElement $element
     *
     * @return String
     */
    protected function getSeverity(DOMElement $element)
    {
        return htmlentities($element->getAttribute($this->severityAttr));
    }
}
