<?php if($this->config->item('with_sub_folders')):?>	
<link href="/static/css/jquery/select2.min.css" rel="stylesheet" type="text/css" />
<link href="/static/css/jquery/select2totree.css" rel="stylesheet" type="text/css" />
<script src="/static/js/jquery/select2.min.js" type="text/javascript"></script>
<script src="/static/js/jquery/select2totree.js" type="text/javascript"></script>
<?php endif?>

<h2 class="tit-01"><?php echo lang('image');?>
	<div class="filter" >
		<label style="margin-left:20px;"><?php echo lang('filter.folder');?>:</label>
		<select id="filterFolder" name="filterFolder" style="width: 120px; margin:0px 2px;" onchange="weather.screen.imageFilter('<?php echo $type;?>');">
			<option value="-1" ><?php echo lang('all');?></option>
			<option value="0" ><?php echo lang('folder.default');?></option>
			<?php if(!$this->config->item('with_sub_folders')):?>	
			<?php if(isset($folders)):?>
				<?php foreach($folders as $f):?>
					<option value="<?php echo $f->id;?>" <?php if($f->id == $folder_id):?>selected="selected"<?php endif;?>><?php echo $f->name;?></option>
				<?php endforeach;?>
			<?php endif;?>
			<?php endif;?>
		</select>
		<input type="hidden" id="folderId" value="<?php echo $folder_id;?>" />
		<a href="javascript:void(0);" class="btn-go" style="margin-left:10px;" onclick="weather.screen.imageFilter('<?php echo $type;?>');"><label><?php echo lang('filter');?></label></a>
	</div>
    <span></span>
</h2>

<div id="imageContent">
	<?php
	$this->load->view($tables);
	?>
</div>
<?php if($this->config->item('with_sub_folders')):?>	
<script type="text/javascript">
	
	$(document).ready(function(){
		var mydata = <?php echo json_encode($folders)?>;
		$("#filterFolder").select2ToTree({treeData: {dataArr: mydata}/*, maximumSelectionLength: 3*/});		

	});
</script>
<?php endif?>	