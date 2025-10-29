<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<form method="POST" id="cf" action="/interaction/doSave" >
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="from-panel">
		<tbody>
			<tr>
				<td width="60">
					<?php echo lang('interaction.name');?>
				</td>
				<td>
					<input type="text" id="name" name="name" class="text ui-widget-content ui-corner-all" style="width:200px;" value="<?php echo $interaction->name;?>"/>
				</td>
				<td>
					<div class="attention" id="errorName" style="display:none;">
						<?php echo lang('warn.template.name');?>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang('resolution'); ?>
				</td>
				<td>
					<?php echo $interaction->width.'X'.$interaction->height;?>
					<input type="hidden" id="screen" name="screen" value="<?php echo $interaction->width.'X'.$interaction->height;?>" />
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang("desc");?>
				</td>
				<td>
					<textarea name="descr" id="descr" class="ui-widget-content ui-corner-all" rows="2" style="width:200px;"><?php echo $interaction->descr;?></textarea>
				</td>
				<td>&nbsp;</td>
			</tr>
		</tbody>
</table>
</form>
<p class="btn-center">
	<input type="hidden" id="id" name="id" value="<?php echo $interaction->id;?>" />
	<a class="btn-01" href="javascript:void(0);" onclick="interaction.doEdit();"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>
