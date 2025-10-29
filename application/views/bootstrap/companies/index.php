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
          <?php echo lang('company') . " (" . lang('total.players') . $all_online_player_count . ")" ?>
        </h2>
      </div>
      <?php if ($auth >= $ADMIN) : ?>
        <div class="col-auto ms-auto">
          <div class="btn-list">
            <a href="/company/edit" class=" btn btn-primary">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
              <?php echo lang('create'); ?>
            </a>
          </div>
        </div>
      <?php endif ?>
    </div>
  </div>
  <div class="page-body">
    <table id="table" class="table table-striped table-responsive" id="table" data-toggle="table" data-url="/company/getTableData" data-pagination=false data-height="700" data-tree-show-field='name' data-parent-id-field='pId' data-sort-name="name" data-sort-order="asc">
      <thead>
        <tr>
          <th data-field="name" data-sortable="true" data-formatter="nameFormatter"><?php echo lang('name'); ?></th>
          <th data-field="descr" data-sortable="true"><?php echo lang('desc'); ?></th>
          <th data-field="max_user" data-sortable="true"><?php echo lang('max.user'); ?></th>
          <th data-field="start_date" data-sortable="true"><?php echo lang('start.date'); ?></th>
          <th data-field="stop_date" data-sortable="true"><?php echo lang('end.date'); ?></th>
          <th data-field="players_count" data-sortable="true"><?php echo "All " . lang('player.count'); ?></th>
          <th data-field="online_count" data-sortable="true"><?php echo "Online " . lang('player.count'); ?></th>
          <th data-field="auto_dst" data-sortable="true"><?php echo lang('auto.dst'); ?></th>
          <th data-formatter="operateFormatter"><?php echo lang('operate'); ?></th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<script>
  var $table = $('#table');
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
    return `<a href="/company/edit?id=${row.id}" class="link-primary">
                   ${value}
                </a>`

  }

  function operateFormatter(value, row) {
    var hide_style = '';
    if (row.pId > '0') {
      hide_style = 'display:none';
    }
    var ret = `<div class="btn-list flex-nowrap">
			<a href="#" onClick="remove_resource('company', ${row.id})" class="link-danger" title="<?php echo lang('delete') ?>">
        <i class="bi bi-x-square"></i>
			</a>
      <a href="/user/edit?company_id=${row.id}" >
        <i class="bi bi-person-plus"></i>
      </a>`;
    <?php if ($this->config->item('with_partners')) : ?>
      ret += ` <a href="/company/edit?parent_id=${row.id}" style="${hide_style}" title="add partner">
        <i class="bi bi-plus-square"></i>
      </a>`
    <?php endif ?>
    ret += `</div>`;
    return ret;

  };

  function autostartFormatter(value, row) {
    if (value === '0') {
      return row.dst_start + "~" + row.dst_end;
    }
    return "OFF";
  }
</script>