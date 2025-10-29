<!doctype html>
<html lang="utf-8">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title><?php echo lang('forget_password') ?></title>
    <meta name="msapplication-TileColor" content="#206bc4" />
    <meta name="theme-color" content="#206bc4" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="HandheldFriendly" content="True" />
    <meta name="MobileOptimized" content="320" />
    <link rel="icon" href="./favicon.ico" type="image/x-icon" />
    <!-- CSS files -->
    <link rel="stylesheet" href="/assets/bootstrap/css/tabler.min.css" />
    <link rel="stylesheet" href="/assets/bootstrap/css/toastr.min.css" />
    <script src="/assets/bootstrap/js/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="/assets/bootstrap/js/toastr.min.js"></script>
</head>

<body class=" border-top-wide border-primary d-flex flex-column">
    <div class="page page-center">
        <div class="container-tight py-4">
            <div class="text-center mb-4">
                <a href="." class="navbar-brand navbar-brand-autodark"><img src="./static/logo.svg" height="36" alt=""></a>
            </div>
            <form class="card card-md" action="." method="get">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4"><?php echo lang('forget_password') ?></h2>
                    <p class="text-muted mb-4"><?php echo lang('password.reset.tips') ?></p>
                    <div class="mb-3 ">
                        <label class="form-label required"><?php echo lang('user_name') ?></label>
                        <input type="text" id="name" class="form-control" placeholder="">
                    </div>
                    <div class="mb-3">
                        <label class="form-label required"><?php echo lang('email_address') ?></label>
                        <input type="email" id="email" class="form-control " placeholder="">
                    </div>
                    <div class="form-footer">
                        <a href="#" class="btn btn-primary w-100" onclick="reset()">
                            <!-- Download SVG icon from http://tabler-icons.io/i/mail -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <rect x="3" y="5" width="18" height="14" rx="2" />
                                <polyline points="3 7 12 13 21 7" />
                            </svg>
                            <?php echo lang('send.password') ?>
                        </a>
                    </div>
                </div>
            </form>
            <div class="text-center text-muted mt-3">
                <?php echo lang('forget_pwd_info1') ?>, <a href="/login"><?php echo lang('forget_pwd_info2') ?></a> <?php echo lang('forget_pwd_info3') ?>
            </div>
        </div>
    </div>
    <script src="/assets/bootstrap/js/tabler.min.js"></script>ß
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script>
        function reset() {
            const name = $("#name").val();
            const email = $("#email").val();
            if (!name) {

            }
            $.post('/login/reset_password', {
                name: name,
                email: email,
            }, function(data) {
                console.log(data);
                if (data.status == 0) {
                    toastr.error(data.msg);
                } else {
                    alert(data.msg);
                    window.location = '/login';
                }
            }, 'json');
        }
    </script>
</body>

</html>