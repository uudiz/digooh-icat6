<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <?php if (!$this->config->item('with_template')) : ?>
        <link rel="icon" href="/assets/img/favicon.png" type="image/x-icon" />
    <?php endif ?>

    <link href="/assets/bootstrap/css/bootstrap-utilities.min.css" rel="stylesheet">

    <link href="/assets/bootstrap/css/bootstrap-table.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/bootstrap/css/select2.min.css" />
    <link rel="stylesheet" href="/assets/bootstrap/css/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="/assets/bootstrap/css/tabler.min.css">
    <link rel="stylesheet" href="/assets/bootstrap/css/toastr.min.css" />
    <link rel="stylesheet" href="/assets/bootstrap/css/tabler-flags.min.css">
    <link rel="stylesheet" href="/assets/css/app.css" />
    <link rel="stylesheet" href="/assets/css/dig-icon.css" />
    <link rel="stylesheet" href="/assets/bootstrap-icons/bootstrap-icons.css">


    <script src="/assets/bootstrap/js/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="/assets/bootstrap/js/tabler.min.js"></script>
    <script src="/assets/bootstrap/js/toastr.min.js"></script>
    <script src="/assets/bootstrap/js/bootstrap-table.min.js"></script>
    <script src="/assets/bootstrap/js/bootstrap-table-de-De.js"></script>
    <script src="/assets/bootstrap/js/select2.min.js"></script>
    <script language="javascript" src="/assets/bootstrap/js/select2totree.js?t=19003"></script>
    <script src="/assets/bootstrap/js/jquery.validate.min.js"></script>
    <script src="/static/js/common.js"></script>
    <script src="/assets/js/app.js"></script>
    
    <title>DIGOOH-CMS *N1</title>
</head>


<body>
    <div class="page">
        <?php if ($this->config->item("with_template")) : ?>
            <aside class="navbar navbar-vertical navbar-expand-lg navbar-dark" style="background-color:<?php echo isset($bg_color) && $bg_color ? $bg_color : '#1e293b' ?>;">
                <?php
                $this->load->view('bootstrap/layout/components/aside');
                ?>
            </aside>
            <header class="navbar navbar-expand-md navbar-light d-none d-lg-flex d-print-none">
                <?php
                $this->load->view('bootstrap/layout/components/header');
                ?>
            </header>
        <?php else : ?>
            <?php $this->load->view('bootstrap/layout/header'); ?>
        <?php endif ?>
        <div class="page-wrapper">
            <?php
            if (isset($body_file)) {
                $this->load->view($body_file);
            }
            ?>

        </div>
        <div class="overlay"></div>
        <div class="spanner">
            <div class="loader"></div>
            <p id="overlay_status_msg"></p>
        </div>
    </div>
    <div class="modal modal-blur fade" id="delete_confirm" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <!-- Download SVG icon from http://tabler-icons.io/i/alert-triangle -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 9v2m0 4v.01" />
                        <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" />
                    </svg>
                    <h3></h3>
                    <div class="text-muted"><?php echo lang('tip.remove.item'); ?></div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col"><a href="#" class="btn w-100" data-bs-dismiss="modal">
                                    <?php echo lang('button.cancel'); ?>
                                </a></div>
                            <div class="col"><a href="#" class="btn btn-danger w-100" data-bs-dismiss="modal" id="delete">
                                    <?php echo lang('delete'); ?>
                                </a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<?php
//加载js
if (isset($jsList)) {
    foreach ($jsList as $js) {
        echo '<script language="javascript" src="' . $js . '?t=' . $date . '"></script>' . PHP_EOL;
    }
}
?>

<script>
    function switchLang(lang) {
        $.post('/Index/update_language', {
            lang: lang
        }, function(data) {
            window.location.href = '/';
        }, 'json');
    };
    var cur_lang = "<?php echo $lang ?>";
    cur_lang == 'germany' ? $.extend($.fn.bootstrapTable.defaults, $.fn.bootstrapTable.locales['de-DE']) : $.extend($.fn.bootstrapTable.defaults, $.fn.bootstrapTable.locales['en-US']);
</script>

</html>