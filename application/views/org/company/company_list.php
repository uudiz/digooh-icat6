
<link rel="stylesheet" href="/static/css/jquery/jquery.treetable.css" />
<link rel="stylesheet" href="/static/css/jquery/jquery.treetable.theme.default.css" />
<script src='/static/js/jquery/jquery.treetable.js'></script>


<table id='treeTable' class="table-list"  width="100%" >
        <tr>
            <th width="200" >
				<a href="javascript:void(0);" onclick="c.page(<?php echo $curpage;?>,'name','<?php if ($order_item == 'name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('name');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'name' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<th width="60" >
				<a href="javascript:void(0);" onclick="c.page(<?php echo $curpage;?>,'code','<?php if ($order_item == 'code' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('code');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'code' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'code' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
            <th width="100">
            	<a href="javascript:void(0);" onclick="c.page(<?php echo $curpage;?>,'descr','<?php if ($order_item == 'descr' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('desc');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'descr' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'descr' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
            </th>
            <th width="100">
				<a href="javascript:void(0);" onclick="c.page(<?php echo $curpage;?>,'start_date','<?php if ($order_item == 'start_date' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('start.date');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'start_date' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'start_date' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<th width="100">
				<a href="javascript:void(0);" onclick="c.page(<?php echo $curpage;?>,'stop_date','<?php if ($order_item == 'stop_date' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('end.date');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'stop_date' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'stop_date' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<th width="100">
				All <?php echo lang('player.count');?>
			</th>
			<th width="100">
				<a href="javascript:void(0);" onclick="c.page(<?php echo $curpage;?>,'player_count','<?php if ($order_item == 'player_count' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" >Online <?php echo lang('player.count');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'player_count' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'player_count' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<th width="100">
				<a href="javascript:void(0);" onclick="c.page(<?php echo $curpage;?>,'max_user','<?php if ($order_item == 'max_user' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('max.user');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'max_user' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'max_user' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<th width="80">
				<a href="javascript:void(0);" onclick="c.page(<?php echo $curpage;?>,'total_disk','<?php if ($order_item == 'total_disk' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('max.disk');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'total_disk' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'total_disk' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>

			
			<th width="80">
				<a href="javascript:void(0);" onclick="c.page(<?php echo $curpage;?>,'storage_usage','<?php if ($order_item == 'storage_usage' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('total.use');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'storage_usage' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'storage_usage' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<th width="80">
				<?php echo lang("auto.dst")?>
			</th>
            <th width="150"><?php echo lang('operate');?></th>
        </tr>
		
		<?php if ($total == 0): ?>
		<tr>
			<td colspan="8">
				<?php echo lang("empty");?>
			</td>
		</tr>
		<?php else:
          $index = 0;
        ?>
			<?php foreach ($data as $row): ?>
			<?php if ($this->config->item('with_partners')):?>
			<tr data-tt-id="<?php echo $row->id?>" data-tt-parent-id="<?php echo $row->pid?>"  >
				<?php else:?>
			<tr <?php if ($index%2 != 0):?>class="even" onmouseout="this.className='even'" <?php else:?>onmouseout="this.className=''"<?php endif;?>  onmouseover="this.className='onSelected'">
			<?php endif;?>
			  <td>
				<a href="/user?type=cid&name=<?php echo $row->id;?>"><?php echo $row->name;?></a>
			  </td>
			  <td>
			  	<?php if ($row->code < 10) {
            echo "00".$row->code;
        } elseif ($row->code < 100) {
            echo "0".$row->code;
        } else {
            echo $row->code;
        }?>
			  </td>
			  <td>
			  	<?php  echo $row->descr;?>
			  </td>
			  
			  <td><?php echo $row->start_date; ?></td>
			  <td><?php echo $row->stop_date; ?></td>
			  <td><?php echo $row->all_player_count; ?></td>
			  <td>
			  	<a href="/player/company_player?cid=<?php echo $row->id;?>"  title="online player list"><?php echo $row->player_count; ?></a> 
			  </td>
			  <td><?php echo $row->max_user; ?></td>
			  <td >
				<?php
                $file_size = $row->total_disk;
                if ($file_size > 1024) {
                    $file_size /= 1024;
                    if ($file_size > 1024) {
                        $file_size /= 1024;
                        if ($file_size > 1024) {
                            echo sprintf('%.2f GB', $file_size/1024);
                        } else {
                            echo sprintf('%.0f MB', $file_size);
                        }
                    } else {
                        echo sprintf('%.0f KB', $file_size);
                    }
                } else {
                    echo $file_size.' byte';
                }
                ?>
			  </td>
			  <td>
			  <?php
                $file_size = $row->storage_usage;
                if ($file_size > 1024) {
                    $file_size /= 1024;
                    if ($file_size > 1024) {
                        $file_size /= 1024;
                        if ($file_size > 1024) {
                            echo sprintf('%.2f GB', $file_size/1024);
                        } else {
                            echo sprintf('%.0f MB', $file_size);
                        }
                    } else {
                        echo sprintf('%.0f KB', $file_size);
                    }
                } else {
                    echo $file_size.' byte';
                }
                ?>
			  </td>
			  <td>
			  	<?php if ($row->auto_dst==0) {
                    echo $row->dst_start."~".$row->dst_end;
                } else {
                    echo "OFF";
                }?>
			  </td>
			  <td>
			  <a href="/company/edit?id=<?php echo $row->id;?>&width=700&height=800" class="thickbox" title="<?php echo lang('edit.company');?>"><img id="edit_<?php echo $row->id;?>" src="/images/icons/24-edit.png"  title="<?php echo lang('edit');?>" /></a>
			  <a href="javascript:void(0);" onclick="c.remove(<?php echo $row->id;?>,<?php if ($curpage > ceil(($total-1)/$limit)) {
                    echo $curpage-1;
                } else {
                    echo $curpage;
                }?>, '<?php echo $order_item;?>', '<?php echo $order;?>','<?php echo lang('tip.remove.item');?>');"><img id="del_<?php echo $row->id;?>" src="/images/icons/24-del.png" title="<?php echo lang('delete');?>" /></a>
			  <?php if ($this->config->item('with_partners')&&!$row->pid):?>
			  <a href="/company/add?parent_id=<?php echo $row->id;?>&width=700&height=900" class="thickbox"  title="<?php echo lang('create.partner');?>"><img id="add_<?php echo $row->id;?>" src="/images/icons/24-add.png"title="<?php echo lang('create.partner');?>" /></a>
			  <?php endif;?>
			  <a href="/user/add?cid=<?php echo $row->id;?>&width=600&height=320" class="thickbox"  title="<?php echo lang('create.user');?>"><img  src="/images/icons/16-add-user.png"title="<?php echo lang('create.user');?>" /></a>
			  </td>
			</tr>
			<?php
                $index++;
                endforeach;
            ?>
		<?php endif;?>

