<div class="container-fluid">
  <!-- Page title -->
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col-6">
        <div class="page-pretitle">
        </div>
        <h2 class="page-title">
          <?php echo lang('ssp.criteria'); ?>
        </h2>
      </div>
      <?php if ($auth >= $ADMIN) : ?>
        <div class="col-auto ms-auto">
          <div class="btn-list">

            <a href="#" class="btn btn-white" onclick="document.getElementById('import_excel').click();return false;">
              <i class="bi bi-file-earmark-arrow-up"></i>
              <?php echo lang('import'); ?>
            </a>

            <a href="/criteriaSSP/edit" class=" btn btn-primary">
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
          <select class="form-select" name="type" data-placeholder="" id="filter_categories_type">
            <option value="-1" selected><?php echo lang('all'); ?></option>
            <?php if (isset($types)): ?>
              <?php foreach ($types as $type) : ?>
                <option value="<?php echo $type->id ?>"><?php echo $type->name ?></option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>
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

    <table id="table" class="table table-striped table-responsive" id="table" data-toggle="table" data-url="/criteriaSSP/getTableData" data-sort-name="name" data-sort-order="asc">
      <thead>
        <tr>
          <th data-checkbox="true"></th>
          <th data-field="name" data-formatter="nameFormatter" data-sortable="true"><?php echo lang('name'); ?></th>
          <th data-field="code" data-sortable="true"><?php echo lang('ssp.criteria.id'); ?></th>
          <th data-field="type_name" data-sortable="true"><?php echo lang('ssp.org'); ?></th>
          <th data-field="descr" data-sortable="true"><?php echo lang('desc'); ?></th>
          <th data-field="player_count" data-sortable="true"><?php echo lang('criteria.pcount'); ?></th>
          <th data-formatter="operateFormatter"><?php echo lang('operate'); ?></th>
        </tr>
      </thead>
    </table>
  </div>
</div>
<script>
  var $table = $('#table');

  function typeFormatter(value) {
    var typeStr = "N/A";
    if (value === '0') {
      typeStr = "dmi";
    } else if (value === '1') {
      typeStr = "dpaa";
    } else if (value === '2') {
      typeStr = "ilab";
    } else if (value === '3') {
      typeStr = "openooh";
    }
    return typeStr;
  }

  function nameFormatter(value, row, index) {
    ret = value;
    <?php if ($auth == 5) : ?>
      ret = `	<a href="/criteriaSSP/edit?id=${row.id}" class="link-primary">
                   ${value}
                </a>`
    <?php endif ?>
    return ret;
  }

  function operateFormatter(value, row) {
    return `<div class="btn-list flex-nowrap">
			<a href="#" onClick="remove_resource('criteriaSSP', ${row.id})"  class="link-danger" title="<?php echo lang('delete') ?>">
                <i class="bi bi-x-square"></i>
			</a>
		</div>`;
  };


  $("#batch_delete").click(function() {
    var selections = $table.bootstrapTable('getSelections');
    if (selections.length) {
      let id = selections.map((item) => {
        return item.id
      });
      remove_resource('criteriaSSP', id);
    }
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
        url: "/criteriaSSP/do_upload",
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