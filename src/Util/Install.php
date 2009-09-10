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

require_once dirname(__FILE__) . '/../FDHandler.php';

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
    protected $_cbFDHandler;
    
    /**
     * Installation path
     * 
     * @var string
     */
    protected $_cbInstallPath = '/usr/share/php5/PHP_CodeBrowser';
    
    /**
     * Constructor
     * 
     * @param string $installPath The installation path
     */
    public function __construct()
    {
        $this->setFDHandler(new cbFDHandler());
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
        $this->_cbInstallPath = realpath($cbInstallPath . '/PHP_CodeBrowser');
    }
    
    /**
     * Start the installation routine
     * 
     * @param string $system System dependent installation
     * 
     * @return void
     */
    public function install($system = null)
    {
        $this->_cleanOldInstallation();
        $this->_cbFDHandler->copyDirectory(dirname(__FILE__) . '/../../src', $this->_cbInstallPath . '/src', array('.svn'));
        $this->_cbFDHandler->copyDirectory(dirname(__FILE__) . '/../../templates', $this->_cbInstallPath . '/templates', array('.svn'));
        $this->_cbFDHandler->copyFile(dirname(__FILE__) . '/../../CodeBrowser.php', $this->_cbInstallPath);
        
        switch ($system) {
            case 'win':
                $this->_installWin();
                break;
            default:
                $this->_installLinux();
        }
    }
    
    /**
     * Cleaning up possible old installations
     * 
     * @return void
     */
    private function _cleanOldInstallation()
    {
        $this->_cbFDHandler->deleteDirectory($this->_cbInstallPath);
        $this->_cbFDHandler->createDirectory($this->_cbInstallPath);
    }

    /**
     * Method for phpcb installation on a Linux system
     * 
     * @return void
     */
    private function _installLinux()
    {
        $content = $this->_cbFDHandler->loadFile(dirname(__FILE__) . '/../../bin/phpcb');
        $content = str_replace('@install@', $this->_cbInstallPath . '/', $content);
        $this->_cbFDHandler->createFile($this->_cbInstallPath . '/bin/phpcb', $content);
        
        // linux specific commands 
        $this->_cbFDHandler->deleteFile('/usr/bin/phpcb');
        system(sprintf('chmod a+x %s/bin/phpcb', $this->_cbInstallPath));
        system(sprintf('chmod a+x %s/CodeBrowser.php', $this->_cbInstallPath));
        system(sprintf('ln -s %s/bin/phpcb /usr/bin/phpcb', $this->_cbInstallPath));
    }
    
    /**
     * Method for phpcb installation on a Linux system
     * 
     * @return void
     */
    private function _installWin()
    {
        $content = $this->_cbFDHandler->loadFile(dirname(__FILE__) . '/../../bin/phpcb.bat');
        $content = str_replace('@install@', $this->_cbInstallPath . '\\', $content);
        $this->_cbFDHandler->createFile($this->_cbInstallPath . '\bin\phpcb.bat', $content);
    }
   
}