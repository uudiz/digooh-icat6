<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<table id="mediaTable" class="from-panel" cellspacing="0" cellpadding="0" border="0">
<?php
	if($type == 'playtime') {
?>
	<tr>
		<td width="100px;"><?php echo lang('play_time');?></td>
		<td>
			<input type="text" name="playTime" id="playTime" value="" style="width:150px;"/> <span class="example"><?php echo lang('fromat.mm.ss');?></span>
		</td>
	</tr>
<?php
	}
	if($type == 'transition') {
?>
	<?php if($media_type != $this->config->item('media_type_video')):?>
	<tr>
		<td colspan="2"><?php echo lang('transition_mode');?></td>
	</tr>
	<tr>
		<td colspan="2" style="margin-left:20px; margin-right:20px; padding-left:60px; padding-right: 60px;">
			<?php for($i = 1; $i < 28; $i++):?>
			<!--
			<img id="<?php echo $i;?>" src="/images/transfer/Transfer_Mode_<?php if($i < 10){echo '0'.$i;}else{echo $i;} if(($transmode_type == $this->config->item('transmode_type_image') && $i > 12 && $i < 26 || $i == 27)  || ($transmode_type == $this->config->item('transmode_type_part') && $i > 18 && $i < 23 || $i == 27 )){echo '_Inactive';}?>.png" title="" />
			-->	
			<?php
				if($i == 27):
			?>
				<img id="<?php echo $i;?>" src="/images/transfer/Transfer_Mode_<?php if($i < 10){echo '0'.$i;}else{echo $i;} if($transmode_type != $this->config->item('transmode_type_full')){echo '_Inactive';}?>.png" title="" />
			<?php
				else:
			?>
				<img id="<?php echo $i;?>" src="/images/transfer/Transfer_Mode_<?php if($i < 10){echo '0'.$i;}else{echo $i;} if(($transmode_type == $this->config->item('transmode_type_image') && $i > 12 && $i < 26 || $i == 27)  || ($transmode_type == $this->config->item('transmode_type_part') && $i > 18 && $i < 23)){echo '_Inactive';}?>.png" title="" />
			<?php
				endif;
			?>
			<?php endfor;?>
			<img id="0" src="/images/transfer/Transfer_Mode_00_Active.png" class="active" title="" />
		</td>
	</tr>
	<?php endif;?>
<?php
		}
?>
</table>
<p class="btn-center">
	<input type="hidden" name="transmode" id="transmode" value="0" />
	<input type="hidden" name="areaId" id="areaId" value="<?php echo $area_id;?>" />
	<input type="hidden" id="playlistId" value="<?php echo $playlistId;?>" />
	<input type="hidden" name="type" id="type" value="<?php echo $type;?>" />
	<a class="btn-01" href="javascript:void(0);" onclick="campaign.saveAreaEditMedia();"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>
<script type="text/javascript">
	campaign.initEditMedia();
</script>