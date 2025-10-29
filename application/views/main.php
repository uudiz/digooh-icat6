<html xmlns="http://www.w3.org/1999/xhtml">

<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Untitled Document</title>
        <link href="/static/css/reset.css" rel="stylesheet" type="text/css" />
        <link href="/static/css/buttons.css" rel="stylesheet" type="text/css" />
</head>

<body>
        <div class="home-icon">
                <ul>
                        <?php if ($pid) : ?>
                                <li>
                                        <dl>
                                                <dt><a href="/player"><img src="/images/icons/icon-02.gif" width="100" height="100" alt="Equipment">
                                                                <p><?php echo lang('player'); ?><?php if ($auth > 1) : ?>(<?php echo $player_count; ?>)<?php endif; ?></p>
                                                        </a></dt>
                                                <dd>
                                                        <?php echo lang('player.desc'); ?>
                                                </dd>
                                        </dl>
                                </li>

                                <li>
                                        <dl>
                                                <dt><a href="/media/images"><img src="/images/icons/icon-03.gif" width="100" height="100" alt="Meaia">
                                                                <p><?php echo lang('media'); ?><?php if ($auth > 1) : ?>(<?php echo $media_count; ?>)<?php endif; ?></p>
                                                        </a></dt>
                                                <dd>
                                                        <?php echo lang('media.desc'); ?>
                                                </dd>
                                        </dl>
                                </li>
                        <?php else : ?>
                                <?php if ($auth > $this->config->item('auth_franchise')) : ?>
                                        <li>
                                                <dl>
                                                        <dt><a href="/criteria"><img src="/images/icons/icon-04.gif" width="100" height="100" alt="Criteria">
                                                                        <p><?php echo lang('criteria'); ?><?php if ($auth > 1) : ?>(<?php echo $criteria_count; ?>)<?php endif ?></p>
                                                                </a></dt>
                                                        <dd>
                                                                <?php echo lang('criteria.desc'); ?>
                                                        </dd>
                                                </dl>
                                        </li>

                                        <li>
                                                <dl>
                                                        <dt><a href="/tag"><img src="/images/icons/icon-05.gif" width="100" height="100" alt="Tag">
                                                                        <p><?php echo lang('tag'); ?><?php if ($auth > 1) : ?>(<?php echo $tag_count; ?>)<?php endif; ?></p>
                                                                </a></dt>
                                                        <dd>
                                                                <?php echo lang('tag.desc'); ?>
                                                        </dd>
                                                </dl>
                                        </li>
                                <?php endif; ?>
                                <li>
                                        <dl>
                                                <dt><a href="/player"><img src="/images/icons/icon-02.gif" width="100" height="100" alt="Equipment">
                                                                <p><?php echo lang('player'); ?><?php if ($auth > 1) : ?>(<?php echo $player_count; ?>)<?php endif; ?></p>
                                                        </a></dt>
                                                <dd>
                                                        <?php echo lang('player.desc'); ?>
                                                </dd>
                                        </dl>
                                </li>

                                <li>
                                        <dl>
                                                <dt><a href="/media/images"><img src="/images/icons/icon-03.gif" width="100" height="100" alt="Meaia">
                                                                <p><?php echo lang('media'); ?><?php if ($auth > 1) : ?>(<?php echo $media_count; ?>)<?php endif; ?></p>
                                                        </a></dt>
                                                <dd>
                                                        <?php echo lang('media.desc'); ?>
                                                </dd>
                                        </dl>
                                </li>



                                <?php if ($auth >= $FRANCHISE) : ?>
                                        <li>
                                                <dl>
                                                        <dt><a href="/playback"><img src="/images/icons/icon-08.gif" width="100" height="100" alt="Feedback">
                                                                        <p><?php echo lang('playback'); ?><?php if ($auth > 1) : ?>(<?php echo $playback_count; ?>)<?php endif; ?></p>
                                                                </a></dt>
                                                        <dd>
                                                                <?php echo lang('playback.desc'); ?>
                                                        </dd>
                                                </dl>
                                        </li>
                                <?php endif; ?>
                </ul>
        <?php endif ?>
        </div>
</body>

</html>