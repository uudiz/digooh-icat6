<style>
  #map {
    height: 300px;
  }

  .subheader {
    font-size: .625rem;
    font-weight: var(--tblr-font-weight-bold);
    text-transform: none;
    letter-spacing: .04em;
    line-height: 1rem;
    color: var(--tblr-muted);
  }
</style>


<div class="modal fade" id="playerModal">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?php echo lang('select.devices') ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container-xl">
          <div class="row g-4">
            <div class="col-4">
              <form autocomplete="off" novalidate>
                <div class="mb-2">
                  <div class="input-icon">
                    <input type="text" id="search" class="form-control " placeholder="">
                    <span class="input-icon-addon">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <circle cx="10" cy="10" r="7"></circle>
                        <line x1="21" y1="21" x2="15" y2="15"></line>
                      </svg>
                    </span>
                  </div>
                </div>
                <div class="subheader"><?php echo lang('criteria'); ?></div>
                <div class="mb-2">
                  <select data-placeholder="" id="filterCriteria" class="form-select form-control-sm playerMapSelect2" multiple>
                    <?php if (isset($criteria)) : ?>
                      <?php foreach ($criteria as $g) : ?>
                        <option value="<?php echo $g->id; ?>"><?php echo $g->name; ?></option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>
                <div class="subheader"><?php echo lang('criteria.and'); ?></div>
                <div class="mb-2">
                  <select data-placeholder="" id="bindCriteria" class="form-select  playerMapSelect2" multiple>
                    <?php if (isset($criteria)) : ?>
                      <?php foreach ($criteria as $g) : ?>
                        <option value="<?php echo $g->id; ?>"><?php echo $g->name; ?></option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>
                <div class="subheader"><?php echo lang('criteria.or'); ?></div>
                <div class="mb-2">
                  <select data-placeholder="" id="bindCriteriaOr" class="form-control playerMapSelect2" multiple>
                    <?php if (isset($criteria)) : ?>
                      <?php foreach ($criteria as $g) : ?>
                        <option value="<?php echo $g->id; ?>"><?php echo $g->name; ?></option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>
                <div class="subheader"><?php echo lang('criteria.exclude'); ?></div>
                <div class="mb-2">
                  <select data-placeholder="" id="exCriteria" class="form-control form-control-sm playerMapSelect2" multiple>
                    <?php if (isset($criteria)) : ?>
                      <?php foreach ($criteria as $g) : ?>
                        <option value="<?php echo $g->id; ?>"><?php echo $g->name; ?></option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>

                <div class="subheader"><?php echo $this->config->item("with_template") ? lang('categories') : lang('tag'); ?></div>
                <div class="mb-2">
                  <select data-placeholder="" id="filterTag" class="form-control form-control-sm playerMapSelect2" multiple>
                    <?php if (isset($tags)) : ?>
                      <?php foreach ($tags as $t) : ?>
                        <option value="<?php echo $t->id; ?>"><?php echo $t->name; ?></option>
                      <?php endforeach; ?>
                    <?php endif; ?>

                  </select>
                </div>


                <div class="subheader"><?php echo $this->config->item("with_template") ? lang('store.display_id') : lang('custom_sn1'); ?></div>
                <div class="mb-2">
                  <textarea id="sdawid" rows="2" class="form-control form-control-sm"></textarea>
                </div>

                <div class="subheader">pps</div>
                <div class="mb-2">
                  <div class="input-group">
                    <div class="input-group-text">pps > </div>
                    <input type="number" name="minipps" min=0 class="form-control" id="minipps">
                  </div>
                </div>

                <?php if ($this->config->item('with_radius_search')) : ?>

                  <div class="subheader"><?php echo lang('address') ?></div>
                  <div class="mb-2">
                    <select id="location-select" aria-placeholder="Address/City/Postcode"></select>
                    <input type="hidden" id="lat">
                    <input type="hidden" id="lng">
                  </div>
                  <div class="subheader ">Radius</div>
                  <div class="mb-2">
                    <div class="input-group">
                      <input type="number" min=2 max=100 class="form-control" id="radius" value=20>
                      <div class="input-group-text">KM</div>
                    </div>
                  </div>
                <?php endif ?>
                <div class="mb-2">

                  <div class="form-check form-switch align-bottom">
                    <label>&nbsp;</label>
                    <input class="form-check-input disable-for-normal-user readonly-for-extension" type="checkbox" id="show_all">
                    <label><?php echo lang('show.planned.displays'); ?></label>
                  </div>
                </div>

                <div class="mt-2">
                  <p id="selected_num"></p>
                </div>

              </form>
            </div>
            <div class="col-8">
              <?php if ($this->config->item('with_radius_search')) : ?>
                <div class="mb-2">
                  <div id="map" class="mb-2"></div>
                </div>
              <?php endif ?>
              <table class="table table-sm table-striped table-bordered" id="choose_players_table" data-toggle="table" data-pagination="false" data-height="500" data-url="/player/players_in_place" data-query-params="queryPlaceParams">
                <thead>
                  <tr>
                    <th data-checkbox="true"></th>
                    <th data-field="name" data-sortable="true"><?php echo lang('player'); ?></th>
                    <th data-field="custom_sn1"><?php echo $this->config->item("with_template") ? lang('store.display_id') : lang('custom_sn1'); ?></th>
                    <th data-field="contown"><?php echo lang('player_contown'); ?></th>
                    <th data-field="conzipcode"><?php echo lang('player_connzipcode'); ?></th>
                    <th data-field="conaddr"><?php echo lang('player_conaddr'); ?></th>
                    <th data-field="viewdirection"><?php echo lang('view_direction'); ?></th>
                    <th data-field="pps"><?php echo lang('pps'); ?></th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <div class="col-auto">
          <input type="hidden" id="main_campaign_id" value="0">
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="submit(1)"><?php echo lang('append') ?></button>
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="submit(0)"><?php echo lang('replace') ?></button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo lang('button.cancel') ?></button>
        </div>
      </div>
    </div>
  </div>
