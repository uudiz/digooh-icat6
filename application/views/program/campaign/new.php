<head>
	<link rel="stylesheet" href="/static/css/jquery/jquery-ui.min.css" />
	<link href='/static/fullcalendar-3.1.0/fullcalendar.min.css' rel='stylesheet' />
	<link href='/static/fullcalendar-3.1.0/fullcalendar.print.min.css' rel='stylesheet' media='print' />
	<link rel="stylesheet" href="/static/css/jquery/chosen.min.css" />
	<link rel="stylesheet" href="/static/css/alertify.core.css" />
	<link rel="stylesheet" href="/static/css/alertify.default.css" />
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
	<script src="/static/js/alertify.min.js" type="text/javascript" charset="utf-8"></script>
	<script src='/static/js/jquery/jquery-ui.min.js'></script>
	<script src='/static/fullcalendar-3.1.0/lib/moment.min.js'></script>
	<script src='/static/fullcalendar-3.1.0/fullcalendar.min.js'></script>
	<script src='/static/js/jquery/chosen.jquery.min.js'></script>


	<style type="text/css">
		#left {
			width: 400px;
			float: left
		}

		#calendar {
			width: 800px;
			float: left;
			max-width: 800px;
			margin: 0 auto;
		}


		.search {
			top: 10px;
			height: 24px;
			line-height: 24px;
			position: relative;
			width: 24px;
		}
	</style>
</head>

<div id="validateTips">
	<div>
		<div id="formMsgContent">
		</div>
	</div>
</div>

