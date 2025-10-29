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

							<input type="text" id="name" name="name" class="text ui-widget-content ui-corner-all" style="width:200px;" value="<?php echo $config->name;?>"/>

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

							<input onchange="cfg.changeType(this);" type="radio" name="type" id="unity" value="0" <?php if($config->type == 0):?>checked="checked" <?php endif;?> >&nbsp;<?php echo lang('timer.type.unity');?>&nbsp;&nbsp;

							<input onchange="cfg.changeType(this);" type="radio" name="type" id="week" value="1" <?php if($config->type == 1):?>checked="checked" <?php endif;?> >&nbsp;<?php echo lang('timer.type.week');?>

						</td>

					</tr>

					<tr>

						<td>

							<?php echo lang("desc");?>

						</td>

						<td>

							<textarea name="descr" id="descr" class="ui-widget-content ui-corner-all" rows="2" style="width:200px;"><?php echo $config->descr; ?></textarea>

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

									<input type="hidden" id="weekId0<?php echo $k;?>" name="weekId0<?php echo $k;?>" value="<?php echo $config->extra[0][$k-1]->id;?>"/>

								</td>

								<td>

									<select id="status0<?php echo $k;?>" name="status0<?php echo $k;?>" onchange="cfg.changeStatus(this,0,<?php echo $k;?>);" <?php if($config->type == 1):?>disabled="disabled"<?php endif;?>>

										<option value="0" <?php if($config->extra[0][$k-1]->status == 0):?>selected="selected"<?php endif;?>><?php echo lang('timer.status.enable');?></option>

										<option value="1" <?php if($config->extra[0][$k-1]->status == 1):?>selected="selected"<?php endif;?> ><?php echo lang('timer.status.disable');?></option>

									</select>

								</td>

								<td>

									<?php $h = substr($config->extra[0][$k-1]->start_time, 0 ,2);$m=substr($config->extra[0][$k-1]->start_time, -2);?>

									<select id="startHour0<?php echo $k;?>" name="startHour0<?php echo $k;?>" <?php if($config->type == 1 || $config->extra[0][$k-1]->status == 1):?>disabled="true"<?php endif;?>>

										<?php for($i = 0; $i < 24; $i++):?>

										<?php if($i < 10){$hour = '0'.$i;}else{$hour = $i;}?>

										<option value="<?php echo $hour;?>" <?php if($h == $hour):?>selected="selected"<?php endif;?>><?php echo $hour;?></option>

										<?php endfor;?>

									</select>

									:

									<select id="startMinute0<?php echo $k;?>" name="startMinute0<?php echo $k;?>" <?php if($config->type == 1 || $config->extra[0][$k-1]->status == 1):?>disabled="true"<?php endif;?>>

										<?php for($i = 0; $i < 60; $i++):?>

										<?php if($i < 10){$minute = '0'.$i;}else{$minute = $i;}?>

										<option value="<?php echo $minute;?>" <?php if($m == $minute):?>selected="selected"<?php endif;?>><?php echo $minute;?></option>

										<?php endfor;?>

									</select>

								</td>

								<td>

									<?php $h = substr($config->extra[0][$k-1]->end_time, 0 ,2);$m=substr($config->extra[0][$k-1]->end_time, -2);?>

									<select id="shutdownHour0<?php echo $k;?>" name="shutdownHour0<?php echo $k;?>" <?php if($config->type == 1 || $config->extra[0][$k-1]->status == 1):?>disabled="true"<?php endif;?>>

										<?php for($i = 0; $i < 24; $i++):?>

										<?php if($i < 10){$hour = '0'.$i;}else{$hour = $i;}?>

										<option value="<?php echo $hour;?>" <?php if($h == $hour):?>selected="selected"<?php endif;?>><?php echo $hour;?></option>

										<?php endfor;?>

									</select>

									:

									<select id="shutdownMinute0<?php echo $k;?>" name="shutdownMinute0<?php echo $k;?>" <?php if($config->type == 1 ||$config->extra[0][$k-1]->status == 1):?>disabled="true"<?php endif;?> >

										<?php for($i = 0; $i < 60; $i++):?>

										<?php if($i < 10){$minute = '0'.$i;}else{$minute = $i;}?>

										<option value="<?php echo $minute;?>" <?php if($m == $minute):?>selected="selected"<?php endif;?>><?php echo $minute;?></option>

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
							<td colspan="1" style="text-align:left;font-weight:bold;" >
								<input type="checkbox" id="wholedayoff7" name="wholedayoff" <?php if($config->type == 0):?>disabled="disabled"<?php endif;?> onchange="cfg.changeWeekStatus(7,true);" <?php if(in_array("7",$config->offweekdays)) echo 'checked="checked"';?>/>
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

									<input type="hidden" id="weekId7<?php echo $k;?>" name="weekId7<?php echo $k;?>" value="<?php echo $config->extra[7][$k-1]->id;?>"/>

								</td>

								<td>

									<select id="status7<?php echo $k;?>" name="status7<?php echo $k;?>" onchange="cfg.changeStatus(this,7,<?php echo $k;?>);" <?php if($config->type == 0||in_array("7",$config->offweekdays)):?>disabled="disabled"<?php endif;?>>

										<option value="0" <?php if($config->extra[7][$k-1]->status == 0):?>SELECTED<?php endif;?>><?php echo lang('timer.status.enable');?></option>

										<option value="1" <?php if($config->extra[7][$k-1]->status == 1):?>SELECTED<?php endif;?>><?php echo lang('timer.status.disable');?></option>

									</select>

								</td>

								<td>

									<select id="startHour7<?php echo $k;?>" name="startHour7<?php echo $k;?>" <?php if($config->type == 0 || $config->extra[7][$k-1]->status == 1):?>disabled="true"<?php endif;?>>

										<?php for($i = 0; $i < 24; $i++):?>

										<?php if($i < 10){$hour = '0'.$i;}else{$hour = $i;}?>

										<option value="<?php echo $hour;?>" <?php if($hour == $config->extra[7][$k-1]->start_hour):?>SELECTED<?php endif;?>><?php echo $hour;?></option>

										<?php endfor;?>

									</select>

									:

									<select id="startMinute7<?php echo $k;?>" name="startMinute7<?php echo $k;?>" <?php if($config->type == 0 || $config->extra[7][$k-1]->status == 1):?>disabled="true"<?php endif;?>>

										<?php for($i = 0; $i < 60; $i++):?>

										<?php if($i < 10){$minute = '0'.$i;}else{$minute = $i;}?>

										<option value="<?php echo $minute;?>" <?php if($minute == $config->extra[7][$k-1]->start_minute):?>SELECTED<?php endif;?>><?php echo $minute;?></option>

										<?php endfor;?>

									</select>

								</td>

								<td>

									<select id="shutdownHour7<?php echo $k;?>" name="shutdownHour7<?php echo $k;?>" <?php if($config->type == 0 || $config->extra[7][$k-1]->status == 1):?>disabled="true"<?php endif;?>>

										<?php for($i = 0; $i < 24; $i++):?>

										<?php if($i < 10){$hour = '0'.$i;}else{$hour = $i;}?>

										<option value="<?php echo $hour;?>" <?php if($hour == $config->extra[7][$k-1]->end_hour):?>SELECTED<?php endif;?>><?php echo $hour;?></option>

										<?php endfor;?>

									</select>

									:

									<select id="shutdownMinute7<?php echo $k;?>" name="shutdownMinute7<?php echo $k;?>" <?php if($config->type == 0 || $config->extra[7][$k-1]->status == 1):?>disabled="true"<?php endif;?>>

										<?php for($i = 0; $i < 60; $i++):?>

										<?php if($i < 10){$minute = '0'.$i;}else{$minute = $i;}?>

										<option value="<?php echo $minute;?>" <?php if($minute == $config->extra[7][$k-1]->end_minute):?>SELECTED<?php endif;?>><?php echo $minute;?></option>

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
								<input type="checkbox" id="wholedayoff<?php echo $w?>" name="wholedayoff" <?php if($config->type == 0):?>disabled="disabled"<?php endif;?> onchange="cfg.changeWeekStatus(<?php echo $w?>,true);" <?php if(in_array($w,$config->offweekdays)) echo 'checked="checked"';?>/>
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

									<input type="hidden" id="weekId<?php echo $w.$k;?>" name="weekId<?php echo $w.$k;?>" value="<?php echo $config->extra[$w][$k-1]->id;?>"/>

								</td>

								<td>

									<select id="status<?php echo $w.$k;?>" name="status<?php echo $w.$k;?>" onchange="cfg.changeStatus(this,<?php echo $w;?>,<?php echo $k;?>);" <?php if($config->type == 0||in_array($w,$config->offweekdays)):?>disabled="disabled"<?php endif;?>>

										<option value="0" <?php if($config->extra[$w][$k-1]->status == 0):?>SELECTED<?php endif;?>><?php echo lang('timer.status.enable');?></option>

										<option value="1" <?php if($config->extra[$w][$k-1]->status == 1):?>SELECTED<?php endif;?>><?php echo lang('timer.status.disable');?></option>

									</select>

								</td>

								<td>

									<select id="startHour<?php echo $w.$k;?>" name="startHour<?php echo $w.$k;?>" <?php if($config->type == 0 || $config->extra[$w][$k-1]->status == 1):?>disabled="true"<?php endif;?>>

										<?php for($i = 0; $i < 24; $i++):?>

										<?php if($i < 10){$hour = '0'.$i;}else{$hour = $i;}?>

										<option value="<?php echo $hour;?>" <?php if($config->extra[$w][$k-1]->start_hour == $hour):?>SELECTED<?php endif;?>><?php echo $hour;?></option>

										<?php endfor;?>

									</select>

									:

									<select id="startMinute<?php echo $w.$k;?>" name="startMinute<?php echo $w.$k;?>" <?php if($config->type == 0 || $config->extra[$w][$k-1]->status == 1):?>disabled="true"<?php endif;?>>

										<?php for($i = 0; $i < 60; $i++):?>

										<?php if($i < 10){$minute = '0'.$i;}else{$minute = $i;}?>

										<option value="<?php echo $minute;?>" <?php if($config->extra[$w][$k-1]->start_minute == $minute):?>SELECTED<?php endif;?>><?php echo $minute;?></option>

										<?php endfor;?>

									</select>

								</td>

								<td>

									<select id="shutdownHour<?php echo $w.$k;?>" name="shutdownHour<?php echo $w.$k;?>" <?php if($config->type == 0 || $config->extra[$w][$k-1]->status == 1):?>disabled="true"<?php endif;?>>

										<?php for($i = 0; $i < 24; $i++):?>

										<?php if($i < 10){$hour = '0'.$i;}else{$hour = $i;}?>

										<option value="<?php echo $hour;?>" <?php if($config->extra[$w][$k-1]->end_hour == $hour):?>SELECTED<?php endif;?>><?php echo $hour;?></option>

										<?php endfor;?>

									</select>

									:

									<select id="shutdownMinute<?php echo $w.$k;?>" name="shutdownMinute<?php echo $w.$k;?>" <?php if($config->type == 0 || $config->extra[$w][$k-1]->status == 1):?>disabled="true"<?php endif;?>>

										<?php for($i = 0; $i < 60; $i++):?>

										<?php if($i < 10){$minute = '0'.$i;}else{$minute = $i;}?>

										<option value="<?php echo $minute;?>" <?php if($config->extra[$w][$k-1]->end_minute == $minute):?>SELECTED<?php endif;?>><?php echo $minute;?></option>

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

	<input type="hidden" id="id" name="id" value="<?php echo $config->id?>" />

 	<a class="btn-01" href="javascript:void(0);" onclick="cfg.saveTimer();"><span><?php echo lang('button.save');?></span></a>

	<a class="btn-01" href="javascript:void(0);" onclick="cfg.goTimerList();"><span><?php echo lang('button.back');?></span></a>	

</p>