<?php if($total > 0):?>
<div class="video-panel">
	<ul>
		<?php foreach($data as $row):?>
		<li>
			<table width="180" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="right">
                	 <div class="operate">
                     	<input type="radio" name="mid" class="mediaId" value="<?php echo $row->id;?>" />
                     </div>
                </td>
              </tr>
              <tr>
                <td valign="top" class="content">
                	<div class="pic">
                		<?php if($row->preview_status > 0):?>
							<?php
							if($row->ext == 'bmp'):
							?>
							<img mid="<?php echo $row->id;?>" height="90" src="<?php echo $row->main_url;?>" style="cursor:pointer;" onclick="interactionpls.chooseMedia(<?php echo $row->id;?>);"/>
							<?php
							else:
							?>
							<img mid="<?php echo $row->id;?>" src="<?php echo $row->tiny_url;?>" style="cursor:pointer;" onclick="interactionpls.chooseMedia(<?php echo $row->id;?>);"/>
							<?php
							endif;
							?>
						
						<?php else:?>
						<?php
							if($row->source ==2 ) {
						?>
							<img id="img_<?php echo $row->id;?>" src="<?php echo $row->full_path;?>" width="120" height="80" bigsrc="<?php echo $row->full_path;?>"style="cursor:pointer;" onclick="interactionpls.chooseMedia(<?php echo $row->id;?>);" />
						<?php
							}else {
						?>
							<img mid="<?php echo $row->id;?>" src="/images/media/video.gif" width="120" height="80" style="cursor:pointer;" onclick="interactionpls.chooseMedia(<?php echo $row->id;?>);"/>
						<?php	
							}
						?>	
						<?php endif;?>
                    </div>
                    <div class="name">
                    	<?php if(strlen($row->name) > 18){echo substr($row->name, 0, 18).'..';}else{echo $row->name;}?>
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
	if($curpage - 4 > $startIndex) {
		$startIndex = $curpage - 4;
	}
	if($curpage + 4 < $endIndex) {
		$endIndex = $curpage + 4;
	}
?>
<?php if($totalPage > 1):?>
<div class="page-panel-center">
    <ul class="pagination">
		<?php if($curpage>1):?>
			<li><a href="javascript:void(0);" onclick="interactionpls.addShowMediaFilter(<?php echo $playlist_id.','.$area_id.',\''.$bmp.'\','.$media_type.',1';?>);" ><?php echo lang('page.first');?></a></li>
			<li><a href="javascript:void(0);" onclick="interactionpls.addShowMediaFilter(<?php echo $playlist_id.','.$area_id.',\''.$bmp.'\','.$media_type.','.($curpage - 1);?>);" ><?php echo lang('page.prev');?></a></li>	
		<?php endif;?>
    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
	    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
	    	<?php if($i == $curpage):?>
				<?php echo $i;?>
			<?php else:?>
				<a href="javascript:void(0);" onclick="interactionpls.addShowMediaFilter(<?php echo $playlist_id.','.$area_id.',\''.$bmp.'\','.$media_type.','.$i;?>);"><?php echo $i;?></a>
			<?php endif;?>
		</li>
		<?php endfor;?>
		<?php if($curpage<$totalPage):?>
			<li><a href="javascript:void(0);" onclick="interactionpls.addShowMediaFilter(<?php echo $playlist_id.','.$area_id.',\''.$bmp.'\','.$media_type.','.($curpage + 1);?>);"><?php echo lang('page.next');?></a></li>
			<li><a href="javascript:void(0);" onclick="interactionpls.addShowMediaFilter(<?php echo $playlist_id.','.$area_id.',\''.$bmp.'\','.$media_type.','.$totalPage;?>);" ><?php echo lang('page.last');?></a></li>	
		<?php endif;?>
  	</ul>
</div>
<?php endif;?>
<br/>
<br/>
<p class="btn-center">
	<a class="btn-01" href="javascript:void(0);" onclick="interactionpls.saveShowMedia(<?php echo $playlist_id;?>,<?php echo $area_id;?>,'<?php echo lang('warn.choose.empty.tip');?>',true);"><span><?php echo lang('button.ok');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>
<?php else:?>
<div style="color:#c0c0c0;">
	<b><?php echo lang('empty');?></b>
</div>
<?php endif;?>