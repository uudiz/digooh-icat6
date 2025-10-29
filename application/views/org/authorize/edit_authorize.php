<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tbody>
		<tr>
			<td>
					<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
						<tbody>
							<tr>
								<td>
									Code
								</td>
								<td>
									<?php echo $authorize->code;?>
								</td>
							</tr>
							<tr>
								<td>
									<?php echo lang("desc");?>
								</td>
								<td>
									<textarea name="descr" id="descr" class="ui-widget-content ui-corner-all" rows="2" style="width: 200px;"><?php echo $authorize->descr;?></textarea>
								</td>
							</tr>
						</tbody>
					</table>
			</td>
		</tr>
		<tr>
			<td>
				<br/>
				<p class="btn-left">
					<input type="hidden" name="id" id="id" value="<?php echo $authorize->id;?>"/>
					<a class="btn-01" href="javascript:void(0);" onclick="au.doSave();"><span>OK</span></a>
					<a class="btn-01" href="javascript:void(0);" onclick="au.goback()"><span><?php echo lang('button.cancel');?></span></a>	
				</p>
			</td>
		</tr>
	</tbody>
</table>