<div class="card">
	<div class="card-body">
		<div class='pb-2 row'>
			<div class="col" id="show_upgrade" style="display: none;">
				<button id="batch_upgrade" class="btn btn-danger"><?php echo lang('upgrade') ?></button>
			</div>
			<div class="col">
				<form class="row align-items-center justify-content-end" id='soft_toolbar'>
					<div class="col-auto">
						<label class="form-check ">
							<input type="checkbox" class="form-check-input" id="online" />
							<span class="form-check-label ">Online</span>
						</label>
					</div>
					<div class="col-auto">
						<div class="input-icon">
							<input type="text" id="upgrade_search" name="search" class="form-control " placeholder="">
							<span class="input-icon-addon">
								<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
									<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
									<circle cx="10" cy="10" r="7"></circle>
									<line x1="21" y1="21" x2="15" y2="15"></line>
								</svg>
							</span>
						</div>
					</div>
				</form>
			</div>
		</div>
		<table id='log_table' class="table table-sm" data-toggle="table" data-pagination="false">
			<thead>
				<tr>
					<th data-checkbox="true"></th>
					<th data-field="name"><?php echo lang('name') ?></th>
					<th data-field="sn"> <?php echo lang('sn') ?> </th>
				</tr>
			</thead>
		</table>
		<input type="hidden" id="mpeg_core" value="<?php echo $mpeg_core; ?>" />
		<input type="hidden" id="software_id" value="<?php echo $software_id; ?>" />
		<input type="hidden" id="version" value="<?php echo $version; ?>" />
	</div>
</div>
<script>
	function logQueryParams(params) {
		params.mpeg_core = $('#mpeg_core').val();
		params.search = $('#upgrade_search').val();

		if ($('#online').is(":checked")) {
			params.online = 1;
		}

		return params;
	}

	$('#online').off("change").on("change", function() {
		$('#log_table').bootstrapTable('refresh');
	});
	$('#upgrade_search').off("input").on("input", function() {
		$('#log_table').bootstrapTable('refresh');
	});


	function operateFormatter(value, row) {
		return `<a href="#" class="link-secondary" onclick="upgrade_player(${row.id})" title="><?php echo lang('upgrade') ?>">
			<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-apps text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
				<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
				<rect x="4" y="4" width="6" height="6" rx="1"></rect>
				<rect x="4" y="14" width="6" height="6" rx="1"></rect>
				<rect x="14" y="14" width="6" height="6" rx="1"></rect>
				<line x1="14" y1="7" x2="20" y2="7"></line>
				<line x1="17" y1="4" x2="17" y2="10"></line>
			</svg>
		</a>`;
	}

	$('#log_table').bootstrapTable({
		url: '/player/getTableData',
		pagination: false,
		queryParams: 'logQueryParams',
		columns: [{
				checkbox: true,

			}, {
				field: 'name',
				title: "<?php echo lang('name') ?>"
			}, {
				field: 'sn',
				title: "<?php echo lang('sn') ?>"
			}, {
				field: 'version',
				sortable: true,
				title: "<?php echo lang('version') ?>"
			},
			{
				field: 'upgrade_version',
				sortable: true,
				title: "<?php echo lang('upgrade.version') ?>"
			},
			{
				formatter: "operateFormatter"
			}
		]
	});
	$('#log_table').on('check.bs.table check-all.bs.table uncheck.bs.table uncheck-all.bs.table',
		function(e, rowsAfter, rowsBefore) {
			var table = $('#log_table');

			if (table.bootstrapTable('getSelections').length) {
				$('#show_upgrade').show();

			} else {
				$('#show_upgrade').hide();
			}

		});

	function do_upgrade(ids, version) {
		$.post('/player/do_upgrade_version', {
			"version": version,
			"ids": ids
		}, function(data) {
			if (data.code == 0) {
				toastr.success(data.msg);
				$('#log_table').bootstrapTable('refresh');
				$.post('/player/android_control', {
					"ids": ids,
					"type": 1,
					"value": 0
				}, function(data) {}); //终端重启
			} else {
				toastr.error(data.msg);
			}
		}, 'json');
	}

	function upgrade_player(id) {
		var version = $("#version").val();
		do_upgrade(id, version);
	}
	$('#batch_upgrade').on('click', function() {
		var version = $("#version").val();
		var selections = $('#log_table').bootstrapTable('getSelections');
		if (selections.length) {
			let ids = selections.map((item) => {
				return item.id
			});
			do_upgrade(ids, version);
		}
	})
</script>