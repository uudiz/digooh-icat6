<table class="table-list" width="100%">
	<tr>
		<th width="200">
			<a href="javascript:void(0);" onclick="player.page(<?php echo $curpage; ?>,'name','<?php if ($order_item == 'name' && $order == 'desc') : ?>asc<?php else : ?>desc<?php endif; ?>');"><?php echo lang('player'); ?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'name' && $order == 'desc') : ?>dir-down.gif<?php elseif ($order_item == 'name' && $order == 'asc') : ?>dir-up.gif<?php else : ?>dir-blank.gif<?php endif; ?>" />
		</th>
		<th width="100">
			<a href="javascript:void(0);" onclick="player.page(<?php echo $curpage; ?>,'sn','<?php if ($order_item == 'sn' && $order == 'desc') : ?>asc<?php else : ?>desc<?php endif; ?>');"><?php echo lang('sn'); ?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'sn' && $order == 'desc') : ?>dir-down.gif<?php elseif ($order_item == 'sn' && $order == 'asc') : ?>dir-up.gif<?php else : ?>dir-blank.gif<?php endif; ?>" />
		</th>
		<th width="40">
			<a href="javascript:void(0);" onclick="player.page(<?php echo $curpage; ?>,'status','<?php if ($order_item == 'status' && $order == 'desc') : ?>asc<?php else : ?>desc<?php endif; ?>');"><?php echo lang('status'); ?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'status' && $order == 'desc') : ?>dir-down.gif<?php elseif ($order_item == 'status' && $order == 'asc') : ?>dir-up.gif<?php else : ?>dir-blank.gif<?php endif; ?>" />
		</th>


		<th width="200">
			<?php echo lang('criteria'); ?>
			<!--
				<a href="javascript:void(0);" onclick="player.page(<?php echo $curpage; ?>,'criteria_name','<?php if ($order_item == 'criteria_name' && $order == 'desc') : ?>asc<?php else : ?>desc<?php endif; ?>');" ><?php echo lang('criteria'); ?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'criteria_name' && $order == 'desc') : ?>dir-down.gif<?php elseif ($order_item == 'criteria_name' && $order == 'asc') : ?>dir-up.gif<?php else : ?>dir-blank.gif<?php endif; ?>" />
				-->
		</th>

		<th width="60"><?php echo lang('desc'); ?></th>
		<?php if (!$pid) : ?>
			<th width="60">
				<a href="javascript:void(0);" onclick="player.page(<?php echo $curpage; ?>,'timer_config_id','<?php if ($order_item == 'timer_config_id' && $order == 'desc') : ?>asc<?php else : ?>desc<?php endif; ?>');"><?php echo lang('timecfg'); ?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'timer_config_id' && $order == 'desc') : ?>dir-down.gif<?php elseif ($order_item == 'timer_config_id' && $order == 'asc') : ?>dir-up.gif<?php else : ?>dir-blank.gif<?php endif; ?>" />
			</th>
		<?php endif; ?>
		<th width="100">
			<a href="javascript:void(0);" onclick="player.page(<?php echo $curpage; ?>,'last_connect','<?php if ($order_item == 'last_connect' && $order == 'desc') : ?>asc<?php else : ?>desc<?php endif; ?>');"><?php echo lang('last.connect'); ?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'last_connect' && $order == 'desc') : ?>dir-down.gif<?php elseif ($order_item == 'last_connect' && $order == 'asc') : ?>dir-up.gif<?php else : ?>dir-blank.gif<?php endif; ?>" />
		</th>

		<th width="60">
			<a href="javascript:void(0);" onclick="player.page(<?php echo $curpage; ?>,'temperature','<?php if ($order_item == 'temperature' && $order == 'desc') : ?>asc<?php else : ?>desc<?php endif; ?>');"><?php echo lang('signal3g_strength'); ?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'temperature' && $order == 'desc') : ?>dir-down.gif<?php elseif ($order_item == 'temperature' && $order == 'asc') : ?>dir-up.gif<?php else : ?>dir-blank.gif<?php endif; ?>" />
		</th>

		<th width="60">
			<a href="javascript:void(0);" onclick="player.page(<?php echo $curpage; ?>,'setupdate','<?php if ($order_item == 'setupdate' && $order == 'desc') : ?>asc<?php else : ?>desc<?php endif; ?>');"><?php echo lang('setup_date'); ?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'setupdate' && $order == 'desc') : ?>dir-down.gif<?php elseif ($order_item == 'setupdate' && $order == 'asc') : ?>dir-up.gif<?php else : ?>dir-blank.gif<?php endif; ?>" />
		</th>
		<?php if (!$pid) : ?>
			<th width="160"><?php echo lang('operate'); ?></th>
		<?php endif; ?>
	</tr>

	<?php if ($total == 0) : ?>
		<tr>
			<td colspan="8">
				<?php if (isset($filter_array['filter_type'])) : ?>
					<?php if ($filter_array['filter_type'] == '') : ?>
						<?php echo lang("empty"); ?>
					<?php else : ?>
						<?php echo lang("search.empty.result1") . "\"" . $filter_array['filter'] . "\"" . lang("search.empty.result2"); ?>
					<?php endif; ?>
				<?php else : ?>
					<?php echo lang("empty"); ?>
				<?php endif; ?>
			</td>
		</tr>
	<?php else :
		$index = 0;
	?>
		<?php foreach ($data as $row) : ?>
			<tr <?php if ($index % 2 != 0) : ?>class="even" onmouseout="this.className='even'" <?php else : ?>onmouseout="this.className=''" <?php endif; ?> onmouseover="this.className='onSelected'">
				<td>
					<font <?php if ($row->player_flag) : ?>color="#ff0000" title="<?php echo lang('player.title'); ?>" <?php endif; ?>>
						<?php if (mb_strlen($row->name) > 36) {
							echo mb_substr($row->name, 0, 36) . '..';
						} else {
							echo $row->name;
						} ?>
					</font>
				</td>
				<td><?php echo format_sn($row->sn); ?></td>
				<td>
					<?php
					$sdesc = $row->status;
					switch ($row->status) {
						case 0: //unknow
							$sdesc = lang('status.0');
							break;
						case 1: //离线状态
							$sdesc = lang('status.1');
							break;
						case 2: //播放状态
							$sdesc = lang('status.2');
							break;
						case 3: //下载状态
							$sdesc = lang('status.3');
							break;
						case 4: //停止状态
							$sdesc = lang('status.4');
							break;
						case 5: //上线状态
							$sdesc = lang('status.5');
							break;
						case 6: //后台下载
							$sdesc = lang('status.6');
							break;
						case 7: //异常状态
							$sdesc = lang('status.7');
							break;
						case 8: //更新终端软件
							$sdesc = lang('status.8');
							break;
						case 9: //undefine
							$sdesc = lang('status.9');
							break;
						case 10: //登出服务器，终端关机前的请求
							$sdesc = lang('status.10');
							break;
						case 12: //待机状态
							$sdesc = lang('status.12');
							break;
						case 127: //待机状态
							$sdesc = lang('status.12');
							break;
					}
					?>
					<img src="<?php if ($row->status <= 1 || $row->status == 10) : ?>/images/icons/led_off.png<?php elseif ($row->status == 12 || $row->status == 127) : ?>/images/icons/led_idle.png<?php elseif ($row->status == 4 || $row->status == 5) : ?>/images/icons/led_stop.png<?php else : ?>/images/icons/led_on.png<?php endif; ?>" alt="<?php echo $sdesc ?>" title="<?php echo $sdesc ?>" status="<?php echo $row->status; ?>" />
				</td>

				<td>
					<?php echo $row->criteria_name; ?>
				</td>

				<td>
					<?php echo $row->descr; ?>
				</td>
				<?php if (!$pid) : ?>
					<td>
						<?php if ($auth == $this->config->item('auth_view')) : ?>
							<?php echo $row->timecfg; ?>
						<?php else : ?>
							<a href="/config/edit_timer?id=<?php echo $row->timer_config_id; ?>"><?php echo $row->timecfg; ?></a>
						<?php endif; ?>
					</td>
				<?php endif; ?>
				<td><?php echo $row->last_connect; ?></td>

				<td>
					<?php
					if ($row->humidity == 3 || $row->humidity == 4) {
						if ($row->temperature >= 0 && $row->temperature <= 5) {
							echo $row->temperature . "(Cell)";
						}
					} elseif ($row->humidity == 2) {
						if ($row->temperature >= 1 && $row->temperature <= 5) {
							echo $row->temperature . "(Wifi)";
						}
					}
					?>
				</td>
				<td>
					<?php if (isset($row->setupdate) && $row->setupdate != '0000-00-00') {
						echo $row->setupdate;
					} ?>

				</td>

				<?php if (!$pid) : ?>
					<td>
						<?php if ($auth >= $ADMIN) : ?>
							<a href="/player/edit?id=<?php echo $row->id; ?>&type=<?php if (isset($filter_array['filter_type'])) {
																						echo $filter_array['filter_type'];
																					} else {
																						echo '';
																					} ?>&name=<?php if (isset($filter_array['filter'])) {
																									echo $filter_array['filter'];
																								} else {
																									echo '';
																								} ?>&width=1280&height=800" class="thickbox" title="<?php echo lang('edit.player'); ?>"><img id="edit_<?php echo $row->id; ?>" src="/images/icons/24-edit.png" title="<?php echo lang('edit'); ?>" /></a>
							<a href="javascript:void(0);" onclick="player.remove(<?php echo $row->id; ?>,'<?php echo lang('tip.remove.item'); ?>');"><img id="del_<?php echo $row->id; ?>" src="/images/icons/24-del.png" title="<?php echo lang('delete'); ?>" /></a>
						<?php endif; ?>

						<a href="javascript:void(0);" onclick="player.toggle(this)" status="0" id="<?php echo $row->id; ?>"><img id="ex_<?php echo $row->id; ?>" src="/images/icons/24-down.png" title="<?php echo lang('tip.expland.item'); ?>" te="<?php echo lang('tip.expland.item'); ?>" tc="<?php echo lang('tip.collapse.item'); ?>" /></a>



						<a href="/player/calendar_view?id=<?php echo $row->id; ?>&width=1024&height=600" class="thickbox" title="Calendar"><img src="/images/icons/24-date.png" title="Calendar" /></a>
						<a href="/player/schedule_date?id=<?php echo $row->id; ?>&width=220&height=240"" class=" thickbox"><img src="/images/icons/export.gif" title="Export PlaySchedule" /></a>
						<?php if ($row->player_type && $row->status >= 2) : ?>
							<a href="javascript:void(0);" onclick="player.changeScreenShot('<?php echo lang('tip.remove.empty.item'); ?>', '<?php echo lang('warn.area.you.sure'); ?>', <?php echo $row->id; ?>, '<?php echo $row->sn; ?>', <?php echo $cid; ?>);" title="<?php echo lang('control.type4'); ?>"><img src="/images/icons/screenshot.png" title="ScreenShot" width="24" height="24" /></a>
						<?php endif; ?>
						<?php if ($row->player_type) : ?>
							&nbsp;&nbsp;
							<a class="thickbox" id="<?php echo $row->sn; ?>" title="Screenshot Preview" href="/player/screenshotView?cid=<?php echo $cid; ?>&id=<?php echo $row->id; ?>&sn=<?php echo $row->sn; ?>&time=<?php echo $row->screenshotDate; ?>&width=800&height=600">
								<?php if ($row->screenshot != '' && file_exists("./resources/preview/$cid/$row->sn.png")) : ?>
									<img src="./resources/preview/<?php echo $cid; ?>/<?php echo $row->sn; ?>.png ?>" width="32" height="24" />
								<?php endif; ?>
							</a>
						<?php endif; ?>

					</td>
				<?php endif; ?>
			</tr>
		<?php
			$index++;
		endforeach;
		?>
	<?php endif; ?>

