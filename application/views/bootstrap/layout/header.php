<header class="sticky-top navbar navbar-expand-md navbar-light d-print-none" style="background-color:<?php echo isset($bg_color) && $bg_color ? $bg_color : '#f2e6ff' ?>;">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <a href="/">
                <img src="<?php echo isset($custom_logo) ? $custom_logo : '/assets/logos/logo-digooh.svg' ?> " width="110" height="32" alt="Digooh" class="navbar-brand-image" onerror="this.src='/assets/logos/logo-digooh.svg'">
            </a>
        </h1>
        <div class="navbar-nav flex-row order-md-last">
            <?php if ($auth == 5 && !$pid && (isset($unproved_media_cnt) && $unproved_media_cnt)) : ?>
                <div class="nav-item dropdown d-none d-md-flex">
                    <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show notifications" id="notification-zone">
                        <!-- Download SVG icon from http://tabler-icons.io/i/bell -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                            <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                        </svg>
                        <span class="badge bg-red"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Notifications</h3>
                            </div>
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col text-truncate">
                                        <a href="/media?fromNotification=1" class="text-body d-block"><?php echo $unproved_media_cnt . " media files waiting for your approval" ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <div class="nav-item dropdown d-none d-md-flex ">
                <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="language">
                    <span id="lang_icon" class="flag <?php echo $lang == 'germany' ? "flag-country-de" : "flag-country-gb" ?> "></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="#" class="dropdown-item set-language" id="english" onclick="switchLang('english')">
                        <span class="flag flag-country-gb nav-link-icon"></span>
                        <span class="nav-link-title">
                            <?php echo lang('lang.english') ?>
                        </span>

                    </a>
                    <a href="#" class="dropdown-item set-language" id="germany" onclick="switchLang('germany')">
                        <span class="flag flag-country-de nav-link-icon"></span>
                        <span class="nav-link-title">
                            <?php echo lang('lang.german') ?>
                        </span>

                    </a>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <span class="avatar avatar-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                        </svg>
                    </span>

                    <div class="d-none d-xl-block ps-2">
                        <div> <?php echo $username; ?> </div>
                        <div class="mt-1 small text-muted"></div>
                    </div>

                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <span></span>
                    <?php if ($auth == $ADMIN) : ?>
                        <a href="/company/settings" class="dropdown-item"><?php echo lang('account'); ?></a>
                    <?php endif; ?>
                    <a href="/user/resetPassword" class="dropdown-item"><?php echo lang('password'); ?></a>
                    <div class="dropdown-divider"></div>

                    <a href="/login/doLogout" class="dropdown-item">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"></path>
                                <path d="M7 12h14l-3 -3m0 6l3 -3"></path>
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            <?php echo lang('button.logout'); ?>
                        </span>
                    </a>
                </div>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="navbar-menu">
            <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">


                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <h3><i class="bi bi-house"></i></h3>
                            </span>
                            <span class="nav-link-title">
                                Home
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/player">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <h3><i class="dig-display"></i></h3>
                            </span>
                            <span class="nav-link-title">
                                <?php echo lang('player'); ?>
                            </span>
                        </a>
                    </li>
                    <?php if ($auth == 10) : ?>
                        <?php if ($auth /*== $SYSTEM*/) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/company">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="bi bi-building"></i>
                                    </span>
                                    <span class="nav-link-title">
                                        <?php echo lang('company'); ?>
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/user">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="bi bi-people"></i>
                                    </span>
                                    <span class="nav-link-title">
                                        <?php echo lang('user'); ?>
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/software">
                                    <?php echo lang('software'); ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/Firmware_controller">
                                    <?php echo lang('firmware') . " " . lang('upgrade'); ?>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="/powersController">
                                    <?php echo lang('off.times'); ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/logger">
                                    <?php echo lang('log'); ?>
                                </a>
                            </li>
                            <?php if ($this->config->item('ssp_feature')) : ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/SspController">
                                        <?php echo lang('ssp_status'); ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php else : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/campaign">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <h3><i class="dig-campaign"></i></h3>
                                </span>
                                <span class="nav-link-title">
                                    <?php echo lang('campaign'); ?>
                                </span>
                            </a>
                        </li>

                        <?php if ($auth >= 4 && !$pid) : ?>
                            <li class="nav-item">
                                <a class="nav-link " href="/criteria">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <h3><i class="bi bi-tags"></i></h3>
                                    </span>
                                    <span class="nav-link-title">
                                        <?php echo lang('criteria'); ?>
                                    </span>
                                </a>
                            </li>
                        <?php endif ?>
                        <li class="nav-item">
                            <a class="nav-link " href="/media">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <h3><i class="bi bi-film"></i></h3>
                                </span>
                                <span class="nav-link-title">
                                    <?php echo lang('media'); ?>
                                </span>
                            </a>
                        </li>

                        <?php if ($auth >= $ADMIN) : ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <h3><i class="bi bi-gear"></i></h3>
                                    </span>
                                    <span class="nav-link-title">
                                        <?php echo lang('setup'); ?>
                                    </span>
                                </a>

                                <div class="dropdown-menu">

                                    <?php if ($auth >= $ADMIN && !$pid) : ?>
                                        <a class="dropdown-item" href="/tag">
                                            <?php echo lang('tag'); ?>
                                        </a>
                                        <a class="dropdown-item" href="/timersController">
                                            <?php echo lang('timer.settings'); ?>
                                        </a>
                                        <a class="dropdown-item" href="/ftpSites">
                                            <?php echo lang('ftp'); ?>
                                        </a>

                                    <?php endif ?>
                                    <?php if ($auth >= $ADMIN || $auth == 2) : ?>
                                        <a class=" dropdown-item" href="/folder">
                                            <?php echo lang('folder'); ?>
                                        </a>
                                    <?php endif ?>

                                    <?php if (!$pid) : ?>
                                        <a class="dropdown-item" href="/configxml">
                                            <?php echo lang('device.setup'); ?>
                                        </a>
                                        <?php if ($this->config->item('ssp_feature')) : ?>
                                            <a class="dropdown-item" href="/criteriaSSP">
                                                <?php echo lang('ssp.criteria'); ?>
                                            </a>
                                            <a class="dropdown-item" href="/tagSSP">
                                                <?php echo lang('ssp.tags'); ?>
                                            </a>
                                        <?php endif ?>
                                    <?php endif ?>
                                    <a class="dropdown-item" href="/user">
                                        <?php echo lang('user'); ?>
                                    </a>
                                </div>
                            </li>
                        <?php endif; ?>
                        <?php if ($auth > 1) : ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="bi bi-sliders"></i>
                                    </span>
                                    <span class="nav-link-title">
                                        <?php echo lang('advanced'); ?>
                                    </span>
                                </a>

                                <div class="dropdown-menu">
                                    <?php if ($auth > $FRANCHISE && !$pid) : ?>
                                        <a class="dropdown-item" href="/playback">
                                            <?php echo lang('playback'); ?>
                                        </a>
                                        <?php if ($auth == 5) : ?>
                                            <a class="dropdown-item" href="/software">
                                                <?php echo lang('software'); ?>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if ($auth == 4 || $auth == $ADMIN) : ?>
                                        <a class="dropdown-item" href="/usage">
                                            <?php echo lang('player.usage'); ?>
                                        </a>
                                        <a class="dropdown-item" href="/powersController">
                                            <?php echo lang('off.times'); ?>
                                        </a>

                                    <?php endif; ?>

                                </div>

                            </li>
                        <?php endif ?>
                    <?php endif ?>
                </ul>

            </div>
        </div>
    </div>
</header>