<?php

class cbErrorCPD extends cbPluginError
{
    /**
     * Setter mothod for the plugin name
     *
     * @return void
     */
    public function setPluginName ()
    {
        $this->pluginName = 'pmd-cpd';
    }
    
    /**
     * Mapper method for this plugin.
     * 
     * @param SingleXMLElement $element The XML plugin node with its errors
     * 
     * @return array
     */
    public function mapError (SimpleXMLElement $xmlElement)
    {
        $attributesF           = $xmlElement->file[0]->attributes();
        $attributesS           = $xmlElement->file[1]->attributes();
        $errorF['line']        = (int) $attributesF['line'];
        $errorF['to-line']     = (int) $attributesF['line'] + (int)$attributesF['lines'];
        $errorF['source']      = 'Duplication';
        $errorF['severity']    = 'notice';
        $errorF['description'] = htmlentities('... ' . substr($attributesS['path'], strlen($attributesS['path']) - 30));
        
        $errorS['line']        = (int) $attributesS['line'];
        $errorS['to-line']     = (int) $attributesS['line'] + (int) $attributesS['lines'];
        $errorS['source']      = 'Duplication';
        $errorS['severity']    = 'notice';
        $errorS['description'] = htmlentities('... ' . substr($attributesF['path'], strlen($attributesF['path']) - 30));
        $errorF['name']        = $this->getRelativeFilePath($attributesF['path'], $this->projectSourceDir);
        $errorS['name']        = $this->getRelativeFilePath($attributesS['path'], $this->projectSourceDir);
        
        return array($errorF , $errorS);
    }
}