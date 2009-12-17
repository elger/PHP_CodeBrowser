<div class="header">
 <a href="./<?php print $csspath; ?>flatView.html">&larr; flat View</a> | <?php print $title; ?>
</div>
<div class="filepath">
<?php print $filepath; ?>
</div>
<div class="sourcecode">
<?php print $source; ?>
</div>
<?php if (!empty($errors)) : ?>
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
    		<?php 
    		  $javascript = '';
    		  foreach ($errors as $key=>$error){
    		  
    		      $htmlMessage = addcslashes("<span class=\"title ".$error['source']."\">".$error['source']."</span><span class=\"message\">".(string)$error['description']."</span>", "\"\'\0..\37!@\177..\377");
    		      
    		      if (isset($lines[$error['line']."-".$error['to-line']])) {
    		          $lines[$error['line']."-".$error['to-line']] .= $htmlMessage;
    		      } else {
    		          $lines[$error['line']."-".$error['to-line']] = $htmlMessage;   		      
    		      }
               
    		  
    		     //$lines["line-".$error['line']."-".$error['to-line']] .= addcslashes($error[0], "\"\'\0..\37!@\177..\377");
        	    
    		?>
    		<tr class="<?php print $error['source']; ?>">
    			<td align="center">
    				<a href="#line-<?php print $error['to-line']-5;?>" 
    		            onclick="new Effect.Highlight('line-<?php print $error['line']."-".$error['to-line']; ?>', {duration: 1.5});">
    				<?php print $error['line']; ?></a>
    			</td>
    			<td align="center">
    				<a href="#line-<?php print $error['to-line']-5;?>"
    				    onclick="new Effect.Highlight('line-<?php print $error['line']."-".$error['to-line']; ?>', {duration: 1.5});">
    				<?php print $error['to-line'];?></a>
    			</td>
    			<td>
    				<a href="#line-<?php print $error['line']-5; ?>"
    				    onclick="new Effect.Highlight('line-<?php print $error['line']."-".$error['to-line']; ?>', {duration: 1.5});">
    				<?php print (string)$error['description'];?></a>
    			</td>
    			<td>
    				<?php print $error['source'];?>
    			</td>
    			<td>
    				<?php print $error['severity'];?>
    			</td>
    		</tr>
    		<?php } ?>
    	</table>
        </div>
    </div>
</div>
<script language="javascript">
    <?php
        if (isset($lines) && is_array($lines)) {
            foreach ($lines as $num => $message) {
            print "if ($('line-".$num."')) {
                        new Tip('line-".$num."', 
        	                  '".$message."', 
        		              { className: 'tooltip', delay: 0.1 });\n
        		   }";
            }
        }
    ?>
    // Call SideBar Observer
    Event.observe(window, 'load', init, true);
</script> 
<?php endif; // div sidebar ?> 
