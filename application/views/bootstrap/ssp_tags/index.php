<div class="container-fluid">
  <!-- Page title -->
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <div class="page-pretitle">
        </div>
        <h2 class="page-title">
          <?php echo lang('ssp.tags'); ?>
        </h2>
      </div>
      <?php if ($auth >= $ADMIN) : ?>
        <div class="col-auto ms-auto">
          <div class="btn-list">
            <span class="d-none d-sm-inline">
              <a href="#" class="btn btn-white" onclick="document.getElementById('import_excel').click();return false;">
                <i class="bi bi-file-earmark-arrow-up"></i>
                <?php echo lang('import'); ?>
              </a>
            </span>
            <a href="/tagSSP/edit" class=" btn btn-primary">
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

    <table id="table" class="table table-striped table-responsive" id="table" data-toggle="table" data-url="/tagSSP/getTableData" data-sort-name="name" data-sort-order="asc">
      <thead>
        <tr>
          <th data-checkbox="true"></th>
          <th data-field="name" data-formatter="nameFormatter" data-sortable="true"><?php echo lang('name'); ?></th>
          <th data-field="descr" data-sortable="true"><?php echo lang('desc'); ?></th>
          <th data-field="player_cnt" data-sortable="true"><?php echo lang('criteria.pcount'); ?></th>
          <th data-formatter="operateFormatter"><?php echo lang('operate'); ?></th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<script>
  var $table = $('#table');


  function nameFormatter(value, row, index) {
    ret = value;
    <?php if ($auth == 5) : ?>
      ret = `	<a href="/tagSSP/edit?id=${row.id}" class="link-primary">
                   ${value}
                </a>`
    <?php endif ?>
    return ret;
  }

  function operateFormatter(value, row) {
    return `<div class="btn-list flex-nowrap">
			<a href="/tagSSP/edit?id=${row.id}" class="link-secondary">
				<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
					<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
					<path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4"></path>
					<line x1="13.5" y1="6.5" x2="17.5" y2="10.5"></line>
				</svg>
			</a>
			<a href="#" onClick="remove_resource('tagSSP', ${row.id})">
				<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash text-red" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
					<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
					<line x1="4" y1="7" x2="20" y2="7"></line>
					<line x1="10" y1="11" x2="10" y2="17"></line>
					<line x1="14" y1="11" x2="14" y2="17"></line>
					<path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
					<path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
				</svg>
			</a>
		</div>`;
    return `<div class="btn-list flex-nowrap">
			<a href="#" onClick="remove_resource('tagSSP', ${row.id})"  class="link-danger" title="<?php echo lang('delete') ?>">
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
      remove_resource('tagSSP', id);
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
        url: "/tagSSP/do_upload",
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