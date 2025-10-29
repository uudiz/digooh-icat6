<h1 class="tit-01"><?php echo lang('template');?>
    <div class="tab-01">
    	<?php
			if($this->config->item('mia_system_set') == $this->config->item('mia_system_np100') || $this->config->item('mia_system_set') == $this->config->item('mia_system_all')):
		?>
		<a href="javascript:void(0);" type="0">NP100</a>
		<?php 
			endif;
			if($this->config->item('mia_system_set') == $this->config->item('mia_system_np200') || $this->config->item('mia_system_set') == $this->config->item('mia_system_all')):
		?>
		<a href="javascript:void(0);" class="on" type="1">NP200</a>
		<?php
			endif;
		?>
    </div>
    <span></span>
</h1>
<div id="templateContent">
	
</div>
<script type="text/javascript">
	campaign.initTemplate();
</script>