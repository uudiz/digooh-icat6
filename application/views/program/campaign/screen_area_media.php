<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<table id="mediaTable" class="from-panel" cellspacing="0" cellpadding="0" border="0">
	<!-- 
	<tr>
		<td width="120px;"><?php echo lang('play_time');?></td>
		<td>
			<input type="text" name="playTime" id="playTime" value="<?php echo $media->duration;?>" style="width:150px;"/> <span class="example"><?php echo lang('fromat.mm.ss');?></span>
		</td>
	</tr>
	 -->
	<?php
	if($playlist->pls_type != 1):
	?>
	<tr>
		<td><?php echo lang('image.size.control');?></td>
		<td>
			<select id="imgfit" name="imgfit" style="width: 200px">
				<option value="1" <?php if($media->img_fitORfill == '1'):?>selected="selected"<?php endif;?>><?php echo lang('image.size.control.fit');?></option>
				<option value="0" <?php if($media->img_fitORfill == '0'):?>selected="selected"<?php endif;?>><?php echo lang('image.size.control.fill');?></option>
			</select>
		</td>
	</tr>
	<?php
	endif;
	?>
	<?php if($media_type != $this->config->item('media_type_video')):?>
	<?php if(false):?>
	<tr>
		<td><?php echo lang('transition_time');?></td>
		<td>
			<select name="transitionTime" id="transitionTime" style="width:150px;">
				<?php for($i = 0; $i < 0.9; $i+=0.1):?>
					<option value="<?php echo $i;?>" <?php if($media->transtime == $i):?>selected="selected"<?php endif;?>><?php echo $i;?></option>
				<?php endfor;?>
			</select>
		</td>
	</tr>
	<?php endif;?>
	<tr>
		<td colspan="2"><?php echo lang('transition_mode');?></td>
	</tr>
	<tr>
		<td colspan="2" style="margin-left:20px; margin-right:20px; padding-left:60px; padding-right: 60px;">
			<?php if(false):?>
			<?php foreach($transmode as $key=>$value):?>
				<img id="<?php echo $key;?>" src="/images/transfer/Transfer_Mode_<?php if($key < 10){echo '0'.$key;}else{echo $key;} if($media->transmode == $key){echo '_Active';}?>.png" title="<?php echo $value;?>" />
			<?php endforeach;?>
			<?php endif;?>
			<?php for($i = 1; $i < 28; $i++):?>
				<!--
				<img id="<?php echo $i;?>" src="/images/transfer/Transfer_Mode_<?php if($i < 10){echo '0'.$i;}else{echo $i;} if($media->transmode == $i){echo '_Active';} if(($transmode_type == $this->config->item('transmode_type_image') && $i > 12 && $i < 26 || $i == 27)  || ($transmode_type == $this->config->item('transmode_type_part') && $i > 18 && $i < 23 || $i == 27)){echo '_Inactive';}?>.png" <?php if($media->transmode == $i){echo 'class="active"';}?> title="" />
				-->
				<?php
				if($i == 27):
				?>
				<img id="<?php echo $i;?>" src="/images/transfer/Transfer_Mode_<?php if($i < 10){echo '0'.$i;}else{echo $i;} if($media->transmode == $i){echo '_Active';} if($transmode_type != $this->config->item('transmode_type_full')){echo '_Inactive';}?>.png" <?php if($media->transmode == $i){echo 'class="active"';}?> title="" />
				<?php
				else:
				?>
				<img id="<?php echo $i;?>" src="/images/transfer/Transfer_Mode_<?php if($i < 10){echo '0'.$i;}else{echo $i;} if($media->transmode == $i){echo '_Active';} if(($transmode_type == $this->config->item('transmode_type_image') && $i > 12 && $i < 26 || $i == 27)  || ($transmode_type == $this->config->item('transmode_type_part') && $i > 18 && $i < 23)){echo '_Inactive';}?>.png" <?php if($media->transmode == $i){echo 'class="active"';}?> title="" />
				<?php
				endif;
				?>	
			<?php endfor;?>
			<img id="0" src="/images/transfer/Transfer_Mode_00<?php if($media->transmode == 0){echo '_Active';}?>.png" <?php if($media->transmode == 0){echo 'class="active"';}?> title="" />
		</td>
	</tr>
	<?php endif;?>
</table>
<p class="btn-center">
		 <input type="hidden" name="transmode" id="transmode" value="<?php echo $media->transmode;?>" />
		 <input type="hidden" name="itemId" id="itemId" value="<?php echo $media->id;?>" />
		 <input type="hidden" name="areaId" id="areaId" value="<?php echo $media->area_id;?>" />
         <a class="btn-01" href="javascript:void(0);" onclick="campaign.saveEditMedia();"><span><?php echo lang('button.save');?></span></a>
		 <a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>
<script type="text/javascript">
	campaign.initEditMedia();
</script>