</div>


<link rel="stylesheet" href="/assets/css/leaflet.css" integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin="" />
<script src="/assets/js/leaflet.js" crossorigin=""></script>

<script>
  <?php if ($this->config->item('with_radius_search')) : ?>
    var germany_point = [
      51.1638175, 10.4478313
    ];
    var map = L.map('map').setView(germany_point, 5);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 20,
      attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    var markersLayer = new L.LayerGroup();
    var clickCircle;
    markersLayer.addTo(map);
  <?php endif ?>

  function drawCircle() {
    if (clickCircle != undefined) {
      map.removeLayer(clickCircle);
    };
    var lat = $("#lat").val();
    var lng = $("#lng").val();
    var radius = $("#radius").val();
    if (radius != "" && lat !== "" && lng !== "") {
      clickCircle = L.circle([lat, lng], radius * 1000, {
        color: '#f07300',
        fillOpacity: 0,
        opacity: 0.5
      }).addTo(map);
    }
  }



  var bt_table = $('#choose_players_table');
  var target = 'players-select-options';

  bt_table.bootstrapTable({
    onLoadSuccess: function(data) {
      bt_table.bootstrapTable('checkAll');
      if (typeof markersLayer !== 'undefined') {
        markersLayer.clearLayers();
        var cities = new Array()
        var markersArray = new Array();
        data.rows.forEach(function(value) {
          var template = value.name;
          var markersArray = new Array();
          var marker = L.marker([value.geox, value.geoy]).bindPopup(template);
          markersLayer.addLayer(marker);
        });
      }
    },
    onAll: function(name, args) {
      if (name == 'check.bs.table' || name == 'check-all.bs.table' || name == 'uncheck.bs.table' || name == 'uncheck-all.bs.table') {
        var selected_num = bt_table.bootstrapTable('getSelections').length;
        if (selected_num == 0) {
          $('#selected_num').html('');
        } else {
          var str = selected_num + ' DCLP';
          $('#selected_num').html(str);
        }
      }
    },

  });

  function queryPlaceParams(params) {
    params.criteria = $('#filterCriteria').val();
    params.bind_criteria = $('#bindCriteria').val();
    params.bind_criteria_or = $('#bindCriteriaOr').val();
    params.ex_criteria = $('#exCriteria').val();
    params.tags = $('#filterTag').val();
    params.sdaw = $('#sdawid').val();
    params.pps = $('#minipps').val();
    params.search = $('#search').val();
    if ($('#main_campaign_id').val() != 0) {
      params.main_campaign_id = $('#main_campaign_id').val();
    }
    var lat = $("#lat").val();
    var lng = $("#lng").val();
    if (lat !== "" && lng !== "") {
      params.lat = lat;
      params.lng = lng;
      params.radius = $('#radius').val() ? $('#radius').val() : 20;
    }
    params.show_all = $('#show_all').prop('checked') ? 1 : 0;
    const company_id = $("#cid").val();
    if (company_id) {
      params.company_id = company_id;
    }


    return params;
  }


  function submit(append) {
    var rows = bt_table.bootstrapTable('getSelections');

    let players = rows.map((item) => {
      return item.id
    });

    if (append) {
      players = $.merge($("#" + target).val(), players);
    }

    $("#" + target).val(players).trigger('change');
  }


  $(document).ready(function() {
    $(".playerMapSelect2").on('change', function() {
      bt_table.bootstrapTable('refresh');
    });
    $('#sdawid').on('input', function() {
      bt_table.bootstrapTable('refresh');
    });
    $('#sdawid').on('input', function() {
      bt_table.bootstrapTable('refresh');
    });
    $('#minipps').on('input', function() {
      bt_table.bootstrapTable('refresh');
    });
    $('#search').on('input', function() {
      bt_table.bootstrapTable('refresh');
    });
    $('#show_all').on('change', function() {
      bt_table.bootstrapTable('refresh');
    });
    $("#radius").on('change', function() {
      var lat = $("#lat").val();
      var lng = $("#lng").val();
      var radius = $("#radius").val();

      if (lat != "" && lng != "" && radius != "") {
        drawCircle();
        bt_table.bootstrapTable('refresh');
      }

    });


    $('#playerModal').on('show.bs.modal', function(e) {
      if (typeof map !== 'undefined') {
        setTimeout(function() {
          map.invalidateSize();
        }, 200);
      }

      var button = e.relatedTarget;
      // Extract info from data-bs-* attributes
      var dest_field = button.getAttribute('data-target-field')
      if (dest_field) {
        target = dest_field;
      } else {
        target = 'players-select-options';
      }
      // var main_campaign_id = button.getAttribute('data-main-campaign-id');


      $('#main_campaign_id').val(0);
      var priorityElement = $('#priority');
      if (priorityElement.length && priorityElement.val() == 8) {
        var mainCampaignElement = $('#main_campaign');
        if (mainCampaignElement.length) {
          $('#main_campaign_id').val(mainCampaignElement.val());
          $("#choose_players_table").bootstrapTable("resetSearch");
        }
      }


      $(this).find('.playerMapSelect2').select2({
        theme: "bootstrap-5",
        dropdownParent: $(this).find('.modal-content'),
        width: '100%',
      });

    });

    $('#playerModal').on('hide.bs.modal', function(e) {
      $('#filterCriteria').val(null);
      $('#bindCriteria').val(null);
      $('#bindCriteriaOr').val(null);
      $('#exCriteria').val(null);
      $('#sdawid').val(null);
      $('#minipps').val(null);
      $('#search').val(null);
      $('#lat').val("");
      $('#lng').val("")
      $('#radius').val(20);
      $('#main_campaign_id').val(0);
      bt_table.bootstrapTable('refresh');
    })


    // 将 <select> 元素转换为一个自动完成的输入框，并设置一些选项
    $('#location-select').select2({
      dropdownParent: $("#playerModal"),
      theme: "bootstrap-5",
      width: "100%",
      placeholder: "Address/City/Postcode",
      minimumInputLength: 4,
      maximumSelectionSize: 1,
      multiple: true,
      minimumResultsForSearch: Infinity,
      delay: 500,
      ajax: {
        url: 'https://nominatim.openstreetmap.org/search',
        type: 'GET',
        data: function(params) {
          return {
            q: params.term,
            format: 'jsonv2',
            limit: 10,
            addressdetails: 1,
            countrycodes: 'de',
            "accept-language": 'de'

          };
        },
        // 处理返回的结果，将其转换为符合 Select2 格式的数组对象
        processResults: function(data) {
          return {
            results: data.map(function(item) {
              return {
                id: item.place_id, // 结果唯一标识符 
                text: item.display_name, // 结果显示名称 
                lat: item.lat, // 结果纬度 
                lon: item.lon, // 结果经度 
                type: item.type,
                boundingbox: item.boundingbox,

              };
            }),
          };
        },
      },
      // 显示每个搜索结果的内容
      templateResult: function(item) {
        return item.text;
      },
      // 显示被选中结果的内容
      templateSelection: function(item) {
        return item.text;
      },
      // 当用户选择了一个结果时，获取该结果的经纬度，并执行一些操作
    }).on('change', function(e) {

      var data = $(this).select2('data')[0];
      if (data) {

        var lat = data.lat;
        var lon = data.lon;
        if (data['type'] == "administrative") {
          var corner1 = L.latLng(data['boundingbox'][0], data['boundingbox'][2]),
            corner2 = L.latLng(data['boundingbox'][1], data['boundingbox'][3]),
            bounds = L.latLngBounds(corner1, corner2);
          map.fitBounds(bounds);
        } else {
          map.flyTo([lat, lon], 10);
        }
        $('#lat').val(lat);
        $('#lng').val(lon);


      } else {
        $('#lat').val("");
        $('#lng').val("");
        map.flyTo(germany_point, 5)
      }
      drawCircle();
      $("#choose_players_table").bootstrapTable("refresh");
    });


  });
</script>