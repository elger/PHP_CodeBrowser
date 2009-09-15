#!/usr/bin/env php
<?php
/**
 * Installation
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
 * @author     Elger Thiele <elger.thiele@mayflower.de>
 * @copyright  2007-2009 Mayflower GmbH
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id: Install.php 5181 2009-09-03 11:23:46Z elger $
 * @link       http://www.phpunit.de/
 * @since      File available since 1.0
 */

require_once dirname(__FILE__) . '/../src/Util/Install.php';

// initialize cbInstallation
$cbInstall   = new cbInstall();
$scriptError = array();

if (!isset($_SERVER['argv'][1])) {
	$scriptError[] = sprintf("Please specify the system you are trying to install phpcb!\n");
}

if (isset($_SERVER['argv'][2])) {
    if (!is_dir($_SERVER['argv'][2])) {
        $scriptError[] = sprintf("You are trying to install phpcb to an invalid directory: [%s]!\n", $_SERVER['argv'][2]);
    } else {
    	$cbInstall->setInstallPath($_SERVER['argv'][2]);
	}
}

if (!empty($scriptError)) {
	echo implode('', $scriptError);
	printf("Example: php install.php linux [/usr/share/php5]\n");
	exit;
}

$cbInstall->install($_SERVER['argv'][1]);
