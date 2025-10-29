<div class="add-panel">
	<a href="/configxml/add?width=500&height=720" id="create" class="thickbox" title="<?php echo lang('device.config');?>"><?php echo lang('create');?></a>
</div>
<div class="clear"></div>

<h1 class="tit-01">
	<?php echo lang('device.setup');?>
	<div class="filter" style="width:70%;" >

		<div class="filter" style="padding-top: 0px; padding-right: 240px;">
			<?php echo lang('filter.by');?>:
			<div id="search" class="filter" style="padding-top: 10px; padding-right: 50px;">
				<input type="text" id="filter" style="width:150px;">
			</div>
			<div class="filter" style="padding-top: 7px; padding-right: 10px;">
				<a href="javascript:void(0);" class="btn-go" onclick="configxml.refresh();"><label><?php echo lang('filter');?></label></a>
			</div>
		</div>	
	</div>	
	<span></span>
</h1>
<div id="layoutContent">
<?php
if(isset($body_view)){
	$this->load->view($body_view);
}
?>
</div>



<script>
	document.onkeyup = function(event){
		if(event.keyCode == 13){
			configxml.refresh();
		}
	};

</script>