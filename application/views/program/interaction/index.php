<h1 class="tit-01"><?php echo lang('interaction');?></h1>
<?php if($auth >= $FRANCHISE):?>
<div id="UploadProgress" style="display: none;"></div>
<div class="add-panel">
	<a href="javascript:void(0);" title="<?php echo lang('template.import');?>"><div id="importBtn" style="width: 50px; height: 18px;"><img src="/images/icons/import.png" /></div></a>&nbsp;
	<a href="/interaction/add?width=450&height=300" id="create" class="thickbox" title="<?php echo lang('create.interaction');?>"><?php echo lang('create');?></a>
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
			var errorHandler = function(id, fileName, reason) {
			    return qq.log("id: " + id + ", fileName: " + fileName + ", reason: " + reason);
			};
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
			       	//itemLimit: 4
				},
				request: {
					endpoint: "/interaction/html5_import"
				},
				deleteFile: {
					enabled: false
			  	},
			 	resume: {
			   		enabled: true
			  	},
				retry: {
			  		enableAuto: false
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
			        	//qq.log("id: " + id + " " + newS);
			 		},
			    	onComplete: function (id, name, response) {
			      		if(response.success == true) {
			      			showMsg(response.msg);
							setTimeout(function(){
								interaction.refresh();
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
<?php if($total > 0):?>
<div class="template-panel">
	<ul>
		<?php
		$idx = 0; 
		foreach($data as $row):
		?>
		<li>
			<table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="right">
                	 <div class="operate">
						<?php if($auth > 0):?>
                     	<a href="/interaction/edit?id=<?php echo $row->id;?>&width=600&height=320" class="thickbox" title="<?php echo lang('edit.template');?>"><img id="edit_<?php echo $row->id;?>" src="/images/icons/24-edit.png" title="<?php echo lang('edit');?>" /></a>
						<a href="/interaction/export?id=<?php echo $row->id;?>"><img id="export_<?php echo $row->id;?>" src="/images/icons/24-export.png" title="<?php echo lang('export');?>" /></a>
					 	<a href="javascript:void(0);" onclick="interaction.remove(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>');"><img id="del_<?php echo $row->id;?>" src="/images/icons/24-del.png" title="<?php echo lang('delete');?>" /></a>
						<?php endif;?>
                     </div>
                </td>
              </tr>
              <tr>
                <td valign="top" class="content">
                	<div class="pic">
                		<?php if(!empty($row->preview_url)):?>
							<a href="/interaction/create_interaction_date?id=<?php echo $row->id;?>">
                    			<img src="<?php echo $row->preview_url;?>?t=<?php echo time();?>"  height="270" />
							</a>
						<?php else:?>
							<a href="/interaction/create_interaction_date?id=<?php echo $row->id;?>">
								<img src="/images/media/video.gif" width="480" height="270" />
							</a>
						<?php endif;?>
                    </div>
                </td>
              </tr>
            </table>
			<h1>
			<?php if($this->config->item('mia_system_set') == $this->config->item('mia_system_all')) :?>
				<img src="/images/icons/android.png"  title="<?php echo lang('type.1');?>" />
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
			<li><a href="/interaction/index/1/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.first');?></a></li>
			<li><a href="/interaction/index/<?php echo $curpage-1;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.prev');?></a></li>	
		<?php endif;?>
		<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
		<li <?php if($i == $curpage):?>class="active"<?php endif;?>>
			<?php if($i == $curpage):?>
			<?php echo $i;?>
			<?php else:?>
				<a href="/interaction/index/<?php echo $i;?>/<?php echo $order_item.'/'.$order;?>"><?php echo $i;?></a>
			<?php endif;?>
		</li>
		<?php endfor;?>
		<?php if($curpage<$totalPage):?>
		<li><a href="/interaction/index/<?php echo $curpage+1;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.next');?></a></li>
		<li><a href="/interaction/index/<?php echo $totalPage;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.last');?></a></li>	
		<?php endif;?>
  	</ul>
</div>
<?php endif;?>
<?php else:?>
<div style="color:#c0c0c0; margin-top:10px;">
	<b><?php echo lang('empty.template');?></b>
</div>
<?php endif;?>
