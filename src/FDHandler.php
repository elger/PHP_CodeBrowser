<?php


class cbFDHandler
{
    
    /**
     * Creates a file with given name and content.
     * If directories to file do not exists they will be created.
     *
     * @param string $fileName    The filename
     * @param string $fileContent The content of the file
     * 
     * @return void
     */
    public function createFile ($fileName, $fileContent)
    {
        $realName = basename($fileName);
        $path     = substr($fileName, 0, - 1 * (strlen($realName)));
        
        if (!empty($path)) $this->createDirectory($path);
        file_put_contents($path . $realName, $fileContent);
    }
    
    /**
     * Delete a file. The filename could inherit a absolute or relative 
     * path-to-file, 
     * e.g. foo/bar/myfile.php
     *
     * @param string $fileName The (path-to) filename
     * 
     * @return void 
     */
    public function deleteFile ($fileName)
    {
        if (file_exists($fileName)) unlink($fileName);
    }
    
    /**
     * Copy a file from a source to target dir. The source could inherit an 
     * absolute or relative path-to-file.
     *
     * @param string $fileSource
     * @param string $sourceFolder
     * 
     * @return return void
     * @throws Exception 
     */
    public function copyFile ($fileSource, $sourceFolder)
    {
        if (!file_exists($fileSource)) throw new Exception('File ' . $fileSource . ' does not exists!');
        
        $fileName = basename($fileSource);
        $this->createFile($sourceFolder . '/' . $fileName, self::loadFile($fileSource));
    }
    
    /**
     * Return the content of a given file.
     *
     * @param string $fileName The file the content should be read in
     * 
     * @return string
     * @throws Exception
     */
    public function loadFile ($fileName)
    {
        if (!file_exists($fileName)) throw new Exception('File ' . $fileName . ' does not exist!');
        return trim(file_get_contents($fileName));
    }
    
	/**
     * Create a directory and its inherit path to directory if not present,
     * e.g. path/that/does/not/exist/myfolder/ 
     *
     * @param string $target The target folder to create
     * 
     * @return void
     */
    public function createDirectory ($target)
    {
        if ('\/' == substr($target, - 1, 1)) $target = substr($target, - 1, 1);
        
        $dirs = explode('/', $target);
        $path = '';
        foreach ($dirs as $folder) {
            if (! is_dir($path = $path . $folder . '/')) {
                mkdir($path);
            }
        }
    }
    
    /**
     * Delete a directory within all its items.
     * Note that the given directory $source will be deleted as well.
     *
     * @param string $source The directory to delete.
     * 
     * @return void
     * @throws Exception
     */
    public function deleteDirectory ($source)
    {
        $iterator = new DirectoryIterator($source);
        while ($iterator->valid()) {
            
            $src = $source . '/' . $iterator->current();
            // delete file
            if ($iterator->isFile()) $this->deleteFile(realpath($src));
            
            // delete folder recursive
            if (! $iterator->isDot() && $iterator->isDir()) $this->deleteDirectory($src);
            
            $iterator->next();
        }
        unset($iterator);

        // delete the source root folder as well
        if (! rmdir($source)) throw new Exception('Could not delete directory ' . $source); 
    }
    
    /**
     * Copy a directory within all its items.
     *
     * @param string $source The source directoryö
     * @param string $target The target to create
     * 
     * @return void
     */
    public function copyDirectory ($source, $target)
    {
        // first check for target itself
        $this->createDirectory($target);
        $iterator = new DirectoryIterator($source);
        while ($iterator->valid()) {
            
            $item = $iterator->current();
            
            // create new file
            if ($iterator->isFile()) $this->copyFile($source . '/' . $item, $target);

            // create folder recursive
            if (! $iterator->isDot() && $iterator->isDir()) {
                $this->copyDirectory($source . '/' . $item, $target . '/' . $item);
            }
            $iterator->next();
        }
    }
}