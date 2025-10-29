<div class="container-fluid">
  <!-- Page title -->
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <div class="page-pretitle">
        </div>
        <h2 class="page-title">
          <?php echo lang('player.usage'); ?>
        </h2>
      </div>
      <div class="col-auto">

        <a href="#" class="btn btn-white" onclick="exportReport();">

          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-export" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
            <path d="M11.5 21h-4.5a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v5m-5 6h7m-3 -3l3 3l-3 3"></path>
          </svg>
          <?php echo lang('export'); ?>
        </a>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class='pb-2'>
      <form class="row align-items-center justify-content-end" id='usageToolbar'>



        <div class="col-md-2">
          <div class="form-group row g-1">
            <label class="form-label col-auto col-form-label"><?php echo lang('criteria'); ?></label>
            <div class="col">
              <select id="criteria" name="criteria" class="form-select select2">
                <option value="-1"><?php echo lang('all'); ?></option>
                <?php if (isset($criteria)) : ?>
                  <?php foreach ($criteria as $c) : ?>
                    <option value="<?php echo $c->id; ?>"><?php echo $c->name; ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
          </div>
        </div>

        <div class="col-auto">
          <div class="input-icon">
            <input type="text" id='usageSearch' class="form-control " placeholder="">
            <span class="input-icon-addon">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <circle cx="10" cy="10" r="7"></circle>
                <line x1="21" y1="21" x2="15" y2="15"></line>
              </svg>
            </span>
          </div>
        </div>
        <div class="col-auto row align-items-center gx-1">

          <label class="form-check col-auto">
            <input type="checkbox" class="form-check-input" name='dataCheck' id="dataCheck" />
            <span class="form-check-label"><?php echo lang('date.range'); ?></span>
          </label>

          <div class="col row gx-1 data-range" style="display:none">
            <div class="col-auto">
              <input type="date" id="start_date" name="end_date" class="form-control" value="<?php echo date("Y-m-d", time()); ?>">
            </div>
            <div class="col-auto">
              <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo date("Y-m-d", strtotime('+1 month')); ?>">
            </div>
          </div>
        </div>
        <div class="col-auto">
          <a href="#" class="btn btn-primary" onclick="doQuery()">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
              <circle cx="10" cy="10" r="7"></circle>
              <line x1="21" y1="21" x2="15" y2="15"></line>
            </svg>
            <?php echo lang('button.query'); ?>
          </a>
        </div>

      </form>

    </div>

    <table id="table" class="table table-striped table-responsive" id="table" data-toggle="table" data-sort-name="name" data-sort-order="asc" data-query-params="playbackQueryParams">
      <thead>
        <tr>
          <th data-field="name" data-sortable="true"><?php echo lang('player'); ?></th>
          <th data-field="sn" data-sortable="true"><?php echo lang('sn'); ?></th>
          <th data-field="least_free"><?php echo lang('least.free') ?></th>
          <th data-field="day7_capcity"><?php echo lang('next.7.day'); ?></th>
          <th data-field="nextmon_capacity"><?php echo lang('next.month'); ?></th>
          <th data-field="next6mon_capacity"><?php echo lang('next.6.month'); ?></th>

        </tr>
      </thead>
    </table>
  </div>
</div>

<script>
  function doQuery() {
    $('#table').bootstrapTable('refresh', {
      url: '/usage/getTableData'
    })
  }

  function playbackQueryParams(params) {
    params.start_date = $('#start_date').val();
    params.end_date = $('#end_date').val();
    params.search = $('#usageSearch').val();
    params.with_range = $('#dataCheck').is(":checked") ? 1 : 0
    var criterion = $('#criteria').val();
    if (criterion > -1) {
      params.criteria = criterion;
    }

    return params;
  }

  function exportReport() {
    var params = playbackQueryParams({});
    var withDate = $('#dataCheck').is(':checked');
    var req = "?with_date=" + (withDate ? 1 : 0);

    if (params.search.length) {
      req += '&search=' + params.search;
    }

    var criterion = $('#criteria').val();
    if (criterion > -1) {
      req += '&criteria=' + criterion;
    }

    if (withDate) {
      var startDate = $('#start_date').val();
      var endDate = $('#end_date').val();
      if (new Date(endDate) < new Date(startDate)) {
        toastr.error("End Date must >= Start Date");
        return;

      }
      req += "&start_date=" + startDate + '&end_date=' + endDate;

    }

    var url = '/usage/excel/' + req;
    var xhr = null;
    try {
      xhr = new XMLHttpRequest()
    } catch (e) {
      xhr = new ActiveXObject("Microsoft.XMLHTTP")
    }

    xhr.open('get', url, true);
    xhr.responseType = "blob"; // 返回类型blob

    xhr.onload = function() {
      // 请求完成
      if (this.status === 200) { //返回200

        var response = this.response;
        var URL = window.URL || window.webkitURL || window;
        var link = document.createElement('a');
        link.href = URL.createObjectURL(response);
        link.download = this.getResponseHeader('File-Name');
        var event = document.createEvent('MouseEvents');
        event.initMouseEvent('click', true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
        link.dispatchEvent(event);
      }
    };
    xhr.send();

  }

  $('#dataCheck').on('change', function() {
    if ($('#dataCheck').is(':checked')) {
      $('.data-range').show();
    } else {
      $('.data-range').hide();
    }
  });
</script>