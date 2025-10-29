<div id="validateTips">
    <div>
        <div id="formMsgContent">
        </div>
    </div>
</div>
<div class="splider">
</div>
<div id="UploadProgress">
    <span class="legend"><?php echo lang('upload.queue'); ?></span>
</div>
<div>
    <div style="float:right;">
        <div id="uploadBtn" class="qq-upload-button">Browse</div>
        <script type="text/template" id="qq-template">
			<div class="qq-uploader-selector qq-uploader qq-gallery">
				<ul class="qq-upload-list-selector qq-upload-list" role="region" aria-live="polite" aria-relevant="additions removals">
					<li>
						<span role="status" class="qq-upload-status-text-selector qq-upload-status-text">success</span>
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
               					<span class="qq-edit-filename-icon-selector qq-btn qq-edit-filename-icon" aria-label="Edit filename"></span>
                 			</div>
                			<span class="qq-upload-size-selector qq-upload-size"></span>
               				<button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">
                				<span class="qq-btn qq-delete-icon" aria-label="Delete"></span>
                  			</button>
                   			<button type="button" class="qq-btn qq-upload-pause-selector qq-upload-pause">
                    			<span class="qq-btn qq-pause-icon" aria-label="Pause"></span>
                  			</button>
                			<button type="button" class="qq-btn qq-upload-continue-selector qq-upload-continue">
                 				<span class="qq-btn qq-continue-icon" aria-label="Continue"></span>
							</button>
						</div>
					</li>
				</ul>
			</div>
		</script>
		<script>
			var errorHandler = function(id, fileName, reason) {
			    return qq.log("id: " + id + ", fileName: " + fileName + ", reason: " + reason);
			};
			var settings = {
				element: document.getElementById("UploadProgress"),
				button: document.getElementById("uploadBtn"),
				autoUpload: true,
			    debug: false,
			    uploadButtonText: "Select Files",
				display: {
			 		fileSizeOnSubmit: true
				},
				validation: {
					allowedExtensions: ["img"],
			        sizeLimit: 500000000,
			       	//itemLimit: 4
				},
				request: {
					endpoint: "/software/do_upload"
				},
				deleteFile: {
					enabled: false
			  	},
			 	resume: {
			   		enabled: true
			  	},
				retry: {
			  		enableAuto: true
				},
			 	callbacks: {
			 		onError: errorHandler,
			  		onUpload: function (id, filename) {
			        	this.setParams({
			        		"hey": "hi ɛ $ hmm \\ hi",
			        		"ho": "foobar"
			       		}, id);
			  		},
			    	onStatusChange: function (id, oldS, newS) {
			        	qq.log("id: " + id + " " + newS);
			 		},
			    	onComplete: function (id, name, response) {
			      		if(response.success == true) {
			      			software.refresh();
			    			setTimeout(tb_remove, 500);
				      	}
			   		}
				}
			};
			fileuploader = new qq.FineUploader(settings);
		</script>
    </div>
    <div>
        <?php echo lang('max.file.tip'); ?>
    </div>
</div>