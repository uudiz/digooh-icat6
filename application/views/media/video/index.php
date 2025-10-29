
<div class="add-panel">
	<a href="/media/upload_videos?width=800&amp;height=500" id="create" class="thickbox" title="<?php echo lang('upload.videos');?>"><?php echo lang('upload');?></a>
</div>
<div class="clear"></div>

<h1 class="tit-01">
    <div class="tab-01">
        <a href="javascript:void(0);" <?php if($type == 1):?>class="on"<?php endif;?> type="1"><img src="/images/icons/icon-list.png" alt="<?php echo lang('view.list');?>" /><?php echo lang('view.list');?></a>
		<a href="javascript:void(0);" <?php if($type == 0):?>class="on"<?php endif;?> type="0"><img src="/images/icons/icon-grid.png" alt="<?php echo lang('view.grid');?>" /><?php echo lang('view.grid');?></a>
    </div>
	<div class="filter" style="width:70%;">
		<?php echo lang('filter.by');?>:
		<select id="filterType" name="filterType" style="width: 14%;margin:0px 4px;" onchange="mediaLib.changeFilterType(this);">
			<option value="name"><?php echo lang('name');?></option>
			<option value="tag_name"><?php echo lang('tag');?></option>
		</select>
		<input type="text" name="filter" id="filter" style="width:12%; margin-left:4px;">
		<label style="margin-left:20px;"><?php echo lang('filter.folder');?>:</label>
		
		<?php if($this->config->item('with_sub_folders')):?>
			<input id="folderSelTop" type="text" readonly value="<?php echo lang('all');?>" style="width:120px;"/>
			<input type="hidden" id="filterFolder" name="folderId" value="-1"/>
		<?php else:?>
			<select id="filterFolder" name="filterFolder" style="width: 14%; margin:0px 2px;" onchange="mediaLib.filter();">
				<option value="-1" ><?php echo lang('all');?></option>
				<?php if($auth > 1):?>
				<option value="0" ><?php echo lang('folder.default');?></option>
				<?php endif ?>
				<?php if(isset($folders)):?>
					<?php foreach($folders as $f):?>
						<option value="<?php echo $f->id;?>"><?php if(mb_strlen($f->name) > 24){echo mb_substr($f->name, 0, 24).'..';}else{echo $f->name;}?></option>
					<?php endforeach;?>
				<?php endif;?>
			</select>
		<?php endif;?>
		<a href="javascript:void(0);" class="btn-go" onclick="mediaLib.filter();"><label><?php echo lang('filter');?></label></a>
	</div>
    <span></span>
</h1>
<div id="layoutContent" type="<?php echo $type;?>">
<?php
if(isset($body_view)){
	$this->load->view($body_view);
}
?>
</div>

<input type="hidden" id="orderItem" value="<?php echo $order_item;?>" />
<input type="hidden" id="order" value="<?php echo $order;?>" />
<input type="hidden" id="curpage" value="<?php echo $curpage;?>" />
<?php if($this->config->item('with_sub_folders')):?>
<div id="menuContent" class="menuContent" style="display:none; position: absolute;">
	<ul id="treeDemo" class="ztree" style="margin-top:0; width:150px;"></ul>
</div>
<script type="text/javascript">
		var cur_id = false;
		var setting = {
			view: {
				dblClickExpand: false,
				showIcon: false,
				selectedMulti: false
			},
			data: {
				simpleData: {
					enable: true
				}
			},
			callback: {
				onClick: onClick
			}
		};

		var zNodes = eval(<?php echo json_encode($folders)?>);
		
		function onClick(e, treeId, treeNode) {
			console.log(treeId);
			var zTree = $.fn.zTree.getZTreeObj("treeDemo"),
			nodes = zTree.getSelectedNodes(),
			v = "";
			id="";
			v = nodes[0].name;
			id=nodes[0].id;

			cur_obj =  $("#"+cur_id);
			cur_obj.attr("value", v);

			hideMenu();
			if(cur_id=='folderSelTop'){
				$("#filterFolder").val(id);
				mediaLib.filter();
			}else{
				$("#folderId").val(id);
			}
		
		}

		function showMenu(id) {
			var obj = $("#"+id);
			var objOffset = obj.offset();
			cur_id = id;
			$("#menuContent").css({left:objOffset.left + "px", top:objOffset.top + obj.outerHeight() + "px"}).slideDown("fast");
		}
		function hideMenu() {
			$("#menuContent").fadeOut("fast");		
		}

		$(document).ready(function(){
			$.fn.zTree.init($("#treeDemo"), setting, zNodes);
			var treeObj = $.fn.zTree.getZTreeObj("treeDemo");
			treeObj.expandAll(true);
			$("#folderSelTop").unbind("click");
			$("#folderSelTop").click(function(){
				if($("#menuContent").css("display") == "block"){
					hideMenu();
				}
				else{
                    showMenu(this.id);
				}
         	   	 	
 			}); 
			$("#folderSel").unbind("click");
			 $("#folderSel").click(function(){
				if($("#menuContent").css("display") == "block"){
					hideMenu();
				}
				else{
                    showMenu(this.id);
				}		 	
 			}); 
		});

</script>
<?php endif;?>
<script>
	mediaLib.init();
</script>