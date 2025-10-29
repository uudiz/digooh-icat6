<link rel="stylesheet" href="/assets/bootstrap-icons/bootstrap-icons.css">
<link type="text/css" href="/assets/bootstrap/css/select2totree.css" rel="stylesheet" />
<link href="/assets/bootstrap/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
<link href="/assets/css/logouploader.css" rel="stylesheet" type="text/css" />
<script src="/assets/bootstrap/js/fileinput.min.js"></script>
<script src="/assets/bootstrap/js/jquery.validate.min.js"></script>
<script src="/assets/bootstrap/js/select2totree.js"></script>
<?php if ($lang == 'germany') : ?>
	<script src="/assets/js/validation/messages_de.js"></script>
<?php endif ?>

<div class="row">
	<div class="col-12 col-lg-8 m-auto pt-3 pb-2 mb-3">
		<div class="card">
			<div class="card-header">
				<h2><?php echo $title ?></h2>
			</div>
			<form id="companyForm" enctype="multipart/form-data">
				<div class="card-body">
					<div class="row g-3">
						<div class="col-12">
							<label for="name"><?php echo lang('name'); ?></label>
							<input type="text" class="form-control" required id="name" name="name" value="<?php if (isset($data->name)) echo $data->name; ?>" />
						</div>
						<div class="col-12">
							<label for="descr"><?php echo lang("desc"); ?></label>
							<textarea type="text" class="form-control" id="descr" name="descr" rows="2"><?php if (isset($data->descr)) echo $data->descr; ?></textarea>
						</div>
						<div class="col-md-6">
							<label for="max_user"><?php echo lang('max.user'); ?></label>
							<input type="number" class="form-control" id="max_user" name="max_user" value="<?php echo isset($data->max_user) ? $data->max_user : '3'; ?>" />
						</div>
						<div class="col-md-6">
							<label for="total_disk"><?php echo lang('max.disk'); ?></label>
							<input type="text" class="form-control" id="total_disk" name="total_disk" value="<?php echo isset($data->total_disk) ? $data->total_disk : '500MB'; ?>" />
						</div>
						<?php if (!isset($parent_id)) : ?>
							<div class="col-md-6">
								<label for="start_date"><?php echo lang('start.date'); ?></label>
								<input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo isset($data->start_date) ? $data->start_date : date("Y-m-d"); ?>" />
							</div>
							<div class="col-md-6">
								<label for="stop_date"><?php echo lang('end.date'); ?></label>
								<input type="date" class="form-control" id="stop_date" name="stop_date" value="<?php echo isset($data->stop_date) ? $data->stop_date : date("Y-m-d", strtotime("+1 month")); ?>" />
							</div>

							<div class="col-md-6">
								<label class="form-check form-switch">
									<input type="checkbox" id='device_setup' class="form-check-input" <?php if ((isset($data) && ($data->device_setup == 'on' || $data->device_setup == '1')) || !isset($data)) : ?>checked <?php endif ?> />
									<label><?php echo lang("device.setup.control"); ?></label>
								</label>
							</div>

							<div class="col-md-6">
								<label class="form-check form-switch">
									<input type="checkbox" id='auto_dst' class="form-check-input" <?php if ((isset($data) && $data->auto_dst == 0) || !isset($data)) : ?>checked <?php endif ?> />
									<label><?php echo lang("auto.dst"); ?></label>
								</label>
							</div>

							<div class="col-md-6">
								<label for="com_imterval"><?php echo lang('comm.interval'); ?><small>(<?php echo lang('valid.interval') ?>)</small></label>
								<input type="number" class="form-control" id="com_imterval" name="com_interval" min="1" max="60" value="<?php echo isset($data->com_interval) ? $data->com_interval : '5'; ?>" />

							</div>
							<?php if (!$this->config->item("with_template")) : ?>
								<div class="col-md-6">
									<label for="xslot"><?php echo lang('campaign.count.xslot'); ?><small>(<?php echo lang('valid.xslot') ?>)</small></label>
									<input type="number" class="form-control" id="xslot" name="xslot" min="1" max="10" value="<?php echo isset($data->nxslot) ? $data->nxslot : '5'; ?>" />

								</div>
							<?php endif ?>
							<div class="col-md-6">
								<label for="playtime">Default JPG Play Time <small>(1-60 seconds)</small></label>
								<input type="number" class="form-control" id="playtime" name="play_time" min="1" max="60" value="<?php echo isset($data->default_play_time) ? $data->default_play_time : '10'; ?>" />
							</div>
						<?php endif ?>
						<div class="col-md-6">
							<label for="playtime">Theme Color</label>
							<input type="color" id='colorpicker' name="theme_color" class="form-control form-control-color" value="<?php echo isset($data->theme_color) ? $data->theme_color : ($this->config->item('with_template') ? '#1e293b' : '#f2e6ff'); ?>">
						</div>
						<?php if (!$this->config->item("with_template")) : ?>
							<?php if (!isset($parent_id)) : ?>
								<div class="col-6">
									<label for="cust_filed1"><?php echo lang('cust.field.name1'); ?></label>
									<input type="text" class="form-control" id="cust_filed1" name="cust_filed1" value="<?php if (isset($data->cust_player_field1)) echo $data->cust_player_field1; ?>" />
								</div>
								<div class="col-6">
									<label for="cust_filed2"><?php echo lang('cust.field.name2'); ?></label>
									<input type="text" class="form-control" id="cust_filed2" name="cust_filed2" value="<?php if (isset($data->cust_player_field2)) echo $data->cust_player_field2; ?>" />
								</div>
							<?php endif ?>
						<?php endif ?>


						<?php if (isset($parent_id)) : ?>
							<div class="col-md-6 ">
								<label></label>
								<label class="form-check form-switch">
									<input type="checkbox" id='active' class="form-check-input" <?php if ((isset($data) && ($data->flag == '0')) || !isset($data)) : ?>checked <?php endif ?> />
									<label><?php echo lang("actived"); ?></label>
								</label>
							</div>
							<div class="col-md-5">
								<label for="shareblock"></label>
								<div class="input-group">
									<label class="form-check form-switch">
										<input type="checkbox" id='shareblock' class="form-check-input" <?php if ((isset($data) && $data->shareblock)) : ?>checked <?php endif ?> />
										<label><?php echo lang("share.block"); ?></label>
									</label>
								</div>
							</div>

							<div class="col-12 row">
								<label for="players-select-options"><?php echo lang('shared.criteria'); ?></label>
								<div class='col-10'>
									<select data-placeholder="" id="criteria-select-options" name="criterion_id" class="form-control select2">
										<?php foreach ($criteria as $crit) : ?>
											<option value="<?php echo $crit->id; ?>" <?php if (isset($data->criterion_id) && $crit->id == $data->criterion_id) : ?>selected<?php endif; ?>><?php echo $crit->name; ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="col-2">
									<input type="number" class="form-control" id="quota" name="quota" max='3600' min='1' value="<?php echo isset($data->quota) ? $data->quota : '600'; ?>" />
								</div>
							</div>
							<div class="col-12 row">
								<label for="players-select-options"><?php echo lang('shared.players'); ?></label>
								<div class='col-10'>
									<select data-placeholder="" id="players-select-options" name="players[]" class="form-control select2" multiple>
										<?php foreach ($players as $crit) : ?>
											<option value="<?php echo $crit->id; ?>" <?php if (isset($data->players)) : ?> <?php $criary = explode(',', $data->players);
																															if (is_array($criary) && in_array($crit->id, $criary)) : ?>selected<?php endif; ?><?php endif; ?>><?php echo $crit->name; ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="col-2">
									<input type="number" class="form-control" id="player_quota" name="player_quota" name="player_quota" max='3600' min='1' value="<?php echo isset($data->player_quota) ? $data->player_quota : '600'; ?>" />
								</div>
							</div>


							<div class="col-md-12">
								<label for="folder-select-options"><?php echo lang('select.folders'); ?></label>
								<select class="form-select tree-select" id="folder-select-options" name="folders"> </select>
							</div>
						<?php endif ?>

						<div class="col-12">
							<label>Logo</label>
							<div>
								<div class="kv-avatar">
									<div class="file-loading">
										<input id="logo" name="logo" type="file" accept="image/*" value="<?php if (isset($data->logo)) echo $data->logo ?>">
									</div>
								</div>
								<div class="kv-avatar-hint">
									<small>Select file < 1500 KB</small>
								</div>
								<div id="kv-avatar-errors-1" class="text-center" style="display:none"></div>
							</div>
						</div>
						<?php if (!$this->config->item("with_template")) : ?>
							<div class="col-12">
								<label for="max_user">Cost per Play</label>
								<div class="input-group">
									<input type="number" class="form-control" min="0.00" max="10.00" step='0.01' id="cost_default" name="cost_default" value="<?php echo isset($cost->cost_per_play) ? $cost->cost_per_play : 0.01; ?>" />
									<span class="input-group-text">
										€
									</span>
								</div>
							</div>

							<div class="col-md-auto">
								<label for="max_user">Scale price 1</label>
								<div class="row">
									<div class="col-auto">
										<div class="input-group">
											<input type="number" class="form-control" min="0.00" step='0.001' id="cost1" name="cost1" value="<?php echo isset($cost->cost1) ? $cost->cost1 : 0.009; ?>" />
											<span class="input-group-text">
												€
											</span>
										</div>
									</div>
									<div class="col-auto">
										<div class="input-group">
											<span class="input-group-text">
												For playing ad >=
											</span>
											<input type="number" class="form-control" min="1" step='1' id="cost_condition1" name="cost1_condition" value="<?php echo isset($cost->cost1_condition) ? $cost->cost1_condition : 100000; ?>" />

										</div>

									</div>
								</div>
							</div>
							<div class="col-md-12">
								<label for="max_user">Scale price 2</label>
								<div class="row">
									<div class="col-auto">
										<div class="input-group">
											<input type="number" class="form-control" min="0.00" step='0.001' id="cost2" name="cost2" value="<?php echo isset($cost->cost2) ? $cost->cost2 : 0.008; ?>" />
											<span class="input-group-text">
												€
											</span>
										</div>
									</div>
									<div class="col-auto">
										<div class="input-group">
											<span class="input-group-text">
												For playing ad >=
											</span>
											<input type="number" class="form-control" min="1" step='1' id="cost_condition2" name="cost2_condition" value="<?php echo isset($cost->cost2_condition) ? $cost->cost2_condition : 250000; ?>" />
										</div>

									</div>
								</div>
							</div>
						<?php endif ?>
						<input type="hidden" id="id" name="id" value="<?php echo isset($data->id) ? $data->id : 0; ?>" />
						<input type="hidden" id="parent_id" name="parent_id" value="<?php echo isset($parent_id) ? $parent_id : 0; ?>" />
					</div>
				</div>
				<div class="card-footer">
					<button class="btn btn-outline-primary" type="submit"><i class="bi bi-cloud-arrow-up"></i><?php echo lang('button.save'); ?></button>
					<a class="btn  btn-outline-primary" href="/company"><i class="bi bi-x-circle"></i><?php echo lang('button.cancel'); ?></a>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$("#companyForm").validate({
			rules: {
				criterion_id: {
					required: {
						depends: function() {
							var auth = $("#parent_id").val();
							if (auth != '0') {
								return true; /* or false */
							}
							return false;
						}
					}
				},
				folders: {
					required: {
						depends: function() {
							var auth = $("#parent_id").val();
							if (auth != '0') {
								return true; /* or false */
							}
							return false;
						}
					}
				},
			},
			submitHandler: function(form) {
				var formData = new FormData($("#companyForm")[0]);
				formData.append("auto_dst", $("#auto_dst").is(':checked') ? 0 : 1);
				formData.append("device_setup", $("#device_setup").is(':checked') ? "1" : "0");
				formData.append("share_block", $("#shareblock").is(':checked') ? 1 : 0);
				formData.append("flag", $("#active").is(':checked') ? 0 : 1);
				$.ajax({
					url: '/company/do_save',
					type: 'POST',
					enctype: 'multipart/form-data',
					data: formData,
					dataType: "json",
					success: function(data) {
						if (data.code != 0) {
							toastr.error(data.msg);
						} else {
							localStorage.setItem("Status", JSON.stringify({
								type: 'success',
								message: data.msg
							}));
							window.location = '/company';
						}
					},
					cache: false,
					contentType: false,
					processData: false
				});
			}
		});
		<?php if (isset($folders)) : ?>
			var treedata = eval(<?php echo $folders; ?>);

			$("#folder-select-options").select2ToTree({
				width: '100%',
				treeData: {
					dataArr: treedata
				}
			});
			<?php if (isset($data->root_folder_id)) : ?> $('#folder-select-options').val("<?php echo $data->root_folder_id ?>").trigger('change');
			<?php endif ?>
		<?php endif ?>
	});

	<?php if ($this->config->item("with_template")) : ?>
		var defaultLogo = '<img id="defaultLogo"  src="<?php echo isset($data->logo) && !empty($data->logo) ? $data->logo : "/assets/logos/default_logo.png" ?>">';
	<?php else : ?>
		var defaultLogo = '<img id="defaultLogo"  src="<?php echo isset($data->logo) && !empty($data->logo) ? $data->logo : "/assets/logos/logo-digooh.svg" ?>">';
	<?php endif ?>

	function setDefaultLogo() {
		$("#logo").fileinput('clear');
		var id = $('#id').val();
		if (id > '0') {
			$.ajax({
				url: '/company/reset_logo',
				data: {
					id: id,
				},
				dataType: "json",
				success: function(data) {
					$('#defaultLogo').attr("src", data.logo);
				},
			});
		}

	};

	var btnCust = '<button type="button" class="btn btn-secondary" title="Reset" ' +
		'onclick="setDefaultLogo()">' +
		'<i class="bi-x-lg"></i>' +
		'</button>';

	$("#criteria-select-options").on('change', function() {
		var selected_index = $(this).children('option:selected').val();

		$.get('/company/get_players_of_criterion/' + selected_index,
			function(data) {
				optionHtml = '';

				$.each(data, function(i) {
					optionHtml += '<option value="' + data[i].id + '" >' + data[i].name + '</option>';
				});

				$('#players-select-options').html(optionHtml);
				$('#players-select-options').trigger("chosen:updated");

			}, 'json');
	});

	$("#logo").fileinput({
		overwriteInitial: true,
		maxFileSize: 1500,
		showClose: false,
		showCaption: false,
		browseLabel: '',
		removeLabel: '',
		maxImageWidth: 1000,
		maxImageHeight: 400,
		browseIcon: '<i class="bi-folder2-open"></i>',
		removeIcon: '<i class="bi-x-lg"></i>',
		removeTitle: 'Cancel or reset changes',
		elErrorContainer: '#kv-avatar-errors-1',
		msgErrorClass: 'alert alert-block alert-danger',
		defaultPreviewContent: defaultLogo,
		layoutTemplates: {
			main2: '{preview} ' + btnCust + ' {browse}',
		},
		allowedFileExtensions: ["jpg", "png", "gif", 'svg', 'jpeg'],
	});
</script>