<?php
/**
 * Install
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
 * @category   PHP_CodeBrowser
 * @package    PHP_CodeBrowser
 * @author     Elger Thiele <elger.thiele@mayflower.de>
 * @copyright  2007-2009 Mayflower GmbH
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.phpunit.de/
 * @since      File available since 1.0
 */

require_once '../FDHandler.php';

/**
 * cbInstall
 *
 * @category   PHP_CodeBrowser
 * @package    PHP_CodeBrowser
 * @author     Elger Thiele <elger.thiele@mayflower.de>
 * @author     Christopher Weckerle <christopher.weckerle@mayflower.de>
 * @copyright  2007-2009 Mayflower GmbH
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since 1.0
 */
class cbInstall 
{
    /**
     * File handler object
     * 
     * @var cbFDhandler
     */
    private $_cbFDHandler;
    
    /**
     * Installation path
     * 
     * @var string
     */
    private $_cbInstallPath = '/usr/share/php5/PHP_CodeBrowser';
    
    /**
     * Constructor
     * 
     * @param cbFDHandler $cbFDHandler File handler object
     */
    public function __construct(cbFDHandler $cbFDHandler)
    {
        $this->setFDHandler($cbFDHandler);
    }
    
    /**
     * Setter method
     * 
     * @param bFDHandler $cbFDHandler File handler object
     * 
     * @return void
     */
    public function setFDHandler($cbFDHandler)
    {
        $this->_cbFDHandler = $cbFDHandler;
    }
    
    /**
     * Setter method
     * 
     * @param string $cbInstallPath Installation path
     * 
     * @return void
     */
    public function setInstallPath($cbInstallPath)
    {
        $this->_cbInstallPath = $cbInstallPath;
    }

    /**
     * Method for phpcb installation
     * 
     * @return void
     */
    public function install()
    {
        $content = $this->_cbFDHandler->loadFile('../../bin/phpcb');
        $str_replace('@install@', $this->_cbInstallPath . '/');
        $this->_cbFDHandler->createFile($this->_cbInstallPath . '/bin/phpcb', $content);
        
        // check if allowed        
        // system(sprintf('chmod a+x %/bin/phpcb', $this->cbInstallPath));
        // system(sprintf('ln -s %/bin/phpcb /usr/bin/phpcb', $this->cbInstallPath));
        
        $this->_cbFDHandler->copyDirectory('../../src', $this->_cbInstallPath . '/src');
        $this->_cbFDHandler->copyDirectory('../../templates', $this->_cbInstallPath . '/templates');
        $this->_cbFDHandler->copyFile('../../CodeBrowser.php', $this->_cbInstallPath);
    }
    // copy / create files
   
}