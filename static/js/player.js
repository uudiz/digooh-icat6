/**
 * @author User
 */
var player = {
  initFilter: function () {
    document.onkeyup = function (event) {
      if (event.keyCode == 13) {
        player.filter();
      }
    };
  },
  filter: function () {
    player.page(1, "id", "desc");
  },
  page: function (curpage, orderItem, order) {
    showLoading();
    var filter = $("#filter").val();
    var online = $('input:checkbox[name="online"]:checked').val();

    var filterTag = $("#filterTag").val();
    var filterCriteria = $("#filterCriteria").val();
    if (online == undefined) {
      online = 0;
    }
    var req = "";
    if (filter.length > 0) {
      /*
			req += '&filter_type=' + filterType + '&filter=' + filter;
			*/
      req += "&filter=" + filter;
    }

    if (parseInt(online) == 1) {
      req += "&online=" + online;
    }

    if (parseInt(filterCriteria) > 0) {
      req += "&criterion_id=" + filterCriteria;
    }
    if (parseInt(filterTag) > 0) {
      req += "&tag_id=" + filterTag;
    }

    var url = "/player/refresh/";

    if ($("#is_usage_page").val() == 1) {
      url = "/player/refresh_usage/";
    }

    $.get(
      url +
        curpage +
        "/" +
        orderItem +
        "/" +
        order +
        "?t=" +
        new Date().getTime() +
        req,
      function (data) {
        $("#playerContent").html(data);
        //reinit this box~
        tb_init("td > a.thickbox"); //pass where to apply thickbox
        hideLoading();
        player.initFilter();
      }
    );
  },
  refresh: function () {
    showLoading();
    $.get("/player/refresh?t=" + new Date().getTime(), function (data) {
      //reinit this box~
      $("#playerContent").html(data);
      tb_init("td > a.thickbox"); //pass where to apply thickbox
      hideLoading();
      player.initFilter();
    });
  },
  cplayerFilter: function () {
    player.companyPlayerPage(0, 1, "id", "desc");
  },
  companyPlayerPage: function (cid, curpage, orderItem, order) {
    showLoading();
    //	var filterType = $('#filterType').val();
    var filter = $("#filter").val();
    var online = $('input:checkbox[name="online"]:checked').val();
    if (online == undefined) {
      online = 0;
    }
    var req = "";
    if (filter.length > 0) {
      /*
			req += '&filter_type=' + filterType + '&filter=' + filter;
			*/
      req += "&filter=" + filter;
    }

    if (parseInt(online) == 1) {
      req += "&online=" + online;
    }

    $.get(
      "/player/company_refresh_player/" +
        cid +
        "/" +
        curpage +
        "/" +
        orderItem +
        "/" +
        order +
        "?t=" +
        new Date().getTime() +
        req,
      function (data) {
        $("#playerContent").html(data);
        //reinit this box~
        tb_init("td > a.thickbox"); //pass where to apply thickbox
        hideLoading();
        //player.initFilter();
      }
    );
  },
  companyPlayerRefresh: function () {
    showLoading();
    $.get(
      "/player/company_refresh_player?t=" + new Date().getTime(),
      function (data) {
        //reinit this box~
        $("#playerContent").html(data);
        tb_init("td > a.thickbox"); //pass where to apply thickbox
        hideLoading();
        player.initFilter();
      }
    );
  },
  toggle: function (obj) {
    var $this = $(obj);
    var status = $this.attr("status");
    var id = $this.attr("id");
    var pp = $this.parent().parent();
    var img = $this.children("img");

    if (status == 0) {
      //set working status
      $this.attr("status", 2);
      //set loading status
      var loading =
        '<tr dl="detailLoading" height="80"><td><div style="left: 50%;" class="loading-01">Loading ......</div></td></tr>';
      pp.after(loading);
      //expland
      $.get(
        "/player/detail?id=" + id + "&t=" + new Date().getTime(),
        function (data) {
          //append detail
          pp.next().remove();
          var css = pp.attr("class");
          var line =
            '<tr dl="' +
            id +
            '" ' +
            (css != undefined && css != "" ? 'class="' + css + '"' : "") +
            '><td colspan="12" class="control-panel">' +
            data +
            "</td></tr>";
          pp.after(line).fadeIn();

          $this.attr("status", 1);
          img.attr("src", "/images/icons/24-up.png");
        }
      );
    } else if (status == 1) {
      //collapse
      var next = pp.next();
      if (id == next.attr("dl")) {
        //next.fadeOut();
        next.remove();
      }

      $this.attr("status", 0);
      img.attr("src", "/images/icons/24-down.png");
    }
  },
  operate: function (pid, actionCode) {
    switch (actionCode) {
      case "shutdown":
      case "reboot":
      case "play":
      case "stop":
      case "vp":
      case "vd":
      case "ss":
        break;
      case "refresh":
        break;
    }
  },
  doSave: function (player_type, status) {
    var page_id = $.trim($("li.active").text());
    var id = $("#id").val();
    var name = $("#name").val();
    var filter_type = $("#filter_type").val();
    var filter_name = $("#filter_name").val();

    if (id == undefined) {
      id = 0;
    }
    if (
      name.indexOf("&") >= 0 ||
      name.indexOf("<") >= 0 ||
      name.indexOf(">") >= 0 ||
      name.indexOf("'") >= 0 ||
      name.indexOf("\\") >= 0 ||
      name.indexOf("%") >= 0
    ) {
      showFormMsg(
        "Special symbols (& < > ' \\ %) are not allowed in the player name.",
        "error"
      );
      return false;
    }
    var req = "";
    if (player_type == undefined) {
      player_type = 0;
    } else {
      req = "&filter_type=" + filter_type + "&filter=" + filter_name;
    }
    if (status == undefined) {
      status = 0;
    }
    var mon = $("#mon").val();
    var tue = $("#tue").val();
    var wed = $("#wed").val();
    var thu = $("#thu").val();
    var fri = $("#fri").val();
    var sat = $("#sat").val();
    var sun = $("#sun").val();
    if (mon) {
      var a = mon.split(",");
    }

    $.post(
      "/player/do_save",
      {
        id: id,
        gid: 0,
        name: name,
        city_code: $("#cityCode").val(),
        tags_select: String($("#jquery-tagbox-select-options").val()),
        criteria_select: String($("#jquery-cribox-select-options").val()),
        screensel: $("#screen").val(),
        descr: $("#descr").val(),
        timer_config_id: $("#timerConfigId").val(),
        barcode: $("#barcode").val(),
        simno: $("#simno").val(),
        conname: $("#conname").val(),
        conphone: $("#phoneno").val(),
        conemail: $("#conemail").val(),
        conaddr: $("#conaddr").val(),
        zipcode: $("#zipcode").val(),
        street_num: $('#street_num').val(),
        contown: $("#contown").val(),
        volume: $("#volume").val(),
        simvolume: $("#simvolume").val(),
        itemnum: $("#itemnum").val(),
        modelname: $("#modelname").val(),
        screensize: $("#screensize").val(),
        sided: $("#sided").val(),
        partnerid: $("#partnerid").val(),
        locationid: $("#locationid").val(),
        geox: $("#geox").val(),
        geoy: $("#geoy").val(),
        setupdate: $("#setupdate").val(),
        viewdirection: $("#viewdirection").val(),
        pps: $("#pps").val(),
        visitors: $("#visitors").val(),
        displaynum: $("#displaynum").val(),
        state: $("#state").val(),
        country: $("#country").val(),
        customsn1: $("#customsn1").val(),
        customsn2: $("#customsn2").val(),
        details: $("#detail").val(),
        ssptags_select: $("#jquery-ssptagbox-select-options").val(),
        dmi_select: $("#dmi-select-options").val(),
        dpaa_select: $("#dpaa-select-options").val(),
        ilb_select: $("#ilb-select-options").val(),
        openoohs_select: $("#openoohs-select-options").val(),
        mon: $("#mon").val(),
        tue: $("#tue").val(),
        wed: $("#wed").val(),
        thu: $("#thu").val(),
        fri: $("#fri").val(),
        sat: $("#sat").val(),
        sun: $("#sun").val(),
        pos_tags: $("#pos_tags").val(),
        ssp_exclude: $("#ssp_exclude").val(),
        ssp_additional: $("#ssp_additional").val(),
        ssp_dsp_alias: $("#ssp_dsp_alias").val(),
        ssp_dsp_ref: $("#ssp_dsp_ref").val(),
        street_num: $("#street_num").val(),
      },
      function (data) {
        if (data.code != 0) {
          showFormMsg(data.msg, "error");
        } else {
          showFormMsg(data.msg, "success");

          setTimeout(function () {
            //tb_remove();
            player.closeModel();
            if (data.needPublish == 1) {
              alertify.alert(data.repubmsg);
            }
            if(filter_name!==undefined){
              var req = "&filter_type=" + filter_type + "&filter=" + filter_name;
            }else{
              req = '';
            }
            $.get(
              "/player/refresh/" + page_id + "?t=" + new Date().getTime() + req,
              function (data) {
                $("#playerContent").html(data);
                tb_init("td > a.thickbox");
                hideLoading();
                player.initFilter();
              }
            );
          }, 100);
        }
      },
      "json"
    );
  },
  remove: function (id, msg) {
    if (confirm(msg)) {
      var req = {
        id: id,
      };
      $.post(
        "/player/do_delete",
        req,
        function (data) {
          if (data.code == 0) {
            showMsg(data.msg, "success");
            if (data.needPublish == 1) {
              alertify.alert(data.repubmsg);
            }
            player.refresh();
            setTimeout(hideMsg, 1000);
          } else {
            showMsg(data.msg, "error");
          }
        },
        "json"
      );
    }
  },
  remove_screenshot: function (id, msg) {
    var page_id = $.trim($("li.active").text());
    if (confirm(msg)) {
      var req = {
        id: id,
      };
      $.post("/player/do_delete_screenshot", req, function (data) {
        $("#TB_closeWindowButton").click();
        player.refresh();
      });
    }
  },

  form: {
    gid: 0,
    formId: "player-form",
    doSave: function () {
      $.post(
        "/player/do_save",
        {
          gid: player.form.gid,
          name: $("#name").val(),
          descr: $("#descr").val(),
        },
        function (data) {
          json = toJsonObj(data);
          if (json != null) {
            if (json.code != 0) {
              $(".validateTips").html(json.msg);
            } else {
              player.form.destoryFormDialog();
              var tabs = $("#tabs");
              var idx = tabs.tabs("option", "selected");
              tabs.tabs("load", idx);
            }
          } else {
            alert("System error....");
          }
        }
      );
    },
    init: function () {
      //this.form = $('#user-form');
    },
    destory: function () {
      uf = $("#" + player.form.formId);
      uf.parent().remove();
      uf.remove();
    },
    destoryFormDialog: function () {
      $("#" + player.form.formId).dialog("destory");
      this.destory();
    },
  },
  //更新 player截图 预览图片
  updateImg: function () {
    $("a#1532462736").html(
      "<img src='/resources/preview/196/1532462736.png?t=" +
        new Date().getTime() +
        "' width='32' height='24' />"
    );
    $("a#1532462736").attr(
      "href",
      "/resources/preview/196/1532462736.png?t=" + new Date().getTime()
    );
  },
  changeScreenShot: function (emptyMsg, cfmMsg, id, sn, cid) {
    var ids = new Array();
    ids.push(id);

    if (ids.length == 0) {
      alert(emptyMsg);
      return;
    } else {
      var value = 0;
      $("#rotateConfirm").dialog({
        resizable: false,
        height: 156,
        modal: true,
        buttons: {
          Yes: function () {
            $(this).dialog("close");
            $.post(
              "/player/android_control",
              { ids: ids, type: 4, value: value },
              function (data) {
                if (data == 1) {
                  setTimeout(function () {
                    $("a#" + sn).html(
                      "<img src='/resources/preview/" +
                        cid +
                        "/" +
                        sn +
                        ".png?t=" +
                        new Date().getTime() +
                        "' width='32' height='24' />"
                    );
                    $("a#" + sn).attr(
                      "href",
                      "/resources/preview/" +
                        cid +
                        "/" +
                        sn +
                        ".png?t=" +
                        new Date().getTime()
                    );
                  }, 5000);
                }
              }
            );
          },
          No: function () {
            $(this).dialog("close");
          },
        },
      });
    }
  },
  list: {
    gid: 0,
    totalLine: 0, //客户机记录数
    init: function () {
      if (this.totalLine > 0) {
        this.initSorttable();
      }
      $("#create-" + this.gid)
        .button()
        .click(function () {
          var gid = this.id.split("-")[1];
          if (gid == 0) {
            gid = $("#gid").val();
          }
          player.form.gid = gid;
          $.get("/player/add?gid=" + gid, function (data) {
            $("#tabs").after(data);
            player.form.formId = "player-form-" + gid;
            $("#" + player.form.formId).dialog({
              autoOpen: true,
              modal: true,
              width: 500,
              buttons: [
                {
                  text: "Ok",
                  click: function () {
                    player.form.doSave();
                    return false;
                  },
                },
                {
                  text: "Cancel",
                  click: function () {
                    player.form.destoryFormDialog();
                    return false;
                  },
                },
              ],
              close: function (event, ui) {
                event.preventDefault();
                player.form.destory();
              },
            });
          });
        });
    },
    initSorttable: function () {
      $("#sorttable-" + this.gid)
        .tablesorter({
          debug: false,
          sortList: [[0, 1]],
          widgets: ["zebra"],
          headers: {
            //0: {sorter: false}
          },
        })
        .tablesorterPager({
          container: $("#sorttable-" + player.list.gid),
          positionFixed: false,
        });
    },
    addItem: function (item) {
      if (this.totalLine == 0) {
        $("#sorttable-" + this.gid + " tbody >  tr:first").before(
          "<tr>" +
            "<td>" +
            item.id +
            "</td>" +
            "<td>" +
            item.name +
            "</td>" +
            "<td>" +
            item.group_name +
            "</td>" +
            "<td>" +
            item.descr +
            "</td>" +
            "<td>" +
            item.status +
            "</td>" +
            "<td>" +
            item.add_time +
            "</td>" +
            "</tr>"
        );

        this.initSorttable();
        this.totalLine++;
      } else {
        $("#sorttable-" + this.gid).trigger(
          "addItem",
          "<tr>" +
            "<td>" +
            item.id +
            "</td>" +
            "<td>" +
            item.name +
            "</td>" +
            "<td>" +
            item.group_name +
            "</td>" +
            "<td>" +
            item.descr +
            "</td>" +
            "<td>" +
            item.status +
            "</td>" +
            "<td>" +
            item.add_time +
            "</td>" +
            "</tr>"
        );
      }
    },
  },
  goback: function () {
    window.location = "/player/anew_player";
  },
  newplayerFilter: function () {
    player.newPlayerPage(0, 1, "id", "desc");
  },
  newPlayerPage: function (cid, curpage, orderItem, order) {
    showLoading();
    //var filterType = $('#filterType').val();
    var filter = $("#filter").val();
    var online = $('input:checkbox[name="online"]:checked').val();
    var filterGroup = $("#filterGroup").val();
    if (online == undefined) {
      online = 0;
    }
    var req = "";
    if (filter.length > 0) {
      /*
			req += '&filter_type=' + filterType + '&filter=' + filter;
			*/
      req += "&filter=" + filter;
    }

    if (parseInt(online) == 1) {
      req += "&online=" + online;
    }
    if (parseInt(filterGroup) > 0) {
      req += "&gid=" + filterGroup;
    }

    $.get(
      "/player/anew_refresh_player/" +
        cid +
        "/" +
        curpage +
        "/" +
        orderItem +
        "/" +
        order +
        "?t=" +
        new Date().getTime() +
        req,
      function (data) {
        $("#playerContent").html(data);
        //reinit this box~
        tb_init("td > a.thickbox"); //pass where to apply thickbox
        hideLoading();
        //player.initFilter();
      }
    );
  },
  newPlayerRefresh: function () {
    showLoading();
    $.get(
      "/player/anew_refresh_player?t=" + new Date().getTime(),
      function (data) {
        //reinit this box~
        $("#playerContent").html(data);
        tb_init("td > a.thickbox"); //pass where to apply thickbox
        hideLoading();
        player.initFilter();
      }
    );
  },
  addPlayerRow: function () {
    var cid = $("#companyId").val();
    var mac = $("#mac").val();
    var code = $("#cityCode").val();
    var descr = $("#descr").val();
    var table = $("#newPlayer");
    var reg_name = /[a-f|A-F|\d]{6}/;
    //var reg_name = /[a-f|A-F|\d]{2}-[a-f|A-F|\d]{2}-[a-f|A-F|\d]{2}-[a-f|A-F|\d]{2}-[a-f|A-F|\d]{2}-[a-f|A-F|\d]{2}/;
    if (mac.length == 6 && reg_name.test(mac)) {
      if (table[0].rows.length > 10) {
        return;
      }

      $.get(
        "/player/add_player_row?cid=" +
          cid +
          "&mac=" +
          mac +
          "&code=" +
          code +
          "&descr=" +
          descr +
          "&t=" +
          new Date().getTime(),
        function (data) {
          table.append(data);
          $("#mac").attr("value", "");
          $("#cityCode").attr("value", "");
          $("#descr").attr("value", "");
        }
      );
    } else {
      if (mac.length == 0) {
        alert("The mac field is required.");
      } else {
        alert("mac invalid");
      }
    }
  },
  newRemove: function (id, msg) {
    if (confirm(msg)) {
      var req = {
        id: id,
      };
      $.post(
        "/player/do_delete",
        req,
        function (data) {
          if (data.code == 0) {
            showMsg(data.msg, "success");
            //player.newPlayerRefresh();
            window.location = "/player/anew_player";
            setTimeout(hideMsg, 1000);
          } else {
            showMsg(data.msg, "error");
          }
        },
        "json"
      );
    }
  },
  removePlayerRow: function (obj) {
    var $this = $(obj);
    $this.parent().parent().remove();
  },
  doSaveRow: function () {
    var sn = document.getElementsByName("sn");
    var rowMac = document.getElementsByName("rowMac");
    var cid = document.getElementsByName("cid");
    var code = document.getElementsByName("code");
    var desc = document.getElementsByName("desc");
    var snArr = new Array();
    var macArr = new Array();
    var cidArr = new Array();
    var codeArr = new Array();
    var descrArr = new Array();

    for (var i = 0; i < sn.length; i++) {
      snArr.push(sn[i].value);
      macArr.push(rowMac[i].value);
      cidArr.push(cid[i].value);
      codeArr.push(code[i].value);
      descrArr.push(desc[i].value);
    }
    $.post(
      "/player/do_save_player",
      {
        sns: snArr,
        macs: macArr,
        cids: cidArr,
        codes: codeArr,
        descrs: descrArr,
      },
      function (data) {
        //alert(data);
        player.goback();
      }
    );
    /*
		alert('sn: '+sn[0].value+', mac: '+rowMac[0].value+', cid: '+cid[0].value);
		alert('sn: '+sn[1].value+', mac: '+rowMac[1].value+', cid: '+cid[1].value);
		alert('sn: '+sn[2].value+', mac: '+rowMac[2].value+', cid: '+cid[2].value);
		*/
  },
  closeModel: function () {
    tb_remove();
    $("#google-map").remove();
    window.google = {};
  },

  exportplayers: function () {
    var filter = $("#filter").val();
    var online = $('input:checkbox[name="online"]:checked').val();

    var filterTag = $("#filterTag").val();
    var filterCriteria = $("#filterCriteria").val();
    if (online == undefined) {
      online = 0;
    }
    var req = "";
    if (filter.length > 0) {
      /*
			req += '&filter_type=' + filterType + '&filter=' + filter;
			*/
      req += "&filter=" + filter;
    }

    if (parseInt(online) == 1) {
      req += "&online=" + online;
    }

    if (parseInt(filterCriteria) > 0) {
      req += "&criterion_id=" + filterCriteria;
    }
    if (parseInt(filterTag) > 0) {
      req += "&tag_id=" + filterTag;
    }

    window.location.href =
      "/player/export_player/" + "?t=" + new Date().getTime() + req;
  },
  change_timer: function () {
    $.get(
      "/player/update_amc_while_changing_timer/" + $("#timerConfigId").val(),
      function (data) {
        //alert(data);
        var obj = JSON.parse(data);
        $("#mon").val(obj.mon);
        $("#tue").val(obj.tue);
        $("#wed").val(obj.tue);
        $("#thu").val(obj.tue);
        $("#fri").val(obj.tue);
        $("#sat").val(obj.tue);
        $("#sun").val(obj.tue);
      }
    );
  },
};

