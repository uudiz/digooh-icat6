<!doctype html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title><?php echo lang("2fa") ?></title>
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

<body class=" d-flex flex-column">
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="." class="navbar-brand navbar-brand-autodark">
                    <img src="<?php echo $tfa->getQRCodeImageAsDataUri($username, $tfa_secret); ?>" width="200px" height="200px">
                </a>
            </div>

            <form class="card card-md" autocomplete="off">
                <div class="card-body">
                    <h2 class="card-title card-title-lg text-center mb-4">Authenticate Your Account</h2>
                    <p class="my-4 text-center">Open your two-factor authenticator (TOTP) app or browser extension like 1Password, Authy, Microsoft Authenticator, etc. to view your authentication code.</strong>.</p>
                    <div class="my-5">
                        <div class="row g-4">
                            <div class="col">
                                <div class="row g-2">
                                    <div class="col">
                                        <input type="text" class="form-control form-control-lg text-center py-3" maxlength="1" inputmode="numeric" pattern="[0-9]*" data-code-input />
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control form-control-lg text-center py-3" maxlength="1" inputmode="numeric" pattern="[0-9]*" data-code-input />
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control form-control-lg text-center py-3" maxlength="1" inputmode="numeric" pattern="[0-9]*" data-code-input />
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="row g-2">
                                    <div class="col">
                                        <input type="text" class="form-control form-control-lg text-center py-3" maxlength="1" inputmode="numeric" pattern="[0-9]*" data-code-input />
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control form-control-lg text-center py-3" maxlength="1" inputmode="numeric" pattern="[0-9]*" data-code-input />
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control form-control-lg text-center py-3" maxlength="1" inputmode="numeric" pattern="[0-9]*" data-code-input />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-footer">
                        <div class="btn-list flex-nowrap">
                            <a class="btn w-100" onclick="doCancel()">
                                <?php echo lang('button.cancel') ?>
                            </a>
                            <a href="#" id="verify_2fa" class="btn btn-primary w-100">
                                Verify
                            </a>
                        </div>
                    </div>
                </div>
            </form>
            <!--
            <div class="text-center text-secondary mt-3">
                It may take a minute to receive your code. Haven't received it? <a href="./">Resend a new code.</a>
            </div>
    -->
        </div>
    </div>
    <!-- Libs JS -->

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var inputs = document.querySelectorAll('[data-code-input]');
            // Attach an event listener to each input element
            for (let i = 0; i < inputs.length; i++) {
                inputs[i].addEventListener('input', function(e) {
                    // If the input field has a character, and there is a next input field, focus it
                    if (e.target.value.length === e.target.maxLength && i + 1 < inputs.length) {
                        inputs[i + 1].focus();
                    }
                });
                inputs[i].addEventListener('keydown', function(e) {
                    // If the input field is empty and the keyCode for Backspace (8) is detected, and there is a previous input field, focus it
                    if (e.target.value.length === 0 && e.keyCode === 8 && i > 0) {
                        inputs[i - 1].focus();
                    }
                });
            }
            inputs[0].focus();
        });

        $("#verify_2fa").on("click", function() {
            var code = '';
            var inputs = document.querySelectorAll('[data-code-input]');
            for (let i = 0; i < inputs.length; i++) {
                code += inputs[i].value;
            }
            $.get('/Tfa/doVertification', {
                code: code,
            }, function(data) {
                if (data.code != 0) {
                    toastr.error(data.msg);
                } else {
                    window.location = '/';
                }
            }, 'json');
        });


        function doCancel() {
            window.location = '/login';
        }

        $(document).keypress(function(e) {
            if (e.which == 13) {
                $("#verify_2fa").click();
            }
        });
    </script>

</body>

</html>