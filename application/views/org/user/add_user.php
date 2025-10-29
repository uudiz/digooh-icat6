<head>

<link rel="stylesheet" href="/static/css/jquery/chosen.min.css" />
<link rel="stylesheet" href="/static/css/zTreeStyle.css" />
<link rel="stylesheet" href="/static/css/user.css" />
<script src='/static/js/jquery/jquery.ztree.core.min.js'></script>
<script src="/static/js/jquery/jquery.ztree.excheck.js"></script>
<script src='/static/js/jquery/chosen.jquery.min.js'></script>
<script src='/static/fileuploader/all.fine-uploader.min.js'></script>

</head>
<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
	<div id="waitting" class="information" style="display:none;margin:10;width:94%;">
		<img src="/images/loading.gif"> &nbsp;Processing, please wait....		
	</div>
</div>
<form method="POST" id="cf" action="/user/do_save" >
	<table width="600" cellspacing="0" cellpadding="0" border="0" class="add_user">
<style>
.add_user td {
	text-align: left;
	padding: 8px 8px;
	font-size: 14px;
}
.table-list td{
	height: 15px;
}
.logo {
    border: 0.1em;
    border-style: dotted;
    width: 200px;
    height: 70px;
}
</style>
		<tbody>
			<tr>
				<td>
					<?php echo lang('user_name');?>
				</td>
				<td>
					<input type="text" id="name" name="name" style="width:200px;"/>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang('password');?>
				</td>
				<td>
					<input type="password" id="password" name="password" style="width:200px;"/>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang('email_address');?>
				</td>
				<td>
					<input type="text" id="email" name="email" style="width:200px;"/>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang("desc");?>
				</td>
				<td>
					<textarea name="descr" id="descr"  rows="2" style="width:200px;"></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang('rule');?>
				</td>
				<td>
					<input type="radio" class="rule" name="auth" value="0"  onclick="u.changeAuth(this);" />
					<?php echo lang('rule.view');?>&nbsp;&nbsp;&nbsp;
				
					<input type="radio" class="rule" name="auth" value="1" onclick="u.changeAuth(this);" />
					<?php echo lang('rule.franchise');?>&nbsp;&nbsp;&nbsp;
					
					<!--
					<input type="radio" class="rule" name="auth" value="2" onclick="u.changeAuth(this);" />
					<?php echo lang('role.partner');?>&nbsp;&nbsp;&nbsp;
					-->
					<input type="radio" class="rule" name="auth" value="4" <?php if ($auth == $ADMIN): ?>  checked="checked" <?php endif;?> onclick="u.changeAuth(this);"/>
					<?php echo lang('role.staff');?>&nbsp;&nbsp;&nbsp;
					
					<?php if ($auth > $ADMIN): ?>
					<input type="radio" class="rule" name="auth" value="5" checked="checked" onclick="u.changeAuth(this);"/>
					<?php echo lang('rule.admin');?>
					<?php endif?>
				</td>
			</tr>

		<tr id="canPublishLine" <?php if ($auth != $VIEW):?>style="display:none;"<?php endif;?>>
			<td>
				<?php echo lang('can.publish');?>
			</td>
			<td>
				<input type="radio"  name="publish" value="1" />
				Yes&nbsp;&nbsp;&nbsp;
				 
				<input type="radio"  name="publish" value="0" checked="checked" />
				No&nbsp;&nbsp;&nbsp;
			</td>			
		</tr>

		<tr id="assignFolderLine" <?php if ($auth >= $ADMIN):?>style="display:none;"<?php endif;?>>
			<td>
				<?php echo lang('select.folders');?>
			</td>
			<td>
				<?php if (isset($folders)&&!empty($folders)):?>
				<?php if ($this->config->item('with_sub_folders')):?>
				<div class="zTreeDemoBackground left">
					<ul id="treeFolder" class="ztree"></ul>
				</div>
				<?php else:?>

				<select  data-placeholder="Choose Folders..." id="folder-select-options" class="chosen-select tag-input-style" multiple>
						<?php foreach ($folders as $folder):?>
							<option value="<?php echo $folder->id;?>"><?php echo $folder->name;?></option>
						<?php endforeach;?>
				</select>   
				<?php endif;?>
				<?php endif;?>
			</td>
		</tr>
			
		<tr class="assignCriteriaLine" <?php if ($auth >= $ADMIN):?>style="display:none;"<?php endif;?>>
			<td>
				<input type="radio" id="usecriteria" name="useplayer"  value=0 >
				<?php echo lang('select.criterias');?>
			</td>
			<td>
				<?php if (isset($criterias)&&!empty($criterias)):?>
				<select  data-placeholder="Choose Criterias..." id="criteria-select-options" class="chosen-select tag-input-style" multiple>
						<option value="0"></option>
						<?php foreach ($criterias as $crit):?>
							<option value="<?php echo $crit->id;?>"><?php echo $crit->name;?></option>
						<?php endforeach;?>
				</select>         
	
				<?php endif;?>
			</td>
		</tr>


		<tr class="assignCriteriaLine" <?php if ($auth >= $ADMIN):?>style="display:none;"<?php endif;?>>
		
			<td>
				<input type="radio" id="useplayer" name='useplayer' checked value=1>
				<?php echo lang('select.players');?>
			</td>
			<td>
				<?php if (isset($players)&&!empty($players)):?>
				<select  data-placeholder="Choose Players..." id="player-select-options" class="chosen-select tag-input-style" multiple>
						<option value="0"></option>
						<?php foreach ($players as $crit):?>
							<option value="<?php echo $crit->id;?>" ><?php echo $crit->name;?></option>
						<?php endforeach;?>
				</select>         
	
				<?php endif;?>
			</td>
		</tr>

		
		<tr id="assignCampaignLine" <?php if ($auth >= $ADMIN):?>style="display:none;"<?php endif;?>>
			<td>
				<?php echo lang('select.campaigns');?>
			</td>
			<td>
				<?php if (isset($campaigns)&&!empty($campaigns)):?>
				<select  data-placeholder="Choose Campaigns..." id="campaign-select-options" class="chosen-select tag-input-style" multiple>
						<option value="0"></option>
						<?php foreach ($campaigns as $crit):?>
							<option value="<?php echo $crit->id;?>"><?php echo $crit->name;?></option>
						<?php endforeach;?>
				</select>         
	
				<?php endif;?>
			</td>
		</tr>

		<tr id="airtimeLine" <?php if ($auth != 2):?>style="display:none;"<?php endif;?>>
			<td>
				<?php echo lang('air.time');?>
			</td>
			<td>
				<input id="air_time_input" type="number"  min=0 max=100 value=" " /><span>%</span>
			</td>			
		</tr>

		<tr>
			<td>
				logo
			</td>
			<td>

				<div class="logo"  <?php if (isset($user->logo)&&!empty($user->logo)):?>style="background-image:url(/images/logos/<?php echo $user->logo?>);"<?php endif;?>></div>

					<a id="uploader" >browse</a>
					<a id="reset" onclick="u.resetLogo()">reset</a>
		
					<div id="UploadProgress" style="display: none;"></div>
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
			</td>

		</tr>			
		</tbody>
	</table>
	<p class="btn-center">
		<input type="hidden" id="custom_logo" name="custom_logo" value="" />
		<input type="hidden" name="cid" id="cid" value="<?php echo $cid;?>"/>
		<input type="hidden" name="save_type" id="save_type" value=1 />
    	<a class="btn-01" href="javascript:void(0);" onclick="u.doSave();"><span><?php echo lang('button.save');?></span></a>
		<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
    </p>

