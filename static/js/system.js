var system = {
	init : function(){
		this.addDstCalendar();
		this.dstChange();	
	},
	dstChange : function(){
		$("#dst").change(function(){
			if ($('#dst').is(":checked")) {
				system.addDstCalendar();
			}else{
				system.removeDstCalendar();
			}
		});
	},
	removeDstCalendar : function() {
		$("#dst_start").datepicker("destroy");
		$("#dst_end").datepicker("destroy");
	},
	addDstCalendar : function(){
		if ($('#dst').is(":checked")) {
			$("#dst_start").datepicker({
				showOn: "button",
				buttonImage: "images/datePicker.gif",
				buttonImageOnly: true,
			    changeMonth: true,
			    changeYear: true,
				dateFormat : 'yy-mm-dd'
			});
			$("#dst_end").datepicker({
				showOn: "button",
				buttonImage: "images/datePicker.gif",
				buttonImageOnly: true,
			    changeMonth: true,
			    changeYear: true,
				dateFormat : 'yy-mm-dd'
			});
		}
	},
	doSave : function(){
		//var enableDst = $('#dst').attr('checked');
		var id = $('#id').val();
		//var cityCode = $('#cityCode').val();
		var weatherFormat = $('#weatherFormat').val();
		//var email = $('#email').val();
		//var email2 = $('#email2').val();
		var offlineEmailFlag=$('#offlineEmailFlag').attr('checked') ? 1 : 0;
		var offlineEmailFlag2=$('#offlineEmailFlag2').attr('checked') ? 1 : 0;
		var playbackEmailFlag=$('#playbackEmailFlag').attr('checked') ? 1 : 0;
		//var eventEmailFlag=$('#eventEmailFlag').attr('checked') ? 1 : 0;
		//var fit = $('#fit').val();
		if(id == undefined){
			id = 0;
		}
		$.post('/sysconfig/do_save',{
				weather_format : weatherFormat,
				offline_email_flag : offlineEmailFlag,
				playback_email_flag : playbackEmailFlag,
				offline_email_interval:$('#emailinterval').val(),
				colorsetting: $('#colorsetting').val(),
				id: id,
				offline_email_flag2 : offlineEmailFlag2,
				offline_email_interval2:$('#emailinterval2').val(),
				users_grp_1: $('#notify_user_1').val(),
				users_grp_2: $('#notify_user_2').val(),
			},
			function(data){
				if(data.code != 0){
					showFormMsg(data.msg,'error');
				}else{
					showFormMsg(data.msg,'success');
					setTimeout(function(){
						window.location.href = '/sysconfig/edit';
					}, 1000);
				}
				
			},'json');
	}
};
