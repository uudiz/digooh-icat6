<link href="/assets/bootstrap/css/select2totree.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="/assets/bootstrap-icons/bootstrap-icons.css">
<link href="/assets/bootstrap/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
<link href="/assets/bootstrap/css/theme.explore.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
<script src="/assets/bootstrap/js/select2totree.js"></script>
<script src="/assets/bootstrap/js/fileinput.min.js"></script>
<script src="/assets/bootstrap/js/fileinput-locales/de.js"></script>
<script src="/assets/bootstrap/js/fileinput-locales/en.js"></script>
<script src="/assets/bootstrap/js/theme.explore.min.js"></script>


<div class="modal fade" id="uploadModal" aria-labelledby="uploadModal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-fullscreen-lg-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo lang('media.upload.files'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="uploader-body">
                <ul class="nav nav-tabs" data-bs-toggle="tabs">
                    <li class="nav-item">
                        <a href="#tabs-home-ex1" class="nav-link active" data-bs-toggle="tab"><?php echo lang('local') ?></a>
                    </li>

                    <li class="nav-item">
                        <a href="#tabs-ftp" class="nav-link" data-bs-toggle="tab"><?php echo lang('ftp') ?></a>
                    </li>

                </ul>
                <form class="pb-1 pt-1">
                    <div class="form-group mb-3 row ">
                        <label class="form-label col-auto col-form-label"><?php echo lang("folder"); ?></label>
                        <div class="col-md-6">
                            <select class="form-select" id="uploader_folder"></select>
                        </div>
                    </div>

                </form>

                <div class="tab-content">
                    <div class="tab-pane active show" id="tabs-home-ex1">
                        <div class="overflow-auto" style="max-height:600px">
                            <div class="file-loading overflow-auto" style="max-height:600px">
                                <input id="input-uploader" name="input-uploader[]" type="file" multiple accept="image/*;video/*">
                            </div>
                            <div id="kartik-file-errors"></div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tabs-ftp">
                        <div class="row mb-3">
                            <div class="col">
                                <select id="ftp-sites" class="form-select col" autocomplete="off" aria-placeholder="Select ftp site">
                                </select>
                            </div>
                            <div class="col-auto">
                                <Button class="btn btn-outline-primary" onclick="connect_ftpServer()"><?php echo lang('button.connect') ?></Button>
                                <button id='save_ftp_btn' type="button" class="btn btn-outline-primary" disabled onclick="save_ftp_files()"><?php echo lang('button.save'); ?></button>
                                <input type="hidden" id="pwd" value="" />
                            </div>

                        </div>

                        <fieldset class="form-fieldset" style="min-height:200px">
                            <div id="jstree_demo_div"></div>

                        </fieldset>

                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal"><?php echo lang('button.close'); ?></button>

            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
