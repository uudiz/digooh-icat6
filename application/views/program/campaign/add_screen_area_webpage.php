<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<?php
$gmt = local_to_gmt(time());
$starttime = date('Y-m-d', $gmt);
$endtime = date('Y-m-d', $gmt+3600*24*31);
?>
<table id="mediaTable" class="from-panel" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td width="70px;">URL</td>
		<td>
			<input type="text" name="url" id="url" value="" style="width:250px;"/><br/>
			ex: http://google.com
		</td>
	</tr>
	<!--
	<tr>
		<td width="70px;"><?php echo lang('play_time');?></td>
		<td>
			<input type="text" name="playTime" id="playTime" value="00:10" style="width:150px;"/> &nbsp;<span class="example"><?php echo lang('fromat.webpage.hh.mm');?></span>
		</td>
	</tr>
	
	<tr>
		<td>URL for IP Camera</td>
		<td><input type="checkbox" name="url_type" id="url_type"/></td>
	</tr>
	-->
	<tr id="updateFrequency">
		<td width="70px;">Update Frequency</td>
		<td>
			<input type="text" name="updateF" id="updateF" value="00:10" style="width:130px;"/> &nbsp;<span class="example"><?php echo lang('fromat.hh.mm');?></span>
		</td>
	</tr>
	<!--
	<tr>
		<td>Start Date</td>
		<td>
			<input type="text" style="width:120px;" id="startDate" name="startDate" readonly="readonly" class="date-input"  value="<?php echo $starttime;?>" >
		</td>
	</tr>
	<tr>
		<td>End Date</td>
		<td>
			<input type="text" style="width:120px;" id="endDate" name="endDate" readonly="readonly" class="date-input"  value="<?php echo $endtime;?>" >
		</td>
	</tr>
	-->
</table>
<p class="btn-center">
	<input type="hidden" name="playTime" id="playTime" value="01:00" style="width:150px;"/>
	<input type="hidden" style="width:120px;" id="startDate" name="startDate" readonly="readonly" class="date-input"  value="<?php echo $starttime;?>" >
	<input type="hidden" style="width:120px;" id="endDate" name="endDate" readonly="readonly" class="date-input"  value="<?php echo $endtime;?>" >
	<a class="btn-01" href="javascript:void(0);" onclick="campaign.saveAreaMediaWebpage(<?php echo $playlist_id;?>, <?php echo $area_id;?>, <?php echo $media_type;?>);"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>
<script type="text/javascript">
	campaign.initEditMedia();
	campaign.initFormDate();
	$(function() {
		$('#url_type').click(function() {
			$('input:checkbox[id="url_type"]').each(function(){
		    	if (this.checked) {
					$('#updateFrequency').hide();
		   		}else {
					$('#updateFrequency').show();
		   		}
		    });	
		});
	});
</script>