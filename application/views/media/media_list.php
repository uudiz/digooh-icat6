
<table class="table-list"  width="100%" border="0">
        <tr>
        	<th width="30" ><input type="checkbox" name="checkAll" id="topCheckAll" onclick="mediaLib.checkAll(this);"></th>
            <th  >
				<a href="javascript:void(0);" onclick="mediaLib.page(<?php echo $curpage;?>,'name','<?php if ($order_item == 'name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('name');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'name' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			
			<th width="100"  >
				<a href="javascript:void(0);" onclick="mediaLib.page(<?php echo $curpage;?>,'tag_name','<?php if ($order_item == 'tag_name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('tag');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'tag_name' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'tag_name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			
			<th width="80" >
				<a href="javascript:void(0);" onclick="mediaLib.page(<?php echo $curpage;?>,'folder_id','<?php if ($order_item == 'folder_id' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('folder');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'folder_id' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'folder_id' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			
			
     		 <th >
            	<a href="javascript:void(0);" onclick="mediaLib.page(<?php echo $curpage;?>,'source','<?php if ($order_item == 'source' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('source');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'source' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'source' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			
		
			
			<th width="80">
        <a href="javascript:void(0);" onclick="mediaLib.page(<?php echo $curpage;?>,'file_size','<?php if ($order_item == 'file_size' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('file.size');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'file_size' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'file_size' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
      <th width="100">
            	<a href="javascript:void(0);" onclick="mediaLib.page(<?php echo $curpage;?>,'descr','<?php if ($order_item == 'descr' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('desc');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'descr' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'descr' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			
			
			<th width="80">
        <a href="javascript:void(0);" onclick="mediaLib.page(<?php echo $curpage;?>,'play_time','<?php if ($order_item == 'play_time' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('playtime');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'play_time' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'play_time' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			
			<th width="80">
				<a href="javascript:void(0);" onclick="mediaLib.page(<?php echo $curpage;?>,'end_date','<?php if ($order_item == 'end_date' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('date.range');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'end_date' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'end_date' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			

			
			<th width="80"><?php echo lang('operate');?></th>
        </tr>
		
		<?php if ($total == 0): ?>
		<tr>
			<td colspan="8">
			<?php echo lang('empty.media');?>
			</td>
		</tr>
		<?php else:
          $index = 0;
        ?>
			<?php foreach ($data as $row):?>
			<tr <?php if ($index%2 != 0):?>class="even" onmouseout="this.className='even'" <?php else:?>onmouseout="this.className=''"<?php endif;?>  onmouseover="this.className='onSelected'">
			  <td><input type="checkbox" name="id" value="<?php echo $row->id;?>" /></td>
			  <td>
			  	<?php if (mb_strlen($row->name) > 128) {
            echo mb_substr($row->name, 0, 128).'..';
        } else {
            echo $row->name;
        }?>
			  </td>
 
			 	<td>
			 	<?php
                        echo $row->tag_name;
                ?>
			 	</td>
			  <td>
			  	<!--<?php if ($row->folder_id > 0) {
                    echo $row->folder_name;
                } else {
                    echo lang('folder.default');
                }?> -->
			  	 <?php echo $row->folder_name;?>
			  </td>
			  
			  <td>
			  	<?php
                    switch ($row->source) {
                            case 0:
                                echo lang('local');
                                break;
                            case 1:
                                echo lang('ftp');
                                break;
                            case 2:
                                echo lang('http');
                                break;
                        }
                ?>
			  </td>
			  	
			  <td>
			  	<?php
                    $this->load->helper("number");
                    echo byte_format($row->file_size, 2);
        
                ?>
			  </td>
			  <td>
			  	<?php if (mb_strlen($row->descr) > 32) {
                    echo mb_substr($row->descr, 0, 32).'...';
                } else {
                    echo $row->descr;
                }?>
			  </td>

			  
			  <td>
			  	<?php if ($row->play_time) {
                    if ($row->play_time>59) {
                        $times = sprintf("%02d:%02d", ($row->play_time/60), ($row->play_time%60));
                    } else {
                        $times = sprintf("00:%02d", $row->play_time);
                    }
                    echo $times;
                }
                ?>
			  </td>

			  <td>
			  	<?php if ($row->date_flag) {
                    if (date('Y-m-d') > $row->end_date) {
                        echo '<font style="color: red;">'.$row->start_date." ~ ".$row->end_date."</font>";
                    } else {
                        echo $row->start_date." ~ ".$row->end_date;
                    }
                }
                
                ?>
				</td>	

			  <td>
			  	
			  	
			  	<?php if ($auth != 1):?>
				<?php
                    $this->load->model('material');
                    $num = $this->material->get_pb_flag($row->id);
                    $warnning = '';
                    if ($num->num > 0) {
                        $warnning = '\n\nWarning:\nThe file has been used in the working playlist. If deleted, it will be removed from playlist as well.';
                    }
                ?>
			  		<a href="/media/edit?id=<?php echo $row->id;?>&amp;width=450&amp;height=480" class="thickbox" ><img id="edit_<?php echo $row->id;?>" src="/images/icons/24-edit.png" title="<?php echo lang('edit.media');?>" /></a>
			  		<a href="javascript:void(0);" onclick="mediaLib.remove(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item').$warnning;?>');" ><img id="del_<?php echo $row->id;?>" src="/images/icons/24-del.png" title="<?php echo lang('delete');?>" /></a>
				<?php else:?>
					&nbsp;
				<?php endif;?>
			  </td>
			</tr>
			<?php
                $index++;
                endforeach;
            ?>
			<?php if ($auth > 1):?>
			<tr <?php if ($index%2 != 0):?>class="even"<?php endif;?>>
			  <td><input type="checkbox" name="checkAll" id="downCheckAll" onclick="mediaLib.checkAll(this)"/></td>
			  <td></td>
			  <td></td>
			  <td width="100px">
			  	 <p>
			  		<a href="javascript:void(0);" class="button"  onclick="mediaLib.moveTo('<?php echo lang('tip.empty.media');?>','<?php echo lang('tip.empty.folder');?>',1);"><?php echo lang('move.to');?></a>
				</p>
			  	
			  </td>
			  <td colspan="2">
			  	<?php echo lang('folder')?>:
				<select id="folderId" name="folderId" style="width: 150px; margin:0px 4px;">
					<?php if (isset($root)):?>
					<option value="<?php echo $root->id;?>"><?php echo $root->name;?></option>
					<?php else:?>
					<option value="0" ><?php echo lang('folder.default');?></option>
					<?php endif ?>
					<?php if (!$this->config->item('with_sub_folders')):?>
					<?php if (isset($folders)):?>
						<?php foreach ($folders as $f):?>
							<option value="<?php echo $f->id;?>"><?php echo $f->name;?></option>
						<?php endforeach;?>
					<?php endif;?>
					<?php endif;?>	
				</select>

			  </td>
				<td>	</td>
				<td>	</td>

			  <td style="text-align:left">
			  	<?php if ($auth != 1):?>	  	 
			  		<a href="javascript:void(0);" onclick="mediaLib.editProperty('<?php echo lang('tip.remove.empty.item');?>','<?php echo lang('edit.media');?>');"><img id="edit_0" src="/images/icons/24-edit.png" title="<?php echo lang('edit.media');?>" /></a>
			  		<a href="javascript:void(0);" onclick="mediaLib.removeAll('<?php echo lang('tip.remove.empty.item');?>','<?php echo lang('tip.remove.choose.item');?>');"><img id="del_0" src="/images/icons/24-del.png" title="<?php echo lang('delete');?>" /></a>
				<?php else:?>
					&nbsp;
				<?php endif;?>
			  </td>
			</tr>
			<?php endif;?>
		<?php endif;?>

