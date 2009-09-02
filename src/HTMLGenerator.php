<?php

class cbHTMLGenerator
{
    private $templateDir;
    private $outputDir;
    private $ressourceFolders = array('css' , 'js' , 'img');
    private $cbFDHandler;
    private $cbErrorHandler;
    private $cbJSGenerator;

    public function __construct (cbFDHandler $cbFDHandler, cbErrorHandler $cbErrorHandler, cbJSGenerator $cbJSGenerator)
    {
        $this->cbFDHandler    = $cbFDHandler;
        $this->cbErrorHandler = $cbErrorHandler;
        $this->cbJSGenerator  = $cbJSGenerator;
    }
    
    public function setTemplateDir ($templateDir)
    {
        $this->templateDir = $templateDir;
    }
    
    public function setOutputDir ($outputDir)
    {
        $this->outputDir = $outputDir;
    }
    
    public function generateViewFlat ($errors)
    {
        if (! is_array($errors)) throw new Exception('Wrong data format for errorlist!');
        
        $data['title']   = 'Code Browser - Overview (flat view mode)';
        $data['files']   = $errors;
        $data['csspath'] = '';
        
        $dataGenrate['title']   = $data['title'];
        $dataGenrate['csspath'] = '';
        $dataGenrate['content'] = $this->render('flatView', $data);
        
        $this->generateView($dataGenrate, 'flatView.html');
    }
    
    public function generateViewTree ($errors)
    {
        if (! is_array($errors)) throw new Exception('Wrong data format for errorlist!');
        
        $data['title']   = 'Code Browser - Tree View';
        $data['files']   = $errors;
        $data['csspath'] = '';
        $data['tree']    = $this->cbJSGenerator->getJSTree($errors);
        
        $dataGenrate['title']   = $data['title'];
        $dataGenrate['csspath'] = '';
        $dataGenrate['content'] = $this->render('tree', $data);
        
        $this->generateView($dataGenrate, 'tree.html');
    }
    
    public function generateViewReview ($errors, $cbXMLFile, $projectSource)
    {
        if (! is_array($errors)) throw new Exception('Wrong data format for errorlist!');
        
        $data['title'] = 'Code Browser - Review View';
        foreach ($errors as $file) {
            $data['errors']   = $this->cbErrorHandler->getErrorsByFile($cbXMLFile, $file['complete']);
            $data['source']   = $this->cbJSGenerator->getHighlightedSource($file['complete'], $data['errors'], $projectSource);
            $data['filepath'] = $file['complete'];
            $data['csspath']  = '';
            
            for ($i = 1; $i <= substr_count($file['complete'], '/'); $i ++) {
                $data['csspath'] .= '../';
            }
            
            $dataGenrate['title']   = $data['title'];
            $dataGenrate['csspath'] = $data['csspath'];
            $dataGenrate['content'] = $this->render('reviewView', $data);
            
            $this->generateView($dataGenrate, $file['complete'] . '.html');
        }
    }
    
    public function copyRessourceFolders ()
    {
        if (! isset($this->outputDir)) throw new Exception('Output directory is not set!');
        
        foreach ($this->ressourceFolders as $folder) {
            $this->cbFDHandler->copyDirectory($this->templateDir . '/' . $folder, $this->outputDir . '/' . $folder);
        }
        $this->cbFDHandler->copyFile($this->templateDir . '/treeView.html', $this->outputDir);
    }
    
    private function render ($templateName, $data)
    {
        if (! file_exists(realpath($this->templateDir) . '/' . $templateName . '.tpl')) {
            throw new Exception('Template ' . $templateName . '.tpl could not be found!');
        }
        if (! count($data)) return '';
        
        extract($data, EXTR_SKIP);
        ob_start();
        include realpath($this->templateDir) . '/' . $templateName . '.tpl';
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
    
    private function generateView ($data, $fileName)
    {
        $this->cbFDHandler->createFile($this->outputDir . '/' . $fileName, $this->render('page', $data));
    }
}
