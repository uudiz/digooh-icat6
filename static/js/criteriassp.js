/**
 * @author User
 */

/**
 * �û�������
 */
var criteria = {
  formId: "criteria-form",

  doSave: function () {
    var id = $("#id").val();
    var name = $("#name").val();
    if (id == undefined) {
      id = 0;
    }

    $.post(
      "/criteriaSSP/do_save",
      {
        type: $("#categories_type").val(),
        name: name,
        descr: $("#descr").val(),
        id: id,
        code: $("#code").val(),
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
            criteria.refresh();
          }, 100);
        }
      },
      "json"
    );
  },
  init: function () {},
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
    var type = $("#filter_categories_type").val();

    $.get(
      "/criteriaSSP/refresh/?name=" + name + "&type=" + type,
      function (data) {
        $("#layoutContent").html(data);
        hideLoading();
        //reinit this box~
        tb_init("td > a.thickbox");
      }
    );
  },
  page: function (curpage, orderItem, order) {
    showLoading();
    orderItem = orderItem || "name";
    order = order || "asc";

    var name = $("#filter").val();
    var type = $("#filter_categories_type").val();
    $.get(
      "/criteriaSSP/refresh/" +
        curpage +
        "/" +
        orderItem +
        "/" +
        order +
        "?name=" +
        name +
        "?type=" +
        type,

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
        "/criteriaSSP/do_delete",
        req,
        function (data) {
          if (data.code == 0) {
            showMsg(data.msg, "success");
            criteria.refresh();
            setTimeout(hideMsg, 1000);
            if (data.needPublish == 1) {
              alertify.alert(data.repubmsg);
            }
          } else {
            showMsg(data.msg, "error");
          }
        },
        "json"
      );
    }
  },
};
