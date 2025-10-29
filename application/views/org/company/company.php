<div class="add-panel">
	<a href="/company/add?width=700&height=600" id="create" class="thickbox" title="<?php echo lang('create.company');?>"><?php echo lang('create');?></a>
</div>
<div class="clear"></div>
<h1 class="tit-01"><?php echo lang('company');?>&nbsp;&nbsp;( Total Online Player = <?php echo $all_player_count;?> )<span></span>
<div class="filter" style="width:70%;">
		<?php echo lang('filter.by');?>:
		<select id="filterType" name="filterType" style="width: 14%;margin:0px 4px;">
			<option value="name"><?php echo lang('name');?></option>
		</select>
		<input type="text" name="filter" id="filter" style="width:12%; margin-left:4px;">
		<a href="javascript:void(0);" class="btn-go" onclick="c.filter();"><label><?php echo lang('filter');?></label></a>
</div>
</h1>
<div id="layoutContent">
<?php
if(isset($body_view)){
	$this->load->view($body_view);
}
?>
</div>
 