var rss = {
	refresh : function(){
		showLoading();
		//刷新当前页面信息
		$.get('/rss/refresh?t='+new Date().getTime(), function(data){
			$('.wrap').html(data);
			hideLoading();
			//reinit this box~
			tb_init('a.thickbox, area.thickbox, input.thickbox');//pass where to apply thickbox
		});
	},
	doSave : function(){
		var name = $('#name').val();
		var url  = $('#url').val();
		var descr = $('#descr').val();
		var id = $('#id').val();
		var interval = $('#interval').val();
		var type = $('#type').val();
		
		if(id == undefined || id == ''){
			id = 0;
		}
		/**
		if(name.length == 0){
			$('#errorName').fadeIn();
			return false;
		}else{
			$('#errorName').fadeOut();
		}
		
		if(url.length == 0){
			$('#errorUrl').fadeIn();
			return false;
		}else{
			$('#errorUrl').fadeOut();
		}
		*/
		$.post('/rss/do_save?t='+new Date().getTime(),{
			name:name,
			descr:descr,
			url:url,
			interval:interval,
			id : id,
			type: type
		},function(data){
			if(data.code != 0){
				showFormMsg(data.msg,'error');
				
			}else{
				showFormMsg(data.msg,'success');
				
				setTimeout(function(){
					//remove
					tb_remove();
					//refresh
					rss.refresh();
				},500);
			}
		},'json');
	},
	remove : function(id, msg){
		if(confirm(msg)){
			$.post('/rss/do_delete?t='+new Date(),{id:id},function(data){
				if(data.code != 0){
					showFormMsg(data.msg,'error');
				}else{
					showFormMsg(data.msg,'success');
					setTimeout(function(){
						//remove
						tb_remove();
						//refresh
						rss.refresh();
					},200);
				}
			},'json');
		}
	}
};
