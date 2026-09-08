<div class="container-fluid">
  <!-- Page title -->
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <div class="page-pretitle">
        </div>
        <h2 class="page-title">
          <?php echo lang('ssp.servers'); ?>
        </h2>
      </div>
      <?php if ($auth >= $ADMIN) : ?>
        <div class="col-auto ms-auto">
          <div class="btn-list">
            <a href="/sspServers/edit" class="btn btn-primary">
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

    <table id="table" class="table table-striped table-responsive" data-toggle="table" data-url="/sspServers/getTableData" data-sort-name="name" data-sort-order="asc">
      <thead>
        <tr>
          <th data-field="name" data-sortable="true" data-formatter="nameFormatter"><?php echo lang('name'); ?></th>
          <th data-field="host" data-sortable="true"><?php echo lang('ssp.host'); ?></th>
          <th data-field="parser_class" data-sortable="true"><?php echo lang('ssp.parser.class'); ?></th>
          <th data-field="priority" data-sortable="true"><?php echo lang('ssp.priority'); ?></th>
          <th data-field="timeout" data-sortable="true"><?php echo lang('ssp.timeout'); ?></th>
          <th data-field="is_active" data-formatter="activeFormatter" data-sortable="true"><?php echo lang('status'); ?></th>
          <?php if ($auth >= $ADMIN) : ?>
            <th data-formatter="operateFormatter"><?php echo lang('operate'); ?></th>
          <?php endif ?>
        </tr>
      </thead>
    </table>
  </div>
</div>

<script>
  var $table = $('#table');

  function nameFormatter(value, row, index) {

    ret = `	<a href="/sspServers/edit?id=${row.id}" class="link-primary">
                   ${value}
                </a>`
    return ret;
  }

  function activeFormatter(value, row) {
    return value == 1 ?
      `<span class="badge bg-success"><?php echo lang('ssp.active'); ?></span>` :
      `<span class="badge bg-secondary"><?php echo lang('ssp.inactive'); ?></span>`;
  }

  function operateFormatter(value, row) {
    return `<div class="btn-list flex-nowrap">
			<a href="#" onClick="remove_resource('sspServers', ${row.id})" class="link-danger" title="<?php echo lang('delete'); ?>">
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
  }
</script>