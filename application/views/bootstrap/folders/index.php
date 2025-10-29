<link href="/assets/bootstrap/css/jquery.treegrid.css" rel="stylesheet">
<script src="/assets/bootstrap/js/jquery.treegrid.min.js"></script>
<script src="/assets/bootstrap/js/bootstrap-table-treegrid.min.js"></script>

<div class="container-fluid">
  <!-- Page title -->
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <div class="page-pretitle">
        </div>
        <h2 class="page-title">
          <?php echo lang('folders'); ?>
        </h2>
      </div>
      <?php if ($auth >= $ADMIN) : ?>
        <div class="col-auto ms-auto">
          <div class="btn-list">
            <a href="/folder/edit" class=" btn btn-primary">
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

    <table id="table" class="table table-striped table-responsive" id="table" data-toggle="table" data-url="/folder/getTableData" data-pagination=false data-height="700" data-tree-show-field='name' data-parent-id-field='pId' data-sort-name="name" data-sort-order="asc" data-root-parent-id="<?php echo $tree_root; ?>">
      <thead>
        <tr>
          <th data-field="name" data-sortable="true" data-formatter="nameFormatter"><?php echo lang('name'); ?> </th>
          <th data-field="descr" data-sortable="true"><?php echo lang('desc'); ?></th>
          <th data-field="start_date" data-sortable="true" data-formatter="dateFormatter"><?php echo lang('date.range'); ?></th>
          <th data-formatter="operateFormatter"><?php echo lang('operate'); ?></th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<script>
  var $table = $('#table');
  $(function() {
    $('#table').on('post-body.bs.table', function(e) {
      var columns = $table.bootstrapTable('getOptions').columns

      if (columns && columns[0][0].visible) {
        $table.treegrid({
          treeColumn: 0,
          initialState: 'expanded',
          onChange: function() {
            $table.bootstrapTable('resetView')
          }
        })
      }
    })
  })

  function nameFormatter(value, row, index) {
    ret = value;
    if (row.noDel) {
      return value;
    }
    ret = `	<a href="/folder/edit?id=${row.id}" class="link-primary">
                   ${value}
                </a>`;
    return ret;
  }

  function dateFormatter(value, row) {
    if (row.date_flag == '1') {
      var CurrentDate = new Date();
      var SelectedDate = new Date(row.end_date);
      var color = '';
      if (CurrentDate.getTime() > SelectedDate.getTime) {
        color = "text-red";
      }

      return `<span class="${color}">${value}~${row.end_date}</span>`;
    }
    return '';
  };

  function operateFormatter(value, row) {
    var html = `<div class="btn-list flex-nowrap">`;

    if (!row.noDel || (row.noDel && row.noDel == '0')) {
      html += `<a href="#" onClick="remove_resource('folder', ${row.id})"  class="link-danger" title="<?php echo lang('delete') ?>" >
        <i class="bi bi-x-square"></i>`;
    }

    html += `<a href="/folder/edit?parent_id=${row.id}" class="link-primary">
        <i class="bi bi-folder-plus"></i>
			</a>
		</div>`;

    return html;

  }
</script>