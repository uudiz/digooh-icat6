<div class="card">
	<div class="card-body">
		<div class='pb-2 row'>
			<div class="col" id="show_upgrade" style="display: none;">
				<button id="batch_upgrade" class="btn btn-danger"><?php echo lang('upgrade') ?></button>
			</div>
			<div class="col" id="filter">
				<form class="row align-items-center justify-content-end" id='toolbar'>
					<div class="col-3 row">
						<div class="col-auto">
							<label for="filterCriteria"><?php echo lang('criteria'); ?></label>
						</div>
						<div class="col">
							<select data-placeholder="" id="filterCriteria" name='criteria' class="form-select select2">
								<option value="-1"><?php echo lang('all'); ?></option>
								<?php if (isset($criteria)) : ?>
									<?php foreach ($criteria as $c) : ?>
										<option value="<?php echo $c->id; ?>"><?php echo $c->name; ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
						</div>
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
		<input type="hidden" id="config_id" value="<?php echo $config_id; ?>" />
	</div>
</div>
<script>
	function logQueryParams(params) {
		params.criteria = $('#filterCriteria').val();
		params.online = 1;
		params.search = $('#upgrade_search').val();
		return params;
	}

	$('#filterCriteria').on('change', function() {
		$('#log_table').bootstrapTable('refresh');
	});

	$("input#upgrade_search").off('input').on('input', function() {
		$('#log_table').bootstrapTable('refresh');
	});


	function operateFormatter(value, row) {
		return `<a href="#" class="link-secondary" onclick="upgrade_player(${row.id})" title="<?php echo lang('upgrade') ?>">
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
				field: 'criteria_name',
				title: "<?php echo lang('criteria') ?>"
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
				$('#filter').hide();

			} else {
				$('#show_upgrade').hide();
				$('#filter').show();
			}

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
</script>