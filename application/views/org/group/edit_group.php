<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
		<tbody>
			<tr>
				<td width="120">
					<?php echo lang('group');?>
				</td>
				<td>
					<input type="text" id="name" name="name" style="width:150px;" value="<?php echo $group->name;?>" />
				</td>
				<td>
					<div class="attention" id="errorName" style="display:none;">
						<?php echo lang('warn.group.name');?>
					</div>
				</td>
			</tr>
			<!-- delete 20131230
			<tr>
				<td >
					<?php echo lang('timer.settings');?>
				</td>
				<td>
					<select name="timerConfigId" id="timerConfigId" style="width:150px;">
						<option value="0">&nbsp;</option>
						<?php foreach($timers as $view):?>
						<option value="<?php echo $view->id;?>" <?php if($view->id == $group->timer_config_id):?>selected="selected"<?php endif;?> ><?php echo $view->name;?></option>
						<?php endforeach;?>
					</select>
				</td>
				<td>
					&nbsp;
				</td>
			</tr>			
			<tr>
				<td >
					<?php echo lang('download.strategy');?>
				</td>
				<td>
					<select name="downloadStrategyId" id="downloadStrategyId" style="width:150px;">
						<option value="0">&nbsp;</option>
						<?php foreach($downloads as $view):?>
						<option value="<?php echo $view->id;?>" <?php if($view->id == $group->download_strategy_id):?>selected="selected"<?php endif;?> ><?php echo $view->name;?></option>
						<?php endforeach;?>
					</select>
				</td>
				<td>
					&nbsp;
				</td>
			</tr>			-->
			<?php if(false):?>
			<tr>
				<td >
					<?php echo lang('view.config');?>
				</td>
				<td>
					<select name="viewConfigId" id="viewConfigId" style="width:150px;">
						<option value="0">&nbsp;</option>
						<?php foreach($views as $view):?>
						<option value="<?php echo $view->id;?>" <?php if($view->id == $group->view_config_id):?>selected="selected"<?php endif;?>  ><?php echo $view->name;?></option>
						<?php endforeach;?>
					</select>
				</td>
				<td>
					<div class="attention" id="errorName" style="display:none;">
						<?php echo lang('warn.group.name');?>
					</div>
				</td>
			</tr>
			<?php endif;?>
			<tr>
				<td>
					<?php echo lang("desc");?>
				</td>
				<td>
					<textarea name="descr" id="descr" rows="3" style="width:150px;"><?php echo $group->descr;?></textarea>
				</td>
				<td>&nbsp;</td>
			</tr>
		</tbody>
</table>
<p class="btn-center">
	<input type="hidden" id="id" name="id" value="<?php echo $group->id;?>" />
	<input type="hidden" id="type" name="type" value="<?php echo $group->type;?>" />
 	<a class="btn-01" href="javascript:void(0);" onclick="g.doSave();"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>
