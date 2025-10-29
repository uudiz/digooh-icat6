<div id="validateTips">
    <div>
        <div id="formMsgContent">
        </div>
    </div>
</div>
<h1 class="tit-01"><?php echo lang('playlist');?>
<div class="tit-02" style="float:right;margin:0px 16px;padding-right:100px;">
	<?php echo lang('filter.by');?>:
	<select id="filterType" name="filterType" style="width: 100px;margin:0px 4px;" onchange="schedule.form.changeFilterType(this);">
		<option value="name"><?php echo lang('name');?></option>
		<option value="date"><?php echo lang('create.date');?></option>
	</select>
	<input type="text" name="filter" id="filter" style="width:120px; margin-left:4px;">
	<label><?php echo lang('author');?></label>
	<select id="author" name="author" style="width: 100px;margin:0px 4px;">
		<option value="0"><?php echo lang('all');?></option>
		<?php if(!empty($users)):?>
		<?php foreach($users as $u):?>
		<option value="<?php echo $u->id?>"><?php echo $u->name;?></option>
		<?php endforeach;?>
		<?php endif;?>
	</select>
	<a href="javascript:void(0);" class="btn-go" style="margin-left:20px;" onclick="schedule.form.filterInteraction(<?php echo $sch_type;?>);"><label><?php echo lang('filter');?></label></a>
</div>
<span></span></h1>
<div id="playlistTable">
<?php $this->load->view($playlist_table);?>
</div>
<p class="btn-center">
	<a href="javascript:void(0);" onclick="schedule.form.addInteractionPlaylist('<?php echo lang('warn.scheudle.playlist.empty');?>');return false;" class="btn-01"><span><?php echo lang('button.add');?></span></a>
	<a href="javascript:void(0);" onclick="tb_remove();return false;" class="btn-01"><span><?php echo lang('button.cancel');?></span></a>
</p>