<div class="modal fade" id="mediaModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo lang('media') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3 align-items-end">


                    <div class="row align-items-center justify-content-end">
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="form-label col-auto col-form-label"></label>
                                <div class="col">
                                    <select class="form-select" id="media_type" name='media_type' onchange="do_search();">
                                        <option value="-1"><?php echo lang('all'); ?></option>
                                        <option value="1"><?php echo lang('image'); ?></option>
                                        <option value="2"><?php echo lang('video'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="form-label col-auto col-form-label"></label>
                                <div class="col">
                                    <select class="form-select" id="mediaFolders" name='folders' onchange="do_search();">
                                        <?php if ($auth > 2 && !$pid) : ?>
                                            <option value="-1"><?php echo lang('all') ?></option>
                                        <?php endif ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-auto">
                            <div class="input-icon">
                                <input type="text" id="mediaSearch" class="form-control " placeholder="">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <circle cx="10" cy="10" r="7"></circle>
                                        <line x1="21" y1="21" x2="15" y2="15"></line>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="mb-3">
                    <table id="tableMediaModal" class="table table-sm table-striped table-responsive" data-page-size="15" id="table" data-side-pagination="server" data-pagination="true" data-search="false" data-sort-name="name" data-sort-order="desc" data-page-list="[15, 30, 50, 100]" data-query-params="mediaQueryParams">

                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="select-media"><?php echo lang('button.save'); ?></button>
                <button type="button" class="btn me-auto" data-bs-dismiss="modal" id="close-media-modal"><?php echo lang('button.cancel'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    function mediaQueryParams(params) {
        params.search = $('#mediaSearch').val();
        params.folders = $('#mediaFolders').val();
        params.media_type = $('#media_type').val();
        params.approved = 1;
        return params;
    }

    $('#mediaModal').on('show.bs.modal', function(e) {
        var button = e.relatedTarget
        var useRadio = button.getAttribute('data-single-sel') ? true : false;

        var imgOnly = button.getAttribute('data-img-only');
        if (imgOnly) {
            $("#media_type").val('1');

            $("#media_type").hide();
        } else {
            $("#media_type").show();
        }
        $('#tableMediaModal').bootstrapTable({
            url: '/media/getTableData',
            queryParams: "mediaQueryParams",
            columns: [{
                    radio: useRadio,
                    checkbox: useRadio ? false : true,
                }, {
                    field: 'name',
                    title: "<?php echo lang('name'); ?>",
                    sortable: true,
                    formatter: "nameFormatter",
                }, {
                    field: 'file_size',
                    title: "<?php echo lang('file.size'); ?>",
                    sortable: true,
                    formatter: "sizeFormatter",
                }, {
                    field: 'folder_name',
                    title: "<?php echo lang('folder'); ?>",
                    sortable: true,
                },
                {
                    field: 'play_time',
                    title: "<?php echo lang('play_time'); ?>",
                    sortable: true,
                    //formatter: "sizeFormatter",
                }, {
                    field: 'start_date',
                    title: "<?php echo lang('date.range'); ?>",
                    sortable: true,
                    formatter: "dateFormatter",
                }
            ]
        })


    });



    function sizeFormatter(value, row) {
        return fileSizeSI(value);
    }

    function nameFormatter(value, row) {

        var previewObject = {
            url: row.full_path,
            name: `${row.name}`,
        };
        return `<div class="row g-2 align-items-center">
                <div class="col-auto">   
                    <img src="${row.tiny_url}" class="rounded" style="max-width:40px; max-height:40px" onerror="javascript:this.remove()">

                </div>
                <div class="col">
                    ${value}
                </div>
                </div>`;
    };

    function dateFormatter(value, row) {
        if (row.date_flag == '1') {
            var CurrentDate = new Date();
            var SelectedDate = new Date(row.end_date);
            var color = '';
            if (CurrentDate > SelectedDate) {
                color = "text-red";
            }

            return `<span class="${color}">${value}~${row.end_date}</span>`;
        }
        return '';
    };
    var mediaTable = $('#tableMediaModal');

    $("input#mediaSearch").on('keydown paste input', function() {
        do_search();
    });


    function do_search() {
        mediaTable.bootstrapTable('refresh');
        mediaTable.bootstrapTable('uncheckAll');
    }



    $('#mediaModal').on('hide.bs.modal', function(e) {
        $('#search').val(null);
        $("#media_type").val('-1');
        mediaTable.bootstrapTable('destroy')
    });

    $(document).ready(function() {
        $.ajax({
            url: '/player/getNestedFolders',
            dataType: "json",
            success: function(res) {
                $("#mediaFolders").select2ToTree({
                    width: '100%',
                    dropdownParent: $("#mediaModal"),
                    treeData: {
                        dataArr: res.data
                    }
                });
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
</script>