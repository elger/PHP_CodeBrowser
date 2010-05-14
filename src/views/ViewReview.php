<?php
class CbViewReview extends CbViewAbstract
{
    
    /**
     * 
     * @param array  $issueList
     * @param string $filePath
     */
    public function generate(Array $issueList, $filePath)
    {
        if (!is_array($issueList)) {
            throw new Exception('Wrong data format for errorlist!');
        }

        $sourceCode  = $this->_cbIOHelper->loadFile($filePath);
        
        $issues = $this->_formatIssues($issueList);
        
        $data['issues']   = $issues;
        $data['title']    = 'Code Browser - ViewReview View';
        $data['filepath'] = $filePath;
        $data['csspath']  = '';
        $data['source']   = $this->_formatSourceCode($sourceCode, $issues);
        $data['jsCode']   = $this->_grenerateJSCode($issues);
        $depth = substr_count($filePath, DIRECTORY_SEPARATOR);
        
        for ($i = 1; $i <= $depth; $i ++) {
            $data['csspath'] .= '../';
        }
        $dataGenrate['title']   = $data['title'];
        $dataGenrate['csspath'] = $data['csspath'];
        
        $dataGenrate['content'] = $this->_render('review', $data);

        $this->_generateView($dataGenrate, 'proxy.php.html');
        
    }
    
    
    private function _grenerateJSCode($issueList)
    {
        $jsCode = '';

        foreach ($issueList as $num=>$lineIssues) {
            
            $htmlMessages[$num] = '';
            
            foreach ($lineIssues as $issue) {
                
                $htmlMessages[$num] .= addcslashes("<span class=\"title ".$issue->foundBy."\">".
                                      $issue->foundBy . "</span><span class=\"message\">".
                                      (string)$issue->description."</span>", "\"\'\0..\37!@\177..\377");
            }
            
            $jsCode .= "if ($('line_".$num."')) {
                        new Tip('line_".$num."', 
                              '".$htmlMessages[$num]."', 
                              { className: 'tooltip', delay: 0.1 });\n}";
            
        }
        
        return $jsCode;
        
    }
    
    /**
     * 
     * @param unknown_type $sourceCode
     * @param unknown_type $outputIssues
     */
    private function _formatSourceCode($sourceCode, $outputIssues)
    {
        
        $formattedCode = trim(highlight_string($sourceCode, true));

        $sourceDom = new DOMDocument();
        
        $sourceDom->loadHTML(utf8_encode($formattedCode));

        //fetch <code>-><span>->children from php generated html
        $sourceElements = $sourceDom->getElementsByTagname('code')->item(0)->childNodes->item(0)->childNodes;
        
        //create target dom
        $targetDom = new DOMDocument();
        
        $targetNode = $targetDom->createElement('ol');
        $targetNode->setAttribute('class', 'code');
        
        $targetParent = $targetDom->createElement('div');
        $targetParent->setAttribute('class', 'codebrowser');
        $targetParent->appendChild($targetNode);
        
        $targetDom->appendChild($targetParent);
        
        
        
        //create first li element wih its anchor
        $li = $targetDom->createElement('li');
        $anchor = $targetDom->createElement('a');
        $anchor->setAttribute('name', 'line_0');
        $li->appendChild($anchor);
        $li->setAttribute('id', 'line_0');
        
        
        //  set li css class depending on line errors
        if (isset($outputIssues[0])) {
            
            if (1 === count($outputIssues[0])) {
                $li->setAttribute('class', $outputIssues[0][0]->foundBy);
            } else if(1 <= count($outputIssues[0])) {
                $li->setAttribute('class', 'moreErrors');
            }
            
        } else {
            
            $li->setAttribute('class', 'white');
            
        }        
        
        $lineNumber = 1;
        
        //iterate through all <span> elements 
        foreach ($sourceElements as $sourceElement) {
            
            if ($sourceElement instanceof DOMElement) {
                
                
                //echo $sourceElement->childNodes->item(0)->wholeText . '<hr>';
                
                $elementStyle = $sourceElement->getAttribute('style');
                
                foreach ($sourceElement->childNodes as $sourceChildElement) {
                    
                    if ($sourceChildElement instanceof DOMElement && 'br' === $sourceChildElement->tagName) {
                        
                        //write last line
                        $targetNode->appendChild($li);
                        
                        // create new li and new line
                        $li = $targetDom->createElement('li');
                        $li->setAttribute('id', 'line_' . $lineNumber);
                        //create anchor for the new line
                        $anchor = $targetDom->createElement('a');
                        $anchor->setAttribute('name', 'line_' . $lineNumber);
                        
                        $li->appendChild($anchor);
                        
                        // set li css class depending on line errors
                        if (isset($outputIssues[$lineNumber])) {
                            
                            if (1 === count($outputIssues[$lineNumber])) {
                                $li->setAttribute('class', $outputIssues[$lineNumber][0]->foundBy);
                            } else if(1 <= count($outputIssues[$lineNumber])) {
                                $li->setAttribute('class', 'moreErrors');
                            }
                            
                        } else {
                            
                            if (0 === $lineNumber % 2) {
                               $li->setAttribute('class', 'white'); 
                            } else {
                                $li->setAttribute('class', 'even');
                            }
                            
                        }
                        
                        //increment line number
                        $lineNumber++;
                    } else {
                        
                        // apend content to current li element
                        $span = $targetDom->createElement('span');
                        $span->nodeValue = htmlspecialchars($sourceChildElement->wholeText);
                        $span->setAttribute('style', $elementStyle );
                        $li->appendChild($span);
                    }
                    
                }
                
                
            }
            
        }
        return $targetDom->saveHTML();
        
    }
    
    private function _formatIssues($issueList)
    {
        
        $outputIssues = array(); 
        
        foreach ($issueList as $issues) foreach ($issues as $error) {
	        for ($i = $error->lineStart; $i <= $error->lineEnd; $i++) {
            	$outputIssues[$i][] = $error;
            }
        }
        
        return $outputIssues;
    }
    
}