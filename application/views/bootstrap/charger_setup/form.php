<script src="/assets/bootstrap/js/jquery.validate.min.js"></script>
<?php if ($lang == 'germany') : ?>
	<script src="/assets/js/validation/messages_de.js"></script>
<?php endif ?>

<div class="row">
	<div class="col-12 col-lg-8 m-auto pt-3 pb-2 mb-3">
		<div class="card">
			<div class="card-header">
				<h2><?php echo $title ?></h2>
			</div>
			<form id="statusDataForm" action="/status_controller/do_save">
				<div class="card-body">
					<div id="validateTips"> </div>

					<div class="row g-3">

						<div class="mb-2">
							<label for="name"><?php echo lang('name'); ?></label>
							<input type="text" class="form-control" name="name" required value="<?php if (isset($data->name)) echo $data->name; ?>" />
						</div>
						<div class="mb-2">
							<label for="descr"><?php echo lang("desc"); ?></label>
							<textarea type="text" class="form-control" name="descr" rows="2"><?php if (isset($data->descr)) echo $data->descr; ?></textarea>
						</div>

						<input type="hidden" id="id" name="id" value="<?php echo isset($data->id) ? $data->id : 0; ?>" />
						<div class="mb-3">
							<label for="descr"><?php echo lang("status"); ?></label>
							<div>
								<table class="table table-responsive table-vcenter" id="table" data-toggle="table" data-url="/status_controller/getStatusTableData" data-pagination="false" data-query-params="statusQueryParams">
									<thead>
										<tr>
											<th data-field="name" data-formatter="nameFormatter"><?php echo lang('status'); ?></th>
											<th data-field="translation" data-formatter="translationFormatter">translation</th>
											<th data-field="bg_color" data-formatter="colorFormatter"><?php echo lang('bg.color'); ?></th>
											<th data-field="font_color" data-formatter="colorFormatter"><?php echo lang('color'); ?></th>
										</tr>
									</thead>
									<tbody>
										<tr></tr>
									</tbody>
								</table>
							</div>
						</div>

					</div>
					<div class="card-footer">
						<button class="btn btn-outline-primary" type="submit"><i class="bi bi-cloud-arrow-up"></i><?php echo lang('button.save'); ?></button>
						<a class="btn  btn-outline-primary" href="/status_controller"><i class="bi bi-x-circle"></i><?php echo lang('button.cancel'); ?></a>
					</div>
			</form>
		</div>
	</div>
</div>


<script type="text/javascript">
	function statusQueryParams(params) {
		params['id'] = $("#id").val();
		return params;
	}

	function titleCase(s) {
		var i, ss = s.toLowerCase().split(/\s+/);
		for (i = 0; i < ss.length; i++) {
			ss[i] = ss[i].slice(0, 1).toUpperCase() + ss[i].slice(1);
		}
		return ss.join(' ');
	}

	function nameFormatter(value, row, index) {
		return titleCase(value);
	}

	function translationFormatter(value, row, index) {
		return `<input type="text" class="form-control" value="${value}" onchange="valueChange(value,'${index}','${this.field}')"/>`;
	};

	function colorFormatter(value, row, index) {

		return `<input type="color" class="form-control form-control-color" value="${value}" onchange="valueChange(value,'${index}','${this.field}')"/>`;
	}


	function translationChange(value, index, field) {

		$("#table").bootstrapTable('updateCell', {
			index: index,
			field: field,
			value: value,
			reinit: false
		})

	}

	function valueChange(value, index, field) {

		$("#table").bootstrapTable('updateCell', {
			index: index,
			field: field,
			value: value,
			reinit: false
		})
	}

	$(document).ready(function() {
		$("#statusDataForm")
			.submit(function(e) {
				e.preventDefault();
			})
			.validate({
				lang: localStorage.getItem("language") == "germany" ? "de" : "en",
				submitHandler: function(form, e) {
					e.preventDefault();
					const form_url = $("form#statusDataForm").attr("action");
					if (!form_url.length) {
						alert("please check action url");
						return;
					}
					var resource = "/" + form_url.split("/")[1];

					var params = {};
					$("form#statusDataForm :input[name]").each(function() {
						var inputs = $(this);
						inputs.each(function() {
							if (this.type === "checkbox") {
								params[`${this.name}`] = $(this).is(":checked") ? 1 : 0;
							} else if ($(this).val().length !== 0) {
								params[`${this.name}`] = $(this).val();
							}

						});
					});

					var tableData = $("#table").bootstrapTable('getData', false);

					const newAry = tableData.map(item => {
						return {
							bg_color: item.bg_color,
							font_color: item.font_color,
							api_status_id: item.api_status_id,
							translation: item.translation,
						}
					});
					params['statusData'] = newAry;

					$.post(
						form_url,
						params,
						function(data) {
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
								window.location.href = resource;
							}
						},
						"json"
					);
					return false;
				},
			});
	});
</script>