<script type="text/javascript">
$('.chosen-select').chosen({width: "400px"}); 
var errorHandler = function(id, fileName, reason) {
			    return qq.log("id: " + id + ", fileName: " + fileName + ", reason: " + reason);
			};
	var settings = {
				element: document.getElementById("UploadProgress"),
				button: document.getElementById("uploader"),
				autoUpload: true,
			    debug: false,
			    uploadButtonText: "Select Files",
				display: {
			 		fileSizeOnSubmit: true
				},
				validation: {
					allowedExtensions: ["jpg", "jpeg","png"],
			        sizeLimit: 1000000,
			       	//itemLimit: 4
				},
				request: {
					endpoint: "/user/upload_logo"
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
			    	onComplete: function (id, name, response) {
			      		if(response.success == true) {
							$('#custom_logo').val(response.file_name);
							$('.logo').css('background-image','url(/images/logos/'+response.file_name+')');
				      	}else {
				      		alert(response.msg);
					    }
			   		}
				}
			};
			fileuploader = new qq.FineUploader(settings);

<?php if ($this->config->item('with_sub_folders')):?>
		var setting = {
			check: {
				enable: true,
				chkboxType: { "Y": "s", "N": "s" }
			},
			data: {
				simpleData: {
					enable: true
				}
			}
		};

		var zNodes = eval(<?php echo json_encode($folders)?>);;
		
		$(document).ready(function(){	
			$.fn.zTree.init($("#treeFolder"), setting, zNodes);	
			var treeObj = $.fn.zTree.getZTreeObj("treeFolder");
			treeObj.expandAll(true);	
		});
<?php endif?>
</script>