<script src="/assets/bootstrap/js/jquery.validate.min.js"></script>
<?php if ($lang == 'germany') : ?>
    <script src="/assets/js/validation/messages_de.js"></script>
<?php endif ?>

<div class="row">
    <div class="col-md-8 col-lg-6 m-auto pt-3 pb-2 mb-3">
        <form class="card" id="dataForm">

            <div class="card-body">

                <div class="col-12">
                    <label for="name"><?php echo lang('password.old'); ?></label>
                    <input type="password" class="form-control" id="old_password" name="old_password" required />
                </div>
                <div class="col-12">
                    <label for="name"><?php echo lang('password.new'); ?></label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required />
                </div>
                <div class="col-12">
                    <label for="name"><?php echo lang('password.confirm'); ?></label>
                    <input type="password" class="form-control" name="confirm_password" required />
                </div>


            </div>
            <div class="card-footer">
                <div class="col-12">
                    <button class="btn btn-primary" type="submit"><?php echo lang('button.save'); ?></button>
                    <a class="btn btn-secondary" href="/"><?php echo lang('button.cancel'); ?></a>

                </div>
            </div>

        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#dataForm").validate({
            submitHandler: function(form) {

                var params = new FormData($('#dataForm')[0]);

                $.ajax({
                    url: '/user/doResetPassword',
                    type: 'POST',
                    data: params,
                    dataType: "json",
                    success: function(data) {
                        if (data.code != 0) {
                            toastr.error(data.msg);
                        } else {
                            console.log(data.msg);
                            localStorage.setItem("Status", JSON.stringify({
                                type: 'success',
                                message: data.msg
                            }));
                            window.location = '/';
                        }
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                })
            },
            rules: {
                old_password: {
                    required: true,
                    remote: {
                        url: "/user/doVerifyPassword",
                        data: {
                            password: function() {
                                return $("#old_password").val();
                            },
                        }
                    },
                },
                confirm_password: {
                    equalTo: "#new_password"
                }
            }

        });
    })
</script>