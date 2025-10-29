<?php if($auth >= $GROUP && $max_limit == FALSE):?>
<div class="add-panel">
	<a href="/ftp/add?width=400&height=340" id="create" class="thickbox" title="<?php echo lang('ftp.create.site');?>"><?php echo lang('create');?></a>
</div>
<?php endif;?>
<div class="clear"></div>
<h1 class="tit-01"><?php echo lang('ftp');?><span></span></h1>
<table class="table-list"  width="100%" >
        <tr>
        	<th width="240" >
				<a href="javascript:void(0);" onclick="ftp.page(<?php echo $curpage;?>,'profile','<?php if($order_item == 'profile' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('ftp.profile');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'profile' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'profile' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
            <th>
				<a href="javascript:void(0);" onclick="ftp.page(<?php echo $curpage;?>,'server','<?php if($order_item == 'server' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('ftp.server');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'server' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'server' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<th width="120">
				<a href="javascript:void(0);" onclick="ftp.page(<?php echo $curpage;?>,'add_user_id','<?php if($order_item == 'add_user_id' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('create.user');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'add_user_id' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'add_user_id' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
            <th width="120">
				<a href="javascript:void(0);" onclick="ftp.page(<?php echo $curpage;?>,'add_time','<?php if($order_item == 'add_time' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('update.time');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'add_time' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'add_time' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<th width="100"><?php echo lang('operate');?></th>
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
			  	<?php if(mb_strlen($row->profile) > 24){echo mb_substr($row->profile, 0, 24).'..';}else{echo $row->profile;}?>
			  </td>
			  <td>
			  	<?php if(mb_strlen($row->server) > 64){echo mb_substr($row->server, 0, 64).'...';}else{echo $row->server;}?>
			  </td>
			  <td>
			  	<?php 
					if($row->add_user == NULL || $row->add_user == '') {
						echo 'N/A';
					}else {
						echo $row->add_user;
					}
				?>
			  </td>
			  <td><?php echo $row->add_time; ?></td>
			  <td>
			  	<?php if($auth > 0):?>
			  		<a href="/ftp/edit/<?php echo $row->id;?>?width=400&height=340" class="thickbox" title="<?php echo lang('edit.ftp');?>"><img  id="edit_<?php echo $row->id;?>" src="/images/icons/24-edit.png"  title="<?php echo lang('edit');?>" /></a>
			  		<a href="javascript:void(0);" onclick="ftp.remove(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>');"><img id="del_<?php echo $row->id;?>" src="/images/icons/24-del.png"  title="<?php echo lang('delete');?>" /></a>
				<?php else:?>
					&nbsp;
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
				<li><a href="/folder/index/1/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.first');?></a></li>
				<li><a href="/folder/index/<?php echo $curpage-1;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.prev');?></a></li>	
			<?php endif;?>
	    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if($i == $curpage):?>
					<?php echo $i;?>
				<?php else:?>
					<a href="/folder/index/<?php echo $i;?>/<?php echo $order_item.'/'.$order;?>"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if($curpage<$totalPage):?>
				<li><a href="/folder/index/<?php echo $curpage+1;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.next');?></a></li>
				<li><a href="/folder/index/<?php echo $totalPage;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.last');?></a></li>	
			<?php endif;?>
	    <?php endif;?>
	    
  	</ul>
</div>

<script>
	$(document).ready(function(){
	//gl.init();
});
</script>