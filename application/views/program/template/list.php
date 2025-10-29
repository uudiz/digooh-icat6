<?php if($type == $system):?>
	<?php if($auth_system):?>
		<div class="add-panel">
		<a href="/template/add?type=<?php echo $type;?>&width=320&height=240" id="create" class="thickbox" title="<?php echo lang('template.new');?>"><?php echo lang('create');?></a>
	</div>
	<div class="clear"></div>
	<?php endif;?>
<?php else:?>
<?php if($auth > 1):?>
	<div id="UploadProgress" style="display: none;"></div>
	<div class="add-panel">
		<a href="javascript:void(0);" title="<?php echo lang('template.import');?>"><div id="importBtn" style="width: 50px; height: 18px;"><img src="/images/icons/import.png" /></div></a>&nbsp;
		<a href="/template/add?type=<?php echo $type;?>&width=320&height=240" id="create" class="thickbox" title="<?php echo lang('template.new');?>"><?php echo lang('create');?></a>
		<script type="text/template" id="qq-template">
		<div class="qq-uploader-selector qq-uploader qq-gallery">
			<ul class="qq-upload-list-selector qq-upload-list" role="region" aria-live="polite" aria-relevant="additions removals">
				<li>
					<div class="qq-file-info">
         				<div class="qq-file-name">
							<span class="qq-upload-file-selector qq-upload-file"></span>
                 		</div>
					</div>
				</li>
			</ul>
		</div>
		</script>
		<script>
			//var errorHandler = function(id, fileName, reason) {return qq.log("id: " + id + ", fileName: " + fileName + ", reason: " + reason);};
			var settings = {
				element: document.getElementById("UploadProgress"),
				button: document.getElementById("importBtn"),
				autoUpload: true,
			    debug: false,
			    uploadButtonText: "Select Files",
				display: {
			 		fileSizeOnSubmit: true
				},
				validation: {
					allowedExtensions: ["xml"],
			        sizeLimit: 1000000,
			       	//itemLimit: 1
				},
				request: {
					endpoint: "/template/html5_import"
				},
			 	resume: {
			   		enabled: true
			  	},
				retry: {
			  		enableAuto: false
				},
			 	callbacks: {
			 		//onError: errorHandler,
			  		onUpload: function (id, filename) { this.setParams({"hey": "hi ɛ $ hmm \\ hi","ho": "foobar"}, id);},
			    	onStatusChange: function (id, oldS, newS) {/*qq.log("id: " + id + " " + newS);*/},
			    	onComplete: function (id, name, response) {
			      		if(response.success == true) {
			      			showMsg(response.msg);
							setTimeout(function(){
								template.index.refresh();
								hideMsg();
							}, 1000);
				      	}else {
				      		alert(response.msg);
					    }
			   		}
				}
			};
			fileuploader = new qq.FineUploader(settings);
		</script>
	</div>
<?php endif;?>
	<div class="clear"></div>
<?php endif;?>

