<head>
  <link rel="stylesheet" href="/static/css/jquery/jquery.dataTables.min.css" />
  <link rel="stylesheet" href="/static/css/jquery/chosen.min.css" />
  <link rel="stylesheet" href="/static/css/jquery/select.dataTables.min.css" />
  <script src='/static/js/jquery/chosen.jquery.min.js'></script>
  <script src='/static/js/jquery/jquery.dataTables.min.js'></script>
  <script src="/static/js/jquery/dataTables.select.min.js"></script>

</head>

<div id="playermap" style="width: 900px">
  <fieldset>
    <legend>Filters</legend>
    <table cellspacing="0" cellpadding="0" border="0" class="from-panel">
      <tbody>
        <tr>
          <td width="120">
            Search
          </td>
          <td>
            <input type="search" id="autocomplete" placeholder="" type="text" style="width:400px;" />
          </td>

        </tr>
        <tr>
          <td width="120">
            SDAW
          </td>
          <td>
            <textarea id="sdawid" rows="2" style="width: 400px;"></textarea>
          </td>

        </tr>
        <tr>
          <td width="120">
            <?php echo lang('criteria'); ?>
          </td>
          <td>
            <select id="filterCriteria" class="chosen-criteria" multiple onchange="search();" data-placeholder="">

              <?php if (isset($criteria)) : ?>
                <?php foreach ($criteria as $g) : ?>
                  <option value="<?php echo $g->id; ?>"><?php echo $g->name; ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </td>
        </tr>
        <tr>
          <td width="120">
            <?php echo lang('campaign.and'); ?>
          </td>
          <td>
            <select id="bindCriteria" class="chosen-criteria" multiple onchange="search();" data-placeholder="">
              <?php if (isset($criteria)) : ?>
                <?php foreach ($criteria as $g) : ?>
                  <option value="<?php echo $g->id; ?>"><?php echo $g->name; ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </td>
        </tr>
        <tr>
          <td width="120">
            <?php echo lang('campaign.and.or'); ?>
          </td>
          <td>
            <select id="bindCriteriaOr" class="chosen-criteria" multiple onchange="search();" data-placeholder="">
              <?php if (isset($criteria)) : ?>
                <?php foreach ($criteria as $g) : ?>
                  <option value="<?php echo $g->id; ?>"><?php echo $g->name; ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </td>
        </tr>
        <tr>
          <td width="120">
            <?php echo lang('exclude.criteria'); ?>
          </td>
          <td>
            <select id="exCriteria" class="chosen-criteria" multiple onchange="search();" data-placeholder="">

              <?php if (isset($criteria)) : ?>
                <?php foreach ($criteria as $g) : ?>
                  <option value="<?php echo $g->id; ?>"><?php echo $g->name; ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </td>
        </tr>
        <tr>
          <td width="120">
            <?php echo lang('filter.tag'); ?>
          </td>
          <td>
            <select id="filterTag" class="chosen-criteria" multiple onchange="search();" data-placeholder="">
              <?php if (isset($tags)) : ?>
                <?php foreach ($tags as $t) : ?>
                  <option value="<?php echo $t->id; ?>"><?php echo $t->name; ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </td>
        </tr>
        <tr>
          <td width="120">
            <?php echo lang('filter.pps'); ?>
          </td>
          <td>
            <input id="minipps" placeholder="" type="number" style="width:400px;" />
          </td>

        </tr>


      </tbody>
    </table>

  </fieldset>

  <div>
    <table id="sel_players" class="display">
      <thead>
        <tr>
          <th></th>
          <th><?php echo lang('player'); ?></th>
          <th>SDAW ID</th>
          <th><?php echo lang('player_contown'); ?></th>
          <th><?php echo lang('player_connzipcode'); ?></th>
          <th><?php echo lang('player_conaddr'); ?></th>
          <th><?php echo lang('view_direction'); ?></th>
          <th><?php echo lang('pps'); ?></th>

        </tr>
      </thead>
    </table>
    <p class="btn-center">
      <a class="btn-01" href="javascript:void(0);" onclick="submit(1)"><span>Append</span></a>
      <a class="btn-01" href="javascript:void(0);" onclick="submit(0)"><span>Replace</span></a>
      <a class="btn-01" href="javascript:void(0);" onclick="$.fancybox.close();"><span><?php echo lang('button.cancel'); ?></span></a>
    </p>
  </div>





  <script>
    $('.chosen-criteria').chosen({
      width: "400px"
    });

    $('#autocomplete').on('input', function() {
      search();
    });
    $('#sdawid').on('input', function() {
      search();
    });
    $('#minipps').on('input', function() {
      search();
    });
    var dataTable = $('#sel_players').DataTable({
      scrollY: "500",
      //scrollCollapse: true,
      paging: false,
      info: true,
      "searching": false,
      columnDefs: [{
        orderable: false,
        className: 'select-checkbox',
        targets: 0,
      }, ],
      select: {
        style: 'multi',
        selector: 'td:first-child',
        blurable: false
      },
      order: [
        [2, 'asc']
      ],
      "processing": true,

      "deferLoading": 0,
      "ajax": {
        "url": "/player/players_in_place",
        "type": "POST",
        rowId: 'id',
        "data": function(para) {
          para.addr = $("#autocomplete").val().trim(),
            para.criteria = $('#filterCriteria').val(),
            para.bind_criteria = $('#bindCriteria').val(),
            para.bind_criteria_or = $('#bindCriteriaOr').val(),
            para.ex_criteria = $('#exCriteria').val(),
            para.tags = $('#filterTag').val(),
            para.sdaw = $('#sdawid').val(),
            para.pps = $('#minipps').val()
        },

      },
      columns: [

        {
          "mData": "id",
          "mRender": function(data) {
            return "";
          }
        },
        {
          data: "name"
        },
        {
          data: "custom_sn1"
        },
        {
          data: "contown"
        },
        {
          data: "conzipcode"
        },
        {
          data: "conaddr"
        },
        {
          data: "viewdirection"
        },
        {
          data: "pps"
        }
      ],


      "language": {
        "info": "Showing _MAX_ devices",
        "infoEmpty": "No records available",
        "infoFiltered": "(filtered from _MAX_ total records)",
        select: {
          rows: {
            _: "Selected %d devices",
            1: "Selected 1 device"
          }
        }
      },
      "initComplete": function(settings, json) {
        //dataTable.rows().select();
      }
    });


    function search() {
      dataTable.ajax.reload(function(json) {
        dataTable.rows().select();
      });;
    }



    function submit(append) {
      target = '<?php echo $target; ?>' || 'players-select-options';

      var players = dataTable.rows({
        selected: true
      }).data().pluck('id').toArray();

      $.fancybox.close();
      if (append) {
        players = $.merge($("#" + target).val(), players);
      }
      $("#" + target).val(players).trigger("chosen:updated");

    }
  </script>