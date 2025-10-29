<?php if ($total > 0): ?>
<div class="add-panel">
	<a href="javascript:void(0);"  onclick="playback.export();"  class="excel" title="<?php echo lang('export.excel');?>"><?php echo lang('export');?></a>
</div>
<div class="clear"></div>
<?php endif;?>
<table class="table-list"  width="100%" >
    <tr>
    	<th width="60" >
			<a href="javascript:void(0);" onclick="playback.query(<?php echo $curpage;?>,'post_date','<?php if ($order_item == 'post_date' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('date.time');?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'post_date' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'post_date' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		</th>
        <th width="100">
			<a href="javascript:void(0);" onclick="playback.query(<?php echo $curpage;?>,'player_name','<?php if ($order_item == 'player_name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('player');?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'player_name' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'player_name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		</th>

		<th >
			<a href="javascript:void(0);" onclick="playback.query(<?php echo $curpage;?>,'media_id','<?php if ($order_item == 'media_id' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('media');?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'media_id' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'media_id' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		</th>
		<th >

			<a href="javascript:void(0);" onclick="playback.query(<?php echo $curpage;?>,'campaign_name','<?php if ($order_item == 'campaign_name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('campaign');?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'campaign_name' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'campaign_name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			
		</th>
		<th width="100" >
			<a href="javascript:void(0);" onclick="playback.query(<?php echo $curpage;?>,'times','<?php if ($order_item == 'times' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('times');?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'times' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'times' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		</th>
        <th width="100">
			<a href="javascript:void(0);" onclick="playback.query(<?php echo $curpage;?>,'duration','<?php if ($order_item == 'duration' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('duration');?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'duration' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'duration' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		</th>
    </tr>
	
	<?php if ($total == 0): ?>
	<tr>
		<td colspan="7">
			<?php echo lang("empty.result");?>
		</td>
	</tr>
	<?php else:
      $index = 0;
    ?>
		<?php foreach ($data as $row):?>
		<tr <?php if ($index%2 != 0):?>class="even" onmouseout="this.className='even'" <?php else:?>onmouseout="this.className=''"<?php endif;?>  onmouseover="this.className='onSelected'">
		  <td><?php echo $row->post_date;?></td>
		
		  <td><?php echo $row->player_name;?></td>

		  <td>
		  	<?php echo $row->media_name;?>
		  </td>
		  <td><?php echo $row->campaign_name; ?></td>
		  <td><?php echo $row->times; ?></td>
		  <td><?php echo $row->duration; ?></td>
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
    	<?php if ($totalPage > 1):?>
			<?php if ($curpage>1):?>
				<li><a href="javascript:void(0);" onclick="playback.query(1,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.first');?></a></li>
				<li><a href="javascript:void(0);" onclick="playback.query(<?php echo $curpage-1;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.prev');?></a></li>	
			<?php endif;?>
	    	<?php for ($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if ($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if ($i == $curpage):?>
					<?php echo $i;?>
				<?php else:?>
					<a href="javascript:void(0);" onclick="playback.query(<?php echo $i;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if ($curpage<$totalPage):?>
				<li><a href="javascript:void(0);" onclick="playback.query(<?php echo $curpage+1;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.next');?></a></li>
				<li><a href="javascript:void(0);" onclick="playback.query(<?php echo $totalPage;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.last');?></a></li>	
			<?php endif;?>
	    <?php endif;?>
  	</ul>
</div>