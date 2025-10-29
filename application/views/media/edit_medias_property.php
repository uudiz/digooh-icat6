<div id="validateTips">
	<div>
		<div id="formMsgContent"></div>
	</div>
</div>

<form method="POST" id="cf" action="/media/do_save_property">
	<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
		<tbody>
			<tr>
				<td><?php echo  $this->config->item("with_template") ? lang('categories') : lang('tag'); ?></td>
				<td width="300">
					<select name="" id="jquery-tagbox-select-options" style="width: 150px;">
						<?php foreach ($tags as $tag) : ?>
							<option value="<?php echo $tag->id; ?>"><?php echo $tag->name ?></option>
						<?php endforeach; ?>
					</select>
					<input type="text" id="jquery-tagbox-select" />
				</td>
			</tr>

			<tr>
				<td><?php echo lang('playtime'); ?></td>
				<td>
					<input type="text" style="width:80px" id="playTime" name="playTime" class="time-input" value="00:30" />
					<?php echo " " . lang('playtime.format'); ?>
				</td>
			</tr>

			<!-- 			
			<tr>
				<td>
					<input type="checkbox" id="playcountFlag" name="playcountFlag" value="0";/><?php echo lang('playcount') ?>
				</td>
				
				<td>
						<input type="number" id="playcountid" name="playcountid" defaultValue="1" class="text ui-widget-content ui-corner-all" min="1" value="1"
						readonly="readonly" style="width:80px;background:#ddd;" />	
				</td>
			</tr> 
			 -->


			<tr>
				<td><input type="checkbox" id="dateFlag" name="dateFlag" value="0" /><?php echo lang('date.range') ?></td>
				<td>
					<input type="text" style="width:80px;" id="startDate" name="startDate" readonly="readonly" class="date-input" value="">
					<em><?php echo lang('to'); ?></em>
					<input type="text" style="width:80px;" id="endDate" name="endDate" readonly="readonly" class="date-input" value="">
				</td>
			</tr>

			<!-- 							
			<tr>
			<td width="65px;"><input type="checkbox" id="alldayFlag" name="alldayFlag" checked="checked" value="1"; /><?php echo lang('all'); ?>&nbsp;<?php echo lang('day'); ?></td>
			<td>
				<input type="text" id="startTime" name="startTime"  class="time-input" value="" "readonly" style="width:80px;background:#ddd;" />
				<em><?php echo lang('to'); ?></em>
				<input type="text" id="endTime" name="endTime"  class="time-input" value="" readonly="readonly" style="width:80px;background:#ddd;" />
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
		<a class="btn-01" href="javascript:void(0);" onclick="mediaLib.doSaveProperty();"><span><?php echo lang('button.save'); ?></span></a>
		<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel'); ?></span></a>
	</p>
</form>

<script src="/static/js/jquery/jquery-ui-latest.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
	$(document).ready(function() {
		if (document.getElementById('dateFlag').value == "0") {
			$('#startDate').datepicker('destroy').addClass('gray');
			$('#endDate').datepicker('destroy').addClass('gray');
		} else if (document.getElementById('dateFlag').value == "1") {
			$('#startDate').datepicker({
				dateFormat: 'yy-mm-dd'
			}).removeClass('gray');

			$('#endDate').datepicker({
				dateFormat: 'yy-mm-dd'
			}).removeClass('gray');
		}
	});

	$(function() {

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
		$('#alldayFlag').click(function() {
			$('input:checkbox[id="alldayFlag"]').each(function() {
				if (this.checked) {
					$('#startTime').attr('readonly', 'readonly');
					$('#endTime').attr('readonly', 'readonly');
					$('#startTime').css('background', '#ddd');
					$('#endTime').css('background', '#ddd');
					$('#alldayFlag').val(1);
				} else {
					$('#startTime').removeAttr('readonly');
					$('#endTime').removeAttr('readonly');
					$('#startTime').css('background', '#fff');
					$('#endTime').css('background', '#fff');
					$('#alldayFlag').val(0);
				}
			});
		});

	});
</script>

<link rel="stylesheet" href="/static/css/jquery.tagbox.css" />
<script src="/static/js/jquery.tagbox.js" type="text/javascript" charset="utf-8"></script>


<script type="text/javascript">
	jQuery(function() {
		jQuery("#jquery-tagbox-text").tagBox();
		jQuery("#jquery-tagbox-select").tagBox({
			enableDropdown: true,
			dropdownSource: function() {
				return jQuery("#jquery-tagbox-select-options");
			}
		});
	});
</script>