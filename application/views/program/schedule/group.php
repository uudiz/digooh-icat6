<div id="validateTips">
    <div>
        <div id="formMsgContent">
        </div>
    </div>
</div>
<h1 class="tit-01"><?php echo lang('group');?><span></span></h1>
<table class="table-list"  width="100%" >
        <tr>
        	<th width="30" ><input type="checkbox" onclick="schedule.form.checkAllGroup(this);" /></th>
            <th width="100" ><?php echo lang('group');?></th>
            <th ><?php echo lang('desc');?></th>
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
			<tr <?php if($index%2 != 0):?>class="even"<?php endif;?>>
			  <td><input type="checkbox" name="gid" value="<?php echo $row->id;?>"/></td>
			  <td><?php echo $row->name;?></td>
			  <td><?php echo $row->descr; ?></td>
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
				<li><a href="javascript:schedule.form.groupPage(1,<?php echo $sch_type?>);"><?php echo lang('page.first');?></a></li>
				<li><a href="javascript:schedule.form.groupPage(<?php echo $curpage-1;?>,<?php echo $sch_type?>);"><?php echo lang('page.prev');?></a></li>	
			<?php endif;?>
	    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if($i == $curpage):?>
					<?php echo $i;?>
				<?php else:?>
					<a href="javascript:schedule.form.groupPage(<?php echo $i;?>,<?php echo $sch_type?>);"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if($curpage<$totalPage):?>
				<li><a href="javascript:schedule.form.groupPage(<?php echo $curpage+1;?>,<?php echo $sch_type?>);"><?php echo lang('page.next');?></a></li>
				<li><a href="javascript:schedule.form.groupPage(<?php echo $totalPage;?>,<?php echo $sch_type?>);"><?php echo lang('page.last');?></a></li>	
			<?php endif;?>
	    <?php endif;?>
	    
  	</ul>
</div>
<p class="btn-center">
	<a href="javascript:void(0);" onclick="schedule.form.addGroup('<?php echo lang('warn.scheudle.group.empty');?>');return false;" class="btn-01"><span><?php echo lang('button.add');?></span></a>
	<a href="javascript:void(0);" onclick="tb_remove();return false;" class="btn-01"><span><?php echo lang('button.cancel');?></span></a>
</p>