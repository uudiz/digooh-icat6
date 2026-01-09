<div class="card">
	<div class="card-body">
		<dl class="row  shadow-sm">
			<div class="col-12 row">

				<div class="col-auto">
					<div class="text-truncate">
						<strong><?php echo lang('model'); ?> </strong> <?php echo $player->model; ?>
					</div>
				</div>
				<div class="col-auto">
					<div class="text-truncate">
						<strong><?php echo lang('daily.restart'); ?> </strong> <?php echo $daily_restart; ?>
					</div>

				</div>
				<div class="col-auto">
					<div class="text-truncate">
						<strong><?php echo lang('version'); ?> </strong> <?php echo $player->version; ?>
					</div>
				</div>

				<div class="col-auto">
					<div class="text-truncate">
						<strong>Firmware </strong> <?php echo $player->firmver; ?>
					</div>
				</div>

				<div class="col-auto">
					<div class="text-truncate">
						<strong><?php echo lang('storage'); ?> </strong> <?php echo $player->storage_info; ?>
					</div>
				</div>

				<div class="col-auto">
					<div class="text-truncate">
						<strong><?php echo lang('time.zone'); ?> </strong> <?php if ($player->time_zone == 0) {
																				echo 'GMT';
																			} else {
																				echo $player->time_zone <= 0 ? 'GMT' . $player->time_zone : 'GMT+' . $player->time_zone;
																			} ?>
					</div>
				</div>

				<div class="col-auto">
					<div class="text-truncate">
						<strong><?php echo lang('connection_type'); ?> </strong>
						<?php if ($player) {

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
					</div>
				</div>

				<div class="col-auto">
					<div class="text-truncate">
						<strong><?php echo lang('last.ip'); ?> </strong> <?php echo $player->last_ip; ?>
					</div>
				</div>

				<div class="col-auto">
					<div class="text-truncate">
						<strong><?php echo lang('screen.type'); ?> </strong>
						<?php
						if ($player->screen_oritation == 0)
							echo lang('player.portrait');
						else
							echo lang('player.landscape');

						?>
					</div>
				</div>
			</div>
			<div class="col-12 row">
				<div class="col-auto">
					<div class="text-truncate">
						<strong><?php echo lang('player_mac'); ?> </strong> <?php echo $player->mac ? $player->mac : "N/A"; ?>
					</div>
				</div>


				<div class="col-auto">
					<div class="text-truncate">
						<strong><?php echo lang('total_consumption'); ?> </strong> <?php echo $player->voltage ? $player->voltage . " kWh" : "N/A"; ?>
					</div>
				</div>


				<div class="col-auto">
					<div class="text-truncate">
						<strong><?php echo lang('present_consumption'); ?> </strong> <?php echo $player->electric ? $player->electric . " watts" : "N/A"; ?>
					</div>
				</div>


				<div class="col-auto">
					<div class="text-truncate">
						<strong><?php echo lang('brightness'); ?> </strong> <?php echo $player->brightness ? $player->brightness . "%" : "N/A"; ?>
					</div>
				</div>

				<dt class="col-auto"><?php echo lang('temperature'); ?></dt>
				<dd class="col-auto"><?php echo $player->temp ? $player->temp . "°" : "N/A" ?></dd>

				<div class="col-auto">
					<div class="text-truncate">
						<strong><?php echo lang('humidity'); ?> </strong> <?php echo $player->dampness ? $player->dampness : "N/A"; ?>
					</div>
				</div>

				<div class="col-auto">
					<div class="text-truncate">
						<strong><?php echo lang('off_times'); ?> </strong>
						<?php if ($player->total_off_times) {
							echo $player->total_off_times . "(" . $player->recentDayOffTimes[0] . "," . $player->recentDayOffTimes[1] . "," . $player->recentDayOffTimes[2] . ","
								. $player->recentDayOffTimes[3] . "," . $player->recentDayOffTimes[4] . "," . $player->recentDayOffTimes[5] . "," . $player->recentDayOffTimes[6] . ")";
						} else {
							echo "0";
						} ?>
					</div>
				</div>
				<div class="col-auto">
					<div class="text-truncate">
						<strong>DB Player ID </strong> <?php echo $player->id; ?>
					</div>
				</div>
				<div class="col-auto">
					<div class="text-truncate">
						<strong><?php echo lang('ethernet_tethering'); ?> </strong> <?php echo $player->ethTetheringOnOff ? "On" : "Off" ?>
					</div>
				</div>
				<?php if ($player->volume !== null): ?>
					<div class="col-auto">
						<div class="text-truncate">
							<strong><?php echo lang('audio') . " " . lang('volume'); ?> </strong> <?php echo $player->volume; ?>
						</div>
					</div>
				<?php endif; ?>

			</div>
		</dl>
		<?php if ($this->config->item('has_peripherial') && $auth == 5) : ?>
			<div class="card">
				<div class="card-body">
					<h3 class="card-title"> <?php echo lang('peripherals'); ?></h3>
					<table id="peripherals_table-<?php echo $player->id ?>" class="table table-sm " data-page-size="10" data-side-pagination="server" data-pagination="true">

					</table>
				</div>
			</div>
		<?php endif ?>

		<table id='log_table-<?php echo $player->id ?>' data-show-refresh="true" class="table table-sm" data-query-params="logQueryParams">
		</table>
	</div>
</div>
<script>
	function logQueryParams(params) {
		params.player_id = "<?php echo $player->id; ?>";
		return params;
	}

	$(`#log_table-<?php echo $player->id ?>`).bootstrapTable({
		url: '/player/getLogData',
		pagination: true,
		queryParams: 'logQueryParams',
		columns: [{
			field: 'add_time',
			title: '<?php echo lang('datetime'); ?>'
		}, {
			field: 'detail',
		}]
	});
	<?php if ($this->config->item('has_peripherial') && $auth == 5) : ?>
		$('#peripherals_table-<?php echo $player->id ?>').bootstrapTable({
			url: '/player/getPeripheriesData',
			pagination: true,
			queryParams: 'logQueryParams',
			pagination: false,
			columns: [{
					field: 'name',
					title: '<?php echo lang('name'); ?>'
				}, {
					field: 'commands',
					formatter: "commandsFormatter",
					title: '<?php echo lang('commands'); ?>'
				},
				{
					formatter: "peripherals_operateFormatter",
					width: "60",
					title: "<?php echo lang('operate'); ?>",
				}
			]
		});
	<?php endif ?>



	function commandsFormatter(value, row) {
		var optStr = '';
		value.forEach(command => {
			optStr += `<option value='${command.id}'>${command.name} [${command.command}]</option>`;
		});
		if (optStr !== '') {
			return `<select class="form-select select2" id='command_select_${row.id}'>${optStr}</select>`;
		} else {
			return '';
		}
	}

	function peripherals_operateFormatter(value, row, index) {
		var ret = ' <div class="btn-list flex-nowrap">';
		<?php if ($auth == 5) : ?>

			ret += `<a href="#" onclick="upgrade_player(${row.id},<?php echo $player->id ?>)" title="<?php echo lang('send') ?>" >
					<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-send" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
						<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
						<path d="M10 14l11 -11"></path>
						<path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5"></path>
					</svg>
                </a>`;

		<?php endif; ?>

		ret += `</div>`;
		return ret;
	}

	function upgrade_player(pid, player_id) {
		var command_id = $(`#command_select_${pid}`).val();
		var ids = [];
		ids.push(player_id);
		$.post('/player/rs485_control', {
			"players": ids,
			"command": command_id,
		}, function(data) {
			console.log(data);
			//$('#table').bootstrapTable('uncheckAll');
			//alert(data);
		});

	}
</script>