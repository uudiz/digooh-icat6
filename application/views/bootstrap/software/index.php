<div class="container-fluid">
  <!-- Page title -->
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <div class="page-pretitle">
        </div>
        <h2 class="page-title">
          <?php echo lang('software'); ?>
        </h2>
      </div>
      <?php if ($auth == 10) : ?>
        <?php
        $this->load->view("bootstrap/software/uploader");
        ?>
        <div class="col-auto ms-auto">
          <div class="btn-list">

            <a href="#" class=" btn btn-primary" id="uploader" data-bs-toggle="modal" data-bs-target="#uploadFirmware">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-import" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                <path d="M5 13v-8a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2h-5.5m-9.5 -2h7m-3 -3l3 3l-3 3"></path>
              </svg>
              <?php echo lang('upload'); ?>
            </a>
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

    <table id="table" class="table table-striped table-responsive" id="table" data-toggle="table" data-url="/software/getTableData" data-sort-name="add_time" data-sort-order="desc" data-detail-view="true" data-detail-formatter="detailFormatter">
      <thead>
        <tr>
          <th data-checkbox="true"></th>
          <th data-field="name" data-formatter="nameFormatter" data-sortable="true"><?php echo lang('name'); ?></th>
          <th data-field="version" data-sortable="true"><?php echo lang('version'); ?></th>
          <th data-field="mpeg_core" data-sortable="true"><?php echo lang('f.model'); ?></th>

          <th data-field="descr" data-sortable="true"><?php echo lang('desc'); ?></th>
          <th data-field="publish_time" data-sortable="true"><?php echo lang('publish.time'); ?></th>
          <th data-field="add_time" data-sortable="true"><?php echo lang('update.time'); ?></th>

          <th data-formatter="operateFormatter"><?php echo lang('operate'); ?></th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<script>
  function nameFormatter(value, row, index) {
    ret = value;
    <?php if ($auth == 10) : ?>
      ret = `<a href="/software/edit?id=${row.id}" class="link-primary">
                   ${value}
                </a>`
    <?php endif ?>
    return ret;
  }

  function operateFormatter(value, row, index) {
    var html = '<div class="btn-list flex-nowrap">';

    html += `<a href="#"  onclick="toggleExpand(${index})" class="link-primary bi bi-chevron-bar-expand">
            </a>`;
    html += `</div>`;
    return html;
  };

  function detailFormatter(index, row) {
    var html = '';
    $.ajaxSettings.async = false;
    $.get(
      `/software/detail?id=${row.id}&version=${row.version}&mpeg_core=${row.mpeg_core_org}`,
      function(data) {
        html = data;
      }
    );
    $.ajaxSettings.async = true;
    return html;
  }
  var $table = $('#table');



  function toggleExpand(index) {
    $table.bootstrapTable('toggleDetailView', index);
  }

  $("#batch_delete").on('click', function() {
    var selections = $table.bootstrapTable('getSelections');
    if (selections.length) {
      let id = selections.map((item) => {
        return item.id
      });
      remove_resource('software', id);
    }
  });
</script>