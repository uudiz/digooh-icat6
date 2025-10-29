/**
 * @author User
 */

/**
 * 用户表单处理
 */
var region = {
	formId : 'region-form',
	
	doSave : function(){
		var id = $('#id').val();
		var name = $('#name').val();
		if(id == undefined){
			id = 0;
		}
		if (name.indexOf("&") >= 0 || name.indexOf("<") >= 0 || name.indexOf(">") >= 0 || name.indexOf("'") >= 0 || name.indexOf("\\") >= 0 || name.indexOf("%") >= 0) {
			showFormMsg("Special symbols (& < > ' \\ %) are not allowed in the region name.", 'error');
            return false;
        }
		$.post('/region/do_save',{
				name : name,
				descr: $('#descr').val(),
				id : id,
			},
			function(data){
				if(data.code != 0){
					$('#validateTips').html('<div>'+data.msg+'</div>').addClass('error');
				}else{
					$('#validateTips').html('<div>'+data.msg+'</div>').addClass('success');
					
					setTimeout(function(){
						//remove
						tb_remove();
						//refresh
						region.refresh();
					},100);
				}
			}, 'json');
	},
	init : function(){	
	},
	destory : function(){
			var uf = $('#' + this.formId);
			uf.parent().remove();
			uf.remove();
		
	},
	destoryFormDialog : function(){
		$('#' + this.formId).dialog('destory');
		this.destory();
	},
	refresh : function(){
		showLoading();
		$.get('/region/refresh?t='+new Date().getTime(), function(data){
			$('.wrap').html(data);
			hideLoading();
			//reinit this box~
			tb_init('a.thickbox, area.thickbox, input.thickbox');//pass where to apply thickbox
		});
	},
	page : function(curpage, orderItem, order){
		showLoading();
		window.location.href='/region/index/'+curpage+"/"+orderItem+"/"+order;
	},
	remove : function(id, msg){
		if(confirm(msg)){
			var req = {
					  id:id
					  }
			$.post('/region/do_delete',req, function(data){
				if(data.code == 0){
					showMsg(data.msg,'success');
					region.refresh();
					setTimeout(hideMsg, 1000);
					
				}else{
					showMsg(data.msg, 'error');
				}
			},'json');
		}
	}
	
};