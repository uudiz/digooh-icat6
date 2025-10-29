<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<?php
$gmt = local_to_gmt(time());
if($media->starttime == '00:00' || $media->starttime == '') {
	$media->starttime = date('Y-m-d', $gmt);
}
if($media->endtime == '00:00' || $media->endtime == '') {
	$media->endtime = date('Y-m-d', $gmt+3600*24*31);
}
?>
<table id="mediaTable" class="from-panel" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td width="70px;"><?php echo lang('play_time');?></td>
		<td>
			<input type="text" name="playTime" id="playTime" value="<?php echo $media->duration;?>" style="width:150px;"/> &nbsp;<span class="example"><?php echo lang('fromat.hh.mm');?></span>
		</td>
	</tr>
	<tr>
		<td>Start Date</td>
		<td>
			<input type="text" style="width:120px;" id="startDate" name="startDate" readonly="readonly" class="date-input"  value="<?php echo $media->starttime;?>" >
		</td>
	</tr>
	<tr>
		<td>End Date</td>
		<td>
			<input type="text" style="width:120px;" id="endDate" name="endDate" readonly="readonly" class="date-input"  value="<?php echo $media->endtime;?>" >
		</td>
	</tr>
</table>
<p class="btn-center">
	<input type="hidden" name="itemId" id="itemId" value="<?php echo $media->id;?>" />
	<input type="hidden" name="areaId" id="areaId" value="<?php echo $media->area_id;?>" />
	<a class="btn-01" href="javascript:void(0);" onclick="campaign.saveEditWebpage();"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>
<script type="text/javascript">
	campaign.initEditMedia();
	campaign.initFormDate();
</script>