<?php
/**
 * Plugin Error
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
 * CbPluginError
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
    protected $_cbIssueXml;

    protected $lineStartAttr;
    protected $lineEndAttr;
    protected $descriptionAttr;
    protected $severityAttr;

    /**
     * Constructor
     *
     * @param CbIssueXml $cbIssueXml     CbIssueXml object
     */
    public function __construct(CbIssueXml $cbIssueXml)
    {
        $this->_cbIssueXml = $cbIssueXml;
    }

    /**
     * Parse the cc XML file for defined error type, e.g. "pmd" and map this
     * error to the needed PHP_CodeBrowser format.
     *
     * @param String $file      Name of the file to parse the errors for.
     * @return array
     */
    public function parseXMLError($file)
    {
        if (!isset($this->_cbIssueXml)) {
            throw new Exception('XML file not loaded!');
        }

        $errors = array();
        foreach ($this->getIssues($file) as $fileNode) {
            $errors = array_merge($errors, $this->mapIssues($fileNode, $file));
        }
        return $errors;
    }

    protected function getIssues($file)
    {
        return $this->_cbIssueXml->query('/cruisecontrol/'.$this->pluginName.'/file[@name="'.$file.'"]');
    }

    public function getFilesWithErrors()
    {
        $filenames = array();

        foreach ($this->_cbIssueXml->query('/cruisecontrol/'.$this->pluginName.'/file[@name]') as $node) {
            $filenames[] = $node->getAttribute('name');
        }

        return array_unique($filenames);
    }

    /**
     * The detailed mapper method for each single plugin, returning an errorlist.
     *
     * @param DomNode $element The XML plugin node with its errors
     * @param filename
     *
     * @return array
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

    protected function getLineStart(DOMElement $element)
    {
        return (int) $element->getAttribute($this->lineStartAttr);
    }

    protected function getLineEnd(DOMElement $element)
    {
        return (int) $element->getAttribute($this->lineEndAttr);
    }

    protected function getSource(DOMElement $element)
    {
        return $this->pluginName;
    }

    protected function getDescription(DOMElement $element)
    {
        return htmlentities($element->getAttribute($this->descriptionAttr));
    }

    protected function getSeverity(DOMElement $element)
    {
        return htmlentities($element->getAttribute($this->severityAttr));
    }
}
