<?php
	$totalPage = intval(($total_group + ($limit_group - 1)) / $limit_group);
	$endIndex   = $totalPage;
?>
<?php if($curpage_group > 1 && $curpage_group <= $totalPage):?>
	<a href="javascript:void(0);" gid="0" page="<?php echo ($curpage_group - 1);?>" title="<?php echo lang('page.prev');?>">&lt;</a>
	<?php endif;?>
	<?php
		foreach($groups as $row):
	?>
    <a href="javascript:void(0);" <?php if($gid == $row->id):?> class="on"<?php endif;?> gid="<?php echo $row->id;?>"><?php echo $row->name;?></a>
	<?php
		endforeach;
	?>
<?php if($curpage_group < $totalPage):?>
	<a href="javascript:void(0);" gid="0" page="<?php echo ($curpage_group + 1);?>" title="<?php echo lang('page.next');?>">&gt;</a>
<?php endif;?>