</table>
<!---
<?php
    $totalPage = intval(($total + ($limit - 1)) / $limit);
    
    $startIndex=($curpage>3)?$curpage-3:1;
    $endIndex= ($curpage<($totalPage-3)) ? ($curpage+3) : $totalPage;
?>

<div class="page-panel clearfix">
    <ul class="pagination">
    	<?php if ($totalPage > 1):?>
		<?php if ($curpage>1):?>
			<li><a href="javascript:void(0);" onclick="c.page(1,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.first');?></a></li>
			<li><a href="javascript:void(0);" onclick="c.page(<?php echo $curpage-1;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.prev');?></a></li>	
		<?php endif;?>
    	<?php for ($i = $startIndex; $i <= $endIndex; $i++):?>
	    <li <?php if ($i == $curpage):?>class="active"<?php endif;?>>
	    	<?php if ($i == $curpage):?>
				<?php echo $i;?>
			<?php else:?>
				
				<a href="javascript:void(0);" onclick="c.page(<?php echo $i;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo $i;?></a>
			<?php endif;?>
		</li>
		<?php endfor;?>
		<?php if ($curpage<$totalPage):?>
			<li><a href="javascript:void(0);" onclick="c.page(<?php echo $curpage+1;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.next');?></a></li>
			<li><a href="javascript:void(0);" onclick="c.page(<?php echo $totalPage;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.last');?></a></li>	
		<?php endif;?>
		<?php endif;?>
  	</ul>
	
</div>
-->

<?php if ($this->config->item('with_partners')):?>
<script type="text/javascript">
	$("#treeTable").treetable({ expandable: true });
	$("#treeTable").treetable('expandAll');
</script>
<?php endif?>