<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
	<tbody>
		<tr>
			<td width="120">
				<?php echo lang('filename');?>
			</td>
			<td>
				<?php echo $software->name;?>
			</td>
		</tr>
		<tr>
			<td >
				<?php echo lang('version');?>
			</td>
			<td>
				<?php echo $software->version;?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo lang("desc");?>
			</td>
			<td>
				<textarea name="descr" id="descr" rows="8" style="width:280px;"><?php echo $software->descr;?></textarea>
			</td>
			<td>&nbsp;</td>
		</tr>
	</tbody>
</table>
<p class="btn-center">
	<input type="hidden" id="id" name="id" value="<?php echo $software->id;?>" />
 	<a class="btn-01" href="javascript:void(0);" onclick="software.doSave();"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>
