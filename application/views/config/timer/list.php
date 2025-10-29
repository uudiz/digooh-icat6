<table class="table-list"  width="100%" >
        <tr>
            <th width="200" >
            	<a href="javascript:void(0);" onclick="cfg.timerpage(<?php echo $curpage;?>,'name','<?php if($order_item == 'name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('name');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'name' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
            </th>
			<th width="120" >
				<a href="javascript:void(0);" onclick="cfg.timerpage(<?php echo $curpage;?>,'type','<?php if($order_item == 'type' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('timer.type');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'type' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'type' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
				
			</th>
            <th>
            	<a href="javascript:void(0);" onclick="cfg.timerpage(<?php echo $curpage;?>,'descr','<?php if($order_item == 'descr' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('desc');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'descr' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'descr' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
            </th>
			<th width="100"><?php echo lang('operator');?></th>
            <th width="120">
            	<a href="javascript:void(0);" onclick="cfg.timerpage(<?php echo $curpage;?>,'add_time','<?php if($order_item == 'add_time' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('create.time');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'add_time' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'add_time' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
            
			<th width="80"><?php echo lang('operate');?></th>
        </tr>
		
		<?php if($total == 0): ?>
		<tr>
			<td colspan="6">
				<?php echo lang("empty");?>
			</td>
		</tr>
		<?php else:
		  $index = 0;
		?>
			<?php foreach($data as $row):?>
			<tr <?php if($index%2 != 0):?>class="even" onmouseout="this.className='even'" <?php else:?>onmouseout="this.className=''"<?php endif;?>  onmouseover="this.className='onSelected'">
			  <td><?php echo $row->name;?></td>
			  <td>
			  	<?php if($row->type == 0):?>
					<?php echo lang('timer.type.unity');?>
				<?php else:?>
					<?php echo lang('timer.type.week');?>
				<?php endif;?>
			  </td>
			  <td><?php echo $row->descr; ?></td>
			  <td>
				<?php
				if($row->username == NULL || $row->username == '') {
					echo 'N/A';
				}else {
					echo $row->username;
				}
				?>
			  </td>
			  <td>
			  <?php echo $row->add_time; ?>

			  </td>
			  <td>
			  	<?php if($auth > 0):?>
			  	<a href="/config/edit_timer?id=<?php echo $row->id;?>" title="<?php echo lang('edit.timer.config');?>"><img id="edit_<?php echo $row->id;?>" src="/images/icons/24-edit.png" title="<?php echo lang('edit.view.config');?>" /></a>
			  	<a href="javascript:void(0);" onclick="cfg.removeTimer(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>');"><img id="del_<?php echo $row->id;?>" src="/images/icons/24-del.png"  title="<?php echo lang('delete');?>" /></a>
				<?php endif;?>
			  </td>
			</tr>
			<?php
			 	$index++; 
				endforeach; 
			?>
		<?php endif;?>

</table>

<?php
	$totalPage = intval(($total + ($limit - 1)) / $limit);
	$startIndex=($curpage>3)?$curpage-3:1;
	$endIndex= ($curpage<($totalPage-3)) ? ($curpage+3) : $totalPage;
?>
<div class="page-panel clearfix">
    <ul class="pagination">
    	<?php if($totalPage > 1):?>
			<?php if($curpage>1):?>
				<li><a href="/config/timers/1"><?php echo lang('page.first');?></a></li>
				<li><a href="/config/timers/<?php echo $curpage-1;?>"><?php echo lang('page.prev');?></a></li>	
			<?php endif;?>
	    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if($i == $curpage):?>
					<?php echo $i;?>
				<?php else:?>
					<a href="/config/timers/<?php echo $i;?>"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if($curpage<$totalPage):?>
				<li><a href="/config/timers/<?php echo $curpage+1;?>"><?php echo lang('page.next');?></a></li>
				<li><a href="/config/timers/<?php echo $totalPage;?>"><?php echo lang('page.last');?></a></li>	
			<?php endif;?>
	    <?php endif;?>
	    
  	</ul>
</div>
