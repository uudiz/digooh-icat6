<div class="card">
    <ul id="areaTabs" class="nav nav-tabs pb-1" data-bs-toggle="tabs">
        <?php if (isset($area_list) && is_array($area_list)) : ?>
            <?php foreach ($area_list as $area) : ?>
                <?php
                if (
                    $area->area_type == $this->config->item('area_type_bg')
                    || $area->area_type == $this->config->item('area_type_date')
                    || $area->area_type == $this->config->item('area_type_time')
                    || $area->area_type == $this->config->item('area_type_weather')
                ) {
                    continue;
                }
                if ($area->area_type == $this->config->item('area_type_movie')) {
                    $title =  $this->lang->line('video');
                    $svg = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-movie" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <rect x="4" y="4" width="16" height="16" rx="2"></rect>
                    <line x1="8" y1="4" x2="8" y2="20"></line>
                    <line x1="16" y1="4" x2="16" y2="20"></line>
                    <line x1="4" y1="8" x2="8" y2="8"></line>
                    <line x1="4" y1="16" x2="8" y2="16"></line>
                    <line x1="4" y1="12" x2="20" y2="12"></line>
                    <line x1="16" y1="8" x2="20" y2="8"></line>
                    <line x1="16" y1="16" x2="20" y2="16"></line>
                </svg>';
                } else if ($area->area_type == $this->config->item('area_type_image')) {
                    if ($area->area_name == "pic1") {
                        $title =  $this->lang->line('image1');
                    } else if ($area->area_name == "pic2") {
                        $title =  $this->lang->line('image2');
                    } else if ($area->area_name == "pic3") {
                        $title =  $this->lang->line('image3');
                    } else if ($area->area_name == "pic4") {
                        $title =  $this->lang->line('image4');
                    }
                    $svg = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-photo" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <line x1="15" y1="8" x2="15.01" y2="8"></line>
                <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                <path d="M4 15l4 -4a3 5 0 0 1 3 0l5 5"></path>
                <path d="M14 14l1 -1a3 5 0 0 1 3 0l2 2"></path>
            </svg>';
                } else if ($area->area_type == $this->config->item('area_type_text')) {
                    $title =  $this->lang->line('text');
                    $svg = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-letter-t" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <line x1="6" y1="4" x2="18" y2="4"></line>
                <line x1="12" y1="4" x2="12" y2="20"></line>
                </svg>';
                } else if ($area->area_type == $this->config->item('area_type_webpage')) {
                    $title =  $this->lang->line('webpage');
                    $svg = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-chrome" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <desc>Download more icon variants from https://tabler-icons.io/i/brand-chrome</desc>
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <circle cx="12" cy="12" r="9"></circle>
                <circle cx="12" cy="12" r="3"></circle>
                <line x1="12" y1="9" x2="20.4" y2="9"></line>
                <line x1="12" y1="9" x2="20.4" y2="9" transform="rotate(120 12 12)"></line>
                <line x1="12" y1="9" x2="20.4" y2="9" transform="rotate(240 12 12)"></line>
            </svg>';
                } else if ($area->area_type == $this->config->item('area_type_logo')) {
                    $title =  $this->lang->line('logo');
                    $svg = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-letter-l" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <desc>Download more icon variants from https://tabler-icons.io/i/letter-l</desc>
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M7 4v16h10"></path>
                 </svg>';
                } else if ($area->area_type == $this->config->item('area_type_mask')) {
                    $title =  $this->lang->line('mask');
                    $svg = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-letter-m" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <desc>Download more icon variants from https://tabler-icons.io/i/letter-m</desc>
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M6 20v-16l6 14l6 -14v16"></path>
            </svg>';
                } else if ($area->area_type == $this->config->item('area_type_staticText')) {
                    $title =  $this->lang->line('static.text');
                    $svg = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clipboard-list" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path>
                <rect x="9" y="3" width="6" height="4" rx="2"></rect>
                <line x1="9" y1="12" x2="9.01" y2="12"></line>
                <line x1="13" y1="12" x2="15" y2="12"></line>
                <line x1="9" y1="16" x2="9.01" y2="16"></line>
                <line x1="13" y1="16" x2="15" y2="16"></line>
            </svg>';
                } else if ($area->area_type == $this->config->item('area_type_weather')) {
                    $title =  $this->lang->line('weather');
                    $svg = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cloud" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <desc>Download more icon variants from https://tabler-icons.io/i/cloud</desc>
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M6.657 18c-2.572 0 -4.657 -2.007 -4.657 -4.483c0 -2.475 2.085 -4.482 4.657 -4.482c.393 -1.762 1.794 -3.2 3.675 -3.773c1.88 -.572 3.956 -.193 5.444 .996c1.488 1.19 2.162 3.007 1.77 4.769h.99c1.913 0 3.464 1.56 3.464 3.486c0 1.927 -1.551 3.487 -3.465 3.487h-11.878"></path>
            </svg>';
                } else if ($area->area_type == $this->config->item('area_type_id')) {
                    $suffix = '';
                    $this->load->helper('chrome_logger');
                    chrome_log($area);

                    if (isset($area->idData->type)) {
                        chrome_log("type=" . $area->idData->type);
                        if ($area->idData->type == 0) {
                            $suffix =  $this->lang->line('charger');
                        } else if ($area->idData->type == 1) {
                            $suffix =  $this->lang->line('webpage');
                        } else if ($area->idData->type == 2) {
                            $suffix =  $this->lang->line('price_text');
                        } else if ($area->idData->type == 3) {
                            $suffix =  $this->lang->line('free_charger_count');
                        } else if ($area->idData->type == 4) {
                            $suffix =  $this->lang->line('register_price');
                        }
                    }
                    chrome_log($suffix);
                    $title =  $area->name . "-" . $suffix;
                    chrome_log($title);
                    $svg = "<p>" . $area->name . " <sub>" . $suffix . "</sub></p>";
                }

                ?>
                <li class="nav-item">
                    <a href="<?php echo $area->area_type == $this->config->item('area_type_id') ? '#idarea-' . $area->id : '#area' . $area->id ?>" class="nav-link" data-bs-toggle="tab" title="<?php echo $title; ?>">
                        <?php echo $svg; ?>
                    </a>
                </li>
            <?php endforeach ?>
        <?php endif; ?>
    </ul>
    <div class="card-body">

        <div class="tab-content">
            <?php if (isset($area_list) && is_array($area_list)) : ?>
                <?php foreach ($area_list as $area) : ?>
                    <?php if (
                        $area->area_type == $this->config->item('area_type_movie')
                        || $area->area_type == $this->config->item('area_type_image')
                        || $area->area_type == $this->config->item('area_type_mask')
                        || $area->area_type == $this->config->item('area_type_logo')
                    ) : ?>
                        <div class="tab-pane show" id="<?php echo "area" . $area->id ?>">
                            <div class="row float-end btn-list g-1 pb-1">
                                <div class="col-auto">
                                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#mediaModal" <?php if ($area->area_type != $this->config->item('area_type_movie')) : ?>data-img-only='1' <?php endif ?> <?php if ($area->area_type == $this->config->item('area_type_logo')) : ?>data-single-sel='1' <?php endif ?>>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-movie" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <rect x="4" y="4" width="16" height="16" rx="2"></rect>
                                            <line x1="8" y1="4" x2="8" y2="20"></line>
                                            <line x1="16" y1="4" x2="16" y2="20"></line>
                                            <line x1="4" y1="8" x2="8" y2="8"></line>
                                            <line x1="4" y1="16" x2="8" y2="16"></line>
                                            <line x1="4" y1="12" x2="20" y2="12"></line>
                                            <line x1="16" y1="8" x2="20" y2="8"></line>
                                            <line x1="16" y1="16" x2="20" y2="16"></line>
                                        </svg>
                                        <?php echo lang('media') ?>
                                    </button>
                                </div>
                                <?php if ($auth == 5) : ?>
                                    <div class="col-auto">
                                        <button class="btn btn-outline-primary" type="button" title="upload" data-bs-toggle="modal" data-bs-target="#uploadModal" data-target-table="<?php echo "area" . $area->id . "Table" ?>" <?php if ($area->area_type != $this->config->item('area_type_movie')) : ?>data-img-only='1' <?php endif ?>>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cloud-upload" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1"></path>
                                                <polyline points="9 15 12 12 15 15"></polyline>
                                                <line x1="12" y1="12" x2="12" y2="21"></line>
                                            </svg>
                                            <?php echo lang('button.upload') ?>
                                        </button>
                                    </div>
                                <?php endif ?>
                                <div class="col-auto">
                                    <button class="btn btn-outline-primary" type="button" onclick="delete_all_media()" title="<?php echo lang('delete') ?>">
                                        <svg xmlns=" http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                        <?php echo lang('delete') ?>
                                    </button>
                                </div>
                            </div>
                            <table id="<?php echo "area" . $area->id . "Table" ?>" data-area-id="<?php echo $area->id ?>" class="table table-responsive table-vcenter table-media" data-pagination='false' data-use-row-attr-func="true" data-reorderable-rows="true">
                                <thead>
                                    <tr>
                                        <th data-field="tiny_url" data-formatter="previewFormatter"><?php echo lang('media.image') ?></th>
                                        <?php if ($this->config->item("with_transition")) : ?>
                                            <th data-field="name" data-formatter="mediaNameFormatter"><?php echo lang('media_name'); ?></th>
                                        <?php else : ?>
                                            <th data-field="name"><?php echo lang('media_name'); ?>
                                            <?php endif; ?>
                                            <th data-field="play_time"><?php echo lang('play_time'); ?></th>
                                            <?php if ($this->config->item("with_transition")) : ?>
                                                <th data-field="transmode" data-formatter="transFormatter"><?php echo lang('transition_mode'); ?></th>
                                            <?php endif ?>
                                            <th data-field="status" data-formatter="excludeFormatter">Exclude</th>
                                            <th data-field="start_date"><?php echo lang('start.date'); ?></th>
                                            <th data-field="end_date"><?php echo lang('end.date'); ?></th>
                                            <th data-formatter="operateFormatter"><?php echo lang('operate'); ?></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    <?php endif ?>
                    <?php if ($area->area_type == $this->config->item('area_type_webpage')) :  ?>
                        <div class="modal fade" id="areaMceTextModal" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <label class="form-label"><?php echo lang('text') ?></label>
                                        <textarea class="form-control" id='mce_text'></textarea>
                                        <input type="hidden" id="area_mce_id" />
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn me-auto" data-bs-dismiss="modal"><?php echo lang('button.cancel') ?></button>
                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="update-mceText"><?php echo lang('button.save') ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane show" id="<?php echo "area" . $area->id ?>">
                            <?php
                            //$this->load->view("bootstrap/template_campaigns/webpage_model");
                            $this->load->view("bootstrap/webpages/mce_modal");
                            ?>
                            <div class="row float-end btn-list g-1 pb-1">
                                <div class="col-auto">
                                    <!--  <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#webpageModal">
                                        <i class="bi bi-plus-lg"></i>
                                        <?php echo lang('create'); ?>
                                    </button>
                                    -->
                                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#mceModal">
                                        <i class="bi bi-plus-lg"></i>
                                        <?php echo lang('create'); ?>
                                    </button>
                                </div>
                            </div>
                            <table id="<?php echo "area" . $area->id . "Table" ?>" data-area-id="<?php echo $area->id ?>" class="table table-responsive table-vcenter table-webpage" data-pagination='false' data-use-row-attr-func="true" data-reorderable-rows="true">
                                <thead>
                                    <tr>
                                        <!--
                                        <th data-field="url" data-formatter="webUrlFormatter"><?php echo lang('url') ?></th>
                                        <th data-field="duration"><?php echo lang('play_time'); ?></th>
                                        <th data-field="updateF"><?php echo lang('update.frequency'); ?></th>

                    -->
                                        <th data-field="name" data-formatter="webNameFormatter"><?php echo lang('name') ?></th>
                                        <th data-field="text" data-formatter="mceTextFormatter"><?php echo lang('text') ?></th>
                                        <th data-field="play_time"><?php echo lang('play_time'); ?></th>
                                        <th data-field="descr"><?php echo lang('desc'); ?></th>
                                        <th data-formatter="operateFormatter"><?php echo lang('operate'); ?></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    <?php endif ?>
                    <?php if ($area->area_type == $this->config->item('area_type_text')) :  ?>
                        <div class="tab-pane show" id="<?php echo "area" . $area->id ?>">
                            <div class="mb-1">
                                <textarea class="form-control" name="motionText" rows="8"> <?php if (isset($area->motion_text)) {
                                                                                                echo $area->motion_text;
                                                                                            } ?></textarea>
                            </div>
                        </div>

                    <?php endif ?>

                    <?php if ($area->area_type == $this->config->item('area_type_date')) :  ?>
                        <div class="tab-pane show" id="<?php echo "area" . $area->id ?>">
                        </div>
                    <?php endif ?>
                    <?php if ($area->area_type == $this->config->item('area_type_time')) : ?>
                        <div class="tab-pane show" id="<?php echo "area" . $area->id ?>">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label"><?php echo lang('font.size'); ?></label>
                                    <select id="textFontSize" class="form-select">
                                        <?php foreach ($this->lang->line('font.size.list') as $v) : ?>
                                            <option value="<?php echo $v; ?>" <?php if (isset($area->setting->font_size) && $v == $area->setting->font_size) : ?>selected<?php endif; ?>><?php echo $v; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><?php echo lang('color'); ?></label>
                                    <input type="color" id=' textColor' class="form-control form-control-color" value="<?php echo isset($area->setting->color) ? $area->setting->color : '#ffffff'; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><?php echo lang('bg.color'); ?></label>
                                    <input type="color" id='textColor' class="form-control form-control-color" value="<?php echo isset($area->setting->bg_color) ? $area->setting->bg_color : '#000000'; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><?php echo lang('style'); ?></label>
                                    <select id="textFontSize" class="form-select">
                                        <option value="1" <?php if (isset($area->setting->style) && $area->setting->style == 1) : ?>selected<?php endif; ?>>HH:MM</option>
                                        <option value="2" <?php if (isset($area->setting->style) && $area->setting->style == 2) : ?>selected<?php endif; ?>>HH:MM PM/AM</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php endif ?>
                    <?php if ($area->area_type == $this->config->item('area_type_website')) :  ?>
                        <div class="tab-pane show" id="<?php echo "area" . $area->id ?>">
                        </div>
                    <?php endif ?>
                    <?php if ($area->area_type == $this->config->item('area_type_staticText')) :  ?>
                        <div class="tab-pane show" id="<?php echo "area" . $area->id ?>">
                        </div>
                    <?php endif ?>
                    <?php if ($area->area_type == $this->config->item('area_type_id')) :  ?>

                        <div class="tab-pane show id-area" id="<?php echo "idarea-" . $area->id ?>">
                            <div class="mb-3" style="display: none">
                                <label class="form-label">Type</label>
                                <div class="form-selectgroup" onchange="changeType(<?php echo $area->id ?>)">
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="<?php echo "types-" . $area->id ?>" value="0" class="form-selectgroup-input" <?php if (!isset($area->idData->type) || (isset($area->idData->type) && $area->idData->type == 0)) : ?>checked<?php endif; ?> />
                                        <span class="form-selectgroup-label"> Charger</span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="<?php echo "types-" . $area->id ?>" value="1" class="form-selectgroup-input" <?php if (isset($area->idData->type) && $area->idData->type == 1) : ?>checked<?php endif; ?> />
                                        <span class="form-selectgroup-label"> Webpage</span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="<?php echo "types-" . $area->id ?>" value="2" class="form-selectgroup-input" <?php if (isset($area->idData->type) && $area->idData->type == 2) : ?>checked<?php endif; ?> />
                                        <span class="form-selectgroup-label">Price/Text</span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="<?php echo "types-" . $area->id ?>" value="3" class="form-selectgroup-input" <?php if (isset($area->idData->type) && $area->idData->type == 3) : ?>checked<?php endif; ?> />
                                        <span class="form-selectgroup-label">Free Charger Count</span>
                                    </label>
                                    <?php if ($this->config->item('with_register_feature')) : ?>
                                        <label class="form-selectgroup-item">
                                            <input type="radio" name="<?php echo "types-" . $area->id ?>" value="4" class="form-selectgroup-input" <?php if (isset($area->idData->type) && $area->idData->type == 4) : ?>checked<?php endif; ?> />
                                            <span class="form-selectgroup-label">Register Price</span>
                                        </label>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="charger-div" <?php if (isset($area->idData->type) && $area->idData->type != 0) : ?>style="display:none" <?php endif ?>>
                                <div class="mb-3">
                                    <label class="form-label">ID</label>
                                    <div class="row g-2">
                                        <div class="col">
                                            <input type="text" required id="<?php echo "id_number-" . $area->id ?>" class="form-control id-numbers" value="<?php echo isset($area->idData->id_number) && $area->idData->type == 0 ? $area->idData->id_number : ''; ?>">
                                        </div>
                                        <div class="col-auto">
                                            <a href="#" class="btn btn-icon" aria-label="Button" data-bs-toggle="modal" data-bs-target="#evsesModal" data-target-field="<?php echo "id_number-" . $area->id ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                                    <path d="M21 21l-6 -6" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"> <?php echo lang('name'); ?></label>
                                    <input type="text" required id="<?php echo "id_name-" . $area->id ?>" class="form-control" value="<?php echo isset($area->idData->name) && $area->idData->type == 0 ? $area->idData->name : ''; ?>">
                                </div>
                            </div>
                            <div class="webpage-div" <?php if (isset($area->idData->type) && $area->idData->type != 1) : ?>style="display:none" <?php endif ?>>
                                <div class="mb-3">
                                    <label class="form-label">Url</label>

                                    <input type="text" required id="<?php echo "id_url-" . $area->id ?>" class="form-control" value="<?php echo isset($area->idData->id_number) && $area->idData->type == 1 ? $area->idData->id_number : ''; ?>">

                                </div>
                                <?php if ($this->config->item('qrcode_feature')) : ?>
                                    <div class="mb-3" id="<?php echo "qrcode_field-" . $area->id ?>" <?php if (isset($area->idData->type) && ($area->idData->type != 1)) : ?>style="display:none" <?php endif ?>>

                                        <label class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="<?php echo "id_qrcode-" . $area->id ?>" <?php if (isset($area->idData->name) && $area->idData->name == 1) : ?>checked<?php endif ?> data-qrcode-switch>
                                            <span class="form-check-label">Convert URL to QR Code</span>
                                        </label>
                                        <div id="<?php echo "qrcode_preview-" . $area->id ?>"></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="price-div" <?php if (isset($area->idData->type) && $area->idData->type != 2) : ?>style="display:none" <?php endif ?>>
                                <div class="mb-3">
                                    <label class="form-label">Price/Text</label>

                                    <input type="text" required id="<?php echo "id_price-" . $area->id ?>" class="form-control" value="<?php echo isset($area->idData->id_number) && $area->idData->type == 2 ? $area->idData->id_number : ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">angle</label>
                                    <input type="number" min=0 max=360 required id="<?php echo "id_angle-" . $area->id ?>" class="form-control" value="<?php echo isset($area->idData->name) && $area->idData->type == 2 ? $area->idData->name : 0; ?>">
                                </div>
                            </div>
                            <div class="freecount-div" <?php if (isset($area->idData->type) && $area->idData->type != 3) : ?>style="display:none" <?php endif ?>>
                                <div class="mb-3">
                                    <label class="form-label">ID</label>
                                    <input required id="<?php echo "id_freenumber-" . $area->id ?>" class="form-control free-numbers" value="<?php echo isset($area->idData->id_number) && $area->idData->type == 3 ? $area->idData->id_number : ''; ?>">

                                </div>
                            </div>

                            <div class="product-div" <?php if (isset($area->idData->type) && $area->idData->type != 4) : ?>style="display:none" <?php endif ?>>
                                <div class="mb-3 row">
                                    <div class='col-auto'>
                                        <label class="form-label"><?php echo lang("store"); ?></label>
                                        <select required id="<?php echo "store_id-" . $area->id ?>" class="form-control">
                                            <?php foreach ($stores as $store) : ?>
                                                <option value="<?php echo $store->id ?>"><?php echo $store->name ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <div class='col'>
                                        <label class="form-label"><?php echo lang("product"); ?></label>
                                        <select required id="<?php echo "product_id-" . $area->id ?>" class="form-control">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><?php echo lang("desc"); ?></label>
                                <textarea type="text" class="form-control" id="<?php echo "id_desc-" . $area->id ?>" rows="2"><?php if (isset($area->idData->descr)) echo $area->idData->descr; ?></textarea>
                            </div>

                            <input type="hidden" id="<?php echo "id_id-" . $area->id ?>" value="<?php echo isset($area->idData->id) ? $area->idData->id : 0; ?>" />
                        </div>
                    <?php endif ?>
                <?php endforeach ?>
            <?php endif ?>
        </div>
    </div>