$(document).ready(function() {
  $(".select2").select2({
    theme: "bootstrap-5",
    width: '100%',
  });
  
  const uploader = $("#pictures");
    const player_id = $("#id").val();
    if(player_id&& player_id != '0'){
    $.getJSON("/player/get_player_pictures", {
      id: player_id
    }, function(result) {
      uploader.fileinput({
        uploadUrl: "/player/upload_photo",
        enableResumableUpload: true,
        uploadExtraData: {
          'pid': $('#id').val(),
        },
        allowedFileTypes: ['image'], // allow only images
        showRemove: false,
        required: true,
        showUpload: false,
        browseOnZoneClick: true,
        initialPreviewAsData: true,
        overwriteInitial: false,
        initialPreview: result.initialPreview, // if you have previously uploaded preview files
        initialPreviewConfig: result.initialPreviewConfig, // if you have previously uploaded preview files
        deleteUrl: "/player/delete_picture",
        showUploadStats: false,
        showClose: false,
        fileActionSettings: {
          showDrag: false,
        },
        previewSettings: {
          image: {
            width: "auto",
            height: "100%",
            'max-width': "100%",
            'max-height': "100%"
          },
        },
      }).on('filebatchselected', function(event, previewId, index, fileId) {
        uploader.fileinput('upload');
      }).on('fileuploaderror', function(event, data, msg) {
        console.log('File Upload Error', 'ID: ' + data.fileId + ', Thumb ID: ' + data.previewId);
      })
    });
  }
});
