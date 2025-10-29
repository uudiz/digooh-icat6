<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tbody>
		<tr>
			<td>
				<form method="POST" id="cf" action="/user/do_save" >
					<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
						<tbody>
							<tr>
								<td>
									Company
								</td>
								<td>
									<select id="companyId" name="companyId" style="width: 200px;">
										<?php foreach($companys as $company):?>
											<option value="<?php echo $company->id;?>">&nbsp;<?php echo $company->name;?></option>
										<?php endforeach;?>
									</select>
								</td>
							</tr>
							<tr>
								<td>
									Mac
								</td>
								<td>
									<input type="text" id="mac" name="mac" value="" style="width: 200px;"/>&nbsp;0C63FC-XXXXXX (Enter last 6 digits only)
								</td>
							</tr>
							<tr>
								<td>
									<?php echo lang('citycode');?>
								</td>
								<td>
									<input type="text" id="cityCode" name="cityCode" style="width: 200px;" value=""/>&nbsp;(<?php echo lang('optional');?>)
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
							<tr>
								<td></td>
								<td>
									<a class="btn-01" href="javascript:void(0);" onclick="player.addPlayerRow();"><span>Create Player ID</span></a>
								</td>
							</tr>
							
						</tbody>
					</table>
					<table width="100%">
						<tr>
							<td width="60%">
								<table class="table-list" id="newPlayer" width="30%" cellspacing="0" cellpadding="0" border="0" class="tdBox">
									<tr>
										<th> ID </th>
										<th> Mac </th>
										<th> Company </th>
										<th> Function </th>
									</tr>
								</table>
							</td>
						</tr>
					</table>	
				</form>
			</td>
		</tr>
		<tr>
			<td>
				
				<br/>
				<p class="btn-left">
					<a class="btn-01" href="javascript:void(0);" onclick="player.doSaveRow();"><span>OK</span></a>
					<a class="btn-01" href="javascript:void(0);" onclick="player.goback()"><span><?php echo lang('button.cancel');?></span></a>	
				</p>
			</td>
		</tr>

	</tbody>
</table>

