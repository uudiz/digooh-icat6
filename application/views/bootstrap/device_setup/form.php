<script src="/assets/bootstrap/js/jquery.validate.min.js"></script>
<?php if ($lang == 'germany') : ?>
	<script src="/assets/js/validation/messages_de.js"></script>
<?php endif ?>
<script src="/assets/bootstrap/js/imask.js"></script>

<div class="row">
	<div class="col-12 col-lg-8 m-auto pt-3 pb-2 mb-3">
		<div class="card">
			<div class="card-header">
				<h2><?php echo $title ?></h2>
			</div>
			<form id="dataForm" action="/configxml/do_save">
				<div class="card-body">
					<div id="validateTips"> </div>
					<div class="row g-1">

						<div class="mb-3">
							<label for="name"><?php echo lang('name'); ?></label>
							<input type="text" class="form-control" name="name" required value="<?php if (isset($data->name)) echo $data->name; ?>" />
						</div>
						<div class="mb-3">
							<label for="descr"><?php echo lang("desc"); ?></label>
							<textarea type="text" class="form-control" name="descr" rows="2"><?php if (isset($data->descr)) echo $data->descr; ?></textarea>
						</div>


						<div class="mb-3">
							<lable></lable>
							<label class="form-check form-switch">
								<label><?php echo lang("synctime"); ?></label>
								<input type="checkbox" name='synctime' class="form-check-input" <?php if (!isset($data) || (isset($data) && $data->synctime)) : ?>checked <?php endif ?> />
							</label>
						</div>

						<div class="mb-3 row g-0">
							<div class="col-auto ">
								<label class="form-check form-switch">
									<label><?php echo lang("daily.restart.time"); ?></label>
									<input type="checkbox" id="drflag" name='drflag' class="form-check-input" <?php if (!isset($data) || (isset($data) && $data->dailyRestartFlag)) : ?>checked <?php endif ?> />
								</label>
							</div>

							<div class="col-auto" id='dailyRestartTime' <?php if ((isset($data) && !$data->dailyRestartFlag)) : ?>style="display:none" <?php endif ?>>
								<input type="time" required class="form-control" name="dailyRestartTime" value="<?php echo isset($data) ? $data->dailyRestartTime : "00:00"; ?>" />
							</div>
						</div>


						<div class="mb-3 col-md-4">
							<label for="players-select-options">
								<?php echo lang("timezone"); ?>
							</label>
							<select class="form-select" name="timezone">
								<?php foreach ($zones as $key => $value) : ?>
									<option value="<?php echo $value; ?>" <?php if (isset($data->timezone) && $data->timezone == $value) : ?>selected="selected" <?php endif; ?>><?php echo $key; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="mb-3 col-md-4">
							<label><?php echo lang("clockpos"); ?></label>
							<select class="form-select" name="clockpos">
								<?php foreach ($clockpos as $key => $pos) : ?>
									<option value="<?php echo $key; ?>" <?php if (isset($data->clockpos) && $data->clockpos == $key) : ?>selected="selected" <?php endif; ?>><?php echo $pos; ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="mb-3 col-md-4">
							<label><?php echo lang("storagepri"); ?></label>
							<select class="form-select" name="storagepri">
								<option value="-1" <?php if (!isset($data) || (isset($data) && $data->storagepri == -1)) : ?>selected="selected" <?php endif; ?>>No change</option>
								<option value="1" <?php if (isset($data) && $data->storagepri == 1) : ?>selected="selected" <?php endif; ?>>Internal Disk</option>
								<option value="2" <?php if (isset($data) && $data->storagepri == 2) : ?>selected="selected" <?php endif; ?>>TF Card</option>
								<option value="3" <?php if (isset($data) && $data->storagepri == 3) : ?>selected="selected" <?php endif; ?>>USB</option>

							</select>
						</div>

						<div class="mb-3 col-md-4">
							<label><?php echo lang("sync.playback"); ?></label>
							<select class="form-select" name="sync_playback">
								<option value="0" <?php if (isset($data) && $data->sync_playback == 0) : ?>selected="selected" <?php endif; ?>><?php echo lang("sync.0"); ?></option>
								<option value="1" <?php if (isset($data) && $data->sync_playback == 1) : ?>selected="selected" <?php endif; ?>><?php echo lang("sync.1"); ?></option>
								<option value="2" <?php if (isset($data) && $data->sync_playback == 2) : ?>selected="selected" <?php endif; ?>><?php echo lang("sync.2"); ?></option>
							</select>
						</div>
						<div class="mb-3 col-md-4">
							<label><?php echo lang("device.orientation"); ?></label>
							<select class="form-select" name="orientation">
								<?php foreach ($orientation_list as $k => $v) : ?>
									<option value="<?php echo $k; ?>" <?php if (isset($data) && $k == $data->orientation) : ?>selected<?php endif; ?>><?php echo $v; ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="mb-3 col-md-4">
							<label>HDMI</label>
							<select class="form-select" name="videomode">
								<option value="-1" <?php if (isset($data) && $data->videomode == -1) : ?>selected="selected" <?php endif; ?>>No change</option>
								<option value="1" <?php if (isset($data) && $data->videomode == 1) : ?>selected="selected" <?php endif; ?>><?php echo lang("hdmi.50"); ?></option>
								<option value="2" <?php if (isset($data) && $data->videomode == 2) : ?>selected="selected" <?php endif; ?>><?php echo lang("hdmi.60"); ?></option>
							</select>
						</div>
						<?php if ($device_setup == '1') : ?>
							<div class="mb-3">
								<label for="sn"><?php echo lang('player.id'); ?></label>
								<input type="text" class="form-control" id="sn" name="sn" value="<?php if (isset($data->sn)) echo $data->sn; ?>" />
							</div>
							<div class="mb-3 col-md-3">
								<label for="connectionMode"><?php echo lang('server.connection.mode'); ?></label>
								<select class="form-select" id="connectionMode" name="connectionMode">
									<option value="0" <?php if (isset($data) && $data->connectionMode == 0) : ?>selected <?php endif; ?>><?php echo lang("ip.mode"); ?></option>
									<option value="1" <?php if (!isset($data) || (isset($data) && $data->connectionMode == 1)) : ?>selected <?php endif; ?>><?php echo lang("domain.mode"); ?></option>
								</select>
							</div>
							<div class="mb-3 col-md-7 ip-mode" <?php if (!isset($data) || (isset($data) && $data->connectionMode != 0)) : ?>style="display:none" <?php endif ?>>
								<label for="ip"><?php echo lang('server.ip'); ?></label>
								<input type="text" class="form-control" name="ip" value="<?php if (isset($data->ip)) echo $data->ip; ?>" />
							</div>
							<div class="mb-3 col-md-7 domain-mode" <?php if (isset($data) && $data->connectionMode != 1) : ?>style="display:none" <?php endif ?>>
								<label for="domain"><?php echo lang('domain'); ?></label>
								<input type="text" class="form-control" name="domain" value="<?php if (isset($data->domain)) echo $data->domain; ?>" />
							</div>
							<div class="mb-3 col-md-2 ">
								<label for="port"><?php echo lang('port'); ?></label>
								<input type="number" class="form-control" name="port" required value="<?php if (isset($data->port)) echo $data->port;
																										else echo "80"; ?>" />
							</div>
							<div class="mb-3 col-md-6">
								<label for="tcpport"><?php echo lang('tcpport'); ?></label>
								<input type="number" class="form-control" name="tcpport" required value="<?php if (isset($data->tcpport)) echo $data->tcpport;
																											else echo 4702 ?>" />
							</div>

							<div class="mb-3 col-md-6">
								<label for="brightness"><?php echo lang('brightness'); ?></label>
								<input type="number" class="form-control" name="brightness" min="1" max="100" value="<?php if (isset($data->brightness)) echo $data->brightness; ?>" />
							</div>

							<label class="form-check form-switch col-md-6">
								<input class="form-check-input" type="checkbox" name='playback_flag' <?php if (!isset($data) || (isset($data) && $data->playback_flag == 1)) : ?> checked <?php endif; ?>>
								<span class="form-check-label"><?php echo lang("report"); ?></span>
							</label>

							<label class="form-check form-switch col-md-6">
								<input class="form-check-input" type="checkbox" name='ethernetTethering' <?php if (isset($data) && $data->ethernetTethering == 1) : ?> checked <?php endif; ?>>
								<span class="form-check-label"><?php echo lang("ethernet_tethering"); ?></span>
							</label>

							<div class="mb-3 col-md-2">
								<label for="connectionMode"><?php echo lang('network_mode'); ?></label>
								<select class="form-select" id="networkmode" name="networkmode">
									<option value="-1" <?php if (isset($data) && $data->networkmode == -1) : ?>selected="selected" <?php endif; ?>><?php echo lang('netowrk.nochange'); ?></option>
									<option value="1" <?php if (isset($data) && $data->networkmode == 1) : ?>selected="selected" <?php endif; ?>><?php echo lang("network_lan"); ?></option>
									<option value="2" <?php if (isset($data) && $data->networkmode == 2) : ?>selected="selected" <?php endif; ?>><?php echo lang("network_wifi"); ?></option>
									<option value="3" <?php if (isset($data) && $data->networkmode == 3) : ?>selected="selected" <?php endif; ?>><?php echo lang("network_3g"); ?></option>
								</select>
							</div>
							<div class="mb-3 col-md-5 wifisetup" style="display:none;">
								<label for="wifissid"><?php echo lang('wifi_ssid'); ?></label>
								<input type="text" class="form-control" name="wifissid" value="<?php if (isset($data)) echo $data->wifissid; ?>" />
							</div>
							<div class="mb-3 col-md-5 wifisetup" style="display:none;">
								<label for="wifipwd"><?php echo lang('wifi_pw'); ?></label>
								<input type="text" class="form-control" name="wifipwd" value="<?php if (isset($data)) echo $data->wifipwd; ?>" />
							</div>
							<div class="mb-3 col-md-5 hotspotsetup" style="display:none;">
								<label for="hotssid"><?php echo lang('hotspot_ssid'); ?></label>
								<input type="text" class="form-control" name="hotspot_ssid" value="<?php if (isset($data->hotssid)) echo $data->hotssid; ?>" />
							</div>
							<div class="mb-3 col-md-5 hotspotsetup" style="display:none;">
								<label for="hotpwd"><?php echo lang('hotspot_pw'); ?></label>
								<input type="text" class="form-control" name="hotpwd" value="<?php if (isset($data->hotpwd)) echo $data->hotpwd; ?>" />
							</div>


						<?php endif ?>
						<input type="hidden" id="id" name="id" value="<?php echo isset($data->id) ? $data->id : 0; ?>" />

					</div>
					<div class="card-footer">
						<button class="btn btn-outline-primary" type="submit"><i class="bi bi-cloud-arrow-up"></i><?php echo lang('button.save'); ?></button>
						<a class="btn  btn-outline-primary" href="/configxml"><i class="bi bi-x-circle"></i><?php echo lang('button.cancel'); ?></a>
					</div>
			</form>
		</div>
	</div>
</div>
<script>
	var maskOptions = {
		mask: '000-000-0000',
		//lazy: false,
		definitions: {
			'0': /[0-9]/
		}
	};

	var element = document.getElementById('sn');
	IMask(element, maskOptions);


	$('#drflag').on('change', function() {
		if (this.checked) {
			$('#dailyRestartTime').show();
		} else {
			$('#dailyRestartTime').hide();
		}
	});
	$("#networkmode").change(function() {
		var checkValue = $("#networkmode").val();
		if (checkValue == 2) {
			$(".wifisetup").show();
		} else {
			$(".wifisetup").hide();
		}
		if (checkValue == 3) {
			$(".hotspotsetup").show();
		} else {
			$(".hotspotsetup").hide();

		}

	});
	$("#connectionMode").change(function() {
		var checkValue = $("#connectionMode").val();
		if (checkValue == 1) {
			$(".ip-mode").hide();
			$(".domain-mode").show();
		}
		if (checkValue == 0) {
			$(".ip-mode").show();
			$(".domain-mode").hide();
		}
	});
</script>