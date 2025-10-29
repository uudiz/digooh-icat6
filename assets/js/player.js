function detailFormatter(index, row) {
  var html = "";
  $.ajaxSettings.async = false;
  $.get(
    "/player/detail?id=" + row.id + "&t=" + new Date().getTime(),
    function (data) {
      html = data;
    }
  );
  $.ajaxSettings.async = true;
  return html;
}

function statusFormatter(value, row) {
  var color = "status-blue";
  if (value <= 1 || value == 10) {
    color = "status-red";
  } else if (value == 2 || value == 6) {
    color = "status-green";
  } else if (value == 4 || value == 5) {
    color = "status-blue";
  } else if (value == 12 || value == 127) {
    color = "status-yellow";
  }

  var status_str = `<span class="status-indicator status-indicator-animated ${color}" title=${row.status_toolbar} data-bs-toggle="tooltip" data-bs-placement="top">
    <span class="status-indicator-circle"></span>
    <span class="status-indicator-circle"></span>
    <span class="status-indicator-circle"></span>
    </span>`;

  return status_str;
}

function signalFormatter(value, row) {
  var icon = "";
  var color = "text-success";
  if (row.humidity == 3 || row.humidity == 4) {
    if (value <= 1) {
      color = "text-danger";
    }
    icon = "bi-reception-" + value;
  } else if (row.humidity == 2) {
    if (value < 3) {
      if (value == 2) {
        icon = "bi-wifi-2";
      } else {
        icon = "bi-wifi-1";
        color = "text-danger";
      }
    } else {
      icon = "bi-wifi";
    }
  } else {
    return "";
  }
  return (
    '<i class="' +
    color +
    " bi " +
    icon +
    '" data-bs-toggle="tooltip" data-bs-placement="top" title="' +
    value +
    '"></i>'
  );
}

function toggleExpand(index) {
  $("#table").bootstrapTable("toggleDetailView", index);
}

function exportPlayers() {
  var params = queryParams({});
  var req = "";

  console.log(params);
  if (params.search && params.search.length > 0) {
    req += "&filter=" + params.search;
  }

  if (params.online) {
    req += "&online=" + params.online;
  }

  if (params.criteria > "0") {
    req += "&criterion_id=" + params.criteria;
  }

  window.location.href =
    "/player/export_player/" + "?t=" + new Date().getTime() + req;
}

var import_btn = $("#import_excel");
if (import_btn.length) {
  import_btn.on("change", function () {
    //获取到选中的文件
    var input = document.querySelector("#import_excel");
    var file = input.files[0];

    if (!file) {
      return;
    }
    var formdata = new FormData();
    formdata.append("file", file);

    input.value = "";
    $.ajax({
      url: "/player/import_players",
      type: "post",
      processData: false,
      contentType: false,
      data: formdata,
      dataType: "json",

      success: function (res) {
        if (res.code == 0) {
          $("#table").bootstrapTable("refresh");
          toastr.success(res.msg);
        } else {
          toastr.error(res.msg);
        }
      },
      error: function (err) {
        console.log(err);
        toastr.error(err);
      },
    });
  });
}
