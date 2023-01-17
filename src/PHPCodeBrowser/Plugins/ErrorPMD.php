<?php

/**
 * PMD
 *
 * PHP Version 5.2
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
 * @category PHP_CodeBrowser
 *
 * @author Elger Thiele <elger.thiele@mayflower.de>
 * @author Michel Hartmann <michel.hartmann@mayflower.de>
 *
 * @copyright 2007-2010 Mayflower GmbH
 *
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @link http://www.phpunit.de/
 *
 * @since File available since  0.1.0
 */

namespace PHPCodeBrowser\Plugins;

use DOMElement;
use PHPCodeBrowser\AbstractPlugin;

/**
 * ErrorPMD
 *
 * @category PHP_CodeBrowser
 *
 * @author Elger Thiele <elger.thiele@mayflower.de>
 * @author Christopher Weckerle <christopher.weckerle@mayflower.de>
 * @author Michel Hartmann <michel.hartmann@mayflower.de>
 *
 * @copyright 2007-2010 Mayflower GmbH
 *
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @version Release: @package_version@
 *
 * @link http://www.phpunit.de/
 *
 * @since Class available since  0.1.0
 */
class ErrorPMD extends AbstractPlugin
{
    /**
     * Name of this plugin.
     * Used to read issues from XML.
     *
     * @var string
     */
    protected $pluginName = 'pmd';

    /**
     * Name of the attribute that holds the number of the first line
     * of the issue.
     *
     * @var string
     */
    protected $lineStartAttr = 'beginline';

    /**
     * Name of the attribute that holds the number of the last line
     * of the issue.
     *
     * @var string
     */
    protected $lineEndAttr = 'endline';

    /**
     * Default string to use as source for issue.
     *
     * @var string
     */
    protected $source = 'PMD';

    /**
     * Get the severity of an issue.
     * Always return 'error'.
     *
     * @param DOMElement $element
     *
     * @phpcs:disable SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     *
     * @return string
     */
    protected function getSeverity(DOMElement $element): string
    {
        return 'error';
    }

    /**
     * Get the description of an issue.
     * Use the textContent of the element.
     *
     * @param DOMElement $element
     *
     * @return string
     */
    protected function getDescription(DOMElement $element): string
    {
        return \str_replace(
            '&#10;',
            '',
            \htmlentities($element->textContent)
        );
    }
}
