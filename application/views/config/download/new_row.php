<tr>
	<td>
		<select name="startHour">
			<?php for($i = 0; $i < 24; $i++):?>
			<?php if($i < 10){$hour = '0'.$i;}else{$hour = $i;}?>
			<option value="<?php echo $hour;?>"><?php echo $hour;?></option>
			<?php endfor;?>
		</select>
		:
		<select name="startMinute">
			<?php for($i = 0; $i < 60; $i++):?>
			<?php if($i < 10){$minute = '0'.$i;}else{$minute = $i;}?>
			<option value="<?php echo $minute;?>"><?php echo $minute;?></option>
			<?php endfor;?>
		</select>
	</td>
	<td></td>
	<td>
		<select name="endHour">
			<?php for($i = 0; $i < 24; $i++):?>
			<?php if($i < 10){$hour = '0'.$i;}else{$hour = $i;}?>
			<option value="<?php echo $hour;?>"><?php echo $hour;?></option>
			<?php endfor;?>
		</select>
		:
		<select name="endMinute">
			<?php for($i = 0; $i < 60; $i++):?>
			<?php if($i < 10){$minute = '0'.$i;}else{$minute = $i;}?>
			<option value="<?php echo $minute;?>"><?php echo $minute;?></option>
			<?php endfor;?>
		</select>
	</td>
	<td></td>
	<td>
		<a href="javascript:void(0);" onclick="cfg.removeDownloadRow(this);">
			<img src="/images/icons/btn_remove.png" alt="" >
		</a>
	</td>
	<td>
		&nbsp;
	</td>
</tr>
