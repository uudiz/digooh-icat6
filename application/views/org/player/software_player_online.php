<div class="right-l" id="pan_<?php echo $pid;?>" style="width:98%;">
	<!--<script type="text/javascript" src="/static/js/software.js"></script>-->
	<table class="table-list" width="100%">
		<tr>
			<th width="30">
				<input type="checkbox" name="checkall" class="checkbox">
			</th>
			<th>
				<a href="javascript:void(0);" onclick="software.page(<?php echo $ptype;?>,<?php echo $core;?>,<?php echo $curpage;?>,'name','<?php if($order_item == 'name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>', <?php echo $id;?>, <?php echo $pid;?>);" ><?php echo lang('player');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'name' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<th>
				<a href="javascript:void(0);" onclick="software.page(<?php echo $ptype;?>,<?php echo $core;?>,<?php echo $curpage;?>,'sn','<?php if($order_item == 'sn' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>', <?php echo $id;?>, <?php echo $pid;?>);" ><?php echo lang('sn');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'sn' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'sn' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<?php if($this->config->item('mia_system_set') == $this->config->item('mia_system_all')) :?>
			<th width="40"><?php echo lang('player.type');?></th>
			<?php endif;?>

			<th>
				<a href="javascript:void(0);" onclick="software.page(<?php echo $ptype;?>,<?php echo $core;?>,<?php echo $curpage;?>,'version','<?php if($order_item == 'version' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>', <?php echo $id;?>, <?php echo $pid;?>);" ><?php echo lang('version');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'version' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'version' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<th>
				<a href="javascript:void(0);" onclick="software.page(<?php echo $ptype;?>,<?php echo $core;?>,<?php echo $curpage;?>,'upgrade_version','<?php if($order_item == 'upgrade_version' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>', <?php echo $id;?>, <?php echo $pid;?>);" ><?php echo lang('upgrade.version');?></a>
				<img alt="" src="/images/icons/<?php if($order_item == 'upgrade_version' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'upgrade_version' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<th width="80"><?php echo lang('operate');?></th>
		</tr>
		<?php if(empty($players)): ?>
		<tr>
			<td colspan="7">
				<?php echo lang("empty");?>
			</td>
		</tr>
		<?php else:
			$index = 0;
		?>
		<?php foreach($players as $row):?>
		<tr <?php if($index%2 != 0):?>class="even" onmouseout="this.className='even'" <?php else:?>onmouseout="this.className=''"<?php endif;?>  onmouseover="this.className='onSelected'">
			<td>
				<input type="checkbox" name="checkbox" value="<?php echo $row->id;?>">
			</td>
			<td>
				<?php echo $row->name;?>
			</td>
			<td><?php echo format_sn($row->sn);?></td>
			<?php if($this->config->item('mia_system_set') == $this->config->item('mia_system_all')) :?>
			<td>
				<?php if($row->player_type):?>
				<img src="/images/icons/android.png"  title="<?php echo lang('type.1');?>" />
				<?php else:?>
				<img src="/images/icons/windows.png"  title="<?php echo lang('type.0');?>" />
				<?php endif;?>
			</td>
			<?php endif;?>

			<td><?php echo $row->version; ?></td>
			<td><label id="upgrade_version_<?php echo $row->id;?>"><?php echo $row->upgrade_version; ?></label></td>
			<td>
				<?php if($auth == $ADMIN):?>
				<a href="javascript:void(0);" class="upgrade" sid="<?php echo $id;?>" pid="<?php echo $row->id;?>" ><img src="/images/icons/upgrade.png" width="16" height="16" title="<?php echo lang('upgrade');?>" ></a>
				<?php endif;?>
			</td>
		</tr>
		<?php
			$index++; 
			endforeach; 
		?>
		<tr>
			<td colspan="6"></td>
			<td>
				<?php if($auth == $ADMIN):?>
				<a href="javascript:void(0);" class="upgradeall" sid="<?php echo $id;?>" ><img src="/images/icons/upgrade.png" width="16" height="16" title="<?php echo lang('upgrade.choose');?>" ></a>
				<?php endif;?>
			</td>
		</tr>
		<?php endif;?>
</table>
</div>