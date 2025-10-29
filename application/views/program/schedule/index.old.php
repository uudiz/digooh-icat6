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
		<a id="create" href="/schedule/add"><?php echo lang('create');?></a>
		<table>
			<thead>
				<tr>
					<th>
						<?php echo lang('schedules');?>
					</th>
					<th>
						<?php echo lang('desc');?>
					</th>
					<th>
						<?php echo lang('schedule');?>
					</th>
					<th>
						<?php echo lang('group');?>
					</th>
					<th>
						<?php echo lang('playlist');?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($data as $row):?>
				<tr>
					<td>
						<?php echo $row->name;?>
					</td>
					<td>
						<?php echo $row->descr;?>
					</td>
					<td>
						<div>
							<?php if($row->time_flag):?>
							<?php echo lang('24h');?>
							<?php else:?>
							<?php echo $row->start_time.'-'.$row->stop_time;?>
							<?php endif;?>
						</div>
						<div>
							<?php if($row->day_flag):?>
							<?php echo lang('all.day');?>
							<?php else:?>
							<?php if($row->sun):?>
							<?php echo lang('sun');?><em>&nbsp;</em>
							<?php endif;?>
							<?php if($row->mon):?>
							<?php echo lang('mon');?><em>&nbsp;</em>
							<?php endif;?>
							<?php if($row->tue):?>
							<?php echo lang('tue');?><em>&nbsp;</em>
							<?php endif;?>
							<?php if($row->wed):?>
							<?php echo lang('wed');?><em>&nbsp;</em>
							<?php endif;?>
							<?php if($row->thu):?>
							<?php echo lang('thu');?><em>&nbsp;</em>
							<?php endif;?>
							<?php if($row->fri):?>
							<?php echo lang('fri');?><em>&nbsp;</em>
							<?php endif;?>
							<?php if($row->sat):?>
							<?php echo lang('sat');?><em>&nbsp;</em>
							<?php endif;?>
							<?php endif;?>
						</div>
					</td>
					<td>
						<?php if($row->groups):?>
							<?php foreach($row->groups as $g):?>
							<label>
								<?php echo $g->name;?>
							</label>
							<?php endforeach;?>
						<?php endif;?>
					</td>
					<td>
						<?php if($row->playlists):?>
							<?php foreach($row->playlists as $p):?>
							<label>
								<?php echo $p->name;?>
							</label>
							<?php endforeach;?>
						<?php endif;?>
					</td>
				</tr>
				<?php endforeach;?>
			</tbody>
		</table>
	</div>
	<div id="calendar" style="display:none;">
		
	</div>
</div>


<script type="text/javascript">
	$(document).ready(function(){
		schedule.index.init();
	});
</script>