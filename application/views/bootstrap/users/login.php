<!doctype html>
<html lang="utf-8">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title><?php echo lang("sign_in") ?></title>
    <meta name="msapplication-TileColor" content="#206bc4" />
    <meta name="theme-color" content="#206bc4" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="HandheldFriendly" content="True" />
    <meta name="MobileOptimized" content="320" />
    <link rel="icon" href="/assets/img/favicon.png" type="image/x-icon" />
    <!-- CSS files -->
    <link rel="stylesheet" href="/assets/bootstrap/css/tabler.min.css" />
    <link rel="stylesheet" href="/assets/bootstrap/css/toastr.min.css" />
    <script src="/assets/bootstrap/js/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="/assets/bootstrap/js/toastr.min.js"></script>
    <script src="/assets/bootstrap/js/tabler.min.js"></script>
    <script src="/assets/bootstrap/js/jquery.validate.min.js"></script>
    <script src="/assets/js/form.js"></script>
    <?php if ($lang == 'germany') : ?>
        <script src="/assets/js/validation/messages_de.js"></script>
    <?php endif ?>
</head>


<body class=" border-top-wide border-primary d-flex flex-column">
    <div class="page page-center">
        <div class="container-tight py-4">
            <div class="text-center mb-4">
                <?php if ($this->config->item("with_template")) : ?>
                    <?php if ($this->config->item("kdh_logo")) : ?>
                        <a href="." class="navbar-brand navbar-brand-autodark"><img src="/assets/logos/KDH_Logo_2023_png.png" alt="KDH"></a>
                    <?php else : ?>
                        <a href="." class="navbar-brand navbar-brand-autodark"><img src="/assets/logos/default_logo_icat7.png" alt="ICAT7"></a>
                    <?php endif ?>
                <?php else : ?>
                    <a href="." class="navbar-brand navbar-brand-autodark"><img src="/assets/logos/logo-digooh.svg" alt="Digooh"></a>
                <?php endif ?>
            </div>
            <form class="card card-md" autocomplete="off" id="loginForm">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4"></h2>
                    <div class="mb-3">
                        <label class="form-label"><?php echo lang('user_name') ?></label>
                        <input id="username" name="username" required type="text" class="form-control" placeholder="<?php echo lang('user_name') ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">
                            <?php echo lang('password') ?>
                        </label>
                        <div class="input-group input-group-flat">
                            <input id="password" name="password" type="password" required class="form-control" placeholder="<?php echo lang('password') ?>" autocomplete="off">
                            <span class="input-group-text">

                                <a class="switch-icon" data-bs-toggle="switch-icon" href="#" class="link-secondary" onclick="toggle()">
                                    <span class="switch-icon-a">
                                        <svg xmlns=" http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <circle cx="12" cy="12" r="2" />
                                            <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" />
                                        </svg>
                                    </span>
                                    <span class="switch-icon-b">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye-off" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <line x1="3" y1="3" x2="21" y2="21"></line>
                                            <path d="M10.584 10.587a2 2 0 0 0 2.828 2.83"></path>
                                            <path d="M9.363 5.365a9.466 9.466 0 0 1 2.637 -.365c4 0 7.333 2.333 10 7c-.778 1.361 -1.612 2.524 -2.503 3.488m-2.14 1.861c-1.631 1.1 -3.415 1.651 -5.357 1.651c-4 0 -7.333 -2.333 -10 -7c1.369 -2.395 2.913 -4.175 4.632 -5.341"></path>
                                        </svg>
                                    </span>
                                </a>
                            </span>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">
                            <span class="form-label-description">
                                <a href="/login/forget_password"><?php echo lang('forget_password') ?></a>
                            </span>
                        </label>
                    </div>
                    <input id="lang" name="lang" value="english" hidden value="<?php echo $this->config->item("language") ?>" />
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100"> <?php echo lang('sign_in') ?></button>
                </div>
            </form>
        </div>
    </div>


    <script type="text/javascript">
        toastr.options.positionClass = "toast-bottom-right";
        toastr.options.showDuration = 60;
        toastr.options.closeButton = true;

        function do_login() {
            var user = $('#username').val();
            var pass = $('#password').val();
            $.post('/login/doLogin', {
                username: user,
                password: pass
            }, function(data) {
                if (data.code != 0) {
                    toastr.error(data.msg);
                } else {
                    window.location = '/';
                }
            }, 'json');
        }
        const lang = localStorage.getItem('language');
        $('#lang').val(lang);

        function toggle() {
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

        $(document).ready(function() {
            $("#loginForm").validate({
                submitHandler: function(form) {
                    var user = $('#username').val();
                    var pass = $('#password').val();
                    $.post('/login/doLogin', {
                        username: user,
                        password: pass
                    }, function(data) {
                        if (data.code != 0) {
                            console.log(data.msg);
                            toastr.error(data.msg);
                        } else {

                            <?php if ($this->config->item('tfa_enabled') == 1) : ?>
                                if (data.tfa_enabled && data.tfa_enabled == 1) {
                                    window.location = '/Tfa/vertification';

                                } else {
                                    window.location = '/';
                                }
                            <?php else : ?>
                                window.location = '/';
                            <?php endif ?>
                        }
                    }, 'json');

                },


            });
        });
    </script>
</body>

</html>