<div id="left">
	<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
		<tbody>
			<tr>
				<td width="120">
					<?php echo lang('campaign') . ' ' . lang('name'); ?>
				</td>
				<td>
					<input type="text" id="name" name="name" class="text ui-widget-content ui-corner-all" style="width:200px;" />
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
					<input type="text" id="customerid" name="customerid" class="text ui-widget-content ui-corner-all" style="width:200px;" />
				</td>
			</tr>
			<tr>
				<td width="120">
					<?php echo lang('contractid'); ?>
				</td>
				<td>
					<input type="text" id="contractid" name="contractid" class="text ui-widget-content ui-corner-all" style="width:200px;" />
				</td>
			</tr>
			<tr>
				<td width="120">
					<?php echo lang('agencyid'); ?>
				</td>
				<td>
					<input type="text" id="agencyid" name="agencyid" class="text ui-widget-content ui-corner-all" style="width:200px;" />
				</td>
			</tr>
			<tr>
				<td width="120">
					<?php echo lang('customername'); ?>
				</td>
				<td>
					<input type="text" id="customername" name="customername" class="text ui-widget-content ui-corner-all" style="width:200px;" />
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang("desc"); ?>
				</td>
				<td>
					<textarea name="descr" id="descr" class="ui-widget-content ui-corner-all" rows="2" style="width:200px;"></textarea>
				</td>

			</tr>

			<tr>
				<td>
					<?php echo lang('priority'); ?>
				</td>
				<td>

					<select name="" id="priority-options" style="width: 200px" onchange="campaign.selchange(this);" <?php if ($auth == $this->config->item('auth_view')) : ?>disabled <?php endif; ?>>
						<option value="1"><?php echo lang('priority.high'); ?></option>
						<option value="2" selected="selected"><?php echo lang('priority.low'); ?></option>

						<?php if ($auth == 5 && $pid == 0) : ?>
							<option value="5"><?php echo lang('priority.reservation'); ?></option>
							<?php if ($this->config->item('campaign_with_tags')) : ?>
								<option value="4"><?php echo lang('priority.trail'); ?></option>
							<?php endif ?>
						<?php endif ?>

						<?php if (($pid && $shareblock) || !$pid) : ?>
							<option value="6"><?php echo lang('priority.simple'); ?></option>
							<option value="3"><?php echo lang('priority.fillin'); ?></option>
						<?php endif ?>

					</select>

				</td>
			</tr>

			<tr class="playcount">

				<td>
					<?php echo lang('play_method'); ?>
				</td>

				<td>
					<select id="playcnttype" style="width:130px;" <?php if ($auth == $this->config->item('auth_view')) : ?>disabled <?php endif; ?>onchange="campaign.typechange(this.value)">
						<option value="1"> <?php echo lang("campaign.count.percent"); ?></option>
						<option value="2"> <?php echo lang("campaign.count.total"); ?></option>
						<option value="0"> <?php echo lang("campaign.count.number") ?></option>
						<?php if (!$pid) : ?>
							<?php if ($this->config->item('xslot_on')) : ?>
								<option value="9"><?php echo lang("campaign.count.xslot"); ?></option>
							<?php endif; ?>
						<?php endif; ?>
					</select>


					<input type="number" style="display:none;width:60px;" id="playcountid" style="width:60px;" class="text ui-widget-content ui-corner-all" min="1" value="24" />

					<input type="number" id="playweightid" class="text ui-widget-content ui-corner-all" min="1" max="100" value="33" <?php if ($auth == $this->config->item('auth_view')) : ?>readonly <?php endif; ?> />

					<input type="number" style="display:none; width:80px;" id="playtotalid" class="text ui-widget-content ui-corner-all" min="1" />
					<?php if ($this->config->item('xslot_on')) : ?>
						<input style="display:none;width:60px;" type="number" id="xslotid" class="text ui-widget-content ui-corner-all" min="1" value=<?php echo $nxslot; ?> readonly disabled="disabled" />
					<?php endif; ?>

				</td>



			</tr>

			<tr>
				<td>
					<?php echo lang('date.range'); ?>
				</td>
				<td>
					<input type="text" style="width:90px;" id="startDate" name="startDate" readonly="readonly" class="date-input" />
					<em><?php echo lang('to'); ?></em>
					<input type="text" style="width:90px;" id="endDate" name="endDate" readonly="readonly" class="date-input" />
				</td>
			</tr>
			<tr class="time-section">
				<td><input type="checkbox" id="alldayFlag" name="alldayFlag" <?php echo 'checked="checked" value="1"' ?> /><?php echo lang('all'); ?>&nbsp;<?php echo lang('day'); ?></td>
				<td>

					<select onchange="" class="time-input" id="startTH" disabled="disabled">
						<?php for ($i = 0; $i < 24; $i++) : ?>
							<option value="<?php echo $i; ?>" <?php if ($i == 0) : ?>selected="selected" <?php endif; ?>><?php echo sprintf("%02d:00", $i); ?></option>
						<?php endfor; ?>
					</select>


					<em><?php echo lang('to'); ?></em>
					<select class="time-input" id="stopTH" disabled="disabled" onchange="campaign.minchange(this);">
						<?php for ($i = 0; $i < 24; $i++) : ?>
							<option value="<?php echo $i; ?>"><?php echo sprintf("%02d:00", $i); ?></option>
						<?php endfor; ?>
						<option value="24" selected="selected">00:00(+1)</option>
					</select>



				</td>
				<td>
					<div class="attention" id="errorTimerange" style="display:none;">
						<?php echo lang('warn.campaign.invalidtime'); ?>
					</div>
				</td>
			</tr>

		</tbody>
	</table>


	<?php if ($auth > 0) : ?>

		<table class="from-panel">
			<tbody>
				<tr>
					<td width="120">
						<?php echo lang('sel.criteria'); ?>
					</td>
					<td>
						<select data-placeholder="Choose Criteria..." id="criteria-select-options" class="chosen-select tag-input-style" multiple>
							<option value="0"></option>
							<?php foreach ($criterias as $crit) : ?>
								<option value="<?php echo $crit->id; ?>"><?php echo $crit->name; ?></option>
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
							<select data-placeholder="Choose Bind with Criteria(AND)" id="criteria-and-select-options" class="chosen-select tag-input-style" multiple>
								<option value="0"></option>
								<?php foreach ($criterias as $crit) : ?>
									<option value="<?php echo $crit->id; ?>"><?php echo $crit->name; ?></option>
								<?php endforeach; ?>
							</select> <span>(&)</span>
							<br /><br />

						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang('campaign.and.or'); ?>
						</td>
						<td>
							<select data-placeholder="Choose Bind with Criteria(OR)" id="criteria-and-select-options-or" class="chosen-select tag-input-style" multiple>
								<option value="0"></option>
								<?php foreach ($criterias as $crit) : ?>
									<option value="<?php echo $crit->id; ?>"><?php echo $crit->name; ?></option>
								<?php endforeach; ?>
							</select> <span>(|)</span>
							<br /><br />

						</td>
					</tr>
				<?php endif; ?>
				<tr>
					<td>
						<?php echo lang('exclude.criteria'); ?>
					</td>
					<td>
						<select data-placeholder="Choose Exclude Criteria..." id="criteria-ex-select-options" class="chosen-select tag-input-style" multiple>

							<option value="0"></option>
							<?php foreach ($criterias as $crit) : ?>
								<option value="<?php echo $crit->id; ?>"><?php echo $crit->name; ?></option>
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
							<select data-placeholder="Choose Players..." id="players-select-options" class="chosen-select tag-input-style" multiple <?php if ($auth == $this->config->item('auth_franchise')) : ?>disabled <?php endif; ?>>
								<option value="0"></option>
								<?php if (isset($players)) : ?>
									<?php foreach ($players as $player) : ?>
										<option value="<?php echo $player->id; ?>"><?php echo $player->name; ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
							<span>
								<a data-fancybox data-type="ajax" data-src="/player/player_map/players-select-options" href="javascript:;">
									<svg class='search' title="search devices" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
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
										<option value="<?php echo $player->id; ?>"><?php echo $player->name; ?></option>
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

			</tbody>
		</table>
	<?php endif ?>

	<table class="from-panel">
		<tbody>
			<?php if ($this->config->item('campaign_with_tags')) : ?>
				<tr id="tags">
				<?php else : ?>
				<tr>
				<?php endif ?>
				<td width="120">
					<?php echo lang('sel.tag'); ?>
				</td>
				<td>
					<select data-placeholder="Choose Tags..." id="tag-select-options" class="chosen-select tag-input-style" multiple>
						<option value="0"></option>
						<?php foreach ($tags as $tag) : ?>
							<option value="<?php echo $tag->id; ?>"><?php echo $tag->name; ?></option>
						<?php endforeach; ?>
					</select>

				</td>
				</tr>

				<?php if ($this->config->item('campaign_with_tags')) : ?>
					<tr id='tag_options'>
						<td>
							<?php echo lang('tag.options'); ?>
						</td>
						<td>
							<select id="tag-options">
								<option value="0" selected="selected"><?php echo lang('tag.rules.none'); ?></option>
								<option value="2"><?php echo lang('tag.rules.exclusive'); ?></option>
							</select>

						</td>
					</tr>

				<?php endif ?>
				<tr id='groupcheckbox'>
					<td>
						<?php echo lang('grouped'); ?>
					</td>
					<td>
						<input type="checkbox" id='grouped' name="grouped" />
					</td>
				</tr>
				<tr>
					<td>
						<?php echo lang('locked'); ?>
					</td>
					<td>
						<input type="checkbox" id='lockded' name="lockded" />
					</td>
				</tr>
				<tr>
					<td width="120">
						<?php echo lang('contactname'); ?>
					</td>
					<td>
						<input type="text" id="contactname" name="contactname" class="text ui-widget-content ui-corner-all" style="width:200px;" />
					</td>
				</tr>
				<tr>
					<td>
						<?php echo lang('customertype'); ?>
					</td>
					<td>

						<select name="" id="customertype" style="width: 200px">
							<option value="0" selected="selected"><?php echo lang('customertype.own'); ?></option>
							<option value="1"><?php echo lang('customertype.local'); ?></option>
							<option value="2"><?php echo lang('customertype.national'); ?></option>
							<option value="2"><?php echo lang('customertype.external'); ?></option>

						</select>

					</td>
				</tr>
				<tr>
					<td>
						<?php echo lang("campaignvalue"); ?>
					</td>
					<td>
						<input type="number" step='0.01' name="campaignvalue" id="campaignvalue" class="ui-widget-content ui-corner-all" style="width:200px;"></input>
					</td>

				</tr>



				<tr>
					<td>
						<a class="btn-01" href="javascript:void(0);" onclick="campaign.calculate();"><span> <?php echo lang('btn.calculate'); ?></span></a>
					</td>
					<td>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo lang('player.selected'); ?>
					</td>
					<td>
						<input type="text" id="player_sel" class="text ui-widget-content ui-corner-all" style="width:200px;" readonly="readonly" />
					</td>
				</tr>
				<tr>
					<td>
						<?php echo lang('avarage.usage'); ?>
					</td>
					<td>
						<input type="text" id="ava_usage" class="text ui-widget-content ui-corner-all" style="width:200px;" readonly="readonly" />
					</td>
				</tr>
				<tr>
					<td>
						Overbooked player count
					</td>
					<td>
						<input type="text" id="total_free" class="text ui-widget-content ui-corner-all" style="width:200px;" readonly="readonly" />
					</td>
				</tr>
				<tr>
					<td>
						<?php echo lang('least.common'); ?>
					</td>
					<td>
						<input type="text" id="least_common" class="text ui-widget-content ui-corner-all" style="width:200px;" readonly="readonly" />
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
	<p class="btn-center">
		<a class="btn-01" href="javascript:void(0);" onclick="campaign.goList();"><span> <?php echo lang('button.back'); ?></span></a>
		<a class="btn-01" href="javascript:void(0);" onclick="campaign.doCreate();"><span><?php echo lang('button.next'); ?></span></a>
	</p>

