<div class="schedule">
	<div class="title">
		<span class="fc-button fc-button-all fc-state-default fc-state-active">
			<span class="fc-button-inner">
				<span class="fc-button-content"><?php echo lang('all');?></span>
				<span class="fc-button-effect"><span></span></span>
			</span>
		</span>
		<span class="fc-button fc-button-day fc-state-default">
			<span class="fc-button-inner">
				<span class="fc-button-content"><?php echo lang('day');?></span>
				<span class="fc-button-effect"><span></span></span>
			</span>
		</span>
		<span class="fc-button fc-button-week fc-state-default">
			<span class="fc-button-inner">
				<span class="fc-button-content"><?php echo lang('week');?></span>
				<span class="fc-button-effect"><span></span></span>
			</span>
		</span>
		<span class="fc-button fc-button-month fc-state-default">
			<span class="fc-button-inner">
				<span class="fc-button-content"><?php echo lang('month');?></span>
				<span class="fc-button-effect"><span></span></span>
			</span>
		</span>
	</div>
	<div id="list">
		<?php if($auth > 0):?>
		<div class="add-panel">
			<a href="/schedule/add?width=400&height=300" id="create" class="thickbox" title="<?php echo lang('schedule.new');?>"><?php echo lang('create');?></a>
		</div>
		<?php endif;?>
		<div class="clear"></div>
		<h1 class="tit-01"><?php echo lang('scheduler');?><span></span>
		<div class="filter" style="width:84%;">
		<?php echo lang('filter.by');?>:
		<select class="input-large" id="filter_type" name="filter_type">
			<option value="group"><?php echo lang('filter.group');?></option>
			<option value="playlist"><?php echo lang('filter.playlist');?></option>
		</select>
		<input type="text" name="filter_name" id="filter_name" style="width:120px; margin-left:4px;">
		<a href="javascript:void(0);" style="margin-left:20px; " class="btn-go" onclick="schedule.index.filter();"><label><?php echo lang('filter');?></label></a>
		</div>
		</h1>
		<div id="layoutContent">
				<?php
				if(isset($body_view)){
					$this->load->view($body_view);
				}
				?>
		</div>
	</div>
	<div id="calendar" style="display:none;">
		
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		var type = $('#filter_type').val();
		var name = $('#filter_name').val();
		schedule.index.init(type, name);
		schedule.index.initCalendar(type, name);
	});
	$('.btn-go').click(function() {
		var type = $('#filter_type').val();
		var name = $('#filter_name').val();
		$('#calendar').html('');
		setTimeout(function() {
			schedule.index.init(type, name);
			schedule.index.initCalendar(type, name);	
		},2000);
	});
</script>