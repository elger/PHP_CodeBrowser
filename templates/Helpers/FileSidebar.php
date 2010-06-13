<?php

function echoFileTree(Array $dir, $prefix) {
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

    echo '<ul>';
    foreach ($subdirs as $key => $val) {
        echo "<li><a href='$prefix$key'>$key</a>";
        echoFileTree($val, $prefix . $key . '/');
        echo '</li>';
    }

    foreach ($files as $f) {
        echo "<li class='php'><a href='$prefix$f.html'>$f</a></li>";
    }
    echo '</ul>';
}
