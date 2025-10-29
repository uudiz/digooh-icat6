<div id="validateTips" >
	<div <?php if(!empty($tip_msg)):?>class="attention" <?php endif;?>>
		<div id="formMsgContent">
			<?php echo $tip_msg;?>
		</div>
	</div>
</div>
<h2 class="tit-01">
    <div class="tab-02">
        <a href="javascript:void(0);" <?php if($type == 1):?>class="on"<?php endif;?> type="1"><img src="/images/icons/icon-list.png" alt="<?php echo lang('view.list');?>" /><?php echo lang('view.list');?></a>
		<a href="javascript:void(0);" <?php if($type == 0):?>class="on"<?php endif;?> type="0"><img src="/images/icons/icon-grid.png" alt="<?php echo lang('view.grid');?>" /><?php echo lang('view.grid');?></a>
    </div>
	<div class="filter" style="float:right;margin:0px 16px;padding-right:2px;">
		<?php echo lang('filter.by');?>:
		<select id="filterType" name="filterType" style="width: 100px;margin:0px 4px;" onchange="interactionpls.changeFilterType(this);">
			<option value="name"><?php echo lang('media.name');?></option>
			<option value="add_time"><?php echo lang('upload.date');?></option>
		</select>
		<input type="text" name="filter" id="filter" style="width:120px; margin-left:4px;">
		<label style="margin-left:20px;"><?php echo lang('filter.folder');?>:</label>
		<select id="filterFolder" name="filterFolder" style="width: 150px; margin:0px 4px;" onchange="interactionpls.addAreaMediaFilter(<?php echo $playlist_id.','.$area_id.',\''.$bmp.'\','.$media_type.',1';?>);">
			<option vlaue="0" <?php if($folder == -1):?>selected<?php endif;?>><?php echo lang('all');?></option>
			<option value="0" <?php if($folder == 0):?>selected<?php endif;?>><?php echo lang('folder.default');?></option>
			<?php if(isset($folders)):?>
				<?php foreach($folders as $f):?>
					<option value="<?php echo $f->id;?>" <?php if($folder == $f->id):?>selected<?php endif;?>><?php echo $f->name;?></option>
				<?php endforeach;?>
			<?php endif;?>
		</select>
		<label style="margin-left:20px;"><?php echo lang('filter.uploader');?>:</label>
		<select id="filterUploader" name="filterUploader" style="width: 80px; margin:0px 4px;">
			<option vlaue="0"><?php echo lang('all');?></option>
			<?php if(isset($users)):?>
				<?php foreach($users as $u):?>
					<option value="<?php echo $u->id;?>"><?php echo $u->name;?></option>
				<?php endforeach;?>
			<?php endif;?>
		</select>
		<a href="javascript:void(0);" class="btn-go" style="margin-left:20px;" onclick="interactionpls.addAreaMediaFilter(<?php echo $playlist_id.','.$area_id.',\''.$bmp.'\','.$media_type.',1';?>);"><label><?php echo lang('filter');?></label></a>
	</div>
</h2>

<div id="layoutContent" type="<?php echo $type;?>">
	<?php $this->load->view($body_view);?>
</div>
<input type="hidden" id="bmp" value="<?php echo $bmp;?>"/>
<input type="hidden" id="screenId" value="<?php echo $screenId;?>"/>
<input type="hidden" id="areaId" value="<?php echo $area_id;?>"/>
<input type="hidden" id="mediaType" value="<?php echo $media_type;?>"/>
<input type="hidden" id="orderItem" value="<?php echo $order_item;?>" />
<input type="hidden" id="order" value="<?php echo $order;?>" />
<input type="hidden" id="curpage" value="<?php echo $curpage;?>" />
<input type="hidden" id="type" value="<?php echo $type;?>" />
<script>
	interactionpls.initMediaPanel();
</script>