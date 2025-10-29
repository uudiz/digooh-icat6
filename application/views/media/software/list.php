<?php if($auth == $SYSTEM):?>
<div class="add-panel">
	<a href="/software/add?width=400&height=350" id="create" class="thickbox" title="<?php echo lang('upload.software');?>"><?php echo lang('upload');?></a>
</div>
<div class="clear"></div>
<?php endif;?>
<h1 class="tit-01"><?php echo lang('software');?><span></span></h1>
<table class="table-list"  width="100%" >
        <tr>
        	<th width="200" >
			<a href="/software/index/<?php echo $curpage;?>/name/<?php if($order_item == 'name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>" ><?php echo lang('filename');?></a>
			<img alt="" src="/images/icons/<?php if($order_item == 'name' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
            <th width="60" >
			<a href="/software/index/<?php echo $curpage;?>/version/<?php if($order_item == 'version' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>" ><?php echo lang('version');?></a>
			<img alt="" src="/images/icons/<?php if($order_item == 'version' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'version' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		</th>
		<?php if($this->config->item('mia_system_set') == $this->config->item('mia_system_all')) :?>
		<th width="40"> <?php echo lang('player.type');?></th>
		<?php endif;?>
		<th>
			<a href="/software/index/<?php echo $curpage;?>/mpeg_core/<?php if($order_item == 'mpeg_core' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>" ><?php echo lang('f.model');?></a>
			<img alt="" src="/images/icons/<?php if($order_item == 'mpeg_core' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'mpeg_core' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		</th>
        <th>
			<a href="/software/index/<?php echo $curpage;?>/descr/<?php if($order_item == 'descr' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>" ><?php echo lang('desc');?></a>
			<img alt="" src="/images/icons/<?php if($order_item == 'descr' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'descr' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		</th>
		<th width="160">
			<a href="/software/index/<?php echo $curpage;?>/publish_time/<?php if($order_item == 'publish_time' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>" ><?php echo lang('publish.time');?></a>
			<img alt="" src="/images/icons/<?php if($order_item == 'publish_time' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'publish_time' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		</th>
        <th width="120">
			<a href="/software/index/<?php echo $curpage;?>/add_time/<?php if($order_item == 'add_time' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>" ><?php echo lang('update.time');?></a>
			<img alt="" src="/images/icons/<?php if($order_item == 'add_time' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'add_time' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		</th>
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
	<tr id="software_<?php echo $row->id;?>" <?php if($index%2 != 0):?>class="even" onmouseout="this.className='even'" <?php else:?>onmouseout="this.className=''"<?php endif;?>  onmouseover="this.className='onSelected'">
		<td>
			<?php echo $row->name;?>
		</td>
		<td>
			<?php echo $row->version;?>
			<input type="hidden" id="version_<?php echo $row->id?>" value="<?php echo $row->version;?>"/>
		</td>
		<?php if($this->config->item('mia_system_set') == $this->config->item('mia_system_all')) :?>
		<td>
			<?php if($row->type):?>
					<?php if($row->mpeg_core==3):?>
						<img src="/images/icons/android_blue.png"  title="<?php echo lang('type.'.$row->type);?>" />
					<?php else:?>
						<img src="/images/icons/android.png"  title="<?php echo lang('type.'.$row->type);?>" />
					<?php endif?>
			<?php else:?>
				<img src="/images/icons/windows.png"  title="<?php echo lang('type.'.$row->type);?>" />
			<?php endif;?>
		</td>
		<?php endif;?>
		<td><?php echo $modelArr[$row->mpeg_core];?></td>
		<td><?php echo $row->descr; ?></td>
		<td><?php echo $row->publish_time; ?></td>
		<td><?php echo $row->add_time; ?></td>
		<td>
			<?php if($auth == $SYSTEM):?>
			 	<a href="javascript:void(0);" onclick="software.remove(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>');"><img id="del_<?php echo $row->id;?>" src="/images/icons/24-del.png" title="<?php echo lang('delete');?>" /></a>
				<a href="/software/edit?id=<?php echo $row->id;?>&amp;width=450&amp;height=300" class="thickbox" title="<?php echo lang('edit.software');?>"><img id="edit_<?php echo $row->id;?>" src="/images/icons/24-edit.png"  title="<?php echo lang('edit');?>" /></a>
			<?php elseif($auth == $ADMIN):?>
				<a href="javascript:void(0);" onclick="software.toggle(this)" status="0" stype="<?php echo $row->type;?>" core="<?php echo $row->mpeg_core;?>" id="<?php echo $row->id;?>">
					<img width="16" height="16" tc="Collapse this item" te="Expland this item" title="Expland this item" src="/images/icons/16-05.gif">
				</a>
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
				<li><a href="/software/index/1"><?php echo lang('page.first');?></a></li>
				<li><a href="/software/index/<?php echo $curpage-1;?>"><?php echo lang('page.prev');?></a></li>	
			<?php endif;?>
	    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if($i == $curpage):?>
					<?php echo $i;?>
				<?php else:?>
					<a href="/software/index/<?php echo $i;?>"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if($curpage<$totalPage):?>
				<li><a href="/software/index/<?php echo $curpage+1;?>"><?php echo lang('page.next');?></a></li>
				<li><a href="/software/index/<?php echo $totalPage;?>"><?php echo lang('page.last');?></a></li>	
			<?php endif;?>
	    <?php endif;?>
	    
  	</ul>
</div>

<script>
	$(document).ready(function(){
	//gl.init();
});
</script>