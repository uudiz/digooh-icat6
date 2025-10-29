<?php if($auth > $GROUP):?>
<div class="add-panel">
	<a href="/folder/add?width=450&height=420" id="create" class="thickbox" title="<?php echo lang('create.folder');?>"><?php echo lang('create');?></a>
</div>
<?php endif;?>
<div class="clear"></div>
<h1 class="tit-01"><?php echo lang('folder');?><span></span></h1>
<div id="layoutContent">
<?php
if(isset($body_view)){
	$this->load->view($body_view);
}
?>
</div>

