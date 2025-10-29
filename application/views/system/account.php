<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
	<tbody>
		<tr>
			<td><?php echo lang('password.old');?></td>
			<td><input type="password" id="old" /></td>
		</tr>
		<tr>
			<td><?php echo lang('password.new');?></td>
			<td><input type="password" id="new" /></td>
		</tr>
		<tr>
			<td><?php echo lang('password.confirm');?></td>
			<td><input type="password" id="confirm" /></td>
		</tr>
		<tr>
			<td colspan="2">
				<a class="btn-01" onclick="account.savePassword();" href="javascript:void(0);">
					<span><?php echo lang('button.save');?></span>
				</a>
			</td>
		</tr>
	</tbody>
</table>
