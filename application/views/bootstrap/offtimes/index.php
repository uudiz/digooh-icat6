<div class="container-fluid">
  <!-- Page title -->
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <div class="page-pretitle">
        </div>
        <h2 class="page-title">
          <?php echo lang('off.times'); ?>
        </h2>
      </div>

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

    <div class="card-table table-responsive">
      <table id="table" class="table table-striped table-responsive table-vcenter" id="table" data-toggle="table" data-url="/powersController/getTableData" data-sort-name="off_at" data-sort-order="desc">
        <thead>
          <tr>
            <th data-checkbox="true"></th>
            <th data-field="name" data-sortable="true"><?php echo lang('name'); ?></th>
            <th data-field="off_at" data-sortable="true"><?php echo lang('off_at'); ?></th>
            <th data-field="on_at" data-sortable="true"><?php echo lang('on_at'); ?></th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>