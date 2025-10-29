var campaign = {
  bbContent: "",
  bbHtmlContent: "",
  saving: false,

  initScreenFlag: function () {
    var ew = $("#enableWeek").change(function (event) {
      if (this.checked) {
        $("#sun").removeAttr("disabled").attr("checked", true);
        $("#mon").removeAttr("disabled").attr("checked", true);
        $("#tue").removeAttr("disabled").attr("checked", true);
        $("#wed").removeAttr("disabled").attr("checked", true);
        $("#thu").removeAttr("disabled").attr("checked", true);
        $("#fri").removeAttr("disabled").attr("checked", true);
        $("#sat").removeAttr("disabled").attr("checked", true);
      } else {
        $("#sun").removeAttr("checked").attr("disabled", "disabled");
        $("#mon").removeAttr("checked").attr("disabled", "disabled");
        $("#tue").removeAttr("checked").attr("disabled", "disabled");
        $("#wed").removeAttr("checked").attr("disabled", "disabled");
        $("#thu").removeAttr("checked").attr("disabled", "disabled");
        $("#fri").removeAttr("checked").attr("disabled", "disabled");
        $("#sat").removeAttr("checked").attr("disabled", "disabled");
      }
    });

    if (ew.attr("checked")) {
      $("#sun").removeAttr("disabled");
      $("#mon").removeAttr("disabled");
      $("#tue").removeAttr("disabled");
      $("#wed").removeAttr("disabled");
      $("#thu").removeAttr("disabled");
      $("#fri").removeAttr("disabled");
      $("#sat").removeAttr("disabled");
    }

    var tf = $("#enableTime").change(function (event) {
      if (this.checked) {
        $("#startTime")
          .focus(function (event) {
            WdatePicker({
              skin: "default",
              dateFmt: "HH:mm",
              lang: curLang,
            });
          })
          .removeClass("gray");
        $("#endTime")
          .focus(function (event) {
            WdatePicker({
              skin: "default",
              dateFmt: "HH:mm",
              lang: curLang,
            });
          })
          .removeClass("gray");
      } else {
        $("#startTime").unbind("focus").addClass("gray");
        $("#endTime").unbind("focus").addClass("gray");
      }
    });

    if (tf.attr("checked")) {
      $("#startTime")
        .removeClass("gray")
        .focus(function (event) {
          WdatePicker({
            skin: "default",
            dateFmt: "HH:mm",
            lang: curLang,
          });
        });

      $("#endTime")
        .removeClass("gray")
        .focus(function (event) {
          WdatePicker({
            skin: "default",
            dateFmt: "HH:mm",
            lang: curLang,
          });
        });
    }

    var df = $("#enableDate");
    if (df.attr("checked")) {
      var sd = $("#startDate");
      var ed = $("#endDate");
      sd.datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd",
      }).removeClass("gray");

      ed.datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd",
      }).removeClass("gray");
    }

    df.change(function (event) {
      var sd = $("#startDate");
      var ed = $("#endDate");
      if (this.checked) {
        sd.datepicker({
          changeMonth: true,
          changeYear: true,
          dateFormat: "yy-mm-dd",
        }).removeClass("gray");

        ed.datepicker({
          changeMonth: true,
          changeYear: true,
          dateFormat: "yy-mm-dd",
        }).removeClass("gray");
      } else {
        sd.datepicker("destroy").addClass("gray");
        ed.datepicker("destroy").addClass("gray");
      }
    });
  },
  initDatePicker: function () {
    $("#custom").change(function (event) {
      $("#playtime")
        .css("display", "")
        .focus(function (event) {
          WdatePicker({
            skin: "default",
            dateFmt: "H:mm:ss",
            lang: "en",
          });
        });
    });
    $("#default").change(function (event) {
      $("#playtime").css("display", "none");
    });
  },
  initTemplate: function () {
    $(".tab-01 a").click(function (event) {
      event.preventDefault();
      var cur = $(this).addClass("on");
      cur.siblings("a").removeClass("on");
      var type = cur.attr("type");
      var curType = $("#templateContent").attr("type");
      if (type != curType) {
        campaign.loadTemplate(type, 1);
      }
    });
    var type = $(".tab-01 a.on").attr("type");
    this.loadTemplate(type, 1);
  },
  loadTemplate: function (type, curpage) {
    type = type || $(".tab-01 a.on").attr("type");
    curpage = curpage || 1;
    $("#templateContent").attr("type", type);
    $.get(
      "/campaign/template_list/" +
        curpage +
        "?type=" +
        type +
        "&t=" +
        new Date().getTime(),
      function (data) {
        $("#templateContent").html(data);

        $("#templateContent img")
          .unbind("click")
          .click(function (event) {
            $("#templateId").val(this.id);
            $("#templateName").val(this.name);
            $("#templateType").val(this.template_type);
            tb_remove();
          });
      }
    );
  },

  get_filterstring: function (filter) {
    var obj = document.getElementsByName(filter);
    crit_ary = [];

    for (i = 0; i < obj.length; i++) {
      var chils = obj[i].childNodes;
      for (j = 0; j < chils.length; j++)
        if (chils[j].nodeName.toUpperCase() == "INPUT") {
          if (chils[j].checked) {
            var a = next(chils[j]);
            crit_ary.push(a.getAttribute("attrval"));
          }
        }
    }

    return crit_ary.toString();
  },

  doCreate: function () {
    var name = $("#name").val();
    var descr = $("#descr").val();
    var templateId = $("#templateId").val();
    var playcount = $("#playcountid").val();

    var start_date = $("#startDate").val();
    var end_date = $("#endDate").val();
    var time_flag = $("#alldayFlag").val();
    var startH = $("#startTH").val();
    var startM = 0;
    var stopH = $("#stopTH").val();
    var stopM = 0;

    if (
      name.indexOf("&") >= 0 ||
      name.indexOf("<") >= 0 ||
      name.indexOf(">") >= 0 ||
      name.indexOf("'") >= 0 ||
      name.indexOf("\\") >= 0 ||
      name.indexOf("%") >= 0
    ) {
      showFormMsg(
        "Special symbols (& < > ' \\ %) are not allowed in the campaign name.",
        "error"
      );
      return false;
    }
    if (name.length == 0) {
      $("#errorName").fadeIn();
      return;
    } else {
      $("#errorName").fadeOut();
    }

    if (!time_flag && (startH > stopH || (startH == stopH && startM > stopM))) {
      $("#errorTimerange").fadeIn();
      return;
    }

    var ob_ids = new Array();
    $('input:checkbox[name="obids"]').each(function () {
      if (this.checked) {
        ob_ids.push(this.value);
      }
    });

    var json = this._createPlaylistData();

    if(ob_ids.length !== 0){
      json.ob_ids = ob_ids;
    }
    //post
    this.restScrollPosition();
    $.post(
      "/campaign/do_save",
      json,
      function (data) {
        if (data.code == 0) {
          showMsg(data.msg, "success");
          setTimeout(function () {
            window.location.href = "/campaign/screen?id=" + data.id;
          }, 200);
        } else if (data.code == 9) {
          alert(data.msg);
          setTimeout(function () {
            window.location.href = "/campaign/screen?id=" + data.id;
          }, 200);
        } else {
          showMsg(data.msg, "error");
        }
      },
      "json"
    );
  },
  goList: function () {
    window.location.href = "/campaign/index";
  },
  page: function (curpage, orderItem, order) {
    showLoading();
    var filter = $("#filter").val();
    var filterPri = $("#filterPriority").val();
    var filterCri = $("#filterCriteria").val();
    var datecheck = $('input:checkbox[id="checkDate"]:checked').val();
    var startdate = $("#startDate").val();
    var enddate = $("#endDate").val();
    var filterTag = $("#filterTag").val();

    var req = "";
    if (filter.length > 0) {
      req += "&name=" + filter;
    }

    if (parseInt(filterPri) >= 0) {
      req += "&pri=" + filterPri;
    }
    if (parseInt(filterCri) > 0) {
      req += "&cri=" + filterCri;
    }
    if (parseInt(filterPlayer) > 0) {
      req += "&pla=" + filterPlayer;
    }

    if (parseInt(filterTag) > 0) {
      req += "&tag=" + filterTag;
    }

    if ($("#withexpired").is(":checked")) {
      req += "&withexpired=1";
    }

    if (datecheck) {
      req += "&start=" + startdate;
      req += "&end=" + enddate;
    }

    $.get(
      "/campaign/refresh/" +
        curpage +
        "/" +
        orderItem +
        "/" +
        order +
        "?t=" +
        new Date().getTime() +
        req,
      function (data) {
        $("#layoutContent").html(data);
        tb_init("a.thickbox"); //pass where to apply thickbox
        hideLoading();
        setTimeout(function () {
          hideMsg();
        }, 200);
      }
    );
  },

  refresh: function () {
    showLoading();

    var filter = $("#filter").val();
    var filterPri = $("#filterPriority").val();
    var datecheck = $('input:checkbox[id="checkDate"]:checked').val();
    var startdate = $("#startDate").val();
    var enddate = $("#endDate").val();
    var filterCri = $("#filterCriteria").val();
    var filterPlayer = $("#filterPlayer").val();
    var filterTag = $("#filterTag").val();
    var req = "";
    if (filter.length > 0) {
      req += "&name=" + filter;
    }

    if (parseInt(filterPri) > 0) {
      req += "&pri=" + filterPri;
    }

    if (parseInt(filterCri) > 0) {
      req += "&cri=" + filterCri;
    }

    if (parseInt(filterPlayer) > 0) {
      req += "&pla=" + filterPlayer;
    }

    if (parseInt(filterTag) > 0) {
      req += "&tag=" + filterTag;
    }

    if ($("#withexpired").is(":checked")) {
      req += "&withexpired=1";
    }

    if (datecheck) {
      req += "&start=" + startdate;
      req += "&end=" + enddate;
    }

    $.get("/campaign/refresh?t=" + new Date().getTime() + req, function (data) {
      $("#layoutContent").html(data);
      tb_init("a.thickbox"); //pass where to apply thickbox
      hideLoading();
      setTimeout(function () {
        hideMsg();
      }, 200);
    });
  },
  remove: function (id, msg) {
    if (confirm(msg)) {
      $.get(
        "/campaign/do_delete?id=" + id + "&t=" + new Date().getTime(),
        function (data) {
          if (data.code == 0) {
            showMsg(data.msg, "success");
            setTimeout(function () {
              campaign.refresh();
            }, 100);
          } else {
            showMsg(data.msg, "error");
          }
        },
        "json"
      );
    }
  },
  initScreenOp: function (content, htmlc) {
    campaign.bbContent = content;
    campaign.bbHtmlContent = htmlc;
    $(".icon-list img").click(function (event) {
      var img = $(event.target);
      var areaId = img.attr("id");
      $(".icon-list img").each(function () {
        //test
        var tImg = $(this);
        var tsrc = tImg.attr("src");
        if (tsrc.indexOf("-on") > 0) {
          if (
            tImg.attr("title") == "Bulletin Board" ||
            tImg.attr("title") == "StaticText"
          ) {
            campaign.bbHtmlContent = $("#static_ticker").val();
            var content = campaign.ApplyLineBreaks("static_ticker");
            campaign.bbContent = content;
          }
        }
      });

      if (img.attr("src").indexOf("-on") > 0) {
        return;
      }

      if (areaId != null) {
        //change tab selected
        $(".icon-list img").each(function () {
          var opImg = $(this);
          var src = opImg.attr("src");

          if (opImg.attr("id") == areaId) {
            //set  on
            src = src.replace(".gif", "-on.gif");
            opImg.attr("src", src);
          } else {
            //set off
            if (src.indexOf("-on") > 0) {
              src = src.replace("-on", "");
              opImg.attr("src", src);
            }
          }
        });
        var targetAreaId = "content_" + areaId;
        $(".tab-area").each(function () {
          if (this.id == targetAreaId) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
      }
    });
  },
  loadArea: function (playlistId, areaId, mediaOp) {
    //mediaOp 0:default 1:add, 2: delete
    var area = $("#content_" + areaId);
    if (area.css("display") != "none") {
      //set selected icon
      var opImg = $("#" + areaId);
      var src = opImg.attr("src");
      if (src.indexOf("-on") == -1) {
        src = src.replace(".gif", "-on.gif");
        opImg.attr("src", src);
      }
    }
    if (mediaOp == undefined) {
      mediaOp = 0;
    }

    area.html(
      '<div style="top: 10%; left: 30%; position:relative;" class="loading-01">Loading......</div>'
    );
    $.get(
      "/campaign/area?playlist_id=" +
        playlistId +
        "&area_id=" +
        areaId +
        (mediaOp > 0 ? "&after_media=" + mediaOp : "") +
        "&t=" +
        new Date().getTime(),
      function (data) {
        area.html(data);
        //初始化编辑

        // tb_init('#content_' + areaId + ' td > a.thickbox');
        tb_init(".content a.thickbox");
        campaign.bindMediaMove(areaId);
        hideMsg();
      }
    );
  },
  bindMediaMove: function (areaId) {
    var up = $("#content_" + areaId + " .up");
    var down = $("#content_" + areaId + " .down");
    up.unbind("click").bind("click", function () {
      var img = $(this);
      var cid = img.attr("cid");
      var pid = img.attr("pid");
      campaign.changeMediaOrder(areaId, pid, cid);
    });
    down.unbind("click").bind("click", function () {
      var img = $(this);
      var cid = img.attr("cid");
      var nid = img.attr("nid");
      campaign.changeMediaOrder(areaId, cid, nid);
    });
  },
  rotateMedia: function (obj, id) {
    var rotate = 0;
    if (obj.checked) {
      rotate = 1;
    }

    $.post(
      "/campaign/do_rotate_media",
      {
        id: id,
        rotate: rotate,
      },
      function (data) {
        if (data.code != 0) {
          showMsg(data.msg, "error");
        }
      },
      "json"
    );
  },
  changeMediaOrder: function (areaId, fid, sid) {
    //first id  and second id
    var playlistId = $("#playlistId").val();
    $.post(
      "/campaign/do_change_media_order",
      {
        playlist_id: playlistId,
        area_id: areaId,
        fid: fid,
        sid: sid,
      },
      function (data) {
        if (data.code == 0) {
          campaign.loadArea(playlistId, areaId);
        } else {
          showMsg(data.msg, "error");
        }
      },
      "json"
    );
  },
  moveTo: function (obj, areaId, id, total, warnNum, warnBound) {
    var cur = $(obj);
    var old = cur.attr("position");
    if (!/^[0-9]*$/.test(obj.value)) {
      alert(warnNum);

      cur.val(old);
      return;
    }

    if (obj.value <= 0 || obj.value > total) {
      alert(warnBound);
      cur.val(old);
      return;
    }

    if (obj.value == old) {
      return;
    }

    var playlistId = $("#playlistId").val();
    $.post(
      "/campaign/do_move_to",
      {
        playlist_id: playlistId,
        area_id: areaId,
        id: id,
        index: obj.value,
      },
      function (data) {
        if (data.code == 0) {
          campaign.loadArea(playlistId, areaId);
        } else {
          showMsg(data.msg, "error");
        }
      },
      "json"
    );
  },
  initMediaPanel: function () {
    $("#TB_ajaxContent .tab-02 a").click(function (event) {
      event.preventDefault();
      var cur = $(this).addClass("on");
      cur.siblings("a").removeClass("on");
      var type = cur.attr("type");
      var curType = $("#layoutContent").attr("type");

      campaign.addAreaMediaFilter(
        $("#playlistId").val(),
        $("#areaId").val(),
        $("#bmp").val(),
        $("#mediaType").val(),
        $("#curpage").val(),
        type,
        $("#orderItem").val(),
        $("#order").val()
      );
    });
    //init filter
    var filterType = $("#filterType").val();
    var filter = $("#filter");
    if (filterType == "add_time") {
      filter.datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd",
      });
      filter.attr("readonly", true);
      filter.addClass("date-input");
    } else {
      filter.attr("readonly", false);
      filter.datepicker("destroy");
      filter.removeClass("date-input");
    }
  },
  chooseAreaAllMedia: function (obj, areaId) {
    $("#content_" + areaId + " input[name='id']").each(function () {
      this.checked = obj.checked;
    });
  },
  chooseAreaAllExclude: function (obj, areaId, playlistId) {
    var ids = [];
    var status;
    $("#content_" + areaId + " input[name='status']").each(function () {
      this.checked = obj.checked;
    });
    if (obj.checked) {
      status = 1;
    } else {
      status = 0;
    }
    $.post(
      "/campaign/do_editAreaStatus",
      { playlistId: playlistId, areaId: areaId, status: status },
      function (date) {
        //campaign/.loadArea(playlistId, areaId);
      }
    );
  },
  removeAreaAllMedia: function (playlistId, areaId, emptyTip, cfmMsg) {
    var ids = [];
    $("#content_" + areaId + " input:checkbox").each(function () {
      if (this.checked) {
        var obj = $(this);
        if (obj.val() != "0") {
          ids.push(obj.val());
        }
      }
    });
    if (ids.length == 0) {
      alert(emptyTip);
      return;
    }
    campaign.removeAreaMedia(playlistId, areaId, ids, cfmMsg);
  },
  //删除区域的媒体文件信息
  removeAreaMedia: function (playlistId, areaId, id, cfmMsg) {
    if (confirm(cfmMsg)) {
      $.post(
        "/campaign/delete_media?t=" + new Date().getTime(),
        {
          playlist_id: playlistId,
          area_id: areaId,
          id: id,
          tags: $("#tag-select-options").val(),
        },
        function (data) {
          if (data.code == 0) {
            campaign.loadArea(playlistId, areaId, 2);
            if (data.tags) {
              $("#tag-select-options")
                .val(data.tags)
                .trigger("chosen:updated")
                .change();
            }
          } else {
            showMsg(data.msg, "error");
          }
        },
        "json"
      );
    }
  },
  addAreaMedia: function (
    playlistId,
    areaId,
    areaType,
    mediaType,
    curpage,
    title
  ) {
    if (curpage == null) {
      curpage = 1;
    }
    if (areaType == 0) {
      areaType = "video";
    }
    if (areaType == 1) {
      areaType = "image";
    }
    if (areaType == 8) {
      areaType = "logo";
    }
    if (areaType == 9) {
      areaType = "bg";
    }
    if (areaType == 28) {
      areaType = "logo";
    }
    if (areaType == 7) {
      var req =
        "/campaign/add_playlist_media_webpage?playlist_id=" +
        playlistId +
        "&area_id=" +
        areaId +
        "&media_type=" +
        mediaType +
        "&width=430&height=200";
    } else {
      var req =
        "/campaign/media_panel?playlist_id=" +
        playlistId +
        "&area_id=" +
        areaId +
        "&bmp=" +
        areaType +
        "&media_type=" +
        mediaType +
        "&curpage=" +
        curpage +
        "&width=1024&height=520";
    }
    // var req = '/campaign/media_panel?playlist_id=' + playlistId + '&area_id=' + areaId + '&bmp=' + areaType + '&media_type=' + mediaType + '&curpage=' + curpage + '&width=1024&height=520';
    if (title == undefined) {
      showLoading();
      $.get(req, function (data) {
        $("#TB_ajaxContent").html(data);
        hideLoading();
      });
    } else {
      tb_show(title, req, "");
    }
    //this.addAreaMediaFilter(playlistId, areaId, mediaType, curpage);
  },
  changeFilterType: function (obj) {
    var filter = $("#filter");
    filter.val("");

    if (obj.value == "add_time") {
      filter.datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd",
      });
      filter.addClass("date-input");
      filter.attr("readonly", true);
    } else {
      filter.datepicker("destroy");
      filter.attr("readonly", false);
      filter.removeClass("date-input");
    }
  },
  //addAreaMediaFilter: function(playlistId, areaId, areaType, mediaType, curpage, orderItem, order){
  addAreaMediaFilter: function (
    playlistId,
    areaId,
    areaType,
    mediaType,
    curpage,
    type,
    orderItem,
    order
  ) {
    if (playlistId == undefined) {
      playlistId = $("#playlistId").val();
    }
    if (areaId == undefined) {
      areaId = $("#areaId").val();
    }

    if (mediaType == undefined) {
      mediaType = $("#mediaType").val();
    }

    if (curpage == undefined) {
      curpage = 1;
    }
    if (orderItem == undefined) {
      orderItem = $("#orderItem").val();
    }
    if (order == undefined) {
      order = $("#order").val();
    }
    if (type == undefined) {
      type = $(".tab-02 a.on").attr("type");
    }

    var req =
      "/campaign/media_panel_filter?playlist_id=" +
      playlistId +
      "&area_id=" +
      areaId +
      "&bmp=" +
      $("#bmp").val() +
      "&media_type=" +
      mediaType +
      "&curpage=" +
      curpage +
      "&order_item=" +
      orderItem +
      "&order=" +
      order +
      "&type=" +
      type;

    var filterType = $("#filterType").val();
    var filter = $("#filter").val();
    var filterFolder = $("#filterFolder").val();
    var filterUploader = $("#filterUploader").val();

    if (filter.length > 0) {
      req += "&filter_type=" + filterType + "&filter=" + filter;
    }

    if (parseInt(filterFolder) >= 0) {
      req += "&folder_id=" + filterFolder;
    }
    if (parseInt(filterUploader) > 0) {
      req += "&uid=" + filterUploader;
    }

    showLoading();
    $.get(req, function (data) {
      $("#layoutContent").html(data);
      hideLoading();
      $("#curpage").val(curpage);
      $("#orderItem").val(orderItem);
      $("#order").val(order);
    });
  },
  checkAllMedia: function (obj) {
    $('input:checkbox[name="mid"]').each(function () {
      this.checked = obj.checked;
    });
  },
  chooseMedia: function (mediaId) {
    var cb = $("#" + mediaId);
    cb.attr("checked", !cb.attr("checked"));
  },
  saveAreaMedia: function (playlistId, areaId, emptyTip, close, radio) {
    if (radio == undefined) {
      radio = false;
    }

    var mediaType = $("#mediaType").val();

    $.post(
      "/campaign/do_save_media_check?t=" + new Date().getTime(),
      {
        media_type: mediaType,
        playlist_id: playlistId,
        area_id: areaId,
      },
      function (checkData) {
        if (checkData.code == 0) {
          var media = [];

          if (radio) {
            $("input:radio[name='mid']:checked").each(function () {
              if (this.checked) {
                media.push(this.value);
              }
            });
          } else {
            $("input:checkbox[name='mid']:checked").each(function () {
              if (this.checked) {
                media.push(this.value);
              }
            });
          }
          if (media.length == 0) {
            alert(emptyTip);
            return;
          }

          var selallflag = false;
          $("input:checkbox[name='checkAll']:checked").each(function () {
            if (this.checked) {
              selallflag = true;
            }
          });

          $.post(
            "/campaign/do_save_media?t=" + new Date().getTime(),
            {
              playlist_id: playlistId,
              area_id: areaId,
              medias: media,
              folderid: $("#filterFolder").val(),
              selallflag: selallflag,
              media_type: mediaType,
              tags: $("#tag-select-options").val(),
              priority: $("#priority-options").val(),
            },
            function (data) {
              if (data == undefined) {
                return;
              }
              if (data.code == 0) {
                showFormMsg(data.msg, "success");
                if (data.tags) {
                  $("#tag-select-options")
                    .val(data.tags)
                    .trigger("chosen:updated")
                    .change();
                }
                setTimeout(function () {
                  campaign.loadArea(playlistId, areaId, 1);
                  if (close) {
                    tb_remove();
                  }
                }, 500);
              } else {
                showFormMsg(data.msg, "error");
              }
            },
            "json"
          );
        } else {
          showFormMsg(checkData.msg, "error");
        }
      },
      "json"
    );
  },
  saveAreaMediaWebpage: function (playlistId, areaId, mediaType) {
    var url = $("#url").val();
    var play_time = "01:00";
    var startDate = $("#startDate").val();
    var endDate = $("#endDate").val();
    var updateF = $("#updateF").val();
    var url_type = $("#url_type").attr("checked") ? 1 : 0;
    var media = [];
    media.push(1);

    $.post(
      "/campaign/do_save_webpage_media?t=" + new Date().getTime(),
      {
        playlist_id: playlistId,
        area_id: areaId,
        medias: media,
        media_type: mediaType,
        url: url,
        play_time: play_time,
        startDate: startDate,
        endDate: endDate,
        updateF: updateF,
        url_type: url_type,
      },
      function (data) {
        if (data == undefined) {
          return;
        }
        if (data.code == 0) {
          showFormMsg(data.msg, "success");
          setTimeout(function () {
            campaign.loadArea(playlistId, areaId, 1);
            if (close) {
              tb_remove();
            }
          }, 500);
        } else {
          showFormMsg(data.msg, "error");
        }
      },
      "json"
    );
  },
  _createPlaylistData: function () {
    var json = new Object();
    //form area
    var playlistId = $("#playlistId").val();

    var name = $("#name").val();
    var descr = $("#descr").val();

    var form = {
      name: name,
      descr: descr,
      id: playlistId,
      priority: $("#priority-options").val(),
      play_count: $("#playcountid").val(),
      date_flag: $("#dateFlag").val(),
      start_date: $("#startDate").val(),
      end_date: $("#endDate").val(),
      time_flag: $("#alldayFlag").val(),
      startH: $("#startTH").val(),
      startM: 0,
      endH: $("#stopTH").val(),
      endM: 0,
      playcnttype: $("#playcnttype").val(),
      playweight: $("#playweightid").val(),
      playtotal: $("#playtotalid").val(),
      priority: $("#priority-options").val(),
      tag_options: $("#tag-options").val(),
      customerid: $("#customerid").val(),
      contractid: $("#contractid").val(),
      agencyid: $("#agencyid").val(),
      customername: $("#customername").val(),
      is_grouped: $("#grouped") && $("#grouped").is(":checked") ? 1 : 0,
      is_locked: $("#lockded").is(":checked") ? 1 : 0,
      contactname: $("#contactname").val(),
      customertype: $("#customertype").val(),
      campaignvalue: $("#campaignvalue").val(),
    };

    json.playlist = form;
    json.playlist_id = playlistId;

    //media add
    var ids = [];
    $(".table-list input:checkbox").each(function () {
      if (parseInt(this.value) > 0) {
        ids.push(this.value);
      }
    });

    json.ids = ids;

    json.criteria = $("#criteria-select-options").val();
    json.and_criteria = $("#criteria-and-select-options").val();
    json.and_criteria_or = $("#criteria-and-select-options-or").val();
    json.ex_criteria = $("#criteria-ex-select-options").val();
    json.players = $("#players-select-options").val();
    json.ex_players = $("#exclude_players_options").val();
    json.tags = $("#tag-select-options").val();

    return json;
  },
  restScrollPosition: function () {
    var d = $(document);
    if (d.scrollTop() > 20) {
      d.scrollTop(0);
    }
  },
  savePlaylist: function () {
    var json = this._createPlaylistData();
    //post
    this.restScrollPosition();
    $.post(
      "/campaign/do_save",
      json,
      function (data) {
        if (data.code == 0) {
          showMsg(data.msg, "success");

          if (typeof data.OverBooking != "undefined") {
            if (data.OverBooking == 1) {
              alertify.alert(data.ob_msg);
            }
          }

          if (data.tags) {
            var str_array = data.tags.split(",");
            $("#tag-select-options")
              .val(str_array)
              .trigger("chosen:updated")
              .change();
          }

          if (!(typeof data.total_times == "undefined"))
            $("#total_times").val(data.total_times);
          if (!(typeof data.costs == "undefined")) $("#cost").val(data.costs);

          if (data.affected_players) {
            $("#affected_players").html(data.affected_players);
          }

          setTimeout(function () {
            hideMsg();
          }, 100);
        } else {
          showMsg(data.msg, "error");
        }
      },
      "json"
    );
  },
  checkAreaMediaPublished: function () {
    var result = true;
    var movie = $(".table_movie");
    var bg = $(".table_bg");
    var logo = $(".table_logo");
    var mask = $(".table_mask");
    if (movie[0] != undefined) {
      if (movie.find("tbody input:checkbox").length == 0) {
        return false;
      }
    }
    /*
        //image区域可以为空
        $('.table_image').each(function(){
            var image = $(this);
            if (image[0] != undefined) {
                if (image.find('tbody input:checkbox').length == 0) {
                    result = false;
                }
            }
        });*/

    if (bg[0] != undefined) {
      if (bg.find("tbody input:checkbox").length == 0) {
        return false;
      }
    }

    if (logo[0] != undefined) {
      if (logo.find("tbody input:checkbox").length == 0) {
        return false;
      }
    }

    if (mask[0] != undefined) {
      if (mask.find("tbody input:checkbox").length == 0) {
        return false;
      }
    }

    return result;
  },
  publishPlaylist: function (msgEmpty, portrait) {
    var priority = $("#priority-options").val();
    if (priority != 5 && !this.checkAreaMediaPublished()) {
      alert(msgEmpty);
      return false;
    }
    var rotate = $('input:checkbox[name="rotate"]:checked').length > 0;
    if (portrait) {
      campaign.postPublishPlaylist(true, true);
      /*
            $("#rotateConfirm").dialog({
                resizable: false,
                height: 210,
                modal: true,
                buttons: {
                    "Fill": function(){
                        $(this).dialog("close");
                        playlist.postPublishPlaylist(true);
                    },
                    "Fit": function(){
                        $(this).dialog("close");
                        playlist.postPublishPlaylist(true, true);
                    },
                    "OK": function(){
                        $(this).dialog("close");
                        playlist.postPublishPlaylist(false);
                    }
                }
            });
            */
    } else {
      campaign.postPublishPlaylist(false);
    }
  },
  postPublishPlaylist: function (rotate, fit) {
    if (campaign.saving) {
      return;
    }
    fit = fit || false;
    var publish = $("#publish");
    publish.attr("disable", true);
    publish.removeClass();
    publish.addClass("btn-02");
    var json = this._createPlaylistData();
    json.rotate = rotate ? 1 : 0;
    json.fit = fit ? 1 : 0;
    var publishing = $("#publishing");
    publishing.show();
    this.restScrollPosition();

    if ($("#criteria-select-options").val().length === 0) {
      var players = $("#players-select-options");
      if (players) {
        if (players.val().length === 0) {
          alertify.alert("Please choose at least one criteria or player!");
          return;
        }
      } else {
        alertify.alert("Please choose at least one criteria or player!");
        return;
      }
    }

    $.post(
      "/campaign/do_publish",
      json,
      function (data) {
        if (data.code == 0) {
          hideMsg();

          if (data.extra_code) {
            alertify.alert(data.extra_msg, function () {
              showMsg(data.msg, "success");
              setTimeout(function () {
                window.location.href = "/campaign/index";
              }, 200);
            });
          } else {
            showMsg(data.msg, "success");
            setTimeout(function () {
              window.location.href = "/campaign/index";
            }, 200);
          }
        } else {
          alertify.alert(data.msg);
          //showMsg(data.msg, 'error');
        }
        publish.attr("disable", false);
        publish.removeClass();
        publish.addClass("btn-01");
        publishing.hide();
      },
      "json"
    );
  },
  publishPlaylistView: function (msgEmpty, portrait) {
    if (!this.checkAreaMediaPublished()) {
      alert(msgEmpty);
      return false;
    }
    var rotate = $('input:checkbox[name="rotate"]:checked').length > 0;
    if (portrait) {
      campaign.postPublishPlaylistView(true, true);
    } else {
      campaign.postPublishPlaylistView(false);
    }
  },
  postPublishPlaylistView: function (rotate, fit) {
    if (campaign.saving) {
      return;
    }
    fit = fit || false;
    var publish = $("#publish");
    publish.attr("disable", true);
    publish.removeClass();
    publish.addClass("btn-02");
    var json = this._createPlaylistData();
    json.rotate = rotate ? 1 : 0;
    json.fit = fit ? 1 : 0;
    var publishing = $("#publishing");
    publishing.show();
    this.restScrollPosition();
    $.post(
      "/campaign/do_publish",
      json,
      function (data) {
        if (data.code == 0) {
          hideMsg();
          showMsg(data.msg, "success");
          setTimeout(function () {
            window.location.href = "/campaign/view_playlist";
          }, 200);
        } else {
          showMsg(data.msg, "error");
        }
        publish.attr("disable", false);
        publish.removeClass();
        publish.addClass("btn-01");
        publishing.hide();
      },
      "json"
    ).error(function (e) {
      alert(e.responseText);
      publishing.hide();
    });
  },
  initTextArea: function () {
    $(".tab-01 a").click(function (event) {
      event.preventDefault();
      var cur = $(this);
      cur.addClass("on").siblings("a").removeClass("on");
      num = $(".tab-01 a").index(cur);
      $(".tab-01-in").eq(num).show().siblings(".tab-01-in").hide();
      if (cur.attr("type") == "rss") {
        //show
        $("#rssOperate").show();
      } else {
        //hide
        $("#rssOperate").hide();
      }
    });

    //color
    $("#colorSelector").ColorPicker({
      color: $("#color").val(),
      onShow: function (colpkr) {
        $(colpkr).fadeIn(500);
        return false;
      },
      onHide: function (colpkr) {
        $(colpkr).fadeOut(500);
        return false;
      },
      onSubmit: function (hsb, hex, rgb, el) {
        $("#colorSelector div").css("backgroundColor", "#" + hex);
        $("#color").val("#" + hex);
        $(el).ColorPickerHide();
      },
    });

    //bg
    $("#bgColorSelector").ColorPicker({
      color: $("#bgColor").val(),
      onShow: function (colpkr) {
        $(colpkr).fadeIn(500);
        return false;
      },
      onHide: function (colpkr) {
        $(colpkr).fadeOut(500);
        return false;
      },
      onSubmit: function (hsb, hex, rgb, el) {
        $("#bgColorSelector div").css("backgroundColor", "#" + hex);
        $("#bgColor").val("#" + hex);
        $(el).ColorPickerHide();
      },
    });

    //set duration
    /*$('#duration').focus(function(event){
         WdatePicker({skin:'default',dateFmt:'HH:mm:ss',minDate:'00:00:00',maxDate:'00:59:59',quickSel:['00:01:00','00:02:00','00:05:00','00:10:00','00:15:00'],lang:curLang});
         });*/
  },
  initDateArea: function () {
    $("#countdown").datetimepicker({
      showSecond: true,
      timeFormat: "hh:mm",
      dateFormat: "yy-mm-dd",
      stepHour: 1,
      stepMinute: 1,
    });
    //color
    $("#dateColorSelector").ColorPicker({
      color: $("#dateColor").val(),
      onShow: function (colpkr) {
        $(colpkr).fadeIn(500);
        return false;
      },
      onHide: function (colpkr) {
        $(colpkr).fadeOut(500);
        return false;
      },
      onSubmit: function (hsb, hex, rgb, el) {
        $("#dateColorSelector div").css("backgroundColor", "#" + hex);
        $("#dateColor").val("#" + hex);
        $(el).ColorPickerHide();
        //更改页面预览内容
        var dateFontSize = $("#dateFontSize").val();
        var dateColor = $("#dateColor").val();
        var dateBgColor = $("#dateBgColor").val();
        var dataStyle = $("#dataStyle").val();
        var dtransparent = $("#dtransparent").val();
        var myDate = new Date();
        var year = myDate.getYear(); //获取当前年份(2位)
        var fullyear = myDate.getFullYear(); //获取完整的年份(4位,1970-????)
        var month = myDate.getMonth() + 1; //获取当前月份(0-11,0代表1月)
        var date = myDate.getDate(); //获取当前日(1-31)
        var day = myDate.getDay(); //天
        var week;
        var htmlDate;
        switch (day) {
          case 0:
            week = "Sunday";
            break;
          case 1:
            week = "Monday";
            break;
          case 2:
            week = "Tuesday";
            break;
          case 3:
            week = "Wednesday";
            break;
          case 4:
            week = "Thursday";
            break;
          case 5:
            week = "Friday";
            break;
          case 6:
            week = "Saturday";
            break;
        }
        var red = parseInt(dateBgColor.substr(1, 2), 16);
        var green = parseInt(dateBgColor.substr(3, 2), 16);
        var blue = parseInt(dateBgColor.substr(5, 2), 16);
        $("#datePreview").css(
          "background-color",
          "rgba(" +
            red +
            "," +
            green +
            ", " +
            blue +
            ", " +
            (1 - dtransparent / 100) +
            ")"
        );
        $(".countdown").hide();
        $(".time30").show();
        if (dataStyle == 1) {
          $("#datePreview").html(
            '<font style="font-size:' +
              dateFontSize +
              "px;color:" +
              dateColor +
              ';">' +
              month +
              "/" +
              date +
              "/" +
              fullyear +
              "&nbsp;" +
              week +
              "</font>"
          );
        }
        if (dataStyle == 2) {
          $("#datePreview").html(
            '<font style="font-size:' +
              dateFontSize +
              "px;color:" +
              dateColor +
              ';">' +
              date +
              "/" +
              month +
              "/" +
              fullyear +
              "&nbsp;" +
              week +
              "</font>"
          );
        }
        if (dataStyle == 3) {
          $("#datePreview").html(
            '<font style="font-size:' +
              dateFontSize +
              "px;color:" +
              dateColor +
              ';">' +
              fullyear +
              "/" +
              month +
              "/" +
              date +
              "&nbsp;" +
              week +
              "</font>"
          );
        }
        if (dataStyle == 4) {
          $("#datePreview").html(
            '<font style="font-size:' +
              dateFontSize +
              "px;color:" +
              dateColor +
              ';">' +
              month +
              "/" +
              date +
              "/" +
              fullyear +
              "</font>"
          );
        }
        if (dataStyle == 5) {
          $("#datePreview").html(
            '<font style="font-size:' +
              dateFontSize +
              "px;color:" +
              dateColor +
              ';">' +
              date +
              "/" +
              month +
              "/" +
              fullyear +
              "</font>"
          );
        }
        if (dataStyle == 6) {
          $("#datePreview").html(
            '<font style="font-size:' +
              dateFontSize +
              "px;color:" +
              dateColor +
              ';">' +
              fullyear +
              "/" +
              month +
              "/" +
              date +
              "</font>"
          );
        }
        if (dataStyle == 9) {
          $(".countdown").show();
          $(".time30").hide();
        }
      },
    });
    $("#dateBgColorSelector").ColorPicker({
      color: $("#dateBgColor").val(),
      onShow: function (colpkr) {
        $(colpkr).fadeIn(500);
        return false;
      },
      onHide: function (colpkr) {
        $(colpkr).fadeOut(500);
        return false;
      },
      onSubmit: function (hsb, hex, rgb, el) {
        $("#dateBgColorSelector div").css("backgroundColor", "#" + hex);
        $("#dateBgColor").val("#" + hex);
        $(el).ColorPickerHide();
        //更改页面预览内容
        var dateFontSize = $("#dateFontSize").val();
        var dateColor = $("#dateColor").val();
        var dateBgColor = $("#dateBgColor").val();
        var dataStyle = $("#dataStyle").val();
        var dtransparent = $("#dtransparent").val();
        var myDate = new Date();
        var year = myDate.getYear(); //获取当前年份(2位)
        var fullyear = myDate.getFullYear(); //获取完整的年份(4位,1970-????)
        var month = myDate.getMonth() + 1; //获取当前月份(0-11,0代表1月)
        var date = myDate.getDate(); //获取当前日(1-31)
        var day = myDate.getDay(); //天
        var week;
        var htmlDate;
        switch (day) {
          case 0:
            week = "Sunday";
            break;
          case 1:
            week = "Monday";
            break;
          case 2:
            week = "Tuesday";
            break;
          case 3:
            week = "Wednesday";
            break;
          case 4:
            week = "Thursday";
            break;
          case 5:
            week = "Friday";
            break;
          case 6:
            week = "Saturday";
            break;
        }
        var red = parseInt(dateBgColor.substr(1, 2), 16);
        var green = parseInt(dateBgColor.substr(3, 2), 16);
        var blue = parseInt(dateBgColor.substr(5, 2), 16);
        $("#datePreview").css(
          "background-color",
          "rgba(" +
            red +
            "," +
            green +
            ", " +
            blue +
            ", " +
            (1 - dtransparent / 100) +
            ")"
        );
        $(".countdown").hide();
        $(".time30").show();
        if (dataStyle == 1) {
          $("#datePreview").html(
            '<font style="font-size:' +
              dateFontSize +
              "px;color:" +
              dateColor +
              ';">' +
              month +
              "/" +
              date +
              "/" +
              fullyear +
              "&nbsp;" +
              week +
              "</font>"
          );
        }
        if (dataStyle == 2) {
          $("#datePreview").html(
            '<font style="font-size:' +
              dateFontSize +
              "px;color:" +
              dateColor +
              ';">' +
              date +
              "/" +
              month +
              "/" +
              fullyear +
              "&nbsp;" +
              week +
              "</font>"
          );
        }
        if (dataStyle == 3) {
          $("#datePreview").html(
            '<font style="font-size:' +
              dateFontSize +
              "px;color:" +
              dateColor +
              ';">' +
              fullyear +
              "/" +
              month +
              "/" +
              date +
              "&nbsp;" +
              week +
              "</font>"
          );
        }
        if (dataStyle == 4) {
          $("#datePreview").html(
            '<font style="font-size:' +
              dateFontSize +
              "px;color:" +
              dateColor +
              ';">' +
              month +
              "/" +
              date +
              "/" +
              fullyear +
              "</font>"
          );
        }
        if (dataStyle == 5) {
          $("#datePreview").html(
            '<font style="font-size:' +
              dateFontSize +
              "px;color:" +
              dateColor +
              ';">' +
              date +
              "/" +
              month +
              "/" +
              fullyear +
              "</font>"
          );
        }
        if (dataStyle == 6) {
          $("#datePreview").html(
            '<font style="font-size:' +
              dateFontSize +
              "px;color:" +
              dateColor +
              ';">' +
              fullyear +
              "/" +
              month +
              "/" +
              date +
              "</font>"
          );
        }
        if (dataStyle == 9) {
          $(".countdown").show();
          $(".time30").hide();
        }
      },
    });
  },
  initTimeArea: function () {
    //color
    $("#timeColorSelector").ColorPicker({
      color: $("#timeColor").val(),
      onShow: function (colpkr) {
        $(colpkr).fadeIn(500);
        return false;
      },
      onHide: function (colpkr) {
        $(colpkr).fadeOut(500);
        return false;
      },
      onSubmit: function (hsb, hex, rgb, el) {
        $("#timeColorSelector div").css("backgroundColor", "#" + hex);
        $("#timeColor").val("#" + hex);
        $(el).ColorPickerHide();
        var timeFontSize = $("#timeFontSize").val();
        var timeColor = $("#timeColor").val();
        var timeBgColor = $("#timeBgColor").val();
        var timeStyle = $("#timeStyle").val();
        var ttransparent = $("#ttransparent").val();
        var myDate = new Date();
        var hours = myDate.getHours(); //获取当前小时数(0-23)
        var minutes = myDate.getMinutes(); //获取当前分钟数(0-59)
        var red = parseInt(timeBgColor.substr(1, 2), 16);
        var green = parseInt(timeBgColor.substr(3, 2), 16);
        var blue = parseInt(timeBgColor.substr(5, 2), 16);
        $("#timePreview").css(
          "background-color",
          "rgba(" +
            red +
            "," +
            green +
            ", " +
            blue +
            ", " +
            (1 - ttransparent / 100) +
            ")"
        );
        if (timeStyle == 1) {
          if (hours >= 10 && minutes >= 10) {
            $("#timePreview").html(
              '<font style="font-size:' +
                timeFontSize +
                "px;color:" +
                timeColor +
                ';">' +
                hours +
                ":" +
                minutes +
                "</font>"
            );
          } else if (hours >= 10 && minutes < 10) {
            $("#timePreview").html(
              '<font style="font-size:' +
                timeFontSize +
                "px;color:" +
                timeColor +
                ';">' +
                hours +
                ":0" +
                minutes +
                "</font>"
            );
          } else if (hours < 10 && minutes < 10) {
            $("#timePreview").html(
              '<font style="font-size:' +
                timeFontSize +
                "px;color:" +
                timeColor +
                ';">' +
                hours +
                ":0" +
                minutes +
                "</font>"
            );
          } else if (hours < 10 && minutes >= 10) {
            $("#timePreview").html(
              '<font style="font-size:' +
                timeFontSize +
                "px;color:" +
                timeColor +
                ';">' +
                hours +
                ":" +
                minutes +
                "</font>"
            );
          }
          //$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'</font>');
        } else {
          if (hours + 1 > 12) {
            hours = hours - 12;
            if (hours >= 10 && minutes >= 10) {
              $("#timePreview").html(
                '<font style="font-size:' +
                  timeFontSize +
                  "px;color:" +
                  timeColor +
                  ';">' +
                  hours +
                  ":" +
                  minutes +
                  "PM</font>"
              );
            } else if (hours >= 10 && minutes < 10) {
              $("#timePreview").html(
                '<font style="font-size:' +
                  timeFontSize +
                  "px;color:" +
                  timeColor +
                  ';">' +
                  hours +
                  ":0" +
                  minutes +
                  "PM</font>"
              );
            } else if (hours < 10 && minutes < 10) {
              $("#timePreview").html(
                '<font style="font-size:' +
                  timeFontSize +
                  "px;color:" +
                  timeColor +
                  ';">' +
                  hours +
                  ":0" +
                  minutes +
                  "PM</font>"
              );
            } else if (hours < 10 && minutes >= 10) {
              $("#timePreview").html(
                '<font style="font-size:' +
                  timeFontSize +
                  "px;color:" +
                  timeColor +
                  ';">' +
                  hours +
                  ":" +
                  minutes +
                  "PM</font>"
              );
            }
            //$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'PM</font>');
          } else {
            if (hours >= 10 && minutes >= 10) {
              $("#timePreview").html(
                '<font style="font-size:' +
                  timeFontSize +
                  "px;color:" +
                  timeColor +
                  ';">' +
                  hours +
                  ":" +
                  minutes +
                  "AM</font>"
              );
            } else if (hours >= 10 && minutes < 10) {
              $("#timePreview").html(
                '<font style="font-size:' +
                  timeFontSize +
                  "px;color:" +
                  timeColor +
                  ';">' +
                  hours +
                  ":0" +
                  minutes +
                  "AM</font>"
              );
            } else if (hours < 10 && minutes < 10) {
              $("#timePreview").html(
                '<font style="font-size:' +
                  timeFontSize +
                  "px;color:" +
                  timeColor +
                  ';">' +
                  hours +
                  ":0" +
                  minutes +
                  "AM</font>"
              );
            } else if (hours < 10 && minutes >= 10) {
              $("#timePreview").html(
                '<font style="font-size:' +
                  timeFontSize +
                  "px;color:" +
                  timeColor +
                  ';">' +
                  hours +
                  ":" +
                  minutes +
                  "AM</font>"
              );
            }
            //$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'AM</font>');
          }
        }
      },
    });
    $("#timeBgColorSelector").ColorPicker({
      color: $("#timeBgColor").val(),
      onShow: function (colpkr) {
        $(colpkr).fadeIn(500);
        return false;
      },
      onHide: function (colpkr) {
        $(colpkr).fadeOut(500);
        return false;
      },
      onSubmit: function (hsb, hex, rgb, el) {
        $("#timeBgColorSelector div").css("backgroundColor", "#" + hex);
        $("#timeBgColor").val("#" + hex);
        $(el).ColorPickerHide();
        var timeFontSize = $("#timeFontSize").val();
        var timeColor = $("#timeColor").val();
        var timeBgColor = $("#timeBgColor").val();
        var timeStyle = $("#timeStyle").val();
        var ttransparent = $("#ttransparent").val();
        var myDate = new Date();
        var hours = myDate.getHours(); //获取当前小时数(0-23)
        var minutes = myDate.getMinutes(); //获取当前分钟数(0-59)
        var red = parseInt(timeBgColor.substr(1, 2), 16);
        var green = parseInt(timeBgColor.substr(3, 2), 16);
        var blue = parseInt(timeBgColor.substr(5, 2), 16);
        $("#timePreview").css(
          "background-color",
          "rgba(" +
            red +
            "," +
            green +
            ", " +
            blue +
            ", " +
            (1 - ttransparent / 100) +
            ")"
        );
        if (timeStyle == 1) {
          if (hours >= 10 && minutes >= 10) {
            $("#timePreview").html(
              '<font style="font-size:' +
                timeFontSize +
                "px;color:" +
                timeColor +
                ';">' +
                hours +
                ":" +
                minutes +
                "</font>"
            );
          } else if (hours >= 10 && minutes < 10) {
            $("#timePreview").html(
              '<font style="font-size:' +
                timeFontSize +
                "px;color:" +
                timeColor +
                ';">' +
                hours +
                ":0" +
                minutes +
                "</font>"
            );
          } else if (hours < 10 && minutes < 10) {
            $("#timePreview").html(
              '<font style="font-size:' +
                timeFontSize +
                "px;color:" +
                timeColor +
                ';">' +
                hours +
                ":0" +
                minutes +
                "</font>"
            );
          } else if (hours < 10 && minutes >= 10) {
            $("#timePreview").html(
              '<font style="font-size:' +
                timeFontSize +
                "px;color:" +
                timeColor +
                ';">' +
                hours +
                ":" +
                minutes +
                "</font>"
            );
          }
          //$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'</font>');
        } else {
          if (hours + 1 > 12) {
            hours = hours - 12;
            if (hours >= 10 && minutes >= 10) {
              $("#timePreview").html(
                '<font style="font-size:' +
                  timeFontSize +
                  "px;color:" +
                  timeColor +
                  ';">' +
                  hours +
                  ":" +
                  minutes +
                  "PM</font>"
              );
            } else if (hours >= 10 && minutes < 10) {
              $("#timePreview").html(
                '<font style="font-size:' +
                  timeFontSize +
                  "px;color:" +
                  timeColor +
                  ';">' +
                  hours +
                  ":0" +
                  minutes +
                  "PM</font>"
              );
            } else if (hours < 10 && minutes < 10) {
              $("#timePreview").html(
                '<font style="font-size:' +
                  timeFontSize +
                  "px;color:" +
                  timeColor +
                  ';">' +
                  hours +
                  ":0" +
                  minutes +
                  "PM</font>"
              );
            } else if (hours < 10 && minutes >= 10) {
              $("#timePreview").html(
                '<font style="font-size:' +
                  timeFontSize +
                  "px;color:" +
                  timeColor +
                  ';">' +
                  hours +
                  ":" +
                  minutes +
                  "PM</font>"
              );
            }
            //$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'PM</font>');
          } else {
            if (hours >= 10 && minutes >= 10) {
              $("#timePreview").html(
                '<font style="font-size:' +
                  timeFontSize +
                  "px;color:" +
                  timeColor +
                  ';">' +
                  hours +
                  ":" +
                  minutes +
                  "AM</font>"
              );
            } else if (hours >= 10 && minutes < 10) {
              $("#timePreview").html(
                '<font style="font-size:' +
                  timeFontSize +
                  "px;color:" +
                  timeColor +
                  ';">' +
                  hours +
                  ":0" +
                  minutes +
                  "AM</font>"
              );
            } else if (hours < 10 && minutes < 10) {
              $("#timePreview").html(
                '<font style="font-size:' +
                  timeFontSize +
                  "px;color:" +
                  timeColor +
                  ';">' +
                  hours +
                  ":0" +
                  minutes +
                  "AM</font>"
              );
            } else if (hours < 10 && minutes >= 10) {
              $("#timePreview").html(
                '<font style="font-size:' +
                  timeFontSize +
                  "px;color:" +
                  timeColor +
                  ';">' +
                  hours +
                  ":" +
                  minutes +
                  "AM</font>"
              );
            }
            //$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'AM</font>');
          }
        }
      },
    });
  },
  initStaticTextArea: function () {
    //color
    $("#STextcolorSelector").ColorPicker({
      color: $("#static_Color").val(),
      onShow: function (colpkr) {
        $(colpkr).fadeIn(500);
        return false;
      },
      onHide: function (colpkr) {
        $(colpkr).fadeOut(500);
        return false;
      },
      onSubmit: function (hsb, hex, rgb, el) {
        $("#STextcolorSelector div").css("backgroundColor", "#" + hex);
        $("#static_Color").val("#" + hex);
        $("#static_ticker").css("color", "#" + hex);
        $(el).ColorPickerHide();
      },
    });
    $("#STextbgColorSelector").ColorPicker({
      color: $("#static_BgColor").val(),
      onShow: function (colpkr) {
        $(colpkr).fadeIn(500);
        return false;
      },
      onHide: function (colpkr) {
        $(colpkr).fadeOut(500);
        return false;
      },
      onSubmit: function (hsb, hex, rgb, el) {
        $("#STextbgColorSelector div").css("backgroundColor", "#" + hex);
        $("#static_BgColor").val("#" + hex);
        if ($("#transparent").attr("checked")) {
          $("#static_ticker").css("background-color", "#000000");
        } else {
          $("#static_ticker").css("background-color", "#" + hex);
        }

        $(el).ColorPickerHide();
      },
    });
  },
  initWeatherArea: function () {
    //color
    $("#weatherColorSelector").ColorPicker({
      color: $("#weatherColor").val(),
      onShow: function (colpkr) {
        $(colpkr).fadeIn(500);
        return false;
      },
      onHide: function (colpkr) {
        $(colpkr).fadeOut(500);
        return false;
      },
      onSubmit: function (hsb, hex, rgb, el) {
        $("#weatherColorSelector div").css("backgroundColor", "#" + hex);
        $("#weatherColor").val("#" + hex);
        $(el).ColorPickerHide();
      },
    });
    $("#weatherBgColorSelector").ColorPicker({
      color: $("#weatherBgColor").val(),
      onShow: function (colpkr) {
        $(colpkr).fadeIn(500);
        return false;
      },
      onHide: function (colpkr) {
        $(colpkr).fadeOut(500);
        return false;
      },
      onSubmit: function (hsb, hex, rgb, el) {
        $("#weatherBgColorSelector div").css("backgroundColor", "#" + hex);
        $("#weatherBgColor").val("#" + hex);
        $(el).ColorPickerHide();
      },
    });
  },
  changeRssFormat: function (obj, playlistId, areaId) {
    showLoading();
    var req = $.post(
      "/campaign/do_change_rss_format",
      {
        playlist_id: playlistId,
        area_id: areaId,
        format: obj.value,
      },
      function (data) {
        if (data.code == 0) {
          $("#ticker").val(data.rss_content);
        }
        hideLoading();
      },
      "json"
    );
  },
  initEditMedia: function () {
    /*$('#playTime').click(function(event){
         WdatePicker({maxDate:'23:59:59',isShowToday:false,startDate:'00:01:00',dateFmt:'HH:mm:ss',lang:curLang,skin:'whyGreen',quickSel:['00:01:00','00:02:00','00:05:00','00:10:00','00:30:00']})
         });*/
    $("#mediaTable img").click(function (event) {
      var curImg = $(this);
      if (curImg.attr("src").indexOf("_Inactive") > 0) {
        return false;
      }
      var active = $(".active");
      ///images/transfer/Transfer_Mode_07_Active.png
      var src = active.attr("src");

      var pos = src.indexOf("_Active");
      if (pos > 0) {
        active.attr("src", src.substring(0, pos) + src.substr(pos + 7));
      }
      active.removeClass("active");

      src = curImg.attr("src");
      pos = src.indexOf(".");
      curImg.attr("src", src.substring(0, pos) + "_Active" + src.substr(pos));
      curImg.addClass("active");
      $("#transmode").val(curImg.attr("id"));
    });
  },
  initFormDate: function () {
    for (var i = 1; i < 10; i++) {
      var sd = $("#startDate" + i);
      var ed = $("#endDate" + i);
      sd.datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd",
      }).removeClass("gray");

      ed.datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd",
      }).removeClass("gray");
    }

    campaign.bindPlaylistMove();
  },
  bindPlaylistMove: function () {
    var up = $("img.up");
    var down = $("img.down");
    up.unbind("click").bind("click", function () {
      var img = $(this);
      var cid = img.attr("cid");
      var pos = parseInt(img.attr("pos"));
      schedule.form.changePlaylistOrder(cid, pos - 1);
    });
    down.unbind("click").bind("click", function () {
      var img = $(this);
      var cid = img.attr("cid");
      var pos = parseInt(img.attr("pos"));
      schedule.form.changePlaylistOrder(cid, pos + 1);
    });
  },
  saveEditWebpage: function () {
    var itemId = $("#itemId").val();
    var playTime = $("#playTime").val();
    var starttime = $("#startDate").val();
    var endtime = $("#endDate").val();
    var areaId = $("#areaId").val();
    var url = $("#url").val();
    var updateF = $("#updateF").val();
    //var url_type=$('#url_type').attr('checked') ? 1 : 0;

    $.post(
      "/campaign/save_playlist_webpage",
      {
        id: itemId,
        duration: playTime,
        starttime: starttime,
        endtime: endtime,
        url: url,
        updateF: updateF,
      },
      function (data) {
        if (data.code == 0) {
          showFormMsg(data.msg, "success");
          setTimeout(function () {
            tb_remove();
            campaign.loadArea($("#playlistId").val(), areaId);
          }, 1000);
        } else {
          showFormMsg(data.msg, "error");
        }
      },
      "json"
    );
  },
  saveEditMedia: function () {
    //var playTime = $('#playTime').val();
    var transitionMode = $("#transmode").val();
    //var transitionTime = $('#transitionTime').val();
    var itemId = $("#itemId").val();
    var areaId = $("#areaId").val();
    var imgfit = $("#imgfit").val();

    if (transitionMode == undefined) {
      transitionMode = -1;
    }
    /*if(transitionTime == undefined){
         transitionTime = -1;
         }*/
    $.post(
      "/campaign/save_playlist_media",
      {
        // duration: playTime,
        transmode: transitionMode,
        /*transtime : transitionTime,*/
        id: itemId,
        imgfit: imgfit,
      },
      function (data) {
        if (data.code == 0) {
          showFormMsg(data.msg, "success");
          setTimeout(function () {
            tb_remove();
            campaign.loadArea($("#playlistId").val(), areaId);
          }, 1000);
        } else {
          showFormMsg(data.msg, "error");
        }
      },
      "json"
    );
  },
  saveAreaEditMedia: function () {
    var playTime = $("#playTime").val();
    var areaId = $("#areaId").val();
    var type = $("#type").val();
    var transitionMode = $("#transmode").val();
    var playlistId = $("#playlistId").val();

    $.post(
      "/campaign/save_playlist_area_media",
      {
        duration: playTime,
        transmode: transitionMode,
        areaId: areaId,
        type: type,
        playlistId: playlistId,
      },
      function (data) {
        if (data.code == 0) {
          showFormMsg(data.msg, "success");
          setTimeout(function () {
            tb_remove();
            campaign.loadArea($("#playlistId").val(), areaId);
          }, 1000);
        } else {
          showFormMsg(data.msg, "error");
        }
      },
      "json"
    );
  },
  editPlayTime_h: function (obj, areaId, id) {
    //修改网页刷新频率   小时
    var cur = $(obj);
    var playTimeh = cur.val();
    var playTimem = $("#" + id + "_playTimem").val();
    var playTime = playTimeh + ":" + playTimem;
    $.post(
      "/campaign/do_editPlayTime",
      { id: id, areaId: areaId, playTimeh: playTimeh, playTimem: playTimem },
      function (data) {
        if (date.code == 1) {
          showMsg(date.msg, "error");
          setTimeout(function () {
            hideMsg();
          }, 1000);
        }
      },
      "json"
    );
  },
  editPlayTime_m: function (obj, areaId, id) {
    //修改网页刷新频率    分钟
    var cur = $(obj);
    var playTimem = cur.val();
    var playTimeh = $("#" + id + "_playTimeh").val();
    var playTime = playTimeh + ":" + playTimem;
    $.post(
      "/campaign/do_editPlayTime",
      { id: id, areaId: areaId, playTimeh: playTimeh, playTimem: playTimem },
      function (data) {
        if (date.code == 1) {
          showMsg(date.msg, "error");
          setTimeout(function () {
            hideMsg();
          }, 1000);
        }
      },
      "json"
    );
  },
  editStartTime_h: function (obj, areaId, id) {
    //修改开始时间  小时
    var cur = $(obj);
    var startTimeh = cur.val();
    var startTimem = $("#" + id + "_startTimem").val();
    var playlistId = $("#playlistId").val();
    var startTime = startTimeh + ":" + startTimem;
    var endTime =
      $("#" + id + "_endTimeh").val() + ":" + $("#" + id + "_endTimem").val();
    $.post(
      "/campaign/do_editStartTime",
      { id: id, areaId: areaId, startTime: startTime, endTime: endTime },
      function (date) {
        if (date.code == 1) {
          showMsg(date.msg, "error");
          setTimeout(function () {
            hideMsg();
          }, 1000);
        }
      },
      "json"
    );
  },
  editStartTime_m: function (obj, areaId, id) {
    //修改开始时间  分钟
    var cur = $(obj);
    var startTimem = cur.val();
    var startTimeh = $("#" + id + "_startTimeh").val();
    var playlistId = $("#playlistId").val();
    var startTime = startTimeh + ":" + startTimem;
    var endTime =
      $("#" + id + "_endTimeh").val() + ":" + $("#" + id + "_endTimem").val();
    $.post(
      "/campaign/do_editStartTime",
      { id: id, areaId: areaId, startTime: startTime, endTime: endTime },
      function (date) {
        if (date.code == 1) {
          showMsg(date.msg, "error");
          setTimeout(function () {
            hideMsg();
          }, 1000);
        }
      },
      "json"
    );
  },
  editEndTime_h: function (obj, areaId, id) {
    //修改结束时间  小时
    var cur = $(obj);
    var endTimeh = cur.val();
    var endTimem = $("#" + id + "_endTimem").val();
    var playlistId = $("#playlistId").val();
    var endTime = endTimeh + ":" + endTimem;
    var startTime =
      $("#" + id + "_startTimeh").val() +
      ":" +
      $("#" + id + "_startTimem").val();
    $.post(
      "/campaign/do_editEndTime",
      { id: id, areaId: areaId, endTime: endTime, startTime: startTime },
      function (date) {
        if (date.code == 1) {
          showMsg(date.msg, "error");
          setTimeout(function () {
            hideMsg();
          }, 1000);
        }
      },
      "json"
    );
  },
  editEndTime_m: function (obj, areaId, id) {
    //修改结束时间  分钟
    var cur = $(obj);
    var endTimem = cur.val();
    var endTimeh = $("#" + id + "_endTimeh").val();
    var playlistId = $("#playlistId").val();
    var endTime = endTimeh + ":" + endTimem;
    var startTime =
      $("#" + id + "_startTimeh").val() +
      ":" +
      $("#" + id + "_startTimem").val();
    $.post(
      "/campaign/do_editEndTime",
      { id: id, areaId: areaId, endTime: endTime, startTime: startTime },
      function (date) {
        if (date.code == 1) {
          showMsg(date.msg, "error");
          setTimeout(function () {
            hideMsg();
          }, 1000);
        }
      },
      "json"
    );
  },
  editStatus: function (obj, areaId, id) {
    var cur = $(obj);
    var status = $("#status").val();
    var playlistId = $("#playlistId").val();
    $.post(
      "/campaign/do_editStatus",
      { id: id, areaId: areaId },
      function (date) {
        //campaign/.loadArea(playlistId, areaId);
      }
    );
  },
  editReload: function (obj, areaId, id) {
    var cur = $(obj);
    var reload = $("#reload").val();
    var playlistId = $("#playlistId").val();
    $.post(
      "/campaign/do_editReload",
      { id: id, areaId: areaId },
      function (data) {
        //campaign/.loadArea(playlistId, areaId);
      }
    );
  },
  updateRssFlag: function (obj, areaId, id) {
    var val = $(obj).val();
    if (val == "") {
      val = "<<";
    }
    $.post("/campaign/do_updateRssFlag", { val: val, id: id }, function (date) {
      campaign.loadArea($("#playlistId").val(), areaId);
    });
  },
  pList: function () {
    $(".table_webpage_list").removeAttr("style");
    $(".table_webpage_grid").css("display", "none");
  },
  pGrid: function () {
    $(".table_webpage_grid").removeAttr("style");
    $(".table_webpage_list").css("display", "none");
  },
  changeStartDate: function (obj, areaId, id, pId) {
    var cur = $(obj);
    var startDate = $("#startDate" + pId).val();
    $.post(
      "/campaign/do_updateWebpageDate",
      { id: id, areaId: areaId, type: 1, date: startDate },
      function (date) {
        //campaign/.loadArea(playlistId, areaId);
      }
    );
  },
  changeEndDate: function (obj, areaId, id, pId) {
    var cur = $(obj);
    var endDate = $("#endDate" + pId).val();
    $.post(
      "/campaign/do_updateWebpageDate",
      { id: id, areaId: areaId, type: 2, date: endDate },
      function (date) {
        //campaign/.loadArea(playlistId, areaId);
      }
    );
  },
  font_bold: function (obj, pid, areaId) {
    var id = $("#static_textId").val();
    var bold = 0;
    if (obj.checked) {
      bold = 1;
      $("#static_ticker").css("font-weight", "bold");
    } else {
      $("#static_ticker").css("font-weight", "normal");
    }
    $.post(
      "/campaign/do_save_static_text",
      { id: id, pid: pid, area_id: areaId, bold: bold, type: "bold" },
      function (data) {}
    );
  },
  font_italic: function (obj, pid, areaId) {
    var id = $("#static_textId").val();
    var italic = 0;
    if (obj.checked) {
      italic = 1;
      $("#static_ticker").css("font-style", "italic");
    } else {
      $("#static_ticker").css("font-style", "normal");
    }
    $.post(
      "/campaign/do_save_static_text",
      { id: id, pid: pid, area_id: areaId, italic: italic, type: "italic" },
      function (data) {}
    );
  },
  font_underline: function (obj, pid, areaId) {
    var id = $("#static_textId").val();
    var underline = 0;
    if (obj.checked) {
      underline = 1;
      $("#static_ticker").css("text-decoration", "underline");
    } else {
      $("#static_ticker").css("text-decoration", "none");
    }
    $.post(
      "/campaign/do_save_static_text",
      {
        id: id,
        pid: pid,
        area_id: areaId,
        underline: underline,
        type: "underline",
      },
      function (data) {}
    );
  },
  font_size: function (obj, pid, areaId) {
    var id = $("#static_textId").val();
    var size = $("#sfont_size").val();
    $("#static_ticker").css("font-size", size + "px");
    $.post(
      "/campaign/do_save_static_text",
      { id: id, pid: pid, area_id: areaId, font_size: size, type: "font_size" },
      function (data) {}
    );
  },
  font_family: function (obj, pid, areaId) {
    var id = $("#static_textId").val();
    var family = $("#sfont_family").val();
    $("#static_ticker").css("font-family", family);
    $.post(
      "/campaign/do_save_static_text",
      {
        id: id,
        pid: pid,
        area_id: areaId,
        font_family: family,
        type: "font_family",
      },
      function (data) {}
    );
  },
  font_position: function (obj, pid, areaId) {
    var id = $("#static_textId").val();
    var position = $("#sfont_position").val();
    var value = "left";

    if (position == 2) {
      value = "center";
    }
    if (position == 3) {
      value = "right";
    }
    $("#static_ticker").css("text-align", value);
    $.post(
      "/campaign/do_save_static_text",
      {
        id: id,
        pid: pid,
        area_id: areaId,
        font_position: position,
        type: "font_position",
      },
      function (data) {}
    );
  },
  font_direction: function () {
    if ($("#direction").val() == 2) {
      $("#ticker").css("direction", "rtl");
      $("#ticker").css("unicode-bidi", "embed");
    } else {
      $("#ticker").css("direction", "ltr");
    }
  },
  sfont_transparent: function (obj, pid, areaId) {
    var id = $("#static_textId").val();
    var transparent = 1;
    if (obj.checked) {
      transparent = 2;
      $("#static_ticker").css("background-color", "#000000");
    } else {
      $("#static_ticker").css("background-color", $("#static_BgColor").val());
    }
    $.post(
      "/campaign/do_save_static_text",
      {
        id: id,
        pid: pid,
        area_id: areaId,
        transparent: transparent,
        type: "transparent",
      },
      function (data) {}
    );
  },
  wImage: function (obj, id) {
    if ($("#weatherStyle").val() == 5) {
      $("#wImage").html('<img src="/images/wstyle5.jpg" />');
    } else {
      $("#wImage").html('<img src="/images/wstyle4.jpg" />');
    }
  },
  dateChange: function (obj, id) {
    var dateFontSize = $("#dateFontSize").val();
    var dateColor = $("#dateColor").val();
    var dateBgColor = $("#dateBgColor").val();
    var dataStyle = $("#dataStyle").val();
    var dtransparent = $("#dtransparent").val();
    var myDate = new Date();
    var year = myDate.getYear(); //获取当前年份(2位)
    var fullyear = myDate.getFullYear(); //获取完整的年份(4位,1970-????)
    var month = myDate.getMonth() + 1; //获取当前月份(0-11,0代表1月)
    var date = myDate.getDate(); //获取当前日(1-31)
    var day = myDate.getDay(); //天
    var week;
    var htmlDate;
    switch (day) {
      case 0:
        week = "Sunday";
        break;
      case 1:
        week = "Monday";
        break;
      case 2:
        week = "Tuesday";
        break;
      case 3:
        week = "Wednesday";
        break;
      case 4:
        week = "Thursday";
        break;
      case 5:
        week = "Friday";
        break;
      case 6:
        week = "Saturday";
        break;
    }
    var red = parseInt(dateBgColor.substr(1, 2), 16);
    var green = parseInt(dateBgColor.substr(3, 2), 16);
    var blue = parseInt(dateBgColor.substr(5, 2), 16);
    $("#datePreview").css(
      "background-color",
      "rgba(" +
        red +
        "," +
        green +
        ", " +
        blue +
        ", " +
        (1 - dtransparent / 100) +
        ")"
    );
    $(".countdown").hide();
    $(".time30").show();
    $("#dpreview").show();
    $("#dlang").show();
    if (dataStyle == 1) {
      $("#datePreview").html(
        '<font style="font-size:' +
          dateFontSize +
          "px;color:" +
          dateColor +
          ';">' +
          month +
          "/" +
          date +
          "/" +
          fullyear +
          "&nbsp;" +
          week +
          "</font>"
      );
    }
    if (dataStyle == 2) {
      $("#datePreview").html(
        '<font style="font-size:' +
          dateFontSize +
          "px;color:" +
          dateColor +
          ';">' +
          date +
          "/" +
          month +
          "/" +
          fullyear +
          "&nbsp;" +
          week +
          "</font>"
      );
    }
    if (dataStyle == 3) {
      $("#datePreview").html(
        '<font style="font-size:' +
          dateFontSize +
          "px;color:" +
          dateColor +
          ';">' +
          fullyear +
          "/" +
          month +
          "/" +
          date +
          "&nbsp;" +
          week +
          "</font>"
      );
    }
    if (dataStyle == 4) {
      $("#datePreview").html(
        '<font style="font-size:' +
          dateFontSize +
          "px;color:" +
          dateColor +
          ';">' +
          month +
          "/" +
          date +
          "/" +
          fullyear +
          "</font>"
      );
    }
    if (dataStyle == 5) {
      $("#datePreview").html(
        '<font style="font-size:' +
          dateFontSize +
          "px;color:" +
          dateColor +
          ';">' +
          date +
          "/" +
          month +
          "/" +
          fullyear +
          "</font>"
      );
    }
    if (dataStyle == 6) {
      $("#datePreview").html(
        '<font style="font-size:' +
          dateFontSize +
          "px;color:" +
          dateColor +
          ';">' +
          fullyear +
          "/" +
          month +
          "/" +
          date +
          "</font>"
      );
    }
    if (dataStyle == 9) {
      $(".countdown").show();
      $(".time30").hide();
      $("#dpreview").hide();
      $("#dlang").hide();
    }
  },
  timeChange: function () {
    var timeFontSize = $("#timeFontSize").val();
    var timeColor = $("#timeColor").val();
    var timeBgColor = $("#timeBgColor").val();
    var timeStyle = $("#timeStyle").val();
    var ttransparent = $("#ttransparent").val();
    var myDate = new Date();
    var hours = myDate.getHours(); //获取当前小时数(0-23)
    var minutes = myDate.getMinutes(); //获取当前分钟数(0-59)
    var red = parseInt(timeBgColor.substr(1, 2), 16);
    var green = parseInt(timeBgColor.substr(3, 2), 16);
    var blue = parseInt(timeBgColor.substr(5, 2), 16);
    $("#timePreview").css(
      "background-color",
      "rgba(" +
        red +
        "," +
        green +
        ", " +
        blue +
        ", " +
        (1 - ttransparent / 100) +
        ")"
    );
    if (timeStyle == 1) {
      if (hours >= 10 && minutes >= 10) {
        $("#timePreview").html(
          '<font style="font-size:' +
            timeFontSize +
            "px;color:" +
            timeColor +
            ';">' +
            hours +
            ":" +
            minutes +
            "</font>"
        );
      } else if (hours >= 10 && minutes < 10) {
        $("#timePreview").html(
          '<font style="font-size:' +
            timeFontSize +
            "px;color:" +
            timeColor +
            ';">' +
            hours +
            ":0" +
            minutes +
            "</font>"
        );
      } else if (hours < 10 && minutes < 10) {
        $("#timePreview").html(
          '<font style="font-size:' +
            timeFontSize +
            "px;color:" +
            timeColor +
            ';">' +
            hours +
            ":0" +
            minutes +
            "</font>"
        );
      } else if (hours < 10 && minutes >= 10) {
        $("#timePreview").html(
          '<font style="font-size:' +
            timeFontSize +
            "px;color:" +
            timeColor +
            ';">' +
            hours +
            ":" +
            minutes +
            "</font>"
        );
      }
    } else {
      if (hours + 1 > 12) {
        hours = hours - 12;
        if (hours >= 10 && minutes >= 10) {
          $("#timePreview").html(
            '<font style="font-size:' +
              timeFontSize +
              "px;color:" +
              timeColor +
              ';">' +
              hours +
              ":" +
              minutes +
              "PM</font>"
          );
        } else if (hours >= 10 && minutes < 10) {
          $("#timePreview").html(
            '<font style="font-size:' +
              timeFontSize +
              "px;color:" +
              timeColor +
              ';">' +
              hours +
              ":0" +
              minutes +
              "PM</font>"
          );
        } else if (hours < 10 && minutes < 10) {
          $("#timePreview").html(
            '<font style="font-size:' +
              timeFontSize +
              "px;color:" +
              timeColor +
              ';">' +
              hours +
              ":0" +
              minutes +
              "PM</font>"
          );
        } else if (hours < 10 && minutes >= 10) {
          $("#timePreview").html(
            '<font style="font-size:' +
              timeFontSize +
              "px;color:" +
              timeColor +
              ';">' +
              hours +
              ":" +
              minutes +
              "PM</font>"
          );
        }
      } else {
        if (hours >= 10 && minutes >= 10) {
          $("#timePreview").html(
            '<font style="font-size:' +
              timeFontSize +
              "px;color:" +
              timeColor +
              ';">' +
              hours +
              ":" +
              minutes +
              "AM</font>"
          );
        } else if (hours >= 10 && minutes < 10) {
          $("#timePreview").html(
            '<font style="font-size:' +
              timeFontSize +
              "px;color:" +
              timeColor +
              ';">' +
              hours +
              ":0" +
              minutes +
              "AM</font>"
          );
        } else if (hours < 10 && minutes < 10) {
          $("#timePreview").html(
            '<font style="font-size:' +
              timeFontSize +
              "px;color:" +
              timeColor +
              ';">' +
              hours +
              ":0" +
              minutes +
              "AM</font>"
          );
        } else if (hours < 10 && minutes >= 10) {
          $("#timePreview").html(
            '<font style="font-size:' +
              timeFontSize +
              "px;color:" +
              timeColor +
              ';">' +
              hours +
              ":" +
              minutes +
              "AM</font>"
          );
        }
      }
    }
  },
  ApplyLineBreaks: function (strTextAreaId) {
    //textarea字符串根据前台分行格式截取
    var oTextarea = document.getElementById(strTextAreaId);
    if (oTextarea.wrap) {
      oTextarea.setAttribute("wrap", "off");
    } else {
      oTextarea.setAttribute("wrap", "off");
      var newArea = oTextarea.cloneNode(true);
      newArea.value = oTextarea.value;
      oTextarea.parentNode.replaceChild(newArea, oTextarea);
      oTextarea = newArea;
    }
    var strRawValue = oTextarea.value;
    oTextarea.value = "";
    var nEmptyWidth = oTextarea.scrollWidth;
    var nLastWrappingIndex = -1;
    for (var i = 0; i < strRawValue.length; i++) {
      var curChar = strRawValue.charAt(i);
      if (curChar == " " || curChar == "-" || curChar == "+")
        nLastWrappingIndex = i;
      oTextarea.value += curChar;
      if (oTextarea.scrollWidth > nEmptyWidth) {
        var buffer = "";
        if (nLastWrappingIndex >= 0) {
          for (var j = nLastWrappingIndex + 1; j < i; j++)
            buffer += strRawValue.charAt(j);
          nLastWrappingIndex = -1;
        }
        buffer += curChar;
        oTextarea.value = oTextarea.value.substr(
          0,
          oTextarea.value.length - buffer.length
        );
        oTextarea.value += "\n" + buffer;
      }
    }
    oTextarea.setAttribute("wrap", "");
    return oTextarea.value.replace(new RegExp("\\n", "g"), "<br/>");
  },
  doSavePrice: function () {
    var one = $("#part_one").val();
    var two = $("#part_two").val();
    var three = $("#part_three").val();
    var four = $("#part_four").val();
    var id = $("#id").val();
    var t_id = $("#template_id").val();
    $.post(
      "/campaign/do_save_price",
      { id: id, one: one, two: two, three: three, four: four, t_id: t_id },
      function (data) {}
    );
    $.post(
      "/campaign/do_publish",
      { playlist_id: id, rotate: 0, ids: -1 },
      function (data) {
        if (data.code == 0) {
          tb_remove();
          window.location.href = "/campaign/price_entry_playlist";
        } else {
          //showMsg(data.msg, 'error');
        }
      },
      "json"
    );
  },
  uploadAreaMedia: function (playlistId, areaId, areaType) {
    var req =
      "/media/upload_medias?playlist_id=" +
      playlistId +
      "&area_id=" +
      areaId +
      "&area_type=" +
      areaType +
      "&width=800&height=530";

    tb_show(" ", req, "");
  },

  calculate: function () {
    var criteria = $("#criteria-select-options").val();

    var players = $("#players-select-options").val();

    if (!criteria && !players) {
      $("#total_free").val("");
      $("#least_common").val("");
      $("#ava_usage").val("");
      $("#player_sel").val(0);
      return;
    }
    var json = this._createPlaylistData();

    $.post(
      "/campaign/do_calculate",
      json,
      function (data) {
        $("#player_sel").val(data.player_num);
        if (typeof data.OverBooking != "undefined") {
          if (data.OverBooking == 1) {
            alertify.alert(data.ob_msg);
          }
        }

        if (typeof data.ava_used !== "undefined") {
          $("#ava_usage").val(data.ava_used);
        } else {
          $("#ava_usage").val("");
        }
        if (typeof data.ava_used !== "undefined") {
          $("#total_free").val(data.ob_cnt);
        } else {
          $("#total_free").val("");
        }

        if (typeof data.ava_used !== "undefined") {
          $("#least_common").val(data.lease_times);
        } else {
          $("#least_common").val("");
        }

        if (typeof data.total_times != "undefined") {
          $("#total_times").val(data.total_times);
        } else {
          $("#total_times").val("");
        }
        if (typeof data.cost != "undefined") {
          $("#cost").val(data.cost);
        } else {
          $("#cost").val("");
        }
        if (typeof data.players != "undefined") {
          $("#selected_players").html(data.players);
        } else {
          $("#selected_players").html("");
        }
      },
      "json"
    );
  },
  selchange: function (s) {
    var nsel = s.options[s.options.selectedIndex].value;
    if (nsel == 1 || nsel == 2 || nsel == 4) {
      $("#groupcheckbox").show();
    } else {
      $("#groupcheckbox").hide();
    }
    if (nsel == 3 || nsel == 6) {
      $(".playcount").hide();
      $(".time-selection").hide();
      $("#tag_options").hide();
      $("#tags").hide();
      $(".weatherplaceholder").show();
    } else {
      $(".weatherplaceholder").hide();
      document.getElementById("alldayFlag").disabled = false;
      $(".time-selection").show();
      if (document.getElementById("playcountid").value == 0) {
        $("#playcountid").val(1);
      }

      if ($(".playcount").is(":hidden")) {
        $(".playcount").show();
        document.getElementById("playweightid").style.display = "inline";
      }

      if (nsel == 4) {
        $("#tag_options").hide();
      } else {
        $("#tag_options").show();
      }
      $("#tags").show();
    }
    if (document.getElementById("alldayFlag").checked == false) {
      if (s.selectedIndex == 0) {
        $(".time-input").removeAttr("disabled");
      } else {
        $("#stopTM").val("0");
        $("#startTM").val("0");

        $(".min-input").attr("disabled", true);
      }
    }
  },
  typechange: function (s) {
    if (s == 0) {
      document.getElementById("playcountid").style.display = "inline";
      document.getElementById("playweightid").style.display = "none";
      document.getElementById("playtotalid").style.display = "none";
      document.getElementById("xslotid").style.display = "none";
    } else if (s == 1) {
      document.getElementById("playcountid").style.display = "none";
      document.getElementById("playweightid").style.display = "inline";
      document.getElementById("playtotalid").style.display = "none";
      document.getElementById("xslotid").style.display = "none";
    } else if (s == 2) {
      document.getElementById("playcountid").style.display = "none";
      document.getElementById("playweightid").style.display = "none";
      document.getElementById("playtotalid").style.display = "inline";
      document.getElementById("xslotid").style.display = "none";
    } else if (s == 9) {
      document.getElementById("playcountid").style.display = "none";
      document.getElementById("playweightid").style.display = "none";
      document.getElementById("playtotalid").style.display = "none";
      document.getElementById("xslotid").style.display = "inline";
    }
  },

  minchange: function (s) {
    if (s.selectedIndex == 24) {
      $("#stopTM").attr("disabled", true);
      $("#stopTM").val("0");
    } else {
      if (document.getElementById("priority-options").value == "0")
        $("#stopTM").attr("disabled", false);
    }
  },

  exclude_player: function () {
    var ids = new Array();
    $('input:checkbox[name="obids"]').each(function () {
      if (this.checked) {
        ids.push(this.value);
      }
    });

    $("#exclude_players_options").val(ids).trigger("chosen:updated");
  },
};
