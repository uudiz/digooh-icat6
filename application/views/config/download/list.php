<?php if($auth > 0):?>
<div class="add-panel">
	<a href="/config/add_download?width=600&height=600" id="create" class="thickbox" title="<?php echo lang('create.download.strategy');?>"><?php echo lang('create');?></a>
</div>
<?php endif;?>
<div class="clear"></div>
<h1 class="tit-01"><?php echo lang('download.strategy');?><span></span></h1>
<table class="table-list"  width="100%" >
        <tr>
            <th width="200" ><?php echo lang('name');?></th>
            <th><?php echo lang('desc');?></th>
			<th><?php echo lang('download.time.range');?></th>
			<th><?php echo lang('group');?></th>
			<th width="100"><?php echo lang('operator');?></th>
            <th width="120"><?php echo lang('update.time');?></th>
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
			  <td><?php echo $row->name;?></td>
			  <td><?php echo $row->descr; ?></td>
			  <td>
			  	<?php
				echo $row->start_time.'~'.$row->end_time;
				if(count($row->extras) > 0){
					echo '<br/>';
				}
				
				for($i = 0; $i < count($row->extras) && $i < 4; $i++){
					echo $row->extras[$i]->start_time.'~'.$row->extras[$i]->end_time;
					if($i < count($row->extras) - 1 && $i < 3){
						echo '<br/>';
					}	
				}
				
				if(count($row->extras) > 4){
					echo '...';
				}
				?>
			  </td>
			  <td>
			  	<?php foreach($row->groups as $g){
			  		echo $g->name.'&nbsp;&nbsp;';
			  	}?>
			  </td>
			  <td><?php echo $row->username; ?></td>
			  <td><?php echo $row->add_time; ?></td>
			  <td>
			  	<?php if($auth > 0):?>
				  	<a href="/config/edit_download?id=<?php echo $row->id;?>&width=600&height=600" class="thickbox" title="<?php echo lang('edit.download.strategy');?>"><img id="edit_<?php echo $row->id;?>" src="/images/icons/24-edit.png" title="<?php echo lang('edit.download.config');?>" /></a>
				  	<a href="javascript:void(0);" onclick="cfg.removeDownload(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>');"><img id="del_<?php echo $row->id;?>" src="/images/icons/24-del.png"  title="<?php echo lang('delete');?>" /></a>
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
				<li><a href="/config/downloads/1"><?php echo lang('page.first');?></a></li>
				<li><a href="/config/downloads/<?php echo $curpage-1;?>"><?php echo lang('page.prev');?></a></li>	
			<?php endif;?>
	    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if($i == $curpage):?>
					<?php echo $i;?>
				<?php else:?>
					<a href="/config/downloads/<?php echo $i;?>"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if($curpage<$totalPage):?>
				<li><a href="/config/downloads/<?php echo $curpage+1;?>"><?php echo lang('page.next');?></a></li>
				<li><a href="/config/downloads/<?php echo $totalPage;?>"><?php echo lang('page.last');?></a></li>	
			<?php endif;?>
	    <?php endif;?>
	    
  	</ul>
</div>

<script>
	$(document).ready(function(){
	//gl.init();
});
</script>