</table>
<?php
    $totalPage = intval(($total + ($limit - 1)) / $limit);
    $startIndex=($curpage>4)?$curpage-4:1;
    $endIndex= ($curpage<($totalPage-4)) ? ($curpage+4) : $totalPage;
?>

<div class="page-panel clearfix">
    <ul class="pagination">
    	<?php if ($totalPage > 1):?>
			<?php if ($curpage>1):?>
				<li><a href="javascript:mediaLib.page(1,<?php echo "'".$order_item."','".$order."'";?>);"><?php echo lang('page.first');?></a></li>
				<li><a href="javascript:mediaLib.page(<?php echo $curpage-1;?>,<?php echo "'".$order_item."','".$order."'";?>);"><?php echo lang('page.prev');?></a></li>	
			<?php endif;?>
	    	<?php for ($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if ($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if ($i == $curpage):?>
		    		<?php echo $i;?>
		    	<?php else:?>
					<a href="javascript:mediaLib.page(<?php echo $i;?>,<?php echo "'".$order_item."','".$order."'";?>);"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if ($curpage<$totalPage):?>
				<li><a href="javascript:mediaLib.page(<?php echo $curpage+1;?>,<?php echo "'".$order_item."','".$order."'";?>);"><?php echo lang('page.next');?></a></li>
				<li><a href="javascript:mediaLib.page(<?php echo $totalPage;?>,<?php echo "'".$order_item."','".$order."'";?>);"><?php echo lang('page.last');?></a></li>	
			<?php endif;?>
	    <?php endif;?>
	    
  	</ul>
</div>

<script type="text/javascript">
		$(document).ready(function(){
			<?php if ($this->config->item('with_sub_folders')):?>
				var mydata = <?php echo $folders?>;
				$("#folderId").select2ToTree({treeData: {dataArr: mydata}/*, maximumSelectionLength: 3*/});		
			<?php endif?>
		
		});
</script>

