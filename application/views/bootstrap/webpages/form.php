<script src="/assets/bootstrap/js/jquery.validate.min.js"></script>
<?php if ($lang == 'germany') : ?>
	<script src="/assets/js/validation/messages_de.js"></script>
<?php endif ?>
<script src="https://unpkg.com/imask"></script>

<?php $this->load->view("bootstrap/media/media_modal"); ?>

<div class="row">
	<div class="col-12 col-lg-10 m-auto pt-3 pb-2 mb-3">
		<div class="card">
			<div class="card-header">
				<h2><?php echo $title ?></h2>
			</div>
			<form id="webForm">
				<div class="card-body">
					<div id="validateTips"> </div>
					<div class="row g-3">

						<div class="col-mb3">
							<label for="name"><?php echo lang('name'); ?></label>
							<input type="text" class="form-control" name="name" required value="<?php if (isset($data->name)) echo $data->name; ?>" />
						</div>
						<div class="col-mb3">
							<label for="descr"><?php echo lang("desc"); ?></label>
							<textarea class="form-control" name="descr" rows="2"><?php if (isset($data->descr)) echo $data->descr; ?></textarea>
						</div>
						<div class="col-mb3 row">
							<div class="col-auto ">
								<label></label>
								<label class="form-check form-switch">
									<label><?php echo lang("set.dimensions"); ?></label>
									<input type="checkbox" id='isFullScreen' name='hasDimension' class="form-check-input" <?php if (isset($data) && $data->hasDimension) : ?>checked <?php endif ?> />
								</label>
							</div>
							<div class="col dimension row" <?php if (!isset($data) || !$data->hasDimension) : ?>style="display:none" <?php endif ?>>

								<div class="col-auto ">
									<label for="width"><?php echo lang('width'); ?></label>
									<input type="number" class="form-control" required id="width" name="width" value="<?php if (isset($data->width)) echo $data->width; ?>" />
								</div>
								<div class="col-auto">
									<label for="height"><?php echo lang('height'); ?></label>
									<input type="number" class="form-control" required id="height" name="height" value="<?php if (isset($data->height)) echo $data->height; ?>" />
								</div>
							</div>
						</div>
						<div class="col-mb3">
							<label for="playtime">Background Color</label>
							<input type="color" id='colorpicker' name="backgroundColor" class="form-control form-control-color" value="<?php echo isset($data->backgroundColor) ? $data->backgroundColor : '#FFFFFF'; ?>">
						</div>
						<div class="col-mb3">
							<button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#mediaModal" data-img-only='1' data-single-sel='1'>
								<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-photo" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
									<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
									<line x1="15" y1="8" x2="15.01" y2="8"></line>
									<rect x="4" y="4" width="16" height="16" rx="3"></rect>
									<path d="M4 15l4 -4a3 5 0 0 1 3 0l5 5"></path>
									<path d="M14 14l1 -1a3 5 0 0 1 3 0l2 2"></path>
								</svg>
								<?php echo lang('bgImage') ?>
							</button>
							<a id='reset-bg' class='btn'><?php echo lang('resetBG') ?></a>
							<input type="hidden" id="bg_id" name="bg_id" value="<?php echo isset($data->bg_id) ? $data->bg_id : 0; ?>" />
						</div>

						<div class="mb-3">
							<label for="play_time"><?php echo lang('play_time'); ?> (HH:MM:SS)</label>
							<input type="text" class="form-control" id="play_time" name="play_time" value="<?php if (isset($data->play_time)) echo $data->play_time;
																											else echo "01:00:00"; ?>" />
						</div>
						<div class="col-mb3">
							<textarea id="html" name='contents'>
							</textarea>
						</div>
						<input type="hidden" id="id" name="id" value="<?php echo isset($data->id) ? $data->id : 0; ?>" />

					</div>
					<div class="card-footer">
						<button class="btn btn-outline-primary" type="submit"><i class="bi bi-cloud-arrow-up"></i><?php echo lang('button.save'); ?></button>
						<a class="btn  btn-outline-primary" href="/webpages"><i class="bi bi-x-circle"></i><?php echo lang('button.cancel'); ?></a>
					</div>
			</form>
		</div>
	</div>
</div>

