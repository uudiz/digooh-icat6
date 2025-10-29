<link rel="stylesheet" href="/assets/bootstrap/css/bootstrap-table-reorder-rows.css" />

<script src="/assets/bootstrap/js/jquery.tablednd.min.js"></script>
<script src="/assets/bootstrap/js/bootstrap-table-reorder-rows.min.js"></script>
<script src="/assets/bootstrap/js/popper.min.js"></script>


<script src="/assets/bootstrap/js/jquery.validate.min.js"></script>
<?php if ($lang == 'germany') : ?>
	<script src="/assets/js/validation/messages_de.js"></script>
<?php endif ?>
<div class="row">
	<div class="col-12 m-auto pt-3 pb-2 mb-3">
		<div class="card">
			<div class="card-header">
				<h2><?php echo isset($data) ? lang('edit.campaign') : lang('campaign.new') ?></h2>
			</div>
			<div class="card-body">
				<form class="form" id="dataForm">
					<div class="accordion" id="accordionExample">
						<div class="accordion-item">
							<h2 class="accordion-header" id="headingOne">
								<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
									<span id="campaign_name"><?php if (isset($data->name)) echo $data->name; ?></span>
									<sub>&nbsp;<?php if (isset($extended_cnt)) echo " (" . $extended_cnt . " " . lang("extended.campaigns") . (isset($replaced_extended_cnt) ? (" - " . $replaced_extended_cnt . ' ' . lang('with_replaced_media')) : "") .  ")"; ?></sub>
								</button>
							</h2>
							<div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
								<div class="accordion-body">
									<div class="row">
										<div class="col-12">
											<label class="col-form-label required" for="name"><?php echo lang('name'); ?></label>
											<input type="text" class="form-control required" id="name" name="name" required minlength="3" value="<?php if (isset($data->name)) echo $data->name; ?>" />
										</div>


										<div class="col-md-3">
											<label class="col-form-label"> <?php echo lang('customerid'); ?></label>
											<input type="text" class="form-control" name="customer_id" value="<?php if (isset($data->customer_id)) echo $data->customer_id; ?>">
										</div>

										<div class="col-md-3">
											<label class="col-form-label"> <?php echo lang('contractid'); ?></label>
											<input type="text" class="form-control" name="contract_id" value="<?php if (isset($data->contract_id)) echo $data->contract_id; ?>">
										</div>

										<div class="col-md-3">
											<label class="col-form-label"> <?php echo lang('agencyid'); ?></label>
											<input type="text" class="form-control" name="agency_id" value="<?php if (isset($data->agency_id)) echo $data->agency_id; ?>">
										</div>

										<div class="col-md-3">
											<label class="col-form-label"> <?php echo lang('customername'); ?></label>
											<input type="text" class="form-control" name="customer_name" value="<?php if (isset($data->customer_name)) echo $data->customer_name; ?>">
										</div>


										<div class="col-md-6">
											<label class="col-form-label"><?php echo $this->config->item('with_template') ? lang('categories') : lang('tag'); ?></label>
											<select class="form-select select2 disable-for-normal-user" name="tags[]" id="tags" multiple>
												<?php foreach ($tags as $tag) : ?>
													<option value="<?php echo $tag->id; ?>" <?php if (isset($data->tags) && is_array($data->tags) && in_array($tag->id, $data->tags)) : ?>selected<?php endif ?>><?php echo $tag->name; ?></option>
												<?php endforeach; ?>
											</select>
										</div>

										<div class="col-md-6">
											<label class="col-form-label"><?php echo lang('desc'); ?></label>
											<textarea class="form-control" name="descr"><?php if (isset($data->descr)) echo $data->descr; ?></textarea>
										</div>
									</div>
									<div class="accordion pt-1" id="accordionFlushExample">
										<div class="accordion-item">
											<h2 class="accordion-header" id="flush-headingDisplay">
												<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
													<?php echo lang('player') ?>
												</button>
											</h2>
											<div id="flush-collapseOne" class="accordion-collapse" aria-labelledby="flush-headingDisplay">
												<div class="accordion-body">
													<div class="row">

														<div class="col-md-3">
															<label class="col-form-label required"><?php echo lang('priority'); ?></label>
															<select class="form-select form-control disable-for-normal-user" name="priority" id="priority">
																<option value="1" <?php if (isset($data) && $data->priority == "1") : ?>selected="selected" <?php endif; ?>><?php echo lang('priority.high'); ?></option>
																<option value="2" <?php if (isset($data) && $data->priority == "2") : ?>selected="selected" <?php endif; ?>><?php echo lang('priority.low'); ?></option>
																<?php if (($auth == 5 && $pid == 0) || $auth <= 1) : ?>
																	<option value="5" <?php if (isset($data) && $data->priority == "5") : ?>selected="selected" <?php endif; ?>><?php echo lang('priority.reservation'); ?></option>
																	<?php if ($this->config->item('campaign_with_tags')) : ?>
																		<option value="4" <?php if (isset($data) && $data->priority == "4") : ?>selected="selected" <?php endif; ?>><?php echo lang('priority.trail'); ?></option>
																	<?php endif ?>
																<?php endif ?>

																<option value="6" <?php if (isset($data) && $data->priority == "6") : ?>selected="selected" <?php endif; ?>><?php echo lang('priority.simple'); ?></option>
																<?php if (($pid && $shareblock) || !$pid) : ?>
																	<option value="3" <?php if (isset($data) && $data->priority == "3") : ?>selected="selected" <?php endif; ?>><?php echo lang('priority.fillin'); ?></option>
																<?php endif ?>
																<?php if ($auth == 5 && $this->config->item('ssp_feature')) : ?>
																	<option value="7" <?php if (isset($data) && $data->priority == "7") : ?>selected="selected" <?php endif; ?>><?php echo lang('Programmatic.fillIn'); ?></option>
																<?php endif ?>
																<?php if (!isset($extended_cnt)) : ?>
																	<option value="8" <?php if (isset($data) && $data->priority == "8") : ?>selected="selected" <?php endif; ?>><?php echo lang('priority.extension'); ?></option>
																<?php endif; ?>

															</select>
														</div>

														<div class="col-md-3" <?php if (!isset($data) || (isset($data) && ($data->priority != 8))) : ?>style="display:none" <?php endif ?> id="main_campaign_selection">
															<label class="col-form-label required"><?php echo lang('main.campaign'); ?></label>
															<select class="form-select form-control required select2" name="main_campaign_id" id="main_campaign" placeholder="select">
																<option value="0" disabled selected></option>
																<?php foreach ($main_campaigns as $main_campaign) : ?>
																	<option value="<?php echo $main_campaign->id; ?>" <?php if (isset($data) && $data->main_campaign_id == $main_campaign->id) : ?>selected="selected" <?php endif; ?>><?php echo $main_campaign->name; ?></option>
																<?php endforeach; ?>
															</select>
														</div>

														<div class="col-auto" <?php if (!isset($data) || (isset($data) && ($data->priority != 8))) : ?>style="display:none" <?php endif ?> id="main_replace_div">
															<label class="col-form-label">&nbsp;</label>
															<div class="form-check form-switch align-bottom">
																<input class="form-check-input" type="checkbox" id="is_replace_main" <?php if (!isset($data) || (isset($data->is_replace_main) && $data->is_replace_main)) : ?> checked<?php endif; ?>
																	<?php if (($auth == 0 && !$can_replace_main) || ($auth >= 1 && $auth < 5)): ?>disabled<?php endif; ?>>
																<label><?php echo lang('replace_main'); ?></label>
															</div>
														</div>

														<div class="col-md-7 row hide-for-extension" <?php if (isset($data) && ($data->priority == 8)) : ?>style="display:none" <?php endif ?>>
															<div class="col-md-3 play-method-count" <?php if (isset($data) && ($data->priority == 3 || $data->priority == 6)) : ?>style="display:none" <?php endif ?>>
																<label class="col-form-label required"><?php echo lang('play_method'); ?></label>
																<select class="form-select disable-for-normal-user " id="play_cnt_type" name="play_cnt_type" <?php if ($auth <= 1) : ?>readonly <?php endif; ?>>
																	<option value="1" <?php if (isset($data) && $data->play_cnt_type == 1) : ?> selected="selected" <?php endif; ?>><?php echo lang("campaign.count.percent"); ?></option>
																	<option value="2" <?php if (isset($data) && $data->play_cnt_type == 2) : ?> selected="selected" <?php endif; ?>><?php echo lang("campaign.count.total"); ?></option>
																	<option value="0" <?php if (isset($data) && $data->play_cnt_type == 0) : ?> selected="selected" <?php endif; ?>><?php echo lang("campaign.count.number"); ?></option>

																	<?php if ($this->config->item('xslot_on')) : ?>
																		<option value="9" <?php if ($pid) : ?>disabled="disabled" <?php endif ?><?php if (isset($data) && $data->play_cnt_type == 9) : ?> selected="selected" <?php endif; ?>><?php echo lang("campaign.count.xslot"); ?></option>
																	<?php endif; ?>

																</select>
															</div>

															<div class="col-md-2 play-method-count" <?php if (isset($data) && ($data->priority == '3' || $data->priority == '6')) : ?>style="display:none" <?php endif ?>>
																<label class="col-form-label required"><?php echo lang('campaign.count'); ?></label>
																<input type="number" class="form-control disable-for-normal-user" id="play_count" name="play_count" required min=1 value="<?php if (isset($data)) echo $data->play_count; ?>" <?php if ($auth <= 1 || (isset($data) && $data->play_cnt_type == 9)) : ?>readonly <?php endif; ?>>
															</div>


															<div class="col-md-2 play-method-count" <?php if (isset($data) && ($data->priority == '3' || $data->priority == '6')) : ?>style="display:none" <?php endif ?>>
																<label class="col-form-label required"><?php echo lang('booked'); ?></label>
																<input type="number" class="form-control disable-for-normal-user" id="booked" name="booked" required min=1 value="<?php if (isset($data)) echo $data->booked; ?>" <?php if ($auth <= 1 || (isset($data) && $data->play_cnt_type == 9)) : ?>readonly <?php endif; ?>>
															</div>
														</div>

													</div>

													<div class="row">

														<div class="col-md-3">
															<label class="col-form-label required"><?php echo lang('start.date'); ?></label>
															<input type="date" class="form-control disable-for-normal-user readonly-for-extension" id="startDate" name="start_date" value="<?php echo isset($data) ? $data->start_date : date("Y-m-d", time()); ?>" <?php if ($auth <= 1) : ?>disabled <?php endif; ?>>
														</div>

														<div class="col-md-3">
															<label class="col-form-label required"><?php echo lang('end.date'); ?></label>
															<input type="date" class="form-control disable-for-normal-user readonly-for-extension" id="endDate" name="end_date" value="<?php echo isset($data) ? $data->end_date : date("Y-m-d", strtotime('+1 month')); ?>" <?php if ($auth <= 1) : ?>disabled <?php endif; ?>>
														</div>

														<div class="col-6 row">
															<div class="col-auto">
																<label class="col-form-label">&nbsp;</label>
																<div class="form-check form-switch align-bottom">
																	<input class="form-check-input disable-for-normal-user readonly-for-extension" type="checkbox" id="time_flag" <?php if (!isset($data) || (isset($data) && $data->time_flag)) : ?> checked<?php endif; ?> <?php if ($auth <= 1) : ?>disabled <?php endif; ?>>
																	<label><?php echo lang('whole.day'); ?></label>
																</div>
															</div>

															<div class="col row time_range" <?php if (!isset($data) || (isset($data) && $data->time_flag)) : ?>style="display:none" <?php endif ?>>
																<div class="col-auto">
																	<label></label>

																	<select class="form-select disable-for-normal-user readonly-for-extension" name="start_timeH" id="start_timeH">
																		<?php for ($i = 0; $i < 24; $i++) : ?>
																			<option value="<?php echo $i; ?>" <?php if (isset($data) && $i == $data->start_timeH) : ?>selected<?php endif ?>><?php echo sprintf("%02d:00", $i); ?></option>
																		<?php endfor; ?>
																	</select>
																</div>

																<div class="col-auto">
																	<label></label>
																	<select class="form-select disable-for-normal-user readonly-for-extension" name="end_timeH" id="end_timeH">
																		<?php for ($i = 1; $i <= 24; $i++) : ?>
																			<option value="<?php echo $i; ?>" <?php if (isset($data) && $i == $data->end_timeH) : ?>selected="selected" <?php endif; ?>><?php echo $i == 24 ? '00:00(+1)' : sprintf("%02d:00", $i); ?></option>
																		<?php endfor; ?>

																	</select>
																</div>
															</div>
														</div>

													</div>
													<div class="row hide-for-extension">
														<div class="col-md-6 play-method-count" <?php if (isset($data) && ($data->priority == 3 || $data->priority == 6)) : ?>style="display:none" <?php endif ?>>
															<label></label>
															<div class="form-check form-switch">
																<input class="form-check-input disable-for-normal-user readonly-for-extension" type="checkbox" id="is_grouped" <?php if (isset($data) && $data->is_grouped) : ?> checked<?php endif; ?>>
																<label class="form-check-label" for="flexSwitchCheckChecked"><?php echo lang('grouped'); ?></label>
															</div>
														</div>
														<div class="col-auto play-method-count" <?php if (isset($data) && ($data->priority == 3 || $data->priority == 6)) : ?>style="display:none" <?php endif ?>>
															<label></label>
															<div class="form-check form-switch">
																<input class="form-check-input disable-for-normal-user readonly-for-extension" type="checkbox" id="is_locked" <?php if (isset($data) && $data->is_locked) : ?> checked<?php endif; ?>>
																<label class="form-check-label" for="flexSwitchCheckChecked"><?php echo lang('locked'); ?></label>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>

										<div class="accordion-item hide-for-extension">
											<h2 class="accordion-header" id="flush-headingTwo">
												<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
													<?php echo lang('dyna_dclp'); ?>
												</button>
											</h2>
											<div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo">
												<div class="accordion-body">
													<div class="row">

														<div class="col-md-4">
															<label><?php echo lang('criteria'); ?></label>
															<select class="form-select select2 disable-for-normal-user" id="criteria" name="criteria[]" multiple>
																<?php foreach ($criteria as $criterion) : ?>
																	<option value="<?php echo $criterion->id; ?>" <?php if (isset($data) && is_array($data->criteria) && in_array($criterion->id, $data->criteria)) : ?>selected<?php endif ?>><?php echo $criterion->name; ?></option>
																<?php endforeach; ?>
															</select>
														</div>

														<div class="col-md-4">
															<label><?php echo lang('criteria.and'); ?></label>
															<select class="form-select select2 disable-for-normal-user" id="and_criteria" name="and_criteria[]" multiple>
																<?php foreach ($criteria as $criterion) : ?>
																	<option value="<?php echo $criterion->id; ?>" <?php if (isset($data) && is_array($data->and_criteria) && in_array($criterion->id, $data->and_criteria)) : ?>selected<?php endif ?>><?php echo $criterion->name; ?></option>
																<?php endforeach; ?>
															</select>
														</div>
														<div class="col-md-4">
															<label><?php echo lang('criteria.or'); ?></label>
															<select class="form-select select2 disable-for-normal-user" id="and_criteria_or" name="and_criteria_or[]" multiple>
																<?php foreach ($criteria as $criterion) : ?>
																	<option value="<?php echo $criterion->id; ?>" <?php if (isset($data) && is_array($data->and_criteria_or) && in_array($criterion->id, $data->and_criteria_or)) : ?>selected<?php endif ?>><?php echo $criterion->name; ?></option>
																<?php endforeach; ?>
															</select>
														</div>

														<div class="col-md-4">
															<label><?php echo lang('criteria.exclude'); ?></label>
															<select class="form-select select2 disable-for-normal-user" id="ex_criteria" name="ex_criteria[]" multiple>
																<?php foreach ($criteria as $criterion) : ?>
																	<option value="<?php echo $criterion->id; ?>" <?php if (isset($data) && is_array($data->ex_criteria) && in_array($criterion->id, $data->ex_criteria)) : ?>selected<?php endif ?>><?php echo $criterion->name; ?></option>
																<?php endforeach; ?>
															</select>
														</div>

														<!--
														<div class="col-md-2">
															<label></label>
															<div class="form-check form-switch">
																<input class="form-check-input disable-for-normal-user" type="checkbox" <?php if (isset($data) && $data->is_locked) : ?> checked="checked" <?php endif; ?>>
																<label class="form-check-label" for="locked"><?php echo lang('locked'); ?></label>
															</div>
														</div>
																-->

													</div>

												</div>
											</div>
										</div>

										<div class="accordion-item">
											<h2 class="accordion-header" id="flush-headingThree">
												<button class="accordion-button collapsed " type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
													<?php echo lang('individual_dclp'); ?>
												</button>
											</h2>
											<div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree">
												<div class="accordion-body">
													<div class="row">

														<div class="col-auto">
															<button type="button" class="btn btn-outline-primary disable-for-normal-user" <?php if ($auth > 1) : ?> data-bs-toggle="modal" data-bs-target="#playerModal" data-target-field="players" <?php endif ?>>
																<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clipboard-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
																	<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
																	<path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path>
																	<rect x="9" y="3" width="6" height="4" rx="2"></rect>
																	<path d="M10 14h4"></path>
																	<path d="M12 12v4"></path>
																</svg>
																<?php echo lang('dest.player'); ?></button>
														</div>

														<div class="col">
															<select class="form-select select2 disable-for-normal-user" id="players" name="players[]" multiple>
																<?php foreach ($players as $player) : ?>
																	<option value="<?php echo $player->id; ?>" <?php if (isset($data) && is_array($data->players) && in_array($player->id, $data->players)) : ?>selected<?php endif ?>><?php echo $player->name; ?></option>
																<?php endforeach; ?>
															</select>
														</div>

													</div>

													<br>

													<div class="row">

														<div class="col-auto">
															<button type="button" class="btn btn-outline-danger disable-for-normal-user" <?php if ($auth > 1) : ?> data-bs-toggle="modal" data-bs-target="#playerModal" data-target-field="ex_players" <?php endif ?>>
																<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clipboard-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
																	<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
																	<path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path>
																	<rect x="9" y="3" width="6" height="4" rx="2"></rect>
																	<path d="M10 12l4 4m0 -4l-4 4"></path>
																</svg>
																<?php echo lang('exclude.players'); ?></button>
														</div>

														<div class="col">
															<select class="form-select select2 disable-for-normal-user" id="ex_players" name="ex_players[]" multiple>
																<?php foreach ($players as $player) : ?>
																	<option value="<?php echo $player->id; ?>" <?php if (isset($data) && is_array($data->ex_players) && in_array($player->id, $data->ex_players)) : ?>selected<?php endif ?>><?php echo $player->name; ?></option>
																<?php endforeach; ?>
															</select>
														</div>

													</div>


												</div>
											</div>
										</div>
										<div class="accordion-item">
											<h2 class="accordion-header" id="flush-headingFour">
												<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFour" aria-expanded="false" aria-controls="flush-collapseFour">
													<?php echo lang('internal_features'); ?>
												</button>
											</h2>
											<div id="flush-collapseFour" class="accordion-collapse collapse" aria-labelledby="flush-headingFour" data-bs-parent="#accordionFlushExample">
												<div class="accordion-body">
													<div class="row">

														<div class="col-md-3">
															<label><?php echo lang("contactname"); ?></label>
															<input type="text" class="form-control" name="contact_name" value="<?php if (isset($data)) echo $data->contact_name; ?>">
														</div>

														<div class="col-md-3">
															<label><?php echo lang("customertype"); ?></label>
															<select name="cust_type" class="form-select">
																<option value="0" <?php if (isset($data) && $data->cust_type == "0") : ?>selected <?php endif; ?>><?php echo lang('customertype.own'); ?></option>
																<option value="1" <?php if (isset($data) && $data->cust_type == "1") : ?>selected<?php endif; ?>><?php echo lang('customertype.local'); ?></option>
																<option value="2" <?php if (isset($data) && $data->cust_type == "2") : ?>selected<?php endif; ?>><?php echo lang('customertype.national'); ?></option>
																<option value="3" <?php if (isset($data) && $data->cust_type == "3") : ?>selected<?php endif; ?>><?php echo lang('customertype.external'); ?></option>

															</select>
														</div>

														<div class="col-md-2">
															<label><?php echo lang("campaignvalue"); ?></label>
															<input type="text" class="form-control" name="cam_value" value="<?php if (isset($data)) echo $data->cam_value; ?>">
														</div>



													</div>

												</div>
											</div>
										</div>

									</div>
								</div>


							</div>
						</div>


						<div class="accordion-item">
							<h2 class="accordion-header" id="headingTwo">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
									<?php echo lang('media') ?> (<span id="total_media">0</span>)
								</button>
							</h2>
							<div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
								<div class="accordion-body">

									<div class="row float-end btn-list g-1 pb-1">
										<div class="col-auto">
											<button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#mediaModal">
												<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-movie" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
													<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
													<rect x="4" y="4" width="16" height="16" rx="2"></rect>
													<line x1="8" y1="4" x2="8" y2="20"></line>
													<line x1="16" y1="4" x2="16" y2="20"></line>
													<line x1="4" y1="8" x2="8" y2="8"></line>
													<line x1="4" y1="16" x2="8" y2="16"></line>
													<line x1="4" y1="12" x2="20" y2="12"></line>
													<line x1="16" y1="8" x2="20" y2="8"></line>
													<line x1="16" y1="16" x2="20" y2="16"></line>
												</svg>
												<?php echo lang('media') ?>
											</button>
										</div>

										<?php if ($auth == 5 && !$pid) : ?>
											<div class="col-auto">
												<button class="btn btn-outline-primary" type="button" title="upload" data-bs-toggle="modal" data-bs-target="#uploadModal" data-target-campaign="true">
													<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cloud-upload" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
														<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
														<path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1"></path>
														<polyline points="9 15 12 12 15 15"></polyline>
														<line x1="12" y1="12" x2="12" y2="21"></line>
													</svg>
													<?php echo lang('button.upload') ?>
												</button>
											</div>
										<?php endif ?>
										<div class="col-auto">
											<button class="btn btn-outline-primary" type="button" onclick="delete_all_media()" title="<?php echo lang('delete') ?>">
												<svg xmlns=" http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
													<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
													<line x1="18" y1="6" x2="6" y2="18"></line>
													<line x1="6" y1="6" x2="18" y2="18"></line>
												</svg>
												<?php echo lang('delete') ?>
											</button>
										</div>
									</div>

									<table id="areaMediaTable" class="table table-responsive table-vcenter" data-pagination='false' data-use-row-attr-func="true" data-reorderable-rows="true">
										<thead>
											<tr>
												<th data-field="tiny_url" data-formatter="previewFormatter"><?php echo lang('media.image') ?></th>
												<th data-field="name" data-formatter="mediaNameFormatter"><?php echo lang('media_name'); ?></th>
												<th data-field="play_time"><?php echo lang('play_time'); ?></th>
												<th data-field="transmode" data-formatter="transFormatter"><?php echo lang('transition_mode'); ?></th>
												<th data-field="status" data-formatter="excludeFormatter">Exclude</th>
												<th data-field="start_date" data-formatter="dateFormatter"><?php echo lang('start.date'); ?></th>
												<th data-field="end_date" data-formatter="dateFormatter"><?php echo lang('end.date'); ?></th>
												<th data-formatter="operateFormatter"><?php echo lang('operate'); ?></th>
											</tr>
										</thead>
									</table>
								</div>
							</div>
						</div>

						<div class="accordion-item" id="accordion-players" style="display:none;">
							<h2 class="accordion-header" id="headingPlayers">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePlayers" aria-expanded="false" aria-controls="collapseThree">
									<?php echo lang('cal.result') ?>
								</button>
							</h2>
							<div id="collapsePlayers" class="accordion-collapse collapse" aria-labelledby="headingPlayers" data-bs-parent="#accordionExample">
								<div class="accordion-body" id='affected-players'>

								</div>
							</div>
						</div>

					</div>

					<div class="card-footer">
						<div class="col-12">
							<?php if (!isset($readonly) || (isset($readonly) && $readonly == 0)) : ?>
								<?php if ($can_publish == 1) : ?>
									<a class="btn btn-outline-primary" id="publish_button" href="#" onclick="doSubmit(1);">
										<i class="bi bi-play-btn"></i>
										<?php echo lang('button.publish'); ?>
									</a>
								<?php endif; ?>
								<a class="btn btn-outline-primary" href="#" onclick="doSubmit(0);">
									<i class="bi bi-cloud-arrow-up"></i>
									<?php echo lang('button.save'); ?>
								</a>

								<a class="btn btn-outline-primary play-method-count" href="#" onclick="doCalculate();" <?php if (isset($data) && ($data->priority == '3' || $data->priority == '6')) : ?>style="display:none" <?php endif ?>>
									<i class="bi bi-calculator"></i>
									<?php echo lang('btn.calculate'); ?>
								</a>
								<?php if (isset($data)) : ?>
									<a class="btn btn-outline-primary" onclick="exportReport()">
										<i class="bi bi-clipboard-data"></i>
										<?php echo lang('playback'); ?>
									</a>
								<?php endif; ?>
							<?php endif; ?>
							<a class="btn btn-outline-primary" type='btn' href="/campaign"><i class="bi bi-x-circle"></i><?php echo lang('button.cancel'); ?></a>
						</div>
					</div>
					<input type="hidden" id="isPublish" value='0' ?>
					<input type="hidden" id="id" name="id" value="<?php echo isset($data->id) ? $data->id : 0; ?>" />
				</form>
			</div>
		</div>
	</div>
	<?php
	$this->load->view("bootstrap/players/player_map");
	$this->load->view("bootstrap/media/uploader");
	$this->load->view("bootstrap/media/media_modal");
	$this->load->view("bootstrap/media/preview_modal");
	?>


	<div class="modal fade" id="areaMediaModal" role="dialog" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<label class="form-label"><?php echo lang('transition_mode') ?></label>

					<div class="form-selectgroup">
						<?php for ($i = 0; $i < 28; $i++) : ?>
							<label class="form-selectgroup-item">
								<input type="radio" name="transition" id="<?php echo 'transition-' . $i ?>" value="<?php echo $i ?>" class="form-selectgroup-input">
								<span class="form-selectgroup-label">
									<img src="/assets/img/transfer/Transfer_Mode_<?php echo sprintf("%02d", $i) ?>.png">
								</span>
							</label>
						<?php endfor; ?>

					</div>
					<input type="hidden" id="area_media_id" />
				</div>
				<div class="modal-footer">
					<button type="button" class="btn me-auto" data-bs-dismiss="modal"><?php echo lang('button.cancel') ?></button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="update-areaMedia"><?php echo lang('button.save') ?></button>
				</div>
			</div>
		</div>
	</div>

