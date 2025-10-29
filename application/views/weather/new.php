<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>

<table width="100%" cellspacing="0" cellpadding="0" border="0" class="from-panel">
	<tbody>
			<tr>
				<td width="60">
					<?php echo lang('template.name');?>
				</td>
				<td>
					<input type="text" id="name" name="name" class="text ui-widget-content ui-corner-all" style="width:200px;"/>
				</td>
			</tr>
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



<p class="btn-center">
	<a class="btn-01" href="javascript:void(0);" onclick="weather.index.doSave();"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>