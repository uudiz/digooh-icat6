
<?php if ($total > 0):?>
<div class="video-panel" style="max-width:900px;">
	<ul>
		<?php foreach ($data as $row):?>
		<li>
			<table width="180" border="0" cellspacing="0" cellpadding="0">
			  <?php if (false):?>
              <tr>
                <td align="right">
                	<?php if ($auth > 0):?>
                	 <div class="operate">
                     <a href="/media/edit?id=<?php echo $row->id;?>&width=600&height=320" class="thickbox" title="<?php echo lang('edit.media');?>"><img id="edit_<?php echo $row->id;?>" src="/images/icons/24-edit.png" title="<?php echo lang('edit');?>" /></a>
					 <a href="javascript:void(0);" onclick="mediaLib.remove(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>');"><img id="del_<?php echo $row->id;?>" src="/images/icons/24-del.png" title="<?php echo lang('delete');?>" /></a>
                     </div>
					 <?php endif;?>
                </td>
              </tr>
			  <?php endif;?>
              <tr>
                <td valign="top" class="content">
					<!--
                	<?php if ($row->source == 0): /*Local support preview*/?>
					<a href="/media/preview?id=<?php echo $row->id;?>&action=play&width=800&height=400" class="thickbox" title="<?php echo lang('preview');?>">
                	<div class="play">
                		<img src="/images/icons/play.png" width="32" height="32" />
					</div>
					</a>
					<?php endif;?>
					-->
                	<div class="pic">
                    	<?php if ($row->preview_status > 0):?>
							<a href="/media/preview?id=<?php echo $row->id;?>&action=play&width=800&height=400" class="thickbox" title="<?php echo lang('preview');?>">
                    			<img src="<?php echo $row->tiny_url;?>" />
							</a>
						<?php else:?>
							<img src="/images/media/video-no.gif" width="120" height="80" />
						<?php endif;?>
                    </div>
                    <div class="name">
                    	<?php if (mb_strlen($row->name) > 16) {
    echo mb_substr($row->name, 0, 16).'..';
} else {
    echo $row->name;
}?>
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
<?php if ($totalPage > 1):?>
<div class="page-panel-center">
    <ul class="pagination">
    	
		<?php if ($curpage>1):?>
			<li><a href="javascript:mediaLib.page(1);"><?php echo lang('page.first');?></a></li>
			<li><a href="javascript:mediaLib.page(<?php echo $curpage-1;?>);"><?php echo lang('page.prev');?></a></li>	
		<?php endif;?>
    	<?php for ($i = $startIndex; $i <= $endIndex; $i++):?>
	    <li <?php if ($i == $curpage):?>class="active"<?php endif;?>>
	    	<?php if ($i == $curpage):?>
				<?php echo $i;?>
			<?php else:?>
				<a href="javascript:mediaLib.page(<?php echo $i;?>);"><?php echo $i;?></a>
			<?php endif;?>
		</li>
		<?php endfor;?>
		<?php if ($curpage<$totalPage):?>
			<li><a href="javascript:mediaLib.page(<?php echo $curpage+1;?>);"><?php echo lang('page.next');?></a></li>
			<li><a href="javascript:mediaLib.page(<?php echo $totalPage;?>);"><?php echo lang('page.last');?></a></li>	
		<?php endif;?>
	    
	    
  	</ul>
</div>
<?php endif;?>

<div id="preview">
	<div id="movie" style="width:480px;height:320px;">
		
	</div>
</div>

<?php else:?>
<div style="color:#000000;">

	<?php echo lang('empty.media');?>

</div>
<?php endif;?>