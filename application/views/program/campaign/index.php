<?php if ($auth >= 0):?>
<head>
<link rel="stylesheet" href="/static/css/alertify.core.css" />
<link rel="stylesheet" href="/static/css/alertify.default.css" />
<script src="/static/js/alertify.min.js" type="text/javascript" charset="utf-8"></script>


</head>

<div class="add-panel">
<?php if ($auth > 1):?>	
	<a href="/campaign/add?width=400&height=300" class="add" id="create" title="<?php echo lang('campaign.new');?>"><?php echo lang('create');?></a>
<?php endif?>
</div>
<a href="/campaign/calendar_view?width=1024&height=728" style="float:right" class="thickbox" title="Calendar"><img src="/images/icons/24-date.png" title="Calendar"/></a>
<?php if ($auth > 1):?>
<a href="javascript:void(0);" onclick="refresh_all_campaigns();" style="float:right" ><img src="/images/icons/24-refresh.png" title="Refresh"/></a>
<?php endif;?>
<?php endif;?>

<div class="clear"></div>

<h1 class="tit-01" ><?php echo lang('campaign');?>
	<div class="filter" style="width:90%;overflow: hidden;">
		
		<label style="margin-left:10px;"><?php echo lang('filter.by');?>:</label>
		<input type="text" name="filter" id="filter" style="width:120px; margin-left:4px;">
		
		<label style="margin-left:10px;"><?php echo lang('priority');?>:</label>
		<select id="filterPriority" style="width: 150px; margin:0px 4px;" onchange="campaign.refresh();">
			<option value="-1"><?php echo lang('all');?></option>
			<option value="1"><?php echo lang('priority.high');?></option>
			<option value="2"><?php echo lang('priority.low');?></option>
			<?php if ($auth==5):?>
			<option value="5"><?php echo lang('priority.reservation');?></option>
			<?php if ($this->config->item('campaign_with_tags')):?>
				<option value="4"><?php echo lang('priority.trail');?></option>
			<?php endif?>		
			<?php endif?>
			<option value="6"><?php echo lang('priority.simple');?></option>	
			<option value="3"><?php echo lang('priority.fillin');?></option>

		</select>
		
		<?php if ($auth > 1):?>
		<label style="margin-left:10px;"><?php echo lang('criteria');?>:</label>
		<select id="filterCriteria" style="width: 150px; margin:0px 4px;" onchange="campaign.refresh();">
			<option value="-1"><?php echo lang('all');?></option>
			<?php if (isset($criterias)):?>
				<?php foreach ($criterias as $cri):?>
					<option value="<?php echo $cri->id;?>"><?php echo $cri->name;?></option>
				<?php endforeach;?>
			<?php endif;?> 

		</select>
			<?php if ($this->config->item('campaign_with_tags')):?>
			<label style="margin-left:10px;"><?php echo lang('tag');?>:</label>
			<select id="filterTag" style="width: 150px; margin:0px 4px;" onchange="campaign.refresh();">
				<option value="-1"><?php echo lang('all');?></option>
				<?php if (isset($tags)):?>
					<?php foreach ($tags as $cri):?>
						<option value="<?php echo $cri->id;?>"><?php echo $cri->name;?></option>
					<?php endforeach;?>
				<?php endif;?> 

			</select>
		<?php endif?>
		<?php if ($this->config->item("cam_with_player")):?>
			<label style="margin-left:10px;"><?php echo lang('player');?>:</label>
			<select id="filterPlayer" style="width: 150px; margin:0px 4px;" onchange="campaign.refresh();">
				<option value="-1"><?php echo lang('all');?></option>
				<?php if (isset($players)):?>
					<?php foreach ($players as $cri):?>
						<option value="<?php echo $cri->id;?>"><?php echo $cri->name;?></option>
					<?php endforeach;?>
				<?php endif;?> 

			</select>
		<?php endif;?>

		<?php endif;?>
		<label for='withexpired' style="margin-left:10px;vertical-align:middle;""><?php echo lang('with.expired');?></label>
		<input type="checkbox" style="vertical-align:middle;" name="withexpired" id="withexpired" onclick="campaign.refresh();"/>
			
		<label for="checkDate" style="margin-left:10px;vertical-align:middle;"><?php echo lang('date.range');?></label>
		<input type="checkbox" style="vertical-align:middle;" id="checkDate" value="0" onclick="checkboxOnclick(this);"/>
		

		<input type="text" readonly="readonly" id="startDate" class="date-input" style="width:90px;" /> -
		<input type="text" readonly="readonly" id="endDate" class="date-input" style="width:90px;"/>

		
		<a href="javascript:void(0);" style="margin-left:10px; " class="btn-go" onclick="campaign.refresh();"><label><?php echo lang('filter');?></label></a>
	</div>
    <span></span>
</h1>
<div id="layoutContent">
<?php
if (isset($body_view)) {
    $this->load->view($body_view);
}
?>
</div>


<script>
	function refresh_all_campaigns(){
	
	alertify.confirm('<?php echo lang('campaign.refresh.msg')?>', function (e) {
		if(e){
			showOverlayLoading();
			$.get("/campaign/do_refresh_all_campaigns",  function(data){
				//console.log(data);
				hideOverlayLoading();
			    if (data.code == 0) {
			    //	document.getElementById('infoid').value = $("#infoid").val()+data.msg+'\n';
			    	//document.getElementById('infoid').value =data.msg;
			    	alertify.alert('<?php echo lang('campaign.refresh.success')?>', function (e) {
			    		  setTimeout(function(){
			    	            window.location.href = '/campaign/index';
			    	        }, 1000);
				
					});
			    }
			    else {
			    		alertify.alert(data.msg, function (e) {
			    		window.location.href = '/campaign/index';
				
					});
			    }
			  
			}, 'json');
		}
	});
	
	};
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
	};

	document.onkeyup = function(event){
		if (event.keyCode == 13) {
			campaign.refresh();
		}
	};

	function checkboxOnclick(checkbox){

		if ( checkbox.checked == true){
			  $('#startDate').attr("disabled", false);
			  $('#endDate').attr("disabled", false);
		}else{
			$('#startDate').attr("disabled", true);
			$('#endDate').attr("disabled", true);
		}

	}

	$(document).ready(function() {
		  $('#filterCriteria').select2();
		   $('#filterPlayer').select2();
		   $('#filterTag').select2();
		  $('#startDate').datepicker({
				 dateFormat: 'yy-mm-dd'
				}).removeClass('gray');
		  $('#endDate').datepicker({
				 dateFormat: 'yy-mm-dd'
				}).removeClass('gray');	
		  var date = new Date();
		  var enddate = new Date(); 
		  enddate.setMonth(enddate.getMonth()+1);
		  $('#startDate').val(date.Format("yyyy-MM-dd"));
		  $('#endDate').val(enddate.Format("yyyy-MM-dd"));
		  $('#startDate').attr("disabled", true);
		  $('#endDate').attr("disabled", true);
	});
	  
</script>