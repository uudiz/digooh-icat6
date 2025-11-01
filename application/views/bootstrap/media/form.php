<link rel="stylesheet" href="/assets/bootstrap/css/select2totree.css" />
<script src="/assets/bootstrap/js/jquery.validate.min.js"></script>
<?php if ($lang == 'germany') : ?>
	<script src="/assets/js/validation/messages_de.js"></script>
<?php endif ?>
<script src="https://unpkg.com/imask"></script>
<link href="/assets/bootstrap/css/tom-select.css" rel="stylesheet">
<link href="/assets/bootstrap/css/tom-select.bootstrap5.css" rel="stylesheet">
<script src="/assets/bootstrap/js/popper.min.js"></script>
<script src="/assets/bootstrap/js/tom-select.complete.min.js"></script>

<div class="row">
	<div class="col-12 col-lg-10 m-auto pt-3 pb-2 mb-3">

		<div class="card">
			<div class="card-header">
				<h2><?php echo $title ?></h2>
			</div>
			<form id="mediumForm">
				<div class="card-body">
					<div class="card d-flex flex-column">
						<div class="row row-0 flex-fill">
							<div class="col-md-4">
								<?php if ($data->media_type == 1) : ?>
									<img src="<?php if (isset($data->full_path)) echo substr($data->full_path, 1); ?>" class="w-100 h-100 object-contain" alt="Card side image" onerror="javascript:this.remove()" />
								<?php elseif ($data->media_type == 2) : ?>
									<video autoplay controls class="w-100 h-100 object-contain">
										<source src="<?php echo substr($data->full_path, 1); ?>" type='video/mp4'>
									</video>
								<?php endif ?>

							</div>
							<div class="col-md-8">
								<div class="card-body">

									<h3 class="card-title"><a href="#">Details</a></h3>

									<div class="row d-flex align-items-center pt-4 mt-auto">

										<div class="col-12">
											<label for="name"><?php echo lang('name'); ?></label>
											<input type="text" class="form-control" name="name" required value="<?php if (isset($data->name)) echo $data->name; ?>" />
										</div>
										<div class="col-12">
											<label for="descr"><?php echo lang("desc"); ?></label>
											<textarea type="text" class="form-control" name="descr" rows="2"><?php if (isset($data->descr)) echo $data->descr; ?></textarea>
										</div>
										<div class="col-12">
											<label for="folders"><?php echo lang("folder"); ?></label>
											<select class="form-select" id="folders" name='folder_id'></select>
										</div>
										<div class="mb-3">
											<label for="tags_select"><?php echo $this->config->item("with_template") ? lang('categories') : lang('tag'); ?></label>
											<select id="tags_select" name='tags_select[]' class="form-select select2" multiple>
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
										<div class="mb-3 row">
											<div class="col-auto ">
												<label class="form-check form-switch">
													<label><?php echo lang("date_range"); ?></label>
													<input type="checkbox" id='date_flag' name='date_flag' class="form-check-input" <?php if (isset($data) && $data->date_flag) : ?>checked <?php endif ?> />
												</label>
											</div>
											<div class="col date_range row" <?php if (!isset($data) || !$data->date_flag) : ?>style="display:none" <?php endif ?>>

												<div class="col-auto ">

													<input type="date" class="form-control" required name="start_date" value="<?php if (isset($data->start_date)) echo $data->start_date; ?>" />
												</div>
												<div class="col-auto">

													<input type="date" class="form-control" required name="end_date" value="<?php if (isset($data->end_date)) echo $data->end_date; ?>" />
												</div>
											</div>
										</div>


										<?php if ($this->config->item('medium_with_weekNtime')): ?>
											<div class="mb-3 row">
												<div class="col-auto ">
													<label class="form-check form-switch">
														<label><?php echo lang("time_range"); ?></label>
														<input type="checkbox" id='time_flag' class="form-check-input" <?php if (isset($data) && $data->time_flag) : ?>checked <?php endif ?> />
													</label>
												</div>
												<div class="col time_range row" <?php if (!isset($data) || !$data->time_flag) : ?>style="display:none" <?php endif ?>>

													<div class="col-auto ">


														<input required type="time" name="start_time" value="<?php echo  isset($data->start_time) ? $data->start_time : ""; ?>" />
													</div>
													<div class="col-auto">


														<input required type="time" name="end_time" value="<?php echo  isset($data->end_time) ? $data->end_time : ""; ?>" />
													</div>
												</div>
											</div>

											<div class="mb-3 row">
												<div class="col-auto">
													<label class="form-check form-switch">
														<label><?php echo lang("weekday"); ?></label>
														<input type="checkbox" id='week_flag' name="week_flag" class="form-check-input" <?php if (isset($data) && $data->week_flag) : ?>checked <?php endif ?> />
													</label>
												</div>

												<div class="weekdays col" <?php if (!isset($data) || !$data->week_flag) : ?>style="display: none" <?php endif ?>>
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
										<?php endif ?>

										<div class="mb-3">
											<label for="play_time"><?php echo lang('play_time'); ?> (MM:SS)</label>
											<input type="text" class="form-control" id="play_time" name="play_time" <?php if ($data->media_type == 2 || $auth < 5) : ?>readonly<?php endif ?> value="<?php if (isset($data->play_time)) echo $data->play_time; ?>" />
										</div>
										<?php if (0 && $auth == 5 && !$pid) : ?>
											<div class="mb-3">
												<div class="col-auto ">
													<label><?php echo lang("approval"); ?></label>
													<label class="form-check form-switch">
														<input type="checkbox" id='approved' name='approved' class="form-check-input" <?php if (isset($data) && $data->approved) : ?>checked <?php endif ?> />
													</label>
												</div>
											</div>
										<?php endif ?>
										<?php if ($this->config->item("with_register_feature") && isset($register_feature) && $register_feature) : ?>
											<div class="mb-3 row">
												<div class='col-auto'>

													<label class="form-label"><?php echo lang("store"); ?></label>

													<select class="form-select select2-opt" id="store_id" name="store_id" aria-placeholder="Stores">
														<option value="0" disabled selected hidden>Please select a store</option>
														<?php if ($this->config->item("multi_providers")) : ?>
															<?php foreach ($stores as $key => $values) : ?>
																<optgroup label="<?php echo $key; ?>">
																	<?php foreach ($values as $store) : ?>
																		<option value="<?php echo $store->id; ?>"><?php echo $store->name; ?></option>
																	<?php endforeach; ?>
																</optgroup>
															<?php endforeach; ?>
														<?php else : ?>
															<?php foreach ($stores as $store) : ?>
																<option value="<?php echo $store->id; ?>"><?php echo $store->name; ?></option>
															<?php endforeach; ?>
														<?php endif ?>
													</select>


												</div>
												<div class='col'>
													<label class="form-label"><?php echo lang("product"); ?></label>
													<select id="product_id" name="product_id" class="form-control">
													</select>
												</div>
											</div>
										<?php endif ?>
										<input type="hidden" id="id" name="id" value="<?php echo isset($data->id) ? $data->id : 0; ?>" />
									</div>

								</div>
							</div>
						</div>
					</div>
				</div>


				<div class="card-footer">

					<button class="btn btn-outline-primary" type="submit"><i class="bi bi-cloud-arrow-up"></i><?php echo lang('button.save'); ?></button>
					<a class="btn  btn-outline-primary" href="/media"><i class="bi bi-x-circle"></i><?php echo lang('button.cancel'); ?></a>

				</div>
			</form>
		</div>

	</div>
