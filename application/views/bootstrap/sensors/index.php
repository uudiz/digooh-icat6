<div class="container-fluid">
  <!-- Page title -->
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <div class="page-pretitle">
        </div>
        <h2 class="page-title">
          <?php echo lang('sensor_thresholds'); ?>
        </h2>
      </div>
      <?php if ($auth >= $ADMIN) : ?>
        <div class="col-auto ms-auto">
          <div class="btn-list">

            <a href="/Threshold_controller/edit" class=" btn btn-primary">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
              <?php echo lang('create'); ?>
            </a>
            <input type=file id=import_excel style="display:none" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
          </div>
        </div>
      <?php endif ?>
    </div>
  </div>

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
      <table id="table" class="table table-striped table-responsive" id="table" data-toggle="table" data-url="/threshold_controller/getTableData" data-sort-name="name" data-sort-order="asc">
        <thead>
          <tr>
            <th data-checkbox="true"></th>
            <th data-field="name" data-sortable="true" data-formatter="nameFormatter"><?php echo lang('name'); ?></th>
            <th data-field="descr" data-sortable="true"><?php echo lang('desc'); ?></th>
            <th data-field="min_temp" data-sortable="true" data-formatter="temperatureFormatter"><?php echo (lang('temperature') . " (°C)"); ?></th>
            <th data-field="min_humidity" data-sortable="true" data-formatter="humidityFormatter"><?php echo lang('humidity') . " (%RH)"; ?></th>
            <th data-field="min_power" data-sortable="true" data-formatter="powerFormatter"><?php echo lang('power_consumption') . " (W·h)"; ?></th>

            <th data-formatter="operateFormatter"><?php echo lang('operate'); ?></th>
          </tr>
        </thead>
      </table>
    </div>

  </div>
</div>

<script>
  function nameFormatter(value, row, index) {
    ret = value;
    <?php if ($auth == 5) : ?>
      ret = `	<a href="/threshold_controller/edit?id=${row.id}" class="link-primary">
                   ${value}
                </a>`
    <?php endif ?>
    return ret;
  }

  function temperatureFormatter(value, row, index) {
    return value + " - " + row.max_temp;
  }

  function humidityFormatter(value, row, index) {
    return value + " - " + row.max_humidity;
  }

  function powerFormatter(value, row, index) {
    return value + " - " + row.max_power;
  }

  var $table = $('#table');



  function operateFormatter(value, row) {
    return `<div class="btn-list flex-nowrap">

			<a href="#" onClick="remove_resource('threshold_controller', ${row.id})" class="link-danger">
                <i class="bi bi-x-square"></i>
			</a>
		</div>`;
  };

  function operateFormatter(value, row, index) {
    var html = '<div class="btn-list flex-nowrap">';
    html += `
			<a href="#" onClick="remove_resource('threshold_controller', ${row.id})">
				<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash text-red" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
					<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
					<line x1="4" y1="7" x2="20" y2="7"></line>
					<line x1="10" y1="11" x2="10" y2="17"></line>
					<line x1="14" y1="11" x2="14" y2="17"></line>
					<path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
					<path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
				</svg>
			</a>`;




    html += `</div>`;
    return html;
  };


  $("#batch_delete").click(function() {
    var selections = $table.bootstrapTable('getSelections');
    if (selections.length) {
      let id = selections.map((item) => {
        return item.id
      });
      remove_resource('Sensor_controller', id);
    }
  });
</script>