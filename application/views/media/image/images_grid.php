<?php if($total > 0):?>
<div class="video-panel" style="max-width:900px;">
	<ul>
		<?php foreach($data as $row):?>
		<li>
			<table width="180" border="0" cellspacing="0" cellpadding="0">
			  <?php if(false):?>
              <tr>
                <td align="right">
                	<?php if($auth > 0):?>
                	 <div class="operate">
                     <a href="/media/edit?id=<?php echo $row->id;?>&width=400&height=320" class="thickbox" title="<?php echo lang('edit.media');?>"><img id="edit_<?php echo $row->id;?>" src="/images/icons/24-edit.png" title="<?php echo lang('edit');?>" /></a>
					 <a href="javascript:void(0);" onclick="mediaLib.remove(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>');"><img id="del_<?php echo $row->id;?>" src="/images/icons/24-del.png"  title="<?php echo lang('delete');?>" /></a>
                     </div>
					 <?php endif;?>
                </td>
              </tr>
			  <?php endif;?>
              <tr>
                <td valign="top" class="content">
                	<div class="pic">
                		<?php if($row->preview_status > 0):?>
							<?php
							if($row->ext == 'bmp'):
							?>
							<a href="/media/preview?id=<?php echo $row->id;?>&width=800&height=500" class="thickbox" title="<?php echo lang('preview');?>">
                    			<img height="90" src="<?php echo $row->main_url;?>" />
							</a>
							<?php
							else:
							?>
							<a href="/media/preview?id=<?php echo $row->id;?>&width=800&height=500" class="thickbox" title="<?php echo lang('preview');?>">
                    			<img src="<?php echo $row->tiny_url;?>" />
							</a>
							<?php
							endif;
							?>
						<?php else:?>
						<?php
						if($row->source ==2 ) {
						?>
						<a href="/media/preview?id=<?php echo $row->id;?>&width=800&height=500" class="thickbox" title="<?php echo lang('preview');?>">
							<img style="display:block; margin:0 auto;" width="120" height="90" src="<?php echo $row->full_path;?>">
						</a>
						<?php
						}else {
						?>
						<img src="/images/media/video.gif" width="120" height="90" />
						<?php	
						}
						?>	
							
						<?php endif;?>
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
	$startIndex=($curpage>4)?$curpage-4:1;
	$endIndex= ($curpage<($totalPage-4)) ? ($curpage+4) : $totalPage;
?>
<?php if($totalPage > 1):?>
<div class="page-panel-center">
    <ul class="pagination">
    	
		<?php if($curpage>1):?>
			<li><a href="javascript:mediaLib.page(1);"><?php echo lang('page.first');?></a></li>
			<li><a href="javascript:mediaLib.page(<?php echo $curpage-1;?>);"><?php echo lang('page.prev');?></a></li>	
		<?php endif;?>
    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
	    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
	    	<?php if($i == $curpage):?>
				<?php echo $i;?>
			<?php else:?>
				<a href="javascript:mediaLib.page(<?php echo $i;?>);"><?php echo $i;?></a>
			<?php endif;?>
		</li>
		<?php endfor;?>
		<?php if($curpage<$totalPage):?>
			<li><a href="javascript:mediaLib.page(<?php echo $curpage+1;?>);"><?php echo lang('page.next');?></a></li>
			<li><a href="javascript:mediaLib.page(<?php echo $totalPage;?>);"><?php echo lang('page.last');?></a></li>	
		<?php endif;?>
	    
	    
  	</ul>
</div>
<?php endif;?>
<?php else:?>

<div style="color:#000000;">
	<?php if($media_filter_array['filter_type']==''):?>
	<?php echo lang('empty.media');?>
	<?php else:?>
	<?php echo lang("search.empty.result1")."\"".$media_filter_array['filter']."\"".lang("search.empty.result2"); ?>
	<?php endif;?>
</div>
<?php endif;?>

<!--
<div>
<span id="toolbar" class="ui-widget-header ui-corner-all">
	<button id="select">select</button>
	<button id="delete">delete</button>
	<button id="new">upload</button>
</span>
</div>
<div class="photo-list">
	<ul>
		<?php foreach($data as $row):?>
		<li>
			<span class="picture">
				<img src="http://s.xnimg.cn/a.gif" data-src="<?php echo $row->tiny_url;?>" style="background-image: url(<?php echo $row->tiny_url;?>);">
			</span>
			
		</li>
		<?php endforeach;?>
	</ul>
</div>
<script type="text/javascript">
	$(function(){
		media.initToolBar();
	})
</script>
-->