<?php if($total > 0): ?>
<div class="add-panel">
	<a href="/logger/excel?start_date=<?php echo $start_date;?>&end_date=<?php echo $end_date;?>&cid=<?php echo $cid;?>"  class="excel" title="<?php echo lang('export.excel');?>">export</a>
</div>
<div class="clear">
</div>
<?php endif;?>
	<table class="table-list"  width="100%" >
        <tr>
            <th width="120" >
				<a href="javascript:void(0);" onclick="log.page(<?php echo $curpage;?>,'add_time','<?php if($order_item == 'add_time' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('logger.time');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'add_time' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'add_time' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<th width="100" >
				<a href="javascript:void(0);" onclick="log.page(<?php echo $curpage;?>,'user_id','<?php if($order_item == 'user_id' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('user');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'user_id' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'user_id' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<th width="60" >
				<a href="javascript:void(0);" onclick="log.page(<?php echo $curpage;?>,'company_id','<?php if($order_item == 'company_id' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('company');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'company_id' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'company_id' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<th width="120"><?php echo lang('ip');?></th>
            <th ><?php echo lang('action');?></th>
        </tr>
		
		<?php if($total == 0): ?>
		<tr>
			<td colspan="5">
				<?php echo lang("empty");?>
			</td>
		</tr>
		<?php else:
		  $index = 0;
		?>
			<?php foreach($data as $row):?>
			<tr <?php if($index%2 != 0):?>class="even" onmouseout="this.className='even'" <?php else:?>onmouseout="this.className=''"<?php endif;?>  onmouseover="this.className='onSelected'">
			  <td>
			  	<?php echo $row->add_time;?>
			  </td>
			  <td><?php echo $row->user_name;?></td>
			  <td><?php echo $row->company_name;?></td>
			  <td><?php echo $row->ip;?></td>
			  <td><?php echo $row->detail;?></td>
			</tr>
			<?php
			 	$index++; 
				endforeach; 
			?>
		<?php endif;?>

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
				<li><a href="javascript:void(0);" onclick="log.page(1,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.first');?></a></li>
				<li><a href="javascript:void(0);" onclick="log.page(<?php echo $curpage-1;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.prev');?></a></li>	
			<?php endif;?>
	    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if($i == $curpage):?>
					<?php echo $i;?>
				<?php else:?>
					<a href="javascript:void(0);" onclick="log.page(<?php echo $i;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if($curpage<$totalPage):?>
				<li><a href="javascript:void(0);" onclick="log.page(<?php echo $curpage+1;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.next');?></a></li>
				<li><a href="javascript:void(0);" onclick="log.page(<?php echo $totalPage;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.last');?></a></li>	
			<?php endif;?>
	    <?php endif;?>
  	</ul>
</div>