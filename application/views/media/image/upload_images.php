<div class="page-name">
	<?php
	echo lang('media.upload.images');
	?>
</div>

<div class="clear"></div>
<h1 class="tit-01">
	<div class="tab-01" style="left: 20px;">
		<a href="javascript:void(0);" class="on"><?php echo lang('local'); ?></a>
		<a href="javascript:void(0);"><?php echo lang('ftp'); ?></a>
		<!--    <a href="javascript:void(0);"><?php echo lang('http'); ?></a>
     -->
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
			<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
				<tbody>
					<tr>
						<td><?php echo lang('filter.folder'); ?>:</td>
						<td>
							<select id="folder_select" name="folder_select" style="width: 240px;">

								<?php if ($auth > 1) : ?>
									<option value="<?php echo $root_id; ?>"><?php echo lang('folder.default'); ?></option>
								<?php endif ?>
								<?php if (!$this->config->item("with_sub_folders")) : ?>
									<?php if (isset($folders)) : ?>
										<?php foreach ($folders as $f) : ?>
											<option value="<?php echo $f->id; ?>"><?php if (mb_strlen($f->name) > 64) {
																						echo mb_substr($f->name, 0, 64) . '..';
																					} else {
																						echo $f->name;
																					} ?></option>
										<?php endforeach; ?>
									<?php endif; ?>
								<?php endif ?>
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
			<!--
			<div id="divStatus">0 Files Uploaded</div>
			
			<div><?php echo lang('max.file.tip'); ?></div>
			-->
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
			allowedExtensions: ["jpeg", "jpg", "png", "bmp"],
			sizeLimit: 500000000,
		},
		request: {
			endpoint: "/media/file_upload_images"
		},
		retry: {
			enableAuto: true
		},
		callbacks: {
			onSubmit: function(id, fileName) {
				fileuploader.setParams({
					foldersel: document.getElementById("folder_select").value
				});
			},

			onValidateBatch: validateBatchHandler,
			onComplete: function(id, name, response) {

				if (response.success == true) {
					$('.qq-file-id-' + id + ' div.qq-file-info div.qq-file-name .qq-upload-size').html('<font color="blue">Media File uploaded successfully</font>');
					mediaLib.refresh();
				} else {

				}
			}
		}
	};
	fileuploader = new qq.FineUploader(settings);
</script>
<div class="tab-01-in" style="display: none;">
	<div class="content clearfix">
		<?php if (isset($sites) && !empty($sites)) : ?>
			<div class="align-left">
				<div>
					<b><?php echo lang('ftp.title'); ?></b>
				</div>
				<!--表单区start-->
				<form id="siteForm" name="siteForm">
					<table width="300" border="0" cellspacing="0" cellpadding="0" class="from-panel">
						<tr>
							<td width="80"><?php echo lang('ftp.sites'); ?>:</td>
							<td>
								<select id="ftpSite" name="ftpSite" style="width: 150px;">
									<option value="0"><?php echo lang('ftp.select.site'); ?></option>
									<?php if (isset($sites)) : ?>
										<?php foreach ($sites as $s) : ?>
											<option value="<?php echo $s->id; ?>"><?php if (mb_strlen($s->profile) > 24) {
																						echo mb_substr($s->profile, 0, 24) . '..';
																					} else {
																						echo $s->profile;
																					} ?></option>
										<?php endforeach; ?>
									<?php endif; ?>
								</select>
								<input type="hidden" id="ftpId" name="ftp.id" />
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><?php echo lang('ftp.profile'); ?>:</td>
							<td>
								<span class="ftpBox" id="ftpProfile"></span>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><?php echo lang('ftp.server'); ?>:</td>
							<td>
								<span class="ftpBox" id="ftpServer"></span>
							</td>
							<td>
								<div class="attention" id="errorFtpServer" style="display:none;">
									<?php echo lang('warn.ftp.server.format'); ?>
								</div>
							</td>
						</tr>
						<tr>
							<td><?php echo lang('ftp.port'); ?>:</td>
							<td>
								<span class="ftpBox" id="ftpPort"></span>
							</td>
							<td>
								<div class="attention" id="errorFtpPort" style="display:none;">
									<?php echo lang('warn.ftp.port.format'); ?>
								</div>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><span class="font-12 font-gray"><?php echo lang('ftp.port.tip'); ?></span></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><?php echo lang('ftp.pasv'); ?>:</td>
							<td>
								<span id="ftpPasv"></span>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><?php echo lang('ftp.account'); ?>:</td>
							<td>
								<span class="ftpBox" id="ftpAccount"></span>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><?php echo lang('ftp.password'); ?>:</td>
							<td>
								<span class="ftpBox" id="ftpPassword"></span>
							</td>
							<td>&nbsp;</td>
						</tr>
					</table>
				</form>
				<!--表单区end-->
				<p class="btn-center">
					<a href="javascript:void(0);" id="connect" class="btn-02"><span><?php echo lang('button.connect'); ?></span></a>
				</p>
			</div>
			<div class="align-left" style="width: 60%; height:100%;">
				<div class="gray-area" style="height:100%;">
					<div name="textarea" id="fileTree" style="height: 300px; width:46%; float:left; border:dotted; border-width:thin;"></div>　
					<div name="textarea" id="fileList" style="height: 300px; width:46%; float:right;  border:dotted; border-width:thin; overflow-y: auto;"></div>
					<div class="clear"></div>
					<div class="btn-right">
						<a href="javascript:void(0);" id="checkall" class="btn-02"><span><?php echo lang('button.checkall'); ?></span></a>
						<a href="javascript:void(0);" id="uncheckall" class="btn-02"><span><?php echo lang('button.uncheckall'); ?></span></a>
						<a href="javascript:void(0);" id="ftpMediaSave" class="btn-02"><span><?php echo lang('button.save'); ?></span></a>
					</div>
				</div>
			</div>
		<?php else : ?>
			<span class="attention"><?php echo lang('warn.ftp.empty'); ?></span>
		<?php endif; ?>
	</div>
