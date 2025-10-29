<div class="container-fluid">

    <div class="page-body">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card card-table table-responsive">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo lang('ssp_hourly') ?></h3>
                        <table class="table table-striped table-responsive" data-toggle="table" data-url="/SspController/getHourlyData" data-sort-name="request_at" data-sort-order="desc" data-pagination="false">
                            <thead>
                                <tr>
                                    <th data-field="request_at"><?php echo lang('datetime'); ?> (Datetime UTC)</th>
                                    <th data-field="player_count"><?php echo lang('players_count'); ?></th>
                                    <th data-field="request_count"><?php echo lang('requests_count'); ?></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card card-table table-responsive">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo lang('ssp_daily') ?></h3>
                        <table class="table table-striped table-responsive" data-toggle="table" data-url="/SspController/getDailyData" data-sort-name="request_at" data-sort-order="desc" data-pagination="false">
                            <thead>
                                <tr>
                                    <th data-field="request_at"><?php echo lang('datetime'); ?></th>
                                    <th data-field="player_count"><?php echo lang('players_count'); ?></th>
                                    <th data-field="request_count"><?php echo lang('requests_count'); ?></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card card-table table-responsive">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo lang('ssp_player_log') ?></h3>
                        <!--
                        <div class='pb-2'>
                            <form class="row align-items-center justify-content-end" id='toolbar'>
                                <div class="col-auto row gx-1">
                                    <div class="col-auto">
                                        <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo date("Y-m-d", strtotime('-1 month')); ?>">
                                    </div>
                                    <div class="col-auto">
                                        <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo date("Y-m-d", time()); ?>">
                                    </div>
                                </div>
                                <div class="col-auto row ">
                                    <label class="form-label col-auto col-form-label"><?php echo lang('player'); ?></label>
                                    <div class="col">
                                        <input type="text" name="player_filter" class="form-control" placeholder="name or sn">
                                    </div>
                                </div>
                                <div class="col-auto row">
                                    <label class="form-label col-auto col-form-label"><?php echo lang('campaign'); ?></label>
                                    <div class="col">
                                        <input type="text" name="compaign_filter" class="form-control" placeholder="">
                                    </div>
                                </div>
                                <div class="col-auto row">
                                    <label class="form-label col-auto col-form-label"><?php echo lang('media'); ?></label>
                                    <div class="col">
                                        <input type="text" name="media_filter" class="form-control" placeholder="">
                                    </div>
                                </div>
                            </form>
                        </div>
                    -->
                        <table id='table' class="table table-striped table-responsive" data-toggle="table" data-url="/SspController/getPlayerLogData" data-sort-name="request_at" data-sort-order="desc" data-pagination="false">
                            <thead>
                                <tr>
                                    <th data-field="created_at"><?php echo lang('date'); ?>(Datetime UTC)</th>
                                    <th data-field="player"><?php echo lang('player'); ?></th>
                                    <th data-field="sn"><?php echo lang('sn'); ?></th>
                                    <th data-field="campaign"><?php echo lang('campaign'); ?></th>
                                    <th data-field="media"><?php echo lang('media'); ?></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function nameFormatter(value, row, index) {
        ret = value;
        <?php if ($auth == 5) : ?>
            ret = `	<a href="/criteria/edit?id=${row.id}" class="link-primary">
                   ${value}
                </a>`
        <?php endif ?>
        return ret;
    }
</script>