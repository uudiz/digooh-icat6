<h1 class="tit-01"><?php echo lang('player.usage');?>
	<div class="filter" style="width:84%;">
		<?php echo lang('filter.by');?>:
		<input type="text" name="filter" id="filter" style="width:120px; margin-left:4px;" >

		<?php echo lang('criteria');?>:
		<span class="criterion-select" >
		<select id="criterionId" style="width:200px;">
		<option value="0"><?php echo lang('all');?></option>
		<?php if (isset($criteria)&&is_array($criteria)):?>
		<?php foreach ($criteria as $p):?>
		<option value="<?php echo $p->id;?>"><?php echo $p->name;?></option>
		<?php endforeach;?>
		<?php endif;?>
		</select>
		</span>


		<label for="checkDate" style="margin-left:10px;vertical-align:middle;"><?php echo lang('date.range');?></label>
		<input type="checkbox" style="vertical-align:middle;" id="checkDate" value="0" onclick="usage.checkboxOnclick(this);"/>
		<input type="text" readonly="readonly" id="startDate" class="date-input" disabled style="width:90px;" value="<?php echo $start_date;?>" /> -
		<input type="text" readonly="readonly" id="endDate" class="date-input" disabled style="width:90px;" value="<?php echo $end_date;?>"/>	
		
		<a href="javascript:void(0);" class="btn-go" style="margin-left:20px;" onclick="usage.query();"><label><?php echo lang('button.query');?></label></a>

	</div>
    <span></span>
</h1>
<div class="clear"></div>
<div id="usageContent" width="100%">

</div>
<script>
	usage.initCalendar();
	
	$('#criterionId').select2();


		
</script>