</div>

<div class="tab-01-in" style="display: none;">
	<div class="content">
		<div>
			<b><?php echo lang('http.title'); ?></b>
		</div>
		<div>
			<form id="httpForm" name="httpForm">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="from-panel">
					<tr>
						<td width="100"><?php echo lang('http.name'); ?></td>
						<td width="300">
							<input type="text" name="name" id="httpName" class="text ui-widget-content ui-corner-all" style="width: 300px;" />
						</td>
						<td>

						</td>
					</tr>
					<tr>
						<td><?php echo lang('desc'); ?></td>
						<td>
							<textarea id="httpDescr" name="descr" class="text ui-widget-content ui-corner-all" style="width: 300px;"></textarea>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td><?php echo lang('url'); ?></td>
						<td>
							<input type="text" name="url" id="httpUrl" class="text ui-widget-content ui-corner-all" style="width: 300px;" />
						</td>
						<td>
							<div class="attention" id="errorUrl" style="display:none;">
								<div>
									<?php echo lang('warn.http.url.format'); ?>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>
							<span class="font-12 font-gray"><?php echo lang('http.url.video.tip'); ?></span>
						</td>
						<td>&nbsp;</td>
					</tr>
				</table>
				<p class="btn-center">
					<a class="btn-01" href="javascript:void(0);" id="httpMediaSave"><span><?php echo lang('button.save'); ?></span></a>
					<a class="btn-01" href="javascript:void(0);" id="httpMediaReset"><span><?php echo lang('button.reset'); ?></span></a>
				</p>
			</form>
		</div>
	</div>
</div>

<div id="rotateConfirm" title="Confirmation" style="display:none;">
	<p>
		<span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 100px 0;"></span>
		Server already has files with same names. Do you want to replace all old files with new files?<br /><br />
		Yes: Will replace all old files<br />
		No: Keep old files but change filename to filename_( )
	</p>
</div>
<link href="/static/css/jquery/select2.min.css" rel="stylesheet" type="text/css" />
<link href="/static/css/jquery/select2totree.css" rel="stylesheet" type="text/css" />
<script src="/static/js/jquery/select2.min.js" type="text/javascript"></script>
<script src="/static/js/jquery/select2totree.js" type="text/javascript"></script>

<script type="text/javascript">
	mediaLib.uploader.testValue = '<?php if ($this->session->userdata('name_v') != '') {
										echo $this->session->userdata('name_v');
									} else {
										echo -1;
									} ?>';
	mediaLib.uploader.sessionId = '<?php echo $session_id; ?>';
	mediaLib.uploader.uploadUrl = '<?php echo $upload_url; ?>';
	mediaLib.uploader.fileTypes = '<?php echo $file_types; ?>';
	mediaLib.uploader.fileTypes_desc = '<?php echo $file_types_desc; ?>';
	mediaLib.uploader.mediaType = <?php echo $media_type; ?>;
	mediaLib.uploader.init();
	<?php if ($this->config->item('with_sub_folders')) : ?>
		var mydata = <?php echo $folders; ?>;
		$("#folder_select").select2ToTree({
			treeData: {
				dataArr: mydata
			} /*, maximumSelectionLength: 3*/
		});
	<?php endif ?>
</script>