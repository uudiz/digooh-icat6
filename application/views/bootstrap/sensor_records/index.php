<div class="container-fluid">
  <!-- Page title -->
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <div class="page-pretitle">
        </div>
        <h2 class="page-title">
          <?php echo lang('sensor_reports'); ?>
        </h2>
      </div>

    </div>
  </div>

  <div class="page-body">
    <div class='pb-2'>
      <form class="row align-items-center justify-content-end" id='toolbar'>


        <div class="col-auto row gx-1">
          <label class="form-label col-auto col-form-label"><?php echo lang('date.range'); ?></label>
          <div class="col-auto">
            <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo date("Y-m-d", strtotime('-1 month')); ?>">
          </div>
          <div class="col-auto">
            <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo date("Y-m-d", time()); ?>">
          </div>
        </div>

        <div class="col-auto ">
          <label class="form-check ">
            <input type="checkbox" class="form-check-input" name="notified_only" />
            <span class="form-check-label "><?php echo lang('above_range'); ?></span>
          </label>
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

    <div class="card-table table-responsive">
      <table id="table" class="table table-striped table-responsive table-vcenter" id="table" data-toggle="table" data-url="/healthy_controller/getTableData" data-sort-name="date" data-sort-order="desc">
        <thead>
          <tr>
            <th data-checkbox="true"></th>
            <th data-field="date" data-sortable="true"><?php echo lang('date'); ?></th>
            <th data-field="player_name" data-sortable="true"><?php echo lang('player') ?></th>
            <th data-field="min_temp" data-sortable="true"><?php echo lang('minimum') . ' ' . lang('temperature'); ?></th>
            <th data-field="max_temp" data-sortable="true"><?php echo lang('maximum') . ' ' . lang('temperature'); ?></th>
            <th data-field="min_humidity" data-sortable="true"><?php echo lang('minimum') . ' ' . lang('humidity'); ?></th>
            <th data-field="max_humidity" data-sortable="true"><?php echo lang('maximum') . ' ' . lang('humidity'); ?></th>
            <th data-field="min_power" data-sortable="true"><?php echo lang('minimum') . ' ' . lang('power_consumption'); ?></th>
            <th data-field="max_power" data-sortable="true"><?php echo lang('maximum') . ' ' . lang('power_consumption'); ?></th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>