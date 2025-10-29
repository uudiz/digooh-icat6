<link rel="stylesheet" href="/assets/jquery-ui/jquery-ui.min.css" />

<link rel="stylesheet" href="/assets/bootstrap/css/nouislider.min.css" />
<link rel="stylesheet" href="/assets/css/template_new.css" />
<script src="/assets/jquery-ui/jquery-ui.min.js"></script>
<script src="/assets/bootstrap/js/nouislider.min.js"></script>
<script src="/assets/js/template_new.js"></script>
<script src="/assets/js/html2canvas.min.js"></script>

<div class="content container-fluid">

	<div class="page-header d-print-none">
		<div class="btn-list pt-1">
			<a href="javascript:void(0)" id="btn-movie" class="btn btn-primary btn-icon" aria-label="Button" data-type="<?php echo $this->config->item('area_type_video') ?>" title="<?php echo lang('video'); ?>">
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
			</a>
			<a href="javascript:void(0)" id="btn-pic1" class="btn btn-success btn-icon" aria-label="Button" data-type=<?php echo $this->config->item('area_type_image') ?> title="<?php echo lang('image1'); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-photo" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
					<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
					<line x1="15" y1="8" x2="15.01" y2="8"></line>
					<rect x="4" y="4" width="16" height="16" rx="3"></rect>
					<path d="M4 15l4 -4a3 5 0 0 1 3 0l5 5"></path>
					<path d="M14 14l1 -1a3 5 0 0 1 3 0l2 2"></path>
				</svg>
			</a>
			<a href="javascript:void(0)" id="btn-pic2" class="btn btn-success btn-icon" aria-label="Button" data-type=<?php echo $this->config->item('area_type_image') ?> title="<?php echo lang('image2'); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-photo" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
					<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
					<line x1="15" y1="8" x2="15.01" y2="8"></line>
					<rect x="4" y="4" width="16" height="16" rx="3"></rect>
					<path d="M4 15l4 -4a3 5 0 0 1 3 0l5 5"></path>
					<path d="M14 14l1 -1a3 5 0 0 1 3 0l2 2"></path>
				</svg>
			</a>
			<a href="javascript:void(0)" id="btn-pic3" class="btn btn-success btn-icon" aria-label="Button" data-type=<?php echo $this->config->item('area_type_image') ?> title="<?php echo lang('image3'); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-photo" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
					<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
					<line x1="15" y1="8" x2="15.01" y2="8"></line>
					<rect x="4" y="4" width="16" height="16" rx="3"></rect>
					<path d="M4 15l4 -4a3 5 0 0 1 3 0l5 5"></path>
					<path d="M14 14l1 -1a3 5 0 0 1 3 0l2 2"></path>
				</svg>
			</a>
			<a href="javascript:void(0)" id="btn-pic4" class="btn btn-success btn-icon" aria-label="Button" data-type=<?php echo $this->config->item('area_type_image') ?> title="<?php echo lang('image4'); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-photo" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
					<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
					<line x1="15" y1="8" x2="15.01" y2="8"></line>
					<rect x="4" y="4" width="16" height="16" rx="3"></rect>
					<path d="M4 15l4 -4a3 5 0 0 1 3 0l5 5"></path>
					<path d="M14 14l1 -1a3 5 0 0 1 3 0l2 2"></path>
				</svg>
			</a>
			<a href="javascript:void(0)" id="btn-date" class="btn  btn-icon" aria-label="Button" data-type=<?php echo $this->config->item('area_type_date') ?> title="<?php echo lang('date'); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
					<desc>Download more icon variants from https://tabler-icons.io/i/calendar</desc>
					<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
					<rect x="4" y="5" width="16" height="16" rx="2"></rect>
					<line x1="16" y1="3" x2="16" y2="7"></line>
					<line x1="8" y1="3" x2="8" y2="7"></line>
					<line x1="4" y1="11" x2="20" y2="11"></line>
					<line x1="11" y1="15" x2="12" y2="15"></line>
					<line x1="12" y1="15" x2="12" y2="18"></line>
				</svg>
			</a>
			<a href="javascript:void(0)" id="btn-time" class="btn  btn-icon" aria-label="Button" data-type=<?php echo $this->config->item('area_type_time') ?> title="<?php echo lang('time'); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
					<desc>Download more icon variants from https://tabler-icons.io/i/clock</desc>
					<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
					<circle cx="12" cy="12" r="9"></circle>
					<polyline points="12 7 12 12 15 15"></polyline>
				</svg>
			</a>
			<a href="javascript:void(0)" id="btn-text" class="btn  btn-icon" aria-label="Button" data-type=<?php echo $this->config->item('area_type_text') ?> title="<?php echo lang('text'); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-letter-t" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
					<desc>Download more icon variants from https://tabler-icons.io/i/letter-t</desc>
					<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
					<line x1="6" y1="4" x2="18" y2="4"></line>
					<line x1="12" y1="4" x2="12" y2="20"></line>
				</svg>
			</a>
			<a href="javascript:void(0)" id="btn-webpage" class="btn  btn-icon" aria-label="Button" data-type=<?php echo $this->config->item('area_type_webpage') ?> title="<?php echo lang('webpage'); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-chrome" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
					<desc>Download more icon variants from https://tabler-icons.io/i/brand-chrome</desc>
					<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
					<circle cx="12" cy="12" r="9"></circle>
					<circle cx="12" cy="12" r="3"></circle>
					<line x1="12" y1="9" x2="20.4" y2="9"></line>
					<line x1="12" y1="9" x2="20.4" y2="9" transform="rotate(120 12 12)"></line>
					<line x1="12" y1="9" x2="20.4" y2="9" transform="rotate(240 12 12)"></line>
				</svg>
			</a>

			<a href="javascript:void(0)" id="btn-mask" class="btn  btn-icon" aria-label="Button" data-type=<?php echo $this->config->item('area_type_mask') ?> title="<?php echo lang('mask'); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-letter-m" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
					<desc>Download more icon variants from https://tabler-icons.io/i/letter-m</desc>
					<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
					<path d="M6 20v-16l6 14l6 -14v16"></path>
				</svg>
			</a>

			<?php if ($this->config->item('with_id_zone')) : ?>
				<a href="javascript:void(0)" id="btn-id" class="btn  btn-icon" aria-label="Button" data-type=<?php echo $this->config->item('area_type_id') ?> title="ID">
					ID
				</a>
			<?php endif ?>
		</div>
		<div class="pt-1">
			<div class="text-muted mt-1">
				<?php if ($using) : ?>
					<b><?php echo lang('warn.template.readonly'); ?></b>
				<?php else : ?>
					<b><?php echo lang('area.move'); ?></b>:&nbsp;<?php echo lang('area.move.tip'); ?>
					<b><?php echo lang('area.enlarge'); ?></b>:&nbsp;<?php echo lang('area.enlarge.tip'); ?>

				<?php endif; ?>
			</div>
		</div>
	</div>


	<div class="page-body">
		<div class="row">
			<div class="col-auto">

				<div style="position:relative; overflow: hidden;" class="gray-area" id="screen">
					<img id="screenbg" style="position: absolute;width:100%; top: 0px; left:0px; z-index:1;" />
				</div>
			</div>
			<div class="col-auto">
				<div style="width:350px">
					<form class="row" id="tempForm">
						<div class="mb-1 ">
							<label class="form-label"><?php echo lang('name'); ?>:</label>
							<input type="text" class="form-control" id="name" required value="<?php if (isset($template->name)) echo $template->name; ?>" />
						</div>
						<div class="mb-1">
							<label class="form-label"><?php echo lang('desc'); ?>:</label>
							<textarea type="text" rows=2 class="form-control" id="descr"><?php if (isset($template->descr)) echo $template->descr; ?></textarea>
						</div>

						<div class="mb-1">
							<label><?php echo lang('resolution'); ?></label>
							<select id="resolution" class="form-select" <?php if (isset($template)) : ?>disabled="disabled" <?php endif ?>>
								<option value="1080X1920" <?php if (isset($template) && $template->width == "1080") : ?>selected="selected" <?php endif; ?>>1080X1920</option>
								<option value="1920X1080" <?php if (isset($template) && $template->width == "1920") : ?>selected="selected" <?php endif; ?>>1920X1080</option>
								<option value="2160X3840" <?php if (isset($template) && $template->width == "2160") : ?>selected="selected" <?php endif; ?>>2160X3840</option>
								<option value="3840X2160" <?php if (isset($template) && $template->width == "3840") : ?>selected="selected" <?php endif; ?>>3840X2160</option>
							</select>
						</div>
						<div class="mb-1">
							<label><?php echo lang('bg'); ?></label>
							<a href="#" class="btn btn-icon" data-bs-toggle="modal" data-bs-target="#mediaModal" data-img-only='1' data-single-sel='1' title="Browse">
								<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
									<desc>Download more icon variants from https://tabler-icons.io/i/file-plus</desc>
									<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
									<path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
									<path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
									<line x1="12" y1="11" x2="12" y2="17"></line>
									<line x1="9" y1="14" x2="15" y2="14"></line>
								</svg>
							</a>
							<a class="btn btn-icon" aria-label="Button" onclick='resetBG()' title="reset">
								<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-square-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
									<desc>Download more icon variants from https://tabler-icons.io/i/square-x</desc>
									<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
									<rect x="4" y="4" width="16" height="16" rx="2"></rect>
									<path d="M10 10l4 4m0 -4l-4 4"></path>
								</svg>
							</a>
						</div>
						<input type="hidden" id="template_id" value="<?php echo isset($template) ? $template->id : 0; ?>" />
					</form>
					<div id="areaInfo" class="form-fieldset" style="display:none;">
						<h3 id="areaTitle"></h3>
						<div class="row">
							<input type="hidden" id="areaChange" value="" />
							<div class="mb-1 col-md-6 row">
								<label class="form-label col-form-label "><?php echo lang('x'); ?></label>

								<div class="input-group col">
									<input id="areaX" type="number" min="0" class="form-control" onblur="template.changeX();" autocomplete="off">
									<span class="input-group-text">
										px
									</span>
								</div>

							</div>
							<div class="form-group mb-1 col-md-6 row">
								<label class="form-label col-form-label"><?php echo lang('y'); ?></label>

								<div class="input-group col">
									<input id="areaY" type="number" min="0" class="form-control" onblur="template.changeY();" autocomplete="off">
									<span class="input-group-text">
										px
									</span>
								</div>
							</div>
							<div class="form-group col-md-6 row ">
								<label class="form-label col-form-label"><?php echo lang('width'); ?></label>
								<div class="row">
									<div class="input-group">
										<input id="areaWidth" type="number" class="form-control" onblur="template.changeW(1);" autocomplete="off">
										<span class="input-group-text">
											px
										</span>
									</div>
									<!--
									<div class="input-group col">
										<input id="areaWidthPercent" type="number" max=100 class="form-control" onblur="template.changeW(2);" autocomplete="off">
										<span class="input-group-text">
											%
										</span>
									</div>
								-->
								</div>
							</div>
							<div class="form-group col-md-6 row">
								<label class="form-label col-form-label"><?php echo lang('height'); ?></label>
								<div class="row">
									<div class="input-group">
										<input id="areaHeight" type="number" class="form-control" onblur="template.changeH();" autocomplete="off">
										<span class="input-group-text">
											px
										</span>
									</div>
									<!--
									<div class="input-group col">
										<input id="areaHeightPercent" type="number" min=1 max=100 class="form-control" onblur="template.changeH(2);" autocomplete="off">
										<span class="input-group-text">
											%
										</span>
									</div>
								-->
								</div>
							</div>

							<form class="row" id="area_settings" style="display:none">
								<div class="mb-3">
									<label class="form-label"><?php echo lang('font.family'); ?></label>
									<select id="areaFontFamily" class="form-select">


										<?php foreach ($this->lang->line('text.fontFamily.list') as $k => $v) : ?>
											<option value="<?php echo $k; ?>"><?php echo $v; ?></option>
										<?php endforeach; ?>

										<?php if ($this->config->item('aral_font')) : ?>
											<?php foreach ($this->lang->line('text.aralFontFamily.list') as $k => $v) : ?>
												<option value="<?php echo $k; ?>"><?php echo $v; ?></option>
											<?php endforeach; ?>
										<?php endif; ?>

									</select>
								</div>

								<div class="mb-3">
									<label class="form-label"><?php echo lang('font.size'); ?></label>
									<div class="form-range" id="rangeFontsize"></div>

								</div>
								<div class="mb-1 col-md-6">
									<label class="form-label"><?php echo lang('color'); ?></label>
									<input type="color" id='areaFontColor' class="form-control form-control-color" value="#FFFFFF">
								</div>
								<div class="mb-1 col-md-6">
									<label class="form-label"><?php echo lang('bg.color'); ?> </label>
									<input type="color" id='areaBgColor' class="form-control form-control-color">
								</div>

								<div class="mb-1">
									<label class="form-label"><?php echo lang('text.transparent'); ?></label>
									<select id="areaTrans" class="form-select">
										<?php foreach ($this->lang->line('text.transparent.list') as $k => $v) : ?>
											<option value="<?php echo $k; ?>"><?php echo $v; ?></option>
										<?php endforeach; ?>
									</select>
								</div>

								<div class="mb-1 status-setting extra-setting" style="display:hide;">
									<label class="form-label"><?php echo lang('evCharger'); ?></label>
									<select id="charger_setting_id" class="form-select">
										<option value="0"><?php echo lang('default'); ?></option>
										<?php if (isset($charger_settings)) : ?>
											<?php foreach ($charger_settings as $charger) : ?>
												<option value="<?php echo $charger->id; ?>"><?php echo $charger->name; ?></option>
											<?php endforeach; ?>
										<?php endif; ?>
									</select>
								</div>
								<div class="mb-1 status-setting extra-setting" style="display:hide;">
									<label class="form-label"><?php echo lang('type'); ?></label>
									<select id="charger_setting_type" class="form-select">
										<option value="0"><?php echo lang('charger'); ?></option>
										<option value="1"><?php echo lang('webpage'); ?></option>
										<option value="2"><?php echo lang('price_text'); ?></option>
										<option value="3"><?php echo lang('free_charger_count'); ?></option>
										<option value="4"><?php echo lang('register_price'); ?></option>
									</select>
								</div>

								<div id="extra_settings">
									<div class="mb-1 extra-setting">
										<label class="form-label" id="select-label"><?php echo lang('style'); ?></label>
										<select id="areaFormat" class="form-select">
											<option value="1">HH:MM</option>
											<option value="2">HH:MM PM/AM</option>
										</select>
									</div>

									<div class="mb-1 txt-setting extra-setting" style="display:hide;">
										<label class="form-label"><?php echo lang('direction'); ?></label>
										<select id="direction" class="form-select">
											<?php foreach ($this->lang->line('np200.text.direction.list') as $k => $v) : ?>
												<option value="<?php echo $k; ?>"><?php echo $v; ?></option>
											<?php endforeach; ?>
										</select>
									</div>


								</div>
							</form>

						</div>
					</div>

				</div>
			</div>
		</div>

		<div class="card-footer">

			<a class="btn btn-default" href="/TemplateController"><span><?php echo lang('button.back'); ?></span></a>
			<?php if ($auth > 0 && !$using) : ?>
				<a class="btn btn-primary" href="javascript:void(0);" onclick="do_save()"><span><?php echo lang('button.save'); ?></span></a>
			<?php endif; ?>
		</div>
	</div>
	<?php $this->load->view("bootstrap/media/media_modal"); ?>
