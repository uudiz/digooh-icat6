function fileSizeSI(a, b, c, d, e) {
  return (
    ((b = Math),
    (c = b.log),
    (d = 1e3),
    (e = (c(a) / c(d)) | 0),
    a / b.pow(d, e)).toFixed(2) +
    " " +
    (e ? "kMGTPEZY"[--e] + "B" : "Bytes")
  );
}

function showSpinner() {
  $("div.spanner").addClass("show");
  $("div.overlay").addClass("show");
}

function hideSpinner() {
  $("div.spanner").removeClass("show");
  $("div.overlay").removeClass("show");
}

function doSearch() {
  var table = $("#table");
  if (table.length) {
    table.bootstrapTable("uncheckAll");
    table.bootstrapTable("refresh");
  }
}

function remove_resource(resource, id) {
  $("#delete_confirm")
    .modal("show")
    .off("click")
    .on("click", "#delete", function (e) {
      $.post(
        `/${resource}/do_delete`,
        {
          id: id,
        },
        function (data) {
          if (data.code == 0) {
            toastr.success(data.msg);
            doSearch();
          } else {
            toastr.error(data.msg);
          }
        },
        "json"
      );
    });
}

function loadingTemplate(message) {
  return '<div class="spinner-border" role="status"></div>';
}

function getLocal() {
  return localStorage.getItem("language") == "germany" ? "de-DE" : "en-US";
}

$.extend($.fn.bootstrapTable.defaults, {
  pagination: true,
  sidePagination: "server",
  method: "post",
  contentType: "application/x-www-form-urlencoded",
  dataType: "json",
  search: false,
  pageSize: "15",
  pageList: "[15, 30, 50, 100]",
  detailViewIcon: false,
  loadingTemplate: "loadingTemplate",
  queryParams: "queryParams",
  buttonsClass: "outline-primary",
  onAll: function (name, args) {
    if (
      name == "check.bs.table" ||
      name == "check-all.bs.table" ||
      name == "uncheck.bs.table" ||
      name == "uncheck-all.bs.table" ||
      name == "load-success.bs.table"
    ) {
      var table = $("#table");
      if (table.length) {
        if (table.bootstrapTable("getSelections").length) {
          $("#batch_operation").show();
          $("#toolbar").hide();
        } else {
          $("#batch_operation").hide();
          $("#toolbar").show();
        }
      }
    }
  },
  onPostBody: function () {
    $(":checkbox").each(function () {
      if (!$(this).hasClass("form-check-input")) {
        $(this).addClass("form-check-input m-0 align-middle");
      }
    });

    var tooltipTriggerList = [].slice.call(
      document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl, {
        boundary: document.body, // or document.querySelector('#boundary')
      });
    });
  },
});

function queryParams(params) {
  $("form#toolbar :input").each(function () {
    var inputs = $(this); // A jquery object of the input
    inputs.each(function () {
      if (this.type === "checkbox") {
        params[`${this.name}`] = $(this).is(":checked") ? 1 : 0;
      } else if ($(this).val().length !== 0) {
        params[`${this.name}`] = $(this).val();
      }
    });
  });
  return params;
}

var entityMap = {
  "&": "&amp;",
  "<": "&lt;",
  ">": "&gt;",
  '"': "&quot;",
  "'": "&#39;",
  "/": "&#x2F;",
  "`": "&#x60;",
  "=": "&#x3D;",
};

function escapeHtml(string) {
  return String(string).replace(/[&<>"'`=\/]/g, function (s) {
    return entityMap[s];
  });
}

function debounce(func, timeout = 300) {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => {
      func.apply(this, args);
    }, timeout);
  };
}

$(function () {
  var toolbar = $("form#toolbar");
  if (toolbar.length) {
    toolbar.off("change").on("change", function () {
      doSearch();
    });
  }

  //$("input#search").off("input").on("input", debounce(doSearch));

  $("form#toolbar :input")
    .off("keypress keydown keyup")
    .on("keypress keydown keyup", function (e) {
      if (e.keyCode == 13) {
        e.target.blur();
        e.preventDefault();
        return false;
      }
    });

  toastr.options.positionClass = "toast-bottom-right";
  toastr.options.showDuration = 60;
  toastr.options.closeButton = true;
  //get it if Status key found

  const status = localStorage.getItem("Status");
  if (status) {
    const alertMsg = JSON.parse(status);

    if (alertMsg.type == "success") {
      toastr.success(alertMsg.message);
    } else if (alertMsg.type == "error") {
      toastr.error(alertMsg.message);
    } else if (alertMsg.type == "Warning") {
      toastr.Warning(alertMsg.message);
    } else {
      toastr.info(alertMsg.message);
    }

    localStorage.clear();
  }

  $(".select2").select2({
    theme: "bootstrap-5",
    width: "100%",
  });
});
