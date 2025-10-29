<link rel="stylesheet" href="/assets/bootstrap/css/bootstrap-table-reorder-rows.css" />
<link href="/assets/bootstrap/css/tom-select.css" rel="stylesheet">
<link href="/assets/bootstrap/css/tom-select.bootstrap5.css" rel="stylesheet">
<script src="/assets/bootstrap/js/jquery.tablednd.min.js"></script>
<script src="/assets/bootstrap/js/bootstrap-table-reorder-rows.min.js"></script>
<script src="/assets/bootstrap/js/popper.min.js"></script>
<script src="/assets/bootstrap/js/tom-select.complete.min.js"></script>

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
				<form class="form" id="campaignDataForm">


					<div id="collapseOne">
						<div class="accordion-body">
							<div class="row">
								<div class="col-12">
									<label for="name"><?php echo lang('name'); ?></label>
									<input type="text" class="form-control" id="name" name="name" required minlength="3" value="<?php if (isset($data->name)) echo $data->name; ?>" />
								</div>



								<div class="col-md-6">
									<label><?php echo lang('categories'); ?></label>
									<select class="form-select select2 disable-for-normal-user" name="tags[]" id="tags" multiple>
										<?php foreach ($tags as $tag) : ?>
											<option value="<?php echo $tag->id; ?>" <?php if (isset($data->tags) && is_array($data->tags) && in_array($tag->id, $data->tags)) : ?>selected<?php endif ?>><?php echo $tag->name; ?></option>
										<?php endforeach; ?>
									</select>
								</div>

								<div class="col-md-6">
									<label><?php echo lang('desc'); ?></label>
									<textarea class="form-control" name="descr" rows=1><?php if (isset($data->descr)) echo $data->descr; ?></textarea>
								</div>

								<div class="col-md-3">
									<label><?php echo lang('start.date'); ?></label>
									<input type="date" class="form-control disable-for-normal-user" id="startDate" name="start_date" value="<?php echo isset($data) ? $data->start_date : date("Y-m-d", time()); ?>">
								</div>

								<div class="col-md-3">
									<label><?php echo lang('end.date'); ?></label>
									<input type="date" class="form-control disable-for-normal-user" id="endDate" name="end_date" value="<?php echo isset($data) ? $data->end_date : date("Y-m-d", strtotime('+1 month')); ?>">
								</div>

								<div class="col-6 row">
									<div class="col-auto">
										<label></label>
										<div class="form-check form-switch align-bottom">
											<input class="form-check-input disable-for-normal-user" type="checkbox" id="time_flag" <?php if (!isset($data) || (isset($data) && $data->time_flag)) : ?> checked<?php endif; ?>>
											<label><?php echo lang('whole.day'); ?></label>
										</div>
									</div>

									<div class="col row time_range" <?php if (!isset($data) || (isset($data) && $data->time_flag)) : ?>style="display:none" <?php endif ?>>
										<div class="col-auto">
											<label></label>

											<select class="form-select disable-for-normal-user" name="start_timeH">
												<?php for ($i = 0; $i < 24; $i++) : ?>
													<option value="<?php echo $i; ?>" <?php if (isset($data) && $i == $data->start_timeH) : ?>selected<?php endif ?>><?php echo sprintf("%02d:00", $i); ?></option>
												<?php endfor; ?>
											</select>
										</div>

										<div class="col-auto">
											<label></label>
											<select class="form-select disable-for-normal-user" name="end_timeH">
												<?php for ($i = 1; $i <= 24; $i++) : ?>
													<option value="<?php echo $i; ?>" <?php if (isset($data) && $i == $data->end_timeH) : ?>selected="selected" <?php endif; ?>><?php echo $i == 24 ? '00:00(+1)' : sprintf("%02d:00", $i); ?></option>
												<?php endfor; ?>

											</select>
										</div>
									</div>
								</div>
								<div class="mb-3">

									<div><?php echo lang("weekday") ?></div>
									<div>
										<label class="form-check form-check-inline">
											<input class="form-check-input weekday" type="checkbox" value="1">
											<span class="form-check-label"><?php echo lang("mon") ?></span>
										</label>
										<label class="form-check form-check-inline">
											<input class="form-check-input weekday" type="checkbox" value="2">
											<span class="form-check-label"><?php echo lang("tue") ?></span>
										</label>
										<label class="form-check form-check-inline">
											<input class="form-check-input weekday" type="checkbox" value="4">
											<span class="form-check-label"><?php echo lang("wed") ?></span>
										</label>
										<label class="form-check form-check-inline">
											<input class="form-check-input weekday" type="checkbox" value="8">
											<span class="form-check-label"><?php echo lang("thu") ?></span>
										</label>
										<label class="form-check form-check-inline">
											<input class="form-check-input weekday" type="checkbox" value="16">
											<span class="form-check-label"><?php echo lang("fri") ?></span>
										</label>
										<label class="form-check form-check-inline">
											<input class="form-check-input weekday" type="checkbox" value="32">
											<span class=" form-check-label"><?php echo lang("sat") ?></span>
										</label>
										<label class="form-check form-check-inline">
											<input class="form-check-input weekday" type="checkbox" value="64">
											<span class=" form-check-label"><?php echo lang("sun") ?></span>
										</label>
									</div>
								</div>
								<div class="row pb-3 criteria_selection">

									<div class="col-md-4">
										<label><?php echo lang('criteria'); ?></label>
										<select class="form-select select2 disable-for-normal-user" id="criteria" name="criteria[]" multiple>
											<?php foreach ($criteria as $criterion) : ?>
												<option value="<?php echo $criterion->id; ?>" <?php if (isset($data->criteria) && is_array($data->criteria) && in_array($criterion->id, $data->criteria)) : ?>selected<?php endif ?>><?php echo $criterion->name; ?></option>
											<?php endforeach; ?>
										</select>
									</div>

									<div class="col-md-4">
										<label><?php echo lang('criteria.and'); ?></label>
										<select class="form-select select2 disable-for-normal-user" id="and_criteria" name="and_criteria[]" multiple>
											<?php foreach ($criteria as $criterion) : ?>
												<option value="<?php echo $criterion->id; ?>" <?php if (isset($data->and_criteria) && is_array($data->and_criteria) && in_array($criterion->id, $data->and_criteria)) : ?>selected<?php endif ?>><?php echo $criterion->name; ?></option>
											<?php endforeach; ?>
										</select>
									</div>

									<div class="col-md-4">
										<label><?php echo lang('criteria.or'); ?></label>
										<select class="form-select select2 disable-for-normal-user" id="and_criteria_or" name="and_criteria_or[]" multiple>
											<?php foreach ($criteria as $criterion) : ?>
												<option value="<?php echo $criterion->id; ?>" <?php if (isset($data->and_criteria_or) && is_array($data->and_criteria_or) && in_array($criterion->id, $data->and_criteria_or)) : ?>selected<?php endif ?>><?php echo $criterion->name; ?></option>
											<?php endforeach; ?>
										</select>
									</div>

									<div class="col-md-4">
										<label><?php echo lang('criteria.exclude'); ?></label>
										<select class="form-select select2 disable-for-normal-user" id="ex_criteria" name="ex_criteria[]" multiple>
											<?php foreach ($criteria as $criterion) : ?>
												<option value="<?php echo $criterion->id; ?>" <?php if (isset($data->ex_criteria) && is_array($data->ex_criteria) && in_array($criterion->id, $data->ex_criteria)) : ?>selected<?php endif ?>><?php echo $criterion->name; ?></option>
											<?php endforeach; ?>
										</select>
									</div>


								</div>

								<div class="row pb-3">

									<div class="col-auto">
										<button type="button" class="btn btn-outline-primary disable-for-normal-user" <?php if ($editable) : ?> data-bs-toggle="modal" data-bs-target="#playerModal" data-target-field="players" <?php endif ?>>
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
										<select class="select2 disable-for-normal-user" id="players" name="players[]" multiple>
											<?php foreach ($players as $player) : ?>
												<option value="<?php echo $player->id; ?>" <?php if (isset($data->players) && is_array($data->players) && in_array($player->id, $data->players)) : ?>selected<?php endif ?>><?php echo $player->name; ?></option>
											<?php endforeach; ?>
										</select>
									</div>

								</div>



								<div class="row pb-3">

									<div class="col-auto">
										<button type="button" class="btn btn-outline-danger disable-for-normal-user" <?php if ($editable) : ?> data-bs-toggle="modal" data-bs-target="#playerModal" data-target-field="ex_players" <?php endif ?>>
											<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clipboard-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
												<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
												<path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path>
												<rect x="9" y="3" width="6" height="4" rx="2"></rect>
												<path d="M10 12l4 4m0 -4l-4 4"></path>
											</svg>
											<?php echo lang('exclude.players'); ?></button>
									</div>

									<div class="col">
										<select class="select2 disable-for-normal-user" id="ex_players" name="ex_players[]" multiple>
											<?php foreach ($players as $player) : ?>
												<option value="<?php echo $player->id; ?>" <?php if (isset($data->ex_players) && is_array($data->ex_players) && in_array($player->id, $data->ex_players)) : ?>selected<?php endif ?>><?php echo $player->name; ?></option>
											<?php endforeach; ?>
										</select>
									</div>

								</div>


							</div>

						</div>


					</div>




					<div id="collapseTwo">

						<div class="accordion-body">
							<div class="mb-3 row">
								<div class="form-group col-md-6">
									<label class="form-label  col-form-label"><?php echo lang('template'); ?></label>

									<select class="form-select" required id="template_id" <?php if (isset($data)) : ?>disabled<?php endif ?>>

										<?php foreach ($templates as $template) : ?>
											<option value="<?php echo $template->id; ?>" <?php if (isset($data->template_id) && $data->template_id == $template->id) : ?>selected<?php endif; ?>>
												<?php echo $template->name; ?>
											</option>
										<?php endforeach; ?>
									</select>


									</select>

								</div>


								<div class="form-group col-md-6" id="master_area_selection" style="display:none;">
									<label class="form-label  col-form-label">
										<?php echo lang('master.zone'); ?>
										<span class="form-help" data-bs-toggle="popover" data-bs-placement="right" data-bs-content="<?php echo lang('master.zone.help'); ?>" tabindex="-1"> ?</span></label>
									<select class="form-select" id="master_area_id" name='master_area_id'></select>
								</div>


							</div>
							<div id="areas">
							</div>
						</div>
					</div>




					<div class="card-footer">
						<div class="col-12">
							<?php if (!isset($readonly) || (isset($readonly) && $readonly == 0)) : ?>
								<?php if ($can_publish == 1) : ?>
									<a class="btn btn-outline-primary" href="#" onclick="doSubmit(1);">
										<i class="bi bi-play-btn"></i>
										<?php echo lang('button.publish'); ?>
									</a>
								<?php endif; ?>
								<a class="btn btn-outline-primary" href="#" onclick="doSubmit(0);">
									<i class="bi bi-cloud-arrow-up"></i>
									<?php echo lang('button.save'); ?>
								</a>


								<?php if (isset($data)) : ?>
									<a class="btn btn-outline-primary" onclick="exportReport()">
										<i class="bi bi-clipboard-data"></i>
										<?php echo lang('playback'); ?>
									</a>
								<?php endif; ?>
							<?php endif; ?>
							<a class="btn btn-outline-primary" type='btn' href="/playlist"><i class="bi bi-x-circle"></i><?php echo lang('button.cancel'); ?></a>
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
	$this->load->view("bootstrap/template_campaigns/evses_modal");

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
	function readonly_select(objs, action) {
		if (action === true)
			objs.prepend('<div class="disabled-select"></div>');
		else
			$(".disabled-select", objs).remove();
	}



	function excludeCheck(obj, id) {
		checked = $(obj).is(':checked') ? "1" : "0";

		$('#areaMediaTable').bootstrapTable('updateCellByUniqueId', {
			id: id,
			field: "status",
			value: checked
		});
	}


	$("#time_flag").change(function() {
		if ($("#time_flag").is(':checked')) {
			$('.time_range').hide();
		} else {
			$('.time_range').show();
		}
	});

	function loadAreas() {
		var url = '/playlist/getAreasView?template_id=' + $('#template_id').val() + "&playlist_id=" + $('#id').val();

		<?php if (isset($data) && isset($data->master_area_id)) : ?>
			url += "&master_area_id=" + "<?php echo $data->master_area_id ?: 0 ?>";
		<?php endif ?>

		$.get(url, function(data) {
			$('#areas').html(data);
		});
	}
	$('#template_id').on('change', function() {
		loadAreas();
	})

	function getActiveTableId() {
		return $('a[data-bs-toggle="tab"].active').attr('href') + 'Table';
	}

	function delete_media(row_id) {
		var activeTableId = getActiveTableId();
		var table = $(`${activeTableId}`);
		if (row_id == -1) {
			table.bootstrapTable('removeAll')
		} else {
			ids = [];
			ids.push(parseInt(row_id));
			/*ret = table.bootstrapTable('remove', {
				field:'$index',
				values: ids,
			});
			*/
			ret = table.bootstrapTable('remove', {
				field: '$index',
				values: ids,
			});
		}
	};








	function doSubmit(isPublish) {
		$('#isPublish').val(isPublish);
		$('#campaignDataForm').submit();
	};



	function exportReport() {
		var req = '?start_date=' + $('#startDate').val() + '&end_date=' + $('#endDate').val();
		req += '&playlist=' + $('#name').val();

		window.location.href = '/playback/excel' + req;
	};

	$("input#name").on('keydown paste input', function() {
		$('#campaign_name').text($('#name').val());

	});
	loadAreas();

	async function savePlaylist($url, $params) {
		try {
			const request = await fetch($url, {
				method: 'POST',
				body: $params
			});
			const response = await request.json();
			hideSpinner();
			if (response.code != 0) {
				toastr.error(response.msg);
			} else {
				localStorage.setItem("Status", JSON.stringify({
					type: 'success',
					message: response.msg
				}));
				window.location = '/playlist';
			}
		} catch (error) {
			hideSpinner();
			console.log(error);
			toastr.error("Unexpected error,Please contact the administrator");

		}
	}
	$(document).ready(function() {


		<?php if (!$editable) : ?>
			$('.disable-for-normal-user').attr("disabled", true);
		<?php endif ?>
		<?php if ($auth == 1 && $this->config->item("new_campaign_user")) : ?>
			$('.criteria_selection').hide();
		<?php endif ?>

		var popover = new bootstrap.Popover(document.querySelector('.form-help'), {
			trigger: 'focus'
		})

		var weekdays = parseInt("<?php echo (isset($data) && isset($data->week)) ? $data->week : '127' ?>");
		var checkboxes = document.getElementsByClassName("weekday");

		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].value & weekdays) {
				checkboxes[i].checked = true;
			} else {
				checkboxes[i].checked = false;
			}
		}

		$("#campaignDataForm").validate({
			rules: {
				name: {
					required: true,
					remote: {
						url: "/playlist/checkName",
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
			},
			submitHandler: function(form) {

				<?php if ($auth <= 1) : ?>
					$('.disable-for-normal-user').removeAttr("disabled");
				<?php endif ?>

				var params = new FormData($('#campaignDataForm')[0]);

				<?php if ($auth <= 1) : ?>
					$('.disable-for-normal-user').attr("disabled", true);
				<?php endif; ?>


				params.append("time_flag", $("#time_flag").is(':checked') ? 1 : 0);

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

				var weekdays = 0;
				var checkboxes = document.getElementsByClassName("weekday");

				for (var i = 0; i < checkboxes.length; i++) {
					if (checkboxes[i].checked) {
						weekdays |= parseInt(checkboxes[i].value);
					}
				}

				params.append("week", weekdays);

				let media_ids = [];
				var isEmpty = false;
				document.querySelectorAll('.table-media').forEach((el) => {
					var areaId = el.getAttribute("data-area-id");
					var media = $(`#${el.id}`).bootstrapTable('getData');
					if (!media.length) {

						isEmpty = true;
						return;
					}

					let ids = media.map((item) => {
						return {
							media_id: item.id,
							status: item.status,
							transmode: item.transmode,
							media_type: item.media_type,
							area_id: areaId,
							area_media_id: item.area_media_id ? item.area_media_id : 0,
							//approved: item.approved,
						}
					});

					if (ids.length) {
						Array.prototype.push.apply(media_ids, ids)
					}
				});



				var webTable = $('.table-webpage');
				if (webTable.length) {

					var items = webTable.bootstrapTable('getData');
					/*
					var webpages = items.map((item) => {
						return {
							url: item.url,
							url_type: item.url_type,
							updateF: item.updateF,
							duration: item.duration,
						}
					});
						*/

					var webpages = items.map((item) => {
						return {
							mce_id: item.id,
							text: item.text,
						}
					});


					params.append("webpages", JSON.stringify(webpages));
				}

				params.append("media", JSON.stringify(media_ids));
				params.append('template_id', $('#template_id').val());

				var id_numbers = [];
				params.delete('id_numbers');

				$('.id-area').each(function() {
					const id_tmp = this.id.split('-');
					if (id_tmp[1]) {
						const area_id = id_tmp[1];
						params.delete('types-' + area_id);
						const name = 'types-' + area_id;
						const type = $(`input[name="${name}"]:checked`).val();


						let numbers = '';
						let name_value = '';
						switch (type) {
							case '0':
								numbers = $('#id_number-' + area_id).val();
								name_value = $('#id_name-' + area_id).val();
								break;
							case '1':
								numbers = $('#id_url-' + area_id).val();
								if ($('#id_qrcode-' + area_id)) {
									name_value = $('#id_qrcode-' + area_id).is(':checked') ? "1" : "0";
								} else {
									name_value = "0";
								}
								break;
							case '2':
								numbers = $('#id_price-' + area_id).val();
								name_value = $('#id_angle-' + area_id).val();
								break;
							case '3':
								numbers = $('#id_freenumber-' + area_id).val();

								break;
							case '4':
								numbers = $('#product_id-' + area_id).val();
								break;
						}

						id_numbers.push({
							id_number: numbers,
							area_id: area_id,
							descr: $('#id_desc-' + area_id).val(),
							name: name_value,
							id: $('#id_id-' + area_id).val(),
							type: type,
						})
					}
				});

				/*
				$('input:text[name="id_numbers[]"]').each(function() {

					const id_tmp = this.id.split('-');


					$('#id_desc-' + id_tmp[1]).val();

					const id_type = $('#id_type-' + id_tmp[1]).val();
					var name = 'types-' + id_tmp[1].val();

					const type = $(`input[name="${name}"]:checked`).val();
					console.log(type);

					var id_name = $('#id_name-' + id_tmp[1]).val();


					id_numbers.push({
						id_number: this.value,
						area_id: id_tmp[1],
						descr: $('#id_desc-' + id_tmp[1]).val(),
						name: id_name,
						id: $('#id_id-' + id_tmp[1]).val(),
						type: $('#id_type-' + id_tmp[1]).val() === null ? 0 : id_type,
					})

				});
				*/
				if (id_numbers.length) {
					params.append("id_numbers", JSON.stringify(id_numbers));
				}
				showSpinner();
				var url = '/playlist/do_save';
				if ($('#isPublish').val() == '1') {
					url = '/playlist/do_publish'
				}
				savePlaylist(url, params);
				/*
				$.ajax({
					url: url,
					type: 'POST',
					data: params,
					dataType: "json",
					success: function(data) {
						hideSpinner();
						if (data.code != 0) {
							toastr.error(data.msg);
						} else {
							localStorage.setItem("Status", JSON.stringify({
								type: 'success',
								message: data.msg
							}));
							window.location = '/playlist';
						}
					},
					cache: false,
					contentType: false,
					processData: false
				})
				*/

			},


		});



	});
</script>