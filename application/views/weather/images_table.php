
<?php if($total > 0):?>
<div class="video-panel">
	<ul>
		<?php foreach($data as $row):?>
		<li>
			<table width="180" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="top" class="content">
                	<div class="pic">
                		<a href="javascript:void(0);" onclick="weather.screen.setImage(<?php echo $row->id.',\''.$type.'\'';?>)" >
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
			<li><a href="javascript:void(0);" onclick="weather.screen.imagePage(1,'<?php echo $type;?>');" ><?php echo lang('page.first');?></a></li>
			<li><a href="javascript:void(0);" onclick="weather.screen.imagePage(<?php echo $curpage-1;?>,'<?php echo $type;?>');"><?php echo lang('page.prev');?></a></li>	
		<?php endif;?>
    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
	    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
	    	<?php if($i == $curpage):?>
				<?php echo $i;?>
			<?php else:?>
				<a href="javascript:void(0);" onclick="weather.screen.imagePage(<?php echo $i;?>,'<?php echo $type;?>');"><?php echo $i;?></a>
			<?php endif;?>
		</li>
		<?php endfor;?>
		<?php if($curpage<$totalPage):?>
			<li><a href="javascript:void(0);" onclick="weather.screen.imagePage(<?php echo $curpage+1;?>,'<?php echo $type;?>');"><?php echo lang('page.next');?></a></li>
			<li><a href="javascript:void(0);" onclick="weather.screen.imagePage(<?php echo $totalPage;?>,'<?php echo $type;?>');"><?php echo lang('page.last');?></a></li>	
		<?php endif;?>
  	</ul>
</div>
<?php endif;?>
<?php else:?>
<div class="empty">
	<b><?php echo lang('empty.media');?></b>
</div>
<?php endif;?>
