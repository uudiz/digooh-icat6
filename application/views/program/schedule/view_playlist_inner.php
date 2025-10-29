<table class="table-list"  width="100%" >
    <tr>
        <th width="120" ><?php echo lang('name');?></th>
		<th><?php echo lang('desc');?></th>
		<th width="120"><?php echo lang('create.user');?></th>
		<?php if(!isset($view)):?>
		<th width="80"><?php echo lang('order');?></th>
		<?php endif;?>
        <th width="80"><?php echo lang('update.time');?></th>
		<?php if(!isset($view)):?>
		<th width="80"><?php echo lang('operate');?></th>
		<?php endif;?>
    </tr>
	<?php if(!empty($playlists)):
	  $index = 0;
	  $total=count($playlists);
	?>
		<?php foreach($playlists as $row):?>
		<tr <?php if($index%2 != 0):?>class="even" onmouseout="this.className='even'" <?php else:?>onmouseout="this.className=''"<?php endif;?>  onmouseover="this.className='onSelected'">
		  <td>
		  	<?php echo $row->name;?>&nbsp;
		  </td>
		  <td><?php echo $row->descr;?></td>
		  <td><?php echo $row->user; ?></td>
		  <?php if(!isset($view)):?>
		  <td>
		  	<?php if($total > 1):?>
			  	<?php if($index == 0 && $total > 1):?>
					<img src="/images/icons/dir-blank.gif" alt=""/>&nbsp;
					<img src="/images/icons/arrow_down.png" cid="<?php echo $row->id;?>" nid="<?php echo $playlists[$index + 1]->id;?>" pos="<?php echo $row->position;?>" class="move down" alt="" title="<?php echo lang('move.down');?>">
				<?php elseif($index == $total - 1):?>
					<img src="/images/icons/arrow_up.png" cid="<?php echo $row->id;?>" pid="<?php echo $playlists[$index - 1]->id;?>" pos="<?php echo $row->position;?>" class="move up" alt="" title="<?php echo lang('move.up');?>">&nbsp;
					<img src="/images/icons/dir-blank.gif" alt=""/>
				<?php else:?>
					<img src="/images/icons/arrow_up.png" cid="<?php echo $row->id;?>" pid="<?php echo $playlists[$index - 1]->id;?>" pos="<?php echo $row->position;?>" class="move up" alt="" title="<?php echo lang('move.up');?>">&nbsp;
					<img src="/images/icons/arrow_down.png" cid="<?php echo $row->id;?>" nid="<?php echo $playlists[$index + 1]->id;?>" pos="<?php echo $row->position;?>" class="move down" alt="" title="<?php echo lang('move.down');?>">
				<?php endif;?>
			<?php endif;?>
		  </td>
		  <?php endif;?>
		  <td><?php echo $row->add_time; ?></td>
		  <?php if(!isset($view)):?>
		  <td>
		  	<?php if($auth > 0):?>
		  		<a href="javascript:void(0);" onclick="schedule.form.removePlaylist(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>');"><img src="/images/icons/16-04.gif" width="16" height="16" title="<?php echo lang('delete');?>" /></a>
				<?php if(false):?>
				<a status="0" onclick="schedule.form.togglePlaylistSchedule(this,<?php echo $row->id;?>)" href="javascript:void(0);">
					<img id="img_<?php echo $row->id;?>" height="16" width="16" tc="Collapse this item" te="Expland this item" title="<?php echo lang('toggle.setting.date');?>" src="/images/icons/16-05.gif">
				</a>
				<?php endif;?>
			<?php endif;?>
		  </td>
		  <?php endif;?>
		</tr>
		<?php if(false):?>
		<tr id="pl_<?php echo $row->id;?>" style="display:none;">
			<td colspan="5" class="control-panel">
				<div class="clear"></div>
				<h1 class="tit-01"><?php echo lang('schedule.date.type');?>
					<span></span>
				</h1>
				<div class="tab-01-in">
					<table width="100%">
						<tr>
							<td width="60px;"><?php echo lang('valid.date');?></td>
							<td >
								<input type="text" style="width:90px;" id="startDate_<?php echo $row->id;?>" name="startDate" readonly="readonly" class="date-input"  value="<?php echo $row->start_date;?>" >
								<em><?php echo lang('to');?></em>
								<input type="text" style="width:90px;" id="endDate_<?php echo $row->id;?>" name="endDate" readonly="readonly" class="date-input"  value="<?php echo $row->end_date;?>" >
							</td>
							<td width="60px;">
								<?php echo lang('enable.time');?>
							</td>
							<?php if(false): ?>
							<td width="50px;">
								<input type="checkbox" id="enableTime_<?php echo $row->id;?>" <?php if($row->time_flag):?>checked="checked"<?php endif;?>/>
							</td>
							<?php endif;?>
							<td >
								<input type="text" id="startTime_<?php echo $row->id;?>" name="startTime"  class="time-input" value="<?php echo $row->start_time;?>" style="width:80px;" />
								<em><?php echo lang('to');?></em>
								<input type="text" id="endTime_<?php echo $row->id;?>" name="endTime"  class="time-input" value="<?php echo $row->end_time;?>" style="width:80px;" />
							</td>
							<td width="60px;"><?php echo lang('enable.week');?></td>
							<?php if(false): ?>
							<td width="50px;"><input type="checkbox" id="enableWeek_<?php echo $row->id;?>" <?php if($row->week_flag):?>checked="checked"<?php endif;?>/></td>
							<?php endif;?>
							<td >
								<input type="checkbox" id="mon_<?php echo $row->id;?>" name="mon" class="week" <?php if($row->mon == 1):?>checked="checked"<?php endif;?> /><?php echo lang('mon');?>
								<input type="checkbox" id="tue_<?php echo $row->id;?>" name="tue" class="week" <?php if($row->tue == 1):?>checked="checked"<?php endif;?>/><?php echo lang('tue');?>
								<input type="checkbox" id="wed_<?php echo $row->id;?>" name="wed" class="week" <?php if($row->wed == 1):?>checked="checked"<?php endif;?>/><?php echo lang('wed');?>
								<input type="checkbox" id="thu_<?php echo $row->id;?>" name="thu" class="week" <?php if($row->thu == 1):?>checked="checked"<?php endif;?>/><?php echo lang('thu');?>
								<input type="checkbox" id="fri_<?php echo $row->id;?>" name="fri" class="week" <?php if($row->fri == 1):?>checked="checked"<?php endif;?>/><?php echo lang('fri');?>
								<input type="checkbox" id="sat_<?php echo $row->id;?>" name="sat" class="week" <?php if($row->sat == 1):?>checked="checked"<?php endif;?>/><?php echo lang('sat');?>
								<input type="checkbox" id="sun_<?php echo $row->id;?>" name="sun" class="week" <?php if($row->sun == 1):?>checked="checked"<?php endif;?>/><?php echo lang('sun');?>
							</td>
						</tr>
					</table>
				</div>
				<p align="right">
					<input type="button" class="button2" value="<?php echo lang('button.update');?>" onclick="schedule.form.savePlaylistFlag(<?php echo $row->id;?>,'<?php echo lang('warn.date.flag.empty');?>', '<?php echo lang('warn.time.flag.empty');?>','<?php echo lang('warn.week.flag.empty');?>');"/>
				</p>
				<script>
					//init flag
					schedule.form.initPlaylistFlag(<?php echo $row->id;?>);
				</script>
			</td>
		</tr>
		<?php endif;?>
		<?php
		 	$index++; 
			endforeach; 
		?>
	<?php endif;?>
</table>
<script type="text/javascript">
		$(function() {
			$(".sch_pl").click(function() {
				parent.$('#sch').removeClass("on");
				parent.$('#play').addClass("on");
			});
		});
	</script>