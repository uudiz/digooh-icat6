<?php if($auth > 0):?>
<div class="add-panel">
	<a href="/interactionpls/add?width=500&height=300" class="add" id="create" title="<?php echo lang('playlist.new');?>"><?php echo lang('create');?></a>
</div>
<?php endif;?>
<div class="clear"></div>
<h1 class="tit-01"><?php echo lang('playlist');?><span></span></h1>
<table class="table-list"  width="100%" >
    <tr>
        <th width="30%" >
        	<a href="/interactionpls/index/<?php echo $curpage.'/name/';if($order_item == 'name' && $order == 'desc'){echo 'asc'; }else{echo 'desc';}?>" ><?php echo lang('name');?></a>
			<img alt="" src="/images/icons/<?php if($order_item == 'name' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		</th>
        <th >
        	<a href="/interactionpls/index/<?php echo $curpage.'/descr/';if($order_item == 'descr' && $order == 'desc'){echo 'asc'; }else{echo 'desc';}?>"  ><?php echo lang('desc');?></a>
			<img alt="" src="/images/icons/<?php if($order_item == 'descr' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'descr' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		</th>
		<th width="80">
			<a href="/interactionpls/index/<?php echo $curpage.'/published/';if($order_item == 'published' && $order == 'desc'){echo 'asc'; }else{echo 'desc';}?>"  ><?php echo lang('published');?></a>
			<img alt="" src="/images/icons/<?php if($order_item == 'published' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'published' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		</th>
		<th width="100">
			<a href="/interactionpls/index/<?php echo $curpage.'/add_user_id/';if($order_item == 'add_user_id' && $order == 'desc'){echo 'asc'; }else{echo 'desc';}?>"  ><?php echo lang('create.user');?></a>
			<img alt="" src="/images/icons/<?php if($order_item == 'add_user_id' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'add_user_id' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		</th>
		<th width="120">
			<a href="/interactionpls/index/<?php echo $curpage.'/update_time/';if($order_item == 'update_time' && $order == 'desc'){echo 'asc'; }else{echo 'desc';}?>"  ><?php echo lang('update.time');?></a>
			<img alt="" src="/images/icons/<?php if($order_item == 'update_time' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'update_time' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		</th>
        <th width="120">
			<a href="/interactionpls/index/<?php echo $curpage.'/template_id/';if($order_item == 'template_id' && $order == 'desc'){echo 'asc'; }else{echo 'desc';}?>"  ><?php echo lang('template');?></a>
			<img alt="" src="/images/icons/<?php if($order_item == 'template_id' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'template_id' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		</th>
		<th width="80"><?php echo lang('operate');?></th>
    </tr>
	<?php if($total == 0): ?>
	<tr>
		<td colspan="7">
			<?php echo lang("empty");?>
		</td>
	</tr>
	<?php else:
	  $index = 0;
	?>
	<?php foreach($data as $row):?>
	<tr <?php if($index%2 != 0):?>class="even" onmouseout="this.className='even'" <?php else:?>onmouseout="this.className=''"<?php endif;?>  onmouseover="this.className='onSelected'">
		<td>
		  	<?php if(mb_strlen($row->name) > 128){echo mb_substr($row->name, 0, 128).'..';}else{echo $row->name;}?>
		</td>
		<td>
		  	<?php if(mb_strlen($row->descr) > 64){echo mb_substr($row->descr, 0, 64).'...';}else{echo $row->descr;}?>
		</td>
		<td>
			<?php if($row->published == 0):?>
				<img src="/images/icons/led_off.png" alt="<?php echo lang('activity.status.saved');?>" title="<?php echo lang('activity.status.saved');?>"/>
			<?php elseif($row->published == 1):?>
				<img src="/images/icons/led_on.png" alt="<?php echo lang('activity.status.published');?>" title="<?php echo lang('activity.status.published');?>"/>
			<?php endif;?>
		</td>
		<td><?php echo $row->user; ?></td>
		<td><?php echo $row->update_time; ?></td>
		<td>
			<a href="/interaction/create_interaction_date?id=<?php echo $row->interaction_id;?>" style="color: #31718C;"><?php if(mb_strlen($row->interaction_name) > 24){echo mb_substr($row->interaction_name, 0, 24).'..';}else{echo $row->interaction_name;}?></a>
		</td>
		<td>
			<?php if($auth > 0):?>
		  	<a href="/interactionpls/screen?id=<?php echo $row->id;?>" title="<?php echo lang('edit.playlist');?>"><img id="edit_<?php echo $row->id;?>" src="/images/icons/24-edit.png"  /></a>
		  	<a href="javascript:void(0);" onclick="interactionpls.remove(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>');" title="<?php echo lang('delete');?>" ><img id="del_<?php echo $row->id;?>" src="/images/icons/24-del.png"  /></a>
			<?php endif;?>
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
				<li><a href="/interactionpls/index/1/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.first');?></a></li>
				<li><a href="/interactionpls/index/<?php echo $curpage-1;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.prev');?></a></li>
			<?php endif;?>
	    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if($i == $curpage):?>
				<?php echo $i;?>
				<?php else:?>
					<a href="/interactionpls/index/<?php echo $i;?>/<?php echo $order_item.'/'.$order;?>"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if($curpage<$totalPage):?>
				<li><a href="/interactionpls/index/<?php echo $curpage+1;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.next');?></a></li>
				<li><a href="/interactionpls/index/<?php echo $totalPage;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.last');?></a></li>
			<?php endif;?>
	    <?php endif;?>
  	</ul>
</div>