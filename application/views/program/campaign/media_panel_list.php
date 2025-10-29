<table class="table-list"  width="100%" >
        <tr>
        	<th width="30" ><input type="checkbox" name="checkAll" id="topCheckAll" onclick="campaign.checkAllMedia(this);"></th>
            <th >
            	<a href="javascript:void(0);" onclick="campaign.addAreaMediaFilter(<?php echo $playlist_id.','.$area_id.',\''.$bmp.'\','.$media_type.','.$curpage.','.$type;?>,'name','<?php if($order_item == 'name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('name');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'name' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			
			<th width="100"  >
				<a href="javascript:void(0);" onclick="campaign.addAreaMediaFilter(<?php echo $playlist_id.','.$area_id.',\''.$bmp.'\','.$media_type.','.$curpage.','.$type;?>,'tag_name','<?php if($order_item == 'tag_name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('tag');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'tag_name' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'tag_name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
					
			
            <th width="100">
            	<a href="javascript:void(0);" onclick="campaign.addAreaMediaFilter(<?php echo $playlist_id.','.$area_id.',\''.$bmp.'\','.$media_type.','.$curpage.','.$type;?>,'folder_id','<?php if($order_item == 'folder_id' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('folder');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'folder_id' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'folder_id' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
		
			<th width="60">            	
				<a href="javascript:void(0);" onclick="campaign.addAreaMediaFilter(<?php echo $playlist_id.','.$area_id.',\''.$bmp.'\','.$media_type.','.$curpage.','.$type;?>,'file_size','<?php if($order_item == 'file_size' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('file.size');?></a>				
				<img alt="" src="/images/icons/<?php if($order_item == 'file_size' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'file_size' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />			
			</th>
			<th >
            	<a href="javascript:void(0);" onclick="campaign.addAreaMediaFilter(<?php echo $playlist_id.','.$area_id.',\''.$bmp.'\','.$media_type.','.$curpage.','.$type;?>,'descr','<?php if($order_item == 'descr' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('desc');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'descr' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'descr' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<th width="60">
				<a href="javascript:void(0);" onclick="campaign.addAreaMediaFilter(<?php echo $playlist_id.','.$area_id.',\''.$bmp.'\','.$media_type.','.$curpage.','.$type;?>,'play_time','<?php if($order_item == 'play_time' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('playtime');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'play_time' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'play_time' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
            <th width="150">
            	<a href="javascript:void(0);" onclick="campaign.addAreaMediaFilter(<?php echo $playlist_id.','.$area_id.',\''.$bmp.'\','.$media_type.','.$curpage.','.$type;?>,'end_date','<?php if($order_item == 'end_date' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('date.range');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'end_date' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'end_date' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
            </th>
            <!-- 
             <th width="150">
            	<a href="javascript:void(0);" onclick="campaign.addAreaMediaFilter(<?php echo $playlist_id.','.$area_id.',\''.$bmp.'\','.$media_type.','.$curpage.','.$type;?>,'end_time','<?php if($order_item == 'end_time' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('time.range');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'end_time' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'end_time' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
            </th>
             -->
        </tr>
		
		<?php if($total == 0): ?>
		<tr>
			<td colspan="7">
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
			  
			  <td>
			 	<?php
						echo $row->tag_name;
				?>
			 </td>
			 	
			  <td>
			  	<?php echo $row->folder_name; ?>
			  </td>
		  
			  <td>				
			  <?php				
				$file_size = $row->file_size;				
				if($file_size > 1024){					
					$file_size /= 1024;					
					if($file_size > 1024){						
						echo sprintf('%.2f MB',$file_size/1024);					
					}else{						
						echo sprintf('%.2f KB', $file_size);					
					}				
				}else{					
					echo $file_size.' byte';				
				}				
			?>			  
			</td>
			  <td><?php echo $row->descr; ?></td>
			  <td>
			  	<?php if($row->play_time) {
							if($row->play_time>59)
							 	$times = sprintf("%02d:%02d", ($row->play_time/60),($row->play_time%60));
						 	else
						 	  $times = sprintf("00:%02d", $row->play_time);		  			
			  			echo $times; 
			  		}
			  	?>
			  </td>
			  
			  <td>
		   		<?php if($row->date_flag) {
			  		if(date('Y-m-d') > $row->end_date)
			  			echo '<font style="color: red;">'.$row->start_date." ~ ".$row->end_date."</font>"; 
			  		else 
			  			echo $row->start_date." ~ ".$row->end_date;
			  	}
			  	?>
				</td>	
				<!-- 
				 <td>
				 	<?php if(!$row->all_day_flag) echo $row->start_time." - ".$row->end_time ?>
				</td>
				 -->
			</tr>
			<?php
			 	$index++; 
				endforeach;
			?>

		<?php endif;?>

</table>
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
<div class="page-panel clearfix">
    <ul class="pagination">
    	<?php if($totalPage > 1):?>
			<?php if($curpage>1):?>
				<li><a href="javascript:campaign.addAreaMediaFilter(<?php echo $playlist_id.','.$area_id.',\''.$bmp.'\','.$media_type.',1'.',1';?>);"><?php echo lang('page.first');?></a></li>
				<li><a href="javascript:campaign.addAreaMediaFilter(<?php echo $playlist_id.','.$area_id.',\''.$bmp.'\','.$media_type.','.($curpage - 1).',1';?>);"><?php echo lang('page.prev');?></a></li>	
			<?php endif;?>
	    	<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if($i == $curpage):?>
					<?php echo $i;?>
				<?php else:?>
					<a href="javascript:campaign.addAreaMediaFilter(<?php echo $playlist_id.','.$area_id.',\''.$bmp.'\','.$media_type.','.$i.',1';?>);"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if($curpage<$totalPage):?>
				<li><a href="javascript:campaign.addAreaMediaFilter(<?php echo $playlist_id.','.$area_id.',\''.$bmp.'\','.$media_type.','.($curpage + 1).',1';?>);"><?php echo lang('page.next');?></a></li>
				<li><a href="javascript:campaign.addAreaMediaFilter(<?php echo $playlist_id.','.$area_id.',\''.$bmp.'\','.$media_type.','.$totalPage.',1';?>)"><?php echo lang('page.last');?></a></li>	
			<?php endif;?>
	    <?php endif;?>
	    
  	</ul>
</div>
<p class="btn-center">
	<?php if(false):?>
	<a class="btn-01" href="javascript:void(0);" onclick="campaign.saveAreaMedia(<?php echo $playlist_id;?>,<?php echo $area_id;?>,'<?php echo lang('warn.choose.empty.tip');?>',false);"><span><?php echo lang('button.save');?></span></a>
	<?php endif;?>
	<a class="btn-01" href="javascript:void(0);" onclick="campaign.saveAreaMedia(<?php echo $playlist_id;?>,<?php echo $area_id;?>,'<?php echo lang('warn.choose.empty.tip');?>',true);"><span><?php echo lang('button.ok');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>