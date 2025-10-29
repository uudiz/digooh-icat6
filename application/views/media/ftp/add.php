<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
	<tbody>
		<tr>
			<td><?php echo lang('ftp.profile');?>:</td>
            <td>
            	<input id="profile" name="ftp.profile" class="text ui-widget-content ui-corner-all"  />
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td><?php echo lang('ftp.server');?>:</td>
            <td>
            	<input id="server" name="ftp.server" class="text ui-widget-content ui-corner-all" />
			</td>
			<td>
				<div class="attention" id="errorFtpServer" style="display:none;">
					<?php echo lang('warn.ftp.server.format');?>
				</div>
			</td>
		</tr>
		<tr>
            <td><?php echo lang('ftp.port');?>:</td>
            <td>
            	<input id="port" name="ftp.port" class="text ui-widget-content ui-corner-all" value="21" />　
			</td>
			<td>
				<div class="attention" id="errorFtpPort" style="display:none;">
					<?php echo lang('warn.ftp.port.format');?>
				</div>
			</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><span class="font-12 font-gray"><?php echo lang('ftp.port.tip');?></span></td>
			<td>&nbsp;</td>
          </tr>
          <tr>
            <td><?php echo lang('ftp.pasv');?>:</td>
            <td>
            	<input type="checkbox" name="ftp.pasv" id="pasv" checked="checked" style="display:inline;" />
			</td>
			<td>&nbsp;</td>
          </tr>
          <tr>
            <td><?php echo lang('ftp.account');?>:</td>
            <td>
            	<input id="account" name="ftp.account" class="text ui-widget-content ui-corner-all" />
			</td>
			<td>&nbsp;</td>
          </tr>
          <tr>
            <td><?php echo lang('ftp.password');?>:</td>
            <td>
            	<input id="password" name="ftp.password" class="text ui-widget-content ui-corner-all" />
			</td>
			<td>&nbsp;</td>
          </tr>
	</tbody>
</table>
<p class="btn-center">
	<input type="hidden" id="id" name="id" value="0" />
 	<a class="btn-01" href="javascript:void(0);" onclick="ftp.saveFtp(this);"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>