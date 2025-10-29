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
					<input type="text" id="name" name="name" class="text ui-widget-content ui-corner-all" style="width:200px;" value="<?php echo $template->name;?>"/>
				</td>
				<td>
					<div class="attention" id="errorName" style="display:none;">
						<?php echo lang('warn.template.name');?>
					</div>
				</td>
			</tr>
			<?php if(false):?>
			<tr>
				<td>
					<?php echo lang('screen.type'); ?>
				</td>
				<td>
					<select id="screen" name="screen" class="text ui-widget-content ui-corner-all" style="width:200px;">
		                <?php foreach ($screens as $s): ?>
		                <option value="<?php echo $s->width.'X'.$s->height;?>" <?php if($s->width == $template->width && $s->height == $template->height){echo 'selected="selected"';}?>><?php echo $s->width.'X'.$s->height; ?></option>
		                <?php endforeach; ?>
		            </select>
				</td>
			</tr>
			<?php endif;?>
			<tr>
				<td>
					<?php echo lang("desc");?>
				</td>
				<td>
					<textarea name="descr" id="descr" class="ui-widget-content ui-corner-all" rows="2" style="width:200px;"><?php echo $template->descr;?></textarea>
				</td>
				<td>&nbsp;</td>
			</tr>
		</tbody>
</table>
</form>
<p class="btn-center">
	<input type="hidden" name="type" id="type" value="<?php echo $template->system;?>"/>
	<input type="hidden" name="id" id="id" value="<?php echo $template->id;?>"/>
	<a class="btn-01" href="javascript:void(0);" onclick="template.index.doSave();"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>
