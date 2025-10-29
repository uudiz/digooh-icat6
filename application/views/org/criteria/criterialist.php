<table class="table-list"  width="100%" border="0">
        <tr>
            <th  >
				<a href="javascript:void(0);" onclick="criteria.page(<?php echo $curpage;?>,'name','<?php if ($order_item == 'name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('criteria');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'name' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
            <th >
				<a href="javascript:void(0);" onclick="criteria.page(<?php echo $curpage;?>,'descr','<?php if ($order_item == 'descr' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('desc');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'descr' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'descr' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>

    		 <th  >
				<a href="javascript:void(0);" onclick="criteria.page(<?php echo $curpage;?>,'player_count','<?php if ($order_item == 'player_count' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('criteria.pcount');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'player_count' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'player_count' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			
			<th width="120"><?php echo lang('operate');?></th>
        </tr>
		
		<?php if ($total == 0): ?>
		<tr>
			<td colspan="5">
				<?php echo lang("empty");?>
			</td>
		</tr>
		<?php else:
          $index = 0;
        ?>
			<?php foreach ($data as $row):?>
			<tr <?php if ($index%2 != 0):?>class="even" onmouseout="this.className='even'" <?php else:?>onmouseout="this.className=''"<?php endif;?>  onmouseover="this.className='onSelected'">
			  <td>
			  	<?php if (mb_strlen($row->name) > 64) {
            echo mb_substr($row->name, 0, 64).'..';
        } else {
            echo $row->name;
        }?>
			  </td>
			  <td>
			  	<?php if (mb_strlen($row->descr) > 64) {
            echo mb_substr($row->descr, 0, 64).'...';
        } else {
            echo $row->descr;
        }?>
			  </td>
			  <td> 
			  	<?php echo $row->player_count; ?>
			  </td>
			  <td>
			  	<?php if ($auth > 1):?>
			  		<a href="/criteria/edit?id=<?php echo $row->id;?>&width=450&height=520"  class="thickbox" title="<?php echo lang('edit.criteria');?>"><img id="edit_<?php echo $row->id;?>" src="/images/icons/24-edit.png"  title="<?php echo lang('edit');?>" /></a>
			  		<a href="javascript:void(0);" onclick="criteria.remove(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>');"><img id="del_<?php echo $row->id;?>" src="/images/icons/24-del.png" title="<?php echo lang('delete');?>" /></a>
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
