<link rel="stylesheet" href="/static/css/alertify.core.css" />
<link rel="stylesheet" href="/static/css/alertify.default.css" />
<link rel="stylesheet" href="/static/css/jquery/chosen.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
<script src='/static/js/jquery/chosen.jquery.min.js'></script>
<script src="/static/js/alertify.min.js" type="text/javascript" charset="utf-8"></script>

<?php if ($auth >= $ADMIN):?>
<div class="add-panel">
	<a href=''  onclick="document.getElementById('import_excel').click();return false;">
		<img src = '/images/icons/import.gif'/><?php echo lang('import');?>
	</a>

	<a href="/tag/add?width=450&height=300" id="create" class="thickbox" title="<?php echo lang('create.tag');?>"><?php echo lang('create');?></a>


	<input type=file id=import_excel style="display:none" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">

</div>
<?php endif;?>
<div class="clear"></div>
<h1 class="tit-01">
	<?php echo lang('tag');?>
	<div class="filter" style="width:70%;" >

		<div class="filter" style="padding-top: 0px; padding-right: 240px;">
		<?php echo lang('filter.by');?>:
		<div id="search" class="filter" style="padding-top: 10px; padding-right: 50px;">
			<input type="text" id="filter" style="width:150px;">
			<input type="hidden" class="input-medium" name="submit_json" id="submit_json">
		</div>
		<div class="filter" style="padding-top: 7px; padding-right: 10px;">
			<a href="javascript:void(0);" class="btn-go" onclick="tag.refresh();"><label><?php echo lang('filter');?></label></a>
		</div>
	</div>
	
</h1>
<div id="layoutContent">
<?php
if (isset($body_view)) {
    $this->load->view($body_view);
}
?>
</div>
<?php
    $totalPage = intval(($total + ($limit - 1)) / $limit);
    $startIndex = 1;
    $endIndex   = $totalPage;
    $midIndex   = intval(($curpage + 5) / 2);
    if ($midIndex - 2 > $startIndex) {
        $startIndex = $midIndex - 2;
    }
    if ($midIndex + 2 < $endIndex) {
        $endIndex = $midIndex + 2;
    }
?>
<div class="page-panel clearfix">
    <ul class="pagination">
    	<?php if ($totalPage > 1):?>
			<?php if ($curpage>1):?>
				<li><a href="/tag/index/1/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.first');?></a></li>
				<li><a href="/tag/index/<?php echo $curpage-1;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.prev');?></a></li>	
			<?php endif;?>
	    	<?php for ($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if ($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if ($i == $curpage):?>
					<?php echo $i;?>
				<?php else:?>
					<a href="/tag/index/<?php echo $i;?>/<?php echo $order_item.'/'.$order;?>"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if ($curpage<$totalPage):?>
				<li><a href="/tag/index/<?php echo $curpage+1;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.next');?></a></li>
				<li><a href="/tag/index/<?php echo $totalPage;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.last');?></a></li>	
			<?php endif;?>
	    <?php endif;?>
	    
  	</ul>
</div>

<script>
	document.onkeyup = function(event){
		if(event.keyCode == 13){
			tag.refresh();
		}
	};
	document.querySelector("#import_excel").addEventListener("change",function () {
        //获取到选中的文件
		var input = document.querySelector("#import_excel");
        var file = input.files[0];
  
		if(!file){
			return;
		}
        var formdata = new FormData();		
        formdata.append("file",file);

		input.value='';
		$.ajax({ 
			url:"/tag/do_upload", 
			type:"post", 
			processData: false,   
        	contentType: false,   
			data:formdata, 
			dataType: 'json',

			success:function(res){ 
				if(res.code==0){
					tag.page(1);
				}
				alertify.alert(res.msg);
			}, 
			error:function(err){ 
				alert("failed:",err); 
			} 
		
		}) 
    });
</script>