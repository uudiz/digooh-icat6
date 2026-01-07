<link rel="stylesheet" type="text/css" href="/assets/calendar/main.min.css" />

<script src="/assets/calendar/main.min.js"></script>

<div class="container-fluid">
  <!-- Page title -->
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col-6">
        <div class="page-pretitle">
        </div>
        <h2 class="page-title">
          <?php echo lang('player'); ?>
        </h2>
      </div>
      <?php if (($auth == 4 || $auth == $ADMIN) && !$pid) : ?>
        <div class="col-auto ms-auto">
          <div class="btn-list">
            <!--
            <span class="d-none d-sm-inline">
      -->
            <a href="#" class="btn btn-white" onclick="document.getElementById('import_excel').click();return false;">

              <i class="bi bi-file-earmark-arrow-up"></i>
              <?php echo lang('import'); ?>
            </a>
            <a href="#" class="btn btn-white" onclick="exportPlayers();">
              <i class="bi bi-file-earmark-arrow-down"></i>
              <?php echo lang('export'); ?>
            </a>
            <!-- </span>
              -->
            <?php if ($auth == $ADMIN) : ?>
              <a href="/player/edit" class=" btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <?php echo lang('create'); ?>
              </a>
            <?php endif ?>
            <input type=file id=import_excel style="display:none" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
          </div>
        </div>
      <?php endif ?>
    </div>
  </div>
  <div class="page-body">
    <div class='pb-2'>
      <?php if ($auth >= $ADMIN && !$pid) : ?>
        <div class="row" id="batch_operation" style="display: none;">
          <div class="col-auto">
            <button class="btn btn-primary" onclick="sendCommand(1)"><?php echo lang('restart'); ?></button>
            <button class="btn btn-danger" onclick="sendCommand(8)"><?php echo lang('format'); ?></button>
            <button class="btn btn-primary" onclick="sendCommand(3)"><?php echo lang('control.type3'); ?></button>
            <button class="btn btn-primary" onclick="sendCommand(4)"><?php echo lang('control.type4'); ?></button>
            <button class="btn btn-primary" onclick="sendCommand(0)"><?php echo lang('audio'); ?></button>
            <?php if ($auth == 10) : ?>
              <button class="btn btn-primary" onclick="sendCommand(10)">Detail Log</button>
            <?php endif ?>
          </div>
        </div>
      <?php endif ?>
      <form class="row align-items-center justify-content-end pb-2" id='toolbar' onsubmit="return false;">
        <?php if ($auth != 10) : ?>
          <div class="col-md-3 col-sm-6 row">
            <div class="col-auto">
              <label class="form-label col-auto col-form-label" for="filterCriteria"><?php echo lang('criteria'); ?></label>
            </div>
            <div class="col">
              <select data-placeholder="" id="filterCriteria" name='criteria' class="form-select select2">
                <option value="-1"><?php echo lang('all'); ?></option>
                <?php if (isset($criteria)) : ?>
                  <?php foreach ($criteria as $c) : ?>
                    <option value="<?php echo $c->id; ?>"><?php echo $c->name; ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
          </div>
        <?php endif ?>
        <div class="col-md-3 col-sm-6 row">
          <div class="col-auto">
            <label class="form-label col-auto col-form-label" for="filterCriteria"><?php echo lang('status'); ?></label>
          </div>
          <div class="col">
            <select data-placeholder="" name='status' class="form-select select2">
              <option value="-1"><?php echo lang('all'); ?></option>
              <option value="1"><?php echo lang('status.1'); ?></option>
              <option value="2"><?php echo lang('status.2'); ?></option>
              <option value="3"><?php echo lang('status.3'); ?></option>
              <option value="4"><?php echo lang('status.4'); ?></option>
              <option value="5"><?php echo lang('status.5'); ?></option>
              <option value="12"><?php echo lang('status.12'); ?></option>
              <option value="0"><?php echo lang('status.0'); ?></option>
            </select>
          </div>
        </div>

        <?php if ($this->config->item('has_sensor')) : ?>
          <div class="col-auto ">
            <label class="form-check ">
              <input type="checkbox" class="form-check-input" name="healthy_status" />
              <span class="form-check-label "><?php echo lang('above_range'); ?></span>
            </label>
          </div>
        <?php endif ?>
        <div class="col-auto">
          <div class="input-icon">
            <input type="text" id="search" name='search' class="form-control " placeholder="">
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
    <div class="table-responsive">
      <table id='table' class="table table-striped table-responsive" id="table" data-toggle="table" data-url="/player/getTableData" data-sort-name="last_connect" data-sort-order="desc" data-detail-view="true">
        <thead>
          <tr>
            <?php if ($auth >= $ADMIN && !$pid) : ?>
              <th data-checkbox="true"></th>
            <?php endif ?>
            <th data-field="name" data-sortable="true" data-formatter="nameFormatter"><?php echo lang('name'); ?></th>
            <th data-field="sn" data-sortable="true"> <?php echo lang('sn'); ?></th>
            <th data-field="status" data-sortable="true" data-formatter="statusFormatter" data-align="center" data-width="60"> <?php echo lang('status'); ?></th>
            <th data-field="descr" data-sortable="true" data-formatter="descFormatter"> <?php echo lang('desc'); ?></th>
            <th data-field="criteria_name"> <?php echo lang('criteria'); ?></th>
            <th data-field="last_connect" data-sortable="true"> <?php echo lang('last.connect'); ?></th>
            <th data-field="timecfg" data-sortable="true" data-formatter="timerFormatter"> <?php echo lang('timecfg'); ?></th>
            <th data-field="temperature" data-sortable="true" data-formatter="signalFormatter" data-align="center" data-width="40"> <?php echo lang('signal3g_strength'); ?></th>
            <th data-field="setupdate" data-sortable="true"> <?php echo lang('setup_date'); ?></th>
            <?php if ($auth > $ADMIN) : ?>
              <th data-field="company_name" data-sortable="true"> <?php echo lang('company'); ?></th>
            <?php endif ?>
            <?php if (!$pid) : ?>
              <th data-formatter="operateFormatter" data-width="60"><?php echo lang('operate'); ?></th>
            <?php endif ?>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

