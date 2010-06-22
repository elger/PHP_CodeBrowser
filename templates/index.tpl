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
            <a href="#">CodeBrowser</a>
        </div>
<?php
require_once (dirname(__FILE__) . '/Helpers/FileSidebar.php');
echoFileTree($dirTree, '');
?>
        </div><div id="treeToggle"><img src="img/treeToggle.gif"></div></div>
        <div id="fileList" style="display: inline-block; margin:15px;">
            <table border="0" cellspacing="2" cellpadding="3">
                <tr class="head">
                    <td><strong>File</strong></td>
                    <td width="50px" align="center"><strong>Errors</strong></td>
                    <td width="50px" align="center"><strong>Notices</strong></td>
                </tr>
<?php
$oddrow = true;
$preLen = strlen(CbIOHelper::getCommonPathPrefix(array_keys($fileList))) + 1;
foreach ($fileList as $filename => $f) {
    $tag = $oddrow ? 'oddrow' : 'file';
    $oddrow = !$oddrow;
    $shortName = substr($filename, $preLen);
    $errors = $f->getErrorCount();
    $notices = $f->getWarningCount();

    echo "<tr class='$tag'>";
    echo "<td><a href='$shortName.html'>$shortName</a></td>";
    echo "<td align='center'><span class='errors'>$errors</span></td>";
    echo "<td align='center'><span class='notices'>$notices</span></td>";
    echo "</tr>";
}
?>
        </table>
        </div>
    </body>
</html>
