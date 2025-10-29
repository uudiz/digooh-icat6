<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<form method="POST" id="cf" action="/template/do_save" >
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="from-panel">
		<tbody>
			<tr>
				<td width="60">
					<?php echo lang('template.name');?>
				</td>
				<td>
					<input type="text" id="name" name="name" class="text ui-widget-content ui-corner-all" style="width:200px;"/>
				</td>
				<td>
					<div class="attention" id="errorName" style="display:none;">
						<?php echo lang('warn.template.name');?>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang('screen.type'); ?>
				</td>
				<td>
					<select id="screen" name="screen" class="text ui-widget-content ui-corner-all" style="width:200px;">
		                <?php foreach ($screens as $s): ?>
		                <option value="<?php echo $s->width.'X'.$s->height;?>"><?php echo $s->name; ?></option>
		                <?php endforeach; ?>
		            </select>
				</td>
			</tr>
			<?php
			if($this->config->item('mia_system_set') == $this->config->item('mia_system_all')):
			?>
			<tr>
				<td>
					Type
				</td>
				<td>
					<select id="template_type" name="template_type" class="text ui-widget-content ui-corner-all" style="width:200px;">
						<option value="1"><?php echo lang('type.1');?></option>
		                <option value="0"><?php echo lang('type.0');?></option>
		            </select>
				</td>
			</tr>
			<?php
			elseif($this->config->item('mia_system_set') == $this->config->item('mia_system_np100')):
			?>
				<input type="hidden" id="template_type" name="template_type" value="0"/>
			<?php
			elseif($this->config->item('mia_system_set') == $this->config->item('mia_system_np200')):
			?>
				<input type="hidden" id="template_type" name="template_type" value="1"/>
			<?php
			endif;
			?>
			<tr>
				<td>
					<?php echo lang("desc");?>
				</td>
				<td>
					<textarea name="descr" id="descr" class="ui-widget-content ui-corner-all" rows="2" style="width:200px;;"></textarea>
				</td>
				<td>&nbsp;</td>
			</tr>
		</tbody>
</table>
</form>
<p class="btn-center">
	<input type="hidden" name="type" id="type" value="<?php echo $type;?>"/>
	<a class="btn-01" href="javascript:void(0);" onclick="template.index.doSave();"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>