<div class="modal modal-blur fade" id="command_confirm" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-status bg-danger"></div>
      <div class="modal-body text-center py-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
          <path d="M12 9v2m0 4v.01" />
          <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" />
        </svg>
        <h3></h3>
        <div class="text-muted" id="prompt"></div>
      </div>
      <div class="modal-footer">
        <div class="w-100">
          <div class="row">
            <div class="col"><a href="#" class="btn w-100" data-bs-dismiss="modal">
                <?php echo lang('button.cancel'); ?>
              </a></div>
            <div class="col"><a href="#" class="btn btn-danger w-100" data-bs-dismiss="modal" id="confirm">
                <?php echo lang('button.ok'); ?>
              </a></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-blur fade" id="screen_shot" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-full-width modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-status bg-danger"></div>
      <div class="modal-body text-center py-4">
        <img id="screenshot_img" />
      </div>
      <div class="modal-footer">
        <div class="row">
          <div class="col-auto">
            <h1 id='captured_at' class="text-muted"></h1>
          </div>
          <div class="col-auto">
            <a id="screenshot_delete" href="#" class="btn btn-danger">
              <?php echo lang('delete') ?>
            </a>
          </div>
          <div class="col-auto">
            <a id="screenshot_download" href="#" class="btn btn-primary" download="screenshot.png">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-download" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                <line x1="12" y1="11" x2="12" y2="17"></line>
                <polyline points="9 14 12 17 15 14"></polyline>
              </svg>
            </a>
          </div>
          <div class="col-auto">
            <a href="#" class="btn w-100" data-bs-dismiss="modal">
              <?php echo lang('button.close'); ?>
            </a>
          </div>
        </div>
      </div>
      <input type="hidden" id="screenshot_row_id" />

    </div>
  </div>
</div>

<div class="modal modal-blur fade" id="export-report" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-bod">
        <div class="card">
          <div class="card-body ">
            <div class="mb-3">
              <label class="form-label"><?php echo lang('player.export.campaign') ?></label>
              <input type="hidden" id="export_row_id" />
              <div class="datepicker-inline" id="datepicker-inline"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-blur fade" id="modal-calendar" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-full-width modal-dialog-scrollable"" role=" document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?php echo lang('calendar') ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id='calendar'></div>
      </div>
      <div class="modal-footer">
        <input type="hidden" id="cal_player_id" />
      </div>
    </div>
  </div>
</div>

<script src="/assets/bootstrap/js/litepicker.js"></script>
<script src="/assets/bootstrap/js/dayjs.min.js"></script>

