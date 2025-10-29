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
					<?php echo lang('template.screen.date.format');?>
				</td>
				<td>
					<select id="format" name="format" class="text ui-widget-content ui-corner-all" onchange="template.screen.changeWeatherFormat(this);" style="width: 150px;">
						<?php foreach($formats as $k=>$v):?>
							<option value="<?php echo $k;?>"><?php echo $v;?></option>
						<?php endforeach;?>
					</select>
				</td>
			</tr>
			<?php if(false):?>
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
			<?php endif;?>
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
						<img border="1" style="width: 50px; height: 50px; display: none;" src="/images/weather/sunny.gif" id="weatherIcon">
						<div>
							<?php echo lang('weather.preview');?>
						</div>
					</div>
				</td>
			</tr>
		</tbody>
</table>
<p class="btn-center">
	<a class="btn-01" href="javascript:void(0);" onclick="template.screen.setWeatherSetting('date');"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>

<script type="text/javascript">
	template.screen.initColorSelector('colorSelector');
</script>