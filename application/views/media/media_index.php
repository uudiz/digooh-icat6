<div class="add-panel">
	<a href="/media/<?php echo $target ?>?width=800&amp;height=530" id="create" class="thickbox" title="<?php echo lang('upload.images'); ?>"><?php echo lang('upload'); ?></a>
</div>

<div class="clear"></div>
<h1 class="tit-01">
	<div class="tab-01">
		<a href="javascript:void(0);" <?php if ($type == 1) : ?>class="on" <?php endif; ?> type="1"><img src="/images/icons/icon-list.png" alt="<?php echo lang('view.list'); ?>" /><?php echo lang('view.list'); ?></a>
		<a href="javascript:void(0);" <?php if ($type == 0) : ?>class="on" <?php endif; ?> type="0"><img src="/images/icons/icon-grid.png" alt="<?php echo lang('view.grid'); ?>" /><?php echo lang('view.grid'); ?></a>
	</div>
	<div class="filter" style="width:70%;">
		<label><?php echo lang('filter.by'); ?>:</label>
		<select id="filterType" name="filterType" class="select2" style="width: 10%;margin:0px 4px;" onchange="mediaLib.changeFilterType(this);">
			<option value="name"><?php echo lang('name'); ?></option>
			<option value="tag_name"><?php echo lang('tag'); ?></option>
		</select>
		<input type="text" name="filter" id="filter" style="width:12%; margin-left:4px;">
		<label style="margin-left:20px;"><?php echo lang('filter.folder'); ?>:</label>
		<select id="filterFolder" name="filterFolder" style="width: 14%;margin-left:4px;" onchange="mediaLib.filter();">
			<option value="-1"><?php echo lang('all'); ?></option>
			<?php if ($auth > 1) : ?>
				<option value="<?php echo $root_id; ?>"><?php echo lang('folder.default'); ?></option>
			<?php endif ?>
			<?php if (!$this->config->item('with_sub_folders')) : ?>
				<?php if (isset($folders)) : ?>
					<?php foreach ($folders as $f) : ?>
						<option value="<?php echo $f->id; ?>"><?php if (mb_strlen($f->name) > 64) {
																	echo mb_substr($f->name, 0, 64) . '..';
																} else {
																	echo $f->name;
																} ?> </option>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endif; ?>
		</select>


		<a href="javascript:void(0);" class="btn-go" onclick="mediaLib.filter();"><label><?php echo lang('filter'); ?></label></a>
	</div>
	<span></span>
</h1>
<div id="layoutContent" type="<?php echo $type; ?>">
	<?php
	if (isset($body_view)) {
		$this->load->view($body_view);
	}
	?>
</div>
<input type="hidden" id="orderItem" value="<?php echo $order_item; ?>" />
<input type="hidden" id="order" value="<?php echo $order; ?>" />
<input type="hidden" id="curpage" value="<?php echo $curpage; ?>" />
<style type="text/css">

</style>


<script>
	mediaLib.init();
	<?php if ($this->config->item('with_sub_folders')) : ?>
		$(document).ready(function() {
			var mydata = <?php echo $folders; ?>;
			$("#filterFolder").select2ToTree({
				treeData: {
					dataArr: mydata
				} /*, maximumSelectionLength: 3*/
			});

		});
	<?php endif ?>
</script>