<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tbody>
		<tr>
			<td class="tdBox" width="33%">
				<table class="from-panel" width="100%" cellspacing="0" cellpadding="0" border="0" >
					<tr>
						<td width="60">
							<?php echo lang('name'); ?>
						</td>
						<td>
							<input type="text" id="name" name="name" class="text ui-widget-content ui-corner-all" style="width:200px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:left;">
							<div class="error" id="errorName" style="display:none;">
								<?php echo lang('warn.timer.name');?>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang("timer.type");?>
						</td>
						<td>
							<input onchange="cfg.changeType(this);" type="radio" name="type" id="unity" value="0" checked="checked">&nbsp;<?php echo lang('timer.type.unity');?>&nbsp;&nbsp;
							<input onchange="cfg.changeType(this);" type="radio" name="type" id="week" value="1" >&nbsp;<?php echo lang('timer.type.week');?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang("desc");?>
						</td>
						<td>
							<textarea name="descr" id="descr" class="ui-widget-content ui-corner-all" rows="2" style="width:200px;"></textarea>
						</td>
					</tr>
				</table>
			</td>
			<td width="1px">&nbsp;</td>
			<td class="tdBox" width="33%">
				<table class="table-list" cellspacing="0" cellpadding="0" border="0" width="100%" height="100%">
					<tbody>
						<tr>
							<td colspan="4" style="text-align:left;font-size:16px;font-weight:bold;"><?php echo lang('timer.type.unity');?></td>
						</tr>
						<tr>
							<th width="20" ><?php echo lang('id');?></th>
							<th width="80" ><?php echo lang('timer.status');?></th>
				            <th width="120"><?php echo lang('startup.time');?></th>
							<th width="120"><?php echo lang('shutdown.time');?></th>
						</tr>
						<?php for($k=1; $k <= 3; $k++): ?>
							<tr <?php if($k % 2 == 0):?>class="even"<?php endif;?>>
								<td>
									<?php echo $k;?>
									<input type="hidden" id="weekId0<?php echo $k;?>" name="weekId0<?php echo $k;?>" value="0"/>
								</td>
								<td>
									<select id="status0<?php echo $k;?>" name="status0<?php echo $k;?>" onchange="cfg.changeStatus(this,0,<?php echo $k;?>);">
										<option value="0"><?php echo lang('timer.status.enable');?></option>
										<option value="1" selected="selected"><?php echo lang('timer.status.disable');?></option>
									</select>
								</td>
								<td>
									<select id="startHour0<?php echo $k;?>" name="startHour0<?php echo $k;?>" disabled="true">
										<?php for($i = 0; $i < 24; $i++):?>
										<?php if($i < 10){$hour = '0'.$i;}else{$hour = $i;}?>
										<option value="<?php echo $hour;?>"><?php echo $hour;?></option>
										<?php endfor;?>
									</select>
									:
									<select id="startMinute0<?php echo $k;?>" name="startMinute0<?php echo $k;?>" disabled="true">
										<?php for($i = 0; $i < 60; $i++):?>
										<?php if($i < 10){$minute = '0'.$i;}else{$minute = $i;}?>
										<option value="<?php echo $minute;?>"><?php echo $minute;?></option>
										<?php endfor;?>
									</select>
								</td>
								<td>
									<select id="shutdownHour0<?php echo $k;?>" name="shutdownHour0<?php echo $k;?>" disabled="true">
										<?php for($i = 0; $i < 24; $i++):?>
										<?php if($i < 10){$hour = '0'.$i;}else{$hour = $i;}?>
										<option value="<?php echo $hour;?>"><?php echo $hour;?></option>
										<?php endfor;?>
									</select>
									:
									<select id="shutdownMinute0<?php echo $k;?>" name="shutdownMinute0<?php echo $k;?>" disabled="true">
										<?php for($i = 0; $i < 60; $i++):?>
										<?php if($i < 10){$minute = '0'.$i;}else{$minute = $i;}?>
										<option value="<?php echo $minute;?>"><?php echo $minute;?></option>
										<?php endfor;?>
									</select>
								</td>
							</tr>
						<?php endfor;?>
					</tbody>
				</table>
			</td>
			<td width="1px">&nbsp;</td>
			<td class="tdBox" width="33%">
				<table class="table-list" cellspacing="0" cellpadding="0" border="0" width="100%" height="100%">
					<tbody>
						<tr>
							<td colspan="2" style="text-align:left;font-size:16px;font-weight:bold;">
								<?php echo lang('timer.type.week.7');?>
								
							</td>
							<td colspan="1" style="text-align:left;font-weight:bold;">
								
								<input type="checkbox" id="wholedayoff7" name="wholedayoff" disabled="disabled" onchange="cfg.changeWeekStatus(7,true);"/>
				
								<?php echo lang('timer.whole.day.off');?>
							</td>
						</tr>
						<tr>
							<th width="20" ><?php echo lang('id');?></th>
							<th width="80" ><?php echo lang('timer.status');?></th>
				            <th width="120"><?php echo lang('startup.time');?></th>
							<th width="120"><?php echo lang('shutdown.time');?></th>
						</tr>
						<?php for($k=1; $k <= 3; $k++): ?>
							<tr <?php if($k % 2 == 0):?>class="even"<?php endif;?>>
								<td>
									<?php echo $k;?>
									<input type="hidden" id="weekId7<?php echo $k;?>" name="weekId7<?php echo $k;?>" value="0"/>
								</td>
								<td>
									<select id="status7<?php echo $k;?>" name="status7<?php echo $k;?>" disabled="disabled" onchange="cfg.changeStatus(this,7,<?php echo $k;?>);">
										<option value="0"><?php echo lang('timer.status.enable');?></option>
										<option value="1" selected="selected"><?php echo lang('timer.status.disable');?></option>
									</select>
								</td>
								<td>
									<select id="startHour7<?php echo $k;?>" name="startHour7<?php echo $k;?>" disabled="disabled">
										<?php for($i = 0; $i < 24; $i++):?>
										<?php if($i < 10){$hour = '0'.$i;}else{$hour = $i;}?>
										<option value="<?php echo $hour;?>"><?php echo $hour;?></option>
										<?php endfor;?>
									</select>
									:
									<select id="startMinute7<?php echo $k;?>" name="startMinute7<?php echo $k;?>" disabled="true">
										<?php for($i = 0; $i < 60; $i++):?>
										<?php if($i < 10){$minute = '0'.$i;}else{$minute = $i;}?>
										<option value="<?php echo $minute;?>"><?php echo $minute;?></option>
										<?php endfor;?>
									</select>
								</td>
								<td>
									<select id="shutdownHour7<?php echo $k;?>" name="shutdownHour7<?php echo $k;?>" disabled="true">
										<?php for($i = 0; $i < 24; $i++):?>
										<?php if($i < 10){$hour = '0'.$i;}else{$hour = $i;}?>
										<option value="<?php echo $hour;?>"><?php echo $hour;?></option>
										<?php endfor;?>
									</select>
									:
									<select id="shutdownMinute7<?php echo $k;?>" name="shutdownMinute7<?php echo $k;?>" disabled="true">
										<?php for($i = 0; $i < 60; $i++):?>
										<?php if($i < 10){$minute = '0'.$i;}else{$minute = $i;}?>
										<option value="<?php echo $minute;?>"><?php echo $minute;?></option>
										<?php endfor;?>
									</select>
								</td>
							</tr>
						<?php endfor;?>
					</tbody>
				</table>
			</td>
			<td width="1px">&nbsp;</td>
		</tr>
		<?php for($line = 0; $line < 2; $line++):?>
		<tr>
			<td colspan="5" height="10px">&nbsp;</td>
		</tr>
		<tr>
			<?php for($w = ($line*3)+1; $w <= ($line + 1)*3; $w++):?>
			<td class="tdBox">
				<table class="table-list" cellspacing="0" cellpadding="0" border="0" width="100%" height="100%">
					<tbody>
						<tr>
							<td colspan="2" style="text-align:left;font-size:16px;font-weight:bold;">
								<?php echo lang('timer.type.week.'.$w);?>
							
							</td>
							<td colspan="1" style="text-align:left;font-weight:bold;" >
								<input type="checkbox" id="wholedayoff<?php echo $w?>" name="wholedayoff" disabled="disabled" onchange="cfg.changeWeekStatus(<?php echo $w?>,true);"/>
								<?php echo lang('timer.whole.day.off');?>
							</td>
							
						</tr>
						<tr>
							<th width="20" ><?php echo lang('id');?></th>
							<th width="80" ><?php echo lang('timer.status');?></th>
				            <th width="120"><?php echo lang('startup.time');?></th>
							<th width="120"><?php echo lang('shutdown.time');?></th>
						</tr>
						<?php for($k=1; $k <= 3; $k++): ?>
							<tr <?php if($k % 2 == 0):?>class="even"<?php endif;?>>
								<td>
									<?php echo $k;?>
									<input type="hidden" id="weekId<?php echo $w.$k;?>" name="weekId<?php echo $w.$k;?>" value="0" />
								</td>
								<td>
									<select id="status<?php echo $w.$k;?>" name="status<?php echo $w.$k;?>" disabled="true" onchange="cfg.changeStatus(this,<?php echo $w;?>,<?php echo $k;?>);">
										<option value="0"><?php echo lang('timer.status.enable');?></option>
										<option value="1" selected="selected"><?php echo lang('timer.status.disable');?></option>
									</select>
								</td>
								<td>
									<select id="startHour<?php echo $w.$k;?>" name="startHour<?php echo $w.$k;?>" disabled="true">
										<?php for($i = 0; $i < 24; $i++):?>
										<?php if($i < 10){$hour = '0'.$i;}else{$hour = $i;}?>
										<option value="<?php echo $hour;?>"><?php echo $hour;?></option>
										<?php endfor;?>
									</select>
									:
									<select id="startMinute<?php echo $w.$k;?>" name="startMinute<?php echo $w.$k;?>" disabled="true">
										<?php for($i = 0; $i < 60; $i++):?>
										<?php if($i < 10){$minute = '0'.$i;}else{$minute = $i;}?>
										<option value="<?php echo $minute;?>"><?php echo $minute;?></option>
										<?php endfor;?>
									</select>
								</td>
								<td>
									<select id="shutdownHour<?php echo $w.$k;?>" name="shutdownHour<?php echo $w.$k;?>" disabled="true">
										<?php for($i = 0; $i < 24; $i++):?>
										<?php if($i < 10){$hour = '0'.$i;}else{$hour = $i;}?>
										<option value="<?php echo $hour;?>"><?php echo $hour;?></option>
										<?php endfor;?>
									</select>
									:
									<select id="shutdownMinute<?php echo $w.$k;?>" name="shutdownMinute<?php echo $w.$k;?>" disabled="true">
										<?php for($i = 0; $i < 60; $i++):?>
										<?php if($i < 10){$minute = '0'.$i;}else{$minute = $i;}?>
										<option value="<?php echo $minute;?>"><?php echo $minute;?></option>
										<?php endfor;?>
									</select>
								</td>
							</tr>
						<?php endfor;?>
					</tbody>
				</table>
			</td>
			<td >&nbsp;</td>
			<?php endfor;?>
		</tr>
		<?php endfor;?>
	</tbody>
</table>
<p class="btn-center">
	<input type="hidden" id="id" name="id" value="0" />
 	<a class="btn-01" href="javascript:void(0);" onclick="cfg.saveTimer();"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="cfg.goTimerList();"><span><?php echo lang('button.back');?></span></a>	
</p>