<script>
  function nameFormatter(value, row, index) {
    ret = "";
    <?php if ($auth == 5 && !$pid) : ?>
      <?php if ($this->config->item('has_sensor')) : ?>
        if (row.threshold_id) {
          $outOfRange = false;
          var thresholds = row.thresholds;

          if (row.electric) {
            var power = Number(row.electric);
            if (!isNaN(power) && (power < thresholds.min_power || power > thresholds.max_power)) {
              $outOfRange = true;
            }
          }
          if (row.dampness) {
            var humidity = Number(row.dampness);
            if (!isNaN(humidity) && (humidity < thresholds.min_humidity || humidity > thresholds.max_humidity)) {
              $outOfRange = true;
            }
          }
          if (row.temp) {
            var temperature = Number(row.temp);
            if (!isNaN(temperature) && (temperature < thresholds.min_temp || temperature > thresholds.max_temp)) {
              $outOfRange = true;
            }
          }

          if ($outOfRange) {

            ret = `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler text-warning icon-tabler-alert-circle" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
              <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
              <path d="M12 8l0 4"></path>
              <path d="M12 16l.01 0"></path>
            </svg>`

          }
        }
      <?php endif; ?>
      ret += `<a href="/player/edit?id=${row.id}" class="link-primary">
        ${value} 
        </a>`;

    <?php else : ?>
      ret = value;
    <?php endif ?>
    return ret;
  }

  function timerFormatter(value, row, index) {
    ret = value;
    <?php if ($auth == 5 && !$pid) : ?>
      if (value) {
        ret = `<a href="/timersController/edit?id=${row.timer_config_id}" class="link-primary">
                   ${value}
                </a>`
      }
    <?php endif ?>
    return ret;
  }

  function descFormatter(value, row, index) {
    if (value) {
      var desc = value;
      if (value.length > 50) {
        desc = value.substring(0, 50) + "...";
      }
      var tooltip = escapeHtml(value)

      return `<span data-bs-toggle="tooltip" data-bs-html="true" data-placement="bottom" data-bs-container="body" title="${tooltip}" >${desc}</span>`;
    } else {
      return '';
    }
  }

  function operateFormatter(value, row, index) {
    var ret = ' <div class="btn-list flex-nowrap">';

    <?php if ($auth == 5 && !$pid) : ?>
      ret += `<a href="#" onclick="remove_resource('player', ${row.id})" title="<?php echo lang('delete') ?>" class="link-danger">
                <i class="bi bi-x-square"></i>
                </a>`;

    <?php endif; ?>
    <?php if ($auth != 10 && $auth >= 4) : ?>
      <?php if (!$this->config->item('with_template')) : ?>
        ret += `<a href=" #" data-bs-toggle="modal" data-bs-target="#export-report" data-bs-id="${row.id}">
          <i class="bi bi-file-earmark-spreadsheet"></i>
          </a>`;
      <?php endif; ?>

      ret += `<a href=" #" data-bs-toggle="modal" data-bs-target="#modal-calendar" data-bs-id="${row.id}" title="<?php echo lang('calendar') ?>">
              <i class="bi bi-calendar2-date"></i>
          </a>`;

    <?php endif; ?>
    if (row.screenshot) {
      ret += `<a href="#" data-bs-toggle="modal" data-bs-target="#screen_shot" title="View Screen Shot" data-bs-image="${row.screenshot}" data-bs-date="${row.screenshotDate}" data-bs-id="${row.id}" >
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-capture" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                      <path d="M4 8v-2a2 2 0 0 1 2 -2h2"></path>
                      <path d="M4 16v2a2 2 0 0 0 2 2h2"></path>
                      <path d="M16 4h2a2 2 0 0 1 2 2v2"></path>
                      <path d="M16 20h2a2 2 0 0 0 2 -2v-2"></path>
                      <circle cx="12" cy="12" r="3"></circle>
                    </svg>
              </a>`;
    }

    <?php if ($auth > 2 || ($this->config->item("new_campaign_user") && $auth == 1)) : ?>
      ret += `<a href="#" onclick="toggleExpand(${index})" class="link-primary">
          <i class="bi bi-chevron-bar-expand"></i>
              </a>`;
    <?php endif; ?>

    ret += `</div>`;
    return ret;
  }

  //if enter key is pressed on the page, refresh player table
  $(document).keypress(function(e) {
    if (e.which == 13) {
      doSearch();
    }
  });

  function sendCommand(command) {
    var prompt_str = "<?php echo lang('warn.area.you.sure'); ?>";
    if (command == 1) {
      prompt_str = "<?php echo lang('warn.player.restart'); ?>";
    } else if (command == 8) {
      prompt_str = "<?php echo lang('warn.player.format'); ?>";
    } else if (command == 3) {
      prompt_str = "<?php echo lang('warn.area.you.sure'); ?>";
    } else if (command == 0) {
      prompt_str = `
      <label class="form-label"><?php echo lang('warn.player.set.volume'); ?></label>
      <input type="range" class="form-range mb-2" id="volume_slider" min="0" max="100" value="0" oninput="this.nextElementSibling.value = this.value">
      <output>0</output>`;
    }

    $('#prompt').html(prompt_str);
    var selections = $('#table').bootstrapTable('getSelections');




    let ids = [];
    if (selections.length) {
      ids = selections.map((item) => {
        if (item.status > 1) {
          return item.id;
        }
      });
    }

    if (!ids.length || ids[0] === undefined) {
      return;
    }

    $('#command_confirm').modal("show").off('click')
      .on('click', '#confirm', function(e) {
        var val = 0;
        if (command == 0) {
          val = $('#volume_slider').val();
        }
        $.post('/player/android_control', {
          "ids": ids,
          "type": command,
          "value": val
        }, function(data) {
          $('#table').bootstrapTable('uncheckAll');
          //alert(data);
        });
      });
  }
  var screenshotModal = document.getElementById('screen_shot')
  screenshotModal.addEventListener('show.bs.modal', function(event) {
    // Button that triggered the modal
    var button = event.relatedTarget
    // Extract info from data-bs-* attributes
    var img_scr = button.getAttribute('data-bs-image');
    var img_date = button.getAttribute('data-bs-date');
    // If necessary, you could initiate an AJAX request here
    // and then do the updating in a callback.
    //
    // Update the modal's content.
    img_scr = img_scr + "?t=" + new Date().getTime();
    $('#screenshot_img').attr('src', img_scr);
    $('#screenshot_download').attr('href', img_scr);
    var captured_at = "<?php echo lang('captured.at'); ?>" + ": " + img_date;
    $('#captured_at').text(captured_at);
    var id = button.getAttribute('data-bs-id');
    $('#screenshot_row_id').val(id);
  });

  $('#screenshot_delete').on('click', function() {
    var row_id = $('#screenshot_row_id').val();
    var req = {
      id: row_id,
    };
    $.post("/player/do_delete_screenshot", req, function(data) {
      toastr.success(data.msg);
      var modal = bootstrap.Modal.getInstance(screenshotModal);
      modal.hide();
      doSearch();
    }, 'json');
  });

  var exportModal = document.getElementById('export-report')
  exportModal.addEventListener('show.bs.modal', function(event) {
    // Button that triggered the modal
    var button = event.relatedTarget
    // Extract info from data-bs-* attributes
    var id = button.getAttribute('data-bs-id');
    $('#export_row_id').val(id);

  });



  var calendarEl = document.getElementById('calendar');


  var calendar = new FullCalendar.Calendar(calendarEl, {
    // themeSystem: 'bootstrap5',
    timeZone: 'UTC',
    initialView: 'timeGridWeek',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'timeGridWeek,timeGridDay,dayGridMonth'
    },
    eventSources: {
      url: '/player/prepare_events',
      extraParams: function() { // a function that returns an object
        return {
          id: $('#cal_player_id').val()
        };
      },
    },
    timeFormat: 'H(:mm)' // uppercase H for 24-hour clock

  });
  calendar.render();
  $('#modal-calendar').on('show.bs.modal', function(event) {
    var button = event.relatedTarget
    // Extract info from data-bs-* attributes
    var id = button.getAttribute('data-bs-id');

    $('#cal_player_id').val(id);
    calendar.refetchEvents();
    setTimeout(function() {
      window.dispatchEvent(new Event('resize'));
      //calendar.updateSize();
    }, 100);
  })

  // @formatter:off
  document.addEventListener("DOMContentLoaded", function() {

    new Litepicker({
      element: document.getElementById('datepicker-inline'),
      buttonText: {
        previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>`,
        nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>`,
      },
      inlineMode: true,
      setup: (picker) => {

        picker.on('selected', (date1, date2) => {

          var row_id = $('#export_row_id').val();
          var day = date1.format('YYYY-MM-DD');
          toastr.success("<?php echo lang('start.download') ?>");
          window.location.href = `/player/get_reports?id=${row_id}&day=${day}`;
          var modal = bootstrap.Modal.getInstance(exportModal);
          modal.hide();
        });

      }
    });
  });

  $('#table').on('expand-row.bs.table', function(e, index, row, $detail) {
    $('#table').find('.detail-view').each(function() {
      if (!$(this).is($detail.parent())) {
        $(this).prev().find('.bi-chevron-bar-expand').click()
      }
    })
    $detail.html('loading...');
    $.get(
      "/player/detail?id=" + row.id + "&t=" + new Date().getTime(),
      function(data) {
        $detail.html(data);
      }
    );

  });
</script>