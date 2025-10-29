<table class="table-list"  width="100%" border="0">
    <tr>
		<th>code</th>
		<th>status</th>
		<th>add time</th>
		<th>Description</th>
        <th>mac</th>
		<th>connect time</th>
        <th>ip</th>
		<th width="100"><?php echo lang('operate');?></th>
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
		<td><?php echo $row->code;?></td>
		<td>
			<?php
				$sdesc=$row->status;
				switch($row->status){
				  	case 0: //未使用
				  		$sdesc = 'Not Used';
						break;
					case 1: //使用
						$sdesc = 'Used';
						break;
				}
			?>
			<img src="<?php if($row->status == 1):?>/images/icons/led_on.png<?php else:?>/images/icons/led_off.png<?php endif;?>" alt="<?php echo $sdesc?>" title="<?php echo $sdesc?>" status="<?php echo $row->status;?>"/>
		</td>
		<td><?php echo $row->add_time;?></td>
		<td><?php echo $row->descr;?></td>
		<td><?php echo $row->mac;?></td>
		<td><?php echo $row->connect_time;?></td>
		<td><?php echo $row->ip;?></td>
		<td>
			<a href="/authorize/edit?id=<?php echo $row->id;?>&width=350&height=250" class="thickbox" title="<?php echo lang('edit.user');?>"><img id="edit_<?php echo $row->id;?>" src="/images/icons/24-edit.png"  title="<?php echo lang('edit');?>" /></a>
			<a href="javascript:void(0);" onclick="au.remove(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>');"><img id="del_<?php echo $row->id;?>" src="/images/icons/24-del.png" title="<?php echo lang('delete');?>" /></a>
		</td>
	</tr>
	<?php
		$index++; 
		endforeach; 
		endif;
	?>
</table>
<?php
	$totalPage = intval(($total + ($limit - 1)) / $limit);
	$startIndex = 1;
	$endIndex   = $totalPage;
	$midIndex   = intval(($curpage + 5) / 2); 
	if($midIndex - 2 > $startIndex){
		$startIndex = $midIndex - 2;
	}
	if($midIndex + 2 < $endIndex){
		$endIndex = $midIndex + 2;
	}
?>
<div class="page-panel clearfix">
    <ul class="pagination">
    	<?php if($totalPage > 1):?>
			<?php if($curpage>1):?>
				<li><a href="/authorize/index/1/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.first');?></a></li>
				<li><a href="/authorize/index/<?php echo $curpage-1;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.prev');?></a></li>	
			<?php endif;?>
	    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if($i == $curpage):?>
					<?php echo $i;?>
				<?php else:?>
					<a href="/authorize/index/<?php echo $i;?>/<?php echo $order_item.'/'.$order;?>"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if($curpage<$totalPage):?>
				<li><a href="/authorize/index/<?php echo $curpage+1;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.next');?></a></li>
				<li><a href="/authorize/index/<?php echo $totalPage;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.last');?></a></li>	
			<?php endif;?>
	    <?php endif;?>
  	</ul>
</div>