</div>
<script>
	var element = document.getElementById('play_time');
	var maskOptions = {
		mask: '00:00',

	};
	var mask = IMask(element, maskOptions);

	$.validator.addMethod("greaterThanStartTime", function(value, element) {
		// Only validate if time_flag is checked
		if (!$("#time_flag").is(':checked')) {
			return true;
		}

		var startTime = $("input[name='start_time']").val();
		var endTime = value;

		// Convert times to minutes for comparison
		function timeToMinutes(timeStr) {
			var parts = timeStr.split(':');
			return parseInt(parts[0]) * 60 + parseInt(parts[1]);
		}

		var startMinutes = timeToMinutes(startTime);
		var endMinutes = timeToMinutes(endTime);

		// Special case: if end time is 00:00, treat it as 24:00 (end of day)
		/*		if (endTime === "00:00") {
					endMinutes = 24 * 60;
				}
		*/
		return endMinutes > startMinutes;
	}, "End time must be greater than start time");

	$('#date_flag').on('change', function() {
		if ($("#date_flag").is(':checked')) {
			$('.date_range').show();
		} else {
			$('.date_range').hide();
		}
	});
	$('#time_flag').on('change', function() {
		if ($("#time_flag").is(':checked')) {
			$('.time_range').show();
		} else {
			$('.time_range').hide();
		}
	});
	$('#week_flag').on('change', function() {
		if ($("#week_flag").is(':checked')) {
			$('.weekdays').show();
		} else {
			$('.weekdays').hide();
		}
	});
	$.validator.addMethod("weekdaysInDateRange", function() {
		// Only validate if both date_flag and week_flag are checked
		if (!$("#date_flag").is(':checked') || !$("#week_flag").is(':checked')) {
			return true;
		}

		// Get start and end dates
		const startDate = new Date($("input[name='start_date']").val());
		const endDate = new Date($("input[name='end_date']").val());

		if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
			return false; // Invalid dates
		}

		// Calculate which weekdays are in the range (as a bitmask)
		let weekdayBitmaskInRange = 0;
		for (let d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
			const jsDay = d.getDay(); // 0=Sunday, 1=Monday, etc.
			let bitValue = 0;

			switch (jsDay) {
				case 1:
					bitValue = 1;
					break; // Monday
				case 2:
					bitValue = 2;
					break; // Tuesday
				case 3:
					bitValue = 4;
					break; // Wednesday
				case 4:
					bitValue = 8;
					break; // Thursday
				case 5:
					bitValue = 16;
					break; // Friday
				case 6:
					bitValue = 32;
					break; // Saturday
				case 0:
					bitValue = 64;
					break; // Sunday (0 in JS)
			}

			weekdayBitmaskInRange |= bitValue;
		}

		// Get selected weekdays as a bitmask
		let selectedWeekdaysBitmask = 0;
		$(".weekday:checked").each(function() {
			selectedWeekdaysBitmask |= parseInt($(this).val());
		});

		// Check if there's overlap between selected days and days in range
		return (selectedWeekdaysBitmask & weekdayBitmaskInRange) !== 0;
	}, function() {
		return localStorage.getItem("language") == "germany" ?
			"Medien werden aufgrund der Vorgaben nicht wiedergegeben. Bitte überprüfen Sie die Einstellungen." :
			"Media will not playback because of the settings. Please check the parameters.";
	});

	<?php if ($this->config->item("with_register_feature") && isset($register_feature) && $register_feature) : ?>
		/*
		var storeSelect = new TomSelect("#store_id", {
			allowEmptyOption: true,
			onChange: function(value) {
				fetch_products(value);
			}
		});
		*/
		$("#store_id").on('change', function() {
			fetch_products($(this).val());
		});


		var productSelect = new TomSelect('#product_id', {
			valueField: 'id',
			labelField: 'name',
			searchField: ['name', 'ean_code', 'plu_code'],
			disabled: true,
			allowEmptyOption: true,
			maxOptions: null,
			plugins: {
				'dropdown_header': {
					html: function(data) {
						return '<div class="dropdown-header d-flex align-items-center"><span class="flex-grow-1">Product</span><span class="flex-grow-1">Artikelnummer</span><span class="flex-grow-1">Price</span> </div>';
					}
				},
				'clear_button': {}
			},

			render: {
				option: function(data) {

					const div = document.createElement('div');
					div.className = 'row';

					const span = document.createElement('span');
					span.className = 'col-lg-4';
					span.innerText = data.name;
					div.append(span);

					const itemno = document.createElement('span');
					itemno.className = 'col-lg-4';
					itemno.innerText = data.product_id;
					div.append(itemno);

					const price = document.createElement('span');
					price.className = 'col-lg-4';

					price.innerText = data.ena_price && data.ena_price > 0 ? data.ena_price : data.price;

					div.append(price);

					return div;
				}

			}
		});

		function fetch_products(store_id) {
			productSelect.clear();
			productSelect.clearOptions();
			if (!store_id) {
				return;
			}

			fetch('/api/products?store_id=' + store_id)
				.then(function(response) {
					return response.json();
				})
				.then(function(products) {
					// Add the products to the product select
					productSelect.addOption(products.data);

					// Enable the product select
					productSelect.enable();
					<?php if (isset($data->product_id)) : ?>
						productSelect.setValue(<?php echo $data->product_id ?>);
					<?php endif; ?>
				});
		}
		<?php if (isset($store_id) && $store_id) : ?>
			$('#store_id').val("<?php echo $store_id ?>").trigger('change');
		<?php endif ?>
	<?php endif ?>
	$(document).ready(function() {
		var weekdays = parseInt("<?php echo (isset($data) && isset($data->weekday)) ? $data->weekday : '127' ?>");
		var checkboxes = document.getElementsByClassName("weekday");

		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].value & weekdays) {
				checkboxes[i].checked = true;
			} else {
				checkboxes[i].checked = false;
			}
		}

		<?php if ($this->config->item('date_range_from_folder') && (isset($data) && isset($data->folder) && $data->folder->date_flag)): ?>
			// Disable data switch and date inputs if parent folder has date_flag set
			$('#date_flag').prop('disabled', true);
			$('input[name="start_date"]').prop('disabled', true);
			$('input[name="end_date"]').prop('disabled', true);

		<?php endif ?>
		$.ajax({
			url: '/player/getNestedFolders',
			dataType: "json",
			success: function(res) {
				$("#folders").select2ToTree({
					width: '100%',
					treeData: {
						dataArr: res.data
					}
				});
				<?php if (isset($data->folder_id)) : ?>
					$('#folders').val(<?php echo $data->folder_id ?>).trigger('change');
				<?php endif ?>
			},
			cache: false,
			contentType: false,
			processData: false
		});

		$("#mediumForm")
			.submit(function(e) {
				e.preventDefault();
			})
			.validate({
				lang: localStorage.getItem("language") == "germany" ? "de" : "en",
				rules: {
					start_time: {
						required: function() {
							return $("#time_flag").is(':checked');
						}
					},
					end_time: {
						required: function() {
							return $("#time_flag").is(':checked');
						},
						greaterThanStartTime: function() {
							return $("#time_flag").is(':checked');
						}
					},
				},
				// Add custom error messages
				messages: {
					start_time: {
						required: function() {
							return localStorage.getItem("language") == "germany" ?
								"Bitte wählen Sie eine Startzeit" :
								"Please select a start time";
						}
					},
					end_time: {
						required: function() {
							return localStorage.getItem("language") == "germany" ?
								"Bitte wählen Sie eine Endzeit" :
								"Please select an end time";
						},
						greaterThanStartTime: function() {
							return localStorage.getItem("language") == "germany" ?
								"Die Endzeit muss nach der Startzeit liegen" :
								"End time must be greater than start time";
						}
					},
				},
				submitHandler: function(form, e) {
					e.preventDefault();

					// Additional validation before form submission
					if ($("#date_flag").is(':checked') && $("#week_flag").is(':checked') && !$.validator.methods.weekdaysInDateRange()) {
						toastr.error(localStorage.getItem("language") == "germany" ?
							"Medien werden aufgrund der Vorgaben nicht wiedergegeben. Bitte überprüfen Sie die Einstellungen." :
							"Media will not playback because of the settings. Please check the parameters."
						);
						return false;
					}
					var formData = new FormData($("#mediumForm")[0]);
					formData.append("date_flag", $("#date_flag").is(':checked') ? 1 : 0);
					formData.append("time_flag", $("#time_flag").is(':checked') ? 1 : 0);

					const week_flag = $("#week_flag").is(':checked') ? 1 : 0;
					formData.append("week_flag", week_flag);

					if (week_flag) {
						var weekdays = 0;
						var checkboxes = document.getElementsByClassName("weekday");

						for (var i = 0; i < checkboxes.length; i++) {
							if (checkboxes[i].checked) {
								weekdays |= parseInt(checkboxes[i].value);
							}
						}

						if (weekdays == 0) {
							toastr.error("Please select at least one weekday.");
							return false;
						}

						formData.append("weekday", weekdays);
					}

					$.ajax({
						url: "/media/do_save",
						type: "POST",
						data: formData,
						processData: false, // prevents jQuery from trying to process the data
						contentType: false, // lets the browser set the proper content-type with boundary
						dataType: "json",
						success: function(data) {
							if (data.code != 0) {
								toastr.error(data.msg);
							} else {
								localStorage.setItem(
									"Status",
									JSON.stringify({
										type: "success",
										message: data.msg,
									})
								);
								window.location.href = "/media";
							}
						}
					});
					return false;
				},
			});
	});
</script>