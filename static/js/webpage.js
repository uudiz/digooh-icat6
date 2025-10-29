var webpage = {
	refresh : function(){
		showLoading();
		//刷新当前页面信息
		$.get('/webpage/refresh?t='+new Date().getTime(), function(data){
			$('.wrap').html(data);
			hideLoading();
			tb_init('a.thickbox, area.thickbox, input.thickbox');//pass where to apply thickbox
		});
	},
	doSave : function(){
		var name = $('#name').val();
		var url  = $('#url').val();
		var descr = $('#descr').val();
		var id = $('#id').val();
		var type = $('#type').val();
		
		if(id == undefined || id == ''){
			id = 0;
		}

		$.post('/webpage/do_save?t='+new Date().getTime(),{
			name:name,
			descr:descr,
			url:url,
			id : id,
			type: type
		},function(data){
			if(data.code != 0){
				showFormMsg(data.msg,'error');
				
			}else{
				showFormMsg(data.msg,'success');
				
				setTimeout(function(){
					tb_remove();
					webpage.refresh();
				},500);
			}
		},'json');
	},
	remove : function(id, msg){
		if(confirm(msg)){
			$.post('/webpage/do_delete?t='+new Date(),{id:id},function(data){
				if(data.code != 0){
					showFormMsg(data.msg,'error');
				}else{
					showFormMsg(data.msg,'success');
					setTimeout(function(){
						tb_remove();
						webpage.refresh();
					},200);
				}
			},'json');
		}
	}
};
