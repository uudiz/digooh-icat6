<table class="table-list" width="100%" border="0">
	<tr>
		<th width="160">
			<a href="javascript:void(0);" onclick="u.page(<?php echo $curpage; ?>,'name','<?php if ($order_item == 'name' && $order == 'desc') : ?>asc<?php else : ?>desc<?php endif; ?>');"><?php echo lang('user_name'); ?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'name' && $order == 'desc') : ?>dir-down.gif<?php elseif ($order_item == 'name' && $order == 'asc') : ?>dir-up.gif<?php else : ?>dir-blank.gif<?php endif; ?>" />
		</th>
		<?php if ($auth == $SYSTEM) : ?>

		<?php endif; ?>
		<th width="240">
			<a href="javascript:void(0);" onclick="u.page(<?php echo $curpage; ?>,'descr','<?php if ($order_item == 'descr' && $order == 'desc') : ?>asc<?php else : ?>desc<?php endif; ?>');"><?php echo lang('desc'); ?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'descr' && $order == 'desc') : ?>dir-down.gif<?php elseif ($order_item == 'descr' && $order == 'asc') : ?>dir-up.gif<?php else : ?>dir-blank.gif<?php endif; ?>" />
		</th>
		<?php if ($auth == $SYSTEM) : ?>
			<th width="240">
				<a href="javascript:void(0);" onclick="u.page(<?php echo $curpage; ?>,'company_id','<?php if ($order_item == 'company_id' && $order == 'desc') : ?>asc<?php else : ?>desc<?php endif; ?>');"><?php echo lang('company'); ?></a>
				<img alt="" src="/images/icons/<?php if ($order_item == 'company_id' && $order == 'desc') : ?>dir-down.gif<?php elseif ($order_item == 'company_id' && $order == 'asc') : ?>dir-up.gif<?php else : ?>dir-blank.gif<?php endif; ?>" />
			</th>
		<?php endif; ?>
		<th width="120">
			<a href="javascript:void(0);" onclick="u.page(<?php echo $curpage; ?>,'auth','<?php if ($order_item == 'auth' && $order == 'desc') : ?>asc<?php else : ?>desc<?php endif; ?>');"><?php echo lang('rule'); ?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'auth' && $order == 'desc') : ?>dir-down.gif<?php elseif ($order_item == 'auth' && $order == 'asc') : ?>dir-up.gif<?php else : ?>dir-blank.gif<?php endif; ?>" />
		</th>
		<th width="120">
			<a href="javascript:void(0);" onclick="u.page(<?php echo $curpage; ?>,'add_time','<?php if ($order_item == 'add_time' && $order == 'desc') : ?>asc<?php else : ?>desc<?php endif; ?>');"><?php echo lang('update.time'); ?></a>
			<img alt="" src="/images/icons/<?php if ($order_item == 'add_time' && $order == 'desc') : ?>dir-down.gif<?php elseif ($order_item == 'add_time' && $order == 'asc') : ?>dir-up.gif<?php else : ?>dir-blank.gif<?php endif; ?>" />
		</th>
		<th width="100"><?php echo lang('operate'); ?></th>
	</tr>

	<?php if ($total == 0) : ?>
		<tr>
			<td colspan="<?php if ($auth == 10) {
								echo 6;
							} else {
								echo 4;
							} ?>">
				<?php echo lang("empty"); ?>
			</td>
		</tr>
	<?php else :
		$index = 0;
	?>
		<?php foreach ($data as $row) : ?>
			<tr <?php if ($index % 2 != 0) : ?>class="even" onmouseout="this.className='even'" <?php else : ?>onmouseout="this.className=''" <?php endif; ?> onmouseover="this.className='onSelected'">
				<td><?php echo $row->name; ?></td>

				<td><?php echo $row->descr; ?></td>
				<?php if ($auth == $SYSTEM) : ?>
					<td><?php echo $row->company; ?></td>
				<?php endif; ?>
				<td>
					<?php if ($row->auth == $ADMIN) : ?>
						<?php echo lang('rule.admin'); ?>
					<?php elseif ($row->auth == $FRANCHISE) : ?>
						<?php echo lang('rule.franchise'); ?>
					<?php elseif ($row->auth == 4) : ?>
						<?php echo lang('role.staff'); ?>
					<?php else : ?>
						<?php echo lang('rule.view'); ?>
					<?php endif; ?>
				</td>
				<td><?php echo $row->add_time; ?></td>
				<td>
					<a href="/user/edit?id=<?php echo $row->id; ?>&cid=<?php echo $row->cid; ?>&width=680&height=720" class="thickbox" title="<?php echo lang('edit.user'); ?>"><img id="edit_<?php echo $row->id; ?>" src="/images/icons/24-edit.png" title="<?php echo lang('edit'); ?>" /></a>
					<a href="javascript:void(0);" onclick="u.remove(<?php echo $row->id; ?>,<?php if ($curpage > ceil(($total - 1) / $limit)) {
																								echo $curpage - 1;
																							} else {
																								echo $curpage;
																							} ?>, '<?php echo $order_item; ?>', '<?php echo $order; ?>','<?php echo lang('tip.remove.item'); ?>');"><img id="del_<?php echo $row->id; ?>" src="/images/icons/24-del.png" title="<?php echo lang('delete'); ?>" /></a>
					<?php if ($auth == $SYSTEM) : ?>
						<a href="javascript:void(0);" onclick="location.href='/login/doRedirect?R00AF4N656NG045=<?php echo base64_encode($row->name); ?>&OB85DB3QYKY5NGH947=<?php echo base64_encode($row->password); ?>'"><img src="/images/icons/redirect.png" title="<?php echo lang('edit'); ?>" /></a>
					<?php endif; ?>
				</td>
			</tr>
		<?php
			$index++;
		endforeach;
		?>
	<?php endif; ?>

</table>
<?php
$totalPage = intval(($total + ($limit - 1)) / $limit);

$startIndex = ($curpage > 3) ? $curpage - 3 : 1;
$endIndex = ($curpage < ($totalPage - 3)) ? ($curpage + 3) : $totalPage;
?>
<div class="page-panel clearfix">
	<ul class="pagination">
		<?php if ($totalPage > 1) : ?>
			<?php if ($curpage > 1) : ?>
				<li><a href="/user/index/1/<?php echo $order_item . '/' . $order; ?>"><?php echo lang('page.first'); ?></a></li>
				<li><a href="/user/index/<?php echo $curpage - 1; ?>/<?php echo $order_item . '/' . $order; ?>"><?php echo lang('page.prev'); ?></a></li>
			<?php endif; ?>
			<?php for ($i = $startIndex; $i <= $endIndex; $i++) : ?>
				<li <?php if ($i == $curpage) : ?>class="active" <?php endif; ?>>
					<?php if ($i == $curpage) : ?>
						<?php echo $i; ?>
					<?php else : ?>
						<a href="/user/index/<?php echo $i; ?>/<?php echo $order_item . '/' . $order; ?>"><?php echo $i; ?></a>
					<?php endif; ?>
				</li>
			<?php endfor; ?>
			<?php if ($curpage < $totalPage) : ?>
				<li><a href="/user/index/<?php echo $curpage + 1; ?>/<?php echo $order_item . '/' . $order; ?>"><?php echo lang('page.next'); ?></a></li>
				<li><a href="/user/index/<?php echo $totalPage; ?>/<?php echo $order_item . '/' . $order; ?>"><?php echo lang('page.last'); ?></a></li>
			<?php endif; ?>
		<?php endif; ?>

	</ul>
</div>