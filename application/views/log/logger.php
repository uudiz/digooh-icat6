<div class="clear"></div>
<h1 class="tit-01" style="position:relative;"><?php echo lang('log');?>
	<div class="filter" style="width:84%;">
		<?php echo lang('filter.by');?>:
		<input type="text" name="start_date" id="filterStartDate"  class="date-input" style="width:90px; margin-left:4px;">
		<em>-</em>
		<input type="text" name="end_date" id="filterEndDate"  class="date-input" style="width:90px; margin-left:4px;">
		<label style="margin-left:20px;"><?php echo lang('company');?>:</label>
		<select id="filterCompany" name="filterCompany" style="width: 150px; margin:0px 4px;">
			<option value="0" selected="selected"><?php echo lang('all');?></option>
			<?php if(isset($companys)):?>
				<?php foreach($companys as $c):?>
					<option value="<?php echo $c->id;?>"><?php echo $c->name;?></option>
				<?php endforeach;?>
			<?php endif;?>
		</select>
		<a href="javascript:void(0);" style="margin-left:20px; " class="btn-go" onclick="log.filter();"><label><?php echo lang('filter');?></label></a>
	</div>
    <span></span>
</h1>
<div class="clear"></div>
<div id="logContent" width="100%">
<?php
$this->load->view("log/table_list");
?>
</div>
<input type="hidden" id="startDate" />
<input type="hidden" id="endDate" />
<input type="hidden" id="cid" />
<script>
	log.init();
</script>