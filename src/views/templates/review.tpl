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
            <?php foreach($issues as $issue):?>
            <?php foreach($issue as $notice):?>
            
            <tr class="<?php print $notice->foundBy; ?>">
                <td align="center">
                    <a href="#line_<?php print $notice->lineEnd;?>" 
                        onclick="new Effect.Highlight('line_<?php print $notice->lineStart."-".$notice->lineEnd; ?>', {duration: 1.5});">
                    <?php print $notice->lineStart; ?></a>
                </td>
                <td align="center">
                    <a href="#line_<?php print $notice->lineEnd;?>"
                        onclick="new Effect.Highlight('line_<?php print $notice->lineStart."-".$notice->lineEnd; ?>', {duration: 1.5});">
                    <?php print $notice->lineEnd;?></a>
                </td>
                <td>
                    <a href="#line_<?php print $notice->lineStart; ?>"
                        onclick="new Effect.Highlight('line_<?php print $notice->lineStart."-".$notice->lineEnd; ?>', {duration: 1.5});">
                    <?php print (string)$notice->description;?></a>
                </td>
                <td>
                    <?php print $notice->foundBy;?>
                </td>
                <td>
                    <?php print $notice->severity;?>
                </td>
            </tr>
            <?php endforeach; //$issue as $notice ?>
            <?php endforeach; //$issues as $issue ?>
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