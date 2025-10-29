<table class="table-list"  width="100%" border="0">
	<tr>
		<th width="240" >
			     <a href="javascript:void(0);" onclick="configxml.pages(<?php echo $curpage;?>,'name','<?php if($order_item == 'name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('filename');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'name' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		</th>
		<?php
		if($this->config->item('mia_system_set') == $this->config->item('mia_system_all')):
		?>
		<th width="100" >
			<?php echo lang('player.type');?>
		</th>
		<?php endif;?>
        <th>
			   <a href="javascript:void(0);" onclick="configxml.pages(<?php echo $curpage;?>,'descr','<?php if($order_item == 'descr' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('desc');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'descr' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'descr' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		</th>
        <th width="120">
	
			<a href="javascript:void(0);" onclick="configxml.pages(<?php echo $curpage;?>,'add_time','<?php if($order_item == 'add_time' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('update.time');?></a>
			<img alt="" src="/images/icons/<?php if($order_item == 'add_time' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'add_time' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
	
		</th>
		<th width="120"><?php echo lang('operate');?></th>
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
			<?php echo $row->name; ?>
		</td>
				<?php
		if($this->config->item('mia_system_set') == $this->config->item('mia_system_all')):
		?>
		<td>
			<?php 
			if($row->player_type):
			?>
			<img src="/images/icons/android.png"  title="<?php echo lang('type.1');?>" />
			<?php else:?>
			<img src="/images/icons/windows.png"  title="<?php echo lang('type.0');?>" />
			<?php endif;?>
		</td>
		<?php endif;?>
		<td>
			<?php echo $row->descr; ?>
		</td>
		<td>
			<?php echo $row->add_time; ?>
		</td>
		<td>
			<a href="/configxml/edit?id=<?php echo $row->id;?>&width=500&height=720"  class="thickbox" title="<?php echo lang('device.config');?>"><img id="edit_<?php echo $row->id;?>" src="/images/icons/24-edit.png"  title="<?php echo lang('edit');?>" /></a>
			<a href="javascript:void(0);" onclick="configxml.remove(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>');"><img id="del_<?php echo $row->id;?>" src="/images/icons/24-del.png" title="<?php echo lang('delete');?>" /></a>
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
				<li><a href="/configxml/index/1/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.first');?></a></li>
				<li><a href="/configxml/index/<?php echo $curpage-1;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.prev');?></a></li>	
			<?php endif;?>
	    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if($i == $curpage):?>
					<?php echo $i;?>
				<?php else:?>
					<a href="/configxml/index/<?php echo $i;?>/<?php echo $order_item.'/'.$order;?>"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if($curpage<$totalPage):?>
				<li><a href="/configxml/index/<?php echo $curpage+1;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.next');?></a></li>
				<li><a href="/configxml/index/<?php echo $totalPage;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.last');?></a></li>	
			<?php endif;?>
	    <?php endif;?>
	    
  	</ul>
</div>
