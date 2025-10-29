/**
 * @author User
 */

/**
 * 用户表单处理
 */
var g = {
	formId : 'group-form',
	
	doSave : function(){
		var id = $('#id').val();
		var name = $('#name').val();
		var type = $('#type').val();
		if(id == undefined){
			id = 0;
		}
		if (name.indexOf("&") >= 0 || name.indexOf("<") >= 0 || name.indexOf(">") >= 0 || name.indexOf("'") >= 0 || name.indexOf("\\") >= 0 || name.indexOf("%") >= 0) {
			showFormMsg("Special symbols (& < > ' \\ %) are not allowed in the group name.", 'error');
            return false;
        }
		$.post('/group/do_save',{
				name : name,
				descr: $('#descr').val(),
				//download_strategy_id : $('#downloadStrategyId').val(), // download_time  delete
				view_config_id : $('#viewConfigId').val(),
				timer_config_id : $('#timerConfigId').val(),
				id : id,
				type : type
			},
			function(data){
				/*if(data.code != 0){
					$('.validateTips').html(data.msg);
				}else{
					g.destoryFormDialog();
					gl.addItem(json);
				}*/
				if(data.code != 0){
					$('#validateTips').html('<div>'+data.msg+'</div>').addClass('error');
				}else{
					$('#validateTips').html('<div>'+data.msg+'</div>').addClass('success');
					
					setTimeout(function(){
						//remove
						tb_remove();
						//refresh
						g.refresh();
					},100);
				}
			}, 'json');
	},
	init : function(){
		//this.form = $('#user-form');	
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
		$.get('/group/refresh?t='+new Date().getTime(), function(data){
			$('.wrap').html(data);
			hideLoading();
			//reinit this box~
			tb_init('a.thickbox, area.thickbox, input.thickbox');//pass where to apply thickbox
		});
	},
	page : function(curpage, orderItem, order){
		showLoading();
		window.location.href='/group/index/'+curpage+"/"+orderItem+"/"+order;
	},
	remove : function(id, msg){
		if(confirm(msg)){
			var req = {
					  id:id
					  }
			$.post('/group/do_delete',req, function(data){
				if(data.code == 0){
					showMsg(data.msg,'success');
					g.refresh();
					setTimeout(hideMsg, 1000);
					
				}else{
					showMsg(data.msg, 'error');
				}
			},'json');
		}
	}
	
};