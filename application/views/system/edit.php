<link rel="stylesheet" href="/static/css/jquery/chosen.min.css" />
<script src='/static/js/jquery/chosen.jquery.min.js'></script>
<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<form method="POST" id="cf" action="/company/do_save" style="width:60%">
	<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
		<tbody>
			<tr>
				<td colspan="2">
					<div class="information">
						<?php echo $storage_used;?>
					</div>
				</td>
			</tr>
			<!--
			<tr>
				<td width="120"><?php echo lang('email');?></td>
				<td>
					<input type="text" id="email" value="<?php echo $company->email;?>" style="width: 200px" />
					&nbsp;&nbsp;;&nbsp;&nbsp;
				   <input type="text" id="email2" value="<?php echo $company->email2;?>" style="width: 200px" /> 
					<p>
						<input type="checkbox" id="offlineEmailFlag" <?php if($company->offline_email_flag):?>checked="checked"<?php endif;?> />&nbsp;<?php echo lang('offline.email.tip');?>&nbsp;  <input type="number" id="emailinterval" min="30" value="<?php echo $company->offline_email_inteval;?>" style="width: 50px" /> minutes
					</p>

				</td>
			</tr>
			-->
			<tr>
					<td width="120">
						<?php echo lang('offline.email');?>
					</td>
					<td>

						<input type="checkbox" id="offlineEmailFlag" <?php if($company->offline_email_flag):?>checked="checked"<?php endif;?> /><?php echo lang('offline.email.tip');?>&nbsp;  <input type="number" min="5" id="emailinterval"  value="<?php echo $company->offline_email_inteval;?>" style="width: 50px" /> <?php echo lang('minutes.to');?>
						
						<select  data-placeholder="Choose users..." id="notify_user_1" class="chosen-select tag-input-style" multiple>
							<option value="0"></option>
							<?php foreach($users as $user):?>
								<option value="<?php echo $user->id;?>" <?php $userary=explode(',',$company->users1); if(is_array($userary)&&in_array($user->id,$userary)):?>selected<?php endif;?>><?php echo $user->name;?></option>
							<?php endforeach;?>
						</select>
						<p></p>
						<input type="checkbox" id="offlineEmailFlag2" <?php if($company->offline_email_flag2):?>checked="checked"<?php endif;?> /><?php echo lang('offline.email.tip');?>&nbsp;  <input type="number" id="emailinterval2" min="5" value="<?php echo $company->offline_email_inteval2;?>" style="width: 50px" /> <?php echo lang('minutes.to');?>
						
						<select  data-placeholder="Choose users..." id="notify_user_2" class="chosen-select tag-input-style" multiple>
							<option value="0"></option>
							<?php foreach($users as $user):?>
									<option value="<?php echo $user->id;?>" <?php $userary=explode(',',$company->users2); if(is_array($userary)&&in_array($user->id,$userary)):?>selected<?php endif;?>><?php echo $user->name;?></option>
							<?php endforeach;?>
						</select>					         
					</td>
			</tr>  
			<tr>
				<td>
					<?php echo lang("weather.format");?>
				</td>
				<td >
					<select id="weatherFormat" name="weatherFormat" style="width: 200px">
						<option value="f" <?php if($company->weather_format == 'f'):?>selected="selected"<?php endif;?>><?php echo lang('weather.format.fahrenheit');?></option>
						<option value="c" <?php if($company->weather_format == 'c'):?>selected="selected"<?php endif;?>><?php echo lang('weather.format.celsius');?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang("config.color.setting");?>
				</td>
				<td >
					<select id="colorsetting"  style="width: 200px">
						<option value="0" <?php if($company->color_setting == '0'):?>selected="selected"<?php endif;?>><?php echo lang('config.color.blue');?></option>
						<option value="1" <?php if($company->color_setting == '1'):?>selected="selected"<?php endif;?>><?php echo lang('config.color.rosy');?></option>
					</select>
				</td>
			</tr>
			<!--
			<tr>
				<td>
					<?php echo lang("video.on.screen");?>
				</td>
				<td >
					<select id="fit" name="fit" style="width: 200px">
						<option value="1" <?php if($company->fitORfill == '1'):?>selected="selected"<?php endif;?>><?php echo lang("video.on.screen.fit");?></option>
						<option value="0" <?php if($company->fitORfill == '0'):?>selected="selected"<?php endif;?>><?php echo lang("video.on.screen.fill");?></option>
					</select>
				</td>
			</tr>
		-->

		</tbody>
	</table>
	<p class="btn-center">
		<input type="hidden" name="id" id="id" value="<?php echo $company->id;?>" />
    	<a class="btn-01" href="javascript:void(0);" onclick="system.doSave();"><span><?php echo lang('button.save');?></span></a>
    </p>
