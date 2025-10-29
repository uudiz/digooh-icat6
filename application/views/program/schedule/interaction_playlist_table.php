<table class="table-list"  width="100%" >
    <tr>
    	<th width="20"></th>
        <th width="200"><?php echo lang('name');?></th>
		<th width="120"><?php echo lang('create.user');?></th>
        <th width="80"><?php echo lang('update.time');?></th>
		
    </tr>
	<?php if($total == 0): ?>
	<tr>
		<td colspan="4">
			<?php echo lang("empty");?>
		</td>
	</tr>
	<?php else:
	  $index = 0;
	?>
		<?php foreach($data as $row):?>
		<tr <?php if($index%2 != 0):?>class="even" onmouseout="this.className='even'" <?php else:?>onmouseout="this.className=''"<?php endif;?>  onmouseover="this.className='onSelected'">
		  <td><input type="radio" name="pid" value="<?php echo $row->id;?>"></td>
		  <td><?php echo $row->name;?></td>
		  <td><?php echo $row->user; ?></td>
		  <td><?php echo $row->add_time; ?></td>
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
				<li><a href="javascript:schedule.form.playlistPage(1);"><?php echo lang('page.first');?></a></li>
				<li><a href="javascript:schedule.form.playlistPage(<?php echo $curpage-1;?>)"><?php echo lang('page.prev');?></a></li>	
			<?php endif;?>
	    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if($i == $curpage):?>
					<?php echo $i;?>
				<?php else:?>
					<a href="javascript:schedule.form.playlistPage(<?php echo $i;?>)"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if($curpage<$totalPage):?>
				<li><a href="javascript:schedule.form.playlistPage(<?php echo $curpage+1;?>)"><?php echo lang('page.next');?></a></li>
				<li><a href="javascript:schedule.form.playlistPage(<?php echo $totalPage;?>)"><?php echo lang('page.last');?></a></li>	
			<?php endif;?>
	    <?php endif;?>
	    
  	</ul>
</div>