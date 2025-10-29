var $table = $('#table');

var import_btn = $('#import_excel');
if (import_btn.length) {
    import_btn.on("change", function() {
        //获取到选中的文件
        var input = document.querySelector("#import_excel");
        var file = input.files[0];

        if (!file) {
            return;
        }
        var formdata = new FormData();
        formdata.append("file", file);

        input.value = '';
        $.ajax({
            url: "/criteria/do_upload",
            type: "post",
            processData: false,
            contentType: false,
            data: formdata,
            dataType: 'json',

            success: function(res) {
                if (res.code == 0) {
                    toastr.success(res.msg);
                    doSearch();
                } else {
                    toastr.error(res.msg);
                }


            },

        })
    });
}