<div class="modal fade" id="webpageModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo lang('webpage') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="mb-3">
                        <label class="form-label"></label>
                        <div class="form-control-plaintext"><?php echo lang('url') ?></div>
                        <input type="text" class="form-control" id="url">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo lang('play_time') ?></label>
                        <input type="text" class="form-control" id="play_time" value="01:00:00"><small>(HH:MM:SS)</small>
                    </div>
                    <div class="mb-3">
                        <label>URL for IP Camera</label>
                        <div class="form-check form-switch align-bottom">
                            <input class="form-check-input" type="checkbox" id="url_type">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo lang('update.frequency') ?></label>
                        <input type="text" class="form-control" id="updateF" value="01:00:00"><small>(HH:MM:SS)</small>
                    </div>
                    <form>
                        <div class="mb=3">

                        </div>
                    </form>
                    <input type="hidden" id="web_index" value="-1" />
                </div>
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-primary" id="add_web"><?php echo lang('button.save'); ?></button>
                <button type="button" class="btn me-auto" data-bs-dismiss="modal" id="close-media-modal"><?php echo lang('button.cancel'); ?></button>
            </div>
        </div>
    </div>
</div>

<script src="/assets/tinymce/js/tinymce/tinymce.min.js"></script>
<script>
    var targetTableId = null;

    function getTargetTable() {
        return targetTableId;
    }

    $('#add_web').on('click', function() {
        {

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
            $('#close-media-modal').click();
        }
    });

    $('#webpageModal').on('show.bs.modal', function(e) {
        targetTableId = $('a[data-bs-toggle="tab"].active').attr('href') + 'Table'


    });

    $('#webpageModal').on('hide.bs.modal', function(e) {
        $('#search').val(null);

        mediaTable.bootstrapTable('uncheckAll');
        var targetTable = getTargetTable();
        $(`${targetTable}`).bootstrapTable('refresh');
    });
    tinymce.init({
        theme: "silver",
        selector: 'textarea#html',
        width: 300,
        height: 1080,
        resize: false,
        plugins: 'table preview', // note the comma at the end of the line!
        menubar: false,
        toolbar: 'undo redo | bold italic underline strikethrough | table | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify |preview | insertfile image media template link anchor',
        font_family_formats: 'Arial=arial,helvetica,sans-serif; Courier New=courier new,courier,monospace; AkrutiKndPadmini=Akpdmi-n'

    });
    document.addEventListener('focusin', (e) => {
        if (e.target.closest(".tox-tinymce, .tox-tinymce-aux, .moxman-window, .tam-assetmanager-root") !== null) {
            e.stopImmediatePropagation();
        }
    });
    $(document).ready(function() {

    });
</script>