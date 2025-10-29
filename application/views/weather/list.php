<div id="templateContent">	
	<div class="pageheader">
		<div class="container-fluid">

			<div class="pull-right">
				<a target="dialog" href="/weather/add?type=<?php echo $type;?>" data-toggle="tooltip" title="<?php echo lang('template.new');?>" class="btn btn-primary"><i class="fa fa-plus"></i></a> 

			</div>

			<h2><?php echo lang('weather.template');?></h2>
			</div>
		  
	</div>  
 	<div class="contentpanel">
 		<div class="row">
          		<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"></h3>
					</div>
					<div class="panel-body">
						<div class="row" >
						<?php if($total <= 0):?>
						<div style="color:#c0c0c0; margin-top:10px;">
							<b><?php echo lang('empty.template');?></b>
						</div>					
						<?php else:?>
							<div class="template-panel">
							<ul>
								<?php
								$idx = 0; 
								foreach($data as $row):
								?>
								<li>
									<table>
									<?php if($auth > 1):?>
						              <tr>
						                <td align="right">
						                	 <div class="operate">										
						                     	<a target="dialog" href="/weather/edit?id=<?php echo $row->id;?>&width=600&height=320" class="thickbox" title="<?php echo lang('edit.template');?>"><img id="edit_<?php echo $row->id;?>" src="/images/icons/24-edit.png" title="<?php echo lang('edit');?>" /></a>
											
											 	<?php if($auth > $STAFF):?>
											 	<a target="dialog" href="javascript:void(0);" onclick="weather.index.remove(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>',false,<?php if($idx == 0 || $curpage == 1){echo $curpage;}elseif($curpage>1){echo $curpage-1;} ?>);"><img id="del_<?php echo $row->id;?>" src="/images/icons/24-del.png" title="<?php echo lang('delete');?>" /></a>
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
												
													<a  href="javascript:void(0);" onclick="weather.index.goScreen(<?php echo $row->id;?>);">
						                    			<img src="<?php echo $row->preview_url;?>?t=<?php if(empty($row->update_time)){echo 0;}else{echo $row->update_time;}?>"  height="270" />
													</a>
													<?php else:?>
														<img src="<?php echo $row->preview_url;?>?t=<?php if(empty($row->update_time)){echo 0;}else{echo $row->update_time;}?>"  height="270" />
													<?php endif;?>
												<?php else:?>
													<a  href="javascript:void(0);" onclick="weather.index.goScreen(<?php echo $row->id;?>);">
														<img src="/images/media/video.gif" width="480" height="270" />
													</a>
												<?php endif;?>
						                    </div>
						                </td>
						              </tr>
						            </table>
									<h1>																	
									
									<?php echo $row->name;?>  (<?php echo $row->width.'X'.$row->height;?>)&nbsp;&nbsp;   
									<input type='checkbox' id='<?php echo $row->id;?>'  <?php if($row->flag) echo "checked=checked;"?>  onclick="onActive(this)">Active</h1>
								
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
						<?php endif?>
						</div>
						
				<?php
				$totalPage = floor(($total + ($limit - 1)) / $limit);
				
				$startIndex=($curpage>3)?$curpage-3:1;
				$endIndex= ($curpage<($totalPage-3)) ? ($curpage+3) : $totalPage;
				?>
				<?php if($totalPage > 1):?>
				    <ul class="pagination pull-right">
				    	
						<?php if($curpage>1):?>
							<li><a href="javascript:void(0);" onclick="weather.index.page(1);"><?php echo lang('page.first');?></a></li>
							<li><a href="javascript:void(0);" onclick="weather.index.page(<?php echo $curpage-1;?>);"><?php echo lang('page.prev');?></a></li>	
						<?php endif;?>
				    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
					    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
					    	<?php if($i == $curpage):?>
								<?php echo $i;?>
							<?php else:?>
								<a href="javascript:void(0);" onclick="weather.index.page( <?php echo $i;?>);"><?php echo $i;?></a>
							<?php endif;?>
						</li>
						<?php endfor;?>
						<?php if($curpage<$totalPage):?>
							<li><a href="javascript:void(0);" onclick="weather.index.page( <?php echo $curpage+1;?>);"><?php echo lang('page.next');?></a></li>
							<li><a href="javascript:vod(0);" onclick="weather.index.page( <?php echo $totalPage;?>);"><?php echo lang('page.last');?></a></li>	
						<?php endif;?>
				  	</ul>
				  <?php endif?>	
				  			<div id="templateConfirm" title="confirm" style="display:none;">
					<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 100px 0;"></span>Do you want to convert this template to NP200's format?</p>
				</div>              	
					</div>
				</div>
		</div>	
	</div>
</div>
<script type="text/javascript"> 

function onActive(checkbox){
	console.log("onActive!");

 	var curid = $(checkbox).attr('id');
 	var req = {
 		id: curid,
 		active:checkbox.checked?1:0
 	}

	if ( checkbox.checked == true){
 		$.each($('input:checkbox:checked'),function(){
    		if($(this).attr('id')!=curid){
    			$(this).prop('checked',false);
    		}	
        });
 	}    
 	$.post('/weather/avtive_template', req, function(data){
            if (data.code == 0) {
                showMsg(data.msg, 'success');
 
                            
             }

    }, 'json');
 
};


$(".upload").on("change","input[type='file']",function(){
	var oData = new FormData(document.forms.namedItem("fileinfo" ));  
	var oReq = new XMLHttpRequest();  
	oReq.open( "POST", "/weather/html5_import" , true );  
	oReq.onload = function() {  
		document.getElementById('myfile').value = "";

		var data = JSON.parse(oReq.responseText);
		console.log(data);
	
		 if (data.code != 0) {  
			 BootstrapDialog.alert(data.msg);
	     } else {  		     
	    	 
	    	 BootstrapDialog.alert(data.msg);
			setTimeout(function(){
				template.index.refresh();
				hideMsg();
			}, 1000);
	     }  
	};
	oReq.send(oData);  

});
</script>
   