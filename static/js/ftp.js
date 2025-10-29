var ftp = {
    page: function(curpage, orderItem, order){
        showLoading();
        //刷新当前页面信息
        $.get('/ftp/refresh/' + curpage + '/' + orderItem + '/' + order + '?t=' + new Date().getTime(), function(data){
            hideLoading();
            $('.wrap').html(data);
            //reinit this box~
            tb_init('td > a.thickbox');
        });
    },
    refresh: function(){
        ftp.page(1, 'id', 'desc');
    },
    remove: function(id, msg){
        if (confirm(msg)) {
            $.post('/ftp/do_delete', {
                'id': id
            }, function(data){
                if (data.code == 1) {
                    showMsg(data.msg, 'error');
                }
                else {
                    showMsg(data.msg, 'success');
                    setTimeout(function(){
                        hideMsg();
                        ftp.refresh();
                    }, 1000);
                }
            }, 'json');
        }
    },
    checkFtp: function(){
        var id = $('#id').val();
		if(id == undefined){
			id = 0;
		}
        var profile = $('#profile').val();
        var server = $('#server').val();
        var port = $('#port').val();
        var pasv = $('#pasv').attr('checked') ? 1 : 0;
        var account = $('#account').val();
        var password = $('#password').val();
        var result = true;
        var tree = new Object();
        if (server == '') {
            $('#errorFtpServer').fadeIn();
            result = false;
        }
        else {
            $('#errorFtpServer').fadeOut();
        }
        if (port == '') {
            $('#errorFtpPort').fadeIn();
            result = false;
        }
        else {
            $('#errorFtpPort').fadeOut();
        }
        if (password.indexOf("@") >= 0 || password.indexOf(":") >= 0) {
			showFormMsg('Special symbols(@ and :) are prohibited in the password', 'error');
            result = false;
        }
        if (profile == '') {
            profile = server;
        }
        if (result) {
            return {
                id: id,
                profile: profile,
                server: server,
                port: port,
                pasv: pasv,
                account: account,
                password: password
            };
        }
        else {
            return false;
        }
        
        
    },
    saveFtp: function(obj){
        var data = this.checkFtp();
        if (data) {
            var btn = $(obj);
            btn.attr("disabled", "disabled")
            $.post('/ftp/do_save_ftp', data, function(resp){
                if(resp.code == 1){
					showFormMsg(resp.msg, 'error');
				}else{
					showFormMsg(resp.msg, 'success');
					setTimeout(function(){
						hideFormMsg();
						tb_remove();
						ftp.refresh();
					}, 1000)
				}
                btn.attr("disabled", "false")
            }, 'json');
        }
        
    }
    
};
