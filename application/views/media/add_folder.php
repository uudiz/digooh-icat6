<head>
	<link rel="stylesheet" href="/static/css/jquery/chosen.min.css" />
	<script src='/static/js/jquery/chosen.jquery.min.js'></script>
</head>

<div id="validateTips">
	<div>
		<div id="formMsgContent"></div>
	</div>
</div>
<table border="0" class="from-panel">
	<tbody>
		<tr>
			<td width="100">
				<?php echo lang('name'); ?>
			</td>
			<td>
				<input type="text" id="name" name="name" style="width:300px;" />
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
				<textarea name="descr" id="descr" rows="2" style="width:300px;"></textarea>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td><?php echo  $this->config->item("with_template") ? lang('categories') : lang('tag') ?></td>
			<td>
				<select data-placeholder=" " id="jquery-tagbox-select-options" class="chosen-select tag-input-style" multiple>
					<option value="0"></option>
					<?php foreach ($tags as $tag) : ?>
						<option value="<?php echo $tag->id; ?>" <?php if (isset($parent)) $sel_tags = explode(',', $parent->tags);
																if (isset($sel_tags) && is_array($sel_tags) && in_array($tag->id, $sel_tags)) : ?>selected<?php endif; ?>><?php echo $tag->name; ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>

		<tr>
			<td><?php echo lang('playtime'); ?></td>
			<td>
				<input type="text" style="width:90px" id="playTime" name="playTime" class="time-input" value="<?php echo $play_time; ?>" />
				<?php echo " " . lang('playtime.format'); ?>
			</td>
		</tr>


		<tr>
			<td><input type="checkbox" id="dateFlag" name="dateFlag" value=<?php if (isset($parent)) echo $parent->date_flag; ?> /><?php echo lang('date.range') ?></td>
			<td>
				<input type="text" style="width:90px;" id="startDate" name="startDate" readonly="readonly" class="date-input" value="<?php if (isset($parent)) echo $parent->start_date; ?>" />
				<em><?php echo lang('to'); ?></em>
				<input type="text" style="width:90px;" id="endDate" name="endDate" readonly="readonly" class="date-input" value="<?php if (isset($parent)) echo $parent->end_date; ?>" />
			</td>
		</tr>

	</tbody>
</table>
<p class="btn-center">
	<input type="hidden" id="id" name="id" value="0" />
	<input type="hidden" id="parent_id" value="<?php if (isset($parent)) echo $parent->id;
												else echo '0'; ?>" />
	<a class="btn-01" href="javascript:void(0);" onclick="<?php echo $folder_target ?>.saveFolder(this);"><span><?php echo lang('button.save'); ?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel'); ?></span></a>
</p>




<script type="text/javascript">
	Date.prototype.Format = function(fmt) { //author: meizz   
		var o = {
			"M+": this.getMonth() + 1, //月份   
			"d+": this.getDate(), //日   
			"h+": this.getHours(), //小时   
			"m+": this.getMinutes(), //分   
			"s+": this.getSeconds(), //秒   
			"q+": Math.floor((this.getMonth() + 3) / 3), //季度   
			"S": this.getMilliseconds() //毫秒   
		};
		if (/(y+)/.test(fmt))
			fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
		for (var k in o)
			if (new RegExp("(" + k + ")").test(fmt))
				fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
		return fmt;
	}

	$(document).ready(function() {
		$('.chosen-select').chosen({
			width: "300px"
		});

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

	});
</script>