var mediaLib = {
    uploadUrl: '/media/upload_images',
    init: function(){
        $(".wrap .tab-01 a").click(function(event){
            event.preventDefault();
            var cur = $(this).addClass("on");
            cur.siblings("a").removeClass("on");
            var type = cur.attr('type');
            var curType = $('#layoutContent').attr('type');
            if (type != curType) {
                mediaLib.page($('#curpage').val(), $('#orderItem').val(), $('#order').val());
            }
        });
       
	document.onkeyup = function(event){
		if(event.keyCode == 13){
			mediaLib.filter();
		}
	};
	 
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
    checkAll: function(obj){
        $('input:checkbox[name="id"]').each(function(){
            this.checked = obj.checked;
        });
        
        $('input:checkbox[name="checkAll"]').each(function(){
            this.checked = obj.checked;
        });
    },
    changeFilterType: function(obj){
        var filter = $("#filter");
        filter.val('');
        
        if (obj.value == 'add_time') {
            filter.datepicker({
    		    changeMonth: true,
    		    changeYear: true,
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
    filter: function(){
        mediaLib.page(1);
    },
    page: function(curpage, orderItem, order){
        if (curpage == undefined||curpage<=0) {
            curpage = 1;
        }
        
        if (orderItem == undefined) {
            orderItem = 'id';
        }
        if (order == undefined) {
            order = 'desc';
        }
        var type = $('.wrap .tab-01 a.on').attr('type');
        var href = location.href;
        var pos = href.indexOf('?');
        if (pos > 0) {
            href = href.substring(0, pos);
        }
        var filterType = $('#filterType').val();
        var filter = $('#filter').val();
        var filterFolder = $('#filterFolder').val();
        var filterUploader = $('#filterUploader').val();
        var filterTag = $('#filterTag').val();
        
        var req = '';
        if (filter.length > 0) {
            req += '&filter_type=' + filterType + '&filter=' + filter;
        }
        
        if (parseInt(filterFolder) >= 0) {
            req += '&folder_id=' + filterFolder;  
        }
        if (parseInt(filterUploader) > 0) {
            req += '&uid=' + filterUploader;
        }
        if (parseInt(filterTag) > 0) {
            req += '&tag_id=' + filterTag;
        }


        //刷新当前页面信息
        $.get(href + '_refresh/' + curpage + '/' + orderItem + '/' + order + '?type=' + type + req + '&t=' + new Date().getTime(), function(data){
            $('#layoutContent').html(data);
            $('#layoutContent').attr('type', type);
            $('#orderItem').val(orderItem);
            $('#order').val(order);
            $('#curpage').val(curpage);
            hideLoading();
            tb_init('#layoutContent a.thickbox');//pass where to apply thickbox
        });
    },
    refresh: function(type){
        if (type == undefined) {
            type = $('.wrap .tab-01 a.on').attr('type');
        }
        
        var href = location.href;
        var pos = href.indexOf('?');
        if (pos > 0) {
            href = href.substring(0, pos);
        }
        showLoading();
        //刷新当前页面信息
        $.get(href + '_refresh?type=' + type + '&t=' + new Date().getTime(), function(data){
            $('#layoutContent').html(data);
            $('#layoutContent').attr('type', type);
            hideLoading();
            //reinit this box~
            tb_init('#layoutContent a.thickbox');//pass where to apply thickbox
        });
    },
    createFolder: function(obj){
        tb_show(obj.title, '/media/add_folder?width=360&height=240&t=' + new Date().getTime(), false);
    },
  
    editProperty: function(emptyMsg,title){
    	  var ids = new Array();
       		 $('input:checkbox[name="id"]').each(function(){
            if (this.checked) {
                ids.push(this.value);
            }
        });
        
        if (ids.length == 0) {
            alert(emptyMsg);
            return;
        }
        tb_show(title, '/media/edit_property?width=480&height=320&t=' + new Date().getTime(), false);
    },  
    saveFolder: function(obj){
        var name = $('#name').val();
        var descr = $('#descr').val();
        if (name == '') {
            $('#errorName').show();
            return;
        }
        
        $('#errorName').hide();
        var btn = $(obj);
        btn.attr("disabled", "disabled")
        $.post('/folder/do_save_folder', {
            name: name,
            descr: descr
        }, function(data){
            if (data.code == 0) {
                $('#folderId').append('<option value="' + data.id + '" selected="selected">' + data.name + '</option>');
                $('#filterFolder').append('<option value="' + data.id + '">' + data.name + '</option>');
                tb_remove();
            }
            else {
                showFormMsg(data.msg, 'error');
            }
            btn.attr("disabled", "false")
        }, 'json');
    },
    moveTo: function(emptyMedia, emptyFolder,mtype){
        var folderId = $('#folderId').val();
        
        if (folderId == null) {
            alert(emptyFolder);
            return;
        }
        
        if (isNaN(parseInt(folderId))) {
            folderId = 0;
        }
        
        var ids = new Array();
        $('input:checkbox[name="id"]').each(function(){
            if (this.checked) {
                ids.push(this.value);
            }
        });
        
        if (ids.length == 0) {
            alert(emptyMedia);
            return;
        }
        else {
            $.post('/media/do_move_to', {
                folder_id: folderId,
                ids: ids,
                mtype:mtype 
            }, function(data){
                if (data.code == 0) {
                    showMsg(data.msg, 'success');
                    setTimeout(function(){
                        mediaLib.refresh();
                        hideMsg();
                    }, 1000);
                    
                }
                else {
                    showMsg(data.msg, 'error');
                }
            }, 'json');
        }
        
        
    },
    remove: function(id, msg){
        if (confirm(msg)) {
            var req = {
                id: id
            }
            $.post('/media/do_delete', req, function(data){
                if (data.code == 0) {
                    showMsg(data.msg, 'success');
                    mediaLib.refresh();
                    setTimeout(hideMsg, 1000);
                    
                }
                else {
                    showMsg(data.msg, 'error');
                }
            }, 'json');
        }
    },
    removeAll: function(emptyMsg, cfmMsg){
        var ids = new Array();
        $('input:checkbox[name="id"]').each(function(){
            if (this.checked) {
                ids.push(this.value);
            }
        });
        
        if (ids.length == 0) {
            alert(emptyMsg);
            return;
        }
        else 
            if (confirm(cfmMsg)) {
                $.post('/media/do_delete', {
                    id: ids
                }, function(data){
                    if (data.code == 0) {
                        showMsg(data.msg, 'success');
                        mediaLib.refresh();
                        setTimeout(hideMsg, 1000);
                        
                    }
                    else {
                        showMsg(data.msg, 'error');
                    }
                }, 'json');
            }
    },
    
    doSaveProperty: function(){
    	var page_id = $.trim($('li.active').text());
        var ids = new Array();
        $('input:checkbox[name="id"]').each(function(){
        		if (this.checked) {
                ids.push(this.value);
            }
        });
       
        
        var tags_select= String($('#jquery-tagbox-select-options').val());
        
        $('#errorName').fadeOut();
        $.post('/media/do_save_protery', {
        	  id: ids,
            tags_select: tags_select,
       //     play_count_flag:$('#playcountFlag').val(),
        //    play_count:$('#playcountid').val(),
            date_flag: $('#dateFlag').val(),
            startDate: $('#startDate').val(),
            endDate: $('#endDate').val(),
            //alldayFlag: $('#alldayFlag').val(),
            //startTime: $('#startTime').val(),
           // endTime: $('#endTime').val(),
            playTime: $('#playTime').val()
            
        }, function(data){  	 
            if (data.code != 0) {
                showFormMsg(data.msg, 'error');
            }
            else {
                showFormMsg(data.msg, 'success');
                setTimeout(function(){
                    tb_remove();
                    mediaLib.page(page_id);
                }, 200);
            }
        }, 'json');
    },
    
    doSave: function(){
		var page_id = $.trim($('li.active').text());
		
        var name = $('#name').val();
        var descr = $('#descr').val();
        var tags_select= String($('#jquery-tagbox-select-options').val());
        
        if (name.length == 0) {
            $('#errorName').fadeIn();
            return false;
        }
        
        $('#errorName').fadeOut();
        $.post('/media/do_save', {
            name: name,
            descr: descr,
            tags_select: tags_select,
            date_flag: $('#dateFlag').val(),
            startDate: $('#startDate').val(),
            endDate: $('#endDate').val(),
            alldayFlag: $('#alldayFlag').val(),
            startTime: $('#startTime').val(),
            endTime: $('#endTime').val(),
            playTime: $('#playTime').val(),
            id: $('#id').val()
            
        }, function(data){
            if (data.code != 0) {
                showFormMsg(data.msg, 'error');
            }
            else {
                showFormMsg(data.msg, 'success');
                setTimeout(function(){
                    tb_remove();
                    mediaLib.page(page_id);
                }, 200);
            }
        }, 'json');
    },
    playVideo: function(id){
        $.get('/media/get_preview_video?id=' + id, function(data){
            if (data.code == 0) {
                $('#preview').dialog({
                    autoOpen: true,
                    modal: true,
                    width: 640,
                    height: 480,
                    create: function(event, ui){
                        flowplayer("movie", "/static/js/flowplayer/flowplayer-3.2.7.swf", data.video);
                        return;
                        flowplayer("movie", "/static/js/flowplayer/flowplayer-3.2.7.swf", {
                            "clip": {
                                "provider": "MIA TECH",
                                "url": "http://localhost" + data.video
                            },
                            "screen": {
                                "height": "100pct",
                                "top": 0
                            }
                            /*,
                             "plugins": {
                             "controls": {
                             "buttonOffColor": "rgba(130,130,130,1)",
                             "timeColor": "#ffffff",
                             "borderRadius": "0px",
                             "bufferGradient": "none",
                             "sliderColor": "#000000",
                             "zIndex": 1,
                             "backgroundColor": "rgba(0, 0, 0, 0)",
                             "scrubberHeightRatio": 0.6,
                             "tooltipTextColor": "#ffffff",
                             "volumeSliderGradient": "none",
                             "sliderGradient": "none",
                             "spacing": {
                             "time": 6,
                             "volume": 8,
                             "all": 2
                             },
                             "timeBorderRadius": 20,
                             "timeBgHeightRatio": 0.8,
                             "volumeSliderHeightRatio": 0.6,
                             "progressGradient": "none",
                             "height": 26,
                             "volumeColor": "#4599ff",
                             "tooltips": {
                             "marginBottom": 5,
                             "buttons": false
                             },
                             "timeSeparator": " ",
                             "name": "controls",
                             "opacity": 1,
                             "volumeBarHeightRatio": 0.2,
                             "timeFontSize": 12,
                             "left": "50pct",
                             "tooltipColor": "rgba(0, 0, 0, 0)",
                             "bufferColor": "#a3a3a3",
                             "volumeSliderColor": "#ffffff",
                             "border": "0px",
                             "buttonColor": "#ffffff",
                             "durationColor": "#b8d9ff",
                             "autoHide": {
                             "enabled": true,
                             "hideDelay": 500,
                             "mouseOutDelay": 500,
                             "hideStyle": "fade",
                             "hideDuration": 400,
                             "fullscreenOnly": true
                             },
                             "backgroundGradient": "none",
                             "width": "100pct",
                             "sliderBorder": "1px solid rgba(128, 128, 128, 0.7)",
                             "display": "block",
                             "buttonOverColor": "#ffffff",
                             "url": "flowplayer.controls-3.2.5.swf",
                             "timeBorder": "0px solid rgba(0, 0, 0, 0.3)",
                             "progressColor": "#4599ff",
                             "timeBgColor": "rgb(0, 0, 0, 0)",
                             "scrubberBarHeightRatio": 0.2,
                             "bottom": 0,
                             "builtIn": false,
                             "volumeBorder": "1px solid rgba(128, 128, 128, 0.7)",
                             "margins": [2, 12, 2, 12]
                             }
                             }*/
                        });
                    },
                    close: function(event, ui){
                        event.preventDefault();
                        var d = $('#preview');
                        d.parent().remove();
                        //d.remove();
                    }
                });
            }
            else {
                showMsg(data.msg, 'error');
            }
        }, 'json');
    },
    uploader: {
        sessionId: 0,
        uploadUrl: '',
        fileTypes: '*.*',
        fileTypesDesc: 'All Files',
        mediaType: 0, // 1:image 2:video 3:mp3
        swfu: null,
        loadingFolder: false,
        data: new Array(),//上传成功的数据
        init: function(){
            this.initTabs();
            //this.initLocal();
            this.initFtp();
            this.initHttp();
        },
        initTabs: function(){
            $("#TB_ajaxContent .tab-01 a").click(function(){
                $(this).addClass("on").siblings("a").removeClass("on");
                num = $("#TB_ajaxContent .tab-01 a").index($(this));
                $("#TB_ajaxContent .tab-01-in").eq(num).show().siblings("#TB_ajaxContent .tab-01-in").hide();
            });
        },
        initLocal: function(){
            var settings = {
                flash_url: "/static/swfupload/swfupload.swf",
                upload_url: this.uploadUrl,
                post_params: {
                    "session_id": this.sessionId
                },
                file_size_limit: "500 MB",
                file_types: this.fileTypes,
                file_types_description: this.fileTypesDesc,
                file_upload_limit: 500,
                file_queue_limit: 0,
                custom_settings: {
                    progressTarget: "fsUploadProgress",
                    cancelButtonId: "btnCancel",
                    working: function(){
                        $.post('/media/check_storage', {file_size: 0}, function(data){
                            if (data.code == 1) {
                                mediaLib.uploader.swfu.cancelQueue();
                                alert(data.msg);
								return false;
                            }
                        }, 'json');
                        
                        $('#TB_closeWindowButton').unbind('click');
                        document.onkeydown = function(e){
                            if (e == null) { // ie
                                keycode = event.keyCode;
                            }
                            else { // mozilla
                                keycode = e.which;
                            }
                            if (keycode == 27) { // close
                                //nothing....
                            }
                        };
                        
						return true;
                    },
                    finished: function(){
                       /* $("#TB_closeWindowButton").click(tb_remove);
                        document.onkeydown = function(e){
                            if (e == null) { // ie
                                keycode = event.keyCode;
                            }
                            else { // mozilla
                                keycode = e.which;
                            }
                            if (keycode == 27) { // close
                                tb_remove();
                            }
                        };
                        
                        //after upload complate, then refresh~
                        mediaLib.refresh();
                        tb_remove();
						*/
                    }
                },
                debug: false,
                
                // Button settings
                //button_image_url: "/static/swfupload/ImageNoText_65x29.png",
                button_width: "80",
                button_height: "26",
                button_placeholder_id: "spanButtonPlaceHolder",
                button_text: '<span class="theFont">&nbsp;&nbsp;Browse</span>',
                button_text_style: ".theFont { font-size: 14;}",
                button_text_left_padding: 6,
                button_text_top_padding: 3,
                
                // The event handler functions are defined in handlers.js
                file_queued_handler: fileQueued,
                file_queue_error_handler: fileQueueError,
                file_dialog_complete_handler: fileDialogComplete,
                upload_start_handler: uploadStart,
                upload_progress_handler: uploadProgress,
                upload_error_handler: uploadError,
                upload_success_handler: uploadSuccess,
                upload_complete_handler: uploadComplete,
                queue_complete_handler: queueComplete // Queue plugin event
            };
           
            function fileDialogComplete(selectedNum, queuedNum, totalNum) {
           	 	var name = '';
							var filename = '';
            	for(var i=0; i < selectedNum; i++) {
            		var file = 'file_'+0;
            		file = this.getFile(i);
							filename = file.name;
							if(filename.indexOf('\'') > -1) {
								alert('The filename cannot contain special symbol \' * | \: "< / > ?');
								mediaLib.uploader.swfu.cancelQueue();
								return false;
							}
            		name = name + '\''+file.name+ '\'' + ',';
           }
				
          	$.post('/media/media_name_check', {name: name}, function(data){
						if(data==1) {
							$("#rotateConfirm").dialog({
			                resizable: false,
			                height: 240,
			                modal: true,
			                draggabled: false,
			                buttons: {
			                    "Yes": function(){
					                   	$.post('/media/media_confirm_value', {value: 1}, function(data2){
					                   		$("#rotateConfirm").dialog("close");
											mediaLib.uploader.swfu.startUpload();
										});
			                    },
			                    "No": function(){
			                        $.post('/media/media_confirm_value', {value: 0}, function(data2){
					                   		$("#rotateConfirm").dialog("close");
											mediaLib.uploader.swfu.startUpload();
										});
			                    }
			                }
			            });
	                }else {
	                	mediaLib.uploader.swfu.startUpload();
	                }
				});
            }
            
			function uploadStart(file) {
				$.post('/media/check_storage', {file_size: file.size}, function(data){
					if (data.code == 1) {
						mediaLib.uploader.swfu.cancelQueue();
                        alert(data.msg);
						return false;
					}else {
						if(file.name.length > 90) {
							$('#'+file.id).find('div.progressBarStatus').html('Maximum length of file name is 90 characters!');
							//$('#'+file.id).find('div.progressBarInProgress').css('width', '0%');
						}else {
							$('#'+file.id).find('div.progressBarStatus').html('Uploading...');
							$('#'+file.id).find('div.progressBarInProgress').css('width', '0%');
						}
					}
                }, 'json');
		    }
		
            function uploadProgress(file,bytesCompleted,bytesTotal) {
		    	if(file.name.length > 90) {
		    		$('#'+file.id).find('div.progressBarStatus').html('<font color="red">Error ! Maximum length of file name is 90 characters!</font>');
		    	}else {
		    		var percentage = Math.round((bytesCompleted / bytesTotal) * 100);
		    		$('#'+file.id).find('div.progressBarInProgress').css('width', percentage + '%');
		    	}
		    }
		  
		  	function uploadComplete(file) {
		  		if(file.name.length > 90) {
		  			$('#'+file.id).find('div.progressBarStatus').html('<font color="red">Error ! Maximum length of file name is 90 characters!</font>');
		  		}else {
		  			$('#'+file.id).find('div.progressBarStatus').html('<font color="blue">Media File uploaded successfully</font>');
		  		}
		  		//mediaLib.refresh();
				$("#TB_closeWindowButton").click(tb_remove);
                        document.onkeydown = function(e){
                            if (e == null) { // ie
                                keycode = event.keyCode;
                            }
                            else { // mozilla
                                keycode = e.which;
                            }
                            if (keycode == 27) { // close
                                tb_remove();
                            }
                        };
                        
                        //after upload complate, then refresh~
                        mediaLib.refresh();
                        //tb_remove();
		  	}
			
			function queueComplete() {
		  		tb_remove();
		  	}
            
            mediaLib.uploader.swfu = new SWFUpload(settings);
            //onclick="mediaLib.uploader.swfu.cancelQueue();"
        },
        initFtp: function(){
            /*$('#saveFtp').click(function(event){
             mediaLib.uploader.saveFtp();
             });
             $('#addFtp').click(function(event){
             mediaLib.uploader.addFtp();
             });*/
            /*$('#reset').click(function(event){
             document.forms['siteForm'].reset();
             });*/
            $('#ftpSite').change(function(event){
                //alert(this);
                var id = this.value;
                if (id > 0) {
                    $.get('/media/get_ftp_config?id=' + this.value, function(data){
                        if (data) {
                            $('#ftpId').val(data.id);
                            $('#ftpProfile').text(data.profile);
                            $('#ftpServer').text(data.server);
                            $('#ftpPort').text(data.port);
                            $('#ftpPasv').html('<img src="/images/icons/checkbox_' + (data.pasv ? 'on' : 'off') + '.gif" alt=""/>');
                            $('#ftpAccount').text(data.account);
                            $('#ftpPassword').text(data.password);
                            mediaLib.uploader.initConnectButton();
                            
                        }
                    }, 'json');
                }
                else {
                    //document.forms['siteForm'].reset();
                    $('#ftpId').val(0);
                    $('#ftpProfile').text('');
                    $('#ftpServer').text('');
                    $('#ftpPort').text('');
                    $('#ftpPasv').text('');
                    $('#ftpAccount').text('');
                    $('#ftpPassword').text('');
                    mediaLib.uploader.disableConnectButton();
                }
                
                return false;
            });
        },
        initConnectButton: function(){
            var c = $('#connect');
            c.unbind('click');
            c.removeClass('btn-02');
            c.addClass('btn-01');
            c.click(function(event){
                mediaLib.uploader.connectFtp();
            });
        },
        disableConnectButton: function(){
            var c = $('#connect');
            c.unbind('click');
            c.removeClass('btn-01');
            c.addClass('btn-02');
        },
        initFtpButton: function(){
            var ca = $('#checkall');
            ca.unbind('click');
            ca.removeClass('btn-02');
            ca.addClass('btn-01');
            ca.click(function(event){
                $("#fileList input:checkbox").each(function(){
                    this.checked = true;
                });
            });
            
            var uca = $('#uncheckall');
            uca.unbind('click');
            uca.removeClass('btn-02');
            uca.addClass('btn-01');
            uca.click(function(event){
                $("#fileList input:checkbox").each(function(){
                    this.checked = false;
                });
            });
            
            var ms = $('#ftpMediaSave');
            ms.unbind('click');
            ms.removeClass('btn-02');
            ms.addClass('btn-01');
            ms.click(function(event){
                mediaLib.uploader.saveFtpMedia();
            });
        },
        destoryFtpButton: function(){
            var ca = $('#checkall');
            ca.unbind('click');
            ca.removeClass('btn-01');
            ca.addClass('btn-02');
            
            var uca = $('#uncheckall');
            uca.unbind('click');
            uca.removeClass('btn-01');
            uca.addClass('btn-02');
            
            var ms = $('#ftpMediaSave');
            ms.unbind('click');
            ms.removeClass('btn-01');
            ms.addClass('btn-02');
        },
        changeButton: function(btn, status){
            //alert('btn:' + btn +" status:" + status);
            if ('disable' == status) {
                $('#' + btn).attr('disabled', 'disabled');
            }
            else {
                $('#' + btn).removeAttr('disabled');// ('disabled','false');
            }
            
        },
        checkFtp: function(){
            var id = $('#ftpId').val();
            if (id == undefined) {
                id = 0;
            }
            if (id > 0) {
                return {
                    id: id
                }
            }
            else {
                return false;
            }
            
            var profile = $('#ftpProfile').val();
            var server = $('#ftpServer').val();
            var port = $('#ftpPort').val();
            var pasv = $('#ftpPasv').attr('checked') ? 1 : 0;
            var account = $('#ftpAccount').val();
            var password = $('#ftpPassword').val();
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
        saveFtp: function(){
            var data = this.checkFtp();
            if (data) {
                $.post('/ftp/do_save_ftp', data, function(json){
                    $('.validateTips').html(json.msg);
                }, 'json');
            }
        },
        addFtp: function(){
            var data = this.checkFtp();
            if (data) {
                $.post('/ftp/do_save_ftp', data, function(json){
                    $('.validateTips').html(json.msg);
                    if (json.code == 0) {
                        $('#ftpSite').append('<option value="' + json.id + '"  selected="selected">' + json.server + '</option>');
                        $('#ftpId').val(json.data.id);
                        
                    }
                }, 'json');
            }
        },
        connectFtp: function(){
            this.cleanFtpContent();
            var data = this.checkFtp();
            hideFormMsg();
            if (data) {
                mediaLib.uploader.disableConnectButton();
                var tree = {
                    id: data.id,
                    file_types: mediaLib.uploader.fileTypes
                };
                //show loading
                $('#fileTree').html('<div style="top: 45%; margin-left: 0pt;" class="loading-01">Loading ......</div>');
                
                $.post('/media/load_ftp_media_list', tree, function(treeResult){
                    //hide loading...
                    $('#fileTree').html('');
                    
                    if (treeResult.code == 1) {
                        showFormMsg(treeResult.msg, 'error');
                        mediaLib.uploader.initConnectButton();
                    }
                    else {
                        hideFormMsg();
                        var pwd = treeResult.file.pwd;
                        var treeData = '<?xml version="1.0" encoding="UTF-8"?><tree id="0"></tree>';
                        var t = new dhtmlXTreeObject("fileTree", "100%", "100%", 0);
                        t.setImagePath("/static/js/dhtmlxtree/imgs/");
                        t.enableDragAndDrop(0);
                        t.loadXMLString(treeData);
                        t.attachEvent("onDblClick", mediaLib.uploader.loadSubFolder);
                        //addData
                        var list = treeResult.file.list;
                        t.insertNewChild("0", pwd, pwd);
                        mediaLib.uploader.tree = t;
                        mediaLib.uploader.addTreeList(pwd, list);
                        
                        
                    }
                }, 'json');
            }
        },
        loadSubFolder: function(tid, tree){
            if (mediaLib.uploader.loadingFolder) {
                return true;
            }
            else 
                if (tree.hasChildren(tid)) {
                    //show...
                    return true;
                }
                else {
                    //set loading...
                    mediaLib.uploader.loadingFolder = true;
                    var image = $('.selectedTreeRow').parent().prev().find('img');
                    var oldImage = image.attr('src');
                    image.attr('src', '/images/loading.gif');
                    var treeReq = {
                        id: $('#ftpId').val(),
                        file_types: mediaLib.uploader.fileTypes,
                        pwd: tid
                    };
                    mediaLib.uploader.destoryFtpButton();
                    var req = $.post('/media/load_ftp_media_list', treeReq, function(treeResult){
                        image.attr('src', oldImage);
                        if (treeResult.code == 1) {
                            showFormMsg(treeResult.msg, 'error');
                        }
                        else {
                            hideFormMsg();
                            mediaLib.uploader.loadingFolder = false;
                            var pwd = treeResult.file.pwd;
                            var list = treeResult.file.list;
                            mediaLib.uploader.addTreeList(pwd, list);
                            
                            
                        }
                    }, 'json');
                    
                    return false;
                }
        },
        cleanFtpContent: function(){
            $('#fileList').html('');
            $('#ftpTree').html('');
        },
        addTreeList: function(parentId, list){
            var fileList = $('#fileList');
            fileList.html('');
            var t = mediaLib.uploader.tree;
            var hasContent = false;
            for (var i = 0; i < list.length; i++) {
                if (list[i].dir) {
                    t.insertNewChild(parentId, parentId + list[i].name + "/", list[i].name, "", "folderClosed.gif", "", "", "", "", "SELECT");
                }
                else {
                    hasContent = true;
                    fileList.append('<p><input type="checkbox" name="mediaId" value="' + parentId + list[i].name + '" filesize="' + list[i].size + '" />&nbsp;' + list[i].name + '</p>');
                }
            }
            if (hasContent) {
                mediaLib.uploader.initFtpButton();
            }
            else {
                mediaLib.uploader.destoryFtpButton();
            }
        },
        saveFtpMedia: function(){
            var media = [];
            $("#fileList input:checkbox").each(function(){
                if (this.checked) {
                    //var m = {};
                    var obj = $(this);
                    //m.file = obj.val();
                    //m.size = obj.attr('filesize');
                    media.push({
                        file: obj.val(),
                        size: obj.attr('filesize')
                    });
                }
            });
            
            if (media.length == 0) {
                showFormMsg('at least chose one file....', 'error');
                return false;
            }
            var id = $('#ftpId').val();
            var data = {
                id: id,
                media: media,
                media_type: this.mediaType
            }
            
            
            $.post('/media/do_save_ftp_media', data, function(json){
                if (json.code != 0) {
                    showFormMsg(json.msg, 'error');
                }
                else {
                    showFormMsg(json.msg, 'success');
                    //刷新
                    mediaLib.refresh();
                }
            }, 'json');
            
            
            return false;
        },
        initHttp: function(){
            $('#httpMediaSave').click(function(event){
                mediaLib.uploader.saveHttpMedia();
            });
            $('#httpMediaReset').click(function(event){
                document.forms['httpForm'].reset();
            });
        },
        checkHttpUrl: function(obj, event){
            var value = obj.value;
            if (this.checkHttpUrlFormat(value)) {
                $.post('/media/get_http_file_size', {
                    url: value
                }, function(json){
                    if (json.code == 1) {
                        showFormMsg(json.msg, 'error');
                        $(obj).removeAttr('filesize');
                    }
                    else {
                        hideFormMsg();
                        $(obj).attr('filesize', json.size);
                    }
                }, 'json');
            }
            return false;
        },
        checkHttpUrlFormat: function(url){
            var len = url.length;
            if (len > 0) {
            
                //检验文件后缀
                if ('*' != this.fileTypes) {
                    var pos = url.lastIndexOf('.');
                    if (pos > 0) {
                        var ext = url.substring(pos + 1, len);
                        var lowerExt = ext.toLowerCase();
                        if (this.fileTypes.indexOf(lowerExt) > 0) {
                            return true;
                        }
                    }
                    
                }
            }
            return false;
        },
        saveHttpMedia: function(){
        
            var name = $('#httpName').val();
            var descr = $('#httpDescr').val();
            var urlObj = $('#httpUrl');
            var url = urlObj.val();
            var size = urlObj.attr('filesize');
            if (size == undefined) {
                size = 0;
            }
            var data = {
                name: name,
                descr: descr,
                url: url,
                size: size,
                media_type: this.mediaType
            }
            if (!this.checkHttpUrlFormat(url)) {
                $('#errorUrl').fadeIn();
                return false;
            }
            
            $('#errorUrl').fadeOut();
            $.post('/media/do_save_http_media', data, function(json){
                if (json.code == 0) {
                    showFormMsg(json.msg, 'success');
                    setTimeout(hideFormMsg, 200);
                    //刷新当前页面
                    mediaLib.refresh();
                }
                else {
                    showFormMsg(json.msg, 'error');
                }
                
            }, 'json');
            
        },
        destoryDialog: function(isCancel){
            var d = $('#media-dialog');
            if (isCancel) {
                d.dialog('destory');
            }
            d.parent().remove();
            d.remove();
        }
    },
    nodes: {
        zNodes: null,
        zTopNodes: null,
        setting:null,
        hideMenu:function() {
            $("#folderSelTop_menuContent").fadeOut("fast");     
            $("#folderSel_menuContent").fadeOut("fast");
            $("#upfolderSel_menuContent").fadeOut("fast");
            
        },
        showMenu:function(id) {
            var obj = $("#"+id);
            var objOffset = obj.offset();
            cur_id = id;
            console.log(id);
            console.log(objOffset);

            $("#"+id+"_menuContent").css({left:objOffset.left + "px", top:objOffset.top + obj.outerHeight() + "px"}).slideDown("fast");
        },

   
        folderArray:function(arr){
            var newArr = [];
            var forFn = function (arr) {
                arr.forEach((item) => { 
                         newArr.push(item.id);

                         if(item.inc) 
                         {
                             forFn(item.inc);                    
                         }                   
                })
            }
            forFn(arr)
            return newArr;
            
        },

        onClick:function(e, treeId, treeNode) {
            var zTree = $.fn.zTree.getZTreeObj(treeId),
            nodes = zTree.getSelectedNodes(),
            v = "";
            id="";
            v = nodes[0].name;
            id=nodes[0].id;


            cur_obj =  $("#"+cur_id);
            cur_obj.attr("value", v);

            mediaLib.nodes.hideMenu();
            if(cur_id=='folderSelTop'){
                var ids = [];
                if(nodes[0].children&&nodes[0].id>0){
                    ids= mediaLib.nodes.folderArray(nodes[0].children);
                }
                ids.push(id);
                $("#filterFolder").val(String(ids));
                mediaLib.filter();
            }else{
                $("#folderId").val(id);
            }
        
        },
    },
};
