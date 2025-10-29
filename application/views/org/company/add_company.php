<head>
<?php if ($this->config->item('with_partners')&&(isset($parent_id)&&$parent_id)): ?>
<link rel="stylesheet" href="/static/css/jquery/chosen.min.css" />
<link rel="stylesheet" href="/static/css/zTreeStyle.css" />
<link rel="stylesheet" href="/static/css/user.css" />
<script src='/static/js/jquery/chosen.jquery.min.js'></script>
<script src='/static/js/jquery/jquery.ztree.core.min.js'></script>
<script src="/static/js/jquery/jquery.ztree.excheck.js"></script>
<?php endif;?>
</head>
<div id="validateTips">
    <div>
        <div id="formMsgContent">
        </div>
    </div>
</div>
<form method="POST" id="cf" action="/company/do_save" enctype='multipart/form-data'>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="from-panel">
        <tbody>
            <tr>
                <td>
                    <?php echo lang("name"); ?>
                </td>
                <td>
                    <input type="text" id="name" name="name" />
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo lang("desc"); ?>
                </td>
                <td>
                    <textarea name="descr" id="descr" class="ui-widget-content ui-corner-all" rows="2"></textarea>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo lang("max.user"); ?>
                </td>
                <td>
                    <input type="text" id="max_user" name="max_user"  value="<?php echo $max_user; ?>" />
                </td>
            </tr>
			<tr >
				<td>
					<?php echo lang("max.disk");?>
				</td>
				<td>
					<input type="text" id="total_disk" name="total_disk"  value="<?php echo $total_disk;?>" />
				</td>
			</tr>
			<?php if (!isset($parent_id)||!$parent_id): ?>
            <tr>
                <td>
                    <?php echo lang("start.date"); ?>
                </td>
                <td>
                    <input type="text" id="start_date" name="start_date" class="date" readonly="readonly" value="<?php echo $start_date; ?>"/>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo lang("end.date"); ?>
                </td>
                <td>
                    <input type="text" id="stop_date" name="stop_date" class="date" readonly="readonly" value="<?php echo $stop_date; ?>"/>
                </td>
            </tr>
			<?php endif ?>
			<?php if ($this->config->item('with_partners')&&(isset($parent_id)&&$parent_id)): ?>
			<tr >
				<td>
					<?php echo lang("actived");?>
				</td>
				<td>
					<input type="checkbox" id="active" name="active" />
				</td>
			</tr>
			<tr >
				<td>
					<?php echo lang('shared.criteria');?>
				</td>
				<td>
				<?php if (isset($criteria)&&!empty($criteria)):?>
				<select  data-placeholder="Choose Criterion" id="criteria-select-options" class="chosen-select tag-input-style">
						<option value="0"> </option>
						<?php foreach ($criteria as $crit):?>
							<option value="<?php echo $crit->id;?>" ><?php echo $crit->name;?></option>
						<?php endforeach;?>
							
				</select>         
				<?php endif;?> 
			  				<span> 	<input type="number" id="quota" name="quota" max='100' min='1'   />%</span>
				</td>

			</tr>
			<tr >
				<td>
					<?php echo lang('shared.players');?>
				</td>
				<td>
	
				<select  data-placeholder="Select Players..." id="players-select-options" class="chosen-select tag-input-style" multiple>
						<option value="0"> </option>
						<?php foreach ($players as $crit):?>
							<option value="<?php echo $crit->id;?>" ><?php echo $crit->name;?></option>
						<?php endforeach;?>
							
				</select>           
		
			  	<span> 	<input type="number" id="player_quota" name="player_quota" max='100' min='1'/>%</span>
				</td>

			</tr>
			<tr >
				<td>
					<?php echo lang("share.block");?>
				</td>
				<td>
					<input type="checkbox" id="shareblock" name="shareblock"  checked="checked"/>
				</td>
			</tr>
			</tr>
			<tr id="assignFolderLine" >
				<td>
					<?php echo lang('select.folders');?>
				</td>
				<td>
					<div class="zTreeDemoBackground left">
						<ul id="treeFolder" class="ztree"></ul>
					</div>

				</td>
			</tr>			
			<tr >
				<td>
					<?php echo lang("cust.field.name1");?>
				</td>
				<td>
					<input  id="cust_filed1"   />
				</td>
			</tr>
			<tr >
				<td>
					<?php echo lang("cust.field.name2");?>
				</td>
				<td>
					<input  id="cust_filed2" />
				</td>
			</tr>			
			<?php else:?>
			
			<tr>
				<td><?php echo lang("device.setup.control");?></td>
				<td>
					<select id="device_setup" name="device_setup" style="width: 120px">
						<option value="on" ><?php echo lang("device.setup.control.on");?></option>
						<option value="off" selected="selected"><?php echo lang("device.setup.control.off");?></option>
					</select>
				</td>
			</tr>

			<tr>
				<td><?php echo lang("auto.dst");?></td>
				<td>
					<select id="auto_dst" name="auto_dst" style="width: 120px">
						<option value="0" selected="selected"><?php echo lang('auto_dst_enable');?></option>
						<option value="1" ><?php echo lang('auto_dst_disable');?></option>
					</select>
				</td>
			</tr>
			

            <tr>
				<td><?php echo lang("comm.interval");?></td>
				<td>
				<input type="number" class="form-control" min="1" max="60" id="com_imterval" placeholder="" value="1">(<?php echo lang('valid.interval')?>)
				</td>
			</tr>  
			<?php if ($this->config->item('xslot_on')):?>
            <tr>
				<td><?php echo lang("campaign.count.xslot");?></td>
				<td>
				<input type="number" class="form-control" min="1" max="10" id="xslot" placeholder="" value="5">(<?php echo lang('valid.xslot')?>)
				</td>
			</tr>  			  
			<?php endif;?>
			<tr>
				<td>JPG Play Time</td>
				<td>
				<input type="number" class="form-control" min="1" max="60" id="playtime" placeholder="" value="10">(Seconds)
				</td>
			</tr> 
			<?php endif;?>


			<tr>
				<td>Theme Color</td>
				<td>
				<input id='colorpicker' />
				</td>
			</tr> 
			

            <tr>
				<td>Cost per Play</td>
				<td >
					<input type="number" class="form-control" min="0.00" max="10.00" step='0.01' id="cost_default" placeholder="" value="0.01">€
				</td>
			</tr>
            <tr>
				<td>Scale price 1</td>
				<td>
					<input type="number" class="form-control" min="0.00" step='0.001' max="10.00" id="cost1" placeholder="" value="0.009" widtd='30px'>€
				</td>
				<td>For playing ad >=</td>
				<td>
					<input type="number" class="form-control" min="1" max="10000000" step='1' id="cost_condition1" placeholder="" value="100000">(Times)
				</td>
			</tr>
            <tr>
				<td>Scale price 2</td>
				<td>
					<input type="number" class="form-control" min="0.00" step='0.001' max="10.00" id="cost2" placeholder="" value="0.008" widtd='30px'>€
				</td>
				<td>For playing ad >=</td>
				<td>
					<input type="number" class="form-control" min="1" max="10000000" step='1' id="cost_condition2" placeholder="" value="250000">(Times)
				</td>
			</tr>
		
        </tbody>
    </table>

    <p class="btn-center">
		<input type="hidden" id="parent_id" value="<?php echo isset($parent_id)?$parent_id:0; ?>"	/>
    	<input type="hidden" id="custom_logo" name="custom_logo" value="" />
        <a class="btn-01" href="javascript:void(0);" onclick="c.doSave(this);"><span><?php echo lang('button.save'); ?></span></a>
        <a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel'); ?></span></a>
    </p>
