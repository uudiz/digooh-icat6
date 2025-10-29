<head>
	<link rel="stylesheet" href="/static/css/jquery/jquery-ui.min.css" />
	<link rel="stylesheet" href="/static/css/jquery/chosen.min.css" />

	<script type="text/javascript" src="/static/js/jquery-1.8.3.min.js"></script>
	<script src='/static/js/jquery/chosen.jquery.min.js'></script>

</head>

<div id="validateTips">
	<div>
		<div id="formMsgContent"></div>
	</div>
</div>
<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
	<tbody>
		<tr>
			<td width="60">
				<?php echo lang('name'); ?>
			</td>
			<td>
				<input type="text" id="name" name="name" style="width:300px;" value="<?php echo $folder->name; ?>" />
			</td>
			<td>
				<div class="error" id="errorName" style="display:none;">
					<?php echo lang('warn.folder.name'); ?>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo lang("desc"); ?>
			</td>
			<td>
				<textarea name="descr" id="descr" rows="2" style="width:300px;"><?php echo $folder->descr; ?></textarea>
			</td>
			<td>&nbsp;</td>
		</tr>

		<tr>
			<td><?php echo  $this->config->item("with_template") ? lang('categories') : lang('tag'); ?></td>
			<td width="300">
				<select data-placeholder="Choose Tags..." id="jquery-tagbox-select-options" class="chosen-select tag-input-style" multiple>
					<option value="0"></option>
					<?php foreach ($tags as $tag) : ?>
						<option value="<?php echo $tag->id; ?>" <?php $sel_tags = explode(',', $folder->tags);
																if (is_array($sel_tags) && in_array($tag->id, $sel_tags)) : ?>selected<?php endif; ?>><?php echo $tag->name; ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>

		<tr>
			<td><?php echo lang('playtime'); ?></td>
			<td>
				<input type="text" style="width:80px" id="playTime" name="playTime" class="time-input" value="<?php echo $folder->play_time; ?>" />
				<?php echo " " . lang('playtime.format'); ?>
				(Image only)
			</td>
		</tr>

		<!-- 	
			<tr>
				<td>
					<input type="checkbox" id="playcountFlag" name="playcountFlag" <?php if ($folder->play_count) {
																						echo 'checked="checked" value="1"';
																					} else {
																						echo 'value="0"';
																					} ?> /><?php echo lang('playcount') ?>
				</td>
				
				<td>
						<input type="number" id="playcountid" name="playcountid" defaultValue="1" class="text ui-widget-content ui-corner-all" min="1" value="<?php if ($folder->play_count) echo $folder->play_count;
																																								else echo "1" ?>"
						<?php if ($folder->play_count) {
							echo 'style="width:90px;"';
						} else {
							echo 'readonly="readonly" style="width:80px;background:#ddd;"';
						} ?> />	
				</td>
			</tr> 
			 -->


		<tr>
			<td><input type="checkbox" id="dateFlag" name="dateFlag" <?php if ($folder->date_flag) {
																			echo 'checked="checked" value="1"';
																		} else {
																			echo 'value="0"';
																		} ?> /><?php echo lang('date.range') ?></td>
			<td>
				<input type="text" style="width:90px;" id="startDate" name="startDate" readonly="readonly" class="date-input" value="<?php echo $folder->start_date; ?>">
				<em><?php echo lang('to'); ?></em>
				<input type="text" style="width:90px;" id="endDate" name="endDate" readonly="readonly" class="date-input" value="<?php echo $folder->end_date; ?>">
			</td>
		</tr>


		<!-- 							
			<tr>
			<td width="65px;"><input type="checkbox" id="alldayFlag" name="alldayFlag" <?php if ($folder->all_day_flag) {
																							echo 'checked="checked" value="1"';
																						} else {
																							echo 'value="0"';
																						} ?> /><?php echo lang('all'); ?>&nbsp;<?php echo lang('day'); ?></td>
			<td>
				<input type="text" id="startTime" name="startTime"  class="time-input" value="<?php echo $folder->start_time; ?>" <?php if ($folder->all_day_flag) {
																																		echo 'readonly="readonly" style="width:80px;background:#ddd;"';
																																	} else {
																																		echo 'style="width:80px;"';
																																	} ?> />
				<em><?php echo lang('to'); ?></em>
				<input type="text" id="endTime" name="endTime"  class="time-input" value="<?php echo $folder->end_time; ?>" <?php if ($folder->all_day_flag) {
																																echo 'readonly="readonly" style="width:80px;background:#ddd;"';
																															} else {
																																echo 'style="width:80px;"';
																															} ?> />
			</td>
			</tr>
		 		
			<tr>
				<td> </td>
			  <td width="200"><?php echo lang('schedule.time.format'); ?> </td>
			</tr>
		-->

	</tbody>
