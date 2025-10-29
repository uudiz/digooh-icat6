<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
	<tr>
		<td colspan="2">
			<img src="<?php echo $path;?>?t=<?php echo time();?>" width="auto" height="480"/>
		</td>
	</tr>
	<tr>
		<td>
			Captured Time: <?php echo $time;?> &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="player.remove_screenshot(<?php echo $id;?>,'<?php echo lang('tip.remove.item');?>');">Delete</a>
		</td>
	</tr>
</table>