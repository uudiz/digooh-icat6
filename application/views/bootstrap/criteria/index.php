<div class="container-fluid">
  <!-- Page title -->
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <div class="page-pretitle">
        </div>
        <h2 class="page-title">
          <?php echo lang('criteria'); ?>
        </h2>
      </div>
      <?php if ($auth >= $ADMIN) : ?>
        <div class="col-auto ms-auto">
          <div class="btn-list">
            <a href="#" class="btn btn-white" onclick="document.getElementById('import_excel').click();return false;">
              <i class="bi bi-file-earmark-arrow-up"></i>
              <?php echo lang('import'); ?>
            </a>

            <a href="/criteria/edit" class=" btn btn-primary">
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
      <table id="table" class="table table-striped table-responsive" id="table" data-toggle="table" data-url="/criteria/getTableData" data-sort-name="name" data-sort-order="asc">
        <thead>
          <tr>
            <th data-checkbox="true"></th>
            <th data-field="name" data-sortable="true" data-formatter="nameFormatter"><?php echo lang('name'); ?></th>
            <th data-field="descr" data-sortable="true"><?php echo lang('desc'); ?></th>
            <th data-field="player_count" data-sortable="true"><?php echo lang('criteria.pcount'); ?></th>
            <th data-formatter="operateFormatter"><?php echo lang('operate'); ?></th>
          </tr>
        </thead>
      </table>
    </div>

    <div class="modal" id="deletionAlertModal" tabindex="-1">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="modal-status bg-danger"></div>
          <div class="modal-body text-center py-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M12 9v2m0 4v.01" />
              <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" />
            </svg>
            <h3></h3>
            <div class="text-secondary"><?php echo lang('criteria.cannot_be_deleted') ?></div>
          </div>
          <div class="modal-footer">
            <div class="w-100">
              <div class="row">
                <div class="col"><a href="#" class="btn btn-danger w-100" data-bs-dismiss="modal">
                    <?php echo lang('ok'); ?>
                  </a></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function nameFormatter(value, row, index) {
    ret = value;
    <?php if ($auth == 5) : ?>
      ret = `	<a href="/criteria/edit?id=${row.id}" class="link-primary">
                   ${value}
                </a>`
    <?php endif ?>
    return ret;
  }
  $("#batch_delete").click(function() {
    var selections = $table.bootstrapTable('getSelections');
    if (selections.length) {
      let id = selections.map((item) => {
        return item.id
      });
      remove_criteria(id);
    }
  });

  function operateFormatter(value, row) {
    return `<div class="btn-list flex-nowrap">

			<a href="#" onClick="remove_criteria(${row.id})" class="link-danger">
                <i class="bi bi-x-square"></i>
			</a>
		</div>`;
  };

  function remove_criteria(id) {
    $("#delete_confirm")
      .modal("show")
      .off("click")
      .on("click", "#delete", function(e) {
        $.post(
          `/criteria/do_delete`, {
            id: id,
          },
          function(data) {
            if (data.code == 0) {
              toastr.success(data.msg);
              doSearch();
            } else {
              toastr.error(data.msg);
            }
          },
          "json"
        );
      });
  }
</script>