</form>
<div id="custom">
    <div class="logo">
    </div>
	<div style="display: inline; border: solid 1px #7FAAFF; padding: 2px;">
        <span id="spanButtonPlaceholder"><?php echo lang("select.custom.logo");?></span>
    </div>
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
	<script>
			$("#colorpicker").spectrum({
		        color: "#951b80"
		    });

			var errorHandler = function(id, fileName, reason) {
			    return qq.log("id: " + id + ", fileName: " + fileName + ", reason: " + reason);
			};
			var settings = {
				element: document.getElementById("UploadProgress"),
				button: document.getElementById("spanButtonPlaceholder"),
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
					endpoint: "/company/upload_logo"
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
			      			$('#custom_logo').val(response.file_name);
							$('.logo').css('background-image','url(/images/logos/'+response.file_name+')');
				      	}else {
				      		alert(response.msg);
					    }
			   		}
				}
			};
			fileuploader = new qq.FineUploader(settings);
	</script>
</div>

<script>
	c.init();
	<?php if ($this->config->item('with_partners')&&(isset($parent_id)&&$parent_id)): ?>
		var setting = {
			check: {
				enable: true,
				chkStyle: "radio",
            	radioType: "all"
			},
			data: {
				simpleData: {
					enable: true
				}
			}
		};

		var zNodes;
		<?php
           $folder_ary = array();
           if (isset($folders)) {
               $folder_ary = $folders;
           }
           
        ?>
		
		zNodes = eval(<?php echo json_encode($folder_ary)?>);

		
		$(document).ready(function(){	
			$('.chosen-select').chosen({width: "180px"}); 

			$.fn.zTree.init($("#treeFolder"), setting, zNodes);	
			var treeObj = $.fn.zTree.getZTreeObj("treeFolder");
			treeObj.expandAll(true);				
		});
	<?php endif?>	
</script>
