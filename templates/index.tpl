<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="css/tree.css"/>
        <link rel="stylesheet" type="text/css" href="css/cruisecontrol.css"/>

        <script type="text/javascript" src="js/jquery-1.4.2.js"></script>
        <script type="text/javascript" src="js/jquery.jstree/jquery.jstree.js"></script>
        <script type="text/javascript" src="js/tree.js"></script>
        <title>PHP CodeBrowser</title>
    </head>
    <body class="codebrowser">
        <div id="treeContainer"><div id="tree">
        <div id="treeHeader">
            <img alt="" src="img/base.gif" id="ia0">CodeBrowser
        </div>
<?php
require_once (dirname(__FILE__) . '/Helpers/FileSidebar.php');
echoFileTree($files, '');
?>
        </div><div id="treeToggle"><img src="img/treeToggle.gif"></div></div>
        <div id="fileList" style="display: inline-block; margin:15px;">
            <ul>
<?php
function printFileList(Array $dir, $prefix) {
    ksort($dir);
    foreach ($dir as $name => $subdir) {
        if (!is_array($subdir)) {
            // No directory
            continue;
        }
        printFileList($subdir, $prefix . $name . '/');
    }
    sort($dir);
    foreach ($dir as $file) {
        if(is_array($file)) {
            // No file
            continue;
        }
        $f = $prefix . $file . '.html';
        echo "<li><a href='$f'>$f</a></li>";
    }
}
printFileList($files, '');
?>
        </ul>
        </div>
    </body>
</html>