</form>
<?php
	/**
	* 如果DST有设定，动态获取DST开始日期
	*
	* 美国：每年的3月的第二个周日进入DST  一直到11月的第一个周日结束
	* 德国：每年的3月的最后一个周日进入DST  一直到10月的最后一个周日结束
	*/
	$dst_country = $this->config->item('dst_country');
	
	$data_array = explode('-', date('Y-m-d'));
	$year = $data_array[0];
	
	switch ($dst_country){
		case 0:
			$month = 3;
			$base_time = strtotime( "{$year}-{$month}-01" );
			$base_w = date ( "w", $base_time );
			if($base_w == '0'){
				//刚好是星期天
				$start_week = strtotime("+7 day", $base_time);
			}else{
				//不是星期天，找到上个星期天是几月几号
				$last_sun = date ( "Y-m-d", strtotime("-$base_w day", $base_time));
				//在上个星期天的基础上 加14天
				$start_week = strtotime( "+14 day", strtotime( $last_sun ) );
			}
			$dst_start = date('Y-m-d' ,  $start_week);	


			$month = 11;
			$base_time = strtotime( "{$year}-{$month}-01" );
			$base_w = date ( "w", $base_time );
			if($base_w == '0'){
				$end_week = strtotime("+0 day", $base_time);
			}else{
				$last_sun = date( "Y-m-d", strtotime("-$base_w day", $base_time));
				$end_week = strtotime( "+7 day", strtotime($last_sun ));
			}
			$dst_end = date('Y-m-d',  $end_week);
			break;
		case 1:
			$month = 3;
			$base_time = strtotime( "{$year}-{$month}-31" );
			$base_w = date ( "w", $base_time );
			if($base_w == '0'){
				$start_week = strtotime("+0 day", $base_time);
			}else{
				$last_sun = date ( "Y-m-d", strtotime("-$base_w day", $base_time));
				$start_week = strtotime( "+0 day", strtotime( $last_sun ) );
			}
			$dst_start = date('Y-m-d' ,  $start_week);	

			$month = 10;
			$base_time = strtotime( "{$year}-{$month}-31" );
			$base_w = date ( "w", $base_time );
			if($base_w == '0'){
				$end_week = strtotime("+0 day", $base_time);
			}else{
				$last_sun = date( "Y-m-d", strtotime("-$base_w day", $base_time));
				$end_week = strtotime( "+0 day", strtotime($last_sun ));
			}
			$dst_end = date('Y-m-d',  $end_week);
			break;
	}
?>
<p style="font-size:14px;margin-top:10px;">
*For DST information, visit <a href="http://www.webexhibits.org/daylightsaving/g.html" target="_blank">http://www.webexhibits.org/daylightsaving/g.html</a>
<!--
<br/>
<br/>
**:To find your City Code, visit http://www.weather.yahoo.com and enter city name. 7 digit numbers will appear on the top url address.<br/>
ex. <a href="http://weather.yahoo.com/united-states/california/los-angeles-2442047" target="_blank">http://weather.yahoo.com/united-states/california/los-angeles-2442047</a>   City Code: 2442047 
-->
</p>
<script type="text/javascript">
	$(function() {
		$('#dst').click(function() {
			
			$('input:checkbox[id="dst"]').each(function(){
		    	if (this.checked) {
		        	$("#dst_start").val("<?php echo $dst_start; ?>");
		        	$('#dst_end').val("<?php echo $dst_end; ?>");
					$("#auto_dst_start").val("<?php echo $dst_start; ?>");
		        	$('#auto_dst_end').val("<?php echo $dst_end; ?>");
		   		}else {
		   			$('#dst_start').val('');
		        	$('#dst_end').val('');
					$('#auto_dst_start').val('');
		        	$('#auto_dst_end').val('');
		   		}
		    });	
		});
	});
	system.init();
	$('#notify_user_1').chosen({width: "200px"}); 
	$('#notify_user_2').chosen({width: "200px"}); 
</script>

