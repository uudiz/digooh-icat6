<div id="validateTips" >
	<div>
		<div id="formMsgContent">
		</div>
	</div>
</div>
<div class="clear"></div>
<h1 class="tit-01"><?php echo lang('webpage');?><span></span></h1>
<table class="table-list"  width="100%" >
	<tr>
		<th width="50" >
        	<input type="checkbox" name="checkAll" id="topCheckAll" onclick="campaign.checkAllMedia(this);">
		</th>
       	<th width="200" ><?php echo lang('name');?></th>
		<th width="200"><?php echo lang('desc');?></th>
		<th width="200"><?php echo lang('url');?></th>
		<th width="100"><?php echo lang('update.time');?></th>
	</tr>
	<?php if(empty($data)): ?>
	<tr>
		<td colspan="5">
			<?php echo lang("empty");?>
		</td>
	</tr>
	<?php else:
		$index = 0;
	?>
	<?php foreach($data as $row):?>
	<tr <?php if($index%2 != 0):?>class="even" onmouseout="this.className='even'" <?php else:?>onmouseout="this.className=''"<?php endif;?>  onmouseover="this.className='onSelected'">
		<td><input type="checkbox" name="mid" value="<?php echo $row->id;?>" /></td>
		<td><?php echo $row->name;?></td>
		<td><?php echo $row->descr; ?></td>
		<td>
			<?php
				if($row->type) {
					if(mb_strlen($row->url) > 60){echo mb_substr($row->url, 0, 60).'..';}else{echo $row->url;}
				}else {
					echo $row->url;
				}
			?>
		</td>
		<td><?php echo $row->add_time; ?></td>
	</tr>
	<?php
		$index++; 
		endforeach; 
	?>
	<?php endif;?>
</table>
<input type="hidden" id="areaId" value="<?php echo $area_id;?>"/>
<input type="hidden" id="mediaType" value="<?php echo $media_type;?>"/>
<p class="btn-center">
	<a class="btn-01" href="javascript:void(0);" onclick="playlist.saveAreaMedia(<?php echo $playlist_id;?>,<?php echo $area_id;?>,'<?php echo lang('warn.choose.empty.tip');?>',true, false);"><span><?php echo lang('button.ok');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>