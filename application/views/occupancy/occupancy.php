<head>
<link rel="stylesheet" href="/static/css/alertify.core.css" />
<link rel="stylesheet" href="/static/css/alertify.default.css" />
<script src="/static/js/alertify.min.js" type="text/javascript" charset="utf-8"></script>
</head>

<div class="clear"></div>
<h1 class="tit-01">
	<?php echo lang('criteria');?>
	<div class="filter" style="width:70%;" >

		<div class="filter" style="padding-top: 0px; padding-right: 240px;">
		<?php echo lang('filter.by');?>:
		<div id="search" class="filter" style="padding-top: 10px; padding-right: 50px;">
			<input type="text" id="filter" style="width:150px;">
			<input type="hidden" class="input-medium" name="submit_json" id="submit_json">
		</div>
		<div class="filter" style="padding-top: 7px; padding-right: 10px;">
			<a href="javascript:void(0);" class="btn-go" onclick="criteria.refresh();"><label><?php echo lang('filter');?></label></a>
		</div>
	</div>
	
</h1>
<div id="layoutContent">
<?php
if(isset($body_view)){
	$this->load->view($body_view);
}
?>
</div>
<?php
	$totalPage = intval(($total + ($limit - 1)) / $limit);
	
	$startIndex=($curpage>3)?$curpage-3:1;
	$endIndex= ($curpage<($totalPage-3)) ? ($curpage+3) : $totalPage;
?>
<!--  
<div class="page-panel clearfix">
    <ul class="pagination">
    	<?php if($totalPage > 1):?>
			<?php if($curpage>1):?>
				<li><a href="/criteria/index/1/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.first');?></a></li>
				<li><a href="/criteria/index/<?php echo $curpage-1;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.prev');?></a></li>	
			<?php endif;?>
	    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if($i == $curpage):?>
					<?php echo $i;?>
				<?php else:?>
					<a href="/criteria/index/<?php echo $i;?>/<?php echo $order_item.'/'.$order;?>"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if($curpage<$totalPage):?>
				<li><a href="/criteria/index/<?php echo $curpage+1;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.next');?></a></li>
				<li><a href="/criteria/index/<?php echo $totalPage;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.last');?></a></li>	
			<?php endif;?>
	    <?php endif;?>
	    
  	</ul>
</div>
-->

<script>
	document.onkeyup = function(event){
		if(event.keyCode == 13){
			criteria.refresh();
		}
	};
	$(document).ready(function(){
	//gl.init();
});
</script>