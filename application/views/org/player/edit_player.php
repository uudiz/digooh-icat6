<head>
<link rel="stylesheet" href="/static/css/jquery/chosen.min.css" />
<link rel="stylesheet" href="/static/css/player.css" />
<link href="/static/css/lightbox.min.css" rel="stylesheet" />
<script src="/static/js/lightbox.min.js"></script>
<script src='/static/js/jquery/chosen.jquery.min.js'></script>
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD5ILgQ2vLjavzFASq5xHfuVYVneV9DBQk&callback=initAutocomplete&libraries=places&v=weekly"
></script>
 <script src="/static/js/googleplaces.js"></script>
</head>
<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
  
<form method="POST"  id="cf" action="/player/do_save" >
  <div>
	  <div style="width:30%;float:left;">
		<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
			<tbody>
				<tr>
					<td>
						<?php echo lang('player');?>
					</td>
					<td>
						<input tabindex="1" type="text" id="name" name="name" class="text ui-widget-content ui-corner-all"  style="width:200px;" value="<?php echo $player->name;?>" />
					</td>
				</tr>
		
				<tr>
					<td><?php echo lang('criteria');?></td>
					<td>
						<select tabindex="2" data-placeholder="Choose Criterias..." id="jquery-cribox-select-options" class="chosen-select tag-input-style" multiple >
						<option value="0"></option>
						<?php foreach ($criteria as $tag):?>
								<option value="<?php echo $tag->id; ?>" <?php if (isset($cristr)) {
    $criary=explode(',', $cristr);
} if (isset($criary)&&in_array($tag->id, $criary)):?>selected<?php endif;?>><?php echo $tag->name ?></option>
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
								<option value="<?php echo $tag->id; ?>"
								 <?php
                                 if (isset($tagstr)) {
                                     $tagary=explode(',', $tagstr);
                                 }
                                if (isset($tagary)&&in_array($tag->id, $tagary)):?>selected<?php endif;?>><?php echo $tag->name
                                ?>
								</option>
							<?php endforeach;?>
						</select>
	
					</td>
				</tr>
		
				<tr>
					<td >
						<?php echo lang('timer.settings');?>
					</td>
					<td>
						<select tabindex="3" name="timerConfigId" class="text ui-widget-content ui-corner-all" id="timerConfigId" style="width:150px;" onchange="player.change_timer();">
							<option value="0">&nbsp;</option>
							<?php foreach ($timers as $view):?>
									<option value="<?php echo $view->id;?>" <?php if ($view->id == $player->timer_config_id):?>selected="selected"<?php endif;?> ><?php echo $view->name;?></option>
							<?php endforeach;?>
						</select> (<?php echo lang('optional');?>)
					</td>
				</tr>

				<tr>
					<td>
						<?php echo lang('screen.type'); ?>
					</td>
					<td>
					  <select tabindex="4" id="screen" name="screen" class="text ui-widget-content ui-corner-all" style="width:200px;">
					        <option value="0" <?php if ($player->screen_oritation== "0"):?>selected="selected"<?php endif;?>><?php echo lang('player.portrait'); ?></option>  
					        <option value="1" <?php if ($player->screen_oritation== "1"):?>selected="selected"<?php endif;?>><?php echo lang('player.landscape'); ?></option>     
				      </select>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo lang("desc");?>
					</td>
					<td>
						<textarea tabindex="5" name="descr" id="descr" class="ui-widget-content ui-corner-all" style="width:200px;" rows="2"><?php echo $player->descr;?></textarea>
					</td>
				
				</tr>
		
						<tr>
							<td>
								<?php echo lang("player_conaddr");?>
							</td>
							<td>
								<input type="text" id="conaddr" name="conaddr" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="11" value="<?php if ($extra) {
                                    echo $extra->conaddr;
                                }?>"/>
							</td>
						</tr>
					<tr>
						<td>
							<?php echo lang("street_num");?>
						</td>
						<td>
							<input type="text" id="street_num" style="width: 200px;" class="text ui-widget-content ui-corner-all"  value="<?php if ($extra) {
                                    echo $extra->street_num;
                                }?>"/>
						</td>
					</tr>

						<tr>
							 <td>
									<?php echo lang("player_contown");?>
							 </td>
								<td>
								<input type="text" id="contown" name="contown" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="13" value="<?php if ($extra) {
                                    echo $extra->contown;
                                }?>"/>
							 </td>
						</tr>					
						<tr>
							<td>
								<?php echo lang("player_connzipcode");?>
							</td>
							<td>
								<input type="text" id="zipcode" name="zipcode" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="12" value="<?php if ($extra) {
                                    echo $extra->conzipcode;
                                }?>"/>
							</td>
						</tr>
						<tr>
							 <td>
									<?php echo lang("player_state");?>
							 </td>
								<td>
								<input type="text" id="state" name="state" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="13" value="<?php if ($extra) {
                                    echo $extra->state;
                                }?>"/>
							 </td>
						</tr>	
						<tr>
							 <td>
									<?php echo lang("player_country");?>
							 </td>
								<td>
								<input type="text" id="country" name="country" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="13" value="<?php if ($extra) {
                                    echo $extra->country;
                                }?>"/>
							 </td>
						</tr>
						<tr>
							<td>
								<?php echo lang("geo_coord");?>
							 </td>
							<td>
								<input type="text" id="geox" name="geox" style="width: 80px;" class="text ui-widget-content ui-corner-all" value="<?php if ($extra) {
                                    echo $extra->geox;
                                }?>" placeholder="Latitude"/>
								<input type="text" id="geoy" name="geoy" style="width: 80px;" class="text ui-widget-content ui-corner-all"  value="<?php if ($extra) {
                                    echo $extra->geoy;
                                }?>" placeholder="longitude"/>
							</td>
						</tr>

						<tr>
							<td>
								<?php echo lang("pps");?>
							</td>
							<td>
								<input type="text" id="pps" name="pps" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="12" value="<?php if ($extra) {
                                    echo $extra->pps;
                                }?>"/>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("visitors");?>
							</td>
							<td>
								<input type="text" id="visitors" name="visitors" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="12" value="<?php if ($extra) {
                                    echo $extra->visitors;
                                }?>"/>
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
								<input type="text" id="barcode" name="barcode" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="6" value="<?php if ($extra) {
                                    echo $extra->barcode;
                                }?>"/>
							</td>
						</tr>
						<tr>	
							<td>
								<?php echo lang("player_simno");?>
							</td>
							<td>
								<input type="text" id="simno" name="simno" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="7" value="<?php if ($extra) {
                                    echo $extra->simno;
                                }?>"/>
							</td>	
						</tr>
						<tr>
							<td style="width: 100px;">
								<?php echo lang("sim_volume");?>
							</td>
							<td>
								<input type="text" id="simvolume" name="simvolume" class="text ui-widget-content ui-corner-all" style="width: 200px;" value="<?php if ($extra) {
                                    echo $extra->simvolume;
                                }?>"/>
							</td>
						</tr>
						
						<tr>
							<td>
								<?php echo lang("item_num");?>
							</td>
							<td>
								<input type="text" id="itemnum" name="itemnum" class="text ui-widget-content ui-corner-all" style="width: 200px;" value="<?php if ($extra) {
                                    echo $extra->itemnum;
                                }?>"/>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("model_name");?>
							</td>
							<td>
								<input type="text" id="modelname" name="modelname" class="text ui-widget-content ui-corner-all" style="width: 200px;" value="<?php if ($extra) {
                                    echo $extra->modelname;
                                }?>"/>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("screen_size");?>
							</td>
							<td>
								<input type="text" id="screensize" name="screensize" class="text ui-widget-content ui-corner-all" style="width: 200px;" value="<?php if ($extra) {
                                    echo $extra->screensize;
                                }?>"/>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("side");?>
							</td>
							<td>
								<select id="sided" name="sided" class="ui-widget-content ui-corner-all" style="width:200px;" >
									<option value="0" <?php if ($extra&&$extra->sided== "0"):?>selected="selected"<?php endif;?>><?php echo lang('single_side');?></option>
									<option value="1" <?php if ($extra&&$extra->sided== "1"):?>selected="selected"<?php endif;?>><?php echo lang('double_side');?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("displaynum");?>
							</td>
							<td>
								<input type="text" id="displaynum" name="displaynum" class="text ui-widget-content ui-corner-all" style="width: 200px;" value="<?php if ($extra) {
                                    echo $extra->displaynum;
                                }?>"/>
							</td>
						</tr>						
						<tr>
							<td>
								<?php echo lang("partner_id");?>
							 </td>
							<td>
								<input type="text" id="partnerid" name="partnerid" class="text ui-widget-content ui-corner-all" style="width: 200px;" value="<?php if ($extra) {
                                    echo $extra->partnerid;
                                }?>"/>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("location_id");?>
							 </td>
							<td>
								<input type="text" id="locationid" name="locationid" class="text ui-widget-content ui-corner-all" style="width: 200px;" value="<?php if ($extra) {
                                    echo $extra->locationid;
                                }?>"/>
							</td>
						</tr>
	
						<tr>
							<td>
								<?php echo lang("setup_date");?>
							 </td>
							<td>
								<input type="date" id="setupdate" name="setupdate" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="13" value="<?php if ($extra) {
                                    echo $extra->setupdate;
                                }?>"/>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("view_direction");?>
							 </td>
							<td>
								<input type="text" id="viewdirection" name="viewdirection" class="text ui-widget-content ui-corner-all" style="width: 200px;" value="<?php if ($extra) {
                                    echo $extra->viewdirection;
                                }?>"/>
							</td>
						</tr>	
					<tr>
						<td>
							<?php echo lang("custom_sn1");?>
						 </td>
						<td>
							<input type="text" id="customsn1" name="customsn1" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="16" value="<?php if ($extra) {
                                    echo $extra->custom_sn1;
                                }?>"/>
						</td>
					</tr>	
					<tr>
						<td>
							<?php echo lang("custom_sn2");?>
						 </td>
						<td>
							<input type="text" id="customsn2" name="customsn2" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="16" value="<?php if ($extra) {
                                    echo $extra->custom_sn2;
                                }?>"/>
						</td>
					</tr>						
						<tr>
							<td style="width: 100px;">
								<?php echo lang("player_conname");?>
							</td>
							<td>
								<input type="text" id="conname" name="conname" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="8" value="<?php if ($extra) {
                                    echo $extra->conname;
                                }?>"/>
							</td>
						</tr>
						
						<tr>
							<td>
								<?php echo lang("player_conphone");?>
							</td>
							<td>
								<input type="text" id="phoneno" name="phoneno" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="9" value="<?php if ($extra) {
                                    echo $extra->conphone;
                                }?>"/>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("player_conemail");?>
							</td>
							<td>
								<input type="text" id="conemail" name="conemail" class="text ui-widget-content ui-corner-all" style="width: 200px;" tabindex="10" value="<?php if ($extra) {
                                    echo $extra->conemail;
                                }?>"/>
							</td>
						</tr>		
						<tr>
							<td>
								<?php echo lang("player.detail");?>
							</td>
							<td>
								<textarea tabindex="5" name="detail" id="detail" class="ui-widget-content ui-corner-all" style="width:200px;" rows="2"><?php echo $player->details;?></textarea>
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
								<select tabindex="2" id="dmi-select-options" class="tag-input-style ssp-select" multiple >
								<option value="0"></option>
									<?php if (isset($dmis)):?>
									<?php foreach ($dmis as $cri):?>
										<option value="<?php echo $cri->id; ?>" 
										<?php
                                            if (isset($sspcristr)) {
                                                $sspcriary=explode(',', $sspcristr);
                                            };
                                            if (isset($sspcriary)&&in_array($cri->id, $sspcriary)):?>selected<?php endif;?>><?php echo $cri->name ?></option>
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
								<select tabindex="2" id="dpaa-select-options" class="tag-input-style ssp-select" multiple >
								<option value="0"></option>
									<?php if (isset($dpaas)):?>
									<?php foreach ($dpaas as $cri):?>
										<option value="<?php echo $cri->id; ?>" 
										<?php
                                            if (isset($sspcristr)) {
                                                $sspcriary=explode(',', $sspcristr);
                                            };
                                            if (isset($sspcriary)&&in_array($cri->id, $sspcriary)):?>selected<?php endif;?>><?php echo $cri->name ?></option>
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
								<select tabindex="2" id="ilb-select-options" class="tag-input-style ssp-select" multiple >
								<option value="0"></option>
									<?php if (isset($ilabs)):?>
									<?php foreach ($ilabs as $cri):?>
										<option value="<?php echo $cri->id; ?>" 
										<?php
                                            if (isset($sspcristr)) {
                                                $sspcriary=explode(',', $sspcristr);
                                            };
                                            if (isset($sspcriary)&&in_array($cri->id, $sspcriary)):?>selected<?php endif;?>><?php echo $cri->name ?></option>
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
										<option value="<?php echo $cri->id; ?>" 
										<?php
                                            if (isset($sspcristr)) {
                                                $sspcriary=explode(',', $sspcristr);
                                            };
                                            if (isset($sspcriary)&&in_array($cri->id, $sspcriary)):?>selected<?php endif;?>><?php echo $cri->name ?></option>
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
								<select data-placeholder="Choose Tags..." id="jquery-ssptagbox-select-options" class="tag-input-style ssp-select " multiple  >
								<option value="0"></option>
									<?php if (isset($ssptags)):?>
									<?php foreach ($ssptags as $tag):?>
									<option value="<?php echo $tag->id; ?>" <?php if (isset($ssptagstr)) {
                                                $tagary=explode(',', $ssptagstr);
                                            } if (isset($tagary)&&in_array($tag->id, $tagary)):?>selected<?php endif;?>><?php echo $tag->name ?>
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
							<input type="text" id="pos_tags"  class="text ui-widget-content ui-corner-all"  style="width:200px;" value="<?php echo $extra->pos_tags;?>" />
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang('ssp.exclude');?>
						</td>
						<td>
							<input type="text" id="ssp_exclude"  class="text ui-widget-content ui-corner-all"  style="width:200px;" value="<?php echo $extra->ssp_exclude;?>" />
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang('ssp.additional');?>
						</td>
						<td>
							<input type="text" id="ssp_additional"  class="text ui-widget-content ui-corner-all"  style="width:200px;" value="<?php echo $extra->ssp_additional;?>" />
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang('ssp.dsp.alias');?>
						</td>
						<td>
							<input type="text" id="ssp_dsp_alias"  class="text ui-widget-content ui-corner-all"  style="width:200px;" value="<?php echo $extra->ssp_dsp_alias;?>" />
						</td>
					</tr>	
					<tr>
						<td>
							<?php echo lang('ssp.dsp.ref');?>
						</td>
						<td>
							<input type="text" id="ssp_dsp_ref"  class="text ui-widget-content ui-corner-all"  style="width:200px;" value="<?php echo $extra->ssp_dsp_ref;?>" />
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
								<input type="text" id="mon" class="text ui-widget-content ui-corner-all"  style="width:300px;" value="<?php if (isset($amc->mon)) {
                                                echo $amc->mon;
                                            }?>" />
							</td>
						</tr>	
						<tr >
							<td >
								<?php echo lang('tue');?>
							</td>
							<td >
								<input type="text" id="tue" class="text ui-widget-content ui-corner-all"  style="width:300px;" value="<?php if (isset($amc->tue)) {
                                                echo $amc->tue;
                                            }?>" />
							</td>
						</tr>
						<tr >
							<td >
								<?php echo lang('wed');?>
							</td>
							<td >
								<input type="text" id="wed" class="text ui-widget-content ui-corner-all"  style="width:300px;" value="<?php if (isset($amc->wed)) {
                                                echo $amc->wed;
                                            }?>" />
							</td>
						</tr>			
						<tr >
							<td >
								<?php echo lang('thu');?>
							</td>
							<td >
								<input type="text" id="thu" class="text ui-widget-content ui-corner-all"  style="width:300px;" value="<?php if (isset($amc->thu)) {
                                                echo $amc->thu;
                                            }?>" />
							</td>
						</tr>			
						<tr >
							<td >
								<?php echo lang('fri');?>
							</td>
							<td >
								<input type="text" id="fri" class="text ui-widget-content ui-corner-all"  style="width:300px;" value="<?php if (isset($amc->fri)) {
                                                echo $amc->fri;
                                            }?>" />
							</td>
						</tr>			
						<tr >
							<td >
								<?php echo lang('sat');?>
							</td>
							<td >
								<input type="text" id="sat" class="text ui-widget-content ui-corner-all"  style="width:300px;" value="<?php  if (isset($amc->sat)) {
                                                echo $amc->sat;
                                            }?>" />
							</td>
						</tr>			
						<tr >
							<td >
								<?php echo lang('sun');?>
							</td>
							<td >
								<input type="text" id="sun" class="text ui-widget-content ui-corner-all"  style="width:300px;" value="<?php  if (isset($amc->sun)) {
                                                echo $amc->sun;
                                            }?>" />
							</td>
						</tr>									
					</tbody>
			   </table>
			</div>
		<?php endif;?>					
	</div>

