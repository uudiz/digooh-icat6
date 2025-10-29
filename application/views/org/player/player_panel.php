<div class="right-l" style="width:98%;">
	<table width="100%" border="0">
		<tr>
			<td style="vertical-align:top;">
				<h1><?php echo lang('detail.panel'); ?></h1>
				<table border="0" style="margin-top:20px; text-align: left;">
					<tr>
						<?php
						if (TRUE || !$player->player_type) :
						?>
							<td><b><?php echo lang('model'); ?></b></td>
							<td><?php if ($player) {
									echo $player->model;
								} ?></td>
						<?php endif; ?>
						<td>&nbsp;</td>
						<td><b><?php echo lang('version'); ?></b></td>
						<td><?php if ($player) {
								echo $player->version;
							} ?></td>

						<?php
						if (!empty($player->storage)) {
							switch ($player->storage) {
								case 0:
									$stor = 'No DISC';
									break;
								case 1:
									$stor = 'Internal DISC';
									break;
								case 2:
									if (!$player->player_type) {
										$stor = 'SD';
									} else {
										$stor = 'TF Card';
									}
									break;
								case 3:
									$stor = 'USB';
									break;
								default:
									$stor = 'N/A';
							}
						} else {
							if ($player->storage == 0) {
								$stor = 'No DISC';
							} else {
								$stor = '';
							}
						}
						if (!empty($player->space)) {
							$space = explode(",", $player->space);
							$s1 = ($space[0] > 1) ? round($space[0]) : $space[0];
							$s2 = ($space[1] > 1) ? round($space[1]) : $space[1];
						} else {
							$s1 = '';
							$s2 = '';
						}
						if (!empty($player->time_zone)) {
							$str = intval($player->time_zone) < 0 ? 'GMT' . $player->time_zone : 'GMT+' . $player->time_zone;
						} else {
							if ($player->time_zone == 0) {
								$str = 'GMT';
							} else {
								$str = '';
							}
						}

						?>
						<td>&nbsp;</td>
						<td colspan="2">
							<?php
							if ($s1 != '' || $s2 != '') {
							?>
								<b><?php echo lang('storage'); ?></b> &nbsp;&nbsp;<?php echo $stor . ':'; ?>
							<?php
								if ($player->disk_total > 0 && $s2 > 0) :
									if ($s1 >= $player->disk_total) {
										if (round($player->disk_total / $s2 * 100, 2) >= 10) {
											$color = "#000000";
										} else {
											$color = "#ff0000";
										}
										echo '<font color=' . $color . '>' . $player->disk_total . '/' . $s2 . 'MB ( ' . round($player->disk_total / $s2 * 100, 2) . '%  Free )</font>';
									} else {
										if (round($s1 / $s2 * 100, 2) >= 10) {
											$color = "#000000";
										} else {
											$color = "#ff0000";
										}
										echo '<font color=' . $color . '>' . $s1 . '/' . $s2 . 'MB (' . round($s1 / $s2 * 100, 2) . '%  Free )</font>';
									}
								endif;
							}
							?>
						</td>
						<td>&nbsp;</td>
						<td>
							<?php
							if ($str != '') {
							?>
								<b><?php echo lang('time.zone'); ?></b>&nbsp;&nbsp; <?php {
																						echo $str;
																					} ?>
							<?php
							}
							?>
						</td>
						<td>
							<b>Daily Restart</b>&nbsp;&nbsp;<?php echo $daily_restart; ?>
						</td>

						<td><b><?php echo lang('connection_type'); ?></b></td>
						<td><?php if ($player) {

								if ($player->humidity == 2)
									echo lang('network_wifi');
								else if ($player->humidity == 3)
									echo lang('network_3g');
								else if ($player->humidity == 4)
									echo lang('network_4g');
								else
									echo lang('network_lan');
							}
							?>
						</td>
						<td><b><?php echo lang('last.ip'); ?></b></td>
						<td><?php echo $player->last_ip; ?></td>
						<td><b><?php echo lang('screen.type'); ?></b></td>
						<td>
							<?php
							if ($player->screen_oritation == 0)
								echo lang('player.portrait');
							else
								echo lang('player.landscape');

							?>
						</td>
						<td><b><?php echo lang('player_mac'); ?></b></td>
						<td><?php echo $player->mac; ?></td>
					</tr>

				</table>
				<table border="0" style="text-align: left;">

					<tr>
						<?php if ($player->voltage || $player->electric) : ?>
							<td><b><?php echo lang('total_consumption'); ?></b></td>
							<td><?php echo $player->voltage . "kWh"; ?></td>
							<td><b><?php echo lang('present_consumption'); ?></b></td>
							<td><?php echo $player->electric . "watts"; ?></td>
						<?php endif ?>
						<td><b><?php echo lang('brightness'); ?></b></td>
						<td><?php if ($player->brightness) {
								echo $player->brightness . "%";
							} else {
								echo "N/A";
							} ?></td>
						<td colspan="1"><b><?php echo lang('temperature'); ?></b></td>
						<td><?php if ($player->temp) {
								echo $player->temp . "°";
							} else {
								echo "N/A";
							} ?></td>
						<td><b><?php echo lang('humidity'); ?></b></td>
						<td><?php if ($player->dampness) {
								echo $player->dampness . "%";
							} else {
								echo "N/A";
							} ?></td>
						<td><b><?php echo lang('off_times'); ?></b></td>
						<td colspan="2"><?php if ($player->total_off_times) {
											echo $player->total_off_times . "(" . $player->recentDayOffTimes[0] . "," . $player->recentDayOffTimes[1] . "," . $player->recentDayOffTimes[2] . ","
												. $player->recentDayOffTimes[3] . "," . $player->recentDayOffTimes[4] . "," . $player->recentDayOffTimes[5] . "," . $player->recentDayOffTimes[6] . ")";
										} else {
											echo "0";
										} ?></td>
					</tr>


				</table>
			</td>
		</tr>
		<tr>
			<td width="90%">
				<h1><?php echo lang('log.panel'); ?></h1>
				<div id="logPanel" style="width:100%;height:150px;" class="logPanel">
					<?php foreach ($logs as $log) : ?>
						<p><?php echo $log->add_time . ":\t" . $log->detail; ?></p>
					<?php endforeach; ?>
				</div>
			</td>
		</tr>
	</table>
</div>