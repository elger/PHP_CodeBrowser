<?php

class cbAutoloader
{
    /**
     * Array of classnames and path
     * 
     * @var array
     */
    private $classes;
    
    /**
     * Constructor
     * 
     * Parses this project root directory for all files and its classnames
     */
    public function __construct ()
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(dirname(__FILE__) . '/../../'));
        
        foreach ($files as $file) {
            $this->classes['cb' . substr($file->getFilename(), 0, -4)] = realpath($file->getPath() . '/' . $file->getFilename());
        }
    }
    
    /**
     * Autoloader function
     * Includes the file matching by the given classname
     * 
     * @param string $classname The classname that file should be included
     */
    public function autoload ($className)
    {
        include $this->classes[$className];
    }
}