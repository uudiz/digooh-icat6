<script src="/assets/bootstrap/js/imask.js"></script>
<div class="card mb-3">
	<div class="card-body">
		<div class='pb-2 row'>
			<div class="col" id="show_upgrade" style="display: none;">
				<button id="batch_upgrade" class="btn btn-danger"><?php echo lang('command') ?></button>
			</div>
			<div class="col" id="filter">
				<form class="row align-items-center justify-content-end" id='toolbar'>

					<div class="col-auto">
						<a href="#" class="btn" data-bs-toggle="modal" data-bs-target="#commandModal">
							<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-direction" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none" />
								<path d="M12 5l0 14" />
								<path d="M5 12l14 0" />
							</svg>
							<?php echo lang('create') . " " . lang("command"); ?>
						</a>
					</div>

				</form>
			</div>
		</div>

		<table id='command_table' class="table table-sm" data-toggle="table" data-pagination="false">

		</table>
		<input type="hidden" id="peripheral_id" value="<?php echo $peripheral_id; ?>" />
	</div>
</div>

<div class="modal modal-blur fade" id="commandModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="command_modal_title"><?php echo isset($peripheral_id) ? lang('edit') . " " . lang('command') : lang('new') . "" . lang('command'); ?></h5>
				<button type=" button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="mb-3">
					<div>
						<label class="form-label"><?php echo lang('name'); ?></label>
						<input type="text" class="form-control" id="command_name" name="command_name" placeholder="" required>
					</div>
				</div>
				<div class="mb-3">
					<label class="form-label"><?php echo lang('command'); ?></label>
					<input type="text" class="form-control" id="command_text" name="command_text" placeholder="" required>
				</div>

				<div class="mb-3">
					<div class="form-label">Auto Mode</div>
					<div>
						<label class="form-check ">
							<input class="form-check-input" type="radio" name="radios-mode" checked value=0>
							<span class="form-check-label">Manually</span>
						</label>
						<label class="form-check ">
							<input class="form-check-input" type="radio" name="radios-mode" value=1>
							<span class="form-check-label">
								<div class='row'>
									<div class="col-auto">Daily At</div>
									<div class="col-md-4">
										<input type="time" id="command_daily_at">
									</div>
								</div>
							</span>
						</label>
					</div>
				</div>
				<div class="mb-3 row">

				</div>



				<input style="display:none" id="command_id" name="command_id" class="form-control " value="<?php if (isset($peripheral_id)) echo $peripheral_id; ?>">

			</div>

			<div class="modal-footer">
				<a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
					<?php echo lang('button.cancel'); ?>
				</a>

				<button class="btn btn-outline-primary" data-bs-dismiss="modal" onclick="save_command()"><i class="bi bi-cloud-arrow-up"></i><?php echo lang('button.save'); ?></button>

			</div>
		</div>
	</div>
</div>


<script>
	$('#filterCriteria').on('change', function() {
		$('#command_table').bootstrapTable('refresh');
	});

	$("input#upgrade_search").off('input').on('input', function() {
		$('#command_table').bootstrapTable('refresh');
	});

	/*
	var ssnMask = IMask(
		document.getElementById('command_text'), {
			mask: 'XX XX XX XX XX XX XX',
			definitions: {
				X: {
					mask: '0',
					displayChar: ' ',
					placeholderChar: '#',
				},
			},
			lazy: false,
			overwrite: 'shift',
		});
    */

	function commandNameFormatter(value, row, index) {
		ret = value;

		ret = `	<a href="#" data-bs-toggle="modal" data-bs-target="#commandModal" class="link-primary" data-command-id="${row.id}">
                   ${value}
                </a>`

		return ret;
	}

	function remove_command(id) {
		$("#delete_confirm")
			.modal("show")
			.on("click", "#delete", function(e) {
				$.post(
					`/Peripheral_controller/deleteCommand`, {
						id: id,
					},
					function(data) {
						if (data.code == 0) {
							toastr.success(data.msg);
							$('#command_table').bootstrapTable('refresh');
						} else {
							toastr.error(data.msg);
						}
					},
					"json"
				);
			});
	}

	function commandOperateFormatter(value, row) {
		return `<div class="btn-list flex-nowrap">

		<a href="#" onClick="remove_command(${row.id})" class="link-danger">
			<i class="bi bi-x-square"></i>
		</a>
		</div>`;
	}

	$('#command_table').bootstrapTable({
		url: '/Peripheral_controller/getCommandTableData?',
		pagination: false,
		queryParams: function(params) {
			var param = {
				peripheral_id: $("#peripheral_id").val()
			};
			return param;
		},

		columns: [{
				field: 'name',
				title: "<?php echo lang('name') ?>",
				formatter: "commandNameFormatter"
			}, {
				field: 'command',
				title: "<?php echo lang('command') ?>"
			},
			{
				formatter: "commandOperateFormatter"
			}
		]
	});


	function do_upgrade(ids, config_id) {
		$.post('/configxml/do_upgrade_config', {
			"id": config_id,
			"ids": ids
		}, function(data) {
			if (data.code == 0) {
				toastr.success(data.msg);
			} else {
				toastr.error(data.msg);
			}
		}, 'json');
	}

	function upgrade_player(id) {
		var config_id = $("#config_id").val();
		do_upgrade(id, config_id);
	}



	$('#batch_upgrade').on('click', function() {
		var config_id = $("#config_id").val();
		var selections = $('#log_table').bootstrapTable('getSelections');
		if (selections.length) {
			let ids = selections.map((item) => {
				return item.id
			});
			do_upgrade(ids, config_id);
		}
	})



	$('#commandModal').on('show.bs.modal', function(e) {
		var button = e.relatedTarget;
		var command_id = button.getAttribute('data-command-id')
		$('#command_modal_title').val("<?php echo lang('create.command') ?>");
		if (command_id) {
			$('#command_id').val(command_id);
			$('#command_modal_title').val("<?php echo lang('edit.command') ?>");

			$.post(
				"/Peripheral_controller/getCommand", {
					id: command_id,
				},
				function(data) {
					if (data.code == 0) {
						$('#command_text').val(data.data.command);
						$('#command_name').val(data.data.name);
						const mode = data.data.auto_mode;
						if (mode == 0) {
							$("input[name='radios-mode']").eq(0).prop("checked", true);
						} else {
							$("input[name='radios-mode']").eq(1).prop("checked", true);
						}

						$('#command_daily_at').val(data.data.daily_at);


						//toastr.success(data.msg);
						//$table.bootstrapTable("refresh");
					} else {
						//toastr.error(data.msg);
					}
				},
				"json"
			);
		} else {
			$('#command_text').val('');
			$('#command_name').val('');

			$('#command_id').val(0);
		}
	});


	function save_command() {
		$.post(
			"/Peripheral_controller/do_save_command", {
				name: $("#command_name").val(),
				command: $("#command_text").val(),
				peripheral_id: $('#peripheral_id').val(),
				daily_at: $('#command_daily_at').val(),
				auto_mode: document.querySelector('input[name="radios-mode"]:checked').value,
				id: $('#command_id').val()
			},
			function(data) {
				if (data.code == 0) {
					toastr.success(data.msg);
					$('#command_table').bootstrapTable("refresh");
				} else {
					toastr.error(data.msg);
				}
			},
			"json"
		);
	}
</script>