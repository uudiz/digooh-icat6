<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tbody>
		<tr>
			<td>
				<form method="POST" id="cf" action="/authorize/do_save" >
					<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
						<tbody>
							<tr>
								<td>
									Count
								</td>
								<td>
									<input type="text" id="count" name="count" style="width: 100px;" value=""/>
								</td>
							</tr>
							<tr>
								<td>
									<?php echo lang("desc");?>
								</td>
								<td>
									<textarea name="descr" id="descr" class="ui-widget-content ui-corner-all" rows="2" style="width: 200px;"></textarea>
								</td>
							</tr>
						</tbody>
					</table>	
				</form>
			</td>
		</tr>
		<tr>
			<td>
				<br/>
				<p class="btn-left">
					<a class="btn-01" href="javascript:void(0);" onclick="au.doSave();"><span>OK</span></a>
					<a class="btn-01" href="javascript:void(0);" onclick="au.goback()"><span><?php echo lang('button.cancel');?></span></a>	
				</p>
			</td>
		</tr>
	</tbody>
</table>

