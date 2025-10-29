<div class="container-fluid">
	<div class="page-header">
		<div class="row align-items-center">
			<div class="col">
				<div class="page-pretitle">
				</div>
				<h2 class="page-title">
					<?php echo lang('template'); ?>
				</h2>
			</div>
			<?php if ($auth == 5) : ?>
				<div class="col-auto ms-auto">
					<div class="btn-list">

						<a href="/TemplateController/edit_screen" id="create_template" data-toggle="tooltip" title="<?php echo lang('template.new'); ?>" class="btn btn-primary">
							<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<line x1="12" y1="5" x2="12" y2="19"></line>
								<line x1="5" y1="12" x2="19" y2="12"></line>
							</svg>
							<?php echo lang('create'); ?>
						</a>


						<a href="#" class="btn btn-white" onclick="document.getElementById('import_excel').click();return false;">
							<i class="bi bi-file-earmark-arrow-up"></i>
							<?php echo lang('import'); ?>
						</a>

						<input type=file id=import_excel style="display:none" accept=".xml">
					</div>
				</div>
			<?php endif ?>
		</div>
	</div>
	<!-- Page title -->
	<div class="page-body">
		<div class='pb-2'>
			<div class="row" id="batch_operation" style="display: none;">
				<div class="col-auto">
					<button id="batch_delete" class="btn btn-danger"><?php echo lang('delete') ?></button>
				</div>
			</div>
			<form class="row align-items-center justify-content-end" id='toolbar'>
				<div class="col-auto">
					<div class="input-icon">
						<input type="text" id="search" name="search" class="form-control " placeholder="">
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
		<div class="card-table table-responsive">
			<table id="table" class="table table-striped table-responsive" id="table" data-toggle="table" data-url="/TemplateController/getTableData" data-sort-name="update_time" data-sort-order="desc">
				<thead>
					<tr>
						<th data-field="preview_url" data-formatter="previewFormatter"></th>
						<th data-field="name" data-sortable="true" data-formatter="nameFormatter"><?php echo lang('name'); ?></th>
						<th data-field="descr" data-sortable="true"><?php echo lang('desc'); ?></th>
						<th data-field="width" data-sortable="true" data-formatter="resFormatter"><?php echo lang('resolution'); ?></th>
						<th data-field="update_time" data-sortable="true"><?php echo lang('update.time'); ?></th>
						<th data-formatter="operateFormatter"><?php echo lang('operate'); ?></th>
					</tr>
				</thead>
			</table>
		</div>

	</div>
</div>

<script src="/assets/bootstrap/js/bootstrap-modbox.min.js"></script>
<script type="text/javascript">
	function nameFormatter(value, row) {
		<?php if ($auth != 5) : ?>
			return value;
		<?php endif ?>
		if (value && value.length > 50) {
			value = value.substring(0, 50) + "...";
		}

		return ` <a href="/TemplateController/edit_screen?id=${row.id}" class="link-primary">
				${value}
			</a>`;
	};

	function resFormatter(value, row) {
		return row.width ? `${row.width}X${row.height}` : 'N/A';
	}

	function previewFormatter(value, row) {


		var tooltips = `
			<img src="${value}" class="rounded"/>
		`;

		var html = '';
		var scr = value + "?noCache=" + (new Date()).getTime();
		html = `<span class="d-inline-block cursor-pointer" tabindex="0" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-html="true"  data-bs-container="body" title='${tooltips}'>
            <img src="${scr}+" class="rounded" style="max-width:40px; max-height:40px" onerror="javascript:this.src='/assets/img/load_fail_pic.svg'">
            </span>`;
		<?php if ($auth == 5) : ?>
			html = `<a href="/TemplateController/edit_screen?id=${row.id}" class="link-primary">` + html + '<a>';
		<?php endif ?>

		return html;
	}

	var $table = $("#table");

	function remove_template(resource, id) {
		$('#delete_confirm').modal("show")
			.on('click', '#delete', function(e) {
				$.post(`/${resource}/do_delete`, {
					id: id
				}, function(data) {
					if (data.code == 0) {
						toastr.success(data.msg);
						doSearch();
					} else {
						modbox.confirm({
								body: data.msg,
								okButton: {
									label: "<?php echo lang('button.ok'); ?>",
								},
								closeButton: {
									label: "<?php echo lang('button.cancel'); ?>",
								}
							})
							.then(() => {
								$.post(`/${resource}/do_delete`, {
									id: id,
									force: 1,
								}, function(data) {
									toastr.success(data.msg);
									doSearch();
								})
							})
							.catch(() => console.log('okButton not clicked'));
					}
				}, 'json');
			});
	}

	function operateFormatter(value, row) {
		<?php if ($auth == 5) : ?>

			return `<div class="btn-list flex-nowrap">
			<a href="#" onClick="remove_template('TemplateController', ${row.id})" class="link-danger">
                <i class="bi bi-x-square"></i>
			</a>
			<a href="/templateController/export?id=${row.id}">
			<i class="bi bi-file-earmark-arrow-down"></i>
			</a>
		</div>`;
		<?php endif ?>
	}

	$("#batch_delete").click(function() {
		var selections = $table.bootstrapTable("getSelections");
		if (selections.length) {
			let id = selections.map((item) => {
				return item.id;
			});
			remove_resource("TemplateController", id);
		}
	});


	$('#create_template').on('click', function() {

	});
	var import_btn = $('#import_excel');
	if (import_btn.length) {
		import_btn.on("change", function() {
			//获取到选中的文件
			var input = document.querySelector("#import_excel");
			var file = input.files[0];

			if (!file) {
				return;
			}
			var formdata = new FormData();
			formdata.append("file", file);

			input.value = '';
			$.ajax({
				url: "/TemplateController/html5_import",
				type: "post",
				processData: false,
				contentType: false,
				data: formdata,
				dataType: 'json',

				success: function(res) {
					if (res.code == 0) {
						toastr.success(res.msg);
						doSearch();
					} else {
						toastr.error(res.msg);
					}


				},

			})
		});
	}
</script>