</table>
<?php
$totalPage = floor(($total + ($limit - 1)) / $limit);

$startIndex = ($curpage > 3) ? $curpage - 3 : 1;
$endIndex = ($curpage < ($totalPage - 3)) ? ($curpage + 3) : $totalPage;
?>
<div class="page-panel clearfix">
	<ul class="pagination">
		<?php if ($totalPage > 1) : ?>
			<?php if ($curpage > 1) : ?>
				<li><a href="javascript:void(0);" onclick="player.page(1,<?php echo '\'' . $order_item . '\',\'' . $order . '\'' ?>);"><?php echo lang('page.first'); ?></a></li>
				<li><a href="javascript:void(0);" onclick="player.page(<?php echo $curpage - 1; ?>,<?php echo '\'' . $order_item . '\',\'' . $order . '\'' ?>);"><?php echo lang('page.prev'); ?></a></li>
			<?php endif; ?>
			<?php for ($i = $startIndex; $i <= $endIndex; $i++) : ?>
				<li <?php if ($i == $curpage) : ?>class="active" <?php endif; ?>>
					<?php if ($i == $curpage) : ?>
						<?php echo $i; ?>
					<?php else : ?>
						<a href="javascript:void(0);" onclick="player.page(<?php echo $i; ?>,<?php echo '\'' . $order_item . '\',\'' . $order . '\'' ?>);"><?php echo $i; ?></a>
					<?php endif; ?>
				</li>
			<?php endfor; ?>
			<?php if ($curpage < $totalPage) : ?>
				<li><a href="javascript:void(0);" onclick="player.page(<?php echo $curpage + 1; ?>,<?php echo '\'' . $order_item . '\',\'' . $order . '\'' ?>);"><?php echo lang('page.next'); ?></a></li>
				<li><a href="javascript:void(0);" onclick="player.page(<?php echo $totalPage; ?>,<?php echo '\'' . $order_item . '\',\'' . $order . '\'' ?>);"><?php echo lang('page.last'); ?></a></li>
			<?php endif; ?>
		<?php endif; ?>
	</ul>
</div>

<div id="rotateConfirm" title="Confirm" style="display:none;">
	<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 80px 0;"></span><?php echo lang('warn.screenshot'); ?></p>
</div>