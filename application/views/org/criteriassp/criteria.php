<head>
<link rel="stylesheet" href="/static/css/alertify.core.css" />
<link rel="stylesheet" href="/static/css/alertify.default.css" />
<script src="/static/js/alertify.min.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="/static/css/jquery/chosen.min.css" />
<link rel="stylesheet" type="text/css" href="/static/css/jquery/jquery.fancybox.min.css">
<script src="/static/js/jquery/jquery.fancybox.min.js"></script>
<script src='/static/js/jquery/chosen.jquery.min.js'></script>
<style>
.fancybox-overlay {
    position: fixed;
	top:0;
	left:0;
    bottom: 0;
    right: 0;
},
</style>

</head>
<?php if ($auth >= $ADMIN):?>
<div class="add-panel">
	<a href=''  onclick="document.getElementById('import_excel').click();return false;">
		<img src = '/images/icons/import.gif'/><?php echo lang('import');?>
	</a>
	<a href="/criteriaSSP/add?width=500&height=300" id="create" class="thickbox" title="<?php echo lang('create.criteria');?>"><?php echo lang('create');?></a>
	<!--
	<input type=file id=import_excel style="display:none" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
-->

</div>
<?php endif;?>
<div class="clear"></div>
<h1 class="tit-01">
	<?php echo lang('ssp.criteria');?>
	<div class="filter" style="width:70%;" >

		<div class="filter" style="padding-top: 0px; padding-right: 240px;">
		<?php echo lang('filter.by');?>:
		<div id="search" class="filter" style="padding-top: 10px; padding-right: 50px;">
			<input type="text" id="filter" style="width:150px;">
			<input type="hidden" class="input-medium" name="submit_json" id="submit_json">
		</div>

		<select  data-placeholder="" id="filter_categories_type" onchange="criteria.refresh();" >
				<option value="-1" selected><?php echo lang('all');?></option>
						<option value="0" >dmi</option>
						<option value="1" >dpaa</option>
						<option value="2" >ilab</option>
						<option value="3" >openooh</option>
		</select>   
					
		<div class="filter" style="padding-top: 7px; padding-right: 10px;">
			<a href="javascript:void(0);" class="btn-go" onclick="criteria.refresh();"><label><?php echo lang('filter');?></label></a>
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
    
    $startIndex=($curpage>3)?$curpage-3:1;
    $endIndex= ($curpage<($totalPage-3)) ? ($curpage+3) : $totalPage;
?>


<script>
	document.onkeyup = function(event){
		if(event.keyCode == 13){
			criteria.refresh();
		}
	};

</script>