</div>
<?php
$this->load->view("bootstrap/media/media_modal");
?>
<script type="text/javascript">
	function get_select_options(type) {
		var options = '';
		if (type == "area_date") {
			$('#select-label').html("<?php echo lang('style') ?>");
			options = `<option value="4" >mm/dd/yyyy</option>
                        <option value="5" >dd/mm/yyyy</option>
                        <option value="6" >yyyy/mm/dd</option>`;
		} else if (type == "area_weather") {
			options = `<option value="5" >Style 1 (Today)</option>
                        <option value="4" >Style 2 (3 days)</option>`;
		} else if (type == 'area_text') {
			$('#select-label').html("<?php echo lang('text.speed') ?>");
			var options = '';
			<?php foreach ($this->lang->line('text.speed.list') as $k => $v) : ?>
				options += `<option value="<?php echo $k; ?>"><?php echo $v; ?></option>`;
			<?php endforeach; ?>
		} else if (type == "area_time") {
			options = `<option value="1">HH:MM</option>
                    <option value="2">HH:MM PM/AM</option>`;
		}
		return options;
	};


	$('#select-media').on('click', function() {
		{
			var selections = mediaTable.bootstrapTable('getSelections');
			if (selections.length == 0) {
				return;
			}
			if (template.bg != null) {
				template.bg.mediaId = selections[0].id;
			} else {
				template.bg = {
					mediaId: selections[0].id,
					type: "<?php echo $this->config->item('area_type_bg') ?>",
					zindex: 0,
				};
			}

			$("#screenbg").attr("src", selections[0].main_url);

			$('#close-media-modal').click();
		}
	});

	function do_save() {
		if (!$('#tempForm').valid()) {
			return false;
		}
		if (template.template_type != 1) {
			if (template.movie == null) {
				alert(template.warnVideo);
				return;
			}
		}

		if (template.isOverlapping()) {
			alert(template.warnOverlap);
		} else {
			html2canvas(document.getElementById("screen")).then(function(canvas) {
				//var params = template.createData();
				var params = new Object();
				params.areas = template.createData();
				params.deletes = template.deletes;
				params.id = $('#template_id').val();
				params.name = $('#name').val();
				params.descr = $('#descr').val();
				params.resolution = $('#resolution').val();
				params.screenshot = canvas.toDataURL("image/png");
				params.w = $('#screen').width();
				params.h = $('#screen').height();
				$.post(
					"/TemplateController/save_screen?t=" + new Date().getTime(), params,
					function(data) {
						if (data.code == 0) {
							localStorage.setItem("Status", JSON.stringify({
								type: 'success',
								message: data.msg
							}));
							window.location = '/TemplateController';

						} else {
							toastr.error(data.msg);
						}
						//window.location.href = "/template";
					},
					"json"
				);
			});

		}

		return false;
	}



	function set_screen_resolution() {
		var tw = 1920;
		var th = 1080;
		var rate = 2;

		var resArr = $("#resolution").val().split("X");
		tw = resArr[0];
		th = resArr[1];

		if (tw > 1920) {
			rate = 4;
		}

		$('#screen').width(tw / rate);
		$('#screen').height(th / rate);
		template.realWidth = tw;
		template.realHeight = th;
		template.width = tw / rate;
		template.height = th / rate;
		template.radio = rate;
		template.warnSpace = '<?php echo lang('warn.screen.space'); ?>';
		template.warnOverlap = '<?php echo lang('warn.screen.overlap'); ?>';
		template.warnLogo = '<?php echo lang('area.logo.tip'); ?>';
		template.warnVideo = '<?php echo lang('area.video.tip'); ?>';
		<?php if ($using || $auth < 5) : ?>
			template.readonly = true;
		<?php endif; ?>

		template.init();
	}

	set_screen_resolution();

	$("#resolution").on("change", function() {
		$('#screen').html(`<img id="screenbg" style="position: absolute;width:100%; top: 0px; left:0px; z-index:1;" />`);
		set_screen_resolution();

	})

	<?php if (!$using) : ?>
		$('.btn-icon').on('click', function() {
			var cur_id = $(this).attr('id');
			var tmp = cur_id.split("-");
			var area_name = tmp[1];


			var type = $(this).attr('data-type');

			if (type != 30) {
				$(this).addClass('disabled');
			}

			var title = $(this).attr('title');
			var zIndex = 10;

			var x = 0;
			var y = 0;
			var w = 120;
			var h = 120;


			if (area_name == "text") {
				w = template.width;
				h = 120 / template.radio;
				y = template.height - h;

				zIndex = 99;
			} else if (area_name == "date" || area_name == "time") {
				x = template.width - w;
				h = 120 / template.radio;
				w = 256 / template.radio;
				zIndex = 99;
			} else if (area_name == "logo" || area_name == "weather") {
				zIndex = 99;
			} else if (area_name == "staticText") {
				zIndex = 9;
			} else if (area_name == "mask") {
				zIndex = 100;
			} else if (area_name == "id") {
				zIndex = 100;
				h = 120 / template.radio;
				w = 512 / template.radio;
			}


			var area = {
				name: title,
				x: x,
				y: y,
				w: w,
				h: h,
				area_type: type,
				areaName: area_name,
				zindex: zIndex,
			};

			template.addArea(area);
		})

	<?php endif ?>

	$('#area_settings').on('change', function() {

		template.setAreasSettings();

	})

	function resetBG() {
		if (template.bg != null) {
			template.bg.mediaId = 0;
		} else {
			template.bg = {
				mediaId: 0,
				type: "<?php echo $this->config->item('area_type_bg') ?>",
				zindex: 0,
			};
		}
		$("#screenbg").removeAttr("src");
	}

	$(document).ready(function() {
		$.get(
			"/TemplateController/getAreaData?id=" + $("#template_id").val(),
			function(res) {

				//window.location.href = "/template";
				var img_index = 1;
				for (var key in res) {
					var area = res[key];
					if (area.area_type == "<?php echo $this->config->item('area_type_bg') ?>") {
						template.bg = {
							areaId: area.id,
							mediaId: area.media_id,
							type: area.area_type,
							zindex: area.zindex,
						};
						$("#screenbg").attr("src", area.main_url);
					} else {
						template.initArea(area);
					}

				}
			},
			"json"
		);
	});
	var slider = document.getElementById('rangeFontsize');
	// @formatter:off
	/*
	document.addEventListener("DOMContentLoaded", function() {
		window.noUiSlider && (noUiSlider.create(slider, {
			start: 40,
			connect: [true, false],
			step: 10,
			tooltips: true,
			range: {
				min: 20,
				max: 220
			},
			format: {
				to: function(value) {
					return Math.round(value);
				},
				from: function(formattedValue) {
					return Math.round(formattedValue);
				},
			},

		}));
	});
	*/
	noUiSlider.create(slider, {
		start: 40,
		connect: [true, false],
		step: 1,
		tooltips: true,
		range: {
			min: 20,
			max: 220
		},
		format: {
			to: function(value) {
				return Math.round(value);
			},
			from: function(formattedValue) {
				return Math.round(formattedValue);
			},
		},

	});
	slider.noUiSlider.on('change.one', function() {
		template.setAreasSettings();
	});
	// @formatter:on
</script>