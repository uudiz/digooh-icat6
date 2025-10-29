<?php if($total > 0): ?>
<!--
<div class="add-panel">
	<a href="/bandwidth/excel?start_date=<?php echo $start_date;?>&end_date=<?php echo $end_date;?>&player_id=<?php echo $player_id;?>"  class="excel" title="<?php echo lang('export.excel');?>"><?php echo lang('export');?></a>
</div>
-->
<div class="clear"></div>
<?php endif;?>
<table class="table-list"  width="100%" >
    <tr>
    	<th width="120" >
			<a href="javascript:void(0);" onclick="bandwidth.query(<?php echo $curpage;?>,'recode_date','<?php if($order_item == 'recode_date' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('recode.date');?></a>
			<img alt="" src="/images/icons/<?php if($order_item == 'recode_date' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'recode_date' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		</th>
		<th>
			<a href="javascript:void(0);" onclick="bandwidth.query(<?php echo $curpage;?>,'group_id','<?php if($order_item == 'group_id' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('group');?></a>
			<img alt="" src="/images/icons/<?php if($order_item == 'group_id' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'group_id' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		</th>
        <th>
			<a href="javascript:void(0);" onclick="bandwidth.query(<?php echo $curpage;?>,'id','<?php if($order_item == 'id' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('player');?></a>
			<img alt="" src="/images/icons/<?php if($order_item == 'id' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'id' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		</th>
		<th width="100"><?php echo lang('sn');?></th>
		<th><?php echo lang('type');?></th>
		<th>
			<a href="javascript:void(0);" onclick="bandwidth.query(<?php echo $curpage;?>,'used_bandwidth','<?php if($order_item == 'used_bandwidth' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('date.volume');?></a>
			<img alt="" src="/images/icons/<?php if($order_item == 'used_bandwidth' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'used_bandwidth' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		</th>
    </tr>
	
	<?php if($total == 0): ?>
	<tr>
		<td colspan="7">
			<?php echo lang("empty.result");?>
		</td>
	</tr>
	<?php else:
	  $index = 0;
	?>
		<?php foreach($data as $row):?>
		<tr <?php if($index%2 != 0):?>class="even" onmouseout="this.className='even'" <?php else:?>onmouseout="this.className=''"<?php endif;?>  onmouseover="this.className='onSelected'">
		  	<td><?php echo $row->recode_date;?></td>
		  	<td><?php echo $row->group_name;?></td>
		  	<td><?php echo $row->player_name;?></td>
		  	<td><?php echo format_sn($row->sn);?></td>
		  	<td>
				<?php 
				if($row->type):
				?>
				<img src="/images/icons/android.png"  title="<?php echo lang('type.1');?>" />
				<?php else:?>
				<img src="/images/icons/windows.png"  title="<?php echo lang('type.0');?>" />
				<?php endif;?>
			</td>
			<td>
				<?php 
				//echo $row->used_bandwidth;
					$file_size = $row->used_bandwidth;
					if($file_size > 1024){
						$file_size /= 1024;
						if($file_size > 1024){
							echo sprintf('%.2f MB',$file_size/1024);
						}else{
							echo sprintf('%.2f KB', $file_size);
						}
					}else{
						echo $file_size.' byte';
					}
				?>
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
				<li><a href="javascript:void(0);" onclick="bandwidth.query(1);"><?php echo lang('page.first');?></a></li>
				<li><a href="javascript:void(0);" onclick="bandwidth.query(<?php echo $curpage-1;?>);"><?php echo lang('page.prev');?></a></li>	
			<?php endif;?>
	    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if($i == $curpage):?>
					<?php echo $i;?>
				<?php else:?>
					<a href="javascript:void(0);" onclick="bandwidth.query(<?php echo $i;?>);"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if($curpage<$totalPage):?>
				<li><a href="javascript:void(0);" onclick="bandwidth.query(<?php echo $curpage+1;?>);"><?php echo lang('page.next');?></a></li>
				<li><a href="javascript:void(0);" onclick="bandwidth.query(<?php echo $totalPage;?>);"><?php echo lang('page.last');?></a></li>	
			<?php endif;?>
	    <?php endif;?>
  	</ul>
</div>