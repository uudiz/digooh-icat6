/**
 * @author User
 */

/**
 * 公司表单处理
 */
var c = {
    saving: false,
    msg: Array(),
    uploader: {
        changeEvent: function() {
            c.resetForm(0);
            $('form').submit();
        },
        change: function() {
            $('#fileUploadInput').change(this.changeEvent);
        },
        callback: function(jsonStr) {
            var image = toJsonObj(jsonStr);
            if (image != null) {
                if (image.code == 0) {
                    $('#descr').html(jsonStr);
                    var bar = $('#fileUploadBar');
                    $('#fileUploadInput').val($('#fileUploadInput2').val());
                    bar.removeClass();
                    //设置图标
                    $('#logo').val(image.image_name);

                    c.resetForm(1);

                    $(".logo-area").html('<div><img width="80" height="80" src="/upload/tmp/' +
                        image.image_name +
                        '" alt=""/></div><a href="javascript:void(0);" onclick="c.uploader.deletePic(\'' +
                        image.image_name +
                        '\');" title="' +
                        image.delete_close +
                        '" class="delete" >' +
                        image.delete_close +
                        '</a>');
                } else {
                    showErrMsg(image.msg);
                }
            } else {
                c.showMsg('Request error:' + jsonStr);
            }
        },
        deletePic: function(imageName) {
            $.get('/company/delete_img/' + imageName,
                function(data) {
                    result = toJsonObj(data);
                    if (result.code == 0) {
                        //succ
                        $(".logo-area").html('');
                        var bar = $('#fileUploadBar');
                        bar.html('<input id="fileUploadInput" type="file" name="file" /><input id="fileUploadInput2" type="file" style="display:none;" />');
                        bar.addClass('loadbutton');
                        c.uploader.change();
                        $('#logo').val('');
                    }
                }
            );
        }
    },
    resetForm: function(flag) {
        var cf = $("#cf");
        //准备上传
        if (flag == 0) {
            cf.attr('action', '/company/do_upload_logo');
            cf.attr('target', 'logo_upload_frame');
            cf.attr('enctype', 'multipart/form-data');
        } else if (flag == 1) { //上传成功后
            cf.attr('action', '/company/do_save');
            cf.attr('target', '_self');
            cf.removeAttr('enctype');
        }
    },
    showMsg: function(msg) {
        var d = $("#dialog-message");
        d.html('<p>' + msg + '</p>');
        d.dialog({
            modal: true,
            buttons: {
                Ok: function() {
                    $(this).dialog("close");
                }
            }
        });
    },
    addDstCalendar: function() {
        if ($('#dst').is(":checked")) {
            $("#dst_start").datepicker({
                showOn: "button",
                buttonImage: "images/datePicker.gif",
                buttonImageOnly: true,
                changeMonth: true,
                changeYear: true,
                dateFormat: 'yy-mm-dd'
            });
            $("#dst_end").datepicker({
                showOn: "button",
                buttonImage: "images/datePicker.gif",
                buttonImageOnly: true,
                changeMonth: true,
                changeYear: true,
                dateFormat: 'yy-mm-dd'
            });
        }
    },
    removeDstCalendar: function() {
        $("#dst_start").datepicker("destroy");
        $("#dst_end").datepicker("destroy");
    },
    addCalendar: function() {
        $("#start_date").datepicker({
            showOn: "button",
            buttonImage: "images/datePicker.gif",
            buttonImageOnly: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd'
        });
        $("#stop_date").datepicker({
            showOn: "button",
            buttonImage: "images/datePicker.gif",
            buttonImageOnly: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd'
        });
    },
    removeCalendar: function() {
        $("#start_date").datepicker("destroy");
        $("#stop_date").datepicker("destroy");
    },
    dstChange: function() {
        $("#dst").change(function() {
            if ($('#dst').is(":checked")) {
                c.addDstCalendar();
            } else {
                c.removeDstCalendar();
            }
        });
    },
    loadMsg: function() {

    },
    doSave: function(obj) {
        if (c.saving) {
            return;
        }
        var cur = $(obj);
        cur.addClass('btn-02');
        cur.removeClass('btn-01');
        c.saving = true;

        //var enableDst = $('#dst').attr('checked'); //delete
        var id = $('#id').val();
        if (id == undefined) {
            id = 0;
        }
        var company = {
            name: $('#name').val(),
            descr: $('#descr').val(),
            max_user: $('#max_user').val(),
            total_disk: $('#total_disk').val(),
            start_date: $('#start_date').val(),
            stop_date: $('#stop_date').val(),
            device_setup: $('#device_setup').val(),
            touch_function: $('#touch_function').val(),
            auto_dst: $('#auto_dst').val(),
            price_entry: $('#price_entry').val(),
            auto_publish: $('#auto_publish').val(),
            logo: '' + $('#custom_logo').val(),
            com_interval: $("#com_imterval").val(),
            xslot: $("#xslot").val(),
            play_time: $("#playtime").val(),
            cost_default: $("#cost_default").val(),
            cost1: $("#cost1").val(),
            cost1_condition: $("#cost_condition1").val(),
            cost2: $("#cost2").val(),
            cost2_condition: $("#cost_condition2").val(),
            theme_color: $("#colorpicker").val(),
            cust_filed1: $('#cust_filed1').val(),
            cust_filed2: $('#cust_filed2').val(),
            id: id
        };

        var pid = $('#parent_id').val();

        if (pid > 0) {
            company['parent_id'] = pid;
            company['criterion_id'] = $('#criteria-select-options').val();
            company['players'] = $('#players-select-options').val();
            company['quota'] = $('#quota').val();
            company['player_quota'] = $('#player_quota').val();
            company['share_block'] = $('#shareblock').is(':checked') ? 1 : 0;
            company['flag'] = $('#active').is(':checked') ? 0 : 1;

            var folders = new Array();
            var treeObj = $.fn.zTree.getZTreeObj("treeFolder");
            if (treeObj) {
                var nodes = treeObj.getCheckedNodes(true);
                for (i = 0; i < nodes.length; i++) {
                    folders.push(nodes[i].id);
                }
            }
            company['folders'] = folders;
        }


        $.post('/company/do_save', company,
            function(data) {
                if (data.code != 0) {
                    //$('#validateTips').html('<div>'+data.msg+'</div>').addClass('error');
                    showFormMsg(data.msg, 'error');

                } else {
                    //$('#validateTips').html('<div>'+data.msg+'</div>').addClass('success');
                    showFormMsg(data.msg, 'success');
                    setTimeout(function() {
                        //refresh
                        c.refresh();
                        //remove
                        tb_remove();
                    }, 200);
                }
                cur.addClass('btn-01');
                cur.removeClass('btn-02');
                c.saving = false;

            }, 'json');
    },
    init: function() {
        this.addDstCalendar();
        //this.uploader.change();
        this.addCalendar();
        this.dstChange();
        var sel = document.getElementById('criteria-select-options');

        $("#criteria-select-options").change(function() {
            var selected_index = $(this).children('option:selected').val();

            $.get('/company/get_players_of_criterion/' + selected_index,
                function(data) {
                    optionHtml = '';

                    $.each(data, function(i) {
                        optionHtml += '<option value="' + data[i].id + '" >' + data[i].name + '</option>';
                    });

                    $('#players-select-options').html(optionHtml);
                    $('#players-select-options').trigger("chosen:updated");

                }, 'json');
        });




    },
    destory: function() {
        this.removeDstCalendar();
        this.removeCalendar();
        var fm = $('#company-form');
        fm.parent().remove();
        fm.remove();
    },
    destoryFormDialog: function() {
        $("#company-form").dialog('destory');
        this.destory();
    },
    refresh: function() {
        showLoading();
        //刷新当前页面信息
        $.get('/company/refresh?t=' + new Date().getTime(), function(data) {
            $('#layoutContent').html(data);
            hideLoading();
            //reinit this box~
            tb_init('td > a.thickbox');
        });
    },
    remove: function(id, curpage, orderItem, order, msg) {
        if (confirm(msg)) {
            var req = {
                id: id
            }
            $.post('/company/do_delete', req, function(data) {
                if (data.code == 0) {
                    showMsg(data.msg, 'success');
                    c.page(curpage, orderItem, order);
                    setTimeout(hideMsg, 1000);

                } else {
                    showMsg(data.msg, 'error');
                }
            }, 'json');
        }
    },
    filter: function() {
        //$('#c_name').val($('#filter_name').val());
        this.page();
    },
    page: function(curpage, orderItem, order) {
        curpage = curpage || 1;
        orderItem = orderItem || "id";
        order = order || "desc";

        var c_name = $('#filter').val();

        $.get("/company/refresh/" + curpage + "/" + orderItem + "/" + order + "?c_name=" + c_name, function(data) {

            $('#layoutContent').html(data);
            tb_init('td > a.thickbox');
            hideLoading();
        });

    },
    initFilter: function() {
        document.onkeyup = function(event) {
            c.filter();
        }
    },
    criterion_change: function(s) {


    }

};