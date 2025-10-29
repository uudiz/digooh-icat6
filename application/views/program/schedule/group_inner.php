<table class="table-list"  width="100%" >
    <tr>
        <th width="100" ><?php echo lang('group');?></th>
        <th ><?php echo lang('desc');?></th>
        <th width="80"><?php echo lang('update.time');?></th>
		<?php if(!isset($view)):?>
		<th width="80"><?php echo lang('operate');?></th>
		<?php endif;?>
    </tr>
	<?php
	  if(!empty($groups)): 
	  $index = 0;
	?>
	<?php foreach($groups as $row):?>
	<tr <?php if($index%2 != 0):?>class="even"<?php endif;?>>
	  <td><?php echo $row->name;?></td>
	  <td><?php echo $row->descr; ?></td>
	  <td><?php echo $row->add_time; ?></td>
	  <?php if(!isset($view)):?>
	  <td>
	  	<?php if($auth > 0):?>
	  		<a href="javascript:void(0);" onclick="schedule.form.removeGroup(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>');"><img src="/images/icons/16-04.gif" width="16" height="16" title="<?php echo lang('delete');?>" /></a>
		<?php else:?>
			&nbsp;
		<?php endif;?>
	  </td>
	  <?php endif;?>
	</tr>
	<?php
	 	$index++; 
		endforeach; 
	?>
	<?php endif;?>
</table>