<script src="/assets/tinymce/js/tinymce/tinymce.min.js"></script>
<script>
	var element = document.getElementById('play_time');
	var maskOptions = {
		mask: '00:00:00',

	};
	var mask = IMask(element, maskOptions);

	$('#select-media').on('click', function() {
		{
			var selections = mediaTable.bootstrapTable('getSelections');
			if (selections.length == 0) {
				return;
			}
			tinymce.get('html').getBody().style.backgroundImage = 'url(' + selections[0].full_path.substring(1) + ')';
			$('#bg_id').val(selections[0].id);
			$('#close-media-modal').click();
		}
	});

	$('#reset-bg').on('click', function() {
		tinymce.get('html').getBody().style.backgroundImage = "";
	});

	tinymce.init({
		selector: 'textarea#html',
		resize: false,
		statusbar: false,
		plugins: 'table advlist link image lists', // note the comma at the end of the line!
		menubar: false,
		contextmenu: 'table',
		toolbar: 'undo redo ｜ bold italic underline | table image | fontfamily fontsize blocks | forecolor backcolor removeformat | alignleft aligncenter alignright alignjustify',
		font_family_formats: 'Arial=Arial,Roboto,helvetica,sans-serif; Courier New=courier new,courier,monospace; AkrutiKndPadmini=Akpdmi-n',
		font_size_formats: '8pt 10pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt 60pt 72pt 96pt 128pt 140pt 152pt 164pt 176pt 188pt 200pt',
		setup: function(editor) {
			editor.on('init', function(e) {
				<?php if (isset($data)) : ?>
					const content = `<?php echo $data->contents; ?>`;
					editor.setContent(content);

				<?php endif ?>
				<?php if (isset($data->hasDimension) && $data->hasDimension) : ?>
					const width = <?php echo $data->width ?>;
					const height = <?php echo $data->height ?>;
					set_dimension(width, height);
				<?php endif ?>
				editor.getBody().style.backgroundColor = $('#colorpicker').val();
				<?php if (isset($data) && $data->bg_id) : ?>
					editor.getBody().style.backgroundImage = "<?php echo "url(" . substr($data->full_path, 1) . ")"; ?>";
				<?php endif ?>

				editor.getBody().style.backgroundRepeat = "no-repeat";
				editor.getBody().style.backgroundAttachment = "fixed";
				editor.getBody().style.backgroundSize = "Cover";
				editor.getBody().style.backgroundPosition = "Center";
			});
		}
	});

	//tinymce.editor('html').editorContainer.style.ackgroundColor = "#FFFF66";
	//
	$('#isFullScreen').on('change', function() {
		if ($("#isFullScreen").is(':checked')) {
			$('.dimension').show();
			let newH = $('#height').val();
			let newW = $('#width').val();
			set_dimension(newW, newH);
		} else {
			$('.dimension').hide();
			tinymce.activeEditor.editorContainer.style.height = "400px";
			tinymce.activeEditor.editorContainer.style.width = "100%";
		}
	});
	$('#colorpicker').on('change', function() {
		var editor = tinymce.get('html');

		editor.getBody().style.backgroundColor = $('#colorpicker').val();


	});
	$('.dimension').on('change', function() {
		let newH = $('#height').val();
		let newW = $('#width').val();
		if (newH && newW && newH > 0 && newW > 0) {
			set_dimension(newW, newH);
		}
	});

	function set_dimension(newW, newH) {
		if (newH && newW && newH > 0 && newW > 0) {
			tinymce.activeEditor.editorContainer.style.height = newH + "px";
			tinymce.activeEditor.editorContainer.style.width = newW + "px";
		}
	}

	$(document).ready(function() {
		$("#webForm").validate({
			lang: localStorage.getItem('language') == 'germany' ? 'de' : 'en',
			submitHandler: function(form) {
				var params = new Object();
				$("form#webForm :input").each(function() {
					var inputs = $(this); // A jquery object of the input
					inputs.each(function() {
						if (this.type === 'checkbox') {
							params[`${this.name}`] = $(this).is(':checked') ? 1 : 0;
						} else if ($(this).val().length !== 0) {
							params[`${this.name}`] = $(this).val();

						}
					});
				});

				params['bgImg'] = tinymce.get('html').getBody().style.backgroundImage;

				$.post('/webpages/do_save', params, function(data) {
					if (data.code != 0) {
						toastr.error(data.msg);
					} else {
						localStorage.setItem("Status", JSON.stringify({
							type: 'success',
							message: data.msg
						}));
						window.location = '/webpages';
					}
				}, 'json');
			}
		});
	});
</script>