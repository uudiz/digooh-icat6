<link href="/static/fileuploader/fineuploader-gallery.css" rel="stylesheet" type="text/css" />
<link href="/static/fileuploader/fineuploader-new.css" rel="stylesheet" type="text/css" />
<link href="/static/fileuploader/fineuploader.css" rel="stylesheet" type="text/css" />
<link href="/static/fileuploader/styles.css" rel="stylesheet" type="text/css" />
<link href="/static/css/media.css" rel="stylesheet" type="text/css" />

<script src="/static/fileuploader/all.fine-uploader.min.js" type="text/javascript"></script>

<?php if ($this->config->item('with_sub_folders')) : ?>
	<link href="/static/css/jquery/select2.min.css" rel="stylesheet" type="text/css" />
	<link href="/static/css/jquery/select2totree.css" rel="stylesheet" type="text/css" />
	<script src="/static/js/jquery/select2.min.js" type="text/javascript"></script>
	<script src="/static/js/jquery/select2totree.js" type="text/javascript"></script>
<?php endif ?>



<div class="page-name">
	<?php
	if ($area_type == $this->config->item('area_type_movie')) {
		echo lang('media.upload.files');
	} else {
		echo lang('media.upload.images');
	}
	?>
</div>

<div class="clear"></div>
<h1 class="tit-01">
	<div class="tab-01" style="left: 20px;">
		<a href="javascript:void(0);" class="on"><?php echo lang('local'); ?></a>
	</div>
	<span></span>
</h1>
<div id="validateTips">
	<div>
		<div id="formMsgContent"></div>
	</div>
</div>
<div class="tab-01-in">
	<div id="validateTips" <?php if (empty($limit_msg)) : ?>style="display:none;" <?php else : ?>class="error" <?php endif; ?>>
		<div>
			<div id="formMsgContent"><?php echo $limit_msg; ?></div>
		</div>
	</div>
	<div class="content clearfix">
		<div>
			<b><?php echo lang('local.title'); ?></b>
		</div>
		<div class="splider"></div>
		<div id="UploadProgress"></div>
		<div>
			<table cellspacing="0" cellpadding="0" border="0">
				<tbody>
					<tr>
						<td><?php echo lang('filter.folder'); ?>:</td>
						<td>
							<select id="folder_select" name="folder_select" style="width: 240px;">
								<?php if ($auth > 1) : ?>
									<?php if (isset($root)) : ?>
										<option value="<?php echo $root->id; ?>"><?php echo $root->name; ?></option>
									<?php else : ?>
										<option value="0"><?php echo lang('folder.default'); ?></option>
									<?php endif ?>
								<?php endif ?>
								<?php if (!$this->config->item('with_sub_folders')) : ?>
									<?php if (isset($folders)) : ?>
										<?php foreach ($folders as $f) : ?>
											<option value="<?php echo $f->id; ?>"><?php if (mb_strlen($f->name) > 64) {
																						echo mb_substr($f->name, 0, 64) . '..';
																					} else {
																						echo $f->name;
																					} ?></option>
										<?php endforeach; ?>
									<?php endif; ?>
								<?php endif; ?>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div>
			<div style="float:right;">
				<div id="uploadBtn" class="qq-upload-button">Browse</div>
			</div>

			<div>
				<br />Note: If you decide to replace old files with new files, related campaign must be published again. <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Otherwise it will cause ERROR during new download.
			</div>
		</div>
	</div>
</div>
<script type="text/template" id="qq-template">
	<div class="qq-uploader-selector qq-uploader qq-gallery">
		<ul class="qq-upload-list-selector qq-upload-list" role="region" aria-live="polite" aria-relevant="additions removals">
			<li>
				<div class="qq-progress-bar-container-selector qq-progress-bar-container">
             		<div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
        		</div>
              	<span class="qq-upload-spinner-selector qq-upload-spinner"></span>
          		<button type="button" class="qq-upload-cancel-selector qq-upload-cancel">X</button>
              	<button type="button" class="qq-upload-retry-selector qq-upload-retry">
               		<span class="qq-btn qq-retry-icon" aria-label="Retry"></span>
                	Retry
             	</button>
				<div class="qq-file-info">
         			<div class="qq-file-name">
						<span class="qq-upload-file-selector qq-upload-file"></span>
						<span class="qq-upload-size-selector qq-upload-size"></span>
                 	</div>
				</div>
			</li>
		</ul>
	</div>
</script>
<script>
	<?php if ($this->config->item('with_sub_folders')) : ?>
		$(document).ready(function() {

			var mydata = <?php echo $folders; ?>;
			$("#folder_select").select2ToTree({
				treeData: {
					dataArr: mydata
				} /*, maximumSelectionLength: 3*/
			});

		});
	<?php endif ?>

	//var errorHandler = function(id, fileName, reason) {return qq.log("id: " + id + ", fileName: " + fileName + ", reason: " + reason);};
	var validateBatchHandler = function(a) {
		var name = ',';
		for (var i = 0; i < a.length; i++) {
			var file = a[0];
			name = name + '\'' + file.name + '\'' + ',';
		}
		name = name.substring(1);
		$.post('/media/media_name_check', {
			name: name
		}, function(data) {
			{
				setTimeout(function() {
					fileuploader.uploadStoredFiles();
				}, 1000);
			}

		});
	};
	var settings = {
		element: document.getElementById("UploadProgress"),
		button: document.getElementById("uploadBtn"),
		autoUpload: false,
		debug: false,
		uploadButtonText: "Select Files",
		maxConnections: 1,
		display: {
			fileSizeOnSubmit: true
		},
		validation: {
			<?php if ($area_type == $this->config->item('area_type_movie')) : ?>
				allowedExtensions: ["mp4", "mkv", "mpg", "mpeg", "avi", "flv", "wmv", "divx", "mov", "jpeg", "jpg", "png", "bmp"],
			<?php else : ?>
				allowedExtensions: ["jpeg", "jpg", "png", "bmp"],
			<?php endif; ?>
			//sizeLimit: 34359738368,
		},
		request: {
			endpoint: "<?php echo $upload_url; ?>"
		},
		retry: {
			enableAuto: false
		},
		callbacks: {
			onSubmit: function(id, fileName) {
				fileuploader.setParams({
					foldersel: document.getElementById("folder_select").value,
					playlist_id: <?php echo $playlist_id; ?>,
					area_id: <?php echo $area_id; ?>,
					area_type: <?php echo $area_type; ?>
				});

			},
			onValidateBatch: validateBatchHandler,
			onComplete: function(id, name, response) {

				if (response.success == true) {
					$('.qq-file-id-' + id + ' div.qq-file-info div.qq-file-name .qq-upload-size').html('<font color="blue">Media File uploaded successfully</font>');
					campaign.loadArea(<?php echo $playlist_id ?>, <?php echo $area_id ?>, 1);

				} else {
					//$('.qq-file-id-'+id+' div.qq-file-info div.qq-file-name .qq-upload-size').html('<font color="blue">Media File uploaded successfully</font>');
				}
			},
			onError: function(id, name, errorReason, xhrOrXdr) {
				$('.qq-file-id-' + id + ' div.qq-file-info div.qq-file-name .qq-upload-size').html(errorReason);
			}
		}
	};
	fileuploader = new qq.FineUploader(settings);
</script>