<?php if(isset($total) && $total > 0):?>
<div class="pager">
	<?php
	if(!isset($limit)){
		$limit = 10;
	}
	$total_page = intval(($total + 1) / $limit);
	if(!isset($curpage)){
		$curpage = 0;
	}
	
	$start = 1;
	$end   = 5;
	$show  = 5;//显示页面数
	$show_radius = $show / 2;
	if($curpage > 0){
		$middle = ($curpage + $show + 1) / 2;
		$start = $middle - $show_radius;
		$end   = $middle + $show_radius;
	}
	
	if($end > $total_page){
		$end = $total_page;
	}
	?>
    <span><?php echo lang('page.total').':'.$total_page?></span>
    <ol class="pagerpro">
        <!--<a href="#">共条26</a>-->
		<?php if($total_page > 1 && $curpage > 0):?>
			<?php if($start > 1):?>
			<li>
				<a href="#" page="0" class="chn">
					<?php echo lang('page.first');?>
				</a>
			</li>
			<?php endif;?>
			<li>
				<a href="#" page="<?php echo ($curpage - 1)?>" class="chn">
					<?php echo lang('page.prev');?>
				</a>
			</li>
		<?php endif;?>
		
		<?php if($start < $end):?>
		<?php for($i = $start; $i <= $end; $i++):?>
			<li <?php if($i-1 == $curpage){echo 'class="current"';}?>>
				<a href="#" page="<?php echo ($i - 1);?>" <?php if($i-1 != $curpage){echo 'class="chn"';}?>><?php echo $i;?></a>
			</li>
		<?php endfor;?>
		<?php endif;?>
				
		<?php if($total_page > 1 && ($curpage < $total_page -1)):?>
			<li>
				<a href="#" page="<?php echo ($curpage + 1)?>" class="chn">
					<?php echo lang('page.next');?>
				</a>
			</li>
			<?php if($end < $total_page):?>
			<li>
				<a href="#" page="<?php echo ($total_page - 1)?>" class="chn">
					<?php echo lang('page.last');?>
				</a>
			</li>
			<?php endif;?>
		<?php endif;?>
    </ol>
</div>
<?php endif;?>