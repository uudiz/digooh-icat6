/**
 * @author User
 */
var special = {
	initFilter : function(){
		document.onkeyup = function(event){
			if (event.keyCode == 13) {
				special.filter();
			}
		}
	},
	filter : function(){
		special.page(1, 'id', 'desc');
	},
	page : function(curpage, orderItem, order){
		showLoading();
		var filterType = $('#filterType').val();
		var filter = $('#filter').val();
		var online = 1;
		var filterGroup = $('#filterGroup').val();
		if(online == undefined){
			online = 0;
		}
		var req = '';
		if(filter.length > 0){
			req +='&filter_type='+filterType+'&filter='+filter;
		}
		
		req += '&online='+online;  
		if(parseInt(filterGroup) > 0){
			req += '&gid='+filterGroup;
		}

		$.get('/player/player_online_refresh/'+curpage+'/'+orderItem+'/'+order+'?t=' + new Date().getTime() + req, 
			function(data){
				$('#specialContent').html(data);
				//reinit this box~
				tb_init('td > a.thickbox');//pass where to apply thickbox
				hideLoading();
				special.initFilter();
			}
		);
	},
	plist: function(curpage, orderItem, order){
		showLoading();
		var filterType = $('#filterType').val();
		var filter = $('#filter').val();
		var online = 1;
		var filterGroup = $('#filterGroup').val();
		if(online == undefined){
			online = 0;
		}
		var req = '';
		if(filter.length > 0){
			req +='&filter_type='+filterType+'&filter='+filter;
		}
		
		req += '&online='+online;  
		if(parseInt(filterGroup) > 0){
			req += '&gid='+filterGroup;
		}

		$.get('/player/plist_refresh/'+curpage+'/'+orderItem+'/'+order+'?t=' + new Date().getTime() + req, 
			function(data){
				$('#specialContent').html(data);
				//reinit this box~
				tb_init('td > a.thickbox');//pass where to apply thickbox
				hideLoading();
				special.initFilter();
			}
		);
	},
	refresh : function(){
		showLoading();
		$.get('/player/player_online_refresh?t=' + new Date().getTime(), 
			function(data){
				//reinit this box~
				$('#specialContent').html(data);
				tb_init('td > a.thickbox');//pass where to apply thickbox
				hideLoading();
				player.initFilter();
			}
		);
	},
	checkAll: function(obj){
        $('input:checkbox[name="id"]').each(function(){
            this.checked = obj.checked;
        });
        
        $('input:checkbox[name="checkAll"]').each(function(){
            this.checked = obj.checked;
        });
    },
    reboot: function(id, msg) {       
        if(confirm(msg)) {
        	$.post('/player/reboot', {"id":id}, function(data) {
        		if(data.code == 0) {
        			showMsg(data.msg, 'success');
        			$("#reboot_flag_"+id).html('1');
        		}else {
        			showMsg(data.msg, 'success');
        		}
        	}, 'json');
        }
    },
    rebootAll: function(emptyMsg, cfmMsg, value, pType) {
    	var ids = new Array();
        $('input:checkbox[name="id"]').each(function(){
            if (this.checked) {
                ids.push(this.value);
            }
        });
        
        if (ids.length == 0) {
            alert(emptyMsg);
            return;
        }else {
        	if(confirm(cfmMsg)) {      	
	        	$.post('/player/reboot', {"id":ids}, function(data) {
	        		if(data.code == 0) {
	        			showMsg(data.msg, 'success');
	        			special.filter();
	        		}else {
	        			showMsg(data.msg, 'success');
	        		}
	        	}, 'json');
        	}
        } 
    },
    format: function(id, msg) {       
        if(confirm(msg)) {
        	$.post('/player/format', {"id":id}, function(data) {
        		if(data.code == 0) {
        			showMsg(data.msg, 'success');
        			$("#format_flag_"+id).html('1');
        		}else {
        			showMsg(data.msg, 'success');
        		}
        	}, 'json');
        }
    },
     formatAll: function(emptyMsg, cfmMsg, value, pType) {
    	var ids = new Array();
        $('input:checkbox[name="id"]').each(function(){
            if (this.checked) {
                ids.push(this.value);
            }
        });
        
        if (ids.length == 0) {
            alert(emptyMsg);
            return;
        }else {
        	if(confirm(cfmMsg)) {      	
	        	$.post('/player/format', {"id":ids}, function(data) {
	        		if(data.code == 0) {
	        			showMsg(data.msg, 'success');
	        			special.filter();
	        		}else {
	        			showMsg(data.msg, 'success');
	        		}
	        	}, 'json');
        	}
        } 
    },
    onchange: function() {
    	alert('test');
    },
    btnGroupClick: function(obj) {
    	var $this = $(obj);
		var id = $this.attr('id');
    	if($('#'+id).hasClass('open')) {
    		$('#'+id).removeClass('open');
    	}else {
    		$('div.btn-group').removeClass('open');
    		$('#'+id).addClass('open');
    	}
    },
    changeStatus: function(emptyMsg, cfmMsg, type) {
    	var ids = new Array();
        $('input:checkbox[name="id"]').each(function(){
            if (this.checked) {
                ids.push(this.value);
            }
        });
        
        if (ids.length == 0) {
            alert(emptyMsg);
            return;
        }else {
        	switch(type) {
        		case 0:
        			$('#volumeConfirm').dialog({
		                resizable: false,
		                height: 140,
		                modal: true,
		                buttons: {
		                    "Execute": function(){
		                        $(this).dialog("close");
		                        var volume = $('#volume').val();
								$('#rotateConfirm').dialog({
					                resizable: false,
					                height: 156,
					                modal: true,
					                buttons: {
					                    "Yes": function(){
					                        $(this).dialog("close");
											$.post('/player/android_control', {"ids": ids, "type": type, "value": volume}, function(data) {
												//alert(data);
											});
					                    },
					                    "No": function(){
					                        $(this).dialog("close");
					                    }
					                }
					            });
		                    },
		                    "Close": function(){
		                        $(this).dialog("close");
		                    }
		                }
		            });
        			break;
        		case 1:
        		case 2:
        		case 3:
        		case 4:
        		case 5:
        		case 6:
        		case 7:
        		case 8:
        		case 10:
        			var c_value = 0;
        			$('#rotateConfirm').dialog({
		                resizable: false,
		                height: 156,
		                modal: true,
		                buttons: {
		                    "Yes": function(){
		                        $(this).dialog("close");
								$.post('/player/android_control', {"ids": ids, "type": type, "value": c_value}, function(data) {
        							//alert(data);
        						});
		                    },
		                    "No": function(){
		                        $(this).dialog("close");
		                    }
		                }
		            });
        			break;
        	}
		}
    }
};


$(function(){
    special.initFilter();
});
