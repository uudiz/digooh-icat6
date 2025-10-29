<?php if($auth > 0):?>
<div class="add-panel">
	<a href="/config/add_view?width=500&height=450" id="create" class="thickbox" title="<?php echo lang('create.view.config');?>"><?php echo lang('create');?></a>
</div>
<?php endif;?>
<div class="clear"></div>
<h1 class="tit-01"><?php echo lang('view.config');?><span></span></h1>
<table class="table-list"  width="100%" >
        <tr>
            <th width="200" ><?php echo lang('name');?></th>
			<th width="120" ><?php echo lang('start.time');?></th>
			<th width="120" ><?php echo lang('end.time');?></th>
            <th><?php echo lang('desc');?></th>
			<th width="100"><?php echo lang('operator');?></th>
            <th width="120"><?php echo lang('create.time');?></th>
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
			  <td><?php echo $row->name;?></td>
			  <td><?php echo $row->start_datetime;?></td>
			  <td><?php echo $row->end_datetime;?></td>
			  <td><?php echo $row->descr; ?></td>
			  <td><?php echo $row->username; ?></td>
			  <td><?php echo $row->add_time; ?></td>
			  <td>
			  	<?php if($auth > 0):?>
			  	<a href="/config/edit_view?id=<?php echo $row->id;?>&width=500&height=450" class="thickbox" title="<?php echo lang('edit.view.config');?>"><img src="/images/icons/16-03.gif" width="16" height="16" title="<?php echo lang('edit.view.config');?>" /></a>
			  	<a href="javascript:void(0);" onclick="cfg.removeView(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>');"><img src="/images/icons/16-04.gif" width="16" height="16" title="<?php echo lang('delete');?>" /></a>
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
				<li><a href="/config/views/1"><?php echo lang('page.first');?></a></li>
				<li><a href="/config/views/<?php echo $curpage-1;?>"><?php echo lang('page.prev');?></a></li>	
			<?php endif;?>
	    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if($i == $curpage):?>
					<?php echo $i;?>
				<?php else:?>
					<a href="/config/views/<?php echo $i;?>"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if($curpage<$totalPage):?>
				<li><a href="/config/views/<?php echo $curpage+1;?>"><?php echo lang('page.next');?></a></li>
				<li><a href="/config/views/<?php echo $totalPage;?>"><?php echo lang('page.last');?></a></li>	
			<?php endif;?>
	    <?php endif;?>
	    
  	</ul>
</div>

<script>
	$(document).ready(function(){
	//gl.init();
});
</script>