</table>
<p class="btn-center">
	<input type="hidden" id="id" name="id" value="<?php echo $folder->id; ?>" />
	<a class="btn-01" href="javascript:void(0);" onclick="<?php echo $folder_target ?>.saveFolder(this);"><span><?php echo lang('button.save'); ?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel'); ?></span></a>
</p>
<p>
	* Once saved, all file tags under this folder will be set to current tag.
</p>
<script type="text/javascript">
	//cfg.initDownload();
</script>

<script src="/static/js/jquery/jquery-ui-latest.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="/static/css/jquery/jquery.ui.all.css" />
<link rel="stylesheet" href="/static/css/jquery.tagbox.css" />
<script src="/static/js/jquery.tagbox.js" type="text/javascript" charset="utf-8"></script>

<script type="text/javascript">
	$(document).ready(function() {
		$('.chosen-select').chosen({
			width: "300px"
		});

		if (document.getElementById('dateFlag').value == "0") {
			$('#startDate').datepicker('destroy').addClass('gray');
			$('#endDate').datepicker('destroy').addClass('gray');
		} else if (document.getElementById('dateFlag').value == "1") {
			var sd = $('#startDate');
			var ed = $('#endDate');
			sd.datepicker({
				dateFormat: 'yy-mm-dd'
			}).removeClass('gray');

			ed.datepicker({
				dateFormat: 'yy-mm-dd'
			}).removeClass('gray');
		}
	});

	$(function() {
		/*
				$('#playcountFlag').click(function() {
						$('input:checkbox[id="playcountFlag"]').each(function(){
		            if (this.checked) {
		            	$('#playcountid').removeAttr('readonly');
		            	$('#playcountid').css('background', '#fff');
		            	$('#playcountFlag').val(1);
		            }
		            else{
		            	$('#playcountid').attr('readonly','readonly');
		            	$('#playcountid').css('background', '#ddd');
		                $('#playcountFlag').val(0);
		            }
		         });
				});
		*/
		$('#dateFlag').click(function() {
			var sd = $('#startDate');
			var ed = $('#endDate');
			$('input:checkbox[id="dateFlag"]').each(function() {
				if (this.checked) {
					sd.datepicker({
						dateFormat: 'yy-mm-dd'
					}).removeClass('gray');

					ed.datepicker({
						dateFormat: 'yy-mm-dd'
					}).removeClass('gray');
					$('#dateFlag').val(1);
				} else {
					sd.datepicker('destroy').addClass('gray');
					ed.datepicker('destroy').addClass('gray');
					$('#dateFlag').val(0);
				}
			});
		});
		/*
	    	$('#alldayFlag').click(function() {
	    		$('input:checkbox[id="alldayFlag"]').each(function(){
		            if (this.checked) {
		              	$('#startTime').attr('readonly','readonly');
		              	$('#endTime').attr('readonly','readonly');
		              	$('#startTime').css('background', '#ddd');
					  	$('#endTime').css('background', '#ddd');
		              	$('#alldayFlag').val(1);
		              	
		            }else {
		            	$('#startTime').removeAttr('readonly');
		            	$('#endTime').removeAttr('readonly');            
		             	$('#startTime').css('background', '#fff');
		              	$('#endTime').css('background', '#fff');  
		                $('#alldayFlag').val(0);
		            }
		        });	
	    	});
	    */
	});
</script>