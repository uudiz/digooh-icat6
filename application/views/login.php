<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo lang('login_title') ?></title>
    <link type="text/css" rel="stylesheet" href="/static/css/login.css" />

    <script>
        //console.log(window);
        if (window.name == 'main-frame') {
            window.parent.location.href = window.location.href;
            //window.location.reload();
        }
    </script>
    <style>
        .custom-main-bg {
            <?php
            if ($this->config->item('mia_system_set') >= $this->config->item('mia_system_np200')) {
            ?>background: transparent url("/static/css/images/login/main-bg.png") no-repeat scroll 0% 0%;
            <?php
            } else {
            ?>background: transparent url("/static/css/images/login/3main-bg.png") no-repeat scroll 0% 0%;
            <?php
            }
            ?>
        }
    </style>
</head>

<body class="body-bg">
    <div class="main-bg custom-main-bg">
        <div class="login-panel">
            <div class="login-message-content">
                <?php echo validation_errors('<div class="error"><div>', '</div></div>'); ?>
                <?php if (isset($errMsg)) : ?>
                    <div class="error">
                        <div><?php echo $errMsg; ?></div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="login-content">
                <form action="/login/doLogin" method="post" id="myForm" name="myForm">
                    <table width="100%" border="0" cellspacing="0" cellpadding="00">
                        <tr>
                            <td>
                                <?php echo lang('user_name') ?>:
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" class="input-style" id="username" name="username" <?php if (isset($username)) {
                                                                                                            echo "value=" . "\"" . $username . "\"";
                                                                                                        } else {
                                                                                                            set_value("username");
                                                                                                        } ?> />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo lang('password') ?>:
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="password" class="input-style" id="password" name="password" <?php if (isset($password)) {
                                                                                                                echo "value=" . "\"" . $password . "\"";
                                                                                                            } ?> />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="submit" name="submit" id="submit" value="" class="btn-1" /><input type="reset" name="reset" value="" class="btn-2" />
                            </td>
                        </tr>

                    </table>
                    <!--<a href="/login/getpass"><?php echo lang('forget_pwd_info1'); ?></a>-->
                    <input type="hidden" id="redirect" name="redirect" value="<?php echo isset($redirect) ? 1 : 0; ?>" />
                    <input type="hidden" id="lang" name="lang" value="<?php echo isset($redirect) ? 1 : 0; ?>" />
                </form>
            </div>
            <div class="foot-text">
                Copyright © 2021 All rights reserved
            </div>
        </div>
    </div>
    <script>
        var username = document.getElementById('username').value;
        var password = document.getElementById('password').value;
        var redirect = document.getElementById('redirect').value;
        if ((username != '' && password != '') || (username != '' && redirect)) {
            setTimeout(function() {
                document.getElementById('submit').click();
            }, 200);
        }
    </script>
</body>

</html>