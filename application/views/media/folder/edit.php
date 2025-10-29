<head>
<link rel="stylesheet" href="/static/css/jquery/jquery-ui.min.css" />
<link rel="stylesheet" href="/static/css/jquery/chosen.min.css" />

<script type="text/javascript" src="/static/js/jquery-1.8.3.min.js" ></script>
<script src='/static/js/jquery/jquery-ui-latest.js'></script>
<script src='/static/js/jquery/chosen.jquery.min.js'></script>

</head>

<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<table  class="from-panel">
	<tbody>
		<tr>
			<td width="60">
				<?php echo lang('name'); ?>
			</td>
			<td>
				<input type="text" id="name" name="name" style="width:300px;" value="<?php echo $folder->name;?>"/>
			</td>
			<td>
				<div class="error" id="errorName" style="display:none;">
					<?php echo lang('warn.folder.name');?>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo lang("desc");?>
			</td>
			<td>
				<textarea name="descr" id="descr"  rows="2" style="width:300px;"><?php echo $folder->descr;?></textarea>
			</td>
			<td>&nbsp;</td>
		</tr>
		
			<tr>
				<td><?php echo lang('tag');?></td>
				<td >
					<select  data-placeholder=" " id="jquery-tagbox-select-options" class="chosen-select tag-input-style" multiple>
						<option value="0"></option>
						<?php foreach($tags as $tag):?>
							<option value="<?php echo $tag->id;?>" <?php $sel_tags =explode(',',$folder->tags); if(is_array($sel_tags) && in_array($tag->id, $sel_tags)):?>selected<?php endif;?>><?php echo $tag->name;?></option>
						<?php endforeach;?>
					</select>  
				</td>
			</tr>
			
			<tr>
				<td><?php echo lang('playtime');?></td>
				<td>
					<input type="text" style="width:80px" id="playTime" name="playTime" class="time-input"
					value="<?php echo $folder->play_time; ?>"/>
					<?php echo " ".lang('playtime.format'); ?> 
					(Image only)
				</td>			
			</tr>				
					
				
			<tr>
					<td><input type="checkbox" id="dateFlag" name="dateFlag" <?php if($folder->date_flag){echo 'checked="checked" value="1"';}else {echo 'value="0"';}?> /><?php echo lang('date.range')?></td>
					<td >
								<input type="text" style="width:90px;" id="startDate" name="startDate" readonly="readonly" class="date-input" value="<?php echo $folder->start_date;?>"  >
								<em><?php echo lang('to');?></em>
								<input type="text" style="width:90px;" id="endDate" name="endDate" readonly="readonly" class="date-input"  value="<?php echo $folder->end_date;?>" >
					</td>
			</tr>	
									
		
	</tbody>
</table>
<p class="btn-center">
	<input type="hidden" id="id" name="id" value="<?php echo $folder->id;?>" />
 	<a class="btn-01" href="javascript:void(0);" onclick="<?php echo $folder_target?>.saveFolder(this);"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>
<p>
	* Once saved, all file tags under this folder will be set to current tag.
</p>
<script type="text/javascript">
	//cfg.initDownload();
</script>


<script type="text/javascript">
   $(document).ready(function() {
	   $('.chosen-select').chosen({width: "300px"}); 
	   
   		if(document.getElementById('dateFlag').value=="0"){
   			$('#startDate').datepicker('destroy').addClass('gray');
   			$('#endDate').datepicker('destroy').addClass('gray');
   		}else if(document.getElementById('dateFlag').value=="1"){
   			var sd = $('#startDate');
			var ed = $('#endDate');
			sd.datepicker({
				 changeMonth: true,
			     changeYear: true,
  				 dateFormat: 'yy-mm-dd'
				}).removeClass('gray');
				
					 ed.datepicker({
   			dateFormat: 'yy-mm-dd'
					 }).removeClass('gray'); 
   		}
   });         	    
           	       
	$(function() {
 				$('#dateFlag').click(function() {
 					var sd = $('#startDate');
 					var ed = $('#endDate');
	    		$('input:checkbox[id="dateFlag"]').each(function(){
		            if (this.checked) {
      					sd.datepicker({
 					 	changeMonth: true,
			    	 	changeYear: true,     								
               				 dateFormat: 'yy-mm-dd'
            				}).removeClass('gray');
            				
     						ed.datepicker({
     						changeMonth: true,
			     			changeYear: true,
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
    
	    });
</script>

