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
