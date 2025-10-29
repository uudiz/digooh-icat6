<div class="container-fluid">
  <!-- Page title -->
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <div class="page-pretitle">
        </div>
        <h2 class="page-title">
          <?php echo lang('user'); ?>
        </h2>
      </div>
      <?php if ($auth == $ADMIN) : ?>
        <div class="col-auto ms-auto">
          <div class="btn-list">

            <a href="/user/edit" class=" btn btn-primary">
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
    <form class="pb-2" id="toolbar">
      <div class="row justify-content-end">
        <?php if ($auth == 10) : ?>
          <div class="col-md-2 row">
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
        <?php endif ?>
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
      </div>
    </form>
    <table id="table" class="table table-striped table-responsive" id="table" data-toggle="table" data-url="/user/getTableData" data-sort-name="name" data-sort-order="asc">
      <thead>
        <tr>
          <th data-field="name" data-formatter="nameFormatter" data-sortable="true"><?php echo lang('name'); ?></th>
          <th data-field="descr" data-sortable="true"><?php echo lang('desc'); ?></th>
          <th data-field="auth" data-sortable="true" data-formatter="rolesFormatter"><?php echo lang('rule'); ?></th>
          <?php if ($auth == 10) : ?>
            <th data-field="company" data-sortable="true"><?php echo lang('company'); ?></th>
          <?php endif ?>
          <th data-formatter="operateFormatter"><?php echo lang('operate'); ?></th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<script>
  function rolesFormatter(value, row) {
    var role = '';
    if (value == 5) {
      role = "<?php echo lang('rule.admin'); ?>";
    } else if (value == 4) {
      role = "<?php echo lang('role.staff'); ?>";
    } else if (value == 0) {
      role = "<?php echo lang('rule.view'); ?>";
    } else if (value == 1) {
      role = "<?php echo lang('rule.franchise'); ?>";
    }
    return role;
  };

  function nameFormatter(value, row, index) {
    ret = value;
    <?php if ($auth >= 5) : ?>
      ret = `	<a href="/user/edit?id=${row.id}" class="link-primary">
                   ${value}
                </a>`
    <?php endif ?>
    return ret;
  }

  function operateFormatter(auth, row) {

    var html = `<div class="btn-list flex-nowrap">

			<a href="#" onClick="remove_resource('user', ${row.id})" class="link-danger" title="<?php echo lang('delete') ?>">
        <i class="bi bi-x-square"></i>
			</a>`;
    <?php if ($auth == 10) : ?>
      html = html + `<a href="#" onclick='doLoginUser("${row.name}","${row.password}")' class="link-primary" title="Login">
          <i class="bi bi-box-arrow-in-left"></i>
        </a>`;
    <?php endif ?>
    html = html + '</div>';
    return html;
  };

  function doLoginUser(name, password) {
    $.post('/login/doRedirect', {
      username: name,
      password: password,
      redirect: true,
    }, function(data) {
      if (data.code != 0) {
        toastr.error(data.msg);
      } else {
        window.location = '/';
      }
    }, 'json');

  }
</script>