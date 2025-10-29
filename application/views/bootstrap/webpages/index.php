<div class="container-fluid">
  <!-- Page title -->
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <div class="page-pretitle">
        </div>
        <h2 class="page-title">
          <?php echo lang('webpage'); ?>
        </h2>
      </div>
      <?php if ($auth >= $ADMIN) : ?>
        <div class="col-auto ms-auto">
          <div class="btn-list">


            <a href="/webpages/edit" class=" btn btn-primary">
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
      <table id="table" class="table table-striped table-responsive" id="table" data-toggle="table" data-url="/webpages/getTableData" data-sort-name="name" data-sort-order="asc">
        <thead>
          <tr>
            <th data-checkbox="true"></th>
            <th data-field="name" data-sortable="true" data-formatter="nameFormatter"><?php echo lang('name'); ?></th>
            <th data-field="descr" data-sortable="true"><?php echo lang('desc'); ?></th>
            <th data-field="play_time" data-sortable="true"><?php echo lang('play_time'); ?></th>
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
      ret = `	<a href="/webpages/edit?id=${row.id}" class="link-primary">
                   ${value}
                </a>`
    <?php endif ?>
    return ret;
  }
</script>