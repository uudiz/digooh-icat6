<h1 class="tit-01"><?php echo lang('template');?>
    <div class="tab-01">
    	<?php if($super):?>
        <a href="javascript:void(0);" <?php if($type == 1):?>class="on"<?php endif;?> type="1"><?php echo lang('template.system');?></a>
		<?php else:?>
		<a href="javascript:void(0);" <?php if($type == 0):?>class="on"<?php endif;?> type="0"><?php echo lang('template.user');?></a>
		<?php endif;?>
    </div>
    <span></span>
</h1>
<div id="templateContent">
	
</div>
<script type="text/javascript">
	template.index.init();
</script>