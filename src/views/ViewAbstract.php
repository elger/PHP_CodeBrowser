<?php
class CbViewAbstract
{
    
    /**
     * Template directory
     *
     * @var string
     */
    protected $_templateDir;

    /**
     * Output directory
     *
     * @var string
     */
    protected $_outputDir;

    /**
     * Available ressource folders
     *
     * @var array
     */
    protected $_ressourceFolders = array('css' , 'js' , 'img');

    /**
     * File handler object
     *
     * @var cbIOHelper
     */
    protected $_cbIOHelper;
    
    
    
    /**
     * Constructor
     *
     * @param CbIOHelper    $cbIOHelper    File handler object
     */
    public function __construct(CbIOHelper $cbIOHelper)
    {
        $this->_cbIOHelper = $cbIOHelper;
    }
    
    /**
     * Setter method
     *
     * @param string $templateDir Path to template diretory
     *
     * @return void
     */
    public function setTemplateDir ($templateDir)
    {
        $this->_templateDir = $templateDir;
    }

    /**
     * Setter mothod
     * Path where generated view-files should be saved.
     *
     * @param string $outputDir Path to output directory
     *
     * @return void
     */
    public function setOutputDir($outputDir)
    {
        $this->_outputDir = $outputDir;
    }
    
    
    /**
     * Copy needed resources to output directory
     *
     * @param boolean $hasErrors Flag to define which index.html will be generated.
     *
     * @return void
     * @throws Exception
     * @see cbIOHelper::copyFile
     */
    public function copyRessourceFolders($hasErrors = true)
    {
        if (!isset($this->_outputDir)) {
            throw new Exception('Output directory is not set!');
        }

        foreach ($this->_ressourceFolders as $folder) {
            $this->_cbIOHelper->copyDirectory(
                $this->_templateDir . DIRECTORY_SEPARATOR . $folder,
                $this->_outputDir . DIRECTORY_SEPARATOR . $folder
            );
        }

        $template = ($hasErrors) ?  'index.tpl' : 'noErrors.tpl';

        $content = $this->_cbIOHelper->loadFile(
            $this->_templateDir . DIRECTORY_SEPARATOR . $template
        );
        $this->_cbIOHelper->createFile(
            $this->_outputDir . DIRECTORY_SEPARATOR . 'index.html', $content
        );
    }
    
    /**
     * Render different template types
     *
     * @param string $templateName Template file to use for rendering
     * @param array  $data         Given dataset to use for rendering
     *
     * @return string              HTML files as string from output buffer
     */
    protected function _render($templateName, $data)
    {
        $filePath = realpath($this->_templateDir)
            . DIRECTORY_SEPARATOR
            . $templateName
            . '.tpl';

        if (!count($data)) {
            return '';
        }

        extract($data, EXTR_SKIP);
        ob_start();
        include realpath($this->_templateDir)
            . DIRECTORY_SEPARATOR
            . $templateName
            . '.tpl';
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    /**
     * Save rendered file to output directory
     *
     * @param array  $data     Dataset information used for rendering
     * @param string $fileName The filename of analyzed file
     *
     * @return void
     * @see cbIOHelper::createFile
     */
    protected function _generateView($data, $fileName)
    {
        $this->_cbIOHelper->createFile(
            $this->_outputDir
            . DIRECTORY_SEPARATOR
            . $fileName,
            $this->_render('page', $data)
        );
    }
    
    
    
}