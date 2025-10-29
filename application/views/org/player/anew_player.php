<?php if ($auth > $GROUP):?>
<div class="add-panel">
	<a href="/player/anew_add" id="create" class="add" title="<?php echo lang('create.player');?>" ><?php echo lang('create');?></a>
	<!--
	<a href="/player/anew_add?width=600&height=400" id="create" class="thickbox" title="<?php echo lang('create.player');?>" ><?php echo lang('create');?></a>
	-->
</div>
<?php endif;?>
<div class="clear"></div>
<h1 class="tit-01" style="position:relative;"><?php echo lang('new.player.auto.reg');?>
	<div class="filter" style="width:84%;">
		<?php echo lang('filter.by');?>:
		<!--
		<select id="filterType" name="filterType" style="width: 100px;margin:0px 4px;">
			<option value="name"><?php echo lang('player');?></option>
			<option value="sn"><?php echo lang('sn');?></option>
		</select>
		-->
		<input type="text" name="filter" id="filter" style="width:120px; margin-left:4px;">
		<!-- 
		<label style="margin-left:20px;"><?php echo lang('filter.group');?>:</label>
		<select id="filterGroup" name="filterGroup" style="width: 150px; margin:0px 4px;">
			<option value="0"><?php echo lang('all');?></option>
		</select>
	 -->	
		<a href="javascript:void(0);" style="margin-left:20px; " class="btn-go" onclick="player.newplayerFilter();"><label><?php echo lang('filter');?></label></a>
	</div>
    <span></span>