</form>

<div class="clear"></div>
<?php if ($this->config->item('player_pics')):?>
	<div id="imgrow" class="image-row">
	<?php if (isset($pics)):?>

		<?php if (is_array($pics)):?>
		<?php foreach ($pics as $pic):?>
	          <a id="<?php echo $pic->id;?>" name="<?php echo $pic->name;?>" class="example-image-link" href="<?php echo '/resources/playerPic/'.$player->id.'/'.$pic->name; ?>" data-lightbox="example-1"><img class="example-image" src="<?php echo '/resources/playerPic/'.$player->id.'/'.$pic->name; ?>" alt="<?php echo $pic->ori_name?>"> <input class="delete" type="button"  value="X" onclick="delpic(this);" />  </a>
	 	<?php endforeach;?>  
		<?php endif;?>

	<?php endif; ?>
    </div>
<?php endif;?>
<div class="clear"></div>
<div>
<p class="btn-center">
	<input type="hidden" id="id" name="id" value="<?php echo $player->id;?>" />
	<input type="hidden" id="filter_type" name="filter_type" value="<?php echo $type;?>" />
	<input type="hidden" id="filter_name" name="filter_name" value="<?php echo $name;?>" />
<?php if ($this->config->item('player_pics')):?>
	<a id="uploadimg" class="btn-01"  href="javascript:void(0);"><span>Browse</span></a>
