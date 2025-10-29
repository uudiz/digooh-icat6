<?php if($auth > 0):?>
<div class="add-panel">
	<a href="/webpage/addWebpage?width=500&height=220" id="create" class="thickbox" title="<?php echo lang('create.user');?>"><?php echo lang('add.webpage');?></a>
</div>
<?php endif;?>
<div class="clear"></div>
<h1 class="tit-01"><?php echo lang('webpage');?><span></span></h1>
<table class="table-list"  width="100%" >
        <tr>
            <th width="300" ><?php echo lang('name');?></th>
            <th width="300"><?php echo lang('desc');?></th>
			<th width="300"><?php echo lang('url');?></th>
            <th width="100"><?php echo lang('update.time');?></th>
			<th width="80"><?php echo lang('operate');?></th>
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
			  <!--
				<a href="/rss/view?id=<?php echo $row->id?>&type=<?php echo $row->type;?>&width=500&height=320"  class="thickbox" title="<?php echo lang('webpage.preview');?>">
			  	<u><?php if(mb_strlen($row->name) > 24){echo mb_substr($row->name, 0, 24).'..';}else{echo $row->name;}?></u>
				</a>
				-->
				<?php if(mb_strlen($row->name) > 24){echo mb_substr($row->name, 0, 24).'..';}else{echo $row->name;}?>
			  </td>
			  <td>
			  	<?php if(mb_strlen($row->descr) > 48){echo mb_substr($row->descr, 0, 48).'...';}else{echo $row->descr;}?>
			  </td>
			  <td>
				<?php
                	echo $row->url;
                ?>
			  </td>
			  <td><?php if(empty($row->last_update)){echo $row->add_time;}else{echo $row->last_update;} ?></td>
			  <td>
			  	<?php if($auth > 0):?>
			  	<a href="/webpage/editWebpage?id=<?php echo $row->id;?>&width=500&height=220" class="thickbox" title="<?php echo lang('edit.webpage');?>"><img id="edit_<?php echo $row->id;?>" src="/images/icons/24-edit.png" title="<?php echo lang('edit');?>" /></a>	
			  	<a href="javascript:void(0);" onclick="rss.remove(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>');"><img id="del_<?php echo $row->id;?>" src="/images/icons/24-del.png" title="<?php echo lang('delete');?>" /></a>
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
				<li><a href="/rss/index/1"><?php echo lang('page.first');?></a></li>
				<li><a href="/rss/index/<?php echo $curpage-1;?>"><?php echo lang('page.prev');?></a></li>	
			<?php endif;?>
	    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if($i == $curpage):?>
					<?php echo $i;?>
				<?php else:?>
					<a href="/rss/index/<?php echo $i;?>"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if($curpage<$totalPage):?>
				<li><a href="/rss/index/<?php echo $curpage+1;?>"><?php echo lang('page.next');?></a></li>
				<li><a href="/rss/index/<?php echo $totalPage;?>"><?php echo lang('page.last');?></a></li>	
			<?php endif;?>
	    <?php endif;?>
	    
  	</ul>
</div>