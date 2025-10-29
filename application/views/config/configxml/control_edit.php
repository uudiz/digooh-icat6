<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<?php foreach($config as $row):?>
	<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
		<tbody>
			<tr>
				<td width="130">
					<?php echo lang('name'); ?>
				</td>
				<td>
					<input type="text" id="name" name="name" value="<?php echo $row->name;?>">
				</td>
				<td>
					<div class="attention" id="errorName" style="display:none;">
						<?php echo lang('device.warn');?>
					</div>
				</td>
			</tr>
			<?php
			if($this->config->item('mia_system_set') == $this->config->item('mia_system_all')):
			?>
			<tr>
				<td width="120">
					<?php echo lang('type');?>
				</td>
				<td>
					<select name="player_type" id="player_type" style="width: 80px;">
						<option value="0" <?php if($row->player_type == 0):?>selected="selected"<?php endif;?>><?php echo lang('type.0');?></option>
						<option value="1" <?php if($row->player_type == 1):?>selected="selected"<?php endif;?>><?php echo lang('type.1');?></option>
					</select>
				</td>
			</tr>
			<?php
			elseif($this->config->item('mia_system_set') == $this->config->item('mia_system_np100')):
			?>
				<input type="hidden" id="player_type" name="player_type" value="0"/>
			<?php
			elseif($this->config->item('mia_system_set') == $this->config->item('mia_system_np200')):
			?>
				<input type="hidden" id="player_type" name="player_type" value="1"/>
			<?php
			endif;
			?>
			<tr>
				<td >
					<?php echo lang("desc");?>
				</td>
				<td>
				<textarea name="descr" id="descr" rows= "4"><?php echo $row->descr;?></textarea>
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
			<tr id="np_dateformat" <?php if($row->player_type == 1):?>style="display:none;"<?php endif;?>>
				<td >
					<?php echo lang("dateformat");?>
				</td>
				<td>
					<select id="dateformat" name="dateformat">
						<?php for($i = 0; $i < count($dateformat); $i++):?>
						<option value="<?php echo $i;?>" <?php if($row->dateformat == $i):?>selected="selected"<?php endif;?>><?php echo $dateformat[$i];?></option>
						<?php endfor;?>
					</select>
				</td>
			</tr>
			<tr id="np_timeformat" <?php if($row->player_type == 1):?>style="display:none;"<?php endif;?>>
				<td >
					<?php echo lang("timeformat");?>
				</td>
				<td>
					<select id="timeformat" name="timeformat">
						<?php for($i = 0; $i < 2; $i++):?>
						<?php if($i == 0){$m = '12H';}else{$m = '24H';}?>
						<option value="<?php echo $i;?>" <?php if($row->timeformat == $i):?>selected="selected"<?php endif;?>><?php echo $m;?></option>
						<?php endfor;?>
					</select>
				</td>
			</tr>
			
			<tr>
				<td>
					<?php echo lang("timezone");?>
				</td>
				<td>
					<select id="timezone" name="timezone">
						<?php foreach($zones as $key => $value):?>
						<option value="<?php echo $value;?>" <?php if($row->timezone == $value):?>selected="selected"<?php endif;?>><?php echo $key;?></option>
						<?php endforeach;?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang("synctime");?>
				</td>
				<td>
					<select id="synctime" name="synctime">
						<?php for($i = 0; $i < 2; $i++):?>
						<?php if($i == 0){$m = 'OFF';}else{$m = 'Sync When Power Up';}?>
						<option value="<?php echo $i;?>" <?php if($row->synctime == $i):?>selected="selected"<?php endif;?>><?php echo $m;?></option>
						<?php endfor;?>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php echo lang("daily.restart.setup");?></td>
				<td>
					<input type="checkbox" id="drflag" name="drflag" class="dst" <?php if($row->dailyRestartFlag==1){echo 'checked="checked"';}?>  />
				</td>
			</tr>
			<tr id="daily_restart" <?php if($row->dailyRestartFlag==2){ echo 'style="display: none;"';}?>>
				<td><?php echo lang("daily.restart.time");?></td>
				<td>
					<?php
					  	$stmp = explode(':', $row->dailyRestartTime);
					  	$sh = $stmp[0];
					  	$sm = $stmp[1];
					 ?>
					<select name="daily_restart_time_h" id="daily_restart_time_h" >
						<?php for($i = 0; $i < 24; $i++):?>
						<?php if($i < 10){$hour = '0'.$i;}else{$hour = $i;}?>
						<option value="<?php echo $hour;?>" <?php if($sh == $hour):?>selected="selected"<?php endif;?>><?php echo $hour;?></option>
						<?php endfor;?>
					</select>
					:
					<select name="daily_restart_time_m" id="daily_restart_time_m" >
						<?php for($i = 0; $i < 60; $i++):?>
						<?php if($i < 10){$minute = '0'.$i;}else{$minute = $i;}?>
						<option value="<?php echo $minute;?>" <?php if($sm == $minute):?>selected="selected"<?php endif;?>><?php echo $minute;?></option>
						<?php endfor;?>
					</select>&nbsp;&nbsp;&nbsp;(HH:MM)
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang("clockpos");?>
				</td>
				<td>
					<select id="clockpos" name="clockpos">
						<?php for($i = 0; $i < count($clockpos); $i++):?>
						<option value="<?php echo $i;?>" <?php if($row->clockpos == $i):?>selected="selected"<?php endif;?>><?php echo $clockpos[$i];?></option>
						<?php endfor;?>
					</select>
				</td>
			</tr>
			<tr id="np100_storagepri" <?php if($row->player_type == 1):?>style="display:none;"<?php endif;?>>
				<td>
					<?php echo lang("storagepri");?>
				</td>
				<td>
					<select id="storagepri" name="storagepri">
						<?php foreach($storagepri as $key => $value):?>
						<option value="<?php echo $key;?>" <?php if($row->storagepri == $key):?>selected="selected"<?php endif;?>><?php echo $value;?></option>
						<?php endforeach;?>
					</select>
				</td>
			</tr>
			<tr id="np200_storagepri" <?php if($row->player_type == 0):?>style="display:none;"<?php endif;?>>
				<td>
					<?php echo lang("storagepri");?>
				</td>
				<td>
					<select id="storagepri2" name="storagepri2">
						<option value="-1" <?php if($row->storagepri == -1):?>selected="selected"<?php endif;?>>No change</option>
						<option value="1" <?php if($row->storagepri == 1):?>selected="selected"<?php endif;?>>Internal Disk</option>
						<option value="2" <?php if($row->storagepri == 2):?>selected="selected"<?php endif;?>>TF Card</option>
						<option value="3" <?php if($row->storagepri == 3):?>selected="selected"<?php endif;?>>USB</option>
					</select>
				</td>
			</tr>
			<tr id="np200sync_playback" <?php if($row->player_type == 0):?>style="display:none;"<?php endif;?>>
				<td>
					<?php echo lang("sync.playback");?>
				</td>
				<td>
					<select id="sync_playback" name="sync_playback">
						<option value="0" <?php if($row->sync_playback == 0):?>selected="selected"<?php endif;?>><?php echo lang("sync.0");?></option>
						<option value="1" <?php if($row->sync_playback == 1):?>selected="selected"<?php endif;?>><?php echo lang("sync.1");?></option>
						<option value="2" <?php if($row->sync_playback == 2):?>selected="selected"<?php endif;?>><?php echo lang("sync.2");?></option>
					</select>
				</td>
			</tr>
			<tr id="np200_orientation" <?php if($row->player_type != 1):?>style="display:none;"<?php endif;?>>
				<td><?php echo lang("device.orientation");?></td>
				<td>
					<select id="orientation" name="orientation" style="width: 100px;">
						<?php foreach($orientation_list as $k=>$v):?>
							<option value="<?php echo $k;?>" <?php if($k==$row->orientation):?>selected<?php endif;?> ><?php echo $v;?></option>
						<?php endforeach;?>
					</select>
				</td>
			</tr>
			<tr id="np100_hdmi" <?php if($row->player_type == 1):?>style="display:none;"<?php endif;?>>
				<td>HDMI</td>
				<td>
					<select id="hdmi" name="hdmi">
						<option value="-1" <?php if($row->videomode == -1):?>selected="selected"<?php endif;?>>No change</option>
						<option value="0" <?php if($row->videomode == 0):?>selected="selected"<?php endif;?>><?php echo lang("hdmi.vga");?></option>
						<option value="1" <?php if($row->videomode == 1):?>selected="selected"<?php endif;?>><?php echo lang("hdmi.50");?></option>
						<option value="2" <?php if($row->videomode == 2):?>selected="selected"<?php endif;?>><?php echo lang("hdmi.60");?></option>
					</select>
				</td>
			</tr>
			
			<tr id="np200_hdmi" <?php if($row->player_type != 1):?>style="display:none;"<?php endif;?>>
				<td>HDMI</td>
				<td>
					<select id="hdmi2" name="hdmi2">
						<option value="-1" <?php if($row->videomode == -1):?>selected="selected"<?php endif;?>>No change</option>
						<option value="1" <?php if($row->videomode == 1):?>selected="selected"<?php endif;?>><?php echo lang("hdmi.50");?></option>
						<option value="2" <?php if($row->videomode == 2):?>selected="selected"<?php endif;?>><?php echo lang("hdmi.60");?></option>
					</select>
				</td>
			</tr>
			<?php if($device_setup=='on'):?>
			<tr>
				<td><?php echo lang("player.id");?></td>
				<td>
					<input type="text" id="sn" name="sn" style="width:150px;" value="<?php echo $row->sn;?>" />&nbsp;&nbsp;<?php echo lang("eg.id");?>
				</td>
			</tr>
			<tr>
				<td><?php echo lang("server.connection.mode");?></td>
				<td>
					<select id="connectionMode" name="connectionMode">
						<option value="0" <?php if($row->connectionMode == 0):?>selected="selected"<?php endif;?>><?php echo lang("ip.mode");?></option>
						<option value="1" <?php if($row->connectionMode == 1):?>selected="selected"<?php endif;?>><?php echo lang("domain.mode");?></option>
					</select>
				</td>
			</tr>
			<tr <?php if($row->connectionMode == 1):?>style="display:none;"<?php endif;?> id="server_ip">
				<td><?php echo lang("server.ip");?></td>
				<td><input type="text" id="ip" name="ip" style="width:150px;" value="<?php echo $row->ip; ?>" />&nbsp;&nbsp;<?php echo lang("eg.ip");?></td>
			</tr>
			<tr <?php if($row->connectionMode == 0):?>style="display:none;"<?php endif;?> id="server_domain">
				<td><?php echo lang("domain");?></td>
				<td><input type="text" id="domain" name="domain" style="width:150px;" value="<?php echo $row->domain; ?>" />&nbsp;&nbsp;<?php echo lang("eg.domain");?></td>
			</tr>
			<tr>
				<td><?php echo lang("port");?></td>
				<td><input type="text" id="port" name="port" style="width:80px;" value="<?php if($row->port!=0) {echo $row->port;}?>"/>&nbsp;&nbsp;<?php echo lang("eg.port");?></td>
			</tr>
			
			<?php
			endif;
			?>
		</tbody>
