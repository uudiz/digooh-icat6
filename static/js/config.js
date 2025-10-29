var cfg = {
	initView : function(){
		$('#startTime').focus(function(event){
				WdatePicker({skin:'default',dateFmt:'yyyy-MM-dd HH:mm',lang:curLang});
		});
		
		$('#endTime').focus(function(event){
				WdatePicker({skin:'default',dateFmt:'yyyy-MM-dd HH:mm',lang:curLang});
		});
	},
	refreshView : function(){
		showLoading();
		$.get('/config/refresh_views?t='+new Date().getTime(), function(data){
			$('.wrap').html(data);
			hideLoading();
			//reinit this box~
			tb_init('a.thickbox, area.thickbox, input.thickbox');//pass where to apply thickbox
		});
	},
	saveView : function(){
		var id = $('#id').val();
		var descr = $('#descr').val();
		var name = $('#name').val();
		if(name == ''){
			$('#errorName').show();
			return;
		}else{
			$('#errorName').hide();
		}
		
		var startTime = $('#startTime').val();
		if(startTime == ''){
			$('#errorStartTime').show();
			return;
		}else{
			$('#errorStartTime').hide();
		}
		
		var endTime   = $('#endTime').val();
		if(endTime == ''){
			$('#errorEndTime').show();
			return;
		}else{
			$('#errorEndTime').hide();
		}
		
		var brightness= $('#brightness').val();
		if(brightness == '' || parseInt(brightness) == NaN){
			$('#errorBrightness').show();
			return;
		}else{
			$('#errorBrightness').hide();
		}
		
		var saturation= $('#saturation').val();
		if(saturation == '' || parseInt(saturation) == NaN){
			$('#errorSaturation').show();
			return;
		}else{
			$('#errorSaturation').hide();
		}
		
		var contrast  = $('#contrast').val();
		if(contrast == '' || parseInt(contrast) == NaN){
			$('#errorContrast').show();
			return;
		}else{
			$('#errorContrast').hide();
		}
		
		$.post('config/do_save_view', {
			id : id,
			name : name,
			descr: descr,
			start_datetime:startTime,
			end_datetime : endTime,
			brightness : brightness,
			saturation : saturation,
			contrast   : contrast
			},
			function(data){
				if(data.code == 0){
					showFormMsg(data.msg,'success');
					setTimeout(function(){
						cfg.refreshView();
						tb_remove();
					}, 1000)
				}else{
					showFormMsg(data.msg, 'error');
				}
			}, 
			'json');
		
	},
	removeView : function(id, msg){
		if(confirm(msg)){
			var req = {
					  id:id
					  }
			$.post('/config/do_delete_view',req, function(data){
				if(data.code == 0){
					showMsg(data.msg,'success');
					cfg.refreshView();
					setTimeout(hideMsg, 1000);
				}else{
					showMsg(data.msg, 'error');
				}
			},'json');
		}
	},
	refreshDownload : function(){
		showLoading();
		$.get('/config/refresh_downloads?t='+new Date().getTime(), function(data){
			$('.wrap').html(data);
			hideLoading();
			//reinit this box~
			tb_init('a.thickbox, area.thickbox, input.thickbox');//pass where to apply thickbox
		});
	},
	addDownloadRow : function(){
		var table = $('#downloadTimeConfig');
		if(table[0].rows.length > 10){
			return;
		}
		
		$.get('config/add_download_row?t='+new Date().getTime(), function(data){
			table.append(data);
		});
	},
	removeDownload : function(id, msg){
		if(confirm(msg)){
			var req = {
					  id:id
					  }
			$.post('/config/do_delete_download',req, function(data){
				if(data.code == 0){
					showMsg(data.msg,'success');
					cfg.refreshDownload();
					setTimeout(hideMsg, 1000);
				}else{
					showMsg(data.msg, 'error');
				}
			},'json');
		}
	},
	removeDownloadRow : function(obj){
		var $this = $(obj);
		$this.parent().parent().remove();
	},
	saveDownload : function(){
		var id = $('#id').val();
		var descr = $('#descr').val();
		var name = $('#name').val();
		
		if(name == ''){
			$('#errorName').show();
			return;
		}else{
			$('#errorName').hide();
		}
		
		var startHour = document.getElementsByName('startHour');
		var startMinute = document.getElementsByName('startMinute');
		var endHour = document.getElementsByName('endHour');
		var endMinute = document.getElementsByName('endMinute');
		var startTime = new Array();
		var endTime   = new Array();
		var startTimeValue = new Array();
		var endTimeValue   = new Array();
		
		var checkError = false;
		
		//set value and check rang
		for(var i =0; i < startHour.length; i++){
			startTimeValue.push(parseInt(startHour[i].value + startMinute[i].value, 10));
			endTimeValue.push(parseInt(endHour[i].value + endMinute[i].value, 10));
			
			//compare start hour and end hour
			if(startTimeValue[i] >= endTimeValue[i]){
				this.showDownloadTimeRangError(startHour, startMinute, endHour, endMinute, i);
				checkError = true;
				break;
			}
			
			startTime.push(startHour[i].value+':'+startMinute[i].value);
			endTime.push(endHour[i].value+':'+endMinute[i].value);
		}
		if(checkError){
			return;
		}
		
		
		//check crosss
		for (var i = 0; i < startTimeValue.length; i++) {
			for(var j = i+1; j < startTimeValue.length; j++){
				if((startTimeValue[i]<=startTimeValue[j] && endTimeValue[i]>=endTimeValue[j])
				   ||(startTimeValue[i]<startTimeValue[j] && endTimeValue[i] < endTimeValue[j] && endTimeValue[i] > startTimeValue[j])
				   ||(startTimeValue[i]>=startTimeValue[j] && endTimeValue[i]<=endTimeValue[j])
				   ||(startTimeValue[i]>=startTimeValue[j] && endTimeValue[i]>endTimeValue[j] && startTimeValue[i] < endTimeValue[j])){
					this.showDownloadTimeCrossError(startHour, startMinute, endHour, endMinute, i, j);
					checkError = true;
					break;
				}
			}
			
			if(checkError){
				break;
			}
		}
		if(checkError){
			return;
		}
		
		$.post('/config/do_save_download', {
			id : id,
			name : name,
			descr : descr,
			start_time: startTime,
			end_time : endTime
		}, function(data){
			if(data.code == 0){
					showFormMsg(data.msg,'success');
					setTimeout(function(){
						cfg.refreshDownload();
						tb_remove();
					}, 1000)
				}else{
					showFormMsg(data.msg, 'error');
				}
		},'json');
			
	},
	showDownloadTimeRangError : function(startHour, startMinute, endHour, endMinute, i){
		startHour[i].style.backgroundColor='red';
		startMinute[i].style.backgroundColor='red';
		endHour[i].style.backgroundColor='red';
		endMinute[i].style.backgroundColor='red';
		$('#rangError').show();
		setTimeout(function(){
			startHour[i].style.backgroundColor='';
			startMinute[i].style.backgroundColor='';
			endHour[i].style.backgroundColor='';
			endMinute[i].style.backgroundColor='';
			$('#rangError').hide();
		}, 1000);
	},
	showDownloadTimeCrossError : function(startHour, startMinute, endHour, endMinute, i, j){
		startHour[i].style.backgroundColor='red';
		startMinute[i].style.backgroundColor='red';
		endHour[i].style.backgroundColor='red';
		endMinute[i].style.backgroundColor='red';
		
		startHour[j].style.backgroundColor='red';
		startMinute[j].style.backgroundColor='red';
		endHour[j].style.backgroundColor='red';
		endMinute[j].style.backgroundColor='red';
		
		$('#conflictError').show();
		setTimeout(function(){
			startHour[i].style.backgroundColor='';
			startMinute[i].style.backgroundColor='';
			endHour[i].style.backgroundColor='';
			endMinute[i].style.backgroundColor='';
			
			startHour[j].style.backgroundColor='';
			startMinute[j].style.backgroundColor='';
			endHour[j].style.backgroundColor='';
			endMinute[j].style.backgroundColor='';
		
			$('#conflictError').hide();
		}, 1000);
	},
	changeType : function(obj){
		if (obj.value == 0) {
			this.changeWeekStatus(0, true);
			for(var i = 1; i <= 7; i++){
				this.changeWeekStatus(i, false);
			}
		}else{
			this.changeWeekStatus(0, false);
			for(var i = 1; i <= 7; i++){
				this.changeWeekStatus(i, true);
			}
		}
	},
	initTimer : function(){
		//初始化编辑
	},
	changeWeekStatus : function(weekCode, enable){
		var wdo = $('#wholedayoff'+weekCode);
		var wdoischecked = false;
		

		if(enable){

			wdo.attr('disabled',false);
		}else{

			wdo.attr('disabled',true);
		}
		for(var i = 0; i < 3; i++){
			var status = $('#status'+weekCode+(i+1));
			var startHour = $('#startHour'+weekCode+(i+1));
			var startMinute = $('#startMinute'+weekCode+(i+1));
			var shutdownHour = $('#shutdownHour'+weekCode+(i+1));
			var shutdownMinute = $('#shutdownMinute'+weekCode+(i+1));
			if(enable){
				
				if(wdo.attr('checked')){
					status.attr('disabled', true);
					startHour.attr('disabled', true);
					startMinute.attr('disabled', true);
					shutdownHour.attr('disabled', true);
					shutdownMinute.attr('disabled', true);
				}else{
					status.attr('disabled', false);
					if(status.val() == 0){
						startHour.attr('disabled', false);
						startMinute.attr('disabled', false);
						shutdownHour.attr('disabled', false);
						shutdownMinute.attr('disabled', false);
					}else{
						startHour.attr('disabled', true);
						startMinute.attr('disabled', true);
						shutdownHour.attr('disabled', true);
						shutdownMinute.attr('disabled', true);
					}
				}
			}else{
				status.attr('disabled', true);
				startHour.attr('disabled', true);
				startMinute.attr('disabled', true);
				shutdownHour.attr('disabled', true);
				shutdownMinute.attr('disabled', true);
			}
		}
	},
	changeStatus : function(obj, weekCode, row){
		var status = $('#status'+weekCode+(row));
		var startHour = $('#startHour'+weekCode+(row));
		var startMinute = $('#startMinute'+weekCode+(row));
		var shutdownHour = $('#shutdownHour'+weekCode+(row));
		var shutdownMinute = $('#shutdownMinute'+weekCode+(row));
			
		if(obj.value == 0){
			startHour.attr('disabled', false);
			startMinute.attr('disabled', false);
			shutdownHour.attr('disabled', false);
			shutdownMinute.attr('disabled', false);
		}else{
			startHour.attr('disabled', true);
			startMinute.attr('disabled', true);
			shutdownHour.attr('disabled', true);
			shutdownMinute.attr('disabled', true);
		}
	},
	refreshTimer : function(){
		showLoading();
		var name = $('#filter').val();
				
		$.get('/config/refresh_timers?name='+name+'&t='+new Date().getTime(), function(data){
			$('#layoutContent').html(data);
			hideLoading();
		});
	},
	goTimerList : function(){
		location.href="/config/timers";
	},
	saveTimer : function(){

		var id = $('#id').val();
		var name = $('#name').val();
		var descr = $('#descr').val();
		var type  = $('input:radio[name="type"]:checked').val();
		if(name == ''){
			$('#errorName').show();
			return;
		}else{
			$('#errorName').hide();
		}
		var weekId = new Array();
		var status = new Array();
		var startTime = new Array();
		var endTime   = new Array();
		var offwds = new Array();
		var j = 0;
		for(var w = 0; w < 8; w++){
			if(w>0){
				var wdo = $('#wholedayoff'+w);
				if(wdo.attr('checked')){
					offwds[j++]=w;
				}
			}
			weekId[w] = new Array();
			status[w] = new Array();
			startTime[w] = new Array();
			endTime[w]  = new Array();
			for (var i = 0; i < 3; i++) {
				var wid = $('#weekId'+w+(i+1));
				var st = $('#status'+w+(i+1));
				var startHour = $('#startHour'+w+(i+1));
				var startMinute = $('#startMinute'+w+(i+1));
				var shutdownHour = $('#shutdownHour'+w+(i+1));
				var shutdownMinute = $('#shutdownMinute'+w+(i+1));
				
				weekId[w][i]=wid.val();
				status[w][i]=st.val();
				startTime[w][i]=startHour.val()+':'+startMinute.val();
				endTime[w][i]=shutdownHour.val()+':'+shutdownMinute.val();
			}
		}
		$.post('/config/do_save_timer', {
			id : id,
			name : name,
			type : type,
			descr: descr,
			week_id : weekId,
			status : status,
			start_time : startTime,
			end_time : endTime,
			off_weekdays: offwds.join(',')
		},function(data){
			//console.log(data);
			if(data.code == 0){
				showFormMsg(data.msg,'success');
				setTimeout(function(){
					cfg.goTimerList();
				}, 1000)
			}else{
				showFormMsg(data.msg, 'error');
			}
		}, 'json');
	},
	removeTimer : function(id, msg){
		if(confirm(msg)){
			var req = {
					  id:id
					  }
			$.post('/config/do_delete_timer',req, function(data){
				if(data.code == 0){
					showMsg(data.msg,'success');
					cfg.goTimerList();
					setTimeout(hideMsg, 1000);
				}else{
					showMsg(data.msg, 'error');
				}
			},'json');
		}
	},
	timerpage : function(curpage, orderItem, order){
		//.showLoading();
		//window.location.href='/tag/index/'+curpage+"/"+orderItem+"/"+order;
		showLoading();
		var name = $('#filter').val();
		$.get('/config/refresh_timers/'+curpage+"/"+orderItem+"/"+order + '?name='+name, function(data){
			$('#layoutContent').html(data);
			hideLoading();
			//reinit this box~
		//	tb_init('td > a.thickbox');
		});	
	},
	
	
};
