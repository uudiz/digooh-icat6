<link rel="stylesheet" href="/assets/bootstrap-icons/bootstrap-icons.css">
<link href="/assets/bootstrap/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
<link href="/assets/bootstrap/css/theme.explore.min.css" rel="stylesheet" type="text/css" />
<script src="/assets/bootstrap/js/fileinput.min.js"></script>
<script src="/assets/bootstrap/js/fileinput-locales/de.js"></script>
<script src="/assets/bootstrap/js/fileinput-locales/en.js"></script>
<script src="/assets/bootstrap/js/theme.explore.min.js"></script>


<div class="modal fade" id="uploadFirmware" aria-labelledby="uploadFirmware" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-fullscreen-lg-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Firmware</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="uploader-body">


                <div class="overflow-auto" style="max-height:600px">
                    <div class="file-loading overflow-auto" style="max-height:600px">
                        <input id="input-uploader" name="input-uploader" type="file" accept=".inf">
                    </div>
                    <div id="kartik-file-errors"></div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal"><?php echo lang('button.close'); ?></button>

            </div>
        </div>
    </div>
</div>

<script>
    $('#uploadFirmware').on('show.bs.modal', function(e) {
        var button = e.relatedTarget
        // Extract info from data-bs-* attributes

        const uploader = $("#input-uploader");
        var lang = 'en';
        <?php if ($lang == "germany") : ?>
            var lang = 'de';
        <?php endif ?>
        uploader.fileinput({
            theme: "explorer",
            uploadUrl: "/Firmware_controller/upload",

            allowedFileExtensions: ["inf"],
            overwriteInitial: false,
            initialPreviewAsData: false,
            removeFromPreviewOnError: true,
            showClose: false,
            showRemove: false,
            showUpload: false,
            fileActionSettings: {
                showRemove: false,
            },
            language: lang,

        }).on('filebatchselected', function(event, previewId, index, fileId) {
            uploader.fileinput('upload');
        });


    })
    $('#uploadFirmware').on('hide.bs.modal', function(e) {
        $("#input-uploader").fileinput('clear');
        $('.table').bootstrapTable('refresh');
    })

    $(document).ready(function() {



    });
</script>