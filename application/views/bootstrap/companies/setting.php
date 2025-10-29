<div class="row">
    <div class="col-md-10 col-lg-8 m-auto pt-3 pb-2 mb-3">
        <form class="card" id="dataForm">
            <div class="card-body">
                <div class="card mb-3">
                    <div class="card-body">
                        <h3 class="card-title">Offline Email</h3>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-auto">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="offlineEmailFlag" <?php if ($company->offline_email_flag) : ?>checked="checked" <?php endif; ?>>
                                        <span class="form-check-label"><?php echo lang('offline.email.tip'); ?></span>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <div class="input-group input-group-sm">
                                        <input type="number" min="5" id="emailinterval" name="offline_email_interval" class="form-control " value="<?php echo $company->offline_email_inteval; ?>" style="width:50px" />
                                        <span class="input-group-text">
                                            minutes
                                        </span>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <span>to user</span>
                                </div>
                            </div>
                            <div>
                                <div class="col-auto">
                                    <select id="notify_user_1" name='users_grp_1[]' class="form-select select2" multiple data-placeholder="<?php echo lang('user') ?>">

                                        <?php foreach ($users as $user) : ?>
                                            <option value="<?php echo $user->id; ?>" <?php if (isset($company->users1) && is_array($company->users1)  && in_array($user->id, $company->users1)) : ?>selected<?php endif; ?>><?php echo $user->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-auto">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="offlineEmailFlag2" <?php if ($company->offline_email_flag2) : ?>checked="checked" <?php endif; ?>>
                                        <span class="form-check-label"><?php echo lang('offline.email.tip'); ?></span>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <div class="input-group input-group-sm">
                                        <input type="number" min="5" id="emailinterval" name="offline_email_interval2" class="form-control " value="<?php echo $company->offline_email_inteval2; ?>" style="width:50px" />
                                        <span class="input-group-text">
                                            minutes
                                        </span>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <span>to user</span>
                                </div>
                            </div>
                            <div>
                                <div class="col-auto">
                                    <select id="notify_user_2" name='users_grp_2[]' class="form-select select2" multiple data-placeholder="<?php echo lang('user') ?>">

                                        <?php foreach ($users as $user) : ?>
                                            <option value="<?php echo $user->id; ?>" <?php if (isset($company->users2) && is_array($company->users2)  && in_array($user->id, $company->users1)) : ?>selected<?php endif; ?>><?php echo $user->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                <?php if ($this->config->item("has_sensor")) : ?>
                    <div class="mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">Notification Email</h3>

                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notificationEmailFlag" <?php if ($company->notification_email_flag) : ?>checked="checked" <?php endif; ?>>
                                    <span class="form-check-label"><?php echo lang('notification.email.tip'); ?></span>
                                </label>

                                <div class="col-auto">
                                    <select id="users_grp_notification" name='users_grp_notification[]' class="form-select select2" multiple>

                                        <?php foreach ($users as $user) : ?>
                                            <option value="<?php echo $user->id; ?>" <?php if (isset($company->users3) && is_array($company->users3)  && in_array($user->id, $company->users3)) : ?>selected<?php endif; ?>><?php echo $user->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endif ?>


                <div class="mb-3">
                    <label for="name"><?php echo lang('weather.format'); ?></label>
                    <select id="weatherFormat" name="weather_format" class="form-select">
                        <option value="f" <?php if ($company->weather_format == 'f') : ?>selected="selected" <?php endif; ?>><?php echo lang('weather.format.fahrenheit'); ?></option>
                        <option value="c" <?php if ($company->weather_format == 'c') : ?>selected="selected" <?php endif; ?>><?php echo lang('weather.format.celsius'); ?></option>
                    </select>
                </div>


            </div>
            <div class="card-footer">
                <div class="col-12">
                    <a href="#" class="btn btn-primary" onclick="doSave()"><?php echo lang('button.save'); ?></a>
                    <a class="btn btn-secondary" href="/"><?php echo lang('button.cancel'); ?></a>


                </div>
            </div>

        </form>
    </div>
</div>

<script>
    function doSave() {
        var params = new FormData($('#dataForm')[0]);
        params.append("offline_email_flag", $("#offlineEmailFlag").is(':checked') ? 1 : 0);
        params.append("offline_email_flag2", $("#offlineEmailFlag2").is(':checked') ? 1 : 0);
        params.append("notification_email_flag", $("#notificationEmailFlag").is(':checked') ? 1 : 0);


        $.ajax({
            url: '/company/doSaveSettings',
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

    }
    $(document).ready(function() {
        $(".select2").select2({
            theme: "bootstrap-5",
            width: '100%',
        });

    })
</script>