<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<fieldset>
	<legend><?php echo lang('page.size.settings');?></legend>
	<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
	<tbody>
		<?php if($auth > $GROUP):?>
		<tr>
			<td><?php echo lang('user.page.size');?>:</td>
            <td>
            	<input id="userPageSize" name="userPageSize" class="text" style="width: 100px;" value="<?php echo $settings->user_page_size;?>"/>
			</td>
		</tr>
		<?php endif;?>
		<tr>
			<td><?php echo lang('group.page.size');?>:</td>
            <td>
            	<input id="groupPageSize" name="groupPageSize" class="text" style="width: 100px;" value="<?php echo $settings->group_page_size;?>"/>
			</td>
		</tr>
		<tr>
			<td><?php echo lang('player.page.size');?>:</td>
            <td>
            	<input id="playerPageSize" name="playerPageSize" class="text" style="width: 100px;" value="<?php echo $settings->player_page_size;?>"/>
			</td>
		</tr>
        <tr>
			<td><?php echo lang('media.page.size');?>:</td>
            <td>
            	<input id="mediaPageSize" name="mediaPageSize" class="text" style="width: 100px;" value="<?php echo $settings->media_page_size;?>"/>
			</td>
		</tr>
        <tr>
			<td><?php echo lang('dialog.media.page.size');?>:</td>
            <td>
            	<input id="dialogMediaPageSize" name="dialogMediaPageSize" class="text" style="width: 100px;" value="<?php echo $settings->dialog_media_page_size;?>"/>
			</td>
		</tr>
        <tr>
			<td><?php echo lang('template.page.size');?>:</td>
            <td>
            	<input id="templatePageSize" name="templatePageSize" class="text" style="width: 100px;" value="<?php echo $settings->template_page_size;?>"/>
			</td>
		</tr>
        <tr>
			<td><?php echo lang('playlist.page.size');?>:</td>
            <td>
            	<input id="playlistPageSize" name="playlistPageSize" class="text" style="width: 100px;" value="<?php echo $settings->playlist_page_size;?>"/>
			</td>
		</tr>
		<tr>
			<td><?php echo lang('schedule.page.size');?>:</td>
            <td>
            	<input id="schedulePageSize" name="schedulePageSize" class="text" style="width: 100px;" value="<?php echo $settings->schedule_page_size;?>"/>
			</td>
		</tr>
	</tbody>
</table>
</fieldset>

<p class="btn-center">
 	<a class="btn-01" href="javascript:void(0);" onclick="settings.save(this);"><span><?php echo lang('button.save');?></span></a>
</p>