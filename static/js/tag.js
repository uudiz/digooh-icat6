/**
 * @author User
 */

/**
 * 用户表单处理
 */
var tag = {
  formId: "tag-form",

  doSave: function () {
    var id = $("#id").val();
    var name = $("#name").val();
    if (id == undefined) {
      id = 0;
    }
    /*
		if (name.indexOf("&") >= 0 || name.indexOf("<") >= 0 || name.indexOf(">") >= 0 || name.indexOf("'") >= 0 || name.indexOf("\\") >= 0 || name.indexOf("%") >= 0) {
			showFormMsg("Special symbols (& < > ' \\ %) are not allowed in the tag name.", 'error');
            return false;
        }
		*/
    $.post(
      "/tag/do_save",
      {
        name: name,
        descr: $("#descr").val(),
        id: id,
        players: $("#players-select-options").val(),
      },
      function (data) {
        if (data.code != 0) {
          $("#validateTips")
            .html("<div>" + data.msg + "</div>")
            .addClass("error");
        } else {
          $("#validateTips")
            .html("<div>" + data.msg + "</div>")
            .addClass("success");

          setTimeout(function () {
            //remove
            tb_remove();
            //refresh
            tag.refresh();
          }, 100);
        }
      },
      "json"
    );
  },
  init: function () {
    //this.form = $('#user-form');
  },
  destory: function () {
    var uf = $("#" + this.formId);
    uf.parent().remove();
    uf.remove();
  },
  destoryFormDialog: function () {
    $("#" + this.formId).dialog("destory");
    this.destory();
  },
  refresh: function () {
    showLoading();
    var name = $("#filter").val();
    $.get("/tag/refresh/?name=" + name, function (data) {
      $("#layoutContent").html(data);
      hideLoading();
      //reinit this box~
      tb_init("td > a.thickbox");
    });
  },
  page: function (curpage, orderItem, order) {
    //.showLoading();
    //window.location.href='/tag/index/'+curpage+"/"+orderItem+"/"+order;
    orderItem = orderItem || "name";
    order = order || "asc";

    showLoading();
    var name = $("#filter").val();
    $.get(
      "/tag/refresh/" +
        curpage +
        "/" +
        orderItem +
        "/" +
        order +
        "?name=" +
        name,
      function (data) {
        $("#layoutContent").html(data);
        hideLoading();
        //reinit this box~
        tb_init("td > a.thickbox");
      }
    );
  },
  remove: function (id, msg) {
    if (confirm(msg)) {
      var req = {
        id: id,
      };
      $.post(
        "/tag/do_delete",
        req,
        function (data) {
          if (data.code == 0) {
            showMsg(data.msg, "success");
            tag.refresh();
            setTimeout(hideMsg, 1000);
          } else {
            showMsg(data.msg, "error");
          }
        },
        "json"
      );
    }
  },
};
