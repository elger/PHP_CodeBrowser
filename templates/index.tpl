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
        <div id="tree">
<?php
require_once (dirname(__FILE__) . '/Helpers/FileSidebar.php');
echoFileTree($files, '');
?>
        </div>
    </body>
</html>
