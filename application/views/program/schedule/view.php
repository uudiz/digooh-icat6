<div class="align-left" style="width: 100%;">
    <!---表单区start--->
    <table border="0" cellspacing="0" cellpadding="0" class="from-panel-2" style="width: 100%;">
        <tr>
            <td>
                <h1 class="h1-style"><?php echo lang('schedule.name'); ?>:</h1>
            </td>
        </tr>
        <tr>
            <td>
               <?php echo $schedule->name;?>
            </td>
        </tr>
        <tr>
            <td>
                <h1 class="h1-style"><?php echo lang('desc'); ?>:</h1>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $schedule->descr; ?>
            </td>
        </tr>
        <tr>
            <td>
                <h1 class="h1-style"><?php echo lang('group'); ?>:</h1>
            </td>
        </tr>
        <tr>
            <td>
            	<?php $this->load->view($group_inner);?>
            </td>
        </tr>
        <tr>
            <td>
                <h1 class="h1-style"><?php echo lang('playlist'); ?>:</h1>
            </td>
        </tr>
        <tr>
            <td>
            	<?php $this->load->view($playlist_inner);?>
            </td>
        </tr>
		<?php if($schedule->sch_type == 1 && $touch == 'on'):?>
        <tr>
            <td>
                <h1 class="h1-style"><?php echo lang('interaction.playlist'); ?>:</h1>
            </td>
        </tr>
        <tr>
            <td>
            	<?php $this->load->view($interactionpls_inner);?>
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
							<td><?php echo lang('valid.date');?></td>
							<td >
								<label style="font-size:12px;">
									<?php echo $schedule->start_date;?>
									<em><?php echo lang('to');?></em>
									<?php echo $schedule->end_date;?>
								</label>
							</td>
							<td >
								<?php echo lang('enable.time');?>		
							</td>
							<td >
								<label style="font-size:12px;">
									<?php echo $schedule->start_time;?>
									<em><?php echo lang('to');?></em>
									<?php echo $schedule->end_time;?>
								</label>
							</td>
							<td ><?php echo lang('enable.week');?></td>
							<td >
								<label style="font-size:12px;">
									<?php if(is_week($schedule->week, 1)){ echo lang('mon');}?>
									<?php if(is_week($schedule->week, 2)){echo lang('tue');}?>
									<?php if(is_week($schedule->week, 3)){echo lang('wed');}?>
									<?php if(is_week($schedule->week, 4)){echo lang('thu');}?>
									<?php if(is_week($schedule->week, 5)){echo lang('fri');}?>
									<?php if(is_week($schedule->week, 6)){echo lang('sat');}?>
									<?php if(is_week($schedule->week, 0)){echo lang('sun');}?>
								</label>
							</td>
						</tr>
					<table>
			 	</td>
			 </tr>
    </table>
    <!---表单区end--->
</div>