</div>

<body>
	<div id='selected_players'></div>
</body>


<script type="text/javascript">
	Date.prototype.Format = function(fmt) { //author: meizz   
		var o = {
			"M+": this.getMonth() + 1, //月份   
			"d+": this.getDate(), //日   
			"h+": this.getHours(), //小时   
			"m+": this.getMinutes(), //分   
			"s+": this.getSeconds(), //秒   
			"q+": Math.floor((this.getMonth() + 3) / 3), //季度   
			"S": this.getMilliseconds() //毫秒   
		};
		if (/(y+)/.test(fmt))
			fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
		for (var k in o)
			if (new RegExp("(" + k + ")").test(fmt))
				fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
		return fmt;
	}


	$(document).ready(function() {

		$('.chosen-select').chosen({
			width: "200px"
		});
		$('#tag-options').chosen({
			disable_search_threshold: 10,
			width: "180px"
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
		var date = new Date();
		var enddate = new Date();
		enddate.setMonth(enddate.getMonth() + 1);
		$('#startDate').val(date.Format("yyyy-MM-dd"));
		$('#endDate').val(enddate.Format("yyyy-MM-dd"));

		if (document.getElementById('priority-options').value == '0') {
			document.getElementById('alldayFlag').disabled = true;
			$('.playcount').hide();
		} else {
			$('.playcount').show();
		}

		/*
    //Calendar
    $('#calendar').fullCalendar({			
    	header: {
    		left: 'prev,next today',
    		center: 'title',
    		right: 'listDay,listWeek,month'
    	},

			// customize the button names,
			// otherwise they'd all just say "list"
			views: {
				listDay: { buttonText: 'Daily'},
				listWeek: { buttonText: 'Weekly' },
				month:{buttonText: 'Monthly'}
			},

			defaultView: 'listDay',
			defaultDate: new Date(),
			navLinks: true, // can click day/week names to navigate views
			editable: false,
			eventLimit: false, // allow "more" link when too many events	
			allDaySlot: true,

			events:{
				url: '/campaign/prepare_events',
			}

		});

	*/
	});

	$(function() {
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
						//$('.min-input').attr('disabled',true);
					}
					$('#alldayFlag').val(0);
				}
			});
		});

	});
</script>