<?php

class cbMockPluginError extends cbPluginError 
{
    public function setPluginName()
    {
        $this->pluginName = 'mock-plugin';
    }
    
    public function mapError(SimpleXMLElement $element)
    {
        return unserialize('a:2:{i:0;a:6:{s:4:"name";s:67:"/opt/cruisecontrol/projects/testPagckage/source/src/cbTestClass.php";s:4:"line";i:85;s:7:"to-line";i:196;s:6:"source";s:15:"NPathComplexity";s:8:"severity";s:5:"error";s:11:"description";s:242:"The NPath complexity is 1848. The NPath complexity of a function or method is the number of acyclic execution paths through that method. A threshold of 200 is generally considered the point where measures should be taken to reduce complexity.";}i:1;a:6:{s:4:"name";s:67:"/opt/cruisecontrol/projects/testPagckage/source/src/cbTestClass.php";s:4:"line";i:77;s:7:"to-line";i:88;s:6:"source";s:12:"CodeCoverage";s:8:"severity";s:5:"error";s:11:"description";s:50:"The code coverage is 0.00 which is considered low.";}}');
    }
}