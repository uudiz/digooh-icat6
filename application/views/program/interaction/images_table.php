<h2 class="tit-01"><?php echo lang('image');?>
	<div class="filter" >
		<label style="margin-left:20px;"><?php echo lang('filter.folder');?>:</label>
		<select id="filterFolder" name="filterFolder" style="width: 120px; margin:0px 2px;">
			<option value="-1" ><?php echo lang('all');?></option>
			<option value="0" ><?php echo lang('folder.default');?></option>
			<?php if(isset($folders)):?>
				<?php foreach($folders as $f):?>
					<option value="<?php echo $f->id;?>" <?php if($f->id == $folder_id):?>selected="selected"<?php endif;?>><?php echo $f->name;?></option>
				<?php endforeach;?>
			<?php endif;?>
		</select>
		<input type="hidden" id="folderId" value="<?php echo $folder_id;?>" />
		<a href="javascript:void(0);" class="btn-go" style="margin-left:10px;" onclick="interaction.screen.imageFilter('<?php echo $type;?>');"><label><?php echo lang('filter');?></label></a>
	</div>
    <span></span>
</h2>
<?php if($total > 0):?>
<div class="video-panel">
	<ul>
		<?php foreach($data as $row):?>
		<li>
			<table width="180" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="top" class="content">
                	<div class="pic">
                		<a href="javascript:void(0);" onclick="interaction.screen.setImage(<?php echo $row->id.',\''.$type.'\',\''.$screenID.'\'';?>)" >
	                		<?php if($row->preview_status > 0):?>
	                    		<img id="img_<?php echo $row->id;?>" src="<?php echo $row->tiny_url;?>" bigsrc="<?php echo $row->main_url; ?>" width="120" height="80" />
							<?php else:?>
								<img id="img_<?php echo $row->id;?>" src="/images/media/video.gif" bigsrc="/images/media/video.gif" width="120" height="80" />
							<?php endif;?>
						</a>
                    </div>
                    <div class="name">
                    	<?php if(mb_strlen($row->name) > 16){echo mb_substr($row->name, 0, 16).'..';}else{echo $row->name;}?>
                    </div>
                </td>
              </tr>
            </table>
		</li>
		<?php endforeach;?>
	</ul>
</div>
<div class="clear"></div>
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
<?php if($totalPage > 1):?>
<div class="page-panel-center">
    <ul class="pagination">
    	
		<?php if($curpage>1):?>
			<li><a href="javascript:void(0);" onclick="interaction.screen.imagePage(1,'<?php echo $type;?>',<?php echo $screenID;?>);" ><?php echo lang('page.first');?></a></li>
			<li><a href="javascript:void(0);" onclick="interaction.screen.imagePage(<?php echo $curpage-1;?>,'<?php echo $type;?>',<?php echo $screenID;?>);"><?php echo lang('page.prev');?></a></li>	
		<?php endif;?>
    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
	    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
	    	<?php if($i == $curpage):?>
				<?php echo $i;?>
			<?php else:?>
				<a href="javascript:void(0);" onclick="interaction.screen.imagePage(<?php echo $i;?>,'<?php echo $type;?>',<?php echo $screenID;?>);"><?php echo $i;?></a>
			<?php endif;?>
		</li>
		<?php endfor;?>
		<?php if($curpage<$totalPage):?>
			<li><a href="javascript:void(0);" onclick="interaction.screen.imagePage(<?php echo $curpage+1;?>,'<?php echo $type;?>',<?php echo $screenID;?>);"><?php echo lang('page.next');?></a></li>
			<li><a href="javascript:void(0);" onclick="interaction.screen.imagePage(<?php echo $totalPage;?>,'<?php echo $type;?>',<?php echo $screenID;?>);"><?php echo lang('page.last');?></a></li>	
		<?php endif;?>
  	</ul>
</div>
<?php endif;?>
<?php else:?>
<div class="empty">
	<b><?php echo lang('empty.media');?></b>
</div>
<?php endif;?>
