<?php

require_once '../FDHandler.php';

class cbInstall 
{

    private $cbFDHandler;
    private $cbInstallPath = '/usr/share/php5/PHP_CodeBrowser';
    
    public function __construct(cbFDHandler $cbFDHandler)
    {
        $this->setFDHandler($cbFDHandler);
    }
    
    public function setFDHandler($cbFDHandler)
    {
        $this->cbFDHandler = $cbFDHandler;
    }
    
    public function setInstallPath($cbInstallPath)
    {
        $this->cbInstallPath = $cbInstallPath;
    }

    public function install()
    {
        $content = $this->cbFDHandler->loadFile('../../bin/phpcb');
        $str_replace('@install@', $this->cbInstallPath . '/');
        $this->cbFDHandler->createFile($this->cbInstallPath . '/bin/phpcb', $content);
        
        // check if allowed        
        // system(sprintf('chmod a+x %/bin/phpcb', $this->cbInstallPath));
        // system(sprintf('ln -s %/bin/phpcb /usr/bin/phpcb', $this->cbInstallPath));
        
        $this->cbFDHandler->copyDirectory('../../src', $this->cbInstallPath . '/src');
        $this->cbFDHandler->copyDirectory('../../templates', $this->cbInstallPath . '/templates');
        $this->cbFDHandler->copyFile('../../CodeBrowser.php', $this->cbInstallPath);
    }
    // copy / create files
   
}