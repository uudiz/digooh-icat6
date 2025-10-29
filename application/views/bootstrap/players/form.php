<link href="/assets/bootstrap/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="/assets/bootstrap-icons/bootstrap-icons.css">
<script src="/assets/bootstrap/js/imask.js"></script>


<div class="row">
	<div class="col-12 col-lg-10 m-auto pt-3 pb-2 mb-3">
		<div class="card">
			<div class="card-header">
				<h2><?php echo $title ?></h2>
			</div>
			<form id='playerForm'>
				<div class="card-body">
					<div class="accordion" id="accordion-example">
						<div class="accordion-item">
							<h2 class="accordion-header" id="heading-1">
								<button class="accordion-button" id="player_name" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-1" aria-expanded="true">
									<?php if (isset($player)) echo $player->name; ?>
								</button>
							</h2>
							<div id="collapse-1" class="accordion-collapse collapse show" data-bs-parent="#accordion-example">
								<div class="accordion-body pt-0">
									<div class="row g-3">
										<div class="col-md-12">
											<label for="name" class="col-form-label required"><?php echo lang('name'); ?></label>
											<input type="text" class="form-control" id="name" name='name' required value="<?php if (isset($player)) echo $player->name; ?>" />
										</div>


										<div class="col-md-12">
											<label for="name" class="col-form-label required"><?php echo lang('criteria'); ?></label>
											<select id="jquery-cribox-select-options" name='criteria[]' required class="chosen-select select2" multiple>
												<option value="0"></option>
												<?php foreach ($criteria as $tag) : ?>
													<option value="<?php echo $tag->id; ?>" <?php if (isset($cristr)) {
																								$criary = explode(',', $cristr);
																							}
																							if (isset($criary) && in_array($tag->id, $criary)) : ?>selected<?php endif; ?>><?php echo $tag->name ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-12">
											<label class="col-form-label" for="jquery-tagbox-select-options"><?php echo  $this->config->item("with_template") ? lang('exclude') . ' ' . lang('categories') : lang('tag'); ?></label>
											<select id="jquery-tagbox-select-options" name='tags' class="form-select select2" multiple>
												<option value="0"></option>
												<?php foreach ($tags as $tag) : ?>
													<option value="<?php echo $tag->id; ?>" <?php
																							if (isset($tagstr)) {
																								$tagary = explode(',', $tagstr);
																							}
																							if (isset($tagary) && in_array($tag->id, $tagary)) : ?>selected<?php endif; ?>><?php echo $tag->name
																																											?>
													</option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-2">
											<label for="setupdate" class="col-form-label label_required"><?php echo lang('setup_date'); ?></label>
											<input type="date" class="form-control" id="setupdate" input_required value="<?php if (isset($extra->setupdate)) echo $extra->setupdate;
																															else echo date("Y-m-d") ?>" />
										</div>
										<div class="col-md-4">
											<label for="timerConfigId" class="col-form-label label_required"><?php echo lang('timer.settings'); ?></label>
											<select name="timerConfigId" class="form-select select2" id="timerConfigId" input_required>
												<option value="0">&nbsp;</option>
												<?php foreach ($timers as $view) : ?>
													<option value="<?php echo $view->id; ?>" <?php if (isset($player) && $view->id == $player->timer_config_id) : ?>selected="selected" <?php endif; ?>><?php echo $view->name; ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<!--
								<div class="col-md-4">
									<label for="deviceId"><?php echo lang('device.setup'); ?></label>
									<select name="deviceId" class="form-select" id="deviceId">
										<option value="0">&nbsp;</option>
										<?php foreach ($configs as $config) : ?>
											<option value="<?php echo $config->id; ?>" <?php if (isset($player) && $config->id == $player->config) : ?>selected="selected" <?php endif; ?>><?php echo $config->name; ?></option>
										<?php endforeach; ?>
									</select>
								</div>
											-->
										<?php if ($this->config->item("with_template")) : ?>
											<div class="col-md-2">
												<label for="customsn2" class="col-form-label"><?php echo lang('store.id') ?></label>
												<input type="text" class="form-control " id="customsn2" value="<?php if (isset($extra->custom_sn2)) echo $extra->custom_sn2; ?>" />
											</div>
											<div class="col-md-2">
												<label for="customsn1" class="col-form-label "><?php echo  lang('store.display_id') ?></label>
												<input type="text" class="form-control " id="customsn1" value="<?php if (isset($extra->custom_sn1)) echo $extra->custom_sn1; ?>" />
											</div>
										<?php else : ?>
											<div class="col-md-2">
												<label for="customsn2" class="col-form-label label_required"><?php echo  lang('custom_sn2'); ?></label>
												<input type="text" class="form-control input_required" id="customsn2" value="<?php if (isset($extra->custom_sn2)) echo $extra->custom_sn2; ?>" />
											</div>
											<div class="col-md-2">
												<label for="customsn1" class="col-form-label label_required"><?php echo lang('custom_sn1'); ?></label>
												<input type="text" class="form-control input_required" id="customsn1" value="<?php if (isset($extra->custom_sn1)) echo $extra->custom_sn1; ?>" />
											</div>
										<?php endif ?>
										<?php if (!$this->config->item("with_template")) : ?>
											<div class="col-md-2">
												<label for="pps" class="col-form-label label_required"><?php echo $this->config->item("with_template") ? lang('Passerby') : lang('pps'); ?></label>
												<input type="number" class="form-control input_required" id="pps" value="<?php if (isset($extra->pps)) echo $extra->pps; ?>" />
											</div>
										<?php endif ?>
										<div class="col-md-2">
											<label for="barcode" class="col-form-label"><?php echo $this->config->item("with_template") ? lang('modell_nubmer') : lang('player_barcode'); ?></label>
											<input type="text" class="form-control" id="barcode" value="<?php if (isset($extra->barcode)) echo $extra->barcode; ?>" />
										</div>
										<?php if (!$this->config->item("with_template")) : ?>
											<div class="col-md-2">
												<label for="displaynum" class="col-form-label"><?php echo lang('displaynum'); ?></label>
												<input type="text" class="form-control" id="displaynum" value="<?php if (isset($extra->displaynum)) echo $extra->displaynum; ?>" />
											</div>
											<div class="col-md-2">
												<label for="locationid" class="col-form-label"><?php echo lang('location_id'); ?></label>
												<input type="text" class="form-control" id="locationid" value="<?php if (isset($extra->locationid)) echo $extra->locationid; ?>" />
											</div>
										<?php endif ?>
										<div class="col-md-2">
											<label for="itemnum" class="col-form-label"><?php echo lang('item_num'); ?></label>
											<input type="text" class="form-control" id="itemnum" value="<?php if (isset($extra->itemnum)) echo $extra->itemnum; ?>" />
										</div>
										<?php if (!$this->config->item("with_template")) : ?>
											<div class="col-md-2">
												<label for="screen" class="col-form-label"><?php echo lang('screen.type'); ?></label>
												<select id="screen" name="screen" class="form-select">
													<option value="0" <?php if (isset($player) && $player->screen_oritation == "0") : ?>selected="selected" <?php endif; ?>><?php echo lang('player.portrait'); ?></option>
													<option value="1" <?php if (isset($player) && $player->screen_oritation == "1") : ?>selected="selected" <?php endif; ?>><?php echo lang('player.landscape'); ?></option>
												</select>
											</div>
											<div class="col-md-1">
												<label for="modelname" class="col-form-label"><?php echo lang('model_name'); ?></label>
												<input type="text" class="form-control" id="modelname" value="<?php if (isset($extra->modelname)) echo $extra->modelname; ?>" />
											</div>
										<?php endif ?>

										<div class="col-md-1">
											<label for="visitors" class="col-form-label"><?php echo lang('visitors'); ?></label>
											<input type="number" class="form-control" id="visitors" value="<?php if (isset($extra->visitors)) echo $extra->visitors; ?>" />
										</div>

										<div class="col-md-3">
											<label for="viewdirection" class="col-form-label"><?php echo lang('view_direction'); ?></label>
											<input type="text" class="form-control" id="viewdirection" value="<?php if (isset($extra->viewdirection)) echo $extra->viewdirection; ?>" />
										</div>

										<?php if ($this->config->item("with_template")) : ?>
											<div class="col-md-2">
												<label for="screensize" class="col-form-label"><?php echo lang('screen_resolution') ?></label>
												<input type="text" class="form-control" id="screensize" value="<?php if (isset($extra->screensize)) echo $extra->screensize; ?>" />
											</div>
										<?php else : ?>
											<div class="col-md-1">
												<label for="screensize" class="col-form-label"><?php echo  lang('screen_size'); ?></label>
												<input type="text" class="form-control" id="screensize" value="<?php if (isset($extra->screensize)) echo $extra->screensize; ?>" />
											</div>
										<?php endif ?>
										<div class="col-md-2">
											<label for="sided" class="col-form-label"><?php echo lang('side'); ?></label>
											<select id="sided" name="sided" class="form-select">
												<option value="0" <?php if (isset($extra->sided) && $extra->sided == "0") : ?>selected="selected" <?php endif; ?>><?php echo lang('single_side'); ?></option>
												<option value="1" <?php if (isset($extra->sided) && $extra->sided == "1") : ?>selected="selected" <?php endif; ?>><?php echo lang('double_side'); ?></option>
											</select>
										</div>
										<?php if ($this->config->item("with_template")) : ?>
											<div class="col-md-2">
												<label for="last_maintenance" class="col-form-label"><?php echo lang('last.maintenance'); ?></label>
												<input type="date" class="form-control" id="last_maintenance" name='last_maintenance' value="<?php if (isset($extra->last_maintenance)) echo $extra->last_maintenance; ?>" />
											</div>

										<?php endif ?>

										<div class="col-md-4">
											<label for="simno" class="col-form-label"><?php echo lang('player_simno'); ?></label>
											<input type="text" class="form-control" id="simno" value="<?php if (isset($extra->simno)) echo $extra->simno; ?>" />
										</div>
										<div class="col-md-2">
											<label for="simvolume" class="col-form-label"><?php echo lang('sim_volume'); ?></label>
											<input type="text" class="form-control" id="simvolume" value="<?php if (isset($extra->simvolume)) echo $extra->simvolume; ?>" />
										</div>
										<?php if ($this->config->item("with_template")) : ?>
											<?php if ($this->config->item('has_sensor')) : ?>
												<div class="col-md-3">
													<label for="videthreshold_ido_playback" class="col-form-label"><?php echo lang('sensor_thresholds'); ?></label>
													<select name="threshold_id" class="form-select select2" id="threshold_id">
														<option value="0">&nbsp;</option>
														<?php foreach ($sensors as $senor) : ?>
															<option value="<?php echo $senor->id; ?>" <?php if (isset($player->threshold_id) && $senor->id == $player->threshold_id) : ?>selected="selected" <?php endif; ?>><?php echo $senor->name; ?></option>
														<?php endforeach; ?>
													</select>
												</div>

											<?php endif ?>
											<div class="col-md-3">
												<label for="video_playback" class="col-form-label"><?php echo lang('video.playback'); ?></label>
												<label class="form-check form-switch ">
													<input class="form-check-input" type="checkbox" id='video_playback' <?php if (!isset($player) || (isset($player) && $player->video_playback)) : ?> checked<?php endif; ?>>
												</label>
											</div>


										<?php endif ?>

										<div class="col-md-4">
											<label for="partnerid" class="col-form-label"><?php echo lang('partner_id'); ?></label>
											<input type="text" class="form-control" id="partnerid" value="<?php if (isset($extra->partnerid)) echo $extra->partnerid; ?>" />
										</div>
										<div class="col-md-4">
											<label for="conname" class="col-form-label"><?php echo lang('player_conname'); ?></label>
											<input type="text" class="form-control" id="conname" value="<?php if (isset($extra->conname)) echo $extra->conname; ?>" />
										</div>
										<div class="col-md-4">
											<label for="conphone" class="col-form-label"><?php echo lang('player_conphone'); ?></label>
											<input type="text" class="form-control" id="conphone" name="conphone" value="<?php if (isset($extra->conphone)) echo $extra->conphone; ?>" />
										</div>
										<div class="col-md-4">
											<label for="conemail" class="col-form-label"><?php echo lang('player_conemail'); ?></label>
											<input type="text" class="form-control" id="conemail" value="<?php if (isset($extra->conemail)) echo $extra->conemail; ?>" />
										</div>
										<?php if (!$this->config->item("with_template")) : ?>
											<div class="col-md-4">
												<label for="last_maintenance" class="col-form-label"><?php echo lang('last.maintenance'); ?></label>
												<input type="date" class="form-control" id="last_maintenance" name='last_maintenance' value="<?php if (isset($extra->last_maintenance)) echo $extra->last_maintenance; ?>" />
											</div>
										<?php endif ?>
										<?php if (!$this->config->item("with_template")) : ?>
											<div class="col-md-4">
												<label for="video_playback" class="col-form-label"><?php echo lang('video.playback'); ?></label>
												<label class="form-check form-switch ">
													<input class="form-check-input" type="checkbox" id='video_playback' <?php if (!isset($player) || (isset($player) && $player->video_playback)) : ?> checked<?php endif; ?>>
												</label>
											</div>

											<?php if ($this->config->item('has_sensor')) : ?>
												<div class="col-md-4">
													<label for="videthreshold_ido_playback" class="col-form-label"><?php echo lang('sensor_thresholds'); ?></label>
													<select name="threshold_id" class="form-select select2" id="threshold_id">
														<option value="0">&nbsp;</option>
														<?php foreach ($sensors as $senor) : ?>
															<option value="<?php echo $senor->id; ?>" <?php if (isset($player->threshold_id) && $senor->id == $player->threshold_id) : ?>selected="selected" <?php endif; ?>><?php echo $senor->name; ?></option>
														<?php endforeach; ?>
													</select>
												</div>
											<?php endif ?>
										<?php endif ?>

										<div class="col-12">
											<label for="descr" class="col-form-label"><?php echo lang("desc"); ?></label>
											<textarea type="text" class="form-control" id="descr" name='descr' rows="2"><?php if (isset($player->descr)) echo $player->descr; ?></textarea>
										</div>
										<div class="col-12">
											<label for="detail" class="col-form-label"><?php echo lang("player.detail"); ?></label>
											<textarea type="text" class="form-control" id="detail" name='detail' rows="2"><?php if (isset($player->details)) echo $player->details; ?></textarea>
										</div>

									</div>
								</div>
							</div>
						</div>
						<div class="accordion-item">
							<h2 class="accordion-header" id="heading-2">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-2" aria-expanded="false">
									<?php echo lang('address') . " " . lang('and') . " " . lang('photos') ?>
								</button>
							</h2>
							<div id="collapse-2" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
								<div class="accordion-body pt-0">
									<div class="row g-3">
										<div class="col-md-2">
											<label for="name"><?php echo lang('player_country'); ?></label>
											<input type="text" class="form-control" id="country" value="<?php if (isset($extra->country)) echo $extra->country; ?>" />
										</div>
										<div class="col-md-2">
											<label for="state"><?php echo lang('player_state'); ?></label>
											<input type="text" class="form-control" id="state" value="<?php if (isset($extra->state)) echo $extra->state; ?>" />
										</div>
										<div class="col-md-2">
											<label for="zipcode"><?php echo lang('player_connzipcode'); ?></label>
											<input type="text" class="form-control" id="zipcode" value="<?php if (isset($extra->conzipcode)) echo $extra->conzipcode; ?>" />
										</div>
										<div class="col-md-6">
											<label for="contown"><?php echo lang('player_contown'); ?></label>
											<input type="text" class="form-control" id="contown" value="<?php if (isset($extra->contown)) echo $extra->contown; ?>" />
										</div>

										<div class="col-md-9">
											<label for="conaddr"><?php echo lang('player_conaddr'); ?></label>
											<input type="text" class="form-control" id="conaddr" value="<?php if (isset($extra->conaddr)) echo $extra->conaddr; ?>" />
										</div>
										<div class="col-auto">
											<label for="street_num"><?php echo lang('street_num'); ?></label>
											<input type="text" class="form-control" id="street_num" value="<?php if (isset($extra->street_num)) echo $extra->street_num; ?>" />
										</div>


										<div class="col-md-2">
											<label for="geox"><?php echo lang('latitude'); ?></label>
											<input type="number" class="form-control" id="geox" name='geox' value="<?php if (isset($extra->geox)) echo $extra->geox; ?>" />
										</div>
										<div class="col-md-2">
											<label for="name"><?php echo lang('longitude'); ?></label>
											<input type="number" class="form-control" id="geoy" name="geoy" value="<?php if (isset($extra->geoy)) echo $extra->geoy; ?>" />
										</div>
										<?php if (isset($player)) : ?>
											<div class="col-12">
												<label for="pictures"><?php echo lang('photos') ?></label>
												<input type="file" id="pictures" name="pictures[]" accept="image/*" multiple>
											</div>
										<?php endif ?>
									</div>
								</div>
							</div>
						</div>
						<?php if ($ssp_feature == 1 && $this->config->item('ssp_feature')) : ?>
							<div class="accordion-item">
								<h2 class="accordion-header" id="heading-5">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-5" aria-expanded="false">
										SSP
									</button>
								</h2>
								<div id="collapse-5" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
									<div class="accordion-body pt-0 row">
										<?php if (isset($ssptypes)): ?>
											<?php foreach ($ssptypes as $ssptype): ?>
												<div class="col-12">
													<label><?php echo strtoupper($ssptype->name) . " " . lang('categories'); ?></label>
													<select class="chosen-select select2 ssp-categories" multiple>
														<?php if (isset($groupedSspCategories) && isset($groupedSspCategories[$ssptype->id])): ?>
															<?php foreach ($groupedSspCategories[$ssptype->id] as $cri): ?>
																<option value="<?php echo $cri->id; ?>" <?php
																										if (isset($sspcristr)) {
																											$sspcriary = explode(',', $sspcristr);
																										};
																										if (isset($sspcriary) && in_array($cri->id, $sspcriary)) : ?>selected<?php endif; ?>><?php echo $cri->name ?></option>
															<?php endforeach; ?>
														<?php endif; ?>
													</select>
												</div>
											<?php endforeach; ?>
										<?php endif; ?>


										<div class="col-12">
											<label for="jquery-ssptagbox-select-options"><?php echo lang('ssp.tags'); ?></label>
											<select id="jquery-ssptagbox-select-options" class="chosen-select select2" multiple>
												<?php if (isset($ssptags)) : ?>
													<?php foreach ($ssptags as $tag) : ?>
														<option value="<?php echo $tag->id; ?>" <?php if (isset($ssptagstr)) {
																									$tagary = explode(',', $ssptagstr);
																								}
																								if (isset($tagary) && in_array($tag->id, $tagary)) : ?>selected<?php endif; ?>><?php echo $tag->name ?>
														</option>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>
										</div>
										<div class="col-12">
											<label for="pos_tags"><?php echo lang('ssp.pos.tags'); ?></label>
											<input type="text" class="form-control" id="pos_tags" value="<?php if (isset($extra->pos_tags)) echo $extra->pos_tags; ?>" />
										</div>
										<div class="col-12">
											<label for="ssp_exclude"><?php echo lang('ssp.exclude'); ?></label>
											<input type="text" class="form-control" id="ssp_exclude" value="<?php if (isset($extra->ssp_exclude)) echo $extra->ssp_exclude; ?>" />
										</div>
										<div class="col-12">
											<label for="ssp_additional"><?php echo lang('ssp.additional'); ?></label>
											<input type="text" class="form-control" id="ssp_additional" value="<?php if (isset($extra->ssp_additional)) echo $extra->ssp_additional; ?>" />
										</div>
										<div class="col-12">
											<label for="ssp_dsp_alias"><?php echo lang('ssp.dsp.alias'); ?></label>
											<input type="text" class="form-control" id="ssp_dsp_alias" value="<?php if (isset($extra->ssp_dsp_alias)) echo $extra->ssp_dsp_alias; ?>" />
										</div>
										<div class="mb-3">
											<label for="ssp_dsp_ref"><?php echo lang('ssp.dsp.ref'); ?></label>
											<input type="text" class="form-control" id="ssp_dsp_ref" value="<?php if (isset($extra->ssp_dsp_alias)) echo $extra->ssp_dsp_ref; ?>" />
										</div>

										<div class="mb-3">
											<label>AMC Werte für 24 Stunden (pro Stunde mit, getrennt)</label>
											</br>
										</div>

										<div class="col-md-6">
											<label for="mon"><?php echo lang('mon'); ?></label>
											<input type="text" class="form-control amc" id="mon" value="<?php if (isset($amc->mon)) echo $amc->mon; ?>" />
										</div>
										<div class="col-md-6">
											<label for="tue"><?php echo lang('tue'); ?></label>
											<input type="text" class="form-control amc" id="tue" value="<?php if (isset($amc->tue)) echo $amc->tue; ?>" />
										</div>
										<div class="col-md-6">
											<label for="wed"><?php echo lang('wed'); ?></label>
											<input type="text" class="form-control amc" id="wed" value="<?php if (isset($amc->wed)) echo $amc->wed; ?>" />
										</div>
										<div class="col-md-6">
											<label for="thu"><?php echo lang('thu'); ?></label>
											<input type="text" class="form-control amc" id="thu" value="<?php if (isset($amc->thu)) echo $amc->thu; ?>" />
										</div>
										<div class="col-md-6">
											<label for="fri"><?php echo lang('fri'); ?></label>
											<input type="text" class="form-control amc" id="fri" value="<?php if (isset($amc->fri)) echo $amc->fri; ?>" />
										</div>
										<div class="col-md-6">
											<label for="sat"><?php echo lang('sat'); ?></label>
											<input type="text" class="form-control amc" id="sat" value="<?php if (isset($amc->sat)) echo $amc->sat; ?>" />
										</div>
										<div class="col-md-6">
											<label for="sun"><?php echo lang('sun'); ?></label>
											<input type="text" class="form-control amc" id="sun" value="<?php if (isset($amc->sun)) echo $amc->sun; ?>" />
										</div>

									</div>

								</div>
							</div>
						<?php endif ?>
					</div>

				</div>
				<div class="card-footer">
					<input type="hidden" id="id" name="id" value="<?php echo isset($player->id) ? $player->id : 0; ?>" />
					<?php if (($auth >= $ADMIN  || (isset($privilege->can_create_player) && $privilege->can_create_player == 1)) && !$pid) : ?>
						<button class="btn btn-outline-primary" type="submit"><i class="bi bi-cloud-arrow-up"></i><?php echo lang('button.save'); ?></button>
					<?php endif ?>
					<a class="btn  btn-outline-primary" href="/player"><i class="bi bi-x-circle"></i><?php echo lang('button.cancel'); ?></a>
				</div>
			</form>
		</div>
	</div>
	<?php
	$this->load->view("bootstrap/campaigns/refreshModal");
	?>
