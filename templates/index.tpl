<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="js/jquery.sidebar/css/codebrowser/sidebar.css" />
        <link rel="stylesheet" type="text/css" href="css/cruisecontrol.css" />
        <link rel="stylesheet" type="text/css" href="css/global.css" />
        <link rel="stylesheet" type="text/css" href="css/review.css" />
        <link rel="stylesheet" type="text/css" href="css/tree.css" />

        <script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="js/jquery.jstree/jquery.jstree.min.js"></script>
        <script type="text/javascript" src="js/jquery.sidebar/jquery-ui-1.7.2.custom.min.js"></script>
        <script type="text/javascript" src="js/jquery.sidebar/jquery.sidebar.js"></script>
        <script type="text/javascript" src="js/jquery.cluetip/lib/jquery.hoverIntent.js"></script>
        <script type="text/javascript" src="js/jquery.cluetip/lib/jquery.bgiframe.min.js"></script>
        <script type="text/javascript" src="js/jquery.cluetip/jquery.cluetip.min.js"></script>
        <script type="text/javascript" src="js/jquery.history.js"></script>

        <script type="text/javascript" src="js/review.js"></script>
        <script type="text/javascript" src="js/tree.js"></script>

        <title>PHP CodeBrowser</title>
    </head>
    <body class="codebrowser">
        <div id="treeContainer">
            <div id="tree">
                <div id="treeHeader">
                    <a href="index.html" class='fileLink'>CodeBrowser</a>
                </div>
                <?php echo $treeList; ?>
            </div>
            <div id="treeToggle" style="background-image: url('img/treeToggle-extended.png');"></div>
        </div>
        <div id="contentBox" style="display: inline-block; margin: 15px;">
            <div id="fileList">
                <table border="0" cellspacing="2" cellpadding="3">
                    <tr class="head">
                        <th><strong>File</strong></td>
                        <th width="50px" align="center"><strong>Errors</strong></td>
                        <th width="50px" align="center"><strong>Warnings</strong></td>
                        <th width="50px" align="center"><strong>Copy &amp; Paste</strong></td>
                        <th width="50px" align="center"><strong>Checkstyle</strong></td>
                        <th width="50px" align="center"><strong>PMD</strong></td>
                        <th width="50px" align="center"><strong>Padawan</strong></td>
                    </tr>
<?php
$oddrow = true;
$preLen = strlen(CbIOHelper::getCommonPathPrefix(array_keys($fileList))) + 1;
foreach ($fileList as $filename => $f) {
    $tag = $oddrow ? 'odd' : 'even';
    $oddrow = !$oddrow;
    $shortName = substr($filename, $preLen);
    $errors = $f->getErrorCount();
    $warnings = $f->getWarningCount();

    $cpdCount        = 0;
    $checkstyleCount = 0;
    $pmdCount        = 0;
    $padawanCount    = 0;

    foreach ($f->getIssues() as $issue) {
        switch ($issue->foundBy) {
        case 'CPD': $cpdCount += 1; break;
        case 'Checkstyle': $checkstyleCount += 1; break;
        case 'PMD': $pmdCount += 1; break;
        case 'Padawan': $padawanCount += 1; break;
        }
    }

    echo "<tr class='$tag'>";
    echo "<td><a class='fileLink' href='$shortName.html'>$shortName</a></td>";
    echo "<td align='center'><span class='errorCount'>$errors</span></td>";
    echo "<td align='center'><span class='warningCount'>$warnings</span></td>";
    echo "<td align='center'>$cpdCount</td>";
    echo "<td align='center'>$checkstyleCount</td>";
    echo "<td align='center'>$pmdCount</td>";
    echo "<td align='center'>$padawanCount</td>";
    echo "</tr>";
}
?>
                </table>
            </div
        </div>
    </body>
</html>
