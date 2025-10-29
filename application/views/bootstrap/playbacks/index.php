<div class="container-fluid">
  <!-- Page title -->
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <div class="page-pretitle">
        </div>
        <h2 class="page-title">
          <?php echo lang('playback'); ?>
        </h2>
      </div>
      <div class="col-auto ms-auto">
        <?php if ($this->config->item('new_playback_detail')) : ?>
          <?php if (!$this->config->item('with_template') && $auth == 5) : ?>
            <a href="#" class="btn btn-white" onclick="exportSummary();">
              <i class="bi bi-clipboard-data"></i>
              <?php echo lang('summary'); ?>
            </a>
          <?php endif ?>
        <?php endif ?>
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
      <form class="row align-items-center justify-content-end" id='playbackToolbar'>
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
            <label class="form-label col-auto col-form-label"><?php echo lang('campaign'); ?></label>
            <div class="col">
              <select id="campaign" name="campaign" class="select2_ajax form-select"></select>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group row">
            <label class="form-label col-auto col-form-label"><?php echo lang('player'); ?></label>
            <div class="col">
              <select id="player" name="player" class="select2_ajax form-select"></select>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group row">
            <label class="form-label col-auto col-form-label"><?php echo lang('media'); ?></label>
            <div class="col">
              <select id="media" name="media" class="select2_ajax form-select"></select>
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
          <?php if (!$this->config->item('with_template')) : ?>
            <a href="#" class="btn btn-primary" onclick="noPlaybackQuery()">
              <i class="bi bi-clipboard-x"></i>
              <?php echo lang('no_playback'); ?>
            </a>
          <?php endif ?>
        </div>
      </form>
    </div>

    <table id="table" class="table table-striped table-responsive" id="table" data-toggle="table" data-sort-name="post_date" data-sort-order="asc" data-query-params="playbackQueryParams">
      <thead>
        <tr>
          <th data-field="post_date" data-sortable="true"><?php echo lang('date.time'); ?></th>
          <th data-field="player_name" data-sortable="true"><?php echo lang('player'); ?></th>
          <th data-field="media_name" data-sortable="true"><?php echo lang('media'); ?></th>
          <th data-field="campaign_name" data-sortable="true"><?php echo lang('campaign'); ?></th>
          <?php if (!$this->config->item("with_template")) : ?>
            <th data-field="planed_times" data-sortable="true"><?php echo lang('planed_times'); ?></th>
          <?php endif ?>
          <th data-field="times" data-sortable="true"><?php echo lang('times'); ?></th>
          <th data-field="duration" data-sortable="true"><?php echo lang('duration'); ?></th>
          <?php if (!$this->config->item("with_template")) : ?>
            <th data-field="fulfillment_planed" data-sortable="true"><?php echo lang('fulfillment_planed'); ?></th>
            <th data-field="fulfillment_booked" data-sortable="true"><?php echo lang('fulfillment_booked'); ?></th>
          <?php endif ?>

        </tr>
      </thead>
    </table>
  </div>
</div>

<script>
  function doQuery() {
    $('#table').bootstrapTable('refresh', {
      url: '/playback/getTableData'
    })
  }

  function noPlaybackQuery() {
    $('#table').bootstrapTable('refresh', {
      url: '/playback/getNoPlaybackTableData'
    })
  }


  function playbackQueryParams(params) {
    params.start_date = $('#start_date').val();
    params.end_date = $('#end_date').val();
    var option = $("#campaign option:selected");
    if (option.val() > 0) {
      params.campaign = option.text();
    }
    var option = $("#player option:selected");
    if (option.val() > 0) {
      params.player = option.val();
    }
    var option = $("#media option:selected");
    if (option.val() > 0) {
      params.media = option.text();
    }
    return params;
  }

  function exportReport() {
    var params = playbackQueryParams({})
    var req = '?start_date=' + params.start_date + '&end_date=' + params.end_date;
    var option = $("#campaign option:selected");
    if (option.val() > 0) {
      req += '&campaign=' + option.text();
    }
    var option = $("#media option:selected");
    if (option.val() > 0) {
      req += '&media=' + option.text();
    }
    var option = $("#player option:selected");
    if (option.val() > 0) {
      req += '&player=' + option.val();
    }
    window.location.href = '/playback/excel' + req;
  }

  function exportSummary() {
    var params = playbackQueryParams({})
    var req = '?start_date=' + params.start_date + '&end_date=' + params.end_date;
    var option = $("#campaign option:selected");
    if (option.val() > 0) {
      req += '&campaign=' + option.text();
    }
    var option = $("#media option:selected");
    if (option.val() > 0) {
      req += '&media=' + option.text();
    }
    var option = $("#player option:selected");
    if (option.val() > 0) {
      req += '&player=' + option.val();
    }
    window.location.href = '/playback/summary' + req;
  }



  $(document).ready(function() {
    $('.select2_ajax').each(function() {

      var id = '#' + this.id;
      var thisID = this.id;
      $(this).select2({
        theme: "bootstrap-5",
        width: '100%',
        placeholder: 'Search for ' + thisID,
        ajax: {
          url: "/playback/get_select_data",
          type: 'post',
          dataType: 'json',
          delay: 150,
          cache: true,
          data: function(params) {
            return {
              type: thisID,
              q: params.term, // search term
              page: params.page
            };
          },
          processResults: function(data, params) {
            params.page = params.page || 1;
            return {
              results: data.items,
              pagination: {
                more: (params.page * 20) < data.total_count
              }
            };
          },
        },

        minimumInputLength: 1,
        allowClear: true,
      });
    });
  });
  document.addEventListener("keypress", function onEvent(event) {
    if (event.key === "Enter") {
      doQuery();
      // Do something better
    }
  });
</script>