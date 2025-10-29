<div class="container-fluid">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="navbar-nav flex-row order-md-last">
        <div></div>
        <div class="d-none d-md-flex">
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
                    <a href="/company/settings" class="dropdown-item"><?php echo lang('account'); ?></a>
                <?php endif; ?>
                <a href="/user/resetPassword" class="dropdown-item"><?php echo lang('password'); ?></a>

                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="/login/doLogout" class="btn btn-default tp-icon">
                    <i class="glyphicon glyphicon-log-out"></i>
                    <span><?php echo lang('button.logout'); ?></span>
                </a>
            </div>
        </div>
    </div>
    <div class="collapse navbar-collapse" id="navbar-menu">

    </div>
</div>