var interactionpls = {
	bbContent: '',
	bbHtmlContent: '',
	saving : false,
    initScreenFlag: function(){
        var ew = $('#enableWeek').change(function(event){
            if (this.checked) {
                $('#sun').removeAttr('disabled').attr('checked', true);
                $('#mon').removeAttr('disabled').attr('checked', true);
                $('#tue').removeAttr('disabled').attr('checked', true);
                $('#wed').removeAttr('disabled').attr('checked', true);
                $('#thu').removeAttr('disabled').attr('checked', true);
                $('#fri').removeAttr('disabled').attr('checked', true);
                $('#sat').removeAttr('disabled').attr('checked', true);
            }else {
                $('#sun').removeAttr('checked').attr('disabled', 'disabled');
                $('#mon').removeAttr('checked').attr('disabled', 'disabled');
                $('#tue').removeAttr('checked').attr('disabled', 'disabled');
                $('#wed').removeAttr('checked').attr('disabled', 'disabled');
                $('#thu').removeAttr('checked').attr('disabled', 'disabled');
                $('#fri').removeAttr('checked').attr('disabled', 'disabled');
                $('#sat').removeAttr('checked').attr('disabled', 'disabled');
            }
        });
        
        if (ew.attr('checked')) {
            $('#sun').removeAttr('disabled');
            $('#mon').removeAttr('disabled');
            $('#tue').removeAttr('disabled');
            $('#wed').removeAttr('disabled');
            $('#thu').removeAttr('disabled');
            $('#fri').removeAttr('disabled');
            $('#sat').removeAttr('disabled');
        }
        
        var tf = $('#enableTime').change(function(event){
            if (this.checked) {
                $('#startTime').focus(function(event){
                    WdatePicker({
                        skin: 'default',
                        dateFmt: 'HH:mm',
                        lang: curLang
                    });
                }).removeClass('gray');
                $('#endTime').focus(function(event){
                    WdatePicker({
                        skin: 'default',
                        dateFmt: 'HH:mm',
                        lang: curLang
                    });
                }).removeClass('gray');
            }
            else {
                $('#startTime').unbind('focus').addClass('gray');
                $('#endTime').unbind('focus').addClass('gray');
            }
        });
        
        if (tf.attr('checked')) {
            $('#startTime').removeClass('gray').focus(function(event){
                WdatePicker({
                    skin: 'default',
                    dateFmt: 'HH:mm',
                    lang: curLang
                });
            });
            
            $('#endTime').removeClass('gray').focus(function(event){
                WdatePicker({
                    skin: 'default',
                    dateFmt: 'HH:mm',
                    lang: curLang
                });
            });
        }
        
        var df = $('#enableDate');
        if (df.attr('checked')) {
            var sd = $('#startDate');
            var ed = $('#endDate');
            sd.datepicker({
    		    changeMonth: true,
    		    changeYear: true,
                dateFormat: 'yy-mm-dd'
            }).removeClass('gray');
            
            ed.datepicker({
    		    changeMonth: true,
    		    changeYear: true,
                dateFormat: 'yy-mm-dd'
            }).removeClass('gray');
        }
        
        df.change(function(event){
            var sd = $('#startDate');
            var ed = $('#endDate');
            if (this.checked) {
                sd.datepicker({
                    dateFormat: 'yy-mm-dd'
                }).removeClass('gray');
                
                ed.datepicker({
                    dateFormat: 'yy-mm-dd'
                }).removeClass('gray');
            }
            else {
                sd.datepicker('destroy').addClass('gray');
                ed.datepicker('destroy').addClass('gray');
            }
        });
    },
    initDatePicker: function(){
        $('#custom').change(function(event){
            $('#playtime').css('display', '').focus(function(event){
                WdatePicker({
                    skin: 'default',
                    dateFmt: 'H:mm:ss',
                    lang: 'en'
                });
            });
        });
        $('#default').change(function(event){
            $('#playtime').css('display', 'none');
        });
    },
    initTemplate: function(){
        $(".tab-01 a").click(function(event){
            event.preventDefault();
            var cur = $(this).addClass("on");
            cur.siblings("a").removeClass("on");
            var type = cur.attr('type');
            var curType = $('#templateContent').attr('type');
            if (type != curType) {
                interactionpls.loadTemplate(type, 1);
            }
        });
        var type = $('.tab-01 a.on').attr('type');
        this.loadTemplate(type, 1);
    },
    loadTemplate: function(type, curpage){
        type = type || $('.tab-01 a.on').attr('type');
        curpage = curpage || 1;
        $('#templateContent').attr('type', type);
        $.get('/interactionpls/template_list/' + curpage + '?type=' + type + '&t=' + new Date().getTime(), function(data){
            $('#templateContent').html(data);
            //reinit this box~
            //tb_init('a.thickbox');//pass where to apply thickbox
            $('#templateContent img').unbind('click').click(function(event){
                $('#templateId').val(this.id);
                $('#templateName').val(this.name);
                $('#templateType').val(this.template_type);
                tb_remove();
            });
        });
    },
    doCreate: function(){
        var name = $('#name').val();
        var descr = $('#descr').val();
        var interactionId = $('#interactionId').val();
        $.post('/interactionpls/do_add?t=' + new Date().getTime(), {
            name: name,
            descr: descr,
            interactionId: interactionId
        }, function(data){
        //	alert(data);
        //});
            if (data.code != 0) {
                showMsg(data.msg, 'error');
            }else {
                showMsg(data.msg, 'success');
                setTimeout(function(){
                    window.location.href = '/interactionpls/screen?id=' + data.id;
                }, 200);
            }
        }, 'json');
    },
    goList: function(){
        window.location.href = '/interactionpls/index';
    },
    refresh: function(){
        showLoading();
        $.get('/interactionpls/refresh?t=' + new Date().getTime(), function(data){
            $('.wrap').html(data);
            tb_init('a.thickbox');//pass where to apply thickbox
            hideLoading();
            setTimeout(function(){
                hideMsg();
            }, 200);
        });
    },
    remove: function(id, msg){
        if (confirm(msg)) {
            $.post('/interactionpls/do_delete?id=' + id + '&t=' + new Date().getTime(), function(data){
                if (data.code == 0) {
                    showMsg(data.msg, 'success');
                    setTimeout(function(){
                        interactionpls.refresh();
                    }, 100);
                }
                else {
                    showMsg(data.msg, 'error');
                }
            }, 'json');
        }
    },
    initScreenOp: function(content, htmlc){
    	interactionpls.bbContent = content;
    	interactionpls.bbHtmlContent = htmlc;
        $('.icon-list img').click(function(event){
            var img = $(event.target);
            var areaId = img.attr('id');
            $('.icon-list img').each(function(){  //test
            	var tImg = $(this);
            	var tsrc = tImg.attr('src');
            	if(tsrc.indexOf('-on') > 0) {
            		if(tImg.attr('title') == 'Bulletin Board' || tImg.attr('title') == 'StaticText') {
            			interactionpls.bbHtmlContent = $('#static_ticker').val();
            			var content = interactionpls.ApplyLineBreaks('static_ticker');
            			interactionpls.bbContent = content;
            		}
            	}
            });
  
            if (img.attr('src').indexOf('-on') > 0) {
                return;
            }
            
            
            if (areaId != null) {
                //change tab selected
                $('.icon-list img').each(function(){
                    var opImg = $(this);
                    var src = opImg.attr('src');
                    
                    if (opImg.attr('id') == areaId) {
                        //set  on
                        src = src.replace('.gif', '-on.gif');
                        opImg.attr('src', src);
                    }
                    else {
                        //set off
                        if (src.indexOf('-on') > 0) {
                            src = src.replace('-on', '');
                            opImg.attr('src', src);
                        }
                    }
                });
                var targetAreaId = 'content_' + areaId;
                $('.tab-area').each(function(){
                    if (this.id == targetAreaId) {
                        $(this).show();
                    }
                    else {
                        $(this).hide();
                    }
                });
            }
        });
    },
    loadArea: function(playlistId, areaId, mediaOp, screen_id){
        //mediaOp 0:default 1:add, 2: delete
        var area = $('#content_' + areaId);
        if (area.css('display') != 'none') {
            //set selected icon
            var opImg = $('#' + areaId);
            var src = opImg.attr('src');
            if (src.indexOf('-on') == -1) {
                src = src.replace('.gif', '-on.gif');
                opImg.attr('src', src);
            }
        }
        if (mediaOp == undefined) {
            mediaOp = 0;
        }
        if (screen_id == undefined) {
            screen_id = 0;
        }

        //area.html('<div style="top: 10%; left: 30%; position:relative;" class="loading-01">Loading......</div>');
        $.get('/interactionpls/area?interactionpls_id=' + playlistId + '&area_id=' + areaId + '&screen_id=' + screen_id + (mediaOp > 0 ? '&after_media=' + mediaOp : '') + '&t=' + new Date().getTime(), function(data){
            area.html(data);
            //初始化编辑
            tb_init('#content_' + areaId + ' .thickbox');
            interactionpls.bindMediaMove(areaId);
            hideMsg();
        });
    },
    bindMediaMove: function(areaId){
        var up = $('#content_' + areaId + ' .up');
        var down = $('#content_' + areaId + ' .down');
        up.unbind('click').bind('click', function(){
            var img = $(this);
            var cid = img.attr('cid');
            var pid = img.attr('pid');
            interactionpls.changeMediaOrder(areaId, pid, cid);
        });
        down.unbind('click').bind('click', function(){
            var img = $(this);
            var cid = img.attr('cid');
            var nid = img.attr('nid');
            interactionpls.changeMediaOrder(areaId, cid, nid);
        });
    },
	rotateMedia : function(obj, id){
		var rotate = 0;
		if(obj.checked){
			rotate=1;
		}
		
		$.post('/interactionpls/do_rotate_media', {
            id: id,
            rotate: rotate
        }, function(data){
            if (data.code != 0) {
                showMsg(data.msg, 'error');
            }
        }, 'json');
	},
    changeMediaOrder: function(areaId, fid, sid){
        //first id  and second id
        var playlistId = $('#playlistId').val();
        $.post('/interactionpls/do_change_media_order', {
            playlist_id: playlistId,
            area_id: areaId,
            fid: fid,
            sid: sid
        }, function(data){
            if (data.code == 0) {
                interactionpls.loadArea(playlistId, areaId);
            }
            else {
                showMsg(data.msg, 'error');
            }
        }, 'json');
    },
    moveTo: function(obj, areaId, id, total, warnNum, warnBound){
        var cur = $(obj);
        var old = cur.attr('position');
        var screenId = $('#screenId').val();
        if (!/^[0-9]*$/.test(obj.value)) {
            alert(warnNum);
            
            cur.val(old);
            return;
        }
        
        if (obj.value <= 0 || obj.value > total) {
            alert(warnBound);
            cur.val(old);
            return;
        }
        
        if (obj.value == old) {
            return;
        }
        
        var playlistId = $('#playlistId').val();
        $.post('/interactionpls/do_move_to', {
            playlist_id: playlistId,
            area_id: areaId,
            id: id,
            index: obj.value
        }, function(data){
            if (data.code == 0) {
                interactionpls.loadArea(playlistId, areaId, false, screenId);
            }
            else {
                showMsg(data.msg, 'error');
            }
        }, 'json');
    },
    initMediaPanel: function(){
        $("#TB_ajaxContent .tab-02 a").click(function(event){
            event.preventDefault();
            var cur = $(this).addClass("on");
            cur.siblings("a").removeClass("on");
            var type = cur.attr('type');
            var curType = $('#layoutContent').attr('type');
            
            interactionpls.addAreaMediaFilter($('#playlistId').val(), $('#areaId').val(), $('#bmp').val(), $('#mediaType').val(), $('#curpage').val(),type ,$('#orderItem').val(), $('#order').val());
        });
        //init filter
        var filterType = $('#filterType').val();
        var filter = $("#filter");
        if (filterType == 'add_time') {
            filter.datepicker({
    		    changeMonth: true,
    		    changeYear: true,
                dateFormat: 'yy-mm-dd'
            });
            filter.attr('readonly', true);
            filter.addClass('date-input');
        }
        else {
            filter.attr('readonly', false);
            filter.datepicker("destroy");
            filter.removeClass('date-input');
        }
    },
    initShowMediaPanel: function(){
        $("#TB_ajaxContent .tab-02 a").click(function(event){
            event.preventDefault();
            var cur = $(this).addClass("on");
            cur.siblings("a").removeClass("on");
            var type = cur.attr('type');
            var curType = $('#layoutContent').attr('type');
            
            interactionpls.addShowMediaFilter($('#playlistId').val(), $('#areaId').val(), $('#bmp').val(), $('#mediaType').val(), $('#curpage').val(),type ,$('#orderItem').val(), $('#order').val());
        });
        //init filter
        var filterType = $('#filterType').val();
        var filter = $("#filter");
        if (filterType == 'add_time') {
            filter.datepicker({
    		    changeMonth: true,
    		    changeYear: true,
                dateFormat: 'yy-mm-dd'
            });
            filter.attr('readonly', true);
            filter.addClass('date-input');
        }
        else {
            filter.attr('readonly', false);
            filter.datepicker("destroy");
            filter.removeClass('date-input');
        }
    },
    chooseAreaAllMedia: function(obj, areaId){
        $("#content_" + areaId + " input[name='id']").each(function(){
            this.checked = obj.checked;
        });
    },
    chooseAreaAllExclude: function(obj, areaId, playlistId){
    	var ids = [];
    	var status;
        $("#content_" + areaId + " input[name='status']").each(function(){
            this.checked = obj.checked;
        });
        if(obj.checked) {
        	status = 1;
        }else {
        	status = 0;
        }	
    	$.post('/interactionpls/do_editAreaStatus', {playlistId: playlistId, areaId: areaId, status: status}, function(date) {
    		//interactionpls.loadArea(playlistId, areaId);
    	});
    },
    removeAreaAllMedia: function(playlistId, areaId, emptyTip, cfmMsg, screen_id){
        if (confirm(cfmMsg)) {
            var ids = [];
            $("#content_" + areaId + " input:checkbox").each(function(){
                if (this.checked) {
                    var obj = $(this);
                    if (obj.val() != "0") {
                        ids.push(obj.val());
                    }
                }
            });
            if (ids.length == 0) {
                alert(emptyTip);
                return;
            }
            
            $.post('/interactionpls/delete_all_media?t=' + new Date().getTime(), {
                playlist_id: playlistId,
                area_id: areaId,
                ids: ids
            }, function(data){
                if (data.code == 0) {
                    showMsg(data.msg, 'success');
                    setTimeout(function(){
                        interactionpls.loadArea(playlistId, areaId, 2, screen_id);
                    }, 2000);
                }
                else {
                    showMsg(data.msg, 'error');
                }
            }, 'json');
        }
    },
    //删除区域的媒体文件信息
    removeAreaMedia: function(playlistId, areaId, id, cfmMsg, screen_id){
        if (confirm(cfmMsg)) {
            $.post('/interactionpls/delete_media?t=' + new Date().getTime(), {
                playlist_id: playlistId,
                area_id: areaId,
                id: id
            }, function(data){
                if (data.code == 0) {
                    showMsg(data.msg, 'success');
                    setTimeout(function(){
                        interactionpls.loadArea(playlistId, areaId, 2, screen_id);
                    }, 2000);
                }
                else {
                    showMsg(data.msg, 'error');
                }
            }, 'json');
        }
    },
    addAreaMedia: function(playlistId, areaId, areaType, mediaType, curpage, title, screenId){
        if (curpage == null) {
            curpage = 1;
        }
        if(areaType == 0) {
        	areaType = 'video';
        }
        if(areaType == 1) {
        	areaType = 'image';
        }
        if(areaType == 8) {
        	areaType = 'logo';
        }
        if(areaType == 9) {
        	areaType = 'bg';
        }
        if(areaType == 7) {
        	var req = '/interactionpls/add_playlist_media_webpage?playlist_id=' + playlistId + '&area_id=' + areaId + '&media_type=' + mediaType + '&width=430&height=200';
        }else {
        	var req = '/interactionpls/media_panel?playlist_id=' + playlistId + '&screenId=' + screenId + '&area_id=' + areaId + '&bmp=' + areaType + '&media_type=' + mediaType + '&curpage=' + curpage + '&width=1024&height=520';
        }
        // var req = '/interactionpls/media_panel?playlist_id=' + playlistId + '&area_id=' + areaId + '&bmp=' + areaType + '&media_type=' + mediaType + '&curpage=' + curpage + '&width=1024&height=520';
        if (title == undefined) {
            showLoading();
            $.get(req, function(data){
                $('#TB_ajaxContent').html(data);
                hideLoading();
            });
        }
        else {
            tb_show(title, req, '');
        }
    },
    addShowMedia: function(playlistId, areaId, areaType, mediaType, curpage, title){
        if (curpage == null) {
            curpage = 1;
        }
        areaType = 'image';
        var req = '/interactionpls/show_media_panel?playlist_id=' + playlistId + '&area_id=' + areaId + '&bmp=' + areaType + '&media_type=' + mediaType + '&curpage=' + curpage + '&width=1024&height=520';
        if (title == undefined) {
            showLoading();
            $.get(req, function(data){
                $('#TB_ajaxContent').html(data);
                hideLoading();
            });
        }
        else {
            tb_show(title, req, '');
        }
    },
    addShowMediaFilter: function(playlistId, areaId, areaType, mediaType, curpage, type, orderItem, order){

        if (playlistId == undefined) {
            playlistId = $('#playlistId').val();
        }
        if (areaId == undefined) {
            areaId = $('#areaId').val();
        }
        
        if (mediaType == undefined) {
            mediaType = $('#mediaType').val();
        }
        
        if (curpage == undefined) {
            curpage = 1;
        }
        if (orderItem == undefined) {
            orderItem = $('#orderItem').val();
        }
        if (order == undefined) {
            order = $('#order').val();
        }
        if(type == undefined) {
        	type = $('.tab-02 a.on').attr('type');
        }
        
        var req = '/interactionpls/show_media_panel_filter?playlist_id=' + playlistId + '&area_id=' + areaId + '&bmp=' + $('#bmp').val() +'&media_type=' + mediaType + '&curpage=' + curpage + '&order_item=' + orderItem + '&order=' + order + '&type=' + type;
        
        var filterType = $('#filterType').val();
        var filter = $('#filter').val();
        var filterFolder = $('#filterFolder').val();
        var filterUploader = $('#filterUploader').val();
        
        if (filter.length > 0) {
            req += '&filter_type=' + filterType + '&filter=' + filter;
        }

        if (parseInt(filterFolder) >= 0) {
            req += '&folder_id=' + filterFolder;
        }
        if (parseInt(filterUploader) > 0) {
            req += '&uid=' + filterUploader;
        }
        
        showLoading();
        $.get(req, function(data){
            $('#layoutContent').html(data);
            hideLoading();
            $('#curpage').val(curpage);
            $('#orderItem').val(orderItem);
            $('#order').val(order);
        });
    },
    changeFilterType: function(obj){
        var filter = $("#filter");
        filter.val('');
        
        if (obj.value == 'add_time') {
            filter.datepicker({
                dateFormat: 'yy-mm-dd'
            });
            filter.addClass('date-input');
            filter.attr('readonly', true);
        }
        else {
            filter.datepicker("destroy");
            filter.attr('readonly', false);
            filter.removeClass('date-input');
        }
    },
    addAreaMediaFilter: function(playlistId, areaId, areaType, mediaType, curpage, type, orderItem, order){

        if (playlistId == undefined) {
            playlistId = $('#playlistId').val();
        }
        if (areaId == undefined) {
            areaId = $('#areaId').val();
        }
        
        if (mediaType == undefined) {
            mediaType = $('#mediaType').val();
        }
        
        if (curpage == undefined) {
            curpage = 1;
        }
        if (orderItem == undefined) {
            orderItem = $('#orderItem').val();
        }
        if (order == undefined) {
            order = $('#order').val();
        }
        if(type == undefined) {
        	type = $('.tab-02 a.on').attr('type');
        }
        
        var req = '/interactionpls/media_panel_filter?playlist_id=' + playlistId + '&area_id=' + areaId + '&bmp=' + $('#bmp').val() +'&media_type=' + mediaType + '&curpage=' + curpage + '&order_item=' + orderItem + '&order=' + order + '&type=' + type;
        
        var filterType = $('#filterType').val();
        var filter = $('#filter').val();
        var filterFolder = $('#filterFolder').val();
        var filterUploader = $('#filterUploader').val();
        
        if (filter.length > 0) {
            req += '&filter_type=' + filterType + '&filter=' + filter;
        }

        if (parseInt(filterFolder) >= 0) {
            req += '&folder_id=' + filterFolder;
        }
        if (parseInt(filterUploader) > 0) {
            req += '&uid=' + filterUploader;
        }
        
        showLoading();
        $.get(req, function(data){
            $('#layoutContent').html(data);
            hideLoading();
            $('#curpage').val(curpage);
            $('#orderItem').val(orderItem);
            $('#order').val(order);
        });
    },
    checkAllMedia: function(obj){
        $('input:checkbox[name="mid"]').each(function(){
            this.checked = obj.checked;
        });
    },
    chooseMedia: function(mediaId){
        var cb = $('#' + mediaId);
        cb.attr('checked', !cb.attr('checked'));
    },
    saveAreaMedia: function(playlistId, areaId, emptyTip, close, radio){
        if (radio == undefined) {
            radio = false;
        }
        
        var mediaType = $('#mediaType').val();
        var screen_id = $('#screenId').val();
        $.post('/interactionpls/do_save_media_check?t=' + new Date().getTime(), {
            media_type: mediaType,
            playlist_id: playlistId,
            area_id: areaId
        }, function(checkData){
            if (checkData.code == 0) {
                var media = [];
                if (radio) {
                    $("input:radio[name='mid']:checked").each(function(){
                        if (this.checked) {
                            media.push(this.value);
                        }
                    });
                }
                else {
                    $("input:checkbox[name='mid']:checked").each(function(){
                        if (this.checked) {
                            media.push(this.value);
                        }
                    });
                }
                if (media.length == 0) {
                    alert(emptyTip);
                    return;
                }
                
                $.post('/interactionpls/do_save_media?t=' + new Date().getTime(), {
                    playlist_id: playlistId,
                    area_id: areaId,
                    medias: media,
                    media_type: mediaType,
                }, function(data){
                    if (data == undefined) {
                        return;
                    }
                    if (data.code == 0) {
                        showFormMsg(data.msg, 'success');
                        setTimeout(function(){
                            interactionpls.loadArea(playlistId, areaId, 1, screen_id);
                            if (close) {
                                tb_remove();
                            }
                        }, 500);
                    }
                    else {
                        showFormMsg(data.msg, 'error');
                    }
                    
                }, 'json');
            }
            else {
                showFormMsg(checkData.msg, 'error');
            }
        }, 'json');
        
    },
    saveShowMedia: function(playlistId, areaId, emptyTip, close, radio){
        var media = 0;
        var mediaType = $('#mediaType').val();
		$("input:radio[name='mid']:checked").each(function(){
			if (this.checked) {
     			media = this.value;
     		}
		});       
		if (media == 0) {
			alert(emptyTip);
			return;
		}       
		
   		$.post('/interactionpls/do_save_show_media?t=' + new Date().getTime(), {
			playlist_id: playlistId,
        	area_id: areaId,
       		medias: media,
      		media_type: mediaType
		}, function(data){
			if (data == undefined) {
          		return;
			}
     		if(data.code == 0) {
            	showFormMsg(data.msg, 'success');
            	$('#'+areaId+'_btnShowName').val(data.name);
            	$('#'+areaId+'_btnShow').val(data.id);
            	setTimeout(function(){
            		tb_remove();
            	}, 1000);
          	}else {
           		showFormMsg(data.msg, 'error');
			}
		}, 'json');

    },
    saveAreaMediaWebpage: function(playlistId, areaId, mediaType) {
		var url = $('#url').val();
		var play_time = '01:00';
		var startDate = $('#startDate').val();
		var endDate = $('#endDate').val();
		var updateF = $('#updateF').val();

        var media = [];
		media.push(1);
                
		$.post('/interactionpls/do_save_webpage_media?t=' + new Date().getTime(), {
			playlist_id: playlistId,
			area_id: areaId,
			medias: media,
			media_type: mediaType,
			url: url,
			play_time: play_time,
			startDate: startDate,
			endDate: endDate,
			updateF: updateF
		}, function(data){
			if (data == undefined) {
				return;
			}
			if(data.code == 0) {
				showFormMsg(data.msg, 'success');
           		setTimeout(function(){
            		interactionpls.loadArea(playlistId, areaId, 1);
            		if (close) {
             			tb_remove();
            		}
               	}, 500);
           	}else {
				showFormMsg(data.msg, 'error');
			}    
		}, 'json');
    },
    _createPlaylistData: function(){
        var json = new Object();
        //form area
        var playlistId = $('#playlistId').val();
        var name = $('#name').val();
        var descr = $('#descr').val();
        var templateId = $('#templateId').val();
        var form = {
            name: name,
            descr: descr,
            template_id: templateId,
            id: playlistId
        };

        json.playlist = form;
        
        json.playlist_id = playlistId;
        
        //media add
        var ids = [];
        $('.table-list input:checkbox').each(function(){
            if (parseInt(this.value) > 0) {
                ids.push(this.value);
            }
        });
        
        //RSS ID
        var rssId = $('#rssId');
        if (rssId.length > 0) {
            ids.push(rssId.val());
        }
        
        json.ids = ids;

        //textCheck
        if (document.getElementById("ticker") != undefined) {
            var content = $('#ticker').val();
            var color = $('#color').val();
            var bgColor = $('#bgColor').val();
            var direction = $('#direction').val();
            var fontFamily = $('#fontFamily').val();
			var font = $('#font_font').val();
            var fontSize = $('#fontSize').val();
            var transparent = $('#text_transparent').val();
            var speed = $('#speed').val();
            var duration = $('#duration').val();
            var id = $('#textId').val();
            var areaId = $('#textAreaId').val();
			var rssFormat = $('#rssFormat').val();
            var text = {
                id: id,
                area_id: areaId,
                content: content,
                color: color,
                bg_color: bgColor,
                direction: direction,
				font: font,
                //font_family:fontFamily,
                font_size: fontSize,
                transparent: transparent,
                speed: speed,
                duration: duration,
				rssFormat: rssFormat
            }
            
            json.text = text;
        }
        
        //staticText 
        if (document.getElementById("static_ticker") != undefined) {
			var fontSize = $('#sfont_size').val();
            var color = $('#static_Color').val();
            var bg_color = $('#static_BgColor').val();
            var id = $('#static_textId').val();
            var areaId = $('#static_textAreaId').val();
            var htmlc = '';
            var htmltext = ''; 
            var content = '';
            $('.icon-list img').each(function(){  //test
            	var tImg = $(this);
            	var tsrc = tImg.attr('src');
            	if(tsrc.indexOf('-on') > 0) {
            		if(tImg.attr('title') == 'Bulletin Board' || tImg.attr('title') == 'StaticText') {
            			htmlc = $('#static_ticker').val();
           				htmltext = htmlc;
            			htmlc = htmlc.replace(new RegExp("\\n", "g"), "<br/>");
            			content = interactionpls.ApplyLineBreaks('static_ticker');
            			interactionpls.bbContent = content;
            		}else {
            			content = interactionpls.bbContent;
            			htmlc = interactionpls.bbHtmlContent;
            			htmltext = htmlc;
            			htmlc = htmlc.replace(new RegExp("\\n", "g"), "<br/>");
            		}
            	}
            });
            
            var staticText = {
                id: id,
                area_id: areaId,
                content: content,
                htmlc: htmlc,
                color: color,
                bg_color: bg_color
            }
            $('#static_ticker').val(htmltext);
            json.staticText = staticText;
        }
		
		//date
		var date = document.getElementById("dateFontSize");
		if(date != undefined){
			var dtransparent;
			var dstyle;
			if(document.getElementById("dtransparent") == undefined) {
				dtransparent = 0;
				dstyle = 0;
			}else {
				dtransparent = $('#dtransparent').val();
				dstyle = $('#dataStyle').val();
			}
			var d = {
				id  : $('#dateId').val(),
				font_size : date.value,
				color : $('#dateColor').val(),
				bg_color : $('#dateBgColor').val(),
				style: dstyle,
				transparent: dtransparent,
				language : $('#dateLanguage').val()
			}
			json.date=d;
		}
		
		//time
		var time = document.getElementById("timeFontSize");
		if(time != undefined){
			var ttransparent;
			var tstyle;
			if(document.getElementById("ttransparent") == undefined) {
				ttransparent = 0;
				tstyle = 0;
			}else {
				ttransparent = $('#ttransparent').val();
				tstyle = $('#timeStyle').val();
			}
			var t = {
				id  : $('#timeId').val(),
				font_size : time.value,
				color : $('#timeColor').val(),
				bg_color : $('#timeBgColor').val(),
				style: tstyle,
				transparent: ttransparent
			}
			json.time=t;
		}
		 
		//weather
        var weather = document.getElementById("weatherFontSize");
		if(weather != undefined){
			var wtransparent;
			var wstyle;
			if(document.getElementById("wtransparent") == undefined) {
				wtransparent = 0;
				wstyle = 0;
			}else {
				wtransparent = $('#wtransparent').val();
				wstyle = $('#weatherStyle').val();
			}
			var w = {
				id  : $('#weatherId').val(),
				font_size : weather.value,
				color : $('#weatherColor').val(),
				bg_color : $('#weatherBgColor').val(),
				style: wstyle,
				transparent: wtransparent,
				language : $('#weatherLanguage').val()
			}
			json.weather=w;
		}
		
		//button
		var areaIds = $('#areaIds').val();
		var areaId, tmp, sid, x, y, w, h, areaId, action, goal, style, show, fullScreen, closeFlag, timeout;
		if(areaIds.indexOf(',')) {
			tmp = areaIds.split(',');
			areaId = tmp[0];
		}else {
			areaId = areaIds;
		}

		var btn = document.getElementById(areaId+"_btnAction");
		if(btn != undefined) {
			json.areaBtn = [];
			for(var i=0; i < tmp.length; i++) {
				sid = x = $('#' + tmp[i] + '_sid').val();
				x = $('#' + tmp[i] + '_x').val();
				y = $('#' + tmp[i] + '_y').val();
				w = $('#' + tmp[i] + '_w').val();
				h = $('#' + tmp[i] + '_h').val();
				areaId = tmp[i];
				action = $('#' + tmp[i] + '_btnAction').val();
				goal = $('#' + tmp[i] + '_btnTarget_'+action).val();
				if(goal == undefined) {
					goal = '';
				}
				style = $('#' + tmp[i] + '_btnStyle').val();
				if(style == undefined) {
					style = 1;
				}
				show = $('#' + tmp[i] + '_btnShow').val();
				fullScreen = $('#' + tmp[i] + '_btnScreen').val();
				closeFlag = $('#' + tmp[i] + '_btnClose').val();
				if(closeFlag == undefined) {
					closeFlag = 1;
				}
				timeout = $('#' + tmp[i] + '_btnTimeout').val();
				json.areaBtn[i] = '{"x":' + x + ',"y":' + y + ',"w":' + w + ',"h":' + h + ',"sid":' + sid + ',"areaId":' + areaId + ',"action":' + action + ',"goal":"' + goal + '","style":' + style + ',"show":' + show + ', "fullScreen":'+ fullScreen + ',"closeFlag":' + closeFlag + ',"timeout":"' + timeout +'"}';
			}
		}
        return json;
    },
    restScrollPosition: function(){
        var d = $(document);
        if (d.scrollTop() > 20) {
            d.scrollTop(0);
        }
    },
    savePlaylist: function(){
        var json = this._createPlaylistData();
        //console.log(json);
        //post
        this.restScrollPosition();
        $.post('/interactionpls/do_save', json, function(data){
        	//alert(data);
        //});
            if (data.code == 0) {
                hideMsg();
                showMsg(data.msg, 'success');
                if (data.refresh_url) {
                    var previewImg = $('#previewImg');
                    previewImg.attr('src', previewImg.attr('src') + "&t2=" + new Date().getTime());
                }
                setTimeout(function(){
                    hideMsg();
                }, 1000);
            }
            else {
                showMsg(data.msg, 'error');
            }
        }, 'json');
    },
    saveOneScreen: function(playlist_id, screen_id){
        var json = this._createPlaylistData();
        this.restScrollPosition();
        $.post('/interactionpls/do_save_one_screen', json, function(data){
            if (data.code == 0) {
                hideMsg();
                setTimeout(function(){
                    window.location.href = '/interactionpls/screen?id='+playlist_id+'&screen_id='+screen_id;
                }, 100);
            }else {
                showMsg(data.msg, 'error');
            }
        }, 'json');
    },
    checkAreaMediaPublished: function(){
        var result = true;
		var movie = $('.table_movie');
		var bg = $('.table_bg');
		var logo = $('.table_logo');
        if (movie[0] != undefined) {
            if (movie.find('tbody input:checkbox').length == 0) {
                return false;
            }
        }
        $('.table_image').each(function(){
            var image = $(this);
            if (image[0] != undefined) {
                if (image.find('tbody input:checkbox').length == 0) {
                    result = false;
                }
            }
        });
        
        if (bg[0] != undefined) {
            if (bg.find('tbody input:checkbox').length == 0) {
                return false;
            }
        }
        
        if (logo[0] != undefined) {
            if (logo.find('tbody input:checkbox').length == 0) {
                return false;
            }
        }
		
        return result;
    },
    publishPlaylist: function(msgEmpty, portrait){
        if (!this.checkAreaMediaPublished()) {
			alert(msgEmpty);
            return false;
        }
		var rotate = $('input:checkbox[name="rotate"]:checked').length > 0;
        if (portrait) {
			interactionpls.postPublishPlaylist(true, true);
        }else{
			interactionpls.postPublishPlaylist(false);
		}
    },
    postPublishPlaylist : function(rotate, fit){
		if(interactionpls.saving){
			return;
		}
		fit = fit || false;
		var publish = $('#publish');
        publish.attr('disable', true);
        publish.removeClass();
        publish.addClass('btn-02');
        var json = this._createPlaylistData();
        json.rotate = rotate ? 1 : 0;
		json.fit = fit ? 1 : 0;
        var publishing = $('#publishing');
        publishing.show();
        this.restScrollPosition();
        $.post('/interactionpls/do_publish', json, function(data){
            if (data.code == 0) {
                hideMsg();
                showMsg(data.msg, 'success');
                setTimeout(function(){
                    window.location.href = '/interactionpls/index';
                }, 500);
            }
            else {
                showMsg(data.msg, 'error');
            }
            publish.attr('disable', false);
            publish.removeClass();
            publish.addClass('btn-01');
            publishing.hide();
        }, 'json').error(function(e){
			alert(e.responseText);
            publishing.hide();
        });
	},
	publishPlaylistTouch: function(msgEmpty, portrait){
        if (!this.checkAreaMediaPublished()) {
			alert(msgEmpty);
            return false;
        }
		var rotate = $('input:checkbox[name="rotate"]:checked').length > 0;
        if (portrait) {
			interactionpls.postPublishPlaylistTouch(true, true);
        }else{
			interactionpls.postPublishPlaylistTouch(false);
		}
    },
    postPublishPlaylistTouch : function(rotate, fit){
		if(interactionpls.saving){
			return;
		}
		fit = fit || false;
		var publish = $('#publish');
        publish.attr('disable', true);
        publish.removeClass();
        publish.addClass('btn-02');
        var json = this._createPlaylistData();
        json.rotate = rotate ? 1 : 0;
		json.fit = fit ? 1 : 0;
        var publishing = $('#publishing');
        publishing.show();
        this.restScrollPosition();
        $.post('/interactionpls/do_publish', json, function(data){
            if (data.code == 0) {
                hideMsg();
                showMsg(data.msg, 'success');
                setTimeout(function(){
                    window.location.href = '/interactionpls/view_touch';
                }, 500);
            }
            else {
                showMsg(data.msg, 'error');
            }
            publish.attr('disable', false);
            publish.removeClass();
            publish.addClass('btn-01');
            publishing.hide();
        }, 'json').error(function(e){
			alert(e.responseText);
            publishing.hide();
        });
	},
    initTextArea: function(){
        $(".tab-01 a").click(function(event){
            event.preventDefault();
            var cur = $(this);
            cur.addClass("on").siblings("a").removeClass("on");
            num = $(".tab-01 a").index(cur);
            $(".tab-01-in").eq(num).show().siblings(".tab-01-in").hide();
            if (cur.attr('type') == 'rss') {
                //show
                $('#rssOperate').show();
            }
            else {
                //hide
                $('#rssOperate').hide();
            }
        });
        
        //color
        $('#colorSelector').ColorPicker({
            color: $('#color').val(),
            onShow: function(colpkr){
                $(colpkr).fadeIn(500);
                return false;
            },
            onHide: function(colpkr){
                $(colpkr).fadeOut(500);
                return false;
            },
            onSubmit: function(hsb, hex, rgb, el){
                $('#colorSelector div').css('backgroundColor', '#' + hex);
                $('#color').val('#' + hex);
                $(el).ColorPickerHide();
            }
        });
        
        //bg
        $('#bgColorSelector').ColorPicker({
            color: $('#bgColor').val(),
            onShow: function(colpkr){
                $(colpkr).fadeIn(500);
                return false;
            },
            onHide: function(colpkr){
                $(colpkr).fadeOut(500);
                return false;
            },
            onSubmit: function(hsb, hex, rgb, el){
                $('#bgColorSelector div').css('backgroundColor', '#' + hex);
                $('#bgColor').val('#' + hex);
                $(el).ColorPickerHide();
            }
        });
    },
	initDateArea : function(){
		//color
        $('#dateColorSelector').ColorPicker({
            color: $('#dateColor').val(),
            onShow: function(colpkr){
                $(colpkr).fadeIn(500);
                return false;
            },
            onHide: function(colpkr){
                $(colpkr).fadeOut(500);
                return false;
            },
            onSubmit: function(hsb, hex, rgb, el){
                $('#dateColorSelector div').css('backgroundColor', '#' + hex);
                $('#dateColor').val('#' + hex);
                $(el).ColorPickerHide();
                //更改页面预览内容
                var dateFontSize = $('#dateFontSize').val();
				var dateColor = $('#dateColor').val();
				var dateBgColor = $('#dateBgColor').val();
				var dataStyle = $('#dataStyle').val();
				var dtransparent = $('#dtransparent').val();
				var myDate = new Date();
				var year = myDate.getYear();        //获取当前年份(2位)
				var fullyear = myDate.getFullYear();    //获取完整的年份(4位,1970-????)
				var month = myDate.getMonth() + 1;       //获取当前月份(0-11,0代表1月)
				var date = myDate.getDate();        //获取当前日(1-31)
				var day = myDate.getDay();         //天
				var week;
				var htmlDate;
				switch(day) {
					case 0:
						week = 'Sunday';
						break;
					case 1:
						week = 'Monday';
						break;
					case 2:
						week = 'Tuesday';
						break;
					case 3:
						week = 'Wednesday';
						break;
					case 4:
						week = 'Thursday';
						break;
					case 5:
						week = 'Friday';
						break;
					case 6:
						week = 'Saturday';
						break;
				}
				var red = parseInt(dateBgColor.substr(1, 2), 16);
				var green = parseInt(dateBgColor.substr(3, 2), 16);
				var blue  = parseInt(dateBgColor.substr(5, 2), 16);
				$('#datePreview').css('background-color', 'rgba('+red+','+green+', '+blue+', '+(1-dtransparent/100)+')');
				if(dataStyle == 1) {
					$('#datePreview').html('<font style="font-size:'+dateFontSize+'px;color:'+dateColor+';">'+month+'/'+date+'/'+fullyear+'&nbsp;'+week+'</font>');
				}
				if(dataStyle == 2) {
					$('#datePreview').html('<font style="font-size:'+dateFontSize+'px;color:'+dateColor+';">'+date+'/'+month+'/'+fullyear+'&nbsp;'+week+'</font>');
				}
				if(dataStyle == 3) {
					$('#datePreview').html('<font style="font-size:'+dateFontSize+'px;color:'+dateColor+';">'+fullyear+'/'+month+'/'+date+'&nbsp;'+week+'</font>');
				}
				if(dataStyle == 4) {
					$('#datePreview').html('<font style="font-size:'+dateFontSize+'px;color:'+dateColor+';">'+month+'/'+date+'/'+fullyear+'</font>');
				}
				if(dataStyle == 5) {
					$('#datePreview').html('<font style="font-size:'+dateFontSize+'px;color:'+dateColor+';">'+date+'/'+month+'/'+fullyear+'</font>');
				}
				if(dataStyle == 6) {
					$('#datePreview').html('<font style="font-size:'+dateFontSize+'px;color:'+dateColor+';">'+fullyear+'/'+month+'/'+date+'</font>');
				}
            }
        });
		$('#dateBgColorSelector').ColorPicker({
            color: $('#dateBgColor').val(),
            onShow: function(colpkr){
                $(colpkr).fadeIn(500);
                return false;
            },
            onHide: function(colpkr){
                $(colpkr).fadeOut(500);
                return false;
            },
            onSubmit: function(hsb, hex, rgb, el){
                $('#dateBgColorSelector div').css('backgroundColor', '#' + hex);
                $('#dateBgColor').val('#' + hex);
                $(el).ColorPickerHide();
                //更改页面预览内容
                var dateFontSize = $('#dateFontSize').val();
				var dateColor = $('#dateColor').val();
				var dateBgColor = $('#dateBgColor').val();
				var dataStyle = $('#dataStyle').val();
				var dtransparent = $('#dtransparent').val();
				var myDate = new Date();
				var year = myDate.getYear();        //获取当前年份(2位)
				var fullyear = myDate.getFullYear();    //获取完整的年份(4位,1970-????)
				var month = myDate.getMonth() + 1;       //获取当前月份(0-11,0代表1月)
				var date = myDate.getDate();        //获取当前日(1-31)
				var day = myDate.getDay();         //天
				var week;
				var htmlDate;
				switch(day) {
					case 0:
						week = 'Sunday';
						break;
					case 1:
						week = 'Monday';
						break;
					case 2:
						week = 'Tuesday';
						break;
					case 3:
						week = 'Wednesday';
						break;
					case 4:
						week = 'Thursday';
						break;
					case 5:
						week = 'Friday';
						break;
					case 6:
						week = 'Saturday';
						break;
				}
				var red = parseInt(dateBgColor.substr(1, 2), 16);
				var green = parseInt(dateBgColor.substr(3, 2), 16);
				var blue  = parseInt(dateBgColor.substr(5, 2), 16);
				$('#datePreview').css('background-color', 'rgba('+red+','+green+', '+blue+', '+(1-dtransparent/100)+')');
				if(dataStyle == 1) {
					$('#datePreview').html('<font style="font-size:'+dateFontSize+'px;color:'+dateColor+';">'+month+'/'+date+'/'+fullyear+'&nbsp;'+week+'</font>');
				}
				if(dataStyle == 2) {
					$('#datePreview').html('<font style="font-size:'+dateFontSize+'px;color:'+dateColor+';">'+date+'/'+month+'/'+fullyear+'&nbsp;'+week+'</font>');
				}
				if(dataStyle == 3) {
					$('#datePreview').html('<font style="font-size:'+dateFontSize+'px;color:'+dateColor+';">'+fullyear+'/'+month+'/'+date+'&nbsp;'+week+'</font>');
				}
				if(dataStyle == 4) {
					$('#datePreview').html('<font style="font-size:'+dateFontSize+'px;color:'+dateColor+';">'+month+'/'+date+'/'+fullyear+'</font>');
				}
				if(dataStyle == 5) {
					$('#datePreview').html('<font style="font-size:'+dateFontSize+'px;color:'+dateColor+';">'+date+'/'+month+'/'+fullyear+'</font>');
				}
				if(dataStyle == 6) {
					$('#datePreview').html('<font style="font-size:'+dateFontSize+'px;color:'+dateColor+';">'+fullyear+'/'+month+'/'+date+'</font>');
				}
            }
        });
	},
	initTimeArea : function(){
		//color
        $('#timeColorSelector').ColorPicker({
            color: $('#timeColor').val(),
            onShow: function(colpkr){
                $(colpkr).fadeIn(500);
                return false;
            },
            onHide: function(colpkr){
                $(colpkr).fadeOut(500);
                return false;
            },
            onSubmit: function(hsb, hex, rgb, el){
                $('#timeColorSelector div').css('backgroundColor', '#' + hex);
                $('#timeColor').val('#' + hex);
                $(el).ColorPickerHide();
                var timeFontSize = $('#timeFontSize').val();
				var timeColor = $('#timeColor').val();
				var timeBgColor = $('#timeBgColor').val();
				var timeStyle = $('#timeStyle').val();
				var ttransparent = $('#ttransparent').val();
				var myDate = new Date();
				var hours = myDate.getHours();       //获取当前小时数(0-23)
				var minutes = myDate.getMinutes();     //获取当前分钟数(0-59)
				var red = parseInt(timeBgColor.substr(1, 2), 16);
				var green = parseInt(timeBgColor.substr(3, 2), 16);
				var blue  = parseInt(timeBgColor.substr(5, 2), 16);
				$('#timePreview').css('background-color', 'rgba('+red+','+green+', '+blue+', '+(1-ttransparent/100)+')');
				if(timeStyle == 1) {
					if(hours >= 10 && minutes >= 10) {
						$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'</font>');
					}else if(hours >= 10 && minutes < 10) {
						$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':0'+minutes+'</font>');
					}else if(hours < 10 && minutes < 10) {
						$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':0'+minutes+'</font>');
					}else if(hours < 10 && minutes >= 10) {
						$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'</font>');
					}
				}else {
					if(hours+1 > 12) {
						hours = hours - 12;
						if(hours >= 10 && minutes >= 10) {
							$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'PM</font>');
						}else if(hours >= 10 && minutes < 10) {
							$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':0'+minutes+'PM</font>');
						}else if(hours < 10 && minutes < 10) {
							$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':0'+minutes+'PM</font>');
						}else if(hours < 10 && minutes >= 10) {
							$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'PM</font>');
						}
					}else {
						if(hours >= 10 && minutes >= 10) {
							$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'AM</font>');
						}else if(hours >= 10 && minutes < 10) {
							$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':0'+minutes+'AM</font>');
						}else if(hours < 10 && minutes < 10) {
							$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':0'+minutes+'AM</font>');
						}else if(hours < 10 && minutes >= 10) {
							$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'AM</font>');
						}
					}
				}
            }
        });
		$('#timeBgColorSelector').ColorPicker({
            color: $('#timeBgColor').val(),
            onShow: function(colpkr){
                $(colpkr).fadeIn(500);
                return false;
            },
            onHide: function(colpkr){
                $(colpkr).fadeOut(500);
                return false;
            },
            onSubmit: function(hsb, hex, rgb, el){
                $('#timeBgColorSelector div').css('backgroundColor', '#' + hex);
                $('#timeBgColor').val('#' + hex);
                $(el).ColorPickerHide();
                var timeFontSize = $('#timeFontSize').val();
				var timeColor = $('#timeColor').val();
				var timeBgColor = $('#timeBgColor').val();
				var timeStyle = $('#timeStyle').val();
				var ttransparent = $('#ttransparent').val();
				var myDate = new Date();
				var hours = myDate.getHours();       //获取当前小时数(0-23)
				var minutes = myDate.getMinutes();     //获取当前分钟数(0-59)
				var red = parseInt(timeBgColor.substr(1, 2), 16);
				var green = parseInt(timeBgColor.substr(3, 2), 16);
				var blue  = parseInt(timeBgColor.substr(5, 2), 16);
				$('#timePreview').css('background-color', 'rgba('+red+','+green+', '+blue+', '+(1-ttransparent/100)+')');
				if(timeStyle == 1) {
					if(hours >= 10 && minutes >= 10) {
						$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'</font>');
					}else if(hours >= 10 && minutes < 10) {
						$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':0'+minutes+'</font>');
					}else if(hours < 10 && minutes < 10) {
						$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':0'+minutes+'</font>');
					}else if(hours < 10 && minutes >= 10) {
						$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'</font>');
					}
				}else {
					if(hours+1 > 12) {
						hours = hours - 12;
						if(hours >= 10 && minutes >= 10) {
							$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'PM</font>');
						}else if(hours >= 10 && minutes < 10) {
							$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':0'+minutes+'PM</font>');
						}else if(hours < 10 && minutes < 10) {
							$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':0'+minutes+'PM</font>');
						}else if(hours < 10 && minutes >= 10) {
							$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'PM</font>');
						}
					}else {
						if(hours >= 10 && minutes >= 10) {
							$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'AM</font>');
						}else if(hours >= 10 && minutes < 10) {
							$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':0'+minutes+'AM</font>');
						}else if(hours < 10 && minutes < 10) {
							$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':0'+minutes+'AM</font>');
						}else if(hours < 10 && minutes >= 10) {
							$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'AM</font>');
						}
					}
				}
            }
        });
	},
	initStaticTextArea: function() {
		//color
    	$('#STextcolorSelector').ColorPicker({
            color: $('#static_Color').val(),
            onShow: function(colpkr){
                $(colpkr).fadeIn(500);
                return false;
            },
            onHide: function(colpkr){
                $(colpkr).fadeOut(500);
                return false;
            },
            onSubmit: function(hsb, hex, rgb, el){
                $('#STextcolorSelector div').css('backgroundColor', '#' + hex);
                $('#static_Color').val('#' + hex);
                $('#static_ticker').css('color', '#'+hex);
                $(el).ColorPickerHide();
            }
        });
		$('#STextbgColorSelector').ColorPicker({
            color: $('#static_BgColor').val(),
            onShow: function(colpkr){
                $(colpkr).fadeIn(500);
                return false;
            },
            onHide: function(colpkr){
                $(colpkr).fadeOut(500);
                return false;
            },
            onSubmit: function(hsb, hex, rgb, el){
                $('#STextbgColorSelector div').css('backgroundColor', '#' + hex);
                $('#static_BgColor').val('#' + hex);
                if($('#transparent').attr('checked')) {
                	$('#static_ticker').css('background-color', '#000000');
                }else {
                	$('#static_ticker').css('background-color', '#'+hex);
                }
                
                $(el).ColorPickerHide();
            }
        });
	},
	initWeatherArea : function(){
		//color
        $('#weatherColorSelector').ColorPicker({
            color: $('#weatherColor').val(),
            onShow: function(colpkr){
                $(colpkr).fadeIn(500);
                return false;
            },
            onHide: function(colpkr){
                $(colpkr).fadeOut(500);
                return false;
            },
            onSubmit: function(hsb, hex, rgb, el){
                $('#weatherColorSelector div').css('backgroundColor', '#' + hex);
                $('#weatherColor').val('#' + hex);
                $(el).ColorPickerHide();
            }
        });
		$('#weatherBgColorSelector').ColorPicker({
            color: $('#weatherBgColor').val(),
            onShow: function(colpkr){
                $(colpkr).fadeIn(500);
                return false;
            },
            onHide: function(colpkr){
                $(colpkr).fadeOut(500);
                return false;
            },
            onSubmit: function(hsb, hex, rgb, el){
                $('#weatherBgColorSelector div').css('backgroundColor', '#' + hex);
                $('#weatherBgColor').val('#' + hex);
                $(el).ColorPickerHide();
            }
        });
	},
    changeRssFormat: function(obj, playlistId, areaId){
        showLoading();
        var req = $.post('/interactionpls/do_change_rss_format', {
            playlist_id: playlistId,
            area_id: areaId,
            format: obj.value
        }, function(data){
            if (data.code == 0) {
                $('#ticker').val(data.rss_content);
            }
            hideLoading();
        }, 'json');
        
    },
    initEditMedia: function(){
        $('#mediaTable img').click(function(event){
            var curImg = $(this);
            if (curImg.attr('src').indexOf('_Inactive') > 0) {
                return false;
            }
            var active = $('.active');

            var src = active.attr('src');
            
            var pos = src.indexOf('_Active');
            if (pos > 0) {
                active.attr('src', src.substring(0, pos) + src.substr(pos + 7));
            }
            active.removeClass('active');
            
            src = curImg.attr('src');
            pos = src.indexOf('.');
            curImg.attr('src', src.substring(0, pos) + "_Active" + src.substr(pos));
            curImg.addClass('active');
            $('#transmode').val(curImg.attr('id'));
        });
    },
    initFormDate: function(){
    	for(var i=1; i<10; i++) {
    		var sd = $('#startDate'+i);
			var ed = $('#endDate'+i);
			sd.datepicker({
				dateFormat: 'yy-mm-dd'
			}).removeClass('gray');
	            
			ed.datepicker({
				dateFormat: 'yy-mm-dd'
			}).removeClass('gray');
    	}
 
		interactionpls.bindPlaylistMove();
	},
	bindPlaylistMove: function(){
		var up = $('img.bup');
		var down = $('img.bdown');
		var playlistId = $('#playlistId').val();
		var screenId = $('#screenId').val();
		up.unbind('click').bind('click', function(){
			var img = $(this);
			var cid = img.attr('cid');
			var areaId = img.attr('areaId');
			var pos = parseInt(img.attr('pos'));
			interactionpls.changePlaylistOrder(cid, pos - 1, areaId, playlistId, screenId);
		});
		down.unbind('click').bind('click', function(){
			var img = $(this);
			var cid = img.attr('cid');
			var areaId = img.attr('areaId');
			var pos = parseInt(img.attr('pos'));
			interactionpls.changePlaylistOrder(cid, pos + 1, areaId, playlistId, screenId);
		});
	},
	changePlaylistOrder: function(cid, index, areaId, playlistId, screenId){
        //first id  and second id
        var id = $('#id').val();
        $.post('/interactionpls/btn_do_move_to', {
            id: id,
            cid: cid,
            index: index
        }, function(data){
            if (data.code == 0) {
                interactionpls.loadArea(playlistId, areaId, false, screenId);
            }
            else {
                showMsg(data.msg, 'error');
            }
        }, 'json');
    },
	saveEditWebpage: function() {
		var itemId = $('#itemId').val();
		var playTime = $('#playTime').val();
		var starttime = $('#startDate').val();
		var endtime = $('#endDate').val();
		var areaId = $('#areaId').val();
		var url = $('#url').val();
		var updateF = $('#updateF').val();
		
		$.post('/interactionpls/save_playlist_webpage', {id: itemId, duration: playTime, starttime: starttime, endtime: endtime, url: url, updateF: updateF}, function(data) {
			if (data.code == 0) {
                showFormMsg(data.msg, 'success');
                setTimeout(function(){
                    tb_remove();
                    interactionpls.loadArea($('#playlistId').val(), areaId);
                }, 2000);
            }
            else {
                showFormMsg(data.msg, 'error');
            }
		}, 'json');
	},
    saveEditMedia: function(){
        var playTime = $('#playTime').val();
        var transitionMode = $('#transmode').val();
        var itemId = $('#itemId').val();
        var areaId = $('#areaId').val();
        var imgfit = $('#imgfit').val()
        
        if (transitionMode == undefined) {
            transitionMode = -1;
        }
        $.post('/interactionpls/save_playlist_media', {
            duration: playTime,
            transmode: transitionMode,
            id: itemId,
            imgfit: imgfit
        
        }, function(data){
            if (data.code == 0) {
                showFormMsg(data.msg, 'success');
                setTimeout(function(){
                    tb_remove();
                    interactionpls.loadArea($('#playlistId').val(), areaId);
                }, 2000);
            }
            else {
                showFormMsg(data.msg, 'error');
            }
        }, 'json');
    },
    saveAreaEditMedia: function(){
        var playTime = $('#playTime').val();
        var areaId = $('#areaId').val();
        var type = $('#type').val();
        var transitionMode = $('#transmode').val();
        $.post('/interactionpls/save_playlist_area_media', {
            duration: playTime,
            transmode: transitionMode,
            areaId: areaId,
            type: type
        }, function(data){
            if (data.code == 0) {
                showFormMsg(data.msg, 'success');
                setTimeout(function(){
                    tb_remove();
                    interactionpls.loadArea($('#playlistId').val(), areaId);
                }, 2000);
            }
            else {
                showFormMsg(data.msg, 'error');
            }
        }, 'json');
    },
    editPlayTime_h: function(obj, areaId, id) { //修改网页刷新频率   小时
    	var cur = $(obj);
    	var playTimeh = cur.val();
    	var playTimem = $('#'+id+'_playTimem').val();
    	var playTime = playTimeh + ':' + playTimem;
    	$.post('/interactionpls/do_editPlayTime', {id: id, areaId: areaId, playTimeh: playTimeh, playTimem: playTimem}, function(data) {
    		if(date.code == 1) {
 				showMsg(date.msg, 'error');
	 			setTimeout(function(){
	            	hideMsg();
	            }, 2000);
 			}
    	}, 'json');
    },
    editPlayTime_m: function(obj, areaId, id) { //修改网页刷新频率    分钟
    	var cur = $(obj);
    	var playTimem = cur.val();
    	var playTimeh = $('#'+id+'_playTimeh').val();
    	var playTime = playTimeh + ':' + playTimem;
    	$.post('/interactionpls/do_editPlayTime', {id: id, areaId: areaId, playTimeh: playTimeh, playTimem: playTimem}, function(data) {
    		if(date.code == 1) {
 				showMsg(date.msg, 'error');
	 			setTimeout(function(){
	            	hideMsg();
	            }, 2000);
 			}
    	}, 'json');
    },
    editStartTime_h: function(obj, areaId, id) { //修改开始时间  小时
    	var cur = $(obj);
    	var startTimeh = cur.val();
    	var startTimem = $('#'+id+'_startTimem').val();
    	var playlistId = $('#playlistId').val();
    	var startTime = startTimeh + ':' + startTimem;
    	var endTime = $('#'+id+'_endTimeh').val() + ':' + $('#'+id+'_endTimem').val();
 		$.post('/interactionpls/do_editStartTime', {id: id, areaId: areaId, startTime: startTime, endTime: endTime}, function(date) {
 			if(date.code == 1) {
 				showMsg(date.msg, 'error');
	 			setTimeout(function(){
	            	hideMsg();
	            }, 2000);
 			}
 		}, 'json');
    },
    editStartTime_m: function(obj, areaId, id) { //修改开始时间  分钟
    	var cur = $(obj);
    	var startTimem = cur.val();
    	var startTimeh = $('#'+id+'_startTimeh').val();
    	var playlistId = $('#playlistId').val();
    	var startTime = startTimeh + ':' + startTimem;
    	var endTime = $('#'+id+'_endTimeh').val() + ':' + $('#'+id+'_endTimem').val();
 		$.post('/interactionpls/do_editStartTime', {id: id, areaId: areaId, startTime: startTime, endTime: endTime}, function(date) {
 			if(date.code == 1) {
 				showMsg(date.msg, 'error');
	 			setTimeout(function(){
	            	hideMsg();
	            }, 2000);
 			}
 		}, 'json');
    },
    editEndTime_h: function(obj, areaId, id) { //修改结束时间  小时
    	var cur = $(obj);
    	var endTimeh = cur.val();
    	var endTimem = $('#'+id+'_endTimem').val();
    	var playlistId = $('#playlistId').val();
    	var endTime = endTimeh + ':' + endTimem;
    	var startTime = $('#'+id+'_startTimeh').val() + ':' + $('#'+id+'_startTimem').val();
    	$.post('/interactionpls/do_editEndTime', {id: id, areaId: areaId, endTime: endTime, startTime: startTime}, function(date) {
    		if(date.code == 1) {
 				showMsg(date.msg, 'error');
	 			setTimeout(function(){
	            	hideMsg();
	            }, 2000);
 			}
 		}, 'json');
    },
    editEndTime_m: function(obj, areaId, id) {  //修改结束时间  分钟
    	var cur = $(obj);
    	var endTimem = cur.val();
    	var endTimeh = $('#'+id+'_endTimeh').val();
    	var playlistId = $('#playlistId').val();
    	var endTime = endTimeh + ':' + endTimem;
    	var startTime = $('#'+id+'_startTimeh').val() + ':' + $('#'+id+'_startTimem').val();
    	$.post('/interactionpls/do_editEndTime', {id: id, areaId: areaId, endTime: endTime, startTime: startTime}, function(date) {
    		if(date.code == 1) {
 				showMsg(date.msg, 'error');
	 			setTimeout(function(){
	            	hideMsg();
	            }, 2000);
 			}
 		}, 'json');
    },
    editStatus: function(obj, areaId, id) {
    	var cur = $(obj);
    	var status = $('#status').val();
    	var playlistId = $('#playlistId').val();
    	$.post('/interactionpls/do_editStatus', {id: id, areaId: areaId}, function(date) {
    		//interactionpls.loadArea(playlistId, areaId);
    	});
    },
    editReload: function(obj, areaId, id) {
    	var cur = $(obj);
    	var reload = $('#reload').val();
    	var playlistId = $('#playlistId').val();
    	$.post('/interactionpls/do_editReload', {id: id, areaId: areaId}, function(data) {
    		//interactionpls.loadArea(playlistId, areaId);
    	});
    },
	updateRssFlag: function(obj, areaId, id) {
		var val = $(obj).val();
		if(val == '') {
			val = '<<';
		}
		$.post('/interactionpls/do_updateRssFlag', {val: val,id: id}, function(date) {
			interactionpls.loadArea($('#playlistId').val(), areaId);
		})
	},
	pList: function() {
		$('.table_webpage_list').removeAttr("style");
		$('.table_webpage_grid').css("display","none");
		
	},
	pGrid: function() {
		$('.table_webpage_grid').removeAttr("style");
		$('.table_webpage_list').css("display","none");
	},
	changeStartDate: function(obj, areaId, id, pId) {
		var cur = $(obj);
    	var startDate = $('#startDate'+pId).val();
    	$.post('/interactionpls/do_updateWebpageDate', {id: id, areaId: areaId, type: 1, date: startDate}, function(date) {
    		//interactionpls.loadArea(playlistId, areaId);
    	});
	},
	changeEndDate: function(obj, areaId, id, pId) {
		var cur = $(obj);
    	var endDate = $('#endDate'+pId).val();
    	$.post('/interactionpls/do_updateWebpageDate', {id: id, areaId: areaId, type: 2, date: endDate}, function(date) {
    		//interactionpls.loadArea(playlistId, areaId);
    	});
	},
	font_bold: function(obj, pid, areaId) {
		var id = $('#static_textId').val();
		var bold = 0;
		if(obj.checked) {
			bold = 1;
			$('#static_ticker').css('font-weight', 'bold');
		}else {
			$('#static_ticker').css('font-weight', 'normal');
		}
		$.post('/interactionpls/do_save_static_text', {id: id, pid: pid, area_id: areaId, bold: bold, type:'bold'}, function(data) {});
	},
	font_italic: function(obj, pid, areaId) {
		var id = $('#static_textId').val();
		var italic = 0;
		if(obj.checked) {
			italic = 1;
			$('#static_ticker').css('font-style', 'italic');
		}else {
			$('#static_ticker').css('font-style', 'normal');
		}
		$.post('/interactionpls/do_save_static_text', {id: id, pid: pid, area_id: areaId, italic: italic, type:'italic'}, function(data) {});
	},
	font_underline: function(obj, pid, areaId) {
		var id = $('#static_textId').val();
		var underline = 0;
		if(obj.checked) {
			underline = 1;
			$('#static_ticker').css('text-decoration', 'underline');
		}else {
			$('#static_ticker').css('text-decoration', 'none');
		}
		$.post('/interactionpls/do_save_static_text', {id: id, pid: pid, area_id: areaId, underline: underline, type:'underline'}, function(data) {});
	},
	font_size: function(obj, pid, areaId) {
		var id = $('#static_textId').val();
		var size = $('#sfont_size').val();
		$('#static_ticker').css('font-size', size+'px');
		$.post('/interactionpls/do_save_static_text', {id: id, pid: pid, area_id: areaId, font_size: size, type:'font_size'}, function(data) {});
	},
	font_family: function(obj, pid, areaId) {
		var id = $('#static_textId').val();
		var family = $('#sfont_family').val();
		$('#static_ticker').css('font-family', family);
		$.post('/interactionpls/do_save_static_text', {id: id, pid: pid, area_id: areaId, font_family: family, type:'font_family'}, function(data) {});
	},
	font_position: function(obj, pid, areaId) {
		var id = $('#static_textId').val();
		var position = $('#sfont_position').val();
		var value = "left";
		
		if(position == 2) {
			value = "center";
		}
		if(position == 3) {
			value = "right";
		}
		$('#static_ticker').css('text-align', value);
		$.post('/interactionpls/do_save_static_text', {id: id, pid: pid, area_id: areaId, font_position: position, type:'font_position'}, function(data) {
		
		});
	},
	font_direction: function() {
		if($('#direction').val() == 2) {
			$('#ticker').css('direction', 'rtl');
			$('#ticker').css('unicode-bidi', 'embed');
		}else {
			$('#ticker').css('direction', 'ltr');
		}
	},
	sfont_transparent: function(obj, pid, areaId) {
		var id = $('#static_textId').val();
		var transparent = 1;
		if(obj.checked) {
		 	transparent = 2;
			$('#static_ticker').css('background-color', '#000000');
		}else {
			$('#static_ticker').css('background-color', $('#static_BgColor').val());
		}
		$.post('/interactionpls/do_save_static_text', {id: id, pid: pid, area_id: areaId, transparent: transparent, type:'transparent'}, function(data) {});
	},
	wImage: function(obj, id) {
		if($('#weatherStyle').val() == 5) {
		 	$('#wImage').html('<img src="/images/wstyle5.jpg" />');
		}else {
			$('#wImage').html('<img src="/images/wstyle4.jpg" />');
		}
	},
	dateChange: function(obj, id) {
		var dateFontSize = $('#dateFontSize').val();
		var dateColor = $('#dateColor').val();
		var dateBgColor = $('#dateBgColor').val();
		var dataStyle = $('#dataStyle').val();
		var dtransparent = $('#dtransparent').val();
		var myDate = new Date();
		var year = myDate.getYear();        //获取当前年份(2位)
		var fullyear = myDate.getFullYear();    //获取完整的年份(4位,1970-????)
		var month = myDate.getMonth() + 1;       //获取当前月份(0-11,0代表1月)
		var date = myDate.getDate();        //获取当前日(1-31)
		var day = myDate.getDay();         //天
		var week;
		var htmlDate;
		switch(day) {
			case 0:
				week = 'Sunday';
				break;
			case 1:
				week = 'Monday';
				break;
			case 2:
				week = 'Tuesday';
				break;
			case 3:
				week = 'Wednesday';
				break;
			case 4:
				week = 'Thursday';
				break;
			case 5:
				week = 'Friday';
				break;
			case 6:
				week = 'Saturday';
				break;
		}
		var red = parseInt(dateBgColor.substr(1, 2), 16);
		var green = parseInt(dateBgColor.substr(3, 2), 16);
		var blue  = parseInt(dateBgColor.substr(5, 2), 16);
		$('#datePreview').css('background-color', 'rgba('+red+','+green+', '+blue+', '+(1-dtransparent/100)+')');
		if(dataStyle == 1) {
			$('#datePreview').html('<font style="font-size:'+dateFontSize+'px;color:'+dateColor+';">'+month+'/'+date+'/'+fullyear+'&nbsp;'+week+'</font>');
		}
		if(dataStyle == 2) {
			$('#datePreview').html('<font style="font-size:'+dateFontSize+'px;color:'+dateColor+';">'+date+'/'+month+'/'+fullyear+'&nbsp;'+week+'</font>');
		}
		if(dataStyle == 3) {
			$('#datePreview').html('<font style="font-size:'+dateFontSize+'px;color:'+dateColor+';">'+fullyear+'/'+month+'/'+date+'&nbsp;'+week+'</font>');
		}
		if(dataStyle == 4) {
			$('#datePreview').html('<font style="font-size:'+dateFontSize+'px;color:'+dateColor+';">'+month+'/'+date+'/'+fullyear+'</font>');
		}
		if(dataStyle == 5) {
			$('#datePreview').html('<font style="font-size:'+dateFontSize+'px;color:'+dateColor+';">'+date+'/'+month+'/'+fullyear+'</font>');
		}
		if(dataStyle == 6) {
			$('#datePreview').html('<font style="font-size:'+dateFontSize+'px;color:'+dateColor+';">'+fullyear+'/'+month+'/'+date+'</font>');
		}
	},
	timeChange: function() {
		var timeFontSize = $('#timeFontSize').val();
		var timeColor = $('#timeColor').val();
		var timeBgColor = $('#timeBgColor').val();
		var timeStyle = $('#timeStyle').val();
		var ttransparent = $('#ttransparent').val();
		var myDate = new Date();
		var hours = myDate.getHours();       //获取当前小时数(0-23)
		var minutes = myDate.getMinutes();     //获取当前分钟数(0-59)
		var red = parseInt(timeBgColor.substr(1, 2), 16);
		var green = parseInt(timeBgColor.substr(3, 2), 16);
		var blue  = parseInt(timeBgColor.substr(5, 2), 16);
		$('#timePreview').css('background-color', 'rgba('+red+','+green+', '+blue+', '+(1-ttransparent/100)+')');
		if(timeStyle == 1) {
			if(hours >= 10 && minutes >= 10) {
				$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'</font>');
			}else if(hours >= 10 && minutes < 10) {
				$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':0'+minutes+'</font>');
			}else if(hours < 10 && minutes < 10) {
				$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':0'+minutes+'</font>');
			}else if(hours < 10 && minutes >= 10) {
				$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'</font>');
			}
		}else {
			if(hours+1 > 12) {
				hours = hours - 12;
				if(hours >= 10 && minutes >= 10) {
					$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'PM</font>');
				}else if(hours >= 10 && minutes < 10) {
					$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':0'+minutes+'PM</font>');
				}else if(hours < 10 && minutes < 10) {
					$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':0'+minutes+'PM</font>');
				}else if(hours < 10 && minutes >= 10) {
					$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'PM</font>');
				}
			}else {
				if(hours >= 10 && minutes >= 10) {
					$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'AM</font>');
				}else if(hours >= 10 && minutes < 10) {
					$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':0'+minutes+'AM</font>');
				}else if(hours < 10 && minutes < 10) {
					$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':0'+minutes+'AM</font>');
				}else if(hours < 10 && minutes >= 10) {
					$('#timePreview').html('<font style="font-size:'+timeFontSize+'px;color:'+timeColor+';">'+hours+':'+minutes+'AM</font>');
				}
			}
		}
	},
	ApplyLineBreaks: function(strTextAreaId) {  //textarea字符串根据前台分行格式截取
		var oTextarea = document.getElementById(strTextAreaId);
		if (oTextarea.wrap) {
			oTextarea.setAttribute("wrap", "off");
		}else {
			oTextarea.setAttribute("wrap", "off");
			var newArea = oTextarea.cloneNode(true);
			newArea.value = oTextarea.value;
			oTextarea.parentNode.replaceChild(newArea, oTextarea);
			oTextarea = newArea;
		}
		var strRawValue = oTextarea.value;
		oTextarea.value = "";
		var nEmptyWidth = oTextarea.scrollWidth;
		var nLastWrappingIndex = -1;
		for (var i = 0; i < strRawValue.length; i++) {
			var curChar = strRawValue.charAt(i);
			if (curChar == ' ' || curChar == '-' || curChar == '+')
				nLastWrappingIndex = i;
				oTextarea.value += curChar;
				if (oTextarea.scrollWidth > nEmptyWidth) {
					var buffer = "";
					if (nLastWrappingIndex >= 0) {
						for (var j = nLastWrappingIndex + 1; j < i; j++)
							buffer += strRawValue.charAt(j);
						nLastWrappingIndex = -1;
					}
					buffer += curChar;
					oTextarea.value = oTextarea.value.substr(0, oTextarea.value.length - buffer.length);
					oTextarea.value += "\n" + buffer; 
					
			}
		}
		oTextarea.setAttribute("wrap", "");
		return oTextarea.value.replace(new RegExp("\\n", "g"), "<br/>");
	},
	changeBtnAction: function(obj, areaId, pId) {
		var checkValue=$("#"+areaId+"_btnAction").val();
		if(checkValue == 1) {
  			$('#'+areaId+'_Screen').hide();
  			$('#'+areaId+'_Close').hide();
  			$('#'+areaId+'_screenx').hide();
  			$('#'+areaId+'_screeny').hide();
  			$('#'+areaId+'_screenw').hide();
  			$('#'+areaId+'_screenh').hide();
  			$('#'+areaId+'_timeout').hide();
  			$('#'+areaId+'_play').hide();
  			$('#'+areaId+'_target_1').show();
  			$('#'+areaId+'_target_2').hide();
  			$('#'+areaId+'_target_3').hide();
  		}
  		if(checkValue == 2) {
  			$('#'+areaId+'_Screen').hide();
  			$('#'+areaId+'_Close').hide();
  			$('#'+areaId+'_screenx').hide();
  			$('#'+areaId+'_screeny').hide();
  			$('#'+areaId+'_screenw').hide();
  			$('#'+areaId+'_screenh').hide();
  			$('#'+areaId+'_timeout').hide();
  			$('#'+areaId+'_play').show();
  			$('#'+areaId+'_target_1').hide();
  			$('#'+areaId+'_target_2').show();
  			$('#'+areaId+'_target_3').hide();
  		}
  		if(checkValue == 3) {
  			var value=$("#"+areaId+"_btnScreen").val();
  			$('#'+areaId+'_target_1').hide();
  			$('#'+areaId+'_target_2').hide();
  			$('#'+areaId+'_target_3').show();
  			if(value == 1) {
  				$('#'+areaId+'_Screen').show();
	  			$('#'+areaId+'_Close').show();
	  			$('#'+areaId+'_screenx').hide();
	  			$('#'+areaId+'_screeny').hide();
	  			$('#'+areaId+'_screenw').hide();
	  			$('#'+areaId+'_screenh').hide();
	  			$('#'+areaId+'_timeout').show();
	  			$('#'+areaId+'_play').hide();
  			}else {
  				$('#'+areaId+'_Screen').show();
	  			$('#'+areaId+'_Close').show();
	  			$('#'+areaId+'_screenx').show();
	  			$('#'+areaId+'_screeny').show();
	  			$('#'+areaId+'_screenw').show();
	  			$('#'+areaId+'_screenh').show();
	  			$('#'+areaId+'_timeout').hide();
	  			$('#'+areaId+'_play').hide();
  			}
  		}
  		$.post('/interactionpls/update_btn_setting',{areaId: areaId, pId: pId, action: checkValue}, function(data){});
	},
	changeScreen: function(obj, areaId) {
		var value=$("#"+areaId+"_btnScreen").val();
		if(value == 1) {
  			$('#'+areaId+'_Screen').show();
	  		$('#'+areaId+'_Close').show();
	  		$('#'+areaId+'_screenx').hide();
	  		$('#'+areaId+'_screeny').hide();
	  		$('#'+areaId+'_screenw').hide();
	  		$('#'+areaId+'_screenh').hide();
	  		$('#'+areaId+'_timeout').show();
	  		$('#'+areaId+'_play').hide();
  		}else {
  			$('#'+areaId+'_Screen').show();
	  		$('#'+areaId+'_Close').show();
	  		$('#'+areaId+'_screenx').show();
	  		$('#'+areaId+'_screeny').show();
	  		$('#'+areaId+'_screenw').show();
	  		$('#'+areaId+'_screenh').show();
	  		$('#'+areaId+'_timeout').hide();
	  		$('#'+areaId+'_play').hide();
  		}
	},
	changeTarget: function(obj, areaId, pId, screen_id) {
		var id = $('#'+areaId+'_btnTarget_2').val();
		var type = $('#'+areaId+'_btnTarget_2').find("option:selected").attr('areaType');
		if(type == 1) {
			$('#media0').hide();
		}else {
			$('#media0').show();
		}
		//保存 target
		$.post('/interactionpls/update_btn_setting', {areaId: areaId, pId: pId, goal: id, goalType: type}, function() {
			//删除 Media
			$.post('/interactionpls/delete_btn_media', {pid: pId, areaid: areaId}, function() {
				interactionpls.loadArea(pId, areaId, false, screen_id);
			});
		});
	}
};
