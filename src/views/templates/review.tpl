<div class="review">
    <div class="header">
     <a href="./<?php echo $csspath; ?>flatView.html">&larr; flat View</a> | <?php echo $title; ?>
    </div>
    <div class="filepath">
    <?php echo $filepath; ?>
    </div>
    
    <?php echo $source; ?>
    
    <?php if (!empty($issues)) : ?>
        
    <div id="sidebar">
        <table cellpadding="3">
            <thead>
                <tr>
                    <th width="40px" align="center">
                        <strong>start</strong>
                    </th>
                    <th width="40px" align="center">
                        <strong>end</strong>
                    </th>
                    <th>
                        <strong>comment</strong>
                    </th>
                    <th width="120px">
                        <strong>type of error</strong>
                    </th>
                    <th width="60px">
                        <strong>severity</strong>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($issues as $issue):?>
                <?php foreach($issue as $notice):?>
                
                <tr class="<?php print $notice->foundBy; ?>">
                    <td align="center">
                        <a href="#line_<?php print $notice->lineStart;?>" onClick="switchLine('line_<?php print $notice->lineStart;?>')">
                        <?php print $notice->lineStart; ?></a>
                    </td>
                    <td align="center">
                        <a href="#line_<?php print $notice->lineStart;?>" onClick="switchLine('line_<?php print $notice->lineStart;?>')">
                        <?php print $notice->lineEnd;?></a>
                    </td>
                    <td>
                        <a href="#line_<?php print $notice->lineStart;?>" onClick="switchLine('line_<?php print $notice->lineStart;?>')">
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
            </tbody>
        </table>
    </div>
    <script language="javascript">
        <?php
            echo $jsCode;
        ?>

        $(function() {
            $("div#sidebar").sidebar({width:600, height: 400, open : "click", close: "click", position: "right"});
        })
    </script> 
    <?php endif; // div sidebar ?> 
</div>