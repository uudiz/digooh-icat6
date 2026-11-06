<div class="container-fluid">
  <!-- Page title -->
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col-6">
        <div class="page-pretitle">

        </div>
        <h2 class="page-title">
          <?php echo lang('campaign'); ?>
        </h2>
      </div>

      <div class="col-auto ms-auto">
        <div class="btn-list">
          <?php if ($auth >=  $ADMIN) : ?>

            <a href="#" class="btn btn-danger" onclick="refresh_all_campaigns()">
              <i class="bi bi-arrow-clockwise"></i>
              <?php echo lang('refresh'); ?>
            </a>
          <?php endif ?>

          <?php if ($auth >= 4) : ?>
            <a href="/campaign/edit" class=" btn btn-primary">
              <i class="bi bi-plus-lg"></i>
              <?php echo lang('create'); ?>
            </a>
          <?php endif ?>
        </div>
      </div>

    </div>
  </div>
  <div class="page-body">
    <form class="row align-items-center justify-content-end pb-2" id='toolbar'>
      <div class="col-lg-2 col-md-3 col-sm-6">
        <div class="form-group row">
          <label class="form-label col-auto col-form-label"><?php echo lang('criteria'); ?></label>
          <div class="col">
            <select data-placeholder="" id="filterCriteria" name="criterion" class="form-select select2">
              <option value="-1"><?php echo lang('all'); ?></option>
              <?php if (isset($criteria)) : ?>
                <?php foreach ($criteria as $cri) : ?>
                  <option value="<?php echo $cri->id; ?>"><?php echo $cri->name; ?></option>
                <?php endforeach; ?>
              <?php endif; ?>

            </select>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-md-3 col-sm-6">
        <div class="form-group row">
          <label class="form-label col-auto col-form-label"><?php echo lang('player'); ?></label>
          <div class="col">
            <select data-placeholder="" id="filterPlayer" name="player" class="form-select select2">
              <option value="-1"><?php echo lang('all'); ?></option>
              <?php if (isset($players)) : ?>
                <?php foreach ($players as $cri) : ?>
                  <option value="<?php echo $cri->id; ?>"><?php echo $cri->name; ?></option>
                <?php endforeach; ?>
              <?php endif; ?>

            </select>
          </div>
        </div>
      </div>
      <div class="col-lg-2 col-md-3 col-sm-6">
        <div class="form-group row">
          <label class="form-label col-auto col-form-label"><?php echo lang('priority'); ?></label>
          <div class="col">
            <select data-placeholder="" name="priority" class="form-select select2">
              <option value="-1"><?php echo lang('all'); ?></option>
              <option value="1"><?php echo lang('priority.high'); ?></option>
              <option value="2"><?php echo lang('priority.low'); ?></option>
              <?php if ($auth == 5) : ?>
                <option value="5"><?php echo lang('priority.reservation'); ?></option>
                <?php if ($this->config->item('campaign_with_tags')) : ?>
                  <option value="4"><?php echo lang('priority.trail'); ?></option>
                <?php endif ?>
              <?php endif ?>
              <option value="6"><?php echo lang('priority.simple'); ?></option>
              <option value="3"><?php echo lang('priority.fillin'); ?></option>
              <?php if ($this->config->item('ssp_feature')) : ?>
                <option value="7"><?php echo lang('Programmatic.fillIn'); ?></option>
              <?php endif ?>
              <option value="8"><?php echo lang('priority.extension'); ?></option>
            </select>
          </div>
        </div>
      </div>
      <div class="col-lg-2 col-md-3 col-sm-6">
        <div class="form-group row">
          <label class="form-label col-auto col-form-label"><?php echo lang('tag'); ?></label>
          <div class="col">
            <select data-placeholder="" id="filterTag" name="tag" class=" form-select select2">
              <option value="-1"><?php echo lang('all'); ?></option>
              <?php if (isset($tags)) : ?>
                <?php foreach ($tags as $cri) : ?>
                  <option value="<?php echo $cri->id; ?>"><?php echo $cri->name; ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>
        </div>
      </div>

      <div class="col-auto">
        <label class="form-check">
          <input type="checkbox" class="form-check-input" name="with_expired" />
          <span class="form-check-label"><?php echo lang('with.expired'); ?></span>
        </label>
      </div>

      <?php if ($auth == 5 && !$pid) : ?>
        <div class="col-auto">
          <label class="form-check">
            <input type="checkbox" class="form-check-input" name="with_partners" />
            <span class="form-check-label"><?php echo lang('with.partners'); ?></span>
          </label>
        </div>
      <?php endif ?>
      <div class="col-auto">
        <div class="input-icon">
          <input type="text" id="search" class="form-control " name="search" placeholder="">
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

    <div class='table-responsive'>
      <table id='table' class="table table-striped table-responsive" id="table" data-toggle="table" data-url="/campaign/getTableData" data-sort-name="update_time" data-sort-order="desc">
        <thead>
          <tr>
            <th data-field="name" data-formatter="nameFormatter" data-sortable="true"><?php echo lang('name'); ?></th>
            <th data-field="criteria_name" data-formatter="criteriaFormatter"><?php echo lang('criteria_player'); ?></th>
            <th data-field="players_cnt" data-formatter="playerCountFormatter"><?php echo lang('device.count'); ?></th>
            <th data-field="published" data-sortable="true" data-formatter="publishedFormatter"> <?php echo lang('published'); ?></th>
            <th data-field="priority" data-sortable="true" data-formatter="priorityFormatter" data-align="center" data-width="60"> <?php echo lang('priority'); ?></th>
            <th data-field="play_count" data-formatter="playCountFormatter"> <?php echo lang('campaign.count'); ?></th>
            <th data-field="start_date" data-sortable="true" data-formatter='dateFormatter'> <?php echo lang('date.range'); ?></th>
            <th data-field="start_timeH" data-formatter="timeFormatter" data-sortable="true"> <?php echo lang('time.range'); ?></th>
            <th data-field="update_time" data-sortable="true"> <?php echo lang('update.time'); ?></th>
            <th data-formatter="operateFormatter" data-width="60"><?php echo lang('operate'); ?></th>

          </tr>
        </thead>
      </table>
    </div>
  </div>

  <div class="modal fade" id="campaign-playersModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <div class="card">
            <div class="card-header">
              <h2 class="card-title"><?php echo lang('selected.players') ?><span id="sel-cnt"></span></h2>
            </div>

            <table class="table table-striped table-responsive table-sm" data-toggle="table" data-height="300" data-pagination="false" data-search="false" id="affectedPlayerTable">
              <thead>
                <tr>
                  <th data-field="name"><?php echo lang('name'); ?></th>
                  <th data-field="custom_sn1">SDAW ID</th>
                  <th data-field="custom_sn2"> QID</th>
                  <th data-field="daysperweek"><?php echo lang('days.per.week') ?></th>
                  <th data-field="workingdays"><?php echo lang('days.in.campaign') ?></th>
                </tr>
              </thead>
            </table>

          </div>
          <div class="card">
            <div class="card-header">
              <h2 class="card-title"><?php echo lang('excluded.players') ?><span id="ex-cnt"></span></h2>
            </div>
            <table class=" table table-striped table-responsive table-sm" data-toggle="table" data-height="200" data-search="false" data-pagination="false" id="excludedPlayerTable">
              <thead>
                <tr>
                  <th data-field="name"><?php echo lang('name'); ?></th>
                  <th data-field="custom_sn1">SDAW ID</th>
                  <th data-field="custom_sn2"> QID</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <a id="export_devices" class="btn btn-primary"><span><?php echo lang('export'); ?></span></a>
          <button type="button" class="btn" data-bs-dismiss="modal"><?php echo lang('button.close') ?></button>
        </div>
      </div>
    </div>
  </div>

  <?php if ($auth == 5) : ?>
    <div class="modal fade" id="campaign-copyModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form class="modal-content" id="copyForm" autocomplete="off">
          <input type="hidden" id="copy_id" name="id">
          <input type="hidden" id="copy_start_date" name="start_date">
          <input type="hidden" id="copy_end_date" name="end_date">
          <div class="modal-header align-items-start h-auto py-3">
            <div>
              <div class="text-muted mb-3"><?php echo lang('campaign.copy.source') ?></div>
              <a href="#" id="copy_source" target="_blank" rel="noopener"></a>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo lang('button.close') ?>"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label text-muted" for="copy_name"><?php echo lang('campaign.copy.name') ?></label>
              <input type="text" class="form-control" id="copy_name" name="name">
            </div>
            <div class="row align-items-start">
              <label class="col-sm-6 col-form-label text-muted" for="copy_period"><?php echo lang('campaign.copy.period') ?></label>
              <div class="col-sm-6 col-md-4">
                <input type="text" class="form-control" id="copy_period" name="copy_period" readonly>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="submit" id="copy_submit" class="btn btn-link link-success p-0" title="<?php echo lang('campaign.copy') ?>">
              <i class="bi bi-subtract" style="font-size: 2rem;"></i>
            </button>
            <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal"><?php echo lang('button.cancel') ?></button>
          </div>
        </form>
      </div>
    </div>
  <?php endif ?>

  <?php
  $this->load->view("bootstrap/campaigns/refreshModal");
  ?>
