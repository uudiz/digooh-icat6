<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<form method="POST" id="cf" >
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="from-panel">
		<tbody>
			<tr>
				<td width="80">
					<?php echo lang('template.screen.time.format');?>
				</td>
				<td>
					<select id="format" name="format" class="text ui-widget-content ui-corner-all" onchange="template.screen.changeTimeFormat(this);" style="width: 150px;">
						<?php foreach($formats as $k=>$v):?>
							<option value="<?php echo $k;?>"><?php echo $v;?></option>
						<?php endforeach;?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang('template.screen.font.family');?>
				</td>
				<td>
					<select id="family" name="family" class="text ui-widget-content ui-corner-all" onchange="template.screen.changeDateFamily(this);" style="width: 150px;">
						<?php foreach($familys as $v):?>
							<option value="<?php echo $v;?>"><?php echo $v;?></option>
						<?php endforeach;?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang('template.screen.font.size');?>
				</td>
				<td>
					<select id="fontSize" name="fontSize" class="text ui-widget-content ui-corner-all" onchange="template.screen.changeDateFontSize(this);" style="width: 150px;">
						<?php foreach($sizes as $v):?>
							<option value="<?php echo $v;?>"><?php echo $v;?></option>
						<?php endforeach;?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang('template.screen.font.bold');?>
				</td>
				<td >
					<input type="checkbox" id="bold" name="bold"  onchange="interaction.screen.changeDateFontBold(this);"/>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang('template.screen.font.color');?>
				</td>
				<td>
					<input type="hidden" id="color" name="color" value="#000000" />
					<div id="colorSelector" class="color"><div style="background-color: #000000"></div></div>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<?php echo lang('template.preview');?>
				</td>
				<td>
					<div id="datePreview" style="font-family:<?php echo $family;?>;font-size:<?php echo $size;?>">
						<?php echo $preview;?>
					</div>
				</td>
			</tr>
		</tbody>
</table>
<p class="btn-center">
	<a class="btn-01" href="javascript:void(0);" onclick="interaction.screen.setDateSetting('time');"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>

<script type="text/javascript">
	interaction.screen.initColorSelector('colorSelector');
</script>

<!--
<div id="dialog" title="<?php echo lang('template.screen.time.setting');?>">
	<form>
		<fieldset>
			<label><?php echo lang('template.screen.time.format');?></label>
			<select id="format" name="format" class="text ui-widget-content ui-corner-all">
				<?php foreach($formats as $k=>$v):?>
					<option value="<?php echo $k;?>"><?php echo $v;?></option>
				<?php endforeach;?>
			</select>
			<label>
				<?php echo lang('template.screen.font.family');?>
			</label>
			<select id="family" name="family" class="text ui-widget-content ui-corner-all">
				<?php foreach($familys as $v):?>
					<option value="<?php echo $v;?>"><?php echo $v;?></option>
				<?php endforeach;?>
			</select>
			<label>
				<?php echo lang('template.screen.font.size');?>
			</label>
			<select id="fontSize" name="fontSize" class="text ui-widget-content ui-corner-all">
				<?php foreach($sizes as $v):?>
					<option value="<?php echo $v;?>"><?php echo $v;?></option>
				<?php endforeach;?>
			</select>
			<label>
				<?php echo lang('template.screen.font.bold');?>
			</label>
			<input type="checkbox" id="bold" name="bold" checked="checked" />
			<input type="hidden" id="color" name="color" value="#00ff00" />
			<div id="colorSelector"><div style="background-color: #00ff00"></div></div>
		</fieldset>
	</form>
</div>
-->