<table class="table-list"  width="100%" >

    <tr>
        <th width="200" >
        	<a href="javascript:void(0);" onclick="campaign.page(<?php echo $curpage;?>,'name','<?php if ($order_item == 'name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');"> <?php echo lang('name');?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'name' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />

		</th>
				
		
		<th width="200">
			<a href="javascript:void(0);" onclick="campaign.page(<?php echo $curpage;?>,'criteria_name','<?php if ($order_item == 'criteria_name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php if ($this->config->item('cam_with_player')) {
    echo lang('criteria_player');
} else {
    echo lang('criteria');
}?></a>

			<img alt="" src="/images/icons/<?php if ($order_item == 'criteria_name' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'criteria_name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />

		</th>

		<?php if ($this->config->item('campaign_with_tags')):?>
		<th width="200">
			<a href="javascript:void(0);" onclick="campaign.page(<?php echo $curpage;?>,'tag_name','<?php if ($order_item == 'tag_name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('tag');?></a>

			<img alt="" src="/images/icons/<?php if ($order_item == 'tag_name' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'tag_name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />

		</th>
		<?php endif?>
		
		 <th width="20">
        	<a href="javascript:void(0);" onclick="campaign.page(<?php echo $curpage;?>,'published','<?php if ($order_item == 'published' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');"> <?php echo lang('published');?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'published' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'published' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />

		</th>
		
		<th width="40">
				<a href="javascript:void(0);" onclick="campaign.page(<?php echo $curpage;?>,'priority','<?php if ($order_item == 'priority' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');"> <?php echo lang('priority');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'priority' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'priority' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
		
		</th>
		

		
		
		<th width="60">
       		<a href="javascript:void(0);" onclick="campaign.page(<?php echo $curpage;?>,'play_count','<?php if ($order_item == 'play_count' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');"> <?php echo lang('campaign.count');?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'play_count' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'play_count' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />

		</th>
		<th width="100">
     		<a href="javascript:void(0);" onclick="campaign.page(<?php echo $curpage;?>,'start_date','<?php if ($order_item == 'start_date' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');"> <?php echo lang('start.date');?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'start_date' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'start_date' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />		
		</th>
		<th width="100">
     		<a href="javascript:void(0);" onclick="campaign.page(<?php echo $curpage;?>,'end_date','<?php if ($order_item == 'end_date' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');"> <?php echo lang('end.date');?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'end_date' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'end_date' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />		
		</th>
		
		
	
		<th width="100">
	    	<a href="javascript:void(0);" onclick="campaign.page(<?php echo $curpage;?>,'start_timeH','<?php if ($order_item == 'start_timeH' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');"> <?php echo lang('time.range');?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'start_timeH' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'start_timeH' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />		
		</th>
		
		
		<!--
		<th width="60">
	    	<a href="javascript:void(0);" onclick="campaign.page(<?php echo $curpage;?>,'update_time','<?php if ($order_item == 'update_time' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');"> <?php echo lang('update.time');?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'update_time' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'update_time' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />	
		</th>
		-->


		<th width="80"><?php echo lang('operate');?></th>

    </tr>

	

	<?php if ($total == 0): ?>

	<tr>

		<td colspan="7">

			<?php echo lang("empty");?>

		</td>

	</tr>

	<?php else:

      $index = 0;

    ?>

		<?php foreach ($data as $row):?>

		<tr <?php if ($index%2 != 0):?>class="even" onmouseout="this.className='even'" <?php else:?>onmouseout="this.className=''"<?php endif;?>  onmouseover="this.className='onSelected'">

		  <td>
			<?php if ($row->is_locked): ?><img src="/images/icons/locked.png" /><?php endif;?>
	        <?php  echo $row->name;?>
		  </td>


		 
		  <td>
			<?php
                    if ($this->config->item('cam_with_player')) {
                        if ($row->criteria_name) {
                            echo $row->criteria_name.($row->player_name?'/'.$row->player_name:'');
                        } else {
                            echo $row->player_name;
                        }
                    } else {
                        echo $row->criteria_name;
                    }
                        
                ?>
		  </td>
		  <?php if ($this->config->item('campaign_with_tags')):?>
		  	<td>	
		  		<?php if ($row->tag_options==2) {
                    echo '<font style="color: red;">'.$row->tag_name.'</font>';
                } else {
                    echo $row->tag_name;
                } ?>
		  		
		  	</td>
		  <?php endif?>
		  
		  <td>

		  	<?php if ($row->published == 0):?>

				<img src="/images/icons/led_off.png" alt="<?php echo lang('activity.status.saved');?>" title="<?php echo lang('activity.status.saved');?>"/>

			<?php elseif ($row->published == 1):?>

				<?php if ($row->priority == 5):?>
					<?php
                        $daydiff = (strtotime($row->start_date)-strtotime('now'))/86400;
                        if (($daydiff>=0&&$daydiff<=7)):
                    ?>
						<img src="/images/icons/led_stop.png" alt="<?php echo lang('activity.status.reserved');?>" title="<?php echo lang('activity.status.reserved');?>"/>
					<?php else:?>
						<img src="/images/icons/led_on.png" alt="<?php echo lang('activity.status.reserved');?>" title="<?php echo lang('activity.status.reserved');?>"/>
					<?php endif ?>

				<?php else:?>
					<img src="/images/icons/led_on.png" alt="<?php echo lang('activity.status.published');?>" title="<?php echo lang('activity.status.published');?>"/>
				<?php endif?>
			  </td>
			<?php endif;?>

		  </td>


		  <td>
			  <?php
                if ($row->priority==0) {
                    echo lang('priority.dedicated');
                } elseif ($row->priority==1) {
                    echo lang('priority.high');
                } elseif ($row->priority==2) {
                    echo lang('priority.low');
                } elseif ($row->priority==3) {
                    echo lang('priority.fillin');
                } elseif ($row->priority==4) {
                    echo lang('priority.trail');
                } elseif ($row->priority==5) {
                    echo lang('priority.reservation');
                } elseif ($row->priority==6) {
                    echo lang('priority.simple');
                }
              ?>
		 </td>

		  
		  <td>
		  	 <?php
             if ($row->priority!=3) {
                 if ($row->play_cnt_type == 1) {
                     if ($row->play_weight>0) {
                         echo $row->play_weight."%";
                     }
                 } elseif ($row->play_cnt_type == 0) {
                     if ($row->play_count>0) {
                         echo $row->play_count;
                     }
                 } elseif ($row->play_cnt_type == 2) {
                     if ($row->play_total>0) {
                         if ($row->published==1) {
                             echo $row->play_total." (".$row->play_totalperhour."x)";
                         } else {
                             echo $row->play_total;
                         }
                     }
                 } elseif ($this->config->item('xslot_on')&&$row->play_cnt_type == 9) {
                     echo "Every ".$xslot."th";
                 }
             }
             ?>
		  </td>
		  <td>
		  		<?php if (date('Y-m-d') > $row->end_date):?> 
		  			<font style="color: red;"> <?php echo $row->start_date;?></font> 
		  		<?php else: ?>
		  			<?php echo $row->start_date;?>
		  		<?php endif;?>
		  		
		  </td>
		  <td>
		  		<?php if (date('Y-m-d') > $row->end_date):?> 
		  			<font style="color: red;"> <?php echo $row->end_date;?></font> 
		  		<?php else: ?>
		  			<?php echo $row->end_date;?>
		  		<?php endif;?>
		  		
		  </td>
		  
		  <td>
		  	 <?php if ($row->time_flag) {
                 echo "00:00-24:00" ;
             } else {
                 echo sprintf("%02d:%02d-%02d:%02d", $row->start_timeH, $row->start_timeM, $row->end_timeH, $row->end_timeM);
             }?>
		  </td>
		  
		  <!--
		  <td><?php echo substr($row->update_time, 2); ?></td>
		-->

		  <td>

		  	<a href="/campaign/screen?id=<?php echo $row->id;?>" title="<?php echo lang('edit.campaign');?>"><img id="edit_<?php echo $row->id;?>" src="/images/icons/24-edit.png"  /></a>
			
			<?php if ($auth>1&&$auth!=4):?>
		  	<a href="javascript:void(0);" onclick="campaign.remove(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>');" title="<?php echo lang('delete');?>" ><img id="del_<?php echo $row->id;?>" src="/images/icons/24-del.png"  /></a>
			<?php endif?>

			<a class="thickbox" href="/campaign/devices?id=<?php echo $row->id;?>&name=<?php echo $row->name;?>&width=640&height=820" title="Devices"><img src="/images/icons/24-devices.png"  /></a>
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

$startIndex=($curpage>3)?$curpage-3:1;
$endIndex= ($curpage<($totalPage-3)) ? ($curpage+3) : $totalPage;

?>

<div class="page-panel clearfix">

   <ul class="pagination">
    	<?php if ($totalPage > 1):?>
			<?php if ($curpage>1):?>
				<li><a href="javascript:void(0);" onclick="campaign.page(1,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.first');?></a></li>
				<li><a href="javascript:void(0);" onclick="campaign.page(<?php echo $curpage-1;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.prev');?></a></li>	
			<?php endif;?>
	    	<?php for ($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if ($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if ($i == $curpage):?>
					<?php echo $i;?>
				<?php else:?>
					<a href="javascript:void(0);" onclick="campaign.page(<?php echo $i;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if ($curpage<$totalPage):?>
				<li><a href="javascript:void(0);" onclick="campaign.page(<?php echo $curpage+1;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.next');?></a></li>
				<li><a href="javascript:void(0);" onclick="campaign.page(<?php echo $totalPage;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.last');?></a></li>	
			<?php endif;?>
	    <?php endif;?>
  	</ul>


</div>
