var software = {
	uploading : false,
	refresh : function(){
		showLoading();
		$.get('/software/refresh?t='+new Date().getTime(), function(data){
			hideLoading();
			$('.wrap').html(data);
			//reinit this box~
			tb_init('a.thickbox, area.thickbox, input.thickbox');//pass where to apply thickbox
		});
	},
	doUpload : function(){
		if(software.uploading){
			return;
		}
		
		var file = $('#file').val();
		if(file.length == 0){
			$('#errorFile').show();
			return;
		}else{
			$('#errorFile').hide();
		}
		
		var pos = file.lastIndexOf('\.');
		if(pos > 0 && file.substr(pos + 1, 3).toLowerCase() == 'img'){
			$('#errorFile').hide();
			software.uploading=true;
			document.forms['form'].submit();
		}else{
			$('#errorFile').show();
			return;
		}
	},
	callbackUpload : function(code, msg){
		software.uploading=false;
		if(code == 0){
			showFormMsg(msg, 'success');
			setTimeout(function(){
				tb_remove();
				software.refresh();
			}, 100);
		}else{
			showFormMsg(msg, 'error');
		}
	},
	remove : function(id, msg){
		if(confirm(msg)){
			var req = {
					  id:id
					  }
			$.post('/software/do_delete',req, function(data){
				if(data.code == 0){
					showMsg(data.msg,'success');
					software.refresh();
					setTimeout(hideMsg, 1000);
					
				}else{
					showMsg(data.msg, 'error');
				}
			},'json');
		}
	},
	toggle : function(obj){
		var $this= $(obj);
		var status = $this.attr('status');
		var id = $this.attr('id');
		var pp = $this.parent().parent();
		var img = $this.children('img');
		var ptype = $this.attr('stype');
		var core = $this.attr('core');
		if(status == 0){
			//set working status
			$this.attr('status', 2);
			software.refreshOnlines(pp, id, ptype, core, function(){
				$this.attr('status', 1);
				img.attr('src','/images/icons/16-06.gif');
			});
			
		}else if(status == 1){
			//collapse
			var next = pp.next();
			if('panel_'+id ==next.attr('id')){
				//next.fadeOut();
				next.remove();
			}
			
			$this.attr('status', 0);
			img.attr('src','/images/icons/16-05.gif');
		}
	},
	refreshOnlines : function(pp, id, ptype, core, callback){
		var next = pp.next();
		if('panel_'+id ==next.attr('id')){
			//next.fadeOut();
			next.remove();
		}
		//set loading status
		var loading = '<tr id="detailLoading" height="80"><td><div style="left: 50%;" class="loading-01">Loading ......</div></td></tr>';
			pp.after(loading);
			//expland
			$.get('/player/software_onlines?id='+id+'&ptype='+ptype+'&core='+core+'&t='+new Date().getTime(), function(data){
				//append detail
				pp.next().remove();
				var css = pp.attr('class');
				if(css == 'onSelected'){
					css ='';
				}
				var line ='<tr id="panel_'+id+'"' +((css != undefined && css != '') ? 'class="'+css+'"' : '' ) +'><td colspan="8">'+data+'</td></tr>';
				pp.after(line);
				software.bindCheckButton(id);
				if(callback != undefined){
					callback();
				}
			});
	},
	page : function(ptype, core, curpage, orderItem, order, id, pid){
		$.get('/player/software_onlines/'+curpage+'/'+orderItem+'/'+order+'?t=' + new Date().getTime()+'&id='+id+'&pid='+pid+'&ptype='+ptype+'&core='+core, 
			function(data){
				var panel = $('#pan_'+pid);
				var pp = panel.parent();
				pp.next().remove();
				//console.info(pp.html()); 
				pp.html(data);
				software.bindCheckButton(id);
			}
		);
	},
	bindCheckButton : function(id){
		var cb = $("#panel_"+id+" input:checkbox[name='checkall']");
		cb.click(function(){
			var checked = this.checked;
			$("#panel_"+id+" input:checkbox[name='checkbox']").each(function(){
                this.checked = checked;
            });
		});
		
		var upgrade = $("#panel_"+id+" .upgrade").click(function(){
			var bu = $(this);
			var version = $("#version_"+id).val();
			var pid = bu.attr('pid');
			$.post('/player/do_upgrade_version', {
				"version":version,
				"ids" : pid
				},function(data){
					if(data.code == 0){
						showMsg(data.msg, 'success');
						$("#upgrade_version_"+pid).html(version);
						$.post('/player/android_control', {"ids": pid, "type": 1, "value": 0}, function(data) {}); //终端重启
					}else{
						showMsg(data.msg, 'error');
					}
				},'json');
		});
		
		var upgradeall = $("#panel_"+id+" .upgradeall").click(function(){
			var version = $("#version_"+id).val();
			var ids = new Array();
			$("#panel_"+id+" input:checkbox[name='checkbox']:checked").each(function(){
                if(this.checked){
					ids.push(this.value);
				}
            });
			$.post('/player/do_upgrade_version', {
				"version":version,
				"ids" : ids
				},function(data){
					if(data.code == 0){
						showMsg(data.msg, 'success');
						software.refreshOnlines($("#software_"+id), id);
						$.post('/player/android_control', {"ids": ids, "type": 1, "value": 0}, function(data) {}); //终端重启
					}else{
						showMsg(data.msg, 'error');
					}
				},'json');
		});
	},
	doSave : function() {
		var descr = $('#descr').val();
		var id = $('#id').val();
		
		$.post('/software/do_save',{descr:descr, id:id},function(data){
				if(data.code != 0){
					showFormMsg(data.msg, 'error');
				}else{
					showFormMsg(data.msg, 'success');
					setTimeout(function(){
						tb_remove();
						software.refresh();
					},1000);
				}
			}, 'json');
	}
};
