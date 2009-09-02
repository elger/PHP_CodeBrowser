<?php
class cbJSGenerator
{
    private $cbFDHandler;
    
    public function __construct(cbFDHandler $cbFDHandler) 
    {
        $this->cbFDHandler = $cbFDHandler;    
    }
    
    /**
     * Generate javascript source tree for HTML files.
     *
     * @param array $errors The PHP_CodeBrowser error list
     * 
     * @return string
     */
    public function getJSTree ($errors)
    {
        ob_start();
        echo "<script type=\"text/javascript\">";
        echo "a = new dTree('a');";
        echo "a.config.folderLinks=false;";
        echo "a.config.useSelection=false;";
        echo "a.config.useCookies=false;";
        echo "a.add(0,-1,'Code Browser','./flatView.html','','reviewView');";
        $this->echoJSTreeNodes($this->getFoldersFilesTree($errors), 0, $errors);
        echo "document.write(a);";
        echo "</script>";
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
    
    /**
     * Generate javascript highlighted source for HTML files. 
     *
     * @param string $fileName The filename where source should be highlighted
     * @param arry   $errors   The error list for this files as highlight base
     * 
     * @return string
     */
    public function getHighlightedSource ($fileName, $errors, $projectSource)
    {
        ob_start();
        $code = $this->cbFDHandler->loadFile($projectSource . '/' . $fileName);
        ini_set('highlight.comment', 'comment');
        ini_set('highlight.default', 'default');
        ini_set('highlight.keyword', 'keyword');
        ini_set('highlight.string', 'string');
        ini_set('highlight.html', 'html');
        $code = highlight_string($code, true);
        // clean-up
        $code = str_replace(array('&nbsp;' , '&amp;' , '<br />' , '<span style="color: '), array(' ' , '&#38;' , "\n" , '<span class="'), substr($code, 33, - 15));
        $code = trim($code);
        // normalize newlines
        $code = str_replace("\r", "\n", $code);
        $code = preg_replace("!\n\n\n+!", "\n\n", $code);
        $lines = explode("\n", $code);
        /* Previous Style */
        $previous = false;
        // Output Listing 
        echo " <ol class=\"code\">\n";
        foreach ($lines as $key => $line) {
            if (substr($line, 0, 7) == '</span>') {
                $previous = false;
                $line = substr($line, 7);
            }
            if (empty($line))
                $line = '&#160;';
            if ($previous)
                $line = "<span class=\"$previous\">" . $line;
                // Set Previous Style
            if (strpos($line, '<span') !== false) {
                switch (substr($line, strrpos($line, '<span') + 13, 1)) {
                    case 'c':
                        $previous = 'comment';
                        break;
                    case 'd':
                        $previous = 'default';
                        break;
                    case 'k':
                        $previous = 'keyword';
                        break;
                    case 's':
                        $previous = 'string';
                        break;
                }
            }
            // Unset Previous Style Unless Span Continues 
            if (substr($line, - 7) == '</span>')
                $previous = false;
            elseif ($previous)
                $line .= '</span>';
            $num = $key + 1;
            // Check if Errors in line 
            $classname = 'white';
            $classnameEven = 'even';
            $prefix = '';
            $suffix = '';
            
            // 76-82 76-92 76-76
            foreach ($errors as $error) {
                if (($error['line'] <= $num) && ($error['to-line'] >= $num)) {
                    $classnameEven = 'transparent';
                    $classname = 'transparent';
                    if ($error['line'] == $num) {
                        $prefix = sprintf('<li id="line-%s-%s" class="%s" ><ul>', $error['line'], $error['to-line'], (($prefix != '') ? 'moreErrors' : $error['source']));
                    }
                    if ($error['to-line'] == $num) {
                         $suffix = "</ul></li>";
                    }
                }
            }
            echo sprintf('%s<li id="line-%d" class="%s"> <a name="line-%d"></a> <code>%s</code></li>%s' . "\n", $prefix, $num, (($key % 2) ? $classnameEven : $classname), $num, $line, $suffix);
        }
        echo "</ol>\n";
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
    
    /**
     * Echos out the Tree recursive. 
     * This function should be only called in a buffered output. 
     *
     * @param array   $tree     Tree array of folders/files
     * @param integer $parentId Id of the parent object
     * 
     * @return void
     */
    private function echoJSTreeNodes ($tree, $parentId, $errors)
    {
        static $id = 0;
        foreach ($tree as $key => $value) {
            $id ++;
            if (is_array($value)) {
                echo sprintf("a.add(%d,%d,'%s', '');", $id, $parentId, $key);
                $this->echoJSTreeNodes($value, $id, $errors);
            } else {
                $key = sprintf('%s ( <span class="errors">%sE</span> | <span class="notices">%sN</span> )', $key, $errors[$value]['count_errors'], $errors[$value]['count_notices']);
                echo sprintf("a.add(%d,%d,'%s','./%s.html','','reviewView');", $id, $parentId, $key, $errors[$value]['complete']);
            }
        }
    }
    
    /**
     * Get folders and files in an tree array
     * 
     * @param array $files Array of files with errors
     *
     * @return array
     */
    private function getFoldersFilesTree ($files)
    {
        $result = array();
        if (is_array($files)) {
            krsort($files);
            foreach ($files as $fileId => $file) {
                $folders = explode('/', $file['complete']);
                
                $folders[count($folders)] = $fileId;
                krsort($folders);
                $tree = null;
                
                foreach ($folders as $folder) {
                    if (is_numeric($folder)) $tree = $folder;
                    elseif ($folder != '')   $tree = array($folder => $tree);
                }
                $result = array_merge_recursive($tree, $result);
            }
        }
        return $result;
    }
}