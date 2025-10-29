<div class="table-responsive">
<table class="table table-striped" >
	<thead>
        <tr >
            <th  >
				<a href="javascript:void(0);" onclick="g.page(<?php echo $curpage;?>,'name','<?php if($order_item == 'name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('group');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'name' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
            <th >
				<a href="javascript:void(0);" onclick="g.page(<?php echo $curpage;?>,'descr','<?php if($order_item == 'descr' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('desc');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'descr' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'descr' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
            <th >
				<a href="javascript:void(0);" onclick="g.page(<?php echo $curpage;?>,'add_time','<?php if($order_item == 'add_time' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('update.time');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'add_time' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'add_time' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<th ><?php echo lang('player.type');?></th>
			<th >
				<a href="javascript:void(0);" onclick="g.page(<?php echo $curpage;?>,'player_count','<?php if($order_item == 'player_count' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('player.count');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'player_count' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'player_count' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />

			</th>
			<!--
			<th width="120">
				<a href="javascript:void(0);" onclick="g.page(<?php echo $curpage;?>,'timer_config_id','<?php if($order_item == 'timer_config_id' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('timecfg');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'timer_config_id' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'timer_config_id' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />

			</th>
			-->
			<th width="120"><?php echo lang('operate');?></th>
        </tr>
	</thead>	
		<?php if($total == 0): ?>
		<tr>
			<td colspan="5">
				<?php echo lang("empty");?>
			</td>
		</tr>
		<?php else:
		  $index = 0;
		?>
		<tbody>
			<?php foreach($data as $row):?>
			<tr <?php if($index%2 != 0):?>class="even" onmouseout="this.className='even'" <?php else:?>onmouseout="this.className=''"<?php endif;?>  onmouseover="this.className='onSelected'">
			  <td>
			  	<?php if(mb_strlen($row->name) > 24){echo mb_substr($row->name, 0, 24).'..';}else{echo $row->name;}?>
			  </td>
			  <td>
			  	<?php if(mb_strlen($row->descr) > 48){echo mb_substr($row->descr, 0, 48).'...';}else{echo $row->descr;}?>
			  </td>
			  <td><?php echo $row->add_time; ?></td>
			  <td>
			  	<?php 
				if($row->type):
				?>
				<img src="/images/icons/android.png"  title="<?php echo lang('type.1');?>" />
				<?php else:?>
				<img src="/images/icons/windows.png"  title="<?php echo lang('type.0');?>" />
				<?php endif;?>
			  </td>
			  <td><?php echo $row->player_count; ?></td>
			  <!--
			  <td>
                    <a href="/config/edit_timer?id=<?php echo $row->timer_config_id;?>"><?php echo $row->timecfg; ?></a>
              </td>
			  -->
			  <td>
			  	<?php if($auth > 1):?>
			  		<a target="dialog" href="/group/edit?id=<?php echo $row->id;?>&width=450&height=320"  class="thickbox" title="<?php echo lang('edit.group');?>"><img id="edit_<?php echo $row->id;?>" src="/images/icons/24-edit.png"  title="<?php echo lang('edit');?>" /></a>
			  		<a target="dialog" href="javascript:void(0);" onclick="g.remove(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>');"><img id="del_<?php echo $row->id;?>" src="/images/icons/24-del.png" title="<?php echo lang('delete');?>" /></a>
				<?php else:?>
					&nbsp;
				<?php endif;?>
			  </td>
			</tr>
			<?php
			 	$index++; 
				endforeach; 
			?>
		</tbody>	
		<?php endif;?>

</table>
</div>
<?php
$totalPage = floor(($total + ($limit - 1)) / $limit);

$startIndex=($curpage>3)?$curpage-3:1;
$endIndex= ($curpage<($totalPage-3)) ? ($curpage+3) : $totalPage;
?>
<?php if($totalPage > 1):?>

    <ul class="pagination">
			<?php if($curpage>1):?>
				<li><a href="/group/index/1/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.first');?></a></li>
				<li><a href="/group/index/<?php echo $curpage-1;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.prev');?></a></li>	
			<?php endif;?>
	    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if($i == $curpage):?>
					<?php echo $i;?>
				<?php else:?>
					<a href="/group/index/<?php echo $i;?>/<?php echo $order_item.'/'.$order;?>"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if($curpage<$totalPage):?>
				<li><a href="/group/index/<?php echo $curpage+1;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.next');?></a></li>
				<li><a href="/group/index/<?php echo $totalPage;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.last');?></a></li>	
			<?php endif;?>

	    
  	</ul>

 <?php endif;?>	