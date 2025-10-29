<div id="validateTips">
    <div>
        <div id="formMsgContent">
        </div>
    </div>
</div>
<table width="80%" border="0" cellspacing="0" cellpadding="0" class="from-panel-2">
  	<tr>
    	<td width="80px"><?php echo lang('name');?>:</td>
		<td><input name="name" id="name" type="text" style="width: 200px;"/></td>
  	</tr>
  	<?php
	if($this->config->item('mia_system_set') == $this->config->item('mia_system_all')):
	?>
  	<tr>
  		<td><?php echo lang('type');?></td>
  		<td>
  			<select name="sch_type" id="sch_type">
				<option value="0"><?php echo lang('type.0');?></option>
				<option value="1"><?php echo lang('type.1');?></option>
			</select>
  		</td>
  	</tr>
  	<?php
	elseif($this->config->item('mia_system_set') == $this->config->item('mia_system_np100')):
	?>
		<input type="hidden" id="sch_type" name="sch_type" value="0"/>
	<?php
	elseif($this->config->item('mia_system_set') == $this->config->item('mia_system_np200')):
	?>
		<input type="hidden" id="sch_type" name="sch_type" value="1"/>
	<?php
	endif;
	?>
  <tr>
    <td><?php echo lang('desc');?>:</td>
	<td valign="middle">
    	<textarea name="descr" id="descr" rows="2"  style=" width: 200px;"></textarea>
	</td>
 </tr>
</table>
<p class="btn-center">
	<a href="javascript:void(0);" onclick="schedule.form.createSchedule('<?php echo lang('warn.scheudle.group.empty');?>');return false;" class="btn-01"><span><?php echo lang('button.add');?></span></a>
	<a href="javascript:void(0);" onclick="tb_remove();return false;" class="btn-01"><span><?php echo lang('button.cancel');?></span></a>
</p>			