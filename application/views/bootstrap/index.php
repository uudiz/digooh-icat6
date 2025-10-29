<div class="container-fluid">
    <!-- Page title -->

    <div class="page-body">
        <div class="row row-deck row-cards mb-4">
            <div class="col-sm-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card card-sm" onclick="location.href='/player';" style="cursor:pointer;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-blue text-white avatar ">
                                    <i class="dig-display"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    <?php echo lang('player') ?>
                                </div>

                                <div class="badge bg-info">
                                    <?php echo $online_cnt ?> / <?php echo $players_cnt ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($auth <= 5) : ?>
                <div class="col-sm-6 col-lg-3 col-xl-2">
                    <div class="card card-sm" <?php if ($this->config->item("with_template")) : ?> onclick="location.href='/playlist';" <?php else : ?>onclick="location.href='/campaign';" <?php endif ?> style="cursor:pointer;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-azure text-white avatar">
                                        <i class="dig-campaign"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        <?php echo lang('campaign') ?>
                                    </div>
                                    <div class="badge bg-info">
                                        <?php echo $campaigns_cnt ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 col-xl-2">
                    <div class="card card-sm" onclick="location.href='/media';" style="cursor:pointer;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-lime text-white avatar">
                                        <i class="bi bi-film"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        <?php echo lang('media') ?>
                                    </div>
                                    <div class="badge bg-info">
                                        <?php echo $media_cnt ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <?php if ($auth > 2 && $auth <= 5 && !$pid) : ?>
                <div class="col-sm-6 col-lg-3 col-xl-2">
                    <div class="card card-sm" onclick="location.href='/criteria';" style="cursor:pointer;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-teal text-white avatar">
                                        <i class="bi bi-tags"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        <?php echo lang('criteria') ?>
                                    </div>
                                    <div class="badge bg-info">
                                        <?php echo $criteria_cnt ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <?php if ($auth <= 5) : ?>


                <?php if ($auth > 2 && !$pid) : ?>
                    <div class="col-sm-6 col-lg-3 col-xl-2">
                        <div class="card card-sm" onclick="location.href='/playback';" style="cursor:pointer;">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="bg-blue text-white avatar">
                                            <i class="bi bi-clipboard-data"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="font-weight-medium">
                                            <?php echo lang('playback') ?>
                                        </div>
                                        <?php if (isset($playback_cnt)) : ?>
                                            <div class="badge bg-info">
                                                <?php echo $playback_cnt ?>
                                            </div>
                                        <?php endif ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            <?php endif ?>
            <?php if ($auth == 10) : ?>
                <div class="col-sm-6 col-lg-3 col-xl-2">
                    <div class="card card-sm" onclick="location.href='/company';" style="cursor:pointer;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-blue text-white avatar">
                                        <i class="bi bi-building"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        <?php echo lang('company') ?>
                                    </div>
                                    <div class="badge bg-info">
                                        <?php echo $companies_cnt ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 col-xl-2">
                    <div class="card card-sm" onclick="location.href='/user';" style="cursor:pointer;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-blue text-white avatar">
                                        <i class="bi bi-people"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        <?php echo lang('user') ?>
                                    </div>
                                    <div class="badge bg-info">
                                        <?php echo $users_cnt ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endif ?>
        </div>

        <?php if ($this->config->item('has_sensor') && $auth >= 5) : ?>
            <div class="row row-cards">
                <div class="col-md-5">
                    <div class="card" onclick="location.href='/healthy_controller';" style="cursor:pointer;">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo lang('sensor_chart') ?></h3>
                            <div id="chart-uptime-incidents"></div>
                        </div>
                    </div>
                </div>

            </div>
        <?php endif ?>
    </div>
</div>
<script src="/assets/js/apexcharts.min.js" defer></script>
<script>
    // @formatter:off
    <?php if ($this->config->item('has_sensor') && $auth >= 5) : ?>
        document.addEventListener("DOMContentLoaded", function() {
            var options = {
                defaultLocale: 'de',
                locales: [{
                    "name": "de",
                    "options": {
                        "months": ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                        "shortMonths": ["Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez"],
                        "days": ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
                        "shortDays": ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"],
                        "toolbar": {
                            "exportToSVG": "Download SVG",
                            "exportToPNG": "Download PNG",
                            "menu": "Menu",
                            "selection": "Selection",
                            "selectionZoom": "Selection Zoom",
                            "zoomIn": "Zoom In",
                            "zoomOut": "Zoom Out",
                            "pan": "Panning",
                            "reset": "Reset Zoom"
                        }
                    }
                }],
                chart: {
                    height: 240,
                    type: 'bar',
                    parentHeightOffset: 0,
                    toolbar: {
                        show: false,
                    },

                },
                plotOptions: {
                    bar: {
                        columnWidth: '50%',
                    }
                },
                dataLabels: {
                    enabled: false,
                },
                fill: {
                    opacity: 1,
                },
                series: [],
                grid: {
                    padding: {
                        top: -20,
                        right: 0,
                        left: -4,
                        bottom: -4
                    },
                    strokeDashArray: 4,
                },
                xaxis: {
                    labels: {
                        padding: 0,
                    },
                    tooltip: {
                        enabled: false
                    },
                    axisBorder: {
                        show: false,
                    },
                    type: 'datetime',
                },
                yaxis: {
                    labels: {
                        padding: 4
                    },
                    max: function(max) {
                        return max + 10
                    },

                },

                colors: [tabler.getColor("red")],
                legend: {
                    show: false,
                },
                noData: {
                    text: 'No data'
                },
                legend: {
                    show: false,
                },
            }

            var chart = new ApexCharts(
                document.getElementById('chart-uptime-incidents'),
                options
            );

            chart.render();
            // chart.setLocale('en');

            $.getJSON("/healthy_controller/getChatDate", function(response) {
                chart.updateSeries([{
                    name: 'incident count',
                    data: response
                }])
            });

        });
        // @formatter:on
    <?php endif ?>
</script>