<?php if($total > 0):?>
<div class="template-panel">
	<ul>
		<?php
		$idx = 0; 
		foreach($data as $row):
		?>
		<li>
			<table border="0" cellspacing="0" cellpadding="0">
			<?php if($auth > 1):?>
              <tr>
                <td align="right">
                	 <div class="operate">
						<!--
                	 	<?php if($row->area_list):?>
                	 	<?php 
						$img_index=0;
						foreach($row->area_list as $area)
							switch($area->area_type){
								case $this->config->item('area_type_bg'):
									echo '<img id="bg_'.$row->id.'" src="/images/icons/24-bg.png" title="'.lang('bg').'" />';
									break;
								case $this->config->item('area_type_movie'):
									echo '<img id="movie_'.$row->id.'" src="/images/icons/24-video.png" title="'.lang('video').'" />';
									break;
								case $this->config->item('area_type_image'):
									echo '<img id="image_'.$img_index.'_'.$row->id.'" src="/images/icons/24-image.png" title="'.lang('image').'" />';
									$img_index++;
									break;
								case $this->config->item('area_type_text'):
									echo '<img id="text_'.$row->id.'" src="/images/icons/24-text.png" title="'.lang('text').'" />';
									break;
								case $this->config->item('area_type_date'):
									echo '<img id="date_'.$row->id.'" src="/images/icons/24-date.png" title="'.lang('date').'" />';
									break;
								case $this->config->item('area_type_time'):
									echo '<img src="/images/icons/16-10.gif" width="16" height="16" title="'.lang('time').'" />';
									break;
								case $this->config->item('area_type_weather'):
									echo '<img id="weather_'.$row->id.'" src="/images/icons/24-weather.png" title="'.lang('weather').'" />';
									break;
								case $this->config->item('area_type_logo'):
									echo '<img src="/images/icons/24-logo.png" title="'.lang('logo').'" />';
									break;
							}
						?>
						<?php endif;?>
						-->
						<?php if($auth > 0):?>
                     	<a href="/template/edit?id=<?php echo $row->id;?>&width=600&height=320" class="thickbox" title="<?php echo lang('edit.template');?>"><img id="edit_<?php echo $row->id;?>" src="/images/icons/24-edit.png" title="<?php echo lang('edit');?>" /></a>
						<a href="/template/export?id=<?php echo $row->id;?>"><img id="export_<?php echo $row->id;?>" src="/images/icons/24-export.png" title="<?php echo lang('export');?>" /></a>
					 	<a href="javascript:void(0);" onclick="template.index.remove(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>',false,<?php echo $type;?>,<?php if($idx == 0 || $curpage == 1){echo $curpage;}elseif($curpage>1){echo $curpage-1;} ?>);"><img id="del_<?php echo $row->id;?>" src="/images/icons/24-del.png" title="<?php echo lang('delete');?>" /></a>
						<?php endif;?>
                     </div>
                </td>
              </tr>
			  <?php endif;?>
              <tr>
                <td valign="top" class="content">
                	<div class="pic">
                		<?php if(!empty($row->preview_url)):?>
							<?php if($auth > 1):?>
							<a href="/template/edit_screen?id=<?php echo $row->id;?>">
                    			<img src="<?php echo $row->preview_url;?>?t=<?php if(empty($row->update_time)){echo 0;}else{echo $row->update_time;}?>"  height="270" />
							</a>
							<?php else:?>
								<img src="<?php echo $row->preview_url;?>?t=<?php if(empty($row->update_time)){echo 0;}else{echo $row->update_time;}?>"  height="270" />
							<?php endif;?>
						<?php else:?>
							<a href="/template/edit_screen?id=<?php echo $row->id;?>">
								<img src="/images/media/video.gif" width="480" height="270" />
							</a>
						<?php endif;?>
                    </div>
                </td>
              </tr>
            </table>
			<h1>
			<?php if($this->config->item('mia_system_set') == $this->config->item('mia_system_all')) :?>
			<?php 
				if($row->template_type):
			?>
				<img src="/images/icons/android.png"  title="<?php echo lang('type.1');?>" />
			<?php else:?>
				<img src="/images/icons/windows.png"  title="<?php echo lang('type.0');?>" />
			<?php endif;?>
			<?php endif;?>
			&nbsp;&nbsp;
			<?php echo $row->name;?>  (<?php echo $row->width.'X'.$row->height;?>)</h1>
			<p>
				<?php echo $row->descr;?>
 			</p>
		</li>
		<?php 
		$idx++;
		endforeach;
		?>
	</ul>
</div>
<div class="clear"></div>
<?php
	$totalPage = intval(($total + ($limit - 1)) / $limit);
	$startIndex = 1;
	$endIndex   = $totalPage;
	$midIndex   = intval(($curpage + 5) / 2); 
	if($midIndex - 2 > $startIndex){
		$startIndex = $midIndex - 2;
	}
	if($midIndex + 2 < $endIndex){
		$endIndex = $midIndex + 2;
	}
?>
<?php if($totalPage > 1):?>
<div class="page-panel-center">
    <ul class="pagination">
    	
		<?php if($curpage>1):?>
			<li><a href="javascript:void(0);" onclick="template.index.page(<?php echo $type;?>, 1);"><?php echo lang('page.first');?></a></li>
			<li><a href="javascript:void(0);" onclick="template.index.page(<?php echo $type;?>, <?php echo $curpage-1;?>);"><?php echo lang('page.prev');?></a></li>	
		<?php endif;?>
    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
	    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
	    	<?php if($i == $curpage):?>
				<?php echo $i;?>
			<?php else:?>
				<a href="javascript:void(0);" onclick="template.index.page(<?php echo $type;?>, <?php echo $i;?>);"><?php echo $i;?></a>
			<?php endif;?>
		</li>
		<?php endfor;?>
		<?php if($curpage<$totalPage):?>
			<li><a href="javascript:void(0);" onclick="template.index.page(<?php echo $type;?>, <?php echo $curpage+1;?>);"><?php echo lang('page.next');?></a></li>
			<li><a href="javascript:vod(0);" onclick="template.index.page(<?php echo $type;?>, <?php echo $totalPage;?>);"><?php echo lang('page.last');?></a></li>	
		<?php endif;?>
  	</ul>
</div>
<?php endif;?>
<?php else:?>
<div style="color:#c0c0c0; margin-top:10px;">
	<b><?php echo lang('empty.template');?></b>
</div>
<?php endif;?>
<div id="templateConfirm" title="confirm" style="display:none;">
	<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 100px 0;"></span>Do you want to convert this template to NP200's format?</p>
</div>
