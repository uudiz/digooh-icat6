<h1 class="tit-01">
	<?php echo lang('device.setup');?>
	<span></span>
</h1>
<table class="table-list"  width="100%" >
	<tr>
		<th>
			<span class="ico-player"></span>
			<?php echo lang('filename'); ?>
		</th>
		<th>
			<?php echo lang('player.type');?>
		</th>
		<th>
			<span class="ico-description"></span>
			<?php echo lang('desc'); ?>
		</th>
		<th>
			<span class="ico-last-update"></span>
			<?php echo lang('update.time'); ?>
		</th>
		<th>
			<span class="ico-function"></span>
			<?php echo lang('operate'); ?>
		</th>
	</tr>
	<?php 
		if($total == 0): 
	?>
	<tr>
		<td colspan="5">
			<?php echo lang("empty");?>
		</td>
	</tr>
	<?php 
		else:
			$index = 0;
	?>
	<?php foreach($data as $row):?>
	<tr <?php if($index%2 != 0): ?>class="bg"<?php endif;?>>
		<td>
			<?php if($auth >= 5): ?>
				<a href="/configxml/edit?id=<?php echo $row->id;?>&control=1&width=500&height=720"  class="thickbox" title="<?php echo lang('device.config');?>"><?php echo $row->name; ?></a>
			<?php else: ?>
				<?php echo $row->name; ?>
			<?php endif;?>
		</td>
		<td>
			<?php 
			if($row->player_type):
			?>
			<img src="/images/icons/android.png"  title="<?php echo lang('type.1');?>" />
			<?php else:?>
			<img src="/images/icons/windows.png"  title="<?php echo lang('type.0');?>" />
			<?php endif;?>
		</td>
		<td>
			<?php echo $row->descr; ?>
		</td>
		<td>
			<?php echo $row->add_time; ?>
		</td>
		<td>
			<a href="javascript:void(0);" onclick="configxml.toggle(this)" status="0" id="<?php echo $row->id;?>" ptype="<?php echo $row->player_type;?>">
				<img width="16" height="16" tc="Collapse this item" te="Expland this item" title="Expland this item" src="/images/icons/16-05.gif">
			</a>
		</td>
	</tr>		
	<?php
		$index++; 
		endforeach; 
	endif;
	?>
</table>