</div>

<script src="/assets/bootstrap/js/litepicker.js"></script>
<script>
  var $table = $('#table')


  function nameFormatter(value, row, index) {
    ret = value;

    ret = `	<a href="/campaign/edit?id=${row.id}" class="link-primary">
                   ${value}
                </a>`
    return ret;
  }

  function playerCountFormatter(value, row, index) {

    return `${row.players_cnt}/${row.ex_players_cnt}`;

  }

  function criteriaFormatter(value, row, index) {
    var criteria = value ? value + (row.player_name ? "/" + row.player_name : "") : row.player_name ? row.player_name : "";
    var tooltip = escapeHtml(criteria);

    if (criteria && criteria.length > 50) {
      criteria = criteria.substring(0, 50) + "...";
    }
    return `<span data-bs-toggle="tooltip" data-bs-html="true" data-placement="bottom" data-bs-container="body" title="${tooltip}" >${criteria}</span>`;

  }

  function timeFormatter(index, row) {
    var html = '';
    if (row.time_flag == '1') {
      html = '';
    } else {
      html = (row.start_timeH < 10 ? '0' + row.start_timeH : row.start_timeH) + ':' + (row.start_timeM < 10 ? '0' + row.start_timeM : row.start_timeM) + '-' +
        (row.end_timeH < 10 ? '0' + row.end_timeH : row.end_timeH) + ':' + (row.end_timeM < 10 ? '0' + row.end_timeM : row.end_timeM)
    }

    return html;
  }

  function playCountFormatter(value, row) {
    var cnt = '';
    if (row.priority == 3) {
      return "";
    }
    switch (row.play_cnt_type) {
      case '0':
        cnt = row.play_count;
        break;
      case '1':
        cnt = row.play_weight + '%';
        break;
      case '2':
        cnt = row.play_total;
        break;
      case '9':
        cnt = "<?php echo "Every " . $xslot . "th"; ?>"
        break;
    }
    return cnt;
  }

  function formatDate(date) {
    var d = new Date(date),
      month = '' + (d.getMonth() + 1),
      day = '' + d.getDate(),
      year = d.getFullYear();

    if (month.length < 2)
      month = '0' + month;
    if (day.length < 2)
      day = '0' + day;

    return [year, month, day].join('-');
  }

  function dateFormatter(value, row) {
    var CurrentDate = new Date();
    var SelectedDate = new Date(row.end_date);
    var color = '';
    if (formatDate(CurrentDate) > formatDate(SelectedDate)) {
      color = "text-red";
    }
    return `<span class="${color}">${value}~${row.end_date}</span>`;

    return '';
  };

  function priorityFormatter(value, row) {
    //data-formatter=
    // data-sort-name="id" data-sort-order="desc"
    var priority = ' ';


    switch (value) {
      case '0':
        priority = "<?php echo lang('priority.dedicated'); ?>";
        break;
      case '1':
        priority = "<?php echo lang('priority.high'); ?>";
        break;
      case '2':
        priority = "<?php echo lang('priority.low'); ?>";
        break;
      case '3':
        priority = "<?php echo lang('priority.fillin'); ?>";
        break;
      case '4':
        priority = "<?php echo lang('priority.trail'); ?>";
        break;
      case '5':
        priority = "<?php echo lang('priority.reservation'); ?>";
        break;
      case '7':
        priority = "<?php echo lang('Programmatic.fillIn'); ?>";
        break;
      case '8':
        priority = "<?php echo lang('priority.extension'); ?>";
        break;
      default:
        priority = "<?php echo lang('priority.simple'); ?>";
    }


    return priority;
  }

  function publishedFormatter(value, row) {
    //data-formatter=
    // data-sort-name="id" data-sort-order="desc"
    //+ value ? "text-green" : "text-red" + 
    var color = value === '1' ? "text-green" : "text-red";
    /*return '<span class="' + color + '">\
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-filled icon-tabler icon-tabler-circle" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">\
          <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>\
          <circle cx="12" cy="12" r="9"></circle>\
        </svg>\
    </span>'
    */

    var color = value === '1' ? "status-green status-indicator-animated" : "status-red status-indicator-animated";
    if (row.priority == 5 && value === '1') {
      color = "status-blue status-indicator-animated";
    }
    return `<span class="status-indicator ${color}">
            <span class="status-indicator-circle"></span>
            <span class="status-indicator-circle"></span>
            <span class="status-indicator-circle"></span>
          </span>`;
  }


  function signalFormatter(value, row) {
    var icon = '';
    if (row.humidity == 3 || row.humidity == 4) {
      icon = 'bi-reception-' + value;
    } else if (row.humidity == 2) {
      if (value < 3) {
        icon = 'bi-wifi-' + (value - 1);
      } else {
        icon = 'bi-wifi';
      }
    } else {
      return '<i class="text-success bi bi-ethernet"></i>'
    }
    return '<i class="text-success bi ' + icon + '" data-bs-toggle="tooltip" data-bs-placement="top" title="' + value + '"></i>'
  }

  function show_devices(id) {
    var myModal = new bootstrap.Modal(document.getElementById('campaign-playersModal'));
    $.getJSON('/campaign/getAffectedPlayers', {
      id: id
    }, function(res) {

      var p_array = [];
      var p_count = 0;
      p_count = res.affect_players ? res.affect_players.length : '0';
      p_array = res.affect_players ? res.affect_players : [];
      $('#affectedPlayerTable').bootstrapTable('load', p_array);
      $('#sel-cnt').html(" : " + p_count);

      p_count = res.exclude_players ? res.exclude_players.length : '0';
      p_array = res.exclude_players ? res.exclude_players : [];
      $('#excludedPlayerTable').bootstrapTable('load', p_array);
      $('#ex-cnt').html(" : " + p_count);
      $('#export_devices').attr('href', `/campaign/export_devices?id=${id}`);
    });

    myModal.show();
  }

  function operateFormatter(value, row) {
    var html = `<div class="btn-list flex-nowrap">`;

    <?php if ($auth > 1 && $auth != 4) : ?>
      html += `<a href="#" onClick="remove_campaign(${row.id})" class="link-danger" title="<?php echo lang('delete') ?>">
        <i class="bi bi-x-square"></i>
			</a>`

    <?php endif ?>
    html += `<a href="#" onClick="show_devices('${row.id}')" class="link-primary" title="<?php echo lang('player') ?>">
        <h3><i class="dig-display"></i></h3>
			</a>
      `;
    <?php if ($auth == 5) : ?>
      if (row.company_id == <?php echo (int) get_instance()->get_cid() ?>) {
        html += `<a href="#" onClick="copy_campaign(${row.id})" class="link-primary" title="<?php echo lang('campaign.copy') ?>">
        <i class="bi bi-subtract"></i>
			</a>`
      }
    <?php endif ?>
    html += `</div>`;
    return html;
  };


  function refresh_all_campaigns() {
    $.ajaxSettings.async = true;
    $('#refresh_confirm-prompt').html('<?php echo lang('campaign.refresh.msg') ?>');

    $('#refresh_confirm').modal("show")
      .off('click').on('click', '#refresh', function(e) {
        showSpinner();
        $.get("/campaign/do_refresh_all_campaigns", function(data) {
          hideSpinner();

          if (data.code == 0) {
            toastr.success(data.msg);
            doSearch();
          } else {
            toastr.error(data.msg);
          }

        }, 'json');
      });
  }

  // ---- copy campaign dialog ----
  var copyPicker = null;
  var copyValidator = null;

  function ymdToDate(ymd) {
    var p = String(ymd || '').split('-');
    return p.length == 3 ? new Date(p[0], p[1] - 1, p[2]) : new Date();
  }

  function ymdToDmy(ymd) {
    var p = String(ymd || '').split('-');
    return p.length == 3 ? `${p[2]}-${p[1]}-${p[0]}` : '';
  }

  function init_copy_dialog() {
    copyPicker = new Litepicker({
      element: document.getElementById('copy_period'),
      singleMode: false,
      format: 'DD-MM-YYYY',
      numberOfMonths: 2,
      numberOfColumns: 2,
      lang: '<?php echo get_instance()->config->item('language') == 'germany' ? 'de-DE' : 'en-US' ?>',
      setup: (picker) => {
        picker.on('selected', (date1, date2) => {
          $('#copy_start_date').val(date1.format('YYYY-MM-DD'));
          $('#copy_end_date').val(date2.format('YYYY-MM-DD'));
          $('#copy_period').valid();
        });
      }
    });

    copyValidator = $('#copyForm').validate({
      errorClass: 'is-invalid',
      validClass: '',
      errorElement: 'div',
      errorPlacement: function(error, element) {
        error.addClass('invalid-feedback').insertAfter(element);
      },
      rules: {
        name: {
          required: true,
          maxlength: 128,
          normalizer: function(value) {
            return $.trim(value);
          },
          remote: {
            url: '/campaign/checkName',
            data: {
              name: function() {
                return $.trim($('#copy_name').val());
              }
            }
          }
        },
        copy_period: {
          required: true
        }
      },
      submitHandler: function() {
        do_copy_campaign();
        return false;
      }
    });
  }

  function copy_campaign(id) {
    showSpinner();
    $.getJSON('/campaign/get_copy_info', {
      id: id
    }, function(data) {
      hideSpinner();
      if (data.code != 0) {
        toastr.error(data.msg);
        return;
      }
      if (!copyPicker) {
        init_copy_dialog();
      }
      copyValidator.resetForm();
      $('#copyForm .is-invalid').removeClass('is-invalid');

      $('#copy_id').val(data.id);
      $('#copy_source').text(data.name).attr('href', `/campaign/edit?id=${data.id}`);
      $('#copy_name').val(data.copy_name);
      $('#copy_start_date').val(data.start_date);
      $('#copy_end_date').val(data.end_date);
      copyPicker.setDateRange(ymdToDate(data.start_date), ymdToDate(data.end_date));
      $('#copy_period').val(`${ymdToDmy(data.start_date)} - ${ymdToDmy(data.end_date)}`);
      // extended campaigns keep the period of their main campaign
      $('#copy_period').prop('disabled', data.priority == 8);
      $('#copy_submit').prop('disabled', false);

      bootstrap.Modal.getOrCreateInstance(document.getElementById('campaign-copyModal')).show();
    }).fail(function() {
      hideSpinner();
      toastr.error('<?php echo lang('campaign.copy.fail') ?>');
    });
  }

  function do_copy_campaign() {
    $('#copy_submit').prop('disabled', true);
    showSpinner();
    $.post('/campaign/do_copy', {
      id: $('#copy_id').val(),
      name: $.trim($('#copy_name').val()),
      start_date: $('#copy_start_date').val(),
      end_date: $('#copy_end_date').val(),
    }, function(data) {
      if (data.code == 0) {
        window.location.href = `/campaign/edit?id=${data.id}`;
        return;
      }
      hideSpinner();
      $('#copy_submit').prop('disabled', false);
      toastr.error(data.msg);
    }, 'json').fail(function() {
      hideSpinner();
      $('#copy_submit').prop('disabled', false);
      toastr.error('<?php echo lang('campaign.copy.fail') ?>');
    });
  }

  function remove_campaign(id) {
    $.post(
      `/campaign/do_delete_check`, {
        id: id,
      },
      function(data) {
        if (data.code == 0) {
          $("#delete_confirm_text").html(data.msg);
          $("#delete_confirm")
            .modal("show")
            .off("click")
            .on("click", "#delete", function(e) {
              $.post(
                `/campaign/do_delete`, {
                  id: id,
                },
                function(deleteData) {
                  if (deleteData.code == 0) {
                    toastr.success(deleteData.msg);
                    doSearch();
                  } else {
                    toastr.error(deleteData.msg);
                  }
                },
                "json"
              );
            });
        } else {
          toastr.error(data.msg);
        }

      },
      "json"
    );

  }

  $(document).keypress(function(e) {
    if (e.which == 13) {
      doSearch();
    }
  });
</script>