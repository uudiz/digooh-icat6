var folder = {
	page : function(curpage, orderItem, order){
		showLoading();
		//刷新当前页面信息
		$.get('/folder/refresh/'+curpage+'/'+orderItem+'/'+order+'?t='+new Date().getTime(), function(data){
			hideLoading();
			$('#layoutContent').html(data);
			//reinit this box~
			tb_init('#layoutContent a.thickbox');
		});
	},
	refresh : function(){

/*
		$.ajax({
 		 crossOrigin: true,
 		 type:"GET",
 		 url: 'http://cms.digooh.com:8081/api/v1/players',
 		 beforeSend: function(xhr) {
    		xhr.setRequestHeader('Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9jbXMuZGlnb29oLmNvbTo4MDgxIiwiaWF0IjoxNTkwNDgzMzY0LCJleHAiOjE2MjIwMTkzNjQsIm5iZiI6MTU5MDQ4MzM2NCwianRpIjoiRExsTWlJUTRGNFBCaDRBRyIsInN1YiI6MzEsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.Sd4of_2yrobmZ-yLEGRJeuefJ9jHMhN738BtrPDCObI');
  		 },
 		 success: function(data) {
			console.log(data);
 		 }
		});
		*/

		folder.page(1, 'name', 'asc');

	},
	remove : function(id, msg){
		if(confirm(msg)){
			$.post('/folder/do_delete', {'id':id}, function(data){
				if(data.code == 1){
					showMsg(data.msg, 'error');
				}else{
					showMsg(data.msg, 'success');
					setTimeout(function(){
						hideMsg();
						folder.refresh();
					}, 1000);
				}
			},'json');
		}
	},

	saveFolder : function(obj){
		var name = $('#name').val();
		var descr = $('#descr').val();
		var id = $('#id').val();
		if(name == ''){
			$('#errorName').show();
			return;
		}
		
		if(id == undefined){
			id = 0;
		}
		$('#errorName').hide();
		var btn = $(obj);
		btn.attr("disabled","disabled")
		console.log($('#jquery-tagbox-select-options').val());
		$.post('/folder/do_save_folder',{
			name  : name,
			descr : descr,
	        tags_select: String($('#jquery-tagbox-select-options').val()),
            date_flag: $('#dateFlag').val(),
            startDate: $('#startDate').val(),
            endDate: $('#endDate').val(),
            playTime: $('#playTime').val(),
            parent_id:$('#parent_id').val(),
			id : id
		}, function(data){
			if(data.code == 0){
				showFormMsg(data.msg, 'success');
				setTimeout(function(){
					tb_remove();
					folder.refresh();
				}, 200);
				
				return;
			}else{
				showFormMsg(data.msg, 'error');
			}
			btn.attr("disabled","false")
		},'json');
	},

};
