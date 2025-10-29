var account = {
	savePassword : function(){
		var old = $('#old').val();
		var newPass = $('#new').val();
		var confirm = $('#confirm').val();
		
		$.post('/sysconfig/do_save_password', {
			old : old,
			new_pass : newPass,
			confirm  : confirm
		}, function(data){
			if(data.code == 0){
				showMsg(data.msg, 'success');
			}else{
				showMsg(data.msg, 'error');
			}
		},'json');
	}
}
