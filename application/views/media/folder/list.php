<link rel="stylesheet" href="/static/css/jquery/jquery.treetable.css" />
<link rel="stylesheet" href="/static/css/jquery/jquery.treetable.theme.default.css" />
<script src='/static/js/jquery/jquery.treetable.js'></script>


<table id="treeTable" class="table-list"  width="100%" >
        <tr>
            <th width="320" >
				<a href="javascript:void(0);" onclick="folder.page(<?php echo $curpage;?>,'name','<?php if ($order_item == 'name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('folder');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'name' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			
			<th width="60">
				<?php echo lang('media.count');?>
			</th>
			
			<th  >
				<a href="javascript:void(0);" onclick="folder.page(<?php echo $curpage;?>,'tag_name','<?php if ($order_item == 'tag_name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('tag');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'tag_name' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'tag_name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>

	
			<th width="80">
       			 <a href="javascript:void(0);" onclick="folder.page(<?php echo $curpage;?>,'play_time','<?php if ($order_item == 'play_time' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('playtime');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'play_time' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'play_time' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			
			<th width="80">
				<a href="javascript:void(0);" onclick="folder.page(<?php echo $curpage;?>,'end_date','<?php if ($order_item == 'end_date' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('date.range');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'end_date' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'end_date' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>

            <th width="100">
				<a href="javascript:void(0);" onclick="folder.page(<?php echo $curpage;?>,'descr','<?php if ($order_item == 'descr' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('desc');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'descr' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'descr' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<th width="120">
				<a href="javascript:void(0);" onclick="folder.page(<?php echo $curpage;?>,'add_user_id','<?php if ($order_item == 'add_user_id' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('create.user');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'add_user_id' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'add_user_id' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>

			<th width="100"><?php echo lang('operate');?></th>
        </tr>
		
		<?php if ($total == 0): ?>
		<tr>
			<td colspan="6">
				<?php echo lang("empty");?>
			</td>
		</tr>
		<?php else:
          $index = 0;
        ?>
			<?php foreach ($data as $row):?>
				<?php if ($this->config->item('with_sub_folders')):?>
			<tr data-tt-id="<?php echo $row->id?>" data-tt-parent-id="<?php echo $row->pId?>"  >
				<?php else:?>
			<tr <?php if ($index%2 != 0):?>class="even" onmouseout="this.className='even'" <?php else:?>onmouseout="this.className=''"<?php endif;?>  onmouseover="this.className='onSelected'">					
				<?php endif;?>
			  <td>
			  	<?php echo $row->name;?>
			  </td>
			  <td><?php echo $row->media_count; ?></td>
			  <td>
			   	<?php echo $row->tag_name;?>
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
			  	<?php echo $row->descr;?>
			  </td>
			  <td>
				<?php
                    if ($row->add_user == null || $row->add_user == '') {
                        echo 'N/A';
                    } else {
                        echo $row->add_user;
                    }
                ?>
			  </td>

			  <td>
			  	<?php if ($auth >= 5||($auth==2&&isset($user_folders)&&in_array($row->id, $user_folders))):?>
			  		<a href="/folder/edit/<?php echo $row->id;?>?width=450&height=420" class="thickbox" title="<?php echo lang('edit.folder');?>"><img id="edit_<?php echo $row->id;?>" src="/images/icons/24-edit.png"  title="<?php echo lang('edit');?>" /></a>
			  		<a href="javascript:void(0);" onclick="folder.remove(<?php echo $row->id;?>,'<?php if ($row->media_count == 0) {
                    echo lang('tip.remove.item');
                } else {
                    echo lang('tip.remove.media.folder');
                }?>');"><img id="del_<?php echo $row->id;?>" src="/images/icons/24-del.png"  title="<?php echo lang('delete');?>" /></a>
			  		<?php if ($this->config->item('with_sub_folders')):?>
					<a href="/folder/add?id=<?php echo $row->id;?>&width=450&height=420" id="create" class="thickbox" title="<?php echo lang('create.folder');?>"><img  src="/images/icons/24-add.png"  title="<?php echo lang('create');?>" /></a>
					<?php endif?>  		
				<?php else:?>
					&nbsp;
				<?php endif;?>
			  </td>
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
    $midIndex   = intval(($curpage + 5) / 2);
    if ($midIndex - 2 > $startIndex) {
        $startIndex = $midIndex - 2;
    }
    if ($midIndex + 2 < $endIndex) {
        $endIndex = $midIndex + 2;
    }
?>
<div class="page-panel clearfix">
    <ul class="pagination">
    	<?php if ($totalPage > 1):?>
			<?php if ($curpage>1):?>
				<li><a href="/folder/index/1/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.first');?></a></li>
				<li><a href="/folder/index/<?php echo $curpage-1;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.prev');?></a></li>	
			<?php endif;?>
	    	<?php for ($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if ($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if ($i == $curpage):?>
					<?php echo $i;?>
				<?php else:?>
					<a href="/folder/index/<?php echo $i;?>/<?php echo $order_item.'/'.$order;?>"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if ($curpage<$totalPage):?>
				<li><a href="/folder/index/<?php echo $curpage+1;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.next');?></a></li>
				<li><a href="/folder/index/<?php echo $totalPage;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.last');?></a></li>	
			<?php endif;?>
	    <?php endif;?>
	    
  	</ul>
</div>

<?php if ($this->config->item('with_sub_folders')):?>
<script type="text/javascript">
	$("#treeTable").treetable({ expandable: true });
	$("#treeTable").treetable('expandAll');
/*
			$.ajax({
		  url: 'http://cms.digooh.com:8081/api/v1/players',
		  beforeSend: function(xhr) {
		    xhr.setRequestHeader("Authorization", "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9jbXMuZGlnb29oLmNvbTo4MDgxIiwiaWF0IjoxNTkwNDgzMzY0LCJleHAiOjE2MjIwMTkzNjQsIm5iZiI6MTU5MDQ4MzM2NCwianRpIjoiRExsTWlJUTRGNFBCaDRBRyIsInN1YiI6MzEsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.Sd4of_2yrobmZ-yLEGRJeuefJ9jHMhN738BtrPDCObI");
		  },
		  success: function(data) {
		  	console.log(data);
		  },
		        error: function (xhr, ajaxOptions, thrownError) {
        console.log(xhr.status);
         console.log(thrownError);
      }
		});
	*/
</script>
<?php endif;?>