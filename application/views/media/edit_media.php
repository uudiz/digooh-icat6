<head>
<link rel="stylesheet" href="/static/css/jquery/chosen.min.css" />
<script src='/static/js/jquery/chosen.jquery.min.js'></script>
</head>

<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>

<form method="POST" id="cf" action="/media/do_save" >
	<table cellspacing="0" cellpadding="0" border="0" class="from-panel">	
		<tbody>
			<tr>
				<td width="100">
					<?php echo lang("media.name");?>
				</td>
				<td>
					<input style="width: 200px" type="text" id="name" name="name" class="text ui-widget-content ui-corner-all" value="<?php echo $media->name;?>"/>
				</td>
				<td>
					<div class="attention" id="errorName" style="display:none;">
						<?php echo lang('warn.media.name.empty');?>
					</div>
				</td>
			</tr>
			<?php if($media->source == $this->config->item('media_source_ftp')):?>
			<tr>
				<td>
					<?php echo lang("address");?>
				</td>
				<td>
					<span style="font-size:12px;">
					<?php
						//$url = substr($media->full_path, 6);
						//$pos = strpos($url, ':');
						//$prev = substr($url, 0, $pos);
						$pos = strrpos($media->full_path, "@");
						$path = substr($media->full_path, $pos + 1); 
						echo 'ftp://'.$path; 
					?>
					</span>
				</td>
				<td>
					<div class="attention" id="errorName" style="display:none;">
						<?php echo lang('warn.media.name.empty');?>
					</div>
				</td>
			</tr>
			<?php endif;?>
			<tr>
				<td>
					<?php echo lang("desc");?>
				</td>
				<td>
					<textarea style="width: 200px" name="descr" id="descr" class="ui-widget-content ui-corner-all" rows="2"><?php echo $media->descr;?></textarea>
				</td>
				<td>
					&nbsp;
				</td>
			</tr>

			<tr>
				<td><?php echo lang('tag');?></td>
				<td width="300">
					
				<select  data-placeholder=" " id="jquery-tagbox-select-options" class="chosen-select tag-input-style" multiple>
						<option value="0"></option>
						<?php foreach($tags as $tag):?>
							<option value="<?php echo $tag->id;?>" <?php $sel_tags =explode(',',$tagstr); if(is_array($sel_tags) && in_array($tag->id, $sel_tags)):?>selected<?php endif;?>><?php echo $tag->name;?></option>
						<?php endforeach;?>
					</select>  
				</td>
			</tr>
			
			<tr>
				<td><?php echo lang('playtime');?></td>
				<td>
					<input type="text"  id="playTime" name="playTime" class="time-input"
					value="<?php echo $media->play_time; ?>" <?php if($media->media_type == $this->config->item('media_type_video')) echo 'readonly="readonly" style="width:80px;background:#ddd;"'; else echo 'style="width:80px"';?>/>
					<?php echo " ".lang('playtime.format'); ?> 
				</td>			
			</tr>				
				
				<!--  	
			<tr>
				<td>
					<input type="checkbox" id="playcountFlag" name="playcountFlag" <?php if($media->play_count){echo 'checked="checked" value="1"';}else {echo 'value="0"';}?> /><?php echo lang('playcount')?>
				</td>
				
				<td>
						<input type="number" id="playcountid" name="playcountid" defaultValue="1" class="text ui-widget-content ui-corner-all" min="1" value="<?php if($media->play_count) echo $media->play_count; else echo "1"?>"
						<?php if($media->play_count){echo 'style="width:80px;"';}else {echo 'readonly="readonly" style="width:80px;background:#ddd;"';}?> />	
				</td>
			</tr> 
		-->	

				
			<tr>
					<td><input type="checkbox" id="dateFlag" name="dateFlag" <?php if($media->date_flag){echo 'checked="checked" value="1"';}else {echo 'value="0"';}?> /><?php echo lang('date.range')?></td>
					<td >
								<input type="text" style="width:80px;" id="startDate" name="startDate"  class="date-input" value="<?php echo $media->start_date;?>"  >
								<em><?php echo lang('to');?></em>
								<input type="text" style="width:80px;" id="endDate" name="endDate"  class="date-input"  value="<?php echo $media->end_date;?>" >
					</td>
			</tr>	
							

			<!-- 						
			<tr>
			<td width="65px;"><input type="checkbox" id="alldayFlag" name="alldayFlag" <?php if($media->all_day_flag){echo 'checked="checked" value="1"';}else {echo 'value="0"';}?> /><?php echo lang('all');?>&nbsp;<?php echo lang('day');?></td>
			<td>
				<input type="text" id="startTime" name="startTime"  class="time-input" value="<?php echo $media->start_time;?>" <?php if($media->all_day_flag){echo 'readonly="readonly" style="width:80px;background:#ddd;"';}else {echo 'style="width:80px;"';}?> />
				<em><?php echo lang('to');?></em>
				<input type="text" id="endTime" name="endTime"  class="time-input" value="<?php echo $media->end_time;?>" <?php if($media->all_day_flag){echo 'readonly="readonly" style="width:80px;background:#ddd;"';}else {echo 'style="width:80px;"';}?> />
			</td>
			</tr>
			 -->		
		
		</tbody>
	</table>
	<p class="btn-center">
		<input type="hidden" name="id" id="id" value="<?php echo $media->id;?>" />
		<a class="btn-01" href="javascript:void(0);" onclick="mediaLib.doSave();"><span><?php echo lang('button.save');?></span></a>
		<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
	</p>
