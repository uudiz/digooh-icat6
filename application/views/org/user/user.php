<?php if(!$super):?>
<div class="add-panel">
	<a href="/user/add?width=600&height=720" id="create" class="thickbox" title="<?php echo lang('create.user');?>"><?php echo lang('create');?></a>
</div>
<?php endif;?>
<div class="clear"></div>
<h1 class="tit-01">
	<?php echo lang('user');?>
	<div class="filter" style="width:70%;" >
		<div class="filter" style="padding-top: 0px; padding-right: 190px;">
		<?php echo lang('filter.by');?>:
			<select id="filterType" name="filterType" style="width: 120px;">
				<option value="uname"><?php echo lang('user_name');?></option>
				<option value="cname"><?php echo lang('company');?></option>
			</select>
		</div>
		<div id="search" class="filter" style="padding-top: 8px; padding-right: 20px;">
			<input type="text" name="filter" id="filter" style="width:150px;">
			<input type="hidden" class="input-medium" name="submit_json" id="submit_json">
		</div>
		<div class="filter" style="padding-top: 7px; padding-right: -20px;">
			<a href="javascript:void(0);" class="btn-go" onclick="u.filter();"><label><?php echo lang('filter');?></label></a>
		</div>
	</div>
</h1>
<div id="layoutContent">
<?php
if(isset($body_view)){
	$this->load->view($body_view);
}
?>
</div>
<style type="text/css">
#search {
	text-align: right;
}
.autocomplete {
	border: 1px solid #9ACCFB;
	background-color: white;
	text-align: left;
	width: 160px;
	padding-right: -60px;
}
.autocomplete li {
	list-style-type: none;
	color: #000;
	height: 30px;
	width: 160px;
	font-size: 16px;
	padding-top: 3px;
}
.clickable {
	cursor: default;
}
.highlight {
	background-color: #4876FF;
}
</style>
<script>
	u.doJson();
</script>