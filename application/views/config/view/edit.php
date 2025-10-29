<div id="validateTips">
	<div >
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
					<input type="text" id="name" name="name" class="text ui-widget-content ui-corner-all" style="width:200px;" value="<?php echo $config->name;?>"/>
				</td>
				<td>
					<div class="attention" id="errorName" style="display:none;">
						<?php echo lang('warn.view.name');?>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang('start.time'); ?>
				</td>
				<td>
					<input type="text" readonly="readonly" id="startTime" name="startTime" style="width:200px;" value="<?php echo $config->start_datetime;?>"/>
				</td>
				<td>
					<div class="attention" id="errorStartTime" style="display:none;">
						<?php echo lang('warn.view.start.time');?>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang('end.time'); ?>
				</td>
				<td>
					<input type="text" readonly="readonly" id="endTime" name="endTime" style="width:200px;" value="<?php echo $config->end_datetime;?>" />
				</td>
				<td>
					<div class="attention" id="errorEndTime" style="display:none;">
						<?php echo lang('warn.view.end.time');?>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang('brightness'); ?>
				</td>
				<td>
					<input type="text" id="brightness" name="brightness" style="width:100px;" value="<?php echo $config->brightness;?>"/><?php echo lang('config.rang');?>
				</td>
				<td>
					<div class="attention" id="errorBrightness" style="display:none;">
						<?php echo lang('warn.view.brightness');?>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang('saturation'); ?>
				</td>
				<td>
					<input type="text" id="saturation" name="saturation" style="width:100px;" value="<?php echo $config->saturation;?>" /><?php echo lang('config.rang');?>
				</td>
				<td>
					<div class="attention" id="errorSaturation" style="display:none;">
						<?php echo lang('warn.view.saturation');?>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang('contrast'); ?>
				</td>
				<td>
					<input type="text" id="contrast" name="contrast" style="width:100px;" value="<?php echo $config->contrast;?>"/><?php echo lang('config.rang');?>
				</td>
				<td>
					<div class="attention" id="errorContrast" style="display:none;">
						<?php echo lang('warn.view.contrast');?>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang("desc");?>
				</td>
				<td>
					<textarea name="descr" id="descr" class="ui-widget-content ui-corner-all" rows="2" style="width:200px;"><?php echo $config->descr;?></textarea>
				</td>
				<td>&nbsp;</td>
			</tr>
		</tbody>
</table>
<p class="btn-center">
	<input type="hidden" id="id" name="id" value="<?php echo $config->id;?>" />
 	<a class="btn-01" href="javascript:void(0);" onclick="cfg.saveView();"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>
<script type="text/javascript">
	cfg.initView();
</script>
