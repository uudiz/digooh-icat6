<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<form method="POST" id="cf" >
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="from-panel">
		<tr>
			<td width="50%">
				<table cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td><?php echo lang('btnGroup.width');?>: </td>
						<td><input type="text" id="area_width" name="area_width" value="980" class="text ui-widget-content ui-corner-all" style="width: 100px;"/> px</td>
					</tr>
					<tr>
						<td><?php echo lang('btnGroup.col');?>: </td>
						<td>
							<select id="col" name="col" class="text ui-widget-content ui-corner-all" style="width: 100px;">
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3" selected>3</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php echo lang('btnGroup.left');?></td>
						<td><input type="text" id="left" name="left" value="0" class="text ui-widget-content ui-corner-all" style="width: 100px;"/> px</td>
					</tr>
					<tr>
						<td><?php echo lang('btnGroup.button.width');?>: </td>
						<td><input type="text" id="width" name="width" value="100" class="text ui-widget-content ui-corner-all" style="width: 100px;"/> px</td>
					</tr>
					<tr>
						<td><?php echo lang('btnGroup.space.between');?>: </td>
						<td><input type="text" id="space_between" name="space_between" value="20" class="text ui-widget-content ui-corner-all" style="width: 100px;"/> px</td>
					</tr>
				</table>
			</td>
			<td width="50%">
				<table cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td><?php echo lang('btnGroup.height');?>: </td>
						<td><input type="text" id="area_height" name="area_height" value="440" class="text ui-widget-content ui-corner-all" style="width: 100px;"/>px</td>
					</tr>
					<tr>
						<td><?php echo lang('btnGroup.row');?>: </td>
						<td>
							<select id="row" name="row" class="text ui-widget-content ui-corner-all" style="width: 100px;">
								<option value="1">1</option>
								<option value="2" selected>2</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php echo lang('btnGroup.top');?>: </td>
						<td><input type="text" id="top" name="top" value="0" class="text ui-widget-content ui-corner-all" style="width: 100px;"/>px</td>
					</tr>
					<tr>
						<td><?php echo lang('btnGroup.button.height');?>: </td>
						<td><input type="text" id="height" name="height" value="100" class="text ui-widget-content ui-corner-all" style="width: 100px;"/>px</td>
					</tr>
					<tr>
						<td><?php echo lang('btnGroup.line.space');?>: </td>
						<td><input type="text" id="line_space" name="line_space" value="20" class="text ui-widget-content ui-corner-all" style="width: 100px;"/>px</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</form>
<p class="btn-center">
	<a class="btn-01" href="javascript:void(0);" onclick="interaction.screen.setBtnGroupSetting('btnGroup');"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>