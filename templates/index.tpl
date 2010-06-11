<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <script type="text/javascript" src="js/jquery-1.4.2.js"></script>
        <script type="text/javascript" src="js/jquery.jstree/jquery.jstree.js"></script>
        <script type="text/javascript" src="js/tree.js"></script>
        <link rel="stylesheet" type="text/css" href="css/tree.css"/>
        <title>PHP CodeBrowser</title>
    </head>
    <body>
        <div id="tree">
        <ul>
<?php
function printDir(Array $dir, $prefix) {
    if ($prefix !== '') {
        $prefix .= '/';
    }
    $subdirs = array();
    $files = array();
    foreach ($dir as $key => $val) {
        if (is_array($val)) {
            $subdirs[$key] = $val;
        } else {
            $files[] = $val;
        }
    }

    ksort($subdirs);
    sort($files);

    foreach ($subdirs as $key => $val) {
        echo "<li><a href='$prefix$key'>$key</a><ul>";
        printDir($val, $prefix . '/' . $key);
        echo "</ul></li>";
    }

    foreach ($files as $f) {
        echo "<li class='php'><a href='$prefix$f.html'>$f</a></li>";
    }
}

printDir($files, '');
?>
        </ul>
        </div>
    </body>
</html>
