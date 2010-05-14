<div class="header">
 <a href="./<?php echo $csspath; ?>flatView.html">&larr; flat View</a> | <?php echo $title; ?>
</div>
<div class="filepath">
<?php echo $filepath; ?>
</div>
<div class="sourcecode">
<?php echo $source; ?>
</div>
<?php if (!empty($issues)) : ?>
<div id="sideBar">
    <a href="javascript:void(0)" id="sideBarTab"> </a>
    <div id="sideBarContents" style="display:none;">
        <div id="sideBarContentsInner">
        <table cellpadding="3">
            <tr class="head">
                <td width="40px" align="center">
                    <strong>start</strong>
                </td>
                <td width="40px" align="center">
                    <strong>end</strong>
                </td>
                <td>
                    <strong>comment</strong>
                </td>
                <td width="120px">
                    <strong>type of error</strong>
                </td>
                <td width="60px">
                    <strong>severity</strong>
                </td>
            </tr>
            <tr class="<?php print $issue->fileName; ?>">
                <td align="center">
                    <a href="#line-<?php print $issue->lineEnd-5;?>" 
                        onclick="new Effect.Highlight('line-<?php print $issue->lineStart."-".$issue->lineEnd; ?>', {duration: 1.5});">
                    <?php print $issue->lineStart; ?></a>
                </td>
                <td align="center">
                    <a href="#line-<?php print $issue->lineEnd-5;?>"
                        onclick="new Effect.Highlight('line-<?php print $issue->lineStart."-".$issue->lineEnd; ?>', {duration: 1.5});">
                    <?php print $issue->lineEnd;?></a>
                </td>
                <td>
                    <a href="#line-<?php print $issue->lineStart-5; ?>"
                        onclick="new Effect.Highlight('line-<?php print $issue->lineStart."-".$issue->lineEnd; ?>', {duration: 1.5});">
                    <?php print (string)$issue->description;?></a>
                </td>
                <td>
                    <?php print $issue->fileName;?>
                </td>
                <td>
                    <?php print $issue->severity;?>
                </td>
            </tr>
            <?php } ?>
        </table>
        </div>
    </div>
</div>
<script language="javascript">
    <?php
        echo $jsCode;
    ?>
    // Call SideBar Observer
    Event.observe(window, 'load', init, true);
</script> 
<?php endif; // div sidebar ?> 
<pre><?php var_dump($lines)?></pre>