</form>


<script type="text/javascript">
Date.prototype.Format = function(fmt)   
{ //author: meizz   
  var o = {   
    "M+" : this.getMonth()+1,                 //月份   
    "d+" : this.getDate(),                    //日   
    "h+" : this.getHours(),                   //小时   
    "m+" : this.getMinutes(),                 //分   
    "s+" : this.getSeconds(),                 //秒   
    "q+" : Math.floor((this.getMonth()+3)/3), //季度   
    "S"  : this.getMilliseconds()             //毫秒   
  };   
  if(/(y+)/.test(fmt))   
    fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));   
  for(var k in o)   
    if(new RegExp("("+ k +")").test(fmt))   
  fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));   
  return fmt;   
}

$(document).ready(function() {
	
	  if(!document.getElementById('dateFlag').checked){
 	   		var date = new Date();
 	   		var enddate = new Date(); 
 	   		enddate.setMonth(enddate.getMonth()+1);
			$('#startDate').val(date.Format("yyyy-MM-dd"));
 			$('#endDate').val(enddate.Format("yyyy-MM-dd"));
 			$('#startDate').datepicker('destroy').addClass('gray');
 			$('#endDate').datepicker('destroy').addClass('gray');

 		}	
	  else{
		  $('#startDate').datepicker({
			 	 dateFormat: 'yy-mm-dd'
				}).removeClass('gray');
		  $('#endDate').datepicker({
				 dateFormat: 'yy-mm-dd'
				}).removeClass('gray');	
	  }

	  $('.chosen-select').chosen({width: "300px"}); 	
    });
/*
$('#playcountFlag').click(function() {
	$('input:checkbox[id="playcountFlag"]').each(function(){
	if (this.checked) {
		$('#playcountid').removeAttr('readonly');
		$('#playcountid').css('background', '#fff');
		$('#playcountFlag').val(1);
	}
	else{
		$('#playcountid').attr('readonly','readonly');
		$('#playcountid').css('background', '#ddd');
	  $('#playcountFlag').val(0);
	}
	});

});
*/

$('#dateFlag').click(function() {
	var sd = $('#startDate');
	var ed = $('#endDate');
	$('input:checkbox[id="dateFlag"]').each(function(){
		if (this.checked) {
					sd.datepicker({
				 dateFormat: 'yy-mm-dd'
			}).removeClass('gray');
			
					 ed.datepicker({
			dateFormat: 'yy-mm-dd'
				 }).removeClass('gray'); 
		$('#dateFlag').val(1);
		}else {
			sd.datepicker('destroy').addClass('gray'); 	            			
			ed.datepicker('destroy').addClass('gray'); 	               
	    $('#dateFlag').val(0);
		}
	});	
});
$('#alldayFlag').click(function() {
	$('input:checkbox[id="alldayFlag"]').each(function(){
	if (this.checked) {
	  $('#startTime').attr('readonly','readonly');
	  $('#endTime').attr('readonly','readonly');
		$('#startTime').css('background', '#ddd');
					$('#endTime').css('background', '#ddd');
		$('#alldayFlag').val(1);
	}else {
		$('#startTime').removeAttr('readonly');
		$('#endTime').removeAttr('readonly');
	  $('#startTime').css('background', '#fff');
	  $('#endTime').css('background', '#fff');  
	    $('#alldayFlag').val(0);
	}
  });	
});	
	
</script>


