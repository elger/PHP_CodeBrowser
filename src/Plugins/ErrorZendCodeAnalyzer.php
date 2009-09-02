<?php

class cbErrorZendCodeAnalyzer extends cbPluginError
{
    /**
     * Setter mothod for the plugin name
     *
     * @return void
     */
    public function setPluginName ()
    {
        $this->pluginName = 'zendcodeanalyzer';
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
        $errorList = array();
        $attributes = $element->attributes();
        $error['name'] = $this->getRelativeFilePath($attributes['name'], $this->projectSourceDir);
        foreach ($element->error as $child) {
            $attributes = $child->attributes();
            $error['line'] = $attributes['line'];
            $error['to-line'] = $attributes['line'];
            $error['source'] = 'ZendCodeAnalyzer';
            $error['severity'] = $attributes['severity'];
            $error['description'] = htmlentities($attributes['message']);
            $errorList[] = $error;
        }
        return $errorList;
    }
}