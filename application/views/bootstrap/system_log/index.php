<div class="container-fluid">
  <!-- Page title -->
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <div class="page-pretitle">
        </div>
        <h2 class="page-title">
          <?php echo lang('log'); ?>
        </h2>
      </div>

    </div>
  </div>

  <div class="page-body">
    <div class='pb-2'>

      <form class="row align-items-center justify-content-end" id='toolbar'>
        <div class="col-auto row gx-1">
          <div class="col-auto">
            <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo date("Y-m-d", strtotime('-1 month')); ?>">
          </div>
          <div class="col-auto">
            <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo date("Y-m-d", time()); ?>">
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group row">
            <label class="form-label col-auto col-form-label"><?php echo lang('company'); ?></label>
            <div class="col">
              <select data-placeholder="" id="filterCompany" name="company_id" class="form-select select2">
                <option value="-1"><?php echo lang('all'); ?></option>
                <?php if (isset($companies)) : ?>
                  <?php foreach ($companies as $company) : ?>
                    <option value="<?php echo $company->id; ?>"><?php echo $company->name; ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>

              </select>
            </div>
          </div>
        </div>
      </form>
    </div>

    <table id="table" class="table table-striped table-responsive" id="table" data-toggle="table" data-url="/logger/getTableData" data-sort-name="id" data-sort-order="asc">
      <thead>
        <tr>
          <th data-field="add_time" data-sortable="true"><?php echo lang('logger.time'); ?></th>
          <th data-field="user_name" data-sortable="true"><?php echo lang('user'); ?></th>
          <th data-field="company_name" data-sortable="true"><?php echo lang('company'); ?></th>
          <th data-field="ip" data-sortable="true"><?php echo lang('ip'); ?></th>
          <th data-field="detail" data-sortable="true"><?php echo lang('action'); ?></th>
        </tr>
      </thead>
    </table>
  </div>
</div>