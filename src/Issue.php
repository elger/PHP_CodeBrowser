<?php
class Issue
{
    /**
     * Source file name
     * 
     * @var string
     */
    public $fileName;
    
    /**
     * Starting Line of the Issue
     * @var string
     */
    public $lineStart;
    
    /**
     * Ending Line of the Issue
     * @var string
     */
    public $lineEnd;
    
    /**
     * Name of the Plugin that found the Issue
     * @var string
     */
    public $foundBy;
    
    /**
     * Issue Description text
     * @var string
     */
    public $description;

    /**
     * Severity of the issue
     * @var string
     */
    public $severity;
    
    /**
     * 
     * @param string $fileName
     * @param string $lineStart
     * @param string $lineEnd
     * @param string $foundBy
     * @param string $description
     * @param string $severity
     */
    public function __construct($fileName, $lineStart, $lineEnd, $foundBy, $description, $severity)
    {
        
        $this->fileName    = $fileName;
        $this->lineStart   = $lineStart;
        $this->lineEnd     = $lineEnd;
        $this->foundBy     = $foundBy;
        $this->description = $description;
        $this->severity    = $severity;
        
    }
    
}