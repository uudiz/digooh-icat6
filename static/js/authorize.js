/**
 * @author User
 */
var au = {
	page : function(curpage, orderItem, order){
		showLoading();
		$.get('/authorize/refresh/'+curpage+'/'+orderItem+'/'+order+'?t=' + new Date().getTime(), 
			function(data){
				$('#layoutContent').html(data);
				hideLoading();
			}
		);
	},
	refresh : function(){
		showLoading();
		$.get('/authorize/refresh?t=' + new Date().getTime(), 
			function(data){
				$('#layoutContent').html(data);
				hideLoading();
			}
		);
	},
	doSave: function(){
		var count = 1;
		var descr = $("#descr").val();
		var id = $("#id").val();
		if(id == undefined) {
			id = 0;
		}
		if(descr == undefined) {
			descr = '';
		}
		$.post('/authorize/do_save', {count: count, descr: descr, id: id}, function(data){
			if(data.code == 0){
				showMsg(data.msg,'success');
				if(id > 0) {
					tb_remove();
					au.refresh();
					setTimeout(hideMsg, 1000);
				}else {
					window.location="/authorize";
				}
			}else{
				showMsg(data.msg, 'error');
				tb_remove();
			}
		},'json');
    },
	remove : function(id, msg){
		if(confirm(msg)){
			var req = {
					  id:id
					  }
			$.post('/authorize/do_delete',req, function(data){
				if(data.code == 0){
					showMsg(data.msg,'success');
					window.location="/authorize";
					setTimeout(hideMsg, 1000);
				}else{
					showMsg(data.msg, 'error');
				}
			},'json');
		}
	},
	goback: function() {
		window.location="/authorize";
	}
};
