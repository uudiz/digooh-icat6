jQuery.validator.setDefaults({
  onfocusout: function (e) {
    this.element(e);
  },
  onkeyup: false,

  highlight: function (element) {
    jQuery(element).addClass("is-invalid");
  },
  unhighlight: function (element) {
    jQuery(element).removeClass("is-invalid");
    //console.log(element.getAttribute('type'));
    //jQuery(element).addClass('is-valid');
  },

  errorElement: "div",
  errorClass: "invalid-feedback",
  errorPlacement: function (error, element) {
    if (element.parent(".input-group-prepend").length) {
      $(element).siblings(".invalid-feedback").append(error);
      //error.insertAfter(element.parent());
    } else {
      error.insertAfter(element);
    }
  },
});

jQuery.validator.addMethod(
  "greaterThan",
  function (value, element, params) {
    var compareValue = $("[name=" + params + "]").val();

    if (!/Invalid|NaN/.test(new Date(value))) {
      return new Date(value) > new Date(compareValue);
    }

    return (
      (isNaN(value) && isNaN($(params).val())) ||
      Number(value) > Number($(params).val())
    );
  },
  " "
);

jQuery.validator.addMethod(
  "greaterOrEqualThan",
  function (value, element, params) {
    var compareValue = $("[name=" + params + "]").val();

    if (!/Invalid|NaN/.test(new Date(value))) {
      return new Date(value) >= new Date(compareValue);
    }

    return (
      (isNaN(value) && isNaN($(params).val())) ||
      Number(value) >= Number($(params).val())
    );
  },
  " "
);

$(document).ready(function () {
  $("#dataForm")
    .submit(function (e) {
      e.preventDefault();
    })
    .validate({
      lang: localStorage.getItem("language") == "germany" ? "de" : "en",
      submitHandler: function (form, e) {
        e.preventDefault();
        const form_url = $("form#dataForm").attr("action");
        if (!form_url.length) {
          alert("please check action url");
          return;
        }
        var resource = "/" + form_url.split("/")[1];

        var params = new Object();
        $("form#dataForm :input[name]").each(function () {
          var inputs = $(this); // A jquery object of the input
          inputs.each(function () {
            if (this.type === "checkbox") {
              params[`${this.name}`] = $(this).is(":checked") ? 1 : 0;
            } else if ($(this).val().length !== 0) {
              params[`${this.name}`] = $(this).val();
            }
          });
        });

        $.post(
          form_url,
          params,
          function (data) {
            if (data.code != 0) {
              toastr.error(data.msg);
            } else {
              localStorage.setItem(
                "Status",
                JSON.stringify({
                  type: "success",
                  message: data.msg,
                })
              );
              window.location.href = resource;
            }
          },
          "json"
        );
        return false;
      },
    });
});
