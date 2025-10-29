<head>
<link rel="stylesheet" href="/static/css/jquery/chosen.min.css" />
<script src='/static/js/jquery/chosen.jquery.min.js'></script>
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD5ILgQ2vLjavzFASq5xHfuVYVneV9DBQk&callback=initAutocomplete&libraries=places&v=weekly"
    defer
></script>
 <script src="/static/js/googleplaces.js"></script>

</head>

<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
 
 
<form method="POST" id="cf" action="/user/do_save" >
    <div style="width:30%;float:left;">
		<table  class="from-panel">
			<tbody>
				<tr>
					<td>
						<?php echo lang('player');?>
					</td>
					<td>
						<input type="text" id="name" name="name" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="1"/>
					</td>
				</tr>
		
				<tr >
					<td >
						<?php echo lang('criteria');?>
					</td>
					<td >
						<select tabindex="2" data-placeholder="Choose Criteria..." id="jquery-cribox-select-options" class="chosen-select tag-input-style" multiple >
						<option value="0"></option>
							<?php foreach ($criteria as $tag):?>
								<option value="<?php echo $tag->id;?>"><?php echo $tag->name;?></option>
							<?php endforeach;?>
						</select>
	
					</td>
				</tr>

				<tr >
					<td >
						<?php echo lang('tag');?>
					</td>
					<td >
						<select tabindex="2" data-placeholder="Choose Tags..." id="jquery-tagbox-select-options" class="chosen-select tag-input-style" multiple >
						<option value="0"></option>
							<?php foreach ($tags as $tag):?>
								<option value="<?php echo $tag->id;?>"><?php echo $tag->name;?></option>
							<?php endforeach;?>
						</select>
	
					</td>
				</tr>
		
				<tr>
					<td >
						<?php echo lang('timer.settings');?>
					</td>
					<td>
						<select name="timerConfigId" id="timerConfigId"  class="ui-widget-content ui-corner-all" style="width:200px;" tabindex="3" onchange="player.change_timer();">
							<option value="0">&nbsp;</option>
							<?php foreach ($timers as $view):?>
							<option value="<?php echo $view->id;?>" ><?php echo $view->name;?></option>
							<?php endforeach;?>
						</select> (<?php echo lang('optional');?>)
					</td>
				</tr>
	
				<tr>
					<td>
						<?php echo lang('screen.type'); ?>
					</td>
					<td>
						<select id="screen" name="screen" class="ui-widget-content ui-corner-all" style="width:200px;"  >
			        		<option value="0"><?php echo lang('player.portrait'); ?></option>  
			        		<option value="1"><?php echo lang('player.landscape'); ?></option>     
			     	 	</select>
					</td>
				</tr>
				
				<tr>
					<td>
						<?php echo lang("desc");?>
					</td>
					<td>
						<textarea tabindex="5" name="descr" id="descr" class="ui-widget-content ui-corner-all" rows="2" style="width: 200px;"></textarea>
					</td>
				</tr>
	

					<tr>
						<td>
							<?php echo lang("player_conaddr");?>
						</td>
						<td>
							<input type="text" id="conaddr" name="conaddr" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="11"/>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang("street_num");?>
						</td>
						<td>
							<input type="text" id="stree_num" style="width: 200px;" class="text ui-widget-content ui-corner-all"  />
						</td>
					</tr>

					<tr>
						 <td>
								<?php echo lang("player_contown");?>
						 </td>
							<td>
							<input type="text" id="contown" name="contown" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="13"/>
						 </td>
					</tr>					
					<tr>
						<td>
							<?php echo lang("player_connzipcode");?>
						</td>
						<td>
							<input type="text" id="zipcode" name="zipcode" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="12"/>
						</td>
					</tr>
						<tr>
							 <td>
									<?php echo lang("player_state");?>
							 </td>
								<td>
								<input type="text" id="state" name="state" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="13"/>
							 </td>
						</tr>	
						<tr>
							 <td>
									<?php echo lang("player_country");?>
							 </td>
								<td>
								<input type="text" id="country" name="country" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="13" />
							 </td>
						</tr>			
					<tr>
						<td>
							<?php echo lang("geo_coord");?>
						 </td>
						<td>
							<input type="text" id="geox" name="geox" style="width: 80px;" class="text ui-widget-content ui-corner-all" tabindex="13" placeholder="Latitude"/>
							<input type="text" id="geoy" name="geoy" style="width: 80px;" class="text ui-widget-content ui-corner-all"  tabindex="13" placeholder="longitude"/>
						</td>
					</tr>		
	
						<tr>
							<td>
								<?php echo lang("pps");?>
							</td>
							<td>
								<input type="text" id="pps" name="pps" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="12" />
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("visitors");?>
							</td>
							<td>
								<input type="text" id="visitors" name="visitors" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="12" />
							</td>
						</tr>	
		
			</tbody>
		</table>
	</div>
	<div style="width:30%;float:left; clear:right;">
			<table  class="from-panel">
				<tbody>
					<tr>
						<td>
							<?php echo lang("player_barcode");?>
						</td>
						<td>
							<input type="text" id="barcode" name="barcode" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="6"/>
						</td>
					</tr>
					<tr>	
						<td>
							<?php echo lang("player_simno");?>
						</td>
						<td>
							<input type="text" id="simno" name="simno" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="7"/>
						</td>	
					</tr>
					<tr>
						<td style="width: 100px;">
							<?php echo lang("sim_volume");?>
						</td>
						<td>
							<input type="text" id="simvolume" name="simvolume" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="8"/>
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo lang("item_num");?>
						</td>
						<td>
							<input type="text" id="itemnum" name="itemnum" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="9"/>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang("model_name");?>
						</td>
						<td>
							<input type="text" id="modelname" name="modelname" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="10"/>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang("screen_size");?>
						</td>
						<td>
							<input type="text" id="screensize" name="screensize" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="11"/>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang("side");?>
						</td>
						<td>
							<select id="sided" class="ui-widget-content ui-corner-all" style="width:200px;"  >
								<option value="0"><?php echo lang('single_side');?></option>
								<option value="1"><?php echo lang('double_side');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang("displaynum");?>
						</td>
						<td>
							<input type="text" id="displaynum" name="displaynum" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="12"/>
						</td>
					</tr>			
					<tr>
						<td>
							<?php echo lang("partner_id");?>
						 </td>
						<td>
							<input type="text" id="partnerid" name="partnerid" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="13"/>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang("location_id");?>
						 </td>
						<td>
							<input type="text" id="locationid" name="locationid" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="14"/>
						</td>
					</tr>

					<tr>
						<td>
							<?php echo lang("setup_date");?>
						 </td>
						<td>
							<input type="date" id="setupdate" name="setupdate" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="15" value="<?php echo date('Y-m-d');?>"/>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang("view_direction");?>
						 </td>
						<td>
							<input type="text" id="viewdirection" name="viewdirection" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="16"/>
						</td>
					</tr>
					<tr>
						<td>
							<?php if (isset($company->cust_player_field1)&&$company->cust_player_field1) {
    echo $company->cust_player_field1;
} else {
    echo lang("custom_sn1");
}?>
						 </td>
						<td>
							<input type="text" id="customsn1" name="customsn1" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="16"/>
						</td>
					</tr>	
					<tr>
						<td>
							<?php if (isset($company->cust_player_field2)&&$company->cust_player_field2) {
    echo $company->cust_player_field2;
} else {
    echo lang("custom_sn2");
}?>
						 </td>
						<td>
							<input type="text" id="customsn2" name="customsn2" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="16"/>
						</td>
					</tr>
					<tr>
						<td style="width: 100px;">
								<?php echo lang("player_conname");?>
						</td>
						<td>
							<input type="text" id="conname" name="conname" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="17"/>
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo lang("player_conphone");?>
						</td>
						<td>
							<input type="text" id="phoneno" name="phoneno" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="19"/>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang("player_conemail");?>
						</td>
						<td>
							<input type="text" id="conemail" name="conemail" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="20"/>
						</td>
					</tr>	

					<tr>
							<td>
								<?php echo lang("player.detail");?>
							</td>
							<td>
								<textarea tabindex="5" name="detail" id="detail" class="ui-widget-content ui-corner-all" style="width:200px;" rows="2"></textarea>
							</td>
						
					</tr>																							
				</tbody>
			</table>
	</div>
		<?php  if ($this->config->item('ssp_feature')):?>
			<div style="width:40%;float:right;">
				<table  class="from-panel">
					<tbody>
							<tr >
							<td >
								<?php echo lang('ssp.category.dmi');?>
							</td>
							<td >
								<select  id="dmi-select-options" class="tag-input-style ssp-select" multiple >
								<option value="0"></option>
									<?php if (isset($dmis)):?>
									<?php foreach ($dmis as $cri):?>
										<option value="<?php echo $cri->id; ?>" ><?php echo $cri->name ?></option>
									<?php endforeach;?>
									<?php endif;?>
								</select>
			
							</td>
						</tr>
						<tr >
							<td >
								<?php echo lang('ssp.category.dpaa');?>
							</td>
							<td >
								<select id="dpaa-select-options" class="tag-input-style ssp-select" multiple >
								<option value="0"></option>
									<?php if (isset($dpaas)):?>
									<?php foreach ($dpaas as $cri):?>
										<option value="<?php echo $cri->id; ?>" ><?php echo $cri->name ?></option>
									<?php endforeach;?>
									<?php endif;?>
								</select>
			
							</td>
						</tr>	
						<tr >
							<td >
								<?php echo lang('ssp.category.iab');?>
							</td>
							<td >
								<select  id="ilb-select-options" class="tag-input-style ssp-select" multiple >
								<option value="0"></option>
									<?php if (isset($ilabs)):?>
									<?php foreach ($ilabs as $cri):?>
										<option value="<?php echo $cri->id; ?>" ><?php echo $cri->name ?></option>
									<?php endforeach;?>
									<?php endif;?>
								</select>
			
							</td>
						</tr>
						<tr >
							<td >
								<?php echo lang('ssp.category.openooh');?>
							</td>
							<td >
								<select tabindex="2" id="openoohs-select-options" class="tag-input-style ssp-select" multiple >
								<option value="0"></option>
									<?php if (isset($openoohs)):?>
									<?php foreach ($openoohs as $cri):?>
										<option value="<?php echo $cri->id; ?>" ><?php echo $cri->name ?></option>
									<?php endforeach;?>
									<?php endif;?>
								</select>
			
							</td>
						</tr>													
						<tr >
							<td >
								<?php echo lang('ssp.tags');?>
							</td>
							<td >
								<select   id="jquery-ssptagbox-select-options" class="tag-input-style ssp-select " multiple  >
								<option value="0"></option>
									<?php if (isset($ssptags)):?>
									<?php foreach ($ssptags as $tag):?>
									<option value="<?php echo $tag->id; ?>"><?php echo $tag->name ?>
									</option>
									<?php endforeach;?>
									<?php endif;?>
								</select>
			
							</td>
						</tr>
						
					<tr>
						<td>
							<?php echo lang('ssp.pos.tags');?>
						</td>
						<td>
							<input type="text" id="pos_tags"  class="text ui-widget-content ui-corner-all"  style="width:200px;"/>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang('ssp.exclude');?>
						</td>
						<td>
							<input type="text" id="ssp_exclude"  class="text ui-widget-content ui-corner-all"  style="width:200px;"  />
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang('ssp.additional');?>
						</td>
						<td>
							<input type="text" id="ssp_additional"  class="text ui-widget-content ui-corner-all"  style="width:200px;"  />
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang('ssp.dsp.alias');?>
						</td>
						<td>
							<input type="text" id="ssp_dsp_alias"  class="text ui-widget-content ui-corner-all"  style="width:200px;" />
						</td>
					</tr>	
					<tr>
						<td>
							<?php echo lang('ssp.dsp.ref');?>
						</td>
						<td>
							<input type="text" id="ssp_dsp_ref"  class="text ui-widget-content ui-corner-all"  style="width:200px;"/>
						</td>
					</tr>						
						<tr >
							<td >
								AMC
							</td>
							<td >
								
							</td>
						</tr>	

						<tr >
							<td >
								<?php echo lang('mon');?>
							</td>
							<td >
								<input type="text" id="mon" class="text ui-widget-content ui-corner-all"  style="width:300px;"  />
							</td>
						</tr>	
						<tr >
							<td >
								<?php echo lang('tue');?>
							</td>
							<td >
								<input type="text" id="tue" class="text ui-widget-content ui-corner-all"  style="width:300px;"  />
							</td>
						</tr>
						<tr >
							<td >
								<?php echo lang('wed');?>
							</td>
							<td >
								<input type="text" id="wed" class="text ui-widget-content ui-corner-all"  style="width:300px;"  />
							</td>
						</tr>			
						<tr >
							<td >
								<?php echo lang('thu');?>
							</td>
							<td >
								<input type="text" id="thu" class="text ui-widget-content ui-corner-all"  style="width:300px;"  />
							</td>
						</tr>			
						<tr >
							<td >
								<?php echo lang('fri');?>
							</td>
							<td >
								<input type="text" id="fri" class="text ui-widget-content ui-corner-all"  style="width:300px;"  />
							</td>
						</tr>			
						<tr >
							<td >
								<?php echo lang('sat');?>
							</td>
							<td >
								<input type="text" id="sat" class="text ui-widget-content ui-corner-all"  style="width:300px;"  />
							</td>
						</tr>			
						<tr >
							<td >
								<?php echo lang('sun');?>
							</td>
							<td >
								<input type="text" id="sun" class="text ui-widget-content ui-corner-all"  style="width:300px;"  />
							</td>
						</tr>									
					</tbody>
			   </table>
			</div>
		<?php endif;?>			
</form>
<div class="clear"></div>

<p class="btn-center">
	<a class="btn-01" href="javascript:void(0);" onclick="player.doSave();"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>

<script type="text/javascript">

$('.chosen-select').chosen({width: "200px"}); 
$('.ssp-select').chosen({width: "300px"}); 
</script>
