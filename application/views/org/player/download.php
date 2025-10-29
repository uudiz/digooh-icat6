<table width="500" cellspacing="0" cellpadding="0" border="0" class="from-panel">
	<tr>
		<td id="download">
			<?php
			if($return_code == 1) {
				echo $return_msg;
			}else {
				echo 'Download playlist package for local playback<br/><br/>Click this URL: ';
			?>
				<a href="<?php echo $return_url;?>" target="_blank"><?php echo 'http://'.$_SERVER['HTTP_HOST'].substr($return_url, 1, strlen($return_url)-1);?></a>
			<?php
			}
			?>
			
		</td>
	</tr>
</table>