</div>

<script type="text/javascript">
	var myCollapse = $("#collapsePlayers");
	var bsCollapse = new bootstrap.Collapse(myCollapse, {
		show: false,
		toggle: false,
		hide: false,
	});

	function readonly_select(objs, action) {
		if (action === true)
			objs.prepend('<div class="disabled-select"></div>');
		else
			$(".disabled-select", objs).remove();
	}

	var media_data = [];
	<?php if (isset($media)) : ?>
		var media = eval(<?php echo $media ?>);
		media_data = media.data;
	<?php endif ?>




	function previewFormatter(value, row) {
		var source = '';
		switch (row.source) {
			case '0':
				source = "<?php echo lang('local'); ?>";
				break;
			case '1':
				source = "<?php echo lang('ftp'); ?>";
				break;
			case '2':
				source = "<?php echo lang('http'); ?>";
				break;
		}

		var file_size = fileSizeSI(row.file_size);

		var tooltips = `<ul class="list-group align-items-start pl-0" style="white-space: nowrap">
			<li class="list-group-item text-white border-0 py-0" >
				<?php echo lang("author") ?>
				<span>${row.author?row.author:''}</span>
			</li>
			<li class="list-group-item text-white border-0 py-0" >
				<?php echo lang("upload.date") ?>
				${row.add_time}
			</li>
			<li class="list-group-item text-white border-0 py-0" >
				<?php echo lang("file.size") ?>
				<span>${file_size}</span>
			</li>
			<li class="list-group-item text-white border-0 py-0">
				<?php echo lang("file.ext") ?>
				<span>${row.ext}</span>
			</li>
			<li class="list-group-item text-white border-0 py-0">
				<?php echo lang("source") ?>
				<span>${source}</span>
			</li>
			<li class="list-group-item text-white border-0 py-0">
				<?php echo lang("dimension") ?>
				<span>${row.width}X${row.height}</span>
			</li>
			<li class="list-group-item text-white border-0 py-0">
				<?php echo lang("folder") ?>
				<span>${(row.folder_id=='0')?"<?php lang('folder.default') ?>":row.folder_name}</span>
			</li>
			<li class="list-group-item text-white border-0 py-0">
				Media ID
				<span>${row.id}</span>
			</li>
			<li class="list-group-item text-white border-0">
				${row.name}
			</li>
		</ul>`;

		return `<span class="d-inline-block cursor-pointer" tabindex="0" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-html="true"  data-bs-container="body" title='${tooltips}'>
		<img src="${value}" class="rounded" style="max-width:90px; max-height:90px" onerror="javascript:this.remove()" data-bs-toggle="modal" data-bs-target="#modal-medium-preview" data-bs-mediumId="${row.id}"/>
		</span>`;
	}

	function mediaNameFormatter(value, row) {
		return `<a href="#" class="link-primary" onclick="editAreaMedia('${row.id}','${row.transmode}');">
				${value}
			</a>`;
	}

	function operateFormatter(value, row) {

		return `<div class="btn-list flex-nowrap">
			<a href="#" class="link-danger removeRowButton">
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square" viewBox="0 0 16 16">
				<path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
				<path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
			</svg>
			</a>
		</div>`;
	};

	function excludeFormatter(value, row) {
		if (value == 1) {
			return `<label class="form-check form-switch">
				<input class="form-check-input excludeCheck" checked type="checkbox"/>
			</label>`
		} else {
			return `<label class="form-check form-switch">
				<input class="form-check-input excludeCheck" type="checkbox" />
			</label>`
		}
	};

	function transFormatter(value, row) {
		if (row.transmode == -1 || row.media_type == 2) {
			return "--";
		} else {
			html = `<img src="/assets/img/transfer/Transfer_Mode_${row.transmode}.png" width="32" height="24" />`;
			return html;
		}
	}

	function dateFormatter(value, row) {
		if (row.date_flag == "1") {
			return value;
		}
		return '';
	}

	$("#time_flag").change(function() {
		if ($("#time_flag").is(':checked')) {
			$('.time_range').hide();
		} else {
			$('.time_range').show();
		}
	});

	$('#priority').change(function() {

		const priority = $(this).val();
		if (priority == '3' || priority == '6') {
			$(".play-method-count").hide();
		} else {
			$(".play-method-count").show();
		}

		if (priority == '8') {
			$('#main_campaign_selection').show();
			$('#main_replace_div').show();
			$('.hide-for-extension').hide();
			$('.readonly-for-extension').attr("disabled", true);
			$('#criteria').val('');
			$('#ex_criteria').val('');
			$('#and_criteria').val('');
			$('#and_criteria_or').val('');
			$('#players').val('');

		} else {
			$('.readonly-for-extension').removeAttr("disabled");
			$('.hide-for-extension').show();
			$('#main_campaign_selection').hide();
			$('#main_replace_div').hide();
		}

	});

	$('#main_campaign').change(function() {
		const main_campaign = $(this).val();
		get_campaign(main_campaign, false, false);
	});

	function get_campaign(cam_id, players, ex_players) {
		$('#criteria').empty();
		$('#and_criteria').empty();
		$('#and_criteria_or').empty();
		$('#ex_criteria').empty();
		$('#players').empty();
		$('#ex_players').empty();
		fetch('/campaign/get_selected_campaign_data?cam_id=' + cam_id)
			.then(response => response.json())
			.then(data => {
				//$('#main_campaign').empty();
				$('#startDate').val(data.start_date);
				$('#endDate').val(data.end_date);
				$('#time_flag').prop('checked', data.time_flag == 1);
				$('#play_cnt_type').val(data.play_cnt_type);
				$('#play_count').val(data.play_count);
				if (!data.booked) {
					$('#booked').val(data.play_count);
				} else {
					$('#booked').val(data.booked);
				}
				//set the check status of the time flag
				if (data.time_flag == 1) {
					$('.time_range').hide();
				} else {
					$('.time_range').show();
					$('#start_timeH').val(data.start_timeH);
					$('#end_timeH').val(data.end_timeH);
				}
				$('#criteria').empty();
				$('#and_criteria').empty();
				$('#and_criteria_or').empty();
				$('#ex_criteria').empty();
				$('#players').empty();
				$('#ex_players').empty();
				$('#is_locked').prop('checked', data.is_locked == 1);
				$('#is_grouped').prop('checked', data.is_grouped == 1);
				var players = data.players;

				if (data.published == 1) {
					$('#publish_button').show();
				} else {
					$('#publish_button').hide();
				}

				players.forEach(item => {
					$('#players').append(`<option value="${item.id}">${item.name}</option>`);
					$('#ex_players').append(`<option value="${item.id}">${item.name}</option>`);
				});
				if (players) {
					$('#players').val(players);
				}
				if (ex_players) {
					$('#ex_players').val(ex_players);
				}
			});
	}

	$("#play_cnt_type").change(function() {
		var play_count = $('#play_count');
		var booked = $('#booked');

		play_count.attr('min', 1);
		booked.attr('min', 1);

		<?php if ($auth <= 1) : ?>
			play_count.attr("readonly", "readonly");
			booked.hide();
		<?php else : ?>
			play_count.removeAttr("readonly");
			booked.show();
		<?php endif ?>

		if ($(this).val() == '9') {
			play_count.attr("readonly", "readonly");
			play_count.val('<?php echo $nxslot ?>')
			booked.attr("readonly", "readonly");
			booked.val('<?php echo $nxslot ?>')
		} else {
			play_count.removeAttr("readonly");
			play_count.val('');
			booked.removeAttr("readonly");
			booked.val('');
		}

	});

	$(document).on('click', '.removeRowButton', function() {
		let rowid = $(this).closest('tr').data('index');

		var table = $('#areaMediaTable');
		table.bootstrapTable('remove', {
			field: '$index',
			values: [rowid]
		});
	});

	$(document).on('click', '.excludeCheck', function() {
		checked = $(this).is(':checked') ? "1" : "0";
		let rowid = $(this).closest('tr').data('index');
		$('#areaMediaTable').bootstrapTable('updateCell', {
			index: rowid,
			field: "status",
			value: checked
		});
	});

	function delete_all_media() {

		var table = $('#areaMediaTable');
		table.bootstrapTable('removeAll')
	};


	function editAreaMedia(id, transmode) {
		var myModal = new bootstrap.Modal(document.getElementById('areaMediaModal'), {
			keyboard: true
		});

		$('#transition-' + transmode).attr("checked", "true");
		$('#area_media_id').val(id);

		myModal.show();
	}

	$('#update-areaMedia').on('click', function() {
		var transMode = "26";

		$('.form-selectgroup-input').each(function() {
			if ($(this).is(':checked')) {
				transMode = $(this).val();
			}
		});

		id = $('#area_media_id').val();

		$('#areaMediaTable').bootstrapTable('updateCellByUniqueId', {
			id: id,
			field: "transmode",
			value: transMode
		});
	});


	function doSubmit(isPublish) {
		$('#isPublish').val(isPublish);
		$('#dataForm').submit();
	};

	function doCalculate() {
		<?php if ($auth <= 1) : ?>
			$('.disable-for-normal-user').removeAttr("disabled");
		<?php endif; ?>
		var params = new FormData($('#dataForm')[0]);
		<?php if ($auth <= 1) : ?>
			$('.disable-for-normal-user').attr("disabled", true);
		<?php endif; ?>
		params.append("time_flag", $("#time_flag").is(':checked') ? 1 : 0);
		params.append("is_locked", $("#is_locked").is(':checked') ? 1 : 0);
		params.append("is_grouped", $("#is_grouped").is(':checked') ? 1 : 0);

		$.ajax({
			url: '/campaign/do_calculate',
			type: 'POST',
			data: params,
			dataType: "json",
			success: function(res) {
				$('#accordion-players').show();
				$("#affected-players").html(res.data);

				bsCollapse.show();
			},
			error: function(data, type, err) {
				console.log(err);
			},

			cache: false,
			contentType: false,
			processData: false
		});

	};

	function exportReport() {
		var req = '?start_date=' + $('#startDate').val() + '&end_date=' + $('#endDate').val();
		req += '&campaign=' + $('#name').val();

		window.location.href = '/playback/excel' + req;
	};

	$("input#name").on('keydown paste input', function() {
		$('#campaign_name').text($('#name').val());

	});

	$('#select-media').on('click', function() {
		{
			var selections = mediaTable.bootstrapTable('getSelections');
			if (selections.length == 0) {
				return;
			}

			let media = selections.map((item) => {
				return {
					id: item.id,
					name: item.name,
					play_time: item.play_time,
					transmode: 26,
					status: 0,
					media_type: item.media_type,
					date_flag: item.date_flag,
					start_date: item.start_date,
					end_date: item.end_date,
					tiny_url: item.tiny_url,
					width: item.width,
					height: item.height,
					folder_name: item.folder_name,
					author: item.author,
					add_time: item.add_time,
					ext: item.ext,
					file_size: item.file_size,
					approved: item.approved,
				}
			});

			$('#areaMediaTable').bootstrapTable('append', media);
			$('#close-media-modal').click();
		}
	});


	async function saveCampaign($url, $params) {
		try {
			const request = await fetch($url, {
				method: 'POST',
				body: $params
			});
			const response = await request.json();

			hideSpinner();
			if (response.code == 0) {
				localStorage.setItem("Status", JSON.stringify({
					type: 'success',
					message: response.msg
				}));
				window.location.href = '/campaign';

			} else {
				if (response.id) {
					$('#id').val(response.id)
				}
				toastr.error(response.msg);
			}
		} catch (error) {
			hideSpinner();
			console.log(error);
			toastr.error("Unexpected error,Please contact the administrator");

		}
	}

	$(document).ready(function() {


		<?php if (isset($data->name)) : ?>
			$('#name').val(`<?php echo $data->name; ?>`);
		<?php endif ?>

		$('#areaMediaTable').bootstrapTable({
			data: media_data,
			uniqueId: "id",
			onAll: function(name, args) {
				if (name == 'post-body.bs.table') {
					var total = $('#areaMediaTable').bootstrapTable('getData').length;
					$('#total_media').html(total);
				}
			}
		});

		$("#dataForm").validate({
			rules: {
				name: {
					required: true,
					remote: {
						url: "/campaign/checkName",
						data: {
							name: function() {
								return $("#name").val();
							},
							id: function() {
								return $("#id").val();
							},
						}
					}
				},
				start_date: {
					required: true,
					dateISO: true,
				},
				end_date: {
					required: true,
					dateISO: true,
					greaterOrEqualThan: "start_date"
				},
				play_count: {
					required: true,
					min: 1,
					max: {
						param: 100,
						depends: function(element) {
							return $('#play_cnt_type').val() == '1';
						}
					}
				},
				booked: {
					required: true,
					min: 1,
					max: {
						param: 100,
						depends: function(element) {
							return $('#play_cnt_type').val() == '1';
						}
					}
				},
				main_campaign_id: {
					required: function(element) {
						return $('#priority').val() == 8 && $('#main_campaign_id').val() == 0;
					},

				},
			},
			submitHandler: function(form) {
				var isExtension = $('#priority').val() == 8 ? true : false;
				<?php if ($auth <= 1) : ?>
					$('.disable-for-normal-user').removeAttr("disabled");
				<?php endif ?>
				if (isExtension) {
					$('.readonly-for-extension').removeAttr("disabled");
				}
				var params = new FormData($('#dataForm')[0]);
				<?php if ($auth <= 1) : ?>
					$('.disable-for-normal-user').attr("disabled", true);
				<?php endif; ?>
				if (isExtension) {
					$('.readonly-for-extension').attr("disabled", true);
				}

				params.append("time_flag", $("#time_flag").is(':checked') ? 1 : 0);
				params.append("is_locked", $("#is_locked").is(':checked') ? 1 : 0);
				params.append("is_grouped", $("#is_grouped").is(':checked') ? 1 : 0);
				params.append("is_replace_main", $("#is_replace_main").is(':checked') ? 1 : 0);

				var ob_ids = [];
				params.delete('obIds');
				$('input:checkbox[name="obIds"]').each(function() {
					if (this.checked) {
						ob_ids.push(this.value);
					}
				});
				if (ob_ids.length) {
					params.append("ob_ids", JSON.stringify(ob_ids));
				}


				var media = $('#areaMediaTable').bootstrapTable('getData');

				let media_ids = media.map((item) => {
					return {
						media_id: item.id,
						status: item.status,
						transmode: item.transmode,
						media_type: item.media_type,
						approved: item.approved,
						area_media_id: item.area_media_id
					}
				});

				params.append("media", JSON.stringify(media_ids));
				showSpinner();
				var url = '/campaign/do_save';
				if ($('#isPublish').val() == '1') {
					url = '/campaign/do_publish'
				}
				saveCampaign(url, params);

			},


		});
		$(".select2").select2({
			theme: "bootstrap-5",
			width: '100%',
			allowClear: true,
			placeholder: "",
		});

		<?php if (isset($data) && $data->priority == 8): ?>
			$('#main_campaign_selection').show();
			$('.hide-for-extension').hide();
			$('.readonly-for-extension').attr("disabled", true);
			<?php if (isset($is_main_campaign_published) && $is_main_campaign_published == 1): ?>
				$('#publish_button').show();
			<?php else: ?>
				$('#publish_button').hide();
			<?php endif; ?>

		<?php endif ?>

		<?php if ($auth <= 1) : ?>
			$('.disable-for-normal-user').attr("disabled", true);
		<?php endif ?>
	});
</script>