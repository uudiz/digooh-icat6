<div class="modal fade" id="mceModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo lang('webpage') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3 align-items-end">

                    <div class="row align-items-center justify-content-end">

                        <div class="col-auto">
                            <div class="input-icon">
                                <input type="text" id="webSearch" class="form-control " placeholder="">
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
                    <table id="tableWebModal" class="table table-sm table-striped table-responsive" data-page-size="15" data-side-pagination="server" data-pagination="true" data-search="false" data-sort-name="name" data-sort-order="desc" data-page-list="[15, 30, 50, 100]" data-query-params="mediaQueryParams">

                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="select-web"><?php echo lang('button.save'); ?></button>
                <button type="button" class="btn me-auto" data-bs-dismiss="modal" id="close-web-modal"><?php echo lang('button.cancel'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    var targetTableId = null;

    function getTargetTable() {
        return targetTableId;
    }

    $('#select-web').on('click', function() {
        {

            /*
            let media = {
                url: $('#url').val(),
                url_type: $('#url_type').val() == 'on' ? 1 : 0,
                updateF: $('#updateF').val(),
                duration: $('#play_time').val(),
            }
            var targetTable = getTargetTable();
            if ($('#web_index').val() !== "-1") {
                $(`${targetTable}`).bootstrapTable('updateRow', {
                    index: $('#web_index').val(),
                    row: media
                })
            } else {
                $(`${targetTable}`).bootstrapTable('append', media);
            }
            */

            var selections = $('#tableWebModal').bootstrapTable('getSelections');
            if (selections.length == 0) {
                return;
            }

            let media = selections.map((item) => {
                return {
                    id: item.id,
                    name: item.name,
                    play_time: item.play_time,
                    descr: item.descr,
                }
            });
            var targetTable = getTargetTable();
            $(`${targetTable}`).bootstrapTable('append', media);
            $(`${targetTable}`).bootstrapTable('refresh');


            $('#close-media-modal').click();
        }
    });


    function webQueryParams(params) {
        params.search = $('#webSearch').val();
        return params;
    }

    $('#mceModal').on('show.bs.modal', function(e) {
        var button = e.relatedTarget
        targetTableId = $('a[data-bs-toggle="tab"].active').attr('href') + 'Table'
        $('#tableWebModal').bootstrapTable({
            url: '/webpages/getTableData',
            queryParams: "webQueryParams",
            columns: [{
                checkbox: true,
            }, {
                field: 'name',
                title: "<?php echo lang('name'); ?>",
                sortable: true,
            }, {
                field: 'descr',
                title: "<?php echo lang('desc'); ?>",
            }, {
                field: 'play_time',
                title: "<?php echo lang('play_time'); ?>",
            }, ]
        })


    });



    var webTable = $('#tableWebModal');

    $("input#webSearch").on('keydown paste input', function() {
        do_search();
    });


    function do_search() {
        webTable.bootstrapTable('refresh');
        webTable.bootstrapTable('uncheckAll');
    }



    $('#mceModal').on('hide.bs.modal', function(e) {
        $('#search').val(null);
        webTable.bootstrapTable('destroy')
    });

    $(document).ready(function() {

    });
</script>