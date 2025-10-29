<div class="add-panel">
	<!--
	<a href="/authorize/add?width=300&height=200" id="create" class="thickbox" title="<?php echo lang('create.user');?>"><?php echo lang('create');?></a>
	-->
	<a href="javascript:void(0);" onclick="au.doSave()" id="create" class="add" title="<?php echo lang('create.user');?>"><?php echo lang('create');?></a>
	
</div>
<div class="clear"></div>
<h1 class="tit-01">
	Authorization
</h1>
<div id="layoutContent">
<?php
if(isset($body_view)){
	$this->load->view($body_view);
}
?>
</div>
<script>
	//u.doJson();
</script>