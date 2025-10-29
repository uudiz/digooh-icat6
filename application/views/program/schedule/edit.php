<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top">
    	   <div>
		   <!--表单区start-->
		   <table width="98%" border="0" cellspacing="0" cellpadding="0" class="from-panel-2">
		      <tr>
		        <td><h1 class="h1-style"><?php echo lang('schedule.name');?>:</h1></td>
		      </tr>
		      <tr>
		        <td><input name="name" id="name" type="text" style="width: 300px;" value="<?php echo $schedule->name;?>"/></td>
		      </tr>
		      <tr>
		        <td><h1 class="h1-style"><?php echo lang('desc');?>:</h1></td>
		     </tr>
		     <tr>
		        <td>
		        	<textarea name="descr" id="descr" rows="2"  style=" width: 300px;"><?php echo $schedule->descr;?></textarea>
					<input type="hidden" name="id" id="id" value="<?php echo $schedule->id;?>">
				</td>
		     </tr>
			 <tr>
		        <td>
		        	<input type="checkbox" id="action" value="1" name="action" <?php if($schedule->action == 1):?>checked="checked"<?php endif;?>><?php echo lang('schedule.playlist.action');?>
				</td>
		     </tr>
		      <tr>
		        <td>
		        	<h1 class="h1-style" style="position:relative;"><?php echo lang('group');?>:
					<div class="add-panel" style="position:absolute;right:0px;top:0px;">
						<a id="44" status="0" onclick="schedule.form.toggleGroup(this)" href="javascript:void(0);">
							<img height="16" width="16" tc="Collapse this item" te="Expland this item" title="Expland this item" src="/images/icons/16-06.gif">
						</a>
						<a title="<?php echo lang('filter.group');?>" id="create" class="thickbox" href="/schedule/add_group?sch_type=<?php echo $schedule->sch_type;?>&amp;width=600&amp;height=540"><?php echo lang('btn.add.group');?></a>
					</div>
					
					<div class="clear"></div>
					</h1>
				</td>
		     </tr>
		      <tr>
		        <td id="innerGroup">
			       <?php 
				   $this->load->view($group_inner);
				   ?>
				</td>
		     </tr>
		     <tr>
		        <td>
		        	<h1 class="h1-style" style="position:relative;"><?php echo lang('playlist');?>:
					<div class="add-panel" style="position:absolute;right:0px;top:0px;">
						<a id="44" status="0" onclick="schedule.form.togglePlaylist(this)" href="javascript:void(0);">
							<img height="16" width="16" tc="Collapse this item" te="Expland this item" title="Collapse this item" src="/images/icons/16-06.gif">
						</a>
						<a id="create" class="thickbox" href="/schedule/add_playlist?sch_type=<?php echo $schedule->sch_type;?>&amp;width=800&amp;height=500" title="<?php echo lang('filter.playlist');?>" ><?php echo lang('btn.add.playlist');?></a>
					</div>
					<div class="clear"></div>
					</h1>
				</td>
		     </tr>
		     <tr>
		      <td id="innerPlaylist">
		      	<?php 
				   $this->load->view($playlist_inner);
				 ?>
		      </td>	
		     </tr>
		     <?php if($schedule->sch_type == 1 && $touch == 'on'):?>
		     <tr>
		        <td>
		        	<h1 class="h1-style" style="position:relative;"><?php echo lang('interaction.playlist');?>:
					<div class="add-panel" style="position:absolute;right:0px;top:0px;">
						<a id="44" status="0" onclick="schedule.form.toggleInteraction(this)" href="javascript:void(0);">
							<img height="16" width="16" tc="Collapse this item" te="Expland this item" title="Collapse this item" src="/images/icons/16-06.gif">
						</a>
						<a id="create" class="thickbox" href="/schedule/add_interaction?sch_type=<?php echo $schedule->sch_type;?>&amp;width=800&amp;height=500" title="<?php echo lang('filter.interaction');?>" ><?php echo lang('btn.add.interaction');?></a>
					</div>
					<div class="clear"></div>
					</h1>
				</td>
		     </tr>
		     <tr>
		      <td id="innerInteraction">
		      	<?php 
				   $this->load->view($interaction_playlist_inner);
				 ?>
		      </td>	
		     </tr>
		     <?php endif;?>
			 <tr>
			 	<td>
			 		<h1 class="h1-style" style="position:relative;"><?php echo lang('schedule');?>:
					</h1>
			 	</td>
			 </tr>
			 <tr>
			 	<td>
			 		<table width="100%">
						<tr>
							<td width="40px;"><?php echo lang('valid.date');?></td>
							<td >
								<input type="text" style="width:90px;" id="startDate" name="startDate" readonly="readonly" class="date-input"  value="<?php echo $schedule->start_date;?>" >
								<em><?php echo lang('to');?></em>
								<input type="text" style="width:90px;" id="endDate" name="endDate" readonly="readonly" class="date-input"  value="<?php echo $schedule->end_date;?>" >
							</td>
							<td width="65px;"><input type="checkbox" id="alldayFlag" name="alldayFlag" <?php if($schedule->allDayFlag){echo 'checked="checked" value="1"';}else {echo 'value="0"';}?> /><?php echo lang('all');?>&nbsp;<?php echo lang('day');?></td>
							<td width="20px;">
								<?php echo lang('enable.time');?>		
							</td>
							<td >
								<input type="text" id="startTime" name="startTime"  class="time-input" value="<?php echo $schedule->start_time;?>" <?php if($schedule->allDayFlag){echo 'readonly="readonly" style="width:80px;background:#ddd;"';}else {echo 'style="width:80px;"';}?> />
								<em><?php echo lang('to');?></em>
								<input type="text" id="endTime" name="endTime"  class="time-input" value="<?php echo $schedule->end_time;?>" <?php if($schedule->allDayFlag){echo 'readonly="readonly" style="width:80px;background:#ddd;"';}else {echo 'style="width:80px;"';}?> />
							</td>
							<td width="40px;"><?php echo lang('enable.week');?></td>
							<td >
								<input type="checkbox" id="mon" name="mon" class="week" <?php if(is_week($schedule->week, 1)):?>checked="checked"<?php endif;?> /><?php echo lang('mon');?>
								<input type="checkbox" id="tue" name="tue" class="week" <?php if(is_week($schedule->week, 2)):?>checked="checked"<?php endif;?>/><?php echo lang('tue');?>
								<input type="checkbox" id="wed" name="wed" class="week" <?php if(is_week($schedule->week, 3)):?>checked="checked"<?php endif;?>/><?php echo lang('wed');?>
								<input type="checkbox" id="thu" name="thu" class="week" <?php if(is_week($schedule->week, 4)):?>checked="checked"<?php endif;?>/><?php echo lang('thu');?>
								<input type="checkbox" id="fri" name="fri" class="week" <?php if(is_week($schedule->week, 5)):?>checked="checked"<?php endif;?>/><?php echo lang('fri');?>
								<input type="checkbox" id="sat" name="sat" class="week" <?php if(is_week($schedule->week, 6)):?>checked="checked"<?php endif;?>/><?php echo lang('sat');?>
								<input type="checkbox" id="sun" name="sun" class="week" <?php if(is_week($schedule->week, 0)):?>checked="checked"<?php endif;?>/><?php echo lang('sun');?>
							</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td colspan="2"><?php echo lang('schedule.time.format'); ?></td>
							<td></td>
							<td></td>
						</tr>
					<table>
			 	</td>
			 </tr>
		    </table>
		<!--表单区end-->
		</div>
    </td>
  </tr>
  <tr>
  	<td>
		<input type="hidden" name="filter_type" id="filter_type" value="<?php echo $type;?>"/>
		<input type="hidden" name="filter_name" id="filter_name" value="<?php echo $name;?>"/>
		<input type="hidden" name="sch_type" id="sch_type" value="<?php echo $schedule->sch_type;?>"/>
  		<p class="btn-center">
  			<a href="javascript:void(0);" onclick="schedule.form.publish();return false;" class="btn-01"><span style="color:red;"><?php echo lang('button.publish');?></span></a>
			<a href="javascript:void(0);" onclick="schedule.form.save();return false;" class="btn-01"><span><?php echo lang('button.save');?></span></a>
			<!--
			<a href="/schedule/filter/1/id/desc?type=<?php echo $type;?>&value=<?php echo $name;?>" class="btn-01"><span><?php echo lang('button.return');?></span></a>
			-->
			<a href="javascript:void(0);" onclick="schedule.form.cancel();return false;" class="btn-01"><span><?php echo lang('button.return');?></span></a>
		</p>
  	</td>
  </tr>
</table>

<script type="text/javascript">
	schedule.form.init();
	$(function() {
	    	$('#alldayFlag').click(function() {
	    		$('input:checkbox[id="alldayFlag"]').each(function(){
		            if (this.checked) {
		                $('.time-input').attr('readonly','readonly');
		            	$('.time-input').css('background', '#ddd');
		            	$('#alldayFlag').val(1);
		            }else {
		            	$('.time-input').removeAttr('readonly');
		                $('.time-input').css('background', '#fff');
		                $('#alldayFlag').val(0);
		            }
		        });	
	    	});
	    	
	    });
</script>