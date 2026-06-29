<link rel="stylesheet" href="/assets/bootstrap-icons/bootstrap-icons.css">
<link type="text/css" href="/assets/bootstrap/css/select2totree.css" rel="stylesheet" />
<link href="/assets/bootstrap/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
<link href="/assets/css/logouploader.css" rel="stylesheet" type="text/css" />
<script src="/assets/bootstrap/js/select2totree.js"></script>
<script src="/assets/bootstrap/js/fileinput.min.js"></script>
<?php if ($lang == 'germany') : ?>
	<script src="/assets/js/validation/messages_de.js"></script>
<?php endif ?>

<div class="row">
	<div class="col-12 col-lg-8 m-auto pt-3 pb-2 mb-3">
		<div class="card">
			<div class="card-header">
				<h2><?php echo html_escape($title); ?></h2>
			</div>
			<form class="row g-3" id="userForm">
				<div class="card-body">
					<div class="mb-1">
						<label for="name" class="col-form-label required"><?php echo lang('user_name'); ?></label>
						<input type="text" class="form-control" id="name" name="name" required value="<?php echo isset($data->name) ? html_escape($data->name) : ''; ?>" />
					</div>
					<div class="mb-1">
						<label for="password" class="col-form-label"><?php echo lang('password'); ?></label>
						<input type="password" class="form-control" id="password" name="password" <?php if (!isset($data)) : ?>required<?php endif ?> />
						<?php if (isset($data->id)) : ?>
							<small><?php echo lang('tip.reset.password'); ?></small>
						<?php endif ?>
					</div>
					<div class="mb-1">
						<label for="email" class="col-form-label required"><?php echo lang('email_address'); ?></label>
						<input type="email" class="form-control" id="email" name="email" required value="<?php echo isset($data->email) ? html_escape($data->email) : ''; ?>" />
					</div>

					<?php if ($this->config->item('with_register_feature') && isset($stores)) : ?>
						<div class="mb-1">
							<label for="stores" class="col-form-label"><?php echo lang('store'); ?></label>
							<select class="form-select select2" id="stores" name="stores[]" multiple>
								<?php foreach ($stores as $store) : ?>
									<option value="<?php echo $store->id; ?>"><?php echo html_escape($store->name); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					<?php endif ?>
					<div class="mb-1">
						<label for="descr"><?php echo lang('desc'); ?></label>
						<textarea class="form-control" id="descr" name="descr" rows="2"><?php echo isset($data->descr) ? html_escape($data->descr) : ''; ?></textarea>
					</div>

					<div class="mb-1">
						<label>Logo</label>
						<div>
							<div class="kv-avatar">
								<div class="file-loading">
									<input id="logo" name="logo" type="file" accept="image/*" value="<?php echo isset($data->logo) ? html_escape($data->logo) : ''; ?>">
								</div>
							</div>
							<div class="kv-avatar-hint">
								<small><?php echo lang('logo.limit'); ?></small>
							</div>
							<div id="kv-avatar-errors-1" class="text-center" style="display:none"></div>
						</div>
					</div>
					<div class="mb-3">
						<label class="form-label"><?php echo lang('rule'); ?></label>
						<div class="form-selectgroup">
							<label class="form-selectgroup-item">
								<input type="radio" name="roles" value="0" class="form-selectgroup-input">
								<span class="form-selectgroup-label"><?php echo lang('rule.view'); ?></span>
							</label>
							<label class="form-selectgroup-item">
								<input type="radio" name="roles" value="1" class="form-selectgroup-input">
								<span class="form-selectgroup-label"><?php echo lang('rule.franchise'); ?></span>
							</label>
							<label class="form-selectgroup-item">
								<input type="radio" name="roles" value="4" class="form-selectgroup-input">
								<span class="form-selectgroup-label"><?php echo lang('role.staff'); ?></span>
							</label>
							<label class="form-selectgroup-item">
								<input type="radio" name="roles" value="5" class="form-selectgroup-input">
								<span class="form-selectgroup-label"><?php echo lang('rule.admin'); ?></span>
							</label>
							<label class="form-selectgroup-item">
								<input type="radio" name="roles" value="101" class="form-selectgroup-input">
								<span class="form-selectgroup-label"><?php echo lang('role.api'); ?></span>
							</label>
							<label class="form-selectgroup-item">
								<input type="radio" name="roles" value="102" class="form-selectgroup-input">
								<span class="form-selectgroup-label"><?php echo lang('role.setup'); ?></span>
							</label>
						</div>
					</div>
					<div class="mb-1 can-publish row" style="display:none;">
						<label class="form-check form-switch col-4">
							<input type="checkbox" id="can_publish" class="form-check-input" <?php if (isset($data->can_publish) && $data->can_publish == '1') : ?>checked<?php endif ?> />
							<label><?php echo lang('can.publish'); ?></label>
						</label>
						<label class="form-check form-switch col-auto">
							<input type="checkbox" id="can_replace_main" class="form-check-input" <?php if (isset($data->can_replace_main) && $data->can_replace_main == '1') : ?>checked<?php endif ?> />
							<label><?php echo lang('can_replace_main'); ?></label>
						</label>
					</div>
					<div class="mb-1 sel-setup row" style="display:none;">
						<label class="form-check form-switch col-4">
							<input type="checkbox" id="can_restart_player" class="form-check-input" <?php if (isset($data->can_restart_player) && $data->can_restart_player == '1') : ?>checked<?php endif ?> />
							<label><?php echo lang('can_restart_player'); ?></label>
						</label>
						<label class="form-check form-switch col-auto">
							<input type="checkbox" id="can_upload_display_picture" class="form-check-input" <?php if (isset($data->can_upload_display_picture) && $data->can_upload_display_picture == '1') : ?>checked<?php endif ?> />
							<label><?php echo lang('can_upload_display_picture'); ?></label>
						</label>
					</div>
					<div class="mb-1 sel-folder" style="display:none;">
						<label for="folder-select-options"><?php echo lang('select.folders'); ?></label>
						<select class="form-select" id="folder-select-options" name="folders[]" <?php if (!$this->config->item('new_campaign_user')) : ?> multiple<?php endif ?>></select>
					</div>
					<div class="mb-1 sel-criteria" style="display:none;">
						<label class="form-check form-switch">
							<input type="checkbox" class="form-check-input useWhich" name="use_player" value="0" <?php if ((isset($use_player) && !$use_player) || !isset($use_player)) : ?>checked<?php endif ?> />
							<label><?php echo lang('select.criterias'); ?></label>
						</label>
						<select class="form-select select2" id="criteria-select-options" name="criteria[]" multiple style="max-height:200px">
							<?php if (isset($criteria) && !empty($criteria)) : ?>
								<?php foreach ($criteria as $crit) : ?>
									<option value="<?php echo $crit->id; ?>"><?php echo html_escape($crit->name); ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>
					<div class="mb-1 sel-player" style="display:none;">
						<label class="form-check form-switch">
							<input type="checkbox" class="form-check-input useWhich" name="use_player" id="useplayer" value="1" <?php if (isset($use_player) && $use_player) : ?>checked<?php endif ?> />
							<label><?php echo lang('select.players'); ?>
								<a data-bs-toggle="modal" data-bs-target="#playerModal">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search text-blue" viewBox="0 0 16 16">
										<path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
									</svg>
								</a>
							</label>
						</label>
						<select class="form-select select2" id="players-select-options" name="players[]" multiple style="max-height:200px">
							<?php if (isset($players) && !empty($players)) : ?>
								<?php foreach ($players as $player) : ?>
									<option value="<?php echo $player->id; ?>"><?php echo html_escape($player->name); ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>
					<div class="mb-1 sel-cam" style="display:none;">
						<label for="campaign-select-options"><?php echo lang('select.campaigns'); ?></label>
						<select class="form-select select2" id="campaign-select-options" name="campaigns[]" multiple>
							<?php if (isset($campaigns) && !empty($campaigns)) : ?>
								<?php foreach ($campaigns as $campaign) : ?>
									<option value="<?php echo $campaign->id; ?>"><?php echo html_escape($campaign->name); ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>
					<?php if ($this->config->item('tfa_enabled')) : ?>
						<div>
							<label class="form-check form-switch">
								<input type="checkbox" id="tfa_enabled" class="form-check-input" <?php if (isset($data->tfa_enabled) && $data->tfa_enabled == '1') : ?>checked<?php endif ?> />
								<label><?php echo lang('2fa'); ?></label>
							</label>
						</div>
					<?php endif ?>
					<input type="hidden" id="id" name="id" value="<?php echo isset($data->id) ? (int)$data->id : 0; ?>" />
					<input type="hidden" id="cid" name="cid" value="<?php echo (int)$cid; ?>" />
				</div>
				<div class="card-footer">
					<button class="btn btn-outline-primary" type="submit"><i class="bi bi-cloud-arrow-up"></i><?php echo lang('button.save'); ?></button>
					<a class="btn btn-outline-primary" href="/user"><i class="bi bi-x-circle"></i><?php echo lang('button.cancel'); ?></a>
				</div>
			</form>
		</div>
	</div>
	<?php $this->load->view('bootstrap/players/player_map'); ?>
