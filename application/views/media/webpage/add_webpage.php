<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<form method="POST" id="cf" action="/webpage/do_save" >
	<table width="500" cellspacing="0" cellpadding="0" border="0" class="from-panel">
		<tbody>
			<tr>
				<td width="120">
					<?php echo lang('webpage.name');?>
				</td>
				<td>
					<input type="hidden" id="type" name="type" value="2"/>
					<input type="text" id="name" name="name" class="text ui-widget-content ui-corner-all" style="width:240px;"/>
				</td>
				<td>
					<div class="attention" id="errorName" style="display:none;">
						<?php echo lang('warn.webpage.name');?>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang('desc');?>
				</td>
				<td>
					<textarea name="descr" id="descr" class="ui-widget-content ui-corner-all" rows="2" style="width:240px;"></textarea>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<?php echo lang('url');?>
				</td>
				<td>
					<input type="text" id="url" name="url" class="text ui-widget-content ui-corner-all" style="width:240px;"/>
				</td>
				<td>
					<div class="attention" id="errorName" style="display:none;">
						<?php echo lang('warn.webpage.url');?>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</form>
<br/>
<p class="btn-center">
	<a class="btn-01" href="javascript:void(0);" onclick="webpage.doSave();"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>
