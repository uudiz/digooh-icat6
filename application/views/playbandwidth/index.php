<h1 class="tit-01"><?php echo lang('bandwidth');?>
	<div class="filter" style="width:84%;">
		<?php echo lang('post.date');?>:
		<input type="text" readonly="readonly" id="postStartDate" class="date-input" style="width:90px;" value="<?php echo $start_date;?>"/> -
		<input type="text" readonly="readonly" id="postEndDate" class="date-input" style="width:90px;" value="<?php echo $end_date;?>"/>
		<?php echo lang('player');?>
		<select id="playerId" name="playerId" style="width:120px;">
		<option value="0"><?php echo lang('all');?></option>
		<?php if(isset($players)):?>
		<?php foreach($players as $p):?>
		<option value="<?php echo $p->id;?>"><?php if(strlen($p->name) > 12){echo substr($p->name, 0, 12);}else{echo $p->name;}?></option>
		<?php endforeach;?>
		<?php endif;?>
		</select>
		<a href="javascript:void(0);" class="btn-go" style="margin-left:20px;" onclick="bandwidth.query();"><label><?php echo lang('button.query');?></label></a>
	</div>
    <span></span>
</h1>
<div class="clear"></div>
<div id="bandwidthContent" width="100%">

</div>
<script>
	bandwidth.initCalendar();
</script>