</h1>
<div class="clear"></div>
<div id="playerContent" gid="0" width="100%">
	<table class="table-list"  width="100%" >
        <tr>
            <th width="120" >
				<a href="javascript:void(0);" onclick="player.newPlayerPage(<?php echo $cid;?>,<?php echo $curpage;?>,'name','<?php if ($order_item == 'name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('player');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'name' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<th width="100" >
				<a href="javascript:void(0);" onclick="player.newPlayerPage(<?php echo $cid;?>,<?php echo $curpage;?>,'sn','<?php if ($order_item == 'sn' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('sn');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'sn' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'sn' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<th width="60" >
				<a href="javascript:void(0);" onclick="player.newPlayerPage(<?php echo $cid;?>,<?php echo $curpage;?>,'status','<?php if ($order_item == 'status' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('status');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'status' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'status' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<?php
                if ($this->config->item('mia_system_set') == $this->config->item('mia_system_all')) :
            ?>
			<th width="40"><?php echo lang('player.type');?></th>
			<?php endif;?>

			<th>
				<a href="javascript:void(0);" onclick="player.newPlayerPage(<?php echo $cid;?>,<?php echo $curpage;?>,'company_id','<?php if ($order_item == 'company_id' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('company');?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'company_id' && $order == 'desc'):?>dir-down.gif<?php elseif ($order_item == 'company_id' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
			</th>
			<th> Mac </th>
            <th><?php echo lang('desc');?></th>
			<th width="220"><?php echo lang('operate');?></th>
        </tr>
		
		<?php if ($total == 0): ?>
		<tr>
			<td colspan="8">
			<?php if (isset($filter_array['filter_type'])): ?>
				<?php if ($filter_array['filter_type']==''):?>
				<?php echo lang("empty"); ?>
				<?php else:?>
				<?php echo lang("search.empty.result1")."\"".$filter_array['filter']."\"".lang("search.empty.result2"); ?>
				<?php endif;?>
			<?php else:?>
				<?php echo lang("empty"); ?>
			<?php endif;?>					
			</td>	
		</tr>
		<?php else:
            $index = 0;
            foreach ($data as $row):
        ?>
		<tr <?php if ($index%2 != 0):?>class="even" onmouseout="this.className='even'" <?php else:?>onmouseout="this.className=''"<?php endif;?>  onmouseover="this.className='onSelected'">
			<td>
			  	<font <?php if ($row->player_flag):?>color="#ff0000" title="<?php echo lang('player.title');?>"<?php endif;?>>
			  		<?php if (mb_strlen($row->name) > 36) {
            echo mb_substr($row->name, 0, 36).'..';
        } else {
            echo $row->name;
        }?>
			  	</font>
			</td>
			<td><?php echo format_sn($row->sn);?></td>
			<td>
				<?php
                    $sdesc=$row->status;
                    switch ($row->status) {
                        case 0: //unknow
                            $sdesc = lang('status.0');
                            break;
                        case 1: //离线状态
                            $sdesc = lang('status.1');
                            break;
                        case 2: //播放状态
                            $sdesc =lang('status.2');
                            break;
                        case 3: //下载状态
                            $sdesc =lang('status.3');
                            break;
                        case 4: //停止状态
                            $sdesc =lang('status.4');
                            break;
                        case 5: //上线状态
                            $sdesc =lang('status.5');
                            break;
                        case 6: //后台下载
                            $sdesc =lang('status.6');
                            break;
                        case 7: //异常状态
                            $sdesc =lang('status.7');
                            break;
                        case 8: //更新终端软件
                            $sdesc =lang('status.8');
                            break;
                        case 9: //undefine
                            $sdesc =lang('status.9');
                            break;
                        case 10: //登出服务器，终端关机前的请求
                            $sdesc =lang('status.10');
                            break;
                        case 12: //待机状态
                            $sdesc =lang('status.12');
                            break;
                        case 127: //待机状态
                            $sdesc =lang('status.12');
                            break;
                    }
                ?>
			  	<img src="<?php if ($row->status <= 1):?>/images/icons/led_off.png<?php elseif ($row->status == 12 || $row->status == 127):?>/images/icons/led_idle.png<?php else:?>/images/icons/led_on.png<?php endif;?>" alt="<?php echo $sdesc?>" title="<?php echo $sdesc?>" status="<?php echo $row->status;?>"/>
			</td>
			<?php if ($this->config->item('mia_system_set') == $this->config->item('mia_system_all')) :?>
			<td>
			  	<?php if ($row->player_type):?>
				<img src="/images/icons/android.png"  title="<?php echo lang('type.1');?>" />
				<?php else:?>
				<img src="/images/icons/windows.png"  title="<?php echo lang('type.0');?>" />
				<?php endif;?>
			</td>
			<?php endif;?>
			<td><?php echo $row->company_name; ?></td>
			<td><?php echo strtoupper($row->mac); ?></td>
			<td>
				<?php if (mb_strlen($row->descr) > 32) {
                    echo mb_substr($row->descr, 0, 32).'...';
                } else {
                    echo $row->descr;
                }?>
			</td>
			<td>
				<a href="javascript:void(0);" onclick="player.newRemove(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>');"><img id="del_<?php echo $row->id;?>" src="/images/icons/24-del.png" title="<?php echo lang('delete');?>" /></a>
			</td>
		</tr>
		<?php
            $index++;
            endforeach;
            endif;
        ?>
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
					<li><a href="javascript:void(0);" onclick="player.newPlayerPage(<?php echo $cid;?>,1,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.first');?></a></li>
					<li><a href="javascript:void(0);" onclick="player.newPlayerPage(<?php echo $cid;?>,<?php echo $curpage-1;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.prev');?></a></li>	
				<?php endif;?>
				<?php for ($i = $startIndex; $i <= $endIndex; $i++):?>
				<li <?php if ($i == $curpage):?>class="active"<?php endif;?>>
					<?php if ($i == $curpage):?>
						<?php echo $i;?>
					<?php else:?>
						<a href="javascript:void(0);" onclick="player.newPlayerPage(<?php echo $cid;?>,<?php echo $i;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo $i;?></a>
					<?php endif;?>
				</li>
				<?php endfor;?>
				<?php if ($curpage<$totalPage):?>
					<li><a href="javascript:void(0);" onclick="player.newPlayerPage(<?php echo $cid;?>,<?php echo $curpage+1;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.next');?></a></li>
					<li><a href="javascript:void(0);" onclick="player.newPlayerPage(<?php echo $cid;?>,<?php echo $totalPage;?>,<?php echo '\''.$order_item.'\',\''.$order.'\''?>);"><?php echo lang('page.last');?></a></li>	
				<?php endif;?>
			<?php endif;?>
		</ul>
	</div>
</div>