</table>
<p class="btn-center">
	<input type="hidden" name="save_type" id="save_type" value=0 />
	<input type="hidden" name="id" id="id" value="<?php echo $row->id;?>"/>
 	<a class="btn-01" href="javascript:void(0);" onclick="configxml.dosave('control');"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>
<?php endforeach; ?>
<script type="text/javascript">
	$(function() {
		$('#drflag').click(function() {
			$('input:checkbox[id="drflag"]').each(function(){
		    	if (this.checked) {
		        	$("#daily_restart").show();
		   		}else {
		   			$("#daily_restart").hide();
		   		}
		    });	
		});
		$("#connectionMode").change(function(){
			var checkValue=$("#connectionMode").val();
			if(checkValue == 1) {
				$("#server_domain").show();
				$("#server_ip").hide();
			}
			if(checkValue == 0) {
				$("#server_ip").show();
				$("#server_domain").hide();
			}
		});
		$("#player_type").change(function(){
			var checkValue=$("#player_type").val();
			if(checkValue == 1) {
				$("#np100_storagepri").hide();
				$("#np200_storagepri").show();
				$("#np200sync_playback").show();
				$("#np200_orientation").show();
				$("#np100_hdmi").hide();
				$("#np200_hdmi").show();
				$("#np_dateformat").hide();
				$("#np_timeformat").hide();
			}
			if(checkValue == 0) {
				$("#np100_storagepri").show();
				$("#np200_storagepri").hide();
				$("#np200sync_playback").hide();
				$("#np200_orientation").hide();
				$("#np100_hdmi").show();
				$("#np200_hdmi").hide();
				$("#np_dateformat").show();
				$("#np_timeformat").show();
			}
		});
	});
</script>