</div>

<script>
    <?php if ($this->config->item('qrcode_feature')) : ?>
        document.addEventListener("DOMContentLoaded", function() {
            var inputs = document.querySelectorAll('[data-qrcode-switch]');
            // Attach an event listener to each input element
            for (let i = 0; i < inputs.length; i++) {
                inputs[i].addEventListener('click', function(e) {
                    //get checked status
                    var checked = e.target.checked;
                    //get id
                    var id = e.target.id;

                });
            }
            inputs[0].focus();
        });
    <?php endif; ?>



    function previewFormatter(value, row) {
        var source = '';
        switch (row.source) {
            case '0':
                source = "<?php echo lang('local'); ?>";
                break;
            case '1':
                source = "<?php echo lang('ftp'); ?>";
                break;
            case '2':
                source = "<?php echo lang('http'); ?>";
                break;
        }

        var file_size = fileSizeSI(row.file_size);

        var tooltips = `<ul class="list-group align-items-start pl-0" style="white-space: nowrap">
			<li class="list-group-item text-white border-0 py-0" >
				<?php echo lang("author") ?>
				<span>${row.author?row.author:''}</span>
			</li>
			<li class="list-group-item text-white border-0 py-0" >
				<?php echo lang("upload.date") ?>
				${row.add_time}
			</li>
			<li class="list-group-item text-white border-0 py-0" >
				<?php echo lang("file.size") ?>
				<span>${file_size}</span>
			</li>
			<li class="list-group-item text-white border-0 py-0">
				<?php echo lang("file.ext") ?>
				<span>${row.ext}</span>
			</li>
			<li class="list-group-item text-white border-0 py-0">
				<?php echo lang("source") ?>
				<span>${source}</span>
			</li>
			<li class="list-group-item text-white border-0 py-0">
				<?php echo lang("dimension") ?>
				<span>${row.width}X${row.height}</span>
			</li>
			<li class="list-group-item text-white border-0 py-0">
				<?php echo lang("folder") ?>
				<span>${(row.folder_id=='0')?"<?php lang('folder.default') ?>":row.folder_name}</span>
			</li>
			<li class="list-group-item text-white border-0 py-0">
				Media ID
				<span>${row.id}</span>
			</li>
			<li class="list-group-item text-white border-0">
				${row.name}
			</li>
		</ul>`;

        return `<span class="d-inline-block cursor-pointer" tabindex="0" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-html="true"  data-bs-container="body" title='${tooltips}'>
		<img src="${value}" class="rounded" style="max-width:90px; max-height:90px" onerror="javascript:this.remove()" data-bs-toggle="modal" data-bs-target="#modal-medium-preview" data-bs-mediumId="${row.id}"/>
		</span>`;
    }

    function mediaNameFormatter(value, row) {
        return `<a href="#" class="link-primary" onclick="editAreaMedia('${row.id}','${row.transmode}');">
				${value}
			</a>`;
    }

    function operateFormatter(value, row, index) {
        return `<div class="btn-list flex-nowrap">
			<a href="#" class="link-danger removeRowButton" >
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square" viewBox="0 0 16 16">
				<path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
				<path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
			</svg>
			</a>
		</div>`;
    };



    function excludeFormatter(value, row) {
        if (value == 1) {
            return `<label class="form-check form-switch">
				<input class="form-check-input excludeCheck" checked type="checkbox"/>
			</label>`
        } else {
            return `<label class="form-check form-switch">
				<input class="form-check-input excludeCheck" type="checkbox"/>
			</label>`
        }
        if (value == 1) {
            return `<input class="form-check-input excludeCheck" checked type="checkbox"/>`;
        } else {
            return `<input class="form-check-input excludeCheck" type="checkbox"/>`;
        }
    };

    $(document).on('click', '.excludeCheck', function() {
        checked = $(this).is(':checked') ? "1" : "0";
        let rowid = $(this).closest('tr').data('index');
        var activeTableId = getActiveTableId();
        $(`${activeTableId}`).bootstrapTable('updateCell', {
            index: rowid,
            field: "status",
            value: checked
        });
    });

    $(document).on('click', '.removeRowButton', function() {
        let rowid = $(this).closest('tr').data('index');

        var activeTableId = getActiveTableId();
        $(`${activeTableId}`).bootstrapTable('remove', {
            field: '$index',
            values: [rowid]
        });
    });


    function delete_all_media() {

        var activeTableId = getActiveTableId();
        $(`${activeTableId}`).bootstrapTable('removeAll')
    };


    function transFormatter(value, row) {
        if (row.transmode == -1 || row.media_type == 2) {
            return "--";
        } else {
            let num = row.transmode >= 10 ? row.transmode : "0" + row.transmode;
            html = `<img src="/assets/img/transfer/Transfer_Mode_${num}.png" width="32" height="24" />`;
            return html;
        }
    }

    function editAreaMedia(id, transmode) {
        var myModal = new bootstrap.Modal(document.getElementById('areaMediaModal'), {
            keyboard: true
        });

        $('#transition-' + transmode).attr("checked", "true");
        $('#area_media_id').val(id);

        myModal.show();
    }

    $('#update-areaMedia').on('click', function() {
        var transMode = "26";

        $('.form-selectgroup-input').each(function() {
            if ($(this).is(':checked')) {
                transMode = $(this).val();
            }
        });

        var target_id = $('a[data-bs-toggle="tab"].active');

        var activeTableId = getActiveTableId();
        m_id = $('#area_media_id').val();

        $(`${activeTableId}`).bootstrapTable('updateCellByUniqueId', {
            id: m_id,
            field: "transmode",
            value: transMode
        });
    });

    function editAreaWeb(index) {
        var myModal = new bootstrap.Modal(document.getElementById('webpageModal'), {
            keyboard: true
        });
        var row = $('.table-webpage').bootstrapTable('getData')[index]

        $('#web_index').val(index);
        $('#url').val(row.url);
        //$('#transition-' + transmode).attr("checked", "true");

        myModal.show();
    }


    function webUrlFormatter(value, row, index) {

        return `<a href="#" class="link-primary" onclick="editAreaWeb(${index});">
				${value}
			</a>`;
    }

    function webNameFormatter(value, row, index) {
        return `<a href="/webpages/edit?id=${row.id}" class="link-primary">
        ${value}
    </a>`;
    }

    function mceTextFormatter(value, row) {


        if (value) {
            return `<a href="#" class="link-primary" onclick="editMceText('${value}','${row.id}');">
				${value}
			</a>`;
        } else {
            return `<a href="#" class="link-primary" onclick="editMceText('','${row.id}');">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                    <path d="M16 5l3 3"></path>
                </svg>
			</a>`;
        }
    }

    function editMceText(value, id) {
        var myModal = new bootstrap.Modal(document.getElementById('areaMceTextModal'), {
            keyboard: true
        });

        value ? $('#mce_text').val(value) : $('#mce_text').val('');
        $('#area_mce_id').val(id);

        myModal.show();
    }

    $('#update-mceText').on('click', function() {

        var text = $('#mce_text').val();

        var target_id = $('a[data-bs-toggle="tab"].active');

        var activeTableId = getActiveTableId();
        var mce_id = $('#area_mce_id').val();

        $(`${activeTableId}`).bootstrapTable('updateCellByUniqueId', {
            id: mce_id,
            field: "text",
            value: text
        });
    });

    function changeType(area_id) {

        var name = 'types-' + area_id;

        // const type = $(`input[name="${name}"]:checked`).val();
        const radios = document.getElementsByName(name);
        let type;
        for (const radio of radios) {
            if (radio.checked) {
                type = radio.value;
                break;
            }
        }
        switch (type) {
            case '0':
                $('.charger-div').show();
                $('.webpage-div').hide();
                $('.price-div').hide();
                $('.freecount-div').hide();
                $('.product-div').hide();
                break;
            case '1':
                $('.charger-div').hide();
                $('.webpage-div').show();
                $('.price-div').hide();
                $('.freecount-div').hide();
                $('.product-div').hide();
                break;
            case '2':
                $('.charger-div').hide();
                $('.webpage-div').hide();
                $('.price-div').show();
                $('.freecount-div').hide();
                $('.product-div').hide();
                break;
            case '3':
                $('.charger-div').hide();
                $('.webpage-div').hide();
                $('.price-div').hide();
                $('.freecount-div').show();
                $('.product-div').hide();
                break;
            case '4':
                $('.charger-div').hide();
                $('.webpage-div').hide();
                $('.price-div').hide();
                $('.freecount-div').hide();
                $('.product-div').show();
                break;
        }

        <?php if ($this->config->item('qrcode_feature')) : ?>
            if (type == 1) {
                $('#qrcode_field-' + area_id).show();
            } else {
                $('#qrcode_field-' + area_id).hide();
            }
        <?php endif ?>
    }


    $('#select-media').off('click').on('click', function() {
        {
            var selections = mediaTable.bootstrapTable('getSelections');
            if (selections.length == 0) {
                return;
            }

            let media = selections.map((item) => {
                return {
                    id: item.id,
                    name: item.name,
                    play_time: item.play_time,
                    transmode: 26,
                    status: 0,
                    media_type: item.media_type,
                    date_flag: item.date_flag,
                    start_date: item.start_date,
                    end_date: item.end_date,
                    tiny_url: item.tiny_url,
                    width: item.width,
                    height: item.height,
                    folder_name: item.folder_name,
                    author: item.author,
                    add_time: item.add_time,
                    ext: item.ext,
                    file_size: item.file_size,
                    approved: item.approved,
                }
            });

            var activeTableId = getActiveTableId();

            $(`${activeTableId}`).bootstrapTable('append', media);
            $(`${activeTableId}`).bootstrapTable('refresh');
            $('#close-media-modal').click();
        }
    });


    function fetch_products(store_id, productSelect) {
        productSelect.clear();
        productSelect.clearOptions();
        if (!store_id) {
            return;
        }

        fetch('/api/products?store_id=' + store_id)
            .then(function(response) {
                return response.json();
            })
            .then(function(products) {
                // Add the products to the product select
                productSelect.addOption(products.data);

                // Enable the product select
                productSelect.enable();
                <?php if (isset($area->idData->id_number) && is_numeric($area->idData->id_number)) : ?>
                    productSelect.setValue(<?php echo $area->idData->id_number ?>);
                <?php endif; ?>
            });
    }

    function init_storeSelect(area_id) {

        var productSelect = new TomSelect('#product_id-' + area_id, {
            valueField: 'id',
            labelField: 'name',
            searchField: ['name', 'ean_code', 'plu_code'],
            placeholder: 'Select a product',
            disabled: true,

            render: {
                option: function(data) {

                    const div = document.createElement('div');
                    div.className = 'd-flex align-items-center';

                    const span = document.createElement('span');
                    span.className = 'flex-grow-1';
                    span.innerText = data.name;
                    div.append(span);

                    const price = document.createElement('span');
                    price.innerText = data.price;

                    div.append(price);

                    return div;
                },

            }
        });

        var storeSelect = new TomSelect("#store_id-" + area_id, {
            onChange: function(value) {
                fetch_products(value, productSelect);

            }
        });

        <?php if (isset($area->idData->store_id)) : ?>
            storeSelect.setValue(<?php echo $area->idData->store_id ?>)
        <?php endif ?>

        fetch_products(storeSelect.getValue(), productSelect);


    }
    $(document).ready(function() {

        document.querySelectorAll('.id-numbers').forEach((el) => {
            let settings = {
                plugins: {
                    remove_button: {
                        title: 'Remove this item',
                    }
                },
                createOnBlur: true,
                persist: false,
                create: function(input) {

                    this.clear();

                    return {
                        value: input,
                        text: input
                    }
                },
            };
            new TomSelect(el, settings);
        });

        document.querySelectorAll('.free-numbers').forEach((el) => {
            let settings = {
                plugins: {
                    remove_button: {
                        title: 'Remove this item',
                    }
                },
                createOnBlur: true,
                persist: false,
                create: true,
            };
            new TomSelect(el, settings);
        });


        $('#master_area_id').val(null).trigger('change');
        var has_master = false;
        <?php if (isset($area_list) && $area_list) : ?>
            <?php foreach ($area_list as $area) : ?>
                <?php if (
                    $area->area_type == $this->config->item('area_type_movie')
                    || $area->area_type == $this->config->item('area_type_image')
                    || $area->area_type == $this->config->item('area_type_webpage')
                    || $area->area_type == $this->config->item('area_type_mask')
                    || $area->area_type == $this->config->item('area_type_logo')
                ) : ?>
                    var media_data = [];

                    <?php if (
                        $area->area_type == $this->config->item('area_type_movie')
                        || $area->area_type == $this->config->item('area_type_image')
                        || $area->area_type == $this->config->item('area_type_webpage')
                    ) : ?>
                        has_master = true;
                        var should_select = false;
                        <?php if ((!$selected_master && $area->area_type == $this->config->item('area_type_movie')) || ($selected_master && $selected_master == $area->id)) : ?>
                            should_select = true;
                        <?php endif; ?>

                        var newState = new Option("<?php echo $area->name ?>", "<?php echo $area->id ?>", false, should_select);

                        $("#master_area_id").append(newState);
                    <?php endif; ?>

                    <?php if (isset($area->media)) : ?>
                        var media = eval(<?php echo $area->media ?>);
                        media_data = media.data;
                    <?php endif ?>

                    var tableId = "<?php echo 'area' . $area->id . 'Table' ?>";

                    var bt_table = $(`#${tableId}`).bootstrapTable({
                        data: media_data,
                        uniqueId: "id",
                    });
                <?php elseif ($area->area_type == $this->config->item('area_type_id')) : ?>
                    <?php if ($this->config->item('with_register_feature') && isset($area->idData->type) && $area->idData->type == 4) : ?>
                        init_storeSelect("<?php echo $area->id ?>");
                    <?php endif; ?>
                <?php endif; ?>


            <?php endforeach ?> <?php endif ?>
        if (has_master) {
            $("#master_area_id").trigger("change");
            $('#master_area_selection').show();
        } else {
            $('#master_area_selection').hide();
        }

        $('#areaTabs').children("li:first").find("a").addClass("active");
        $(".tab-pane").first().addClass("active");

    })
</script>