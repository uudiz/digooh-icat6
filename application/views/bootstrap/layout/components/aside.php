<style>
    .mia-brand-image {
        height: 2.5rem;
        width: auto;
    }
</style>
<div class="container-fluid">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
        <span class="navbar-toggler-icon"></span>
    </button>
    <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
        <a href="/">
            <img src="<?php echo isset($custom_logo) ? $custom_logo : '/assets/logos/default_logo.png' ?> " alt="logo" class="mia-brand-image" onerror="this.src='/assets/logos/default_logo.png'">
        </a>
    </h1>

    <div class="navbar-nav flex-row d-lg-none">

        <div class="nav-item dropdown">
            <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                <span class="avatar avatar-sm"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-circle" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <circle cx="12" cy="12" r="9"></circle>
                        <circle cx="12" cy="10" r="3"></circle>
                        <path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855"></path>
                    </svg></span>
                <div class="d-none d-xl-block ps-2">
                    <div> <?php echo $username; ?></div>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <?php if ($auth == $ADMIN) : ?>
                    <a class="dropdown-item" target='dialog' href="/company/settings"> <?php echo lang('account'); ?></a>
                <?php endif; ?>
                <a class="dropdown-item" target='dialog' href="/sysconfig/password"><?php echo lang('password'); ?></a>

                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="/login/doLogout" class="btn btn-default tp-icon">
                    <i class="glyphicon glyphicon-log-out"></i>
                    <span><?php echo lang('button.logout'); ?></span>
                </a>
            </div>
        </div>
    </div>

    <div class="collapse navbar-collapse" id="navbar-menu">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="/">
                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <polyline points="5 12 3 12 12 3 21 12 19 12" />
                            <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                            <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                        </svg>
                    </span>
                    <span class="nav-link-title">
                        Home
                    </span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/player">
                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-desktop" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <desc>Download more icon variants from https://tabler-icons.io/i/device-desktop</desc>
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <rect x="3" y="4" width="18" height="12" rx="1"></rect>
                            <line x1="7" y1="20" x2="17" y2="20"></line>
                            <line x1="9" y1="16" x2="9" y2="20"></line>
                            <line x1="15" y1="16" x2="15" y2="20"></line>
                        </svg>
                    </span>
                    <span class="nav-link-title">
                        <?php echo lang('player'); ?>
                    </span>
                </a>
                <?php if ($this->config->item('has_peripherial') && $auth == 5) : ?>
                    <a class="nav-link" href="/peripheral_controller">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-desktop" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <rect x="3" y="4" width="18" height="12" rx="1"></rect>
                                <line x1="7" y1="20" x2="17" y2="20"></line>
                                <line x1="9" y1="16" x2="9" y2="20"></line>
                                <line x1="15" y1="16" x2="15" y2="20"></line>
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            <?php echo lang('peripherals'); ?>
                        </span>
                    </a>
                <?php endif ?>

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
                    <?php if ($this->config->item('has_sensor')) : ?>
                        <a class="nav-link" href="/Healthy_controller">
                            <?php echo lang('sensor_reports'); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ($this->config->item('has_activation')) : ?>
                        <a class="nav-link" href="/ActivationController">
                            NP211 Activation
                        </a>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/logger">
                            <?php echo lang('log'); ?>
                        </a>
                    </li>

                <?php endif; ?>
            <?php else : ?>
                <li class="nav-item">
                    <a class="nav-link" href="/playlist">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-list" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <line x1="9" y1="6" x2="20" y2="6"></line>
                                <line x1="9" y1="12" x2="20" y2="12"></line>
                                <line x1="9" y1="18" x2="20" y2="18"></line>
                                <line x1="5" y1="6" x2="5" y2="6.01"></line>
                                <line x1="5" y1="12" x2="5" y2="12.01"></line>
                                <line x1="5" y1="18" x2="5" y2="18.01"></line>
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            <?php echo lang('campaign'); ?>
                        </span>
                    </a>
                </li>

                <?php if ($auth >= 4 && !$pid) : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/templateController">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout-board-split" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <rect x="4" y="4" width="16" height="16" rx="2"></rect>
                                    <path d="M4 12h8"></path>
                                    <path d="M12 15h8"></path>
                                    <path d="M12 9h8"></path>
                                    <path d="M12 4v16"></path>
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                <?php echo lang('template'); ?>
                            </span>
                        </a>
                    </li>
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
                                    <?php echo lang('categories'); ?>
                                </a>
                                <a class="dropdown-item" href="/timersController">
                                    <?php echo lang('timer.settings'); ?>
                                </a>



                                <a class="dropdown-item" href="/ftpSites">
                                    <?php echo lang('ftp'); ?>
                                </a>

                                <a class="dropdown-item" href="/webpages">
                                    <?php echo lang('webpage'); ?>
                                </a>
                                <?php if ($this->config->item('has_sensor') && $auth >= 5) : ?>
                                    <a class="dropdown-item" href="/threshold_controller">
                                        <?php echo lang('sensor_thresholds'); ?>
                                    </a>
                                <?php endif ?>

                            <?php endif ?>
                            <?php if ($auth >= $ADMIN || $auth == 2) : ?>
                                <a class=" dropdown-item" href="/folder">
                                    <?php echo lang('folder'); ?>
                                </a>
                            <?php endif ?>

                            <a class="dropdown-item" href="/user">
                                <?php echo lang('user'); ?>
                            </a>

                            <a class="dropdown-item" href="/Status_controller">
                                <?php echo lang('evCharger') . " " . lang("setup"); ?>
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
                                    <a class="dropdown-item" href="/configxml">
                                        <?php echo lang('device.setup'); ?>
                                    </a>
                                    <a class="dropdown-item" href="/software">
                                        <?php echo lang('software'); ?>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if ($auth == 4 || $auth == $ADMIN) : ?>
                                <a class="dropdown-item" href="/powersController">
                                    <?php echo lang('off.times'); ?>
                                </a>


                                <?php if ($this->config->item('has_sensor')) : ?>
                                    <a class="dropdown-item" href="/healthy_controller">
                                        <?php echo lang('sensor_reports'); ?>
                                    </a>

                                <?php endif; ?>
                            <?php endif; ?>



                        </div>

                    </li>
                <?php endif ?>
            <?php endif ?>
            <?php if ($this->config->item("new_campaign_user")) : ?>
                <?php if ($auth == 1) : ?>
                    <li class="nav-item">
                        <a class="nav-link " href="/folder">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <h3><i class="bi bi-folder"></i></h3>
                            </span>
                            <span class="nav-link-title">
                                <?php echo lang('folder'); ?>
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/templateController">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout-board-split" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <rect x="4" y="4" width="16" height="16" rx="2"></rect>
                                    <path d="M4 12h8"></path>
                                    <path d="M12 15h8"></path>
                                    <path d="M12 9h8"></path>
                                    <path d="M12 4v16"></path>
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                <?php echo lang('template'); ?>
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="/playback">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <h3><i class="bi bi-card-list"></i></h3>
                            </span>
                            <span class="nav-link-title">
                                <?php echo lang('playback'); ?>
                            </span>
                        </a>
                    </li>

                <?php endif; ?>
            <?php endif ?>
        </ul>

    </div>
</div>