</div>
<script src="/assets/bootstrap/js/jquery.validate.min.js"></script>
<?php if ($lang == 'germany') : ?>
	<script src="/assets/js/validation/messages_de.js"></script>
<?php endif ?>
<script src="/assets/bootstrap/js/fileinput.min.js"></script>
<script src="/assets/bootstrap/js/fileinput-locales/de.js"></script>
<script src="/assets/bootstrap/js/fileinput-locales/en.js"></script>
<script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD5ILgQ2vLjavzFASq5xHfuVYVneV9DBQk&callback=initAutocomplete&libraries=places&v=weekly" defer></script>
<script src="/assets/js/googleplaces.js"></script>
<script type="text/javascript">
	async function refreshPlayerCampaigns($id, $method) {
		try {
			const request = await fetch('/player/refresh_player?id=' + $id + "method=" + $method, {
				method: 'GET',
				headers: {
					'Content-Type': 'application/json'
				}
			});
			const response = await request.json();

			hideSpinner();
			if (response.code == 'success') {
				localStorage.setItem("Status", JSON.stringify({
					type: 'success',
					message: response.msg
				}));
				window.location.href = '/player';

			} else {
				toastr.error(response.msg);
			}
		} catch (error) {
			console.log(error);
			hideSpinner();
			toastr.error("Unexpected error,Please contact the administrator");


		}
	}
	$(document).ready(function() {
		/*
		var maskOptions = {
			mask: '0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0',
			//lazy: false,
			placeholderChar: '__',
			definitions: {
				'0': /[0-1]/
			}
		};
		$.each(['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'], function() {
			var element = document.getElementById(this);
			IMask(element, maskOptions);
		});
		*/
		/*
		IMask(
			document.getElementById('mac'), {
				mask: '**:**:**:**:**:**',
			}
		)
		*/



		var validation_rules = {
			email: {
				email: true
			},
		}
		<?php if ($this->config->item("digooh_player_form_validation")) : ?>
			$('.label_required').addClass('required');
			$('.input_required').prop('required', true);
			var digooh_rules = {
				geox: {
					range: [46, 56],
				},
				geoy: {
					range: [5, 15],
				},
			};
			Object.assign(validation_rules, digooh_rules);

		<?php endif ?>


		$("#playerForm").validate({

			rules: validation_rules,

			submitHandler: function(form) {
				var page_id = $.trim($("li.active").text());
				var id = $("#id").val();
				var name = $("#name").val();
				var filter_type = $("#filter_type").val();
				var filter_name = $("#filter_name").val();

				if (id == undefined) {
					id = 0;
				}
				if (
					name.indexOf("&") >= 0 ||
					name.indexOf("<") >= 0 ||
					name.indexOf(">") >= 0 ||
					name.indexOf("'") >= 0 ||
					name.indexOf("\\") >= 0 ||
					name.indexOf("%") >= 0
				) {
					showFormMsg(
						"Special symbols (& <> ' \\ %) are not allowed in the player name.",
						"error"
					);
					return false;
				}
				const selectElements = document.querySelectorAll('.ssp-categories');
				let allSelectedValues = [];

				selectElements.forEach(selectElement => {
					const selectedValues = Array.from(selectElement.selectedOptions).map(option => option.value);
					allSelectedValues = allSelectedValues.concat(selectedValues);
				});

				var formData = {
					id: id,
					gid: 0,
					name: name,
					city_code: $("#cityCode").val(),
					tags_select: String($("#jquery-tagbox-select-options").val()),
					criteria_select: String($("#jquery-cribox-select-options").val()),
					screensel: $("#screen").val(),
					descr: $("#descr").val(),
					mac: $('#mac').val(),
					timer_config_id: $("#timerConfigId").val(),
					config: $('#deviceId').val(),
					barcode: $("#barcode").val(),
					simno: $("#simno").val(),
					conname: $("#conname").val(),
					conphone: $("#conphone").val(),
					conemail: $("#conemail").val(),
					conaddr: $("#conaddr").val(),
					zipcode: $("#zipcode").val(),
					street_num: $('#street_num').val(),
					contown: $("#contown").val(),
					volume: $("#volume").val(),
					simvolume: $("#simvolume").val(),
					itemnum: $("#itemnum").val(),
					modelname: $("#modelname").val(),
					screensize: $("#screensize").val(),
					sided: $("#sided").val(),
					partnerid: $("#partnerid").val(),
					locationid: $("#locationid").val(),
					geox: $("#geox").val(),
					geoy: $("#geoy").val(),
					setupdate: $("#setupdate").val(),
					viewdirection: $("#viewdirection").val(),
					pps: $("#pps").val(),
					visitors: $("#visitors").val(),
					displaynum: $("#displaynum").val(),
					state: $("#state").val(),
					country: $("#country").val(),
					customsn1: $("#customsn1").val(),
					customsn2: $("#customsn2").val(),
					details: $("#detail").val(),
					ssptags_select: $("#jquery-ssptagbox-select-options").val(),
					ssp_categories: allSelectedValues,
					mon: $("#mon").val(),
					tue: $("#tue").val(),
					wed: $("#wed").val(),
					thu: $("#thu").val(),
					fri: $("#fri").val(),
					sat: $("#sat").val(),
					sun: $("#sun").val(),
					pos_tags: $("#pos_tags").val(),
					ssp_exclude: $("#ssp_exclude").val(),
					ssp_additional: $("#ssp_additional").val(),
					ssp_dsp_alias: $("#ssp_dsp_alias").val(),
					ssp_dsp_ref: $("#ssp_dsp_ref").val(),
					street_num: $("#street_num").val(),
					last_maintenance: $("#last_maintenance").val(),
					video_playback: $("#video_playback").is(':checked') ? "1" : "0",
					threshold_id: $('#threshold_id').val()
				};

				$.post(
					"/player/do_save", formData,
					function(data) {
						if (data.code != 0) {
							toastr.error(data.msg);
						} else {
							if (data.needPublish == 1) {

								<?php if (!$this->config->item('refresh_camapign_while_saving_player')) : ?>

									localStorage.setItem("Status", JSON.stringify({
										type: 'alert',
										message: data.repubmsg
									}));
									window.location = '/player';

								<?php else : ?>

									$('#refresh_confirm-prompt').html(data.repubmsg);
									$('#refresh_confirm').modal("show")
										.off('click').on('click', '#refresh', function(e) {
											showSpinner();
											refreshPlayerCampaigns(data.id, data.method);
										});

								<?php endif ?>


							} else {
								localStorage.setItem("Status", JSON.stringify({
									type: 'success',
									message: data.msg
								}));
								window.location = '/player';
							}

						}
					},
					"json"
				);

			}
		});
	});
	$("input#name").on('keydown paste input', function() {
		$('#player_name').text($('#name').val());
	});

	var lang = 'en';
	<?php if ($lang == "germany") : ?>
		var lang = 'de';
	<?php endif ?>

	const uploader = $("#pictures");
	const player_id = $("#id").val();
	if (player_id && player_id != '0') {
		$.getJSON("/player/get_player_pictures", {
			id: player_id
		}, function(result) {
			uploader.fileinput({
				uploadUrl: "/player/upload_photo",
				enableResumableUpload: true,
				uploadExtraData: {
					'pid': $('#id').val(),
				},
				allowedFileTypes: ['image'], // allow only images
				showRemove: false,
				required: true,
				showUpload: false,
				browseOnZoneClick: true,
				initialPreviewAsData: true,
				overwriteInitial: false,
				initialPreview: result.initialPreview, // if you have previously uploaded preview files
				initialPreviewConfig: result.initialPreviewConfig, // if you have previously uploaded preview files
				deleteUrl: "/player/delete_picture",
				showUploadStats: false,
				showClose: false,
				fileActionSettings: {
					showDrag: false,
				},
				language: lang,
				previewSettings: {
					image: {
						width: "auto",
						height: "100%",
						'max-width': "100%",
						'max-height': "100%"
					},
				},
			}).on('filebatchselected', function(event, previewId, index, fileId) {
				uploader.fileinput('upload');
			}).on('fileuploaderror', function(event, data, msg) {
				console.log('File Upload Error', 'ID: ' + data.fileId + ', Thumb ID: ' + data.previewId);
			})
		});
	};
</script>