<?php endif;?>
	<a class="btn-01" href="javascript:void(0);" onclick="player.doSave(<?php echo $player->player_type;?>, <?php echo $player->status;?>);"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="player.closeModel();"><span><?php echo lang('button.cancel');?></span></a>	
</p>
</div>
<div id="restartConfirm" title="Confirm" style="display:none;">
	<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 80px 0;"></span>Player will restart if group is changed. Are you sure to change?</p>
</div>



<script type="text/javascript">



$('.chosen-select').chosen({width: "200px"}); 
$('.ssp-select').chosen({width: "300px"}); 
$("#uploadimg").on("click",function(){
    var thisObj = $(this);
    var inputFile = document.createElement("input");
    inputFile.type = "file";
    inputFile.name = "file[]";
    inputFile.accept = "image/jpeg,image/jpg,image/png";
    inputFile.multiple="multiple";
    inputFile.click();

    $(inputFile).change(function(){
        var files = inputFile.files;
        for(var i=0;i<files.length;i++){
            uploadImg(files[i],thisObj);
        }
    });
});

//$(".delete").click( function(event){
function delpic(target){
    var event = window.event || arguments.callee.caller.arguments[0];
    var id = $(target).parent().attr("id");
    var name =$(target).parent().attr("name");
    var pid = $('#id').val();

    $.post('/player/delete_picture', {'id':id,'pid':pid,'name':name}, function(data){
		if(data.code == 0){
			showFrmMsg("Failed to delete image", 'error');
			return;
		}
	},'json');
    $(target).parent().remove();
    // 阻止事件冒泡到DOM树上
    event.preventDefault();
   
    event.stopPropagation();  

    return false;
};


function uploadImg(file,thisObj){
    var formData = new FormData();
    var pid = $('#id').val();
    formData.append('image', file);
    formData.append('pid',pid);
    $.ajax({
        url: "/player/upload_picture",
        type: "post",
        data: formData,
        dataType:"json",
        contentType: false,
        processData: false,
        mimeType: "multipart/form-data",
        success: function (data) {
            if(data.code == 0){
            	var imgrow = $("#imgrow");
            	var html =  '<a id="'+data.pic['id']+'" name="'+data.pic['name']+'" class="example-image-link" href="/resources/playerPic/'+pid+'/'+data.pic['name']+'"  data-lightbox="example-1"><img class="example-image" src="/resources/playerPic/'+pid+'/'+data.pic['name']+'" alt="'+data.pic['ori_name']+'"> <input class="delete" type="button"  value="X" onclick="delpic(this);"/></a>';
                imgrow.append(html); 
　　　　　　　}
        },
    });
}
</script>