<table class="table-list"  width="100%" >
    <tr>
        <th width="120" ><?php echo lang('name');?></th>
		<th><?php echo lang('desc');?></th>
		<th width="220"><?php echo lang('create.user');?></th>
        <th width="80"><?php echo lang('update.time');?></th>
		<?php if(!isset($view)):?>
		<th width="80"><?php echo lang('operate');?></th>
		<?php endif;?>
    </tr>
	
	<?php if(!empty($interactions)):
	  $index = 0;
	  $total=count($interactions);
	?>
		<?php foreach($interactions as $row):?>
		<tr <?php if($index%2 != 0):?>class="even" onmouseout="this.className='even'" <?php else:?>onmouseout="this.className=''"<?php endif;?>  onmouseover="this.className='onSelected'">
		  <td>
		  	<?php echo $row->name;?>&nbsp;
		  </td>
		  <td><?php echo $row->descr;?></td>
		  <td><?php echo $row->user; ?></td>
		  <td><?php echo $row->add_time; ?></td>
		  <?php if(!isset($view)):?>
		  <td>
		  	<?php if($auth > 0):?>
		  		<a href="javascript:void(0);" onclick="schedule.form.removeInteraction(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>');"><img src="/images/icons/16-04.gif" width="16" height="16" title="<?php echo lang('delete');?>" /></a>
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
<script type="text/javascript">
		$(function() {
			$(".sch_touch").click(function() {
				parent.$('#sch').removeClass("on");
				parent.$('#touch').addClass("on");
			});
		});
	</script>