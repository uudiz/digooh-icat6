<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
		<tbody>
			<tr>
				<td width="120">
					<?php echo lang('group');?>
				</td>
				<td>
					<input type="text" id="name" name="name" style="width:150px;" />
				</td>
				<td>
					<div class="attention" id="errorName" style="display:none;">
						<?php echo lang('warn.group.name');?>
					</div>
				</td>
			</tr>
			<?php
			if($this->config->item('mia_system_set') == $this->config->item('mia_system_all')):
			?>
			<tr>
				<td><?php echo lang('type');?></td>
				<td>
					<select name="type" id="type" style="width: 80px;">
						<option value="0"><?php echo lang('type.0');?></option>
						<option value="1"><?php echo lang('type.1');?></option>
					</select>
				</td>
			</tr>
			<?php
			elseif($this->config->item('mia_system_set') == $this->config->item('mia_system_np100')):
			?>
				<input type="hidden" id="type" name="type" value="0"/>
			<?php
			elseif($this->config->item('mia_system_set') == $this->config->item('mia_system_np200')):
			?>
				<input type="hidden" id="type" name="type" value="1"/>
			<?php
			endif;
			?>
			<tr>
				<td>
					<?php echo lang("desc");?>
				</td>
				<td>
					<textarea name="descr" id="descr" rows="3" style="width:150px;"></textarea>
				</td>
				<td>&nbsp;</td>
			</tr>
		</tbody>
</table>
<p class="btn-center">
	<input type="hidden" id="id" name="id" value="0" />
 	<a class="btn-01" href="javascript:void(0);" onclick="g.doSave();"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>