</div>

<script type="text/javascript">
	$('.select2').select2({
		theme: 'bootstrap-5',
		width: '100%',
	});

	// Role-based UI visibility map: { role: { section: visible } }
	var roleVisibility = {
		'0': { 'can-publish': true,  'sel-setup': false, 'sel-folder': true,  'sel-player': true,  'sel-criteria': true,  'sel-cam': true,  'sel-template': false },
		'1': {
			'can-publish': false,
			'sel-setup':   false,
			'sel-folder':  true,
			<?php if ($this->config->item('new_campaign_user')) : ?>
				'sel-player':   true,
				'sel-criteria': true,
				'sel-cam':      false,
				'sel-template': true,
			<?php else : ?>
				'sel-player':   false,
				'sel-criteria': false,
				'sel-cam':      true,
				'sel-template': false,
			<?php endif ?>
		},
		'101': { 'can-publish': false, 'sel-setup': false, 'sel-folder': false, 'sel-player': false, 'sel-criteria': false, 'sel-cam': false, 'sel-template': false },
		'102': { 'can-publish': false, 'sel-setup': true,  'sel-folder': false, 'sel-player': false, 'sel-criteria': false, 'sel-cam': false, 'sel-template': false },
		'default': { 'can-publish': false, 'sel-setup': false, 'sel-folder': false, 'sel-player': false, 'sel-criteria': false, 'sel-cam': false, 'sel-template': false }
	};

	function switch_ui(role) {
		var visibility = roleVisibility[role] || roleVisibility['default'];
		$.each(visibility, function(section, visible) {
			$('.' + section).toggle(visible);
		});
		<?php if (isset($parent_id) && $parent_id > 0) : ?>
			$('.sel-criteria').hide();
			$('input[type=radio][name=use_player][value=1]').prop('checked', true);
		<?php endif ?>
	}

	var defaultLogoSrc = <?php echo json_encode(isset($data->logo) && !empty($data->logo) ? $data->logo : ($this->config->item('with_template') ? '/assets/logos/default_logo.png' : '/assets/logos/logo-digooh.svg')); ?>;
	var defaultLogo = '<img id="defaultLogo" src="' + defaultLogoSrc + '">';

	function setDefaultLogo() {
		$('#logo').fileinput('clear');
		var id = parseInt($('#id').val(), 10);
		if (id > 0) {
			$.ajax({
				url: '/user/reset_logo',
				data: { id: id },
				dataType: 'json',
				success: function(data) {
					$('#defaultLogo').attr('src', data.logo);
				}
			});
		}
	}

	var btnCust = '<button type="button" class="btn btn-secondary btn-reset-logo" title="Reset">' +
		'<i class="bi-x-lg"></i>' +
		'</button>';

	$('#logo').fileinput({
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
		allowedFileExtensions: ['jpg', 'png', 'gif', 'svg', 'jpeg'],
	});

	// Delegate click for reset logo button (avoids inline onclick)
	$(document).on('click', '.btn-reset-logo', setDefaultLogo);

	// Helper: set selected values on a select2 element from PHP data
	function setSelect2Values(selectId, phpData) {
		if (phpData) {
			$(selectId).val(phpData).trigger('change');
		}
	}

	$(document).ready(function() {
		// Load folder tree
		$.ajax({
			url: '/player/getNestedFolders?company_id=' + $('#cid').val(),
			dataType: 'json',
			success: function(res) {
				$('#folder-select-options').select2ToTree({
					width: '100%',
					treeData: { dataArr: res.data }
				});
				<?php if (isset($data->folders)) : ?>
					setSelect2Values('#folder-select-options', <?php echo json_encode($data->folders); ?>);
				<?php endif ?>
			},
			cache: false,
			contentType: false,
			processData: false
		});

		// Set initial role
		var checked_role = '4';
		<?php if (isset($data->auth)) : ?>
			checked_role = '<?php echo (int)$data->auth; ?>';
		<?php endif; ?>
		$('input:radio[name="roles"]').filter('[value="' + checked_role + '"]').prop('checked', true);
		switch_ui(checked_role);

		// Set initial select2 values from PHP data
		<?php if (isset($data->criteria)) : ?>
			setSelect2Values('#criteria-select-options', <?php echo json_encode($data->criteria); ?>);
		<?php endif ?>
		<?php if (isset($data->players)) : ?>
			setSelect2Values('#players-select-options', <?php echo json_encode($data->players); ?>);
		<?php endif ?>
		<?php if (isset($data->campaigns)) : ?>
			setSelect2Values('#campaign-select-options', <?php echo json_encode($data->campaigns); ?>);
		<?php endif ?>
		<?php if (isset($data->stores)) : ?>
			setSelect2Values('#stores', <?php echo json_encode($data->stores); ?>);
		<?php endif ?>
		<?php if (isset($data->templates)) : ?>
			setSelect2Values('#templates-select-options', <?php echo json_encode($data->templates); ?>);
		<?php endif ?>

		// Role change handler
		$('input:radio[name="roles"]').change(function() {
			switch_ui($("input[name='roles']:checked").val());
		});

		// Mutual exclusion for use_player checkboxes
		$('.useWhich').change(function() {
			$('input.useWhich').not(this).prop('checked', false);
		});

		// Form validation
		$('#userForm').validate({
			rules: {
				name: {
					required: true,
					remote: {
						url: '/user/checkName',
						data: {
							name: function() { return $('#name').val(); },
							id: function() { return $('#id').val(); }
						}
					}
				},
				email: {
					required: true,
					email: true
				},
				'folders[]': {
					required: {
						depends: function() {
							var auth = $("input[name='roles']:checked").val();
							return auth === '0' || auth === '1';
						}
					}
				},
				'campaigns[]': {
					required: {
						depends: function() {
							var auth = $("input[name='roles']:checked").val();
							<?php if ($this->config->item('with_template')) : ?>
								return auth === '0';
							<?php else : ?>
								return auth === '0' || auth === '1';
							<?php endif ?>
						}
					}
				},
				'templates[]': {
					required: {
						depends: function() {
							var auth = $("input[name='roles']:checked").val();
							<?php if ($this->config->item('with_template') && $this->config->item('new_campaign_user')) : ?>
								return auth === '1';
							<?php else : ?>
								return false;
							<?php endif ?>
						}
					}
				},
				'criteria[]': {
					required: {
						depends: function() {
							var auth = $("input[name='roles']:checked").val();
							var use_player = $("input[name='useplayer']:checked").val();
							return (auth === '0' || auth === '1') && use_player == 0;
						}
					}
				},
				'players[]': {
					required: {
						depends: function() {
							var auth = $("input[name='roles']:checked").val();
							var use_player = $("input[name='useplayer']:checked").val();
							return (auth === '0' || auth === '1') && use_player == 1;
						}
					}
				}
			},
			submitHandler: function(form) {
				var formData = new FormData($('#userForm')[0]);
				formData.append('can_publish', $('#can_publish').is(':checked') ? 1 : 0);
				formData.append('can_replace_main', $('#can_replace_main').is(':checked') ? 1 : 0);
				formData.append('can_restart_player', $('#can_restart_player').is(':checked') ? 1 : 0);
				formData.append('can_upload_display_picture', $('#can_upload_display_picture').is(':checked') ? 1 : 0);
				formData.append('auth', $("input[name='roles']:checked").val());
				formData.append('tfa_enabled', $('#tfa_enabled').is(':checked') ? 1 : 0);

				$.ajax({
					url: '/user/do_save',
					type: 'POST',
					enctype: 'multipart/form-data',
					data: formData,
					dataType: 'json',
					success: function(data) {
						if (data.code != 0) {
							toastr.error(data.msg);
						} else {
							localStorage.setItem('Status', JSON.stringify({
								type: 'success',
								message: data.msg
							}));
							window.location = '/user';
						}
					},
					cache: false,
					contentType: false,
					processData: false
				});
			}
		});
	});
</script>