<script>
    var target_campaign = null;
    var dest_field = null;
    /*   $(' #jstree_demo_div').on("changed.jstree", function(e, data) { console.log(data); }); */


    function getNodePath(node) {
        if (node.id === undefined) {
            return null;
        }
        var path = node.id;

        if (path == '#') {
            path = ".";
        } else {
            if (node.parents === undefined) {
                return node.id;
            }
            var parents = node.parents.filter(function(item) {
                if (item == '#') {
                    return false
                }
                return true;
            });

            if (parents.length) {
                var parent_path = parents.reverse().join('/');
                path = parent_path + '/' + path;
            }
        }
        return path;
    }

    function save_ftp_files() {
        var selected = $('#jstree_demo_div').jstree().get_selected(true);
        console.log(selected);

        var files = selected.filter(function(item) {
            if (item.type == "file") {
                return true;
            }
            return false;
        });

        if (files.length == 0) {
            toastr.warning('Please at least choose one file');
            return false;
        }

        var root_pwd = $("#pwd").val();

        var media = files.map((node) => {
            if (node.type === 'file') {

                return {
                    file: root_pwd + '/' + getNodePath(node),
                    size: node.data,
                }
            } else {
                return false;
            }
        })
        //console.log(media);


        var id = $('#ftp-sites').val();
        var data = {
            id: id,
            folderSel: $('#uploader_folder').val(),
            media: media,
            media_type: this.mediaType
        }

        $.post('/media/do_save_ftp_media', data, function(json) {
            if (json.code != 0) {
                toastr.error(json.msg);
            } else {
                toastr.success(json.msg);
            }
        }, 'json');
    }

    function connect_ftpServer() {
        var site_id = $('#ftp-sites').val();
        $('#jstree_demo_div').jstree('destroy');
        if (site_id > '0') {
            $('#jstree_demo_div').jstree({
                "themes": {
                    "stripes": true
                },
                "check_callback": true,
                'core': {
                    'data': {
                        url: '/ftpSites/list_ftp_files/',
                        type: 'POST',
                        dataType: "json",
                        'data': function(node) {
                            var path = node.id;
                            if (path == '#') {
                                path = ".";
                            } else {
                                var parents = node.parents.filter(function(item) {
                                    if (item == '#') {
                                        return false
                                    }
                                    return true;
                                });

                                if (parents.length) {
                                    var parent_path = parents.reverse().join('/');
                                    path = parent_path + '/' + path;
                                }
                            }

                            return {
                                'config_id': site_id,
                                'pwd': path,
                            };
                        },
                        'dataFilter': function(res) {
                            var resData = JSON.parse(res);
                            $('#pwd').val(resData.rootPwd ? resData.rootPwd : '');
                            data = JSON.stringify(resData.nodes);
                            return data;
                        },
                        'error': function(err) {
                            console.log(err);
                        }

                    }
                },
                "plugins": [
                    "checkbox", "dnd", "search",
                    "state", "types", "wholerow"
                ],
                "types": {
                    "file": {
                        "icon": "bi bi-file",
                        "valid_children": []
                    }
                },
            }).on("changed.jstree", function(e, data) {
                if (data.selected.length === 0) {
                    $('#save_ftp_btn').attr('disabled');

                } else {
                    $('#save_ftp_btn').removeAttr('disabled');
                }
            });;
        }
    }

    $('#uploadModal').on('show.bs.modal', function(e) {
        var button = e.relatedTarget
        // Extract info from data-bs-* attributes
        var dest_field = button.getAttribute('data-target-campaign');
        $('.nav-tabs a:first').tab('show');

        const uploader = $("#input-uploader");
        var lang = 'en';
        <?php if ($lang == "germany") : ?>
            var lang = 'de';
        <?php endif ?>
        uploader.fileinput({
            //enableResumableUpload: true,
            theme: "explorer",
            uploadUrl: "/media/upload",

            allowedFileExtensions: ["avi", "mp4", "divx", 'mpeg', 'mpg', 'mkv', 'mov', 'png', 'jpg', 'jpeg'],
            overwriteInitial: false,
            initialPreviewAsData: true,
            removeFromPreviewOnError: true,
            showClose: false,
            showRemove: false,
            showUpload: false,
            fileActionSettings: {
                showRemove: false,
            },
            uploadExtraData: extraData,
            language: lang,

        }).on('filebatchselected', function(event, previewId, index, fileId) {
            uploader.fileinput('upload');
        }).on('fileuploaded', function(event, data, previewId, index, fileId) {
            if (data.response.medium && dest_field) {
                var medium = data.response.medium;
                data.response.medium.transmode = 26;
                data.response.medium.status = 0;
                $('#areaMediaTable').bootstrapTable('append', data.response.medium);

            }
        });;

        function extraData(previewId, index) {
            return {
                folderSel: $('#uploader_folder').val(),
                targetCampaign: target_campaign ? target_campaign : 0,
            };
        }

    })
    $('#uploadModal').on('hide.bs.modal', function(e) {
        target_campaign = null;
        $("#input-uploader").fileinput('clear');
        $('.table').bootstrapTable('refresh');
        $('#ftp-sites').val("").trigger("change");
        $('#jstree_demo_div').jstree('destroy');
    })

    $(document).ready(function() {
        $('#ftp-sites').on('change', function() {
            $('#jstree_demo_div').jstree('destroy');
            $('#save_ftp_btn').attr('disabled');
        });


        $.ajax({
            url: '/player/getNestedFolders',
            dataType: "json",
            success: function(res) {
                $("#uploader_folder").select2ToTree({
                    dropdownParent: $("#uploadModal"),
                    width: '100%',
                    treeData: {
                        dataArr: res.data
                    }
                });
            },
            cache: false,
            contentType: false,
            processData: false
        });

        $.ajax({
            url: '/ftpSites/get_ftp_list',
            dataType: "json",
            success: function(res) {
                var ftps = $.map(res.rows, function(obj) {
                    obj.id = obj.id;
                    obj.text = obj.profile;
                    return obj;
                });
                $("#ftp-sites").select2({
                    dropdownParent: $("#uploadModal"),
                    width: '100%',
                    data: ftps
                });
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
</script>