<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<form method="POST" id="cf" action="/rss/do_save" >
	<table width="500" cellspacing="0" cellpadding="0" border="0" class="from-panel">
		<tbody>
			<tr>
				<td width="100">
					<?php echo lang('rss.name');?>
				</td>
				<td>
					<input type="text" id="name" name="name" class="text ui-widget-content ui-corner-all" style="width:200px;" value="<?php echo $rss->name;?>"/>
				</td>
				<td>
					<div class="attention" id="errorName" style="display:none;">
						<?php echo lang('warn.rss.name');?>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang("desc");?>
				</td>
				<td>
					<textarea name="descr" id="descr" class="ui-widget-content ui-corner-all" rows="2" style="width:200px;"><?php echo $rss->descr;?></textarea>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<?php echo lang("url");?>
				</td>
				<td>
					<input type="text" id="url" name="url" class="text ui-widget-content ui-corner-all" style="width:200px;" value="<?php echo $rss->url;?>"/>
				</td>
				<td>
					<div class="attention" id="errorName" style="display:none;">
						<?php echo lang('warn.rss.url');?>
					</div>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2">
					<span class="font-12 font-gray"><?php echo lang('tip.url');?></span>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang('update.interval');?>
				</td>
				<td colspan="2">
					<select id="interval" style="width:200px;">
						<option value="15" <?php if($rss->interval == 15):?>selected="selected"<?php endif;?> ><?php echo lang('update.interval.quarter');?></option>
						<option value="30" <?php if($rss->interval == 30):?>selected="selected"<?php endif;?> ><?php echo lang('update.interval.half');?></option>
						<option value="60" <?php if($rss->interval == 60):?>selected="selected"<?php endif;?> ><?php echo lang('update.interval.hour');?></option>
					</select>
				</td>
			</tr>
		</tbody>
</table>
<p class="btn-center">
	<input type="hidden" id="type" name="type" value="0"/>
	<input type="hidden" id="id" name="id" value="<?php echo $rss->id;?>" />
	<a class="btn-01" href="javascript:void(0);" onclick="rss.doSave();"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>
