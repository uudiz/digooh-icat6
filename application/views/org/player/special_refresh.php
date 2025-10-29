	<table class="table-list"  width="100%" >
       <tr>
       		<th width="30" ><input type="checkbox" name="checkAll" id="topCheckAll" onclick="special.checkAll(this);"></th>
            <th width="120" >
				<a href="javascript:void(0);" onclick="special.page(<?php echo $curpage;?>,'name','<?php if ($order_item == 'name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('player');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'name' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<th width="100" >
				<a href="javascript:void(0);" onclick="special.page(<?php echo $curpage;?>,'sn','<?php if ($order_item == 'sn' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('sn');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'sn' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'sn' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>

            <th width="100">
            	<a href="javascript:void(0);" onclick="special.page(<?php echo $curpage;?>,'version','<?php if ($order_item == 'version' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('version');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'version' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'version' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
            </th>

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
			  <td><input type="checkbox" name="id" value="<?php echo $row->id;?>" /></td>
			  <td>
			  	<?php echo $row->name;?>
			  </td>
			  <td><?php echo format_sn($row->sn);?></td>

			   		 	
			  <td><?php echo $row->version;?></td>

			</tr>
			<?php
                $index++;
                endforeach;
            ?>
			<tr <?php if ($index%2 != 0):?>class="even"<?php endif;?>>
				<td colspan="9">
					<a href="javascript:void(0);" class="button" onclick="special.changeStatus('<?php echo lang('tip.remove.empty.item');?>','<?php echo lang('warn.player.restart');?>', 1);"  title="<?php echo lang('restart');?>"><?php echo lang('restart');?></a>&nbsp;&nbsp;
					<a href="javascript:void(0);" class="button" onclick="special.changeStatus('<?php echo lang('tip.remove.empty.item');?>','<?php echo lang('warn.player.format');?>', 8);"  title="<?php echo lang('format');?>" ><?php echo lang('format');?></a>
					<?php if ($this->session->userdata('uname') == 'lt123' || $this->session->userdata('uname') == 'np200'):?>
					<a href="javascript:void(0);" class="button" onclick="special.changeStatus('<?php echo lang('tip.remove.empty.item');?>','', 10);"  title="uploadErrorLog" >uploadErrorLog</a>
					<?php endif;?>
					<a href="javascript:void(0);" class="button" onclick="special.changeStatus('<?php echo lang('tip.remove.empty.item');?>','<?php echo lang('warn.player.format');?>', 3);"  title="<?php echo lang('control.type3');?>" ><?php echo lang('control.type3');?></a>
					<?php if ($this->session->userdata('uname') == 'mialog' || $this->session->userdata('uname') == 'np200'):?>
					<a href="javascript:void(0);" class="button" onclick="special.changeStatus('<?php echo lang('tip.remove.empty.item');?>','', 10);"  title="uploadErrorLog" >uploadErrorLog</a>
					<?php endif;?>
				</td>
			</tr>

			<a href="javascript:void(0);" class="button" onclick="special.changeStatus('<?php echo lang('tip.remove.empty.item');?>','', 10);"  title="uploadErrorLog" >uploadErrorLog</a>

		<?php endif;?>

</table>
<?php
    $totalPage = floor(($total + ($limit - 1)) / $limit);
    $startIndex = 1;
    $endIndex   = $totalPage;
    $midIndex   = floor(($curpage + 5) / 2);
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
				<li><a href="javascript:void(0);" onclick="special.page(1,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.first');?></a></li>
				<li><a href="javascript:void(0);" onclick="special.page(<?php echo $curpage-1;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.prev');?></a></li>	
			<?php endif;?>
	    	<?php for ($i = $startIndex; $i <= $endIndex; $i++):?>
		    <li <?php if ($i == $curpage):?>class="active"<?php endif;?>>
		    	<?php if ($i == $curpage):?>
					<?php echo $i;?>
				<?php else:?>
					<a href="javascript:void(0);" onclick="special.page(<?php echo $i;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo $i;?></a>
				<?php endif;?>
			</li>
			<?php endfor;?>
			<?php if ($curpage<$totalPage):?>
				<li><a href="javascript:void(0);" onclick="special.page(<?php echo $curpage+1;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.next');?></a></li>
				<li><a href="javascript:void(0);" onclick="special.page(<?php echo $totalPage;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.last');?></a></li>	
			<?php endif;?>
	    <?php endif;?>
  	</ul>
  	<div id="rotateConfirm" title="Confirm" style="display:none;">
		<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 80px 0;"></span><?php echo lang('warn.send.data');?></p>
	</div>
	<div id="volumeConfirm" title="Set the volume" style="display:none;">
		Volume (0-100 is an integer):&nbsp;&nbsp;<input type="text" id="volume" name="volume" style="width: 40px;" value="0"/>
	</div>
</div>

