<div class="modal modal-blur fade" id="modal-medium-preview" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">

            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            <div class="modal-body" id="medium-preview-modal-body">
            </div>
            <div class="modal-footer">
                <?php if ($auth == 5 && !$pid) : ?>
                    <div id="approval">
                        <a type="button" class="btn btn-outline-primary approval" id="1"><i class="bi bi-check-lg"></i><?php echo lang('approve'); ?></a>

                    </div>
                    <a type="button" class="btn btn-outline-primary approval btn-for-video" id="2"><i class="bi bi-picture" style="display:none"></i><?php echo lang('approveAsP'); ?>
                    </a>
                    <a type="button" class="btn btn-outline-warning" id="reject" style="display:none"><i class="bi bi-x"></i><?php echo lang('reject'); ?></a>
                <?php endif ?>

                <a type="button" class="btn btn-outline-primary" id="preview_download"><i class="bi bi-cloud-arrow-down"></i><?php echo lang('download'); ?></a>
                <a type="button" class="btn btn-outline-secondary me-auto" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i><?php echo lang('button.close'); ?></a>
                <input type="hidden" id="preview_id" />
            </div>
        </div>
    </div>
</div>
<script>
    var exampleModal = document.getElementById('modal-medium-preview')
    exampleModal.addEventListener('show.bs.modal', function(event) {
        // Button that triggered the modal
        var button = event.relatedTarget
        // Extract info from data-bs-* attributes
        var id = button.getAttribute('data-bs-mediumId')

        var showApproval = button.getAttribute('data-bs-showApproval')

        if (id) {
            $('#preview_id').val(id);
            $.getJSON(`/media/preview?id=${id}`, function(res) {
                $('#preview_download').attr('href', `/media/download?id=${id}`);

                $('#medium-preview-modal-body').html(res.medium);
                if (showApproval && showApproval == 1) {
                    var m_type = button.getAttribute('data-bs-mediumType');
                    if (res.approved == 0) {
                        $('#approval').show();

                    }
                    if (m_type && m_type == '2' && (res.approved == 1 || res.approved == 0)) {
                        $('.btn-for-video').show();
                    }

                    $('.approval').on('click', function(e) {
                        var approved_type = $(e.target).attr('id');
                        console.log(approved_type);
                        $.get(`/media/do_approval?id=${id}&approved=${approved_type}`, function(response) {
                            var myModal = bootstrap.Modal.getInstance(exampleModal)
                            myModal.hide();
                            $('#table').bootstrapTable('refresh');
                        })
                    });

                } else {
                    $('#approval').hide();
                    $('.btn-for-video').hide();
                }
            });
        }
    })
    exampleModal.addEventListener('show.bs.modal', function(event) {
        $('#approval').hide();
        $('.btn-for-video').hide();
        $('#reject').hide();
        $('.approval').unbind('click');
        $('#reject').unbind('click');
    })
</script>