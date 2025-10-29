<?php if($auth >= 5):?>
	
<div class="add-panel">
<?php if(!isset($usage_page)):?>
	<a href="javascript:void(0);"  onclick="player.exportplayers();"><img src="/images/icons/export.gif" title="export"/>Export</a>
<?php endif;?>	
</div>

<?php endif;?>
<table class="table-list"  width="100%" >
       <tr>
            <th width="200">
				<a href="javascript:void(0);" onclick="player.page(<?php echo $curpage;?>,'name','<?php if($order_item == 'name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('player');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'name' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<th width="100" >
				<a href="javascript:void(0);" onclick="player.page(<?php echo $curpage;?>,'sn','<?php if($order_item == 'sn' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('sn');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'sn' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'sn' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>

					 
		  	<th width='100'>
				<a href="javascript:void(0);" onclick="player.page(<?php echo $curpage;?>,'criteria_name','<?php if($order_item == 'criteria_name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('criteria');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'criteria_name' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'criteria_name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
            
         	<th width="100"><?php echo lang('next.7.day');?></th>
         	<th width="100"><?php echo lang('next.month');?></th>
         	<th width="100"><?php echo lang('next.6.month');?></th>

        </tr>
		
		<?php if($total == 0): ?>
		<tr>
			<td colspan="8">
			<?php if(isset($filter_array['filter_type'])): ?>
				<?php if($filter_array['filter_type']==''):?>
				<?php echo lang("empty"); ?>
				<?php else:?>
				<?php echo lang("search.empty.result1")."\"".$filter_array['filter']."\"".lang("search.empty.result2"); ?>
				<?php endif;?>
			<?php else:?>
				<?php echo lang("empty"); ?>
			<?php endif;?>				
			</td>	
		</tr>
		<?php else:
		  $index = 0;
		?>
		<?php foreach($data as $row):?>
		<tr <?php if($index%2 != 0):?>class="even" onmouseout="this.className='even'" <?php else:?>onmouseout="this.className=''"<?php endif;?>  onmouseover="this.className='onSelected'">
			<td>
	
			  		<?php if(mb_strlen($row->name) > 36){echo mb_substr($row->name, 0, 36).'..';}else{echo $row->name;}?>
	
			</td>
			<td><?php echo format_sn($row->sn);?></td>

			
			<td >
			 	<?php	 	echo $row->criteria_name; ?>			
			</td>

			<td>
				<?php if(isset($capacity[$row->id])) echo $capacity[$row->id]['day7_capcity'].'%';?>
			</td>
				
			<td>
				<?php if(isset($capacity[$row->id])) echo $capacity[$row->id]['nextmon_capacity'].'%';?>
			</td>
			<td>
				<?php if(isset($capacity[$row->id])) echo $capacity[$row->id]['next6mon_capacity'].'%';?>
			</td>

	

			</tr>
			<?php
			 	$index++; 
				endforeach; 
			?>
		<?php endif;?>

</table>
<?php
	$totalPage = floor(($total + ($limit - 1)) / $limit);
	
	$startIndex=($curpage>3)?$curpage-3:1;
	$endIndex= ($curpage<($totalPage-3)) ? ($curpage+3) : $totalPage;
?>
<div class="page-panel clearfix">
    <ul class="pagination">
    	<?php if($totalPage > 1):?>
			<?php if($curpage>1):?>
				<li><a href="javascript:void(0);" onclick="player.page(1,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.first');?></a></li>
				<li><a href="javascript:void(0);" onclick="player.page(<?php echo $curpage-1;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.prev');?></a></li>	
			<?php endif;?>
	    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if($i == $curpage):?>
					<?php echo $i;?>
				<?php else:?>
					<a href="javascript:void(0);" onclick="player.page(<?php echo $i;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if($curpage<$totalPage):?>
				<li><a href="javascript:void(0);" onclick="player.page(<?php echo $curpage+1;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.next');?></a></li>
				<li><a href="javascript:void(0);" onclick="player.page(<?php echo $totalPage;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.last');?></a></li>	
			<?php endif;?>
	    <?php endif;?>
  	</ul>
</div>

