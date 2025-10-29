<head>
	<link rel="stylesheet" type="text/css" href="/static/css/jquery/jquery.fancybox.min.css">
	<link rel="stylesheet" href="/static/css/jquery/chosen.min.css" />
	<link rel="stylesheet" href="/static/css/alertify.core.css" />
	<link rel="stylesheet" href="/static/css/alertify.default.css" />

	<script src="/static/js/jquery/jquery.fancybox.min.js"></script>
	<script src="/static/js/alertify.min.js" type="text/javascript" charset="utf-8"></script>
	<script src='/static/js/jquery/chosen.jquery.min.js'></script>

	<style>
		.flex {
			display: flex;
		}

		.item {
			flex: 1;

			&+.item {
				margin-left: 10px;
			}

			margin: 5px;

		}

		.flex-20 {
			flex-basis: 20%;


		}

		.flex-70 {
			flex-basis: 70%;
		}
	</style>
</head>

<div class='flex'>

	<div class="item flex-70">

		<div class="icon-list">
			<ul>
				<li><img id="<?php echo $area->id; ?>" type="movie" src="/images/icons/42-01.gif" width="42" height="32" alt="" title="<?php echo $area->name; ?>" /></li>

			</ul>
		</div>
		<div class="clear"></div>
		<div id="publishing" class="information" style="display:none;margin:10;width:98%;">
			<?php echo lang('publishing'); ?>
		</div>

		<div class="tab-area" id="content_<?php echo $area->id; ?>">
			<?php
			if (isset($body_view)) {
				$this->load->view($body_view);
			}
			?>

		</div>
		<div>
			<p class="btn-center">
				<input type="hidden" id="playlistId" name="playlistId" value="<?php echo $playlist->id; ?>" />

				<?php if ($can_publish) : ?>
					<a href="javascript:void(0);" id="publish" onclick="campaign.publishPlaylist('<?php echo lang("warn.publish.empty.media") ?>',<?php echo 'false'; ?>);" class="btn-01"><span style="color:red;"><?php echo lang('button.publish'); ?></span></a>
					<a href="javascript:void(0);" id="save" onclick="campaign.savePlaylist();" class="btn-01"><span><?php echo lang('button.save'); ?></span></a>
					<a href="/campaign/" class="btn-01"><span><?php echo lang('button.return'); ?></span></a>
				<?php else : ?>

					<a href="javascript:void(0);" id="save" onclick="campaign.savePlaylist();" class="btn-01"><span><?php echo lang('button.save'); ?></span></a>
					<a href="/campaign/" class="btn-01"><span><?php echo lang('button.return'); ?></span></a>
				<?php endif; ?>
			</p>
		</div>


	</div>
	<div class="item flex-20 gray-area from-panel">
		<table class="from-panel">
			<tbody>
				<tr>
					<td width="80">
						<?php echo lang('campaign') . ' ' . lang('name'); ?>
					</td>
					<td>
						<input type="text" id="name" name="name" class="text ui-widget-content ui-corner-all" style="width:200px;" value="<?php echo $playlist->name; ?>" />
					</td>
					<td>
						<div class="attention" id="errorName" style="display:none;">
							<?php echo lang('warn.campaign.name'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="120">
						<?php echo lang('customerid'); ?>
					</td>
					<td>
						<input type="text" id="customerid" name="customerid" class="text ui-widget-content ui-corner-all" style="width:200px;" value="<?php echo $playlist->customer_id; ?>" />
					</td>
				</tr>
				<tr>
					<td width="120">
						<?php echo lang('contractid'); ?>
					</td>
					<td>
						<input type="text" id="contractid" name="contractid" class="text ui-widget-content ui-corner-all" style="width:200px;" value="<?php echo $playlist->contract_id; ?>" />
					</td>
				</tr>
				<tr>
					<td width="120">
						<?php echo lang('agencyid'); ?>
					</td>
					<td>
						<input type="text" id="agencyid" name="agencyid" class="text ui-widget-content ui-corner-all" style="width:200px;" value="<?php echo $playlist->agency_id; ?>" />
					</td>
				</tr>
				<tr>
					<td width="120">
						<?php echo lang('customername'); ?>
					</td>
					<td>
						<input type="text" id="customername" name="customername" class="text ui-widget-content ui-corner-all" style="width:200px;" value="<?php echo $playlist->customer_name; ?>" />
					</td>
				</tr>
				<tr>
					<td>
						<?php echo lang("desc"); ?>
					</td>
					<td>
						<textarea name="descr" id="descr" class="ui-widget-content ui-corner-all" rows="2" style="width:200px;"><?php echo $playlist->descr; ?></textarea>
					</td>
					<td>
						&nbsp;
					</td>
				</tr>

				<tr>
					<td>
						<?php echo lang('priority'); ?>
					</td>
					<td>
						<select name="" id="priority-options" style="width: 150px" onchange="campaign.selchange(this);" <?php if ($auth <= 1) : ?>disabled <?php endif; ?>>
							<option value="1" <?php if ($playlist->priority == "1") : ?>selected="selected" <?php endif; ?>><?php echo lang('priority.high'); ?></option>
							<option value="2" <?php if ($playlist->priority == "2") : ?>selected="selected" <?php endif; ?>><?php echo lang('priority.low'); ?></option>
							<?php if (($auth == 5 && $pid == 0) || $auth <= 1) : ?>
								<option value="5" <?php if ($playlist->priority == "5") : ?>selected="selected" <?php endif; ?>><?php echo lang('priority.reservation'); ?></option>
								<?php if ($this->config->item('campaign_with_tags')) : ?>
									<option value="4" <?php if ($playlist->priority == "4") : ?>selected="selected" <?php endif; ?>><?php echo lang('priority.trail'); ?></option>
								<?php endif ?>
							<?php endif ?>

							<option value="6" <?php if ($playlist->priority == "6") : ?>selected="selected" <?php endif; ?>><?php echo lang('priority.simple'); ?></option>
							<?php if (($pid && $shareblock) || !$pid) : ?>
								<option value="3" <?php if ($playlist->priority == "3") : ?>selected="selected" <?php endif; ?>><?php echo lang('priority.fillin'); ?></option>
						</select>
					<?php endif ?>
					</td>
				</tr>
				<tr class="playcount">
					<td>
						<?php echo lang('play_method'); ?>
					</td>

					<td>
						<select id="playcnttype" style="width:120px;" onchange="campaign.typechange(this.value)" <?php if ($auth <= 1) : ?>disabled <?php endif; ?>>

							<option value="1" <?php if ($playlist->play_cnt_type == 1) : ?> selected="selected" <?php endif; ?>><?php echo lang("campaign.count.percent"); ?></option>
							<option value="2" <?php if ($playlist->play_cnt_type == 2) : ?> selected="selected" <?php endif; ?>><?php echo lang("campaign.count.total"); ?></option>
							<option value="0" <?php if ($playlist->play_cnt_type == 0) : ?> selected="selected" <?php endif; ?>><?php echo lang("campaign.count.number"); ?></option>
							<?php if (!$pid) : ?>
								<?php if ($this->config->item('xslot_on')) : ?>
									<option value="9" <?php if ($playlist->play_cnt_type == 9) : ?> selected="selected" <?php endif; ?>><?php echo lang("campaign.count.xslot"); ?></option>
								<?php endif; ?>
							<?php endif; ?>
						</select>

						<input <?php if ($playlist->play_cnt_type != 0) : ?> style="display:none;width:60px;" <?php else : ?> style="width:60px;" <?php endif; ?> type="number" id="playcountid" class="text ui-widget-content ui-corner-all" min="1" value=<?php echo $playlist->play_count; ?> <?php if ($auth <= 1) : ?>readonly <?php endif; ?> />
						<input <?php if ($playlist->play_cnt_type != 1) : ?> style="display:none;width:60px;" <?php else : ?> style="width:60px;" <?php endif; ?> type="number" id="playweightid" class="text ui-widget-content ui-corner-all" min="1" max="100" value=<?php echo $playlist->play_weight; ?> <?php if ($auth <= 1) : ?>readonly <?php endif; ?> />
						<input <?php if ($playlist->play_cnt_type != 2) : ?> style="display:none;width:60px;" <?php else : ?> style="width:60px;" <?php endif; ?> type="number" id="playtotalid" class="text ui-widget-content ui-corner-all" min="1" value=<?php echo $playlist->play_total; ?> <?php if ($auth <= 1) : ?>readonly <?php endif; ?> />
						<?php if ($this->config->item('xslot_on')) : ?>
							<input <?php if ($playlist->play_cnt_type != 9) : ?> style="display:none;width:60px;" <?php else : ?> style="width:60px;" <?php endif; ?> type="number" id="xslotid" class="text ui-widget-content ui-corner-all" min="1" value=<?php echo $playlist->nxslot; ?> readonly disabled="disabled" />
						<?php endif; ?>


					</td>
				</tr>
				<tr>
					<td><?php echo lang('date.range'); ?></td>

					<td>
						<input type="text" style="width:80px;" id="startDate" name="startDate" readonly="readonly" class="date-input" value="<?php echo $playlist->start_date; ?>" <?php if ($auth <= 1) : ?>disabled <?php endif; ?> />
						<em><?php echo lang('to'); ?></em>
						<input type="text" style="width:80px;" id="endDate" name="endDate" readonly="readonly" class="date-input" value="<?php echo $playlist->end_date; ?>" <?php if ($auth <= 1) : ?>disabled <?php endif; ?> />
					</td>
				</tr>

				<tr class="time-selection">
					<td><input type="checkbox" id="alldayFlag" name="alldayFlag" <?php if ($playlist->time_flag) {
																						echo 'checked="checked" value="1"';
																					} else {
																						echo 'value="0"';
																					} ?> <?php if ($auth <= 1) : ?>disabled <?php endif; ?> /><?php echo lang('all'); ?>&nbsp;<?php echo lang('day'); ?></td>
					<td>


						<select style="width: 70px" class="time-input" id="startTH" <?php if ($playlist->time_flag || $auth <= 1) {
																						echo "disabled='disabled'";
																					} ?>>
							<?php for ($i = 0; $i < 24; $i++) : ?>
								<option value="<?php echo $i; ?>" <?php if ($i == $playlist->start_timeH) : ?>selected="selected" <?php endif; ?>><?php echo sprintf("%02d:00", $i); ?></option>
							<?php endfor; ?>
						</select>

						<em>-</em>

						<select style="width: 70px" class="time-input" id="stopTH" <?php if ($playlist->time_flag || $auth <= 1) {
																						echo "disabled='disabled'";
																					} ?> onchange="campaign.minchange(this);">
							<?php for ($i = 0; $i < 24; $i++) : ?>
								<option value="<?php echo $i; ?>" <?php if ($i == $playlist->end_timeH) : ?>selected="selected" <?php endif; ?>><?php echo sprintf("%02d:00", $i); ?></option>
							<?php endfor; ?>
							<option value="24" <?php if (24 == $playlist->end_timeH) : ?>selected="selected" <?php endif; ?>>00:00(+1)</option>
						</select>

					</td>


				</tr>


				<tr>
					<td width="80">
						<?php echo lang('criteria'); ?>
					</td>
					<td>
						<select data-placeholder="Choose Criteria..." id="criteria-select-options" class="chosen-select tag-input-style" multiple <?php if ($auth <= 1) : ?>disabled <?php endif; ?>>
							<option value="0"></option>
							<?php foreach ($criterias as $crit) : ?>
								<option value="<?php echo $crit->id; ?>" <?php $tagary = explode(',', $playlist->criterias);
																			if (is_array($tagary) && in_array($crit->id, $tagary)) : ?>selected<?php endif; ?>><?php echo $crit->name; ?></option>
							<?php endforeach; ?>
						</select>



					</td>
				</tr>
				<?php if ($this->config->item('xslot_on')) : ?>
					<tr>
						<td>
							<?php echo lang('campaign.and'); ?>
						</td>
						<td>
							<select data-placeholder="bind with criteria(and)" id="criteria-and-select-options" class="chosen-select tag-input-style" multiple <?php if ($auth <= 1) : ?>disabled<?php endif; ?>>
								<option value="0"></option>
								<?php foreach ($criterias as $crit) : ?>
									<option value="<?php echo $crit->id; ?>" <?php $tagary = explode(',', $playlist->and_criterias);
																				if (is_array($tagary) && in_array($crit->id, $tagary)) : ?>selected<?php endif; ?>><?php echo $crit->name; ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang('campaign.and.or'); ?>
						</td>
						<td>
							<select data-placeholder="bind with criteria(or)" id="criteria-and-select-options-or" class="chosen-select tag-input-style" multiple <?php if ($auth <= 1) : ?>disabled<?php endif; ?>>
								<option value="0"></option>
								<?php foreach ($criterias as $crit) : ?>
									<option value="<?php echo $crit->id; ?>" <?php $tagary = explode(',', $playlist->and_criteria_or);
																				if (is_array($tagary) && in_array($crit->id, $tagary)) : ?>selected<?php endif; ?>><?php echo $crit->name; ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				<?php endif ?>
				<tr>
					<td>
						<?php echo lang('exclude.criteria'); ?>
					</td>
					<td>
						<select data-placeholder="Choose Exclude Criteria..." id="criteria-ex-select-options" class="chosen-select tag-input-style" multiple <?php if ($auth <= 1) : ?>disabled <?php endif; ?>>
							<option value="0"></option>
							<?php foreach ($criterias as $crit) : ?>
								<option value="<?php echo $crit->id; ?>" <?php $tagary = explode(',', $playlist->ex_criterias);
																			if (is_array($tagary) && in_array($crit->id, $tagary)) : ?>selected<?php endif; ?>><?php echo $crit->name; ?></option>
							<?php endforeach; ?>
						</select>

					</td>
				</tr>
				<?php if ($this->config->item("cam_with_player")) : ?>
					<tr>
						<td>
							<?php echo lang('dest.player'); ?>
						</td>
						<td>
							<select data-placeholder="Choose Players..." id="players-select-options" class="chosen-select" multiple <?php if ($auth <= 1) : ?>disabled <?php endif; ?>>
								<option value="0"></option>
								<?php foreach ($players as $player) : ?>
									<option value="<?php echo $player->id; ?>" <?php if (isset($sel_players) && is_array($sel_players) && in_array($player->id, $sel_players)) : ?>selected<?php endif; ?>><?php echo $player->name; ?></option>
								<?php endforeach; ?>
							</select>
							<span>
								<a data-fancybox data-type="ajax" data-src="/player/player_map/players-select-options" href="javascript:;">
									<svg class='search-player-icon' title="search devices" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
										<path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path>
									</svg>
								</a>
							</span>
						</td>

					</tr>
					<tr>
						<td>
							<?php echo lang('exclude.players'); ?>
						</td>
						<td>
							<select id="exclude_players_options" class="chosen-select tag-input-style" multiple <?php if ($auth <= 1) : ?>disabled <?php endif; ?>>
								<option value="0"></option>
								<?php if (isset($players)) : ?>
									<?php foreach ($players as $player) : ?>
										<option value="<?php echo $player->id; ?>" <?php $tagary = explode(',', $playlist->ex_players);
																					if (is_array($tagary) && in_array($player->id, $tagary)) : ?>selected<?php endif; ?>><?php echo $player->name; ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
							<span>
								<a data-fancybox data-type="ajax" data-src="/player/player_map/exclude_players_options" href="javascript:;">
									<svg class='search-player-icon' title="search devices" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
										<path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path>
									</svg>
								</a>
							</span>
						</td>

					</tr>
				<?php endif; ?>


				<tr id="tags">
					<td width="80">
						<?php echo lang('tag'); ?>
					</td>
					<td>
						<select data-placeholder=" " id="tag-select-options" class="chosen-select tag-input-style" multiple <?php if ($auth <= 1) : ?>disabled <?php endif; ?>>
							<?php foreach ($tags as $tag) : ?>
								<option value="<?php echo $tag->id; ?>" <?php $tagary = explode(',', $playlist->tags);
																		if (is_array($tagary) && in_array($tag->id, $tagary)) : ?>selected<?php endif; ?>><?php echo $tag->name; ?></option>
							<?php endforeach; ?>
						</select>

					</td>
				</tr>
				<?php if ($this->config->item('campaign_with_tags')) : ?>
					<tr id="tag_options">
						<td>
							<?php echo lang('tag.options'); ?>
						</td>
						<td>
							<select id="tag-options" <?php if ($auth <= 1) : ?>disabled <?php endif; ?>>
								<option value="0" <?php if ($playlist->tag_options == 0) : ?>selected<?php endif ?>><?php echo lang('tag.rules.none'); ?></option>
								<option value="2" <?php if ($playlist->tag_options == 2) : ?>selected<?php endif ?>><?php echo lang('tag.rules.exclusive'); ?></option>
							</select>

						</td>
					</tr>

				<?php endif ?>

				<tr id='groupcheckbox'>
					<td>
						<?php echo lang('grouped'); ?>
					</td>
					<td>
						<input type="checkbox" id='grouped' name="grouped" <?php if ($playlist->is_grouped) : ?> checked="checked" <?php endif; ?> />
					</td>
				</tr>
				<tr>
					<td>
						<?php echo lang('locked'); ?>
					</td>
					<td>
						<input type="checkbox" id='lockded' name="lockded" <?php if ($playlist->is_locked) : ?> checked="checked" <?php endif; ?> />
					</td>
				</tr>

				<tr>
					<td width="120">
						<?php echo lang('contactname'); ?>
					</td>
					<td>
						<input type="text" id="contactname" name="contactname" class="text ui-widget-content ui-corner-all" style="width:200px;" value="<?php echo $playlist->contact_name; ?>" />
					</td>
				</tr>
				<tr>
					<td>
						<?php echo lang('customertype'); ?>
					</td>
					<td>

						<select name="" id="customertype" style="width: 200px">
							<option value="0" <?php if ($playlist->cust_type == "0") : ?>selected="selected" <?php endif; ?>><?php echo lang('customertype.own'); ?></option>
							<option value="1" <?php if ($playlist->cust_type == "1") : ?>selected="selected" <?php endif; ?>><?php echo lang('customertype.local'); ?></option>
							<option value="2" <?php if ($playlist->cust_type == "2") : ?>selected="selected" <?php endif; ?>><?php echo lang('customertype.national'); ?></option>
							<option value="2" <?php if ($playlist->cust_type == "3") : ?>selected="selected" <?php endif; ?>><?php echo lang('customertype.external'); ?></option>

						</select>

					</td>
				</tr>
				<tr>
					<td>
						<?php echo lang("campaignvalue"); ?>
					</td>
					<td>
						<input input type="number" step='0.01' name="campaignvalue" id="campaignvalue" class="ui-widget-content ui-corner-all" style="width:200px;" value="<?php echo $playlist->cam_value; ?>"></input>
					</td>

				</tr>
				<?php if ($this->config->item("cost_entry")) : ?>


					<tr>
						<td>
							<?php echo lang('total.times'); ?>
						</td>
						<td>
							<input type="text" id="total_times" class="text ui-widget-content ui-corner-all" style="width:200px;" readonly="readonly" />
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang('cost'); ?>
						</td>
						<td>
							<input type="text" id="cost" class="text ui-widget-content ui-corner-all" style="width:200px;" readonly="readonly" />€
						</td>
					</tr>

				<?php endif; ?>


			</tbody>
		</table>
		<span>&nbsp&nbsp<?php echo lang('save.tips'); ?></span>
	</div>


</div>

<div id="rotateConfirm" title="<?php echo lang('rotate.confirm.title') ?>" style="display:none;">
	<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 100px 0;"></span><?php echo lang("rotate.confirm") ?></p>
</div>


<script type="text/javascript">
	$(document).ready(function() {
		$(".fancybox").fancybox({
			helpers: {
				overlay: null
			}
		});
		$('.chosen-select').chosen({
			width: "240px"
		});
		$('#tag-options').chosen({
			disable_search_threshold: 10,
			width: "240px"
		});
		$("ul.chosen-choices").css({
			'overflow': 'auto',
			'max-height': '200px'
		});

		$('#startDate').datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd'
		}).removeClass('gray');
		$('#endDate').datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd'
		}).removeClass('gray');


		var priority = document.getElementById('priority-options').value;
		if (priority == '0') {
			$('.time-selection').show();
			document.getElementById('alldayFlag').disabled = true;
			$('.playcount').hide();
			$('.weatherplaceholder').hide();
		} else if (priority == '3' || priority == '6') {
			$('.playcount').hide();
			$('#tag_options').hide();
			$('#tags').hide();
			$('.weatherplaceholder').show();

		} else if (priority == '4') {
			$('#tag_options').hide();
			$('.weatherplaceholder').hide();
		} else {
			$('.time-selection').show();
			$('.playcount').show();
			$('.weatherplaceholder').hide();
		}
		if (priority == 1 || priority == 2 || priority == 4) {
			$('#groupcheckbox').show();
		} else {
			$('#groupcheckbox').hide();
		}

	});



	$(function() {
		$('#dateFlag').click(function() {
			var sd = $('#startDate');
			var ed = $('#endDate');
			$('input:checkbox[id="dateFlag"]').each(function() {
				if (this.checked) {
					sd.datepicker({
						dateFormat: 'yy-mm-dd'
					}).removeClass('gray');

					ed.datepicker({
						dateFormat: 'yy-mm-dd'
					}).removeClass('gray');
					$('#dateFlag').val(1);
				} else {
					sd.datepicker('destroy').addClass('gray');
					ed.datepicker('destroy').addClass('gray');
					$('#dateFlag').val(0);
				}
			});
		});
		$('#alldayFlag').click(function() {
			$('input:checkbox[id="alldayFlag"]').each(function() {
				if (this.checked) {
					$('.time-input').attr('disabled', true);
					$('#alldayFlag').val(1);

				} else {
					$('.time-input').removeAttr('disabled');
					if ($('#priority-options').val() != 0) {
						$('#stopTM').val("0");
						$('#startTM').val("0");
						$('.min-input').attr('disabled', true);
					}
					$('#alldayFlag').val(0);
				}
			});
		});

	});
</script>