<div class="header">
	<?php print $title; ?>
</div>
<div class="filelist">
	<table border="0" cellspacing="2" cellpadding="3">
		<tr class="head">
			<td>
				<strong>file</strong>
			</td>
			<td width="50px" align="center">
				<strong>errors</strong>
			</td>
			<td width="50px" align="center">
				<strong>notices</strong>
			</td>
		</tr>
		<?php
		if(is_array($files)){
    		foreach($files as $key => $file){ 
    		?>
    		<tr class="<?php echo ($key % 2) ? 'oddrow' : 'file';?>">
    			<td>
    				<a href="./<?php print $file['complete']; ?>.html"><?php print $file['complete']; ?></a>
    			</td>
    			<td align="center">
    				<span class="errors"><?php print $file['count_errors']; ?></span>
    			</td>
    			<td align="center">
    				<span class="notices"> <?php print $file['count_notices']; ?></span>				
    			</td>
    		</tr>
    		<?php 
    		}
		} 
		?>
	</table>
</div>
