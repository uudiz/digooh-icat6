<div class="card">
    <div class="row row-0">
        <div class="col-md-8 col-sm-12">
            <?php if ($medium->media_type == 1) : ?>
                <img src="<?php if (isset($medium->full_path)) echo substr($medium->full_path, 1); ?>" class="w-100 h-100 object-contain" alt="Card side image" onerror="javascript:this.remove()" />
            <?php elseif ($medium->media_type == 2) : ?>
                <video autoplay controls class="w-100 h-100 object-contain">
                    <source src="<?php echo substr($medium->full_path, 1); ?>" type='video/mp4'>
                </video>
            <?php endif ?>
        </div>
        <div class="col-md-4 d-none d-sm-inline">
            <div class="card-body ">
                <h3 class="card-title">"<?php echo $medium->name ?>"</h3>
                <div class="mb-2">
                    <?php echo lang("author") ?>: <strong><?php echo $medium->author ? $medium->author : "" ?></strong>
                </div>
                <div class="mb-2">
                    <?php echo lang("upload.date") ?>: <strong><?php echo $medium->add_time ?></strong>
                </div>
                <div class="mb-2">
                    <?php echo lang("file.size") ?>: <strong><?php echo byte_format($medium->file_size, 2) ?></strong>
                </div>
                <div class="mb-2">
                    <?php echo lang("dimension") ?>: <strong><?php echo $medium->width . 'X' . $medium->height ?></strong>
                </div>
                <div class="mb-2">
                    <?php echo lang("folder") ?>: <strong><?php echo $medium->folder_id == '0' ? lang('folder.default') : $medium->folder->name ?></strong>
                </div>
                <div class="mb-2">
                    <?php echo lang("approval") ?>:
                    <strong>
                        <?php if ($medium->approved == '0') {
                            echo lang('unapproved');
                        } else {
                            echo $medium->approved == '2' ? lang("approveAsP") : lang('approved');
                        } ?>
                    </strong>
                </div>
            </div>
        </div>
    </div>
</div>