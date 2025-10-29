<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
		<tbody>
			<tr>
				<td width="100">
					<?php echo lang('name'); ?>
				</td>
				<td>
					<input type="text" id="name" name="name" class="text ui-widget-content ui-corner-all" style="width:200px;" value="<?php echo $config->name;?>"/>
				</td>
				<td>
					<div class="error" id="errorName" style="display:none;">
						<?php echo lang('warn.download.name');?>
					</div>
				</td>
			</tr>
			<tr>
				<td style="vertical-align:middle;"><?php echo lang('download.time'); ?></td>
				<td>
					<table id="downloadTimeConfig" cellspacing="0" cellpadding="0" >
						<tr>
							<td><?php echo lang('start.time');?></td>
							<td></td>
							<td><?php echo lang('end.time');?></td>
							<td ></td>
							<td>
							</td>
							<td>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td>
								<select name="startHour">
									<?php for($i = 0; $i < 24; $i++):?>
									<?php if($i < 10){$hour = '0'.$i;}else{$hour = $i;}?>
									<option value="<?php echo $hour;?>" <?php if(substr($config->start_time,0,2) == $hour):?>selected="selected"<?php endif;?>><?php echo $hour;?></option>
									<?php endfor;?>
								</select>
								:
								<select name="startMinute">
									<?php for($i = 0; $i < 60; $i++):?>
									<?php if($i < 10){$minute = '0'.$i;}else{$minute = $i;}?>
									<option value="<?php echo $minute;?>" <?php if(substr($config->start_time,-2) == $minute):?>selected="selected"<?php endif;?>><?php echo $minute;?></option>
									<?php endfor;?>
								</select>
							</td>
							<td></td>
							<td>
								<select name="endHour">
									<?php for($i = 0; $i < 24; $i++):?>
									<?php if($i < 10){$hour = '0'.$i;}else{$hour = $i;}?>
									<option value="<?php echo $hour;?>" <?php if(substr($config->end_time,0,2) == $hour):?>selected="selected"<?php endif;?>><?php echo $hour;?></option>
									<?php endfor;?>
								</select>
								:
								<select name="endMinute">
									<?php for($i = 0; $i < 60; $i++):?>
									<?php if($i < 10){$minute = '0'.$i;}else{$minute = $i;}?>
									<option value="<?php echo $minute;?>" <?php if(substr($config->end_time,-2) == $minute):?>selected="selected"<?php endif;?> ><?php echo $minute;?></option>
									<?php endfor;?>
								</select>
							</td>
							<td></td>
							<td>
								<a href="javascript:void(0);" onclick="cfg.addDownloadRow();">
									<img src="/images/icons/btn_add.png" alt="" >
								</a>
							</td>
							<td >
								<div id="rangError" class="error" style="display:none;">
									<?php echo lang('warn.download.time.rang');?>
								</div>
								<div id="conflictError" class="error" style="display:none;">
									<?php echo lang('warn.download.time.conflict');?>
								</div>
							</td>
						</tr>
						<?php if($config->extra):?>
						<?php foreach($config->extra as $extra):?>
						<tr>
							<td>
								<select name="startHour">
									<?php for($i = 0; $i < 24; $i++):?>
									<?php if($i < 10){$hour = '0'.$i;}else{$hour = $i;}?>
									<option value="<?php echo $hour;?>" <?php if(substr($extra->start_time,0,2) == $hour):?>selected="selected"<?php endif;?>><?php echo $hour;?></option>
									<?php endfor;?>
								</select>
								:
								<select name="startMinute">
									<?php for($i = 0; $i < 60; $i++):?>
									<?php if($i < 10){$minute = '0'.$i;}else{$minute = $i;}?>
									<option value="<?php echo $minute;?>" <?php if(substr($extra->start_time,-2) == $minute):?>selected="selected"<?php endif;?>><?php echo $minute;?></option>
									<?php endfor;?>
								</select>
							</td>
							<td></td>
							<td>
								<select name="endHour">
									<?php for($i = 0; $i < 24; $i++):?>
									<?php if($i < 10){$hour = '0'.$i;}else{$hour = $i;}?>
									<option value="<?php echo $hour;?>" <?php if(substr($extra->end_time,0,2) == $hour):?>selected="selected"<?php endif;?>><?php echo $hour;?></option>
									<?php endfor;?>
								</select>
								:
								<select name="endMinute">
									<?php for($i = 0; $i < 60; $i++):?>
									<?php if($i < 10){$minute = '0'.$i;}else{$minute = $i;}?>
									<option value="<?php echo $minute;?>" <?php if(substr($extra->end_time,-2) == $minute):?>selected="selected"<?php endif;?> ><?php echo $minute;?></option>
									<?php endfor;?>
								</select>
							</td>
							<td></td>
							<td>
								<a href="javascript:void(0);" onclick="cfg.removeDownloadRow(this);">
									<img src="/images/icons/btn_remove.png" alt="" >
								</a>
							</td>
							<td >
								
							</td>
						</tr>	
						<?php endforeach;?>
						<?php endif;?>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang("desc");?>
				</td>
				<td>
					<textarea name="descr" id="descr" class="ui-widget-content ui-corner-all" rows="2" style="width:200px;"><?php echo $config->descr;?></textarea>
				</td>
				<td>&nbsp;</td>
			</tr>
		</tbody>
</table>
<p class="btn-center">
	<input type="hidden" id="id" name="id" value="<?php echo $config->id;?>" />
 	<a class="btn-01" href="javascript:void(0);" onclick="cfg.saveDownload();"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>
