<?php

class cbErrorPMD extends cbPluginError
{
    /**
     * Setter mothod for the plugin name
     *
     * @return void
     */
    public function setPluginName ()
    {
        $this->pluginName = 'pmd';
    }
    
    /**
     * Mapper method for this plugin.
     * 
     * @param SingleXMLElement $element The XML plugin node with its errors
     * 
     * @return array
     */
    public function mapError (SimpleXMLElement $element)
    {
        $errorList     = array();
        $attr          = $element->attributes();
        $error['name'] = $this->getRelativeFilePath($attr['name'], $this->projectSourceDir);
        
        foreach ($element->violation as $child) {
            $attributes           = $child->attributes();
            $error['line']        = (int) $attributes['line'];
            $error['to-line']     = (int) $attributes['to-line'];
            $error['source']      = (string) $attributes['rule'];
            $error['severity']    = 'error';
            $error['description'] = htmlentities((string) $child);
            $errorList[]          = $error;
        }
        return $errorList;
    }
}