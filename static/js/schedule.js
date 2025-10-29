var schedule = {
    index: {
        showAll: function(event){
            $('.fc-state-active').removeClass('fc-state-active');
            $(event.currentTarget).addClass('fc-state-active');
            
            $('#list').show();
            $('#calendar').hide();
        },
        showDay: function(event){
        
            $('.fc-state-active').removeClass('fc-state-active');
            $(event.currentTarget).addClass('fc-state-active');
            
            $('#list').hide();
            var c = $('#calendar');
            c.show();
            c.fullCalendar('changeView', 'agendaDay');
        },
        showWeek: function(event){
            $('.fc-state-active').removeClass('fc-state-active');
            $(event.currentTarget).addClass('fc-state-active');
            
            $('#list').hide();
            var c = $('#calendar');
            c.show();
            c.fullCalendar('changeView', 'agendaWeek');
        },
        showMonth: function(event){
            $('.fc-state-active').removeClass('fc-state-active');
            $(event.currentTarget).addClass('fc-state-active');
            
            $('#list').hide();
            var c = $('#calendar');
            c.show();
            c.fullCalendar('changeView', 'month');
        },
        init: function(type, name){
            $('.fc-button-all').click(function(event){
                schedule.index.showAll(event);
            });
            $('.fc-button-day').click(function(event){
                schedule.index.showDay(event);
            });
            $('.fc-button-week').click(function(event){
                schedule.index.showWeek(event);
            });
            $('.fc-button-month').click(function(event){
                schedule.index.showMonth(event);
            });
            
            //this.initCalendar();
            
            //tb_init('a.thickbox, area.thickbox, input.thickbox');//pass where to apply thickbox
        
        },
        initCalendar: function(type, name){
            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();
            $('#calendar').fullCalendar({
                editable: false,
                slotMinutes: 30,
                firstHour: 7,
				timeFormat:{'': 'h(:mm)tt'},
                loading: function(isLoading, view){
                    if (isLoading) {
                        showLoading();
                    }
                    else {
                        hideLoading();
                    }
                },
                eventDrop: function(event, dayDelta, minuteDelta, allDay, revertFunc, jsEvent, ui, view){
                    var id = event.id;
                    var days = dayDelta;
                    var minutes = minuteDelta;
                    var dayFlag = allDay ? 1 : 0;
                    var viewName = view.name;
                    var eventType = 'drop';
                    var start = Date.parse(event.start) / 1000;//s
                    var end = start + 2 * 60 * 60;//s
                    if (event.end != null) {
                        end = Date.parse(event.end) / 1000; //s
                    }
                    
                    var data = {
                        id: id,
                        days: days,
                        minutes: minutes,
                        day_flag: dayFlag,
                        view: viewName,
                        event_type: eventType,
                        start: start,
                        end: end
                    };
                    $.post('/schedule/update_event', data, function(result){
                        if (result.code == 1) {
                            revertFunc();
                        }
                    }, 'json');
                },
                eventResize: function(event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view){
                    var id = event.id;
                    var days = dayDelta;
                    var minutes = minuteDelta;
                    var viewName = view.name;
                    var eventType = 'resize';
                    var start = Date.parse(event.start) / 1000;//s
                    var end = start + 2 * 60 * 60;//s
                    if (event.end != null) {
                        end = Date.parse(event.end) / 1000;
                    }
                    
                    var data = {
                        id: id,
                        days: days,
                        minutes: minutes,
                        view: viewName,
                        event_type: eventType,
                        start: start,
                        end: end
                    };
                    
                    $.post('/schedule/update_event', data, function(result){
                        if (result.code == 1) {
                            revertFunc();
                        }
                    }, 'json');
                },
                eventClick: function(event, jsEvent, view){
                    //alert(event.id);
                    tb_show(event.title, '/schedule/view?id=' + event.id + '&t=' + new Date().getTime() + '&width=720&height=600', '');
                },
                eventMouseover: function(event, jsEvent, view){
                    //alert(event.id);
                },
                eventMouseout: function(event, jsEvent, view){
                    //alert(event.id);
                },
                //events: '/schedule/events?type=&name='
                events: '/schedule/events?type='+type+'&name='+name
            });
        },
        refresh: function(curpage, orderItem, order){
            if (curpage == undefined) {
                curpage = 1;
            }
            if (orderItem == undefined) {
                orderItem = 'id';
            }
            if (order == undefined) {
                order = 'desc';
            }
            showLoading();
            $.get('/schedule/refresh/' + curpage + '/' + orderItem + '/' + order + '?t=' + new Date().getTime(), function(data){
                $('.wrap').html(data);
                tb_init('a.thickbox');
                hideLoading();
            });
        },
        remove: function(id, msg){
            if (confirm(msg)) {
                $.post('/schedule/do_delete?t=' + new Date().getTime(), {
                    id: id
                }, function(data){
                    if (data.code == 0) {
                        showMsg(data.msg, 'success');
                        schedule.index.filter();
                        setTimeout(hideMsg, 1000);
                    }
                    else {
                        showMsg(data.msg, 'error');
                    }
                }, 'json');
            }
        },
		filter: function(curpage, orderItem, order){
			curpage   = curpage||1;
			orderItem = orderItem || "id";
			order     = order || "desc";
			
			var value = $('#filter_name').val();
			var type = $('#filter_type').val();
			showLoading();
			$.get("/schedule/filter/"+ curpage + "/" + orderItem + "/" + order + "?t=" + new Date().getTime(), {
				value : value,
				type  : type,
			},function(data){
					$('.schedule').html(data);	
					hideLoading();
				}
			).error(function(e){
			console.info(e.responseText);
			});
		}
    },
    form: {
        id: 0,
        group_deletes: [],
        playlist_deletes: [],
        init: function(){
        
            var sd = $('#startDate');
            var ed = $('#endDate');
            sd.datepicker({
                dateFormat: 'yy-mm-dd'
            }).removeClass('gray');
            
            ed.datepicker({
                dateFormat: 'yy-mm-dd'
            }).removeClass('gray');
            
			schedule.form.bindPlaylistMove();
			
            /*document.onkeydown=function(e){
             if (e == null) { // ie
             keycode = event.keyCode;
             }
             else { // mozilla
             keycode = e.which;
             }
             if (keycode == 8) { // close
             return false;
             }
             }*/
            /*$('#dayflag').change(function(event){
             if(this.checked){
             $('#sun').removeAttr('checked').attr('disabled','disabled');
             $('#mon').removeAttr('checked').attr('disabled','disabled');
             $('#tue').removeAttr('checked').attr('disabled','disabled');
             $('#wed').removeAttr('checked').attr('disabled','disabled');
             $('#thu').removeAttr('checked').attr('disabled','disabled');
             $('#fri').removeAttr('checked').attr('disabled','disabled');
             $('#sat').removeAttr('checked').attr('disabled','disabled');
             }else{
             $('#sun').removeAttr('disabled');
             $('#mon').removeAttr('disabled');
             $('#tue').removeAttr('disabled');
             $('#wed').removeAttr('disabled');
             $('#thu').removeAttr('disabled');
             $('#fri').removeAttr('disabled');
             $('#sat').removeAttr('disabled');
             }
             });
             var tf = $('#timeflag').change(function(event){
             if(this.checked){
             $('#starttime').unbind('focus').addClass('gray');
             $('#stoptime').unbind('focus').addClass('gray');
             }else{
             $('#starttime').focus(function(event){
             WdatePicker({skin:'default',
             dateFmt:'HH:mm',
             lang: curLang,
             quickSel: ['00:00','08:00','12:00', '18:00','21:00','23:59']
             });
             }).removeClass('gray');
             $('#stoptime').focus(function(event){
             WdatePicker({skin:'default',
             dateFmt:'HH:mm',
             lang: curLang,
             quickSel: ['00:00','08:00','12:00', '18:00','21:00','23:59']
             });
             }).removeClass('gray');
             }
             });
             
             if (!tf.attr('checked')) {
             $('#starttime').focus(function(event){
             WdatePicker({
             skin: 'default',
             dateFmt: 'HH:mm',
             lang: curLang,
             quickSel: ['00:00','08:00','12:00', '18:00','21:00','23:59']
             });
             });
             
             $('#stoptime').focus(function(event){
             WdatePicker({
             skin: 'default',
             dateFmt: 'HH:mm',
             lang: curLang,
             quickSel: ['00:00','08:00','12:00', '18:00','21:00','23:59']
             });
             });
             }
             
             
             $('#startdate').focus(function(event){
             WdatePicker({skin:'default',dateFmt:'yyyy-MM-dd',lang:curLang});
             });
             $('#stopdate').focus(function(event){
             WdatePicker({skin:'default',dateFmt:'yyyy-MM-dd',lang:curLang});
             });*/
        },
        createSchedule: function(){
            var name = $('#name').val();
            var descr = $('#descr').val();
            var sch_type = $('#sch_type').val();
            var allDayFlag = 1;
            if (name.indexOf("&") >= 0 || name.indexOf("<") >= 0 || name.indexOf(">") >= 0 || name.indexOf("'") >= 0 || name.indexOf("\\") >= 0 || name.indexOf("%") >= 0) {
			showFormMsg("Special symbols (& < > ' \\ %) are not allowed in the schedule name.", 'error');
            return false;
			}
            $.post('/schedule/do_save', {
                name: name,
                descr: descr,
                allDayFlag: allDayFlag,
                sch_type: sch_type
            }, function(data){
                if (data.code == 0) {
                    showFormMsg(data.msg, 'success');
					tb_remove();
                    setTimeout(function(){
                        location.href = '/schedule/edit?id=' + data.schedule.id;
                    }, 1000);
                }
                else {
                    showFormMsg(data.msg, 'error');
                }
            }, 'json');
        },
        toggleInteraction: function(obj){
            $('#innerInteraction').toggle();
            var img = $(obj).children('img');
            if (img.attr('src').indexOf('16-05') > 0) {
                img.attr('src', '/images/icons/16-06.gif');
            }
            else {
                img.attr('src', '/images/icons/16-05.gif');
            }
            
        },
        groupPage: function(curpage, sch_type){
            $.get('/schedule/add_group?curpage=' + curpage + '&sch_type='+sch_type+'&t=' + new Date().getTime(), function(data){
                $('#TB_ajaxContent').html(data);
            })
        },
        checkAllGroup: function(obj){
            $('input:checkbox[name="gid"]').each(function(){
                this.checked = obj.checked;
            });
        },
        toggleGroup: function(obj){
            $('#innerGroup').toggle();
            
            var img = $(obj).children('img');
            if (img.attr('src').indexOf('16-05') > 0) {
                img.attr('src', '/images/icons/16-06.gif');
            }
            else {
                img.attr('src', '/images/icons/16-05.gif');
            }
            
        },
        refreshInnerGroup: function(){
            var id = $('#id').val();
            $.get('/schedule/refresh_inner_group?id=' + id + '&t=' + new Date().getTime(), function(data){
                $('#innerGroup').html(data);
            });
        },
        addGroup: function(emptyMsg){
            var gids = new Array();
            $('input:checkbox[name="gid"]:checked').each(function(){
                gids.push(this.value);
            });
            
            if (gids.length == 0) {
                alert(emptyMsg);
                return;
            }
            
            var id = $('#id').val();
            $.post('/schedule/save_group', {
                id: id,
                gids: gids
            }, function(data){
                if (data.code == 0) {
                    showFormMsg(data.msg, 'success');
                    schedule.form.refreshInnerGroup();
                    setTimeout(tb_remove, 1000);
                }
                else {
                    showFormMsg(data.msg, 'error');
                }
            }, 'json');
        },
        removeGroup: function(gid, cfmMsg){
            if (confirm(cfmMsg)) {
                var id = $('#id').val();
                $.post('/schedule/remove_group', {
                    id: id,
                    gid: gid
                }, function(data){
                    if (data.code == 0) {
                        showMsg(data.msg, 'success');
                        schedule.form.refreshInnerGroup();
                        setTimeout(function(){
                            hideMsg();
                        }, 1000)
                    }
                    else {
                        showMsg(data.msg, 'error');
                    }
                }, 'json');
            }
        },
        checkAllPlaylist: function(obj){
            $('input:checkbox[name="pid"]').each(function(){
                this.checked = obj.checked;
            });
        },
        togglePlaylist: function(obj){
            $('#innerPlaylist').toggle();
            var $this = $(obj);
            var img = $this.children('img');
            
            if (img.attr('src').indexOf('16-05') > 0) {
                img.attr('src', '/images/icons/16-06.gif');
                img.attr('title', img.attr('tc'));
            }
            else {
                img.attr('src', '/images/icons/16-05.gif');
                img.attr('title', img.attr('te'));
            }
        },
        togglePlaylistSchedule: function(obj, playlistId){
            $('#pl_' + playlistId).toggle();
            var img = $(obj).children('img');
            if (img.attr('src').indexOf('16-05') > 0) {
                img.attr('src', '/images/icons/16-06.gif');
                img.attr('title', img.attr('tc'));
            }
            else {
                img.attr('src', '/images/icons/16-05.gif');
                img.attr('title', img.attr('te'));
            }
        },
        initPlaylistFlag: function(playlistId){
        
            $("#tab-" + playlistId + " a").click(function(event){
                event.preventDefault();
                $(this).addClass("on").siblings("a").removeClass("on");
                num = $(".tab-01 a").index($(this));
                $(".tab-01-in").eq(num).show().siblings(".tab-01-in").hide();
            });
            
            /*var ew = $('#enableWeek_'+playlistId).change(function(event){
             if(this.checked){
             $('#sun_'+playlistId).removeAttr('disabled').attr('checked',true);
             $('#mon_'+playlistId).removeAttr('disabled').attr('checked',true);
             $('#tue_'+playlistId).removeAttr('disabled').attr('checked',true);
             $('#wed_'+playlistId).removeAttr('disabled').attr('checked',true);
             $('#thu_'+playlistId).removeAttr('disabled').attr('checked',true);
             $('#fri_'+playlistId).removeAttr('disabled').attr('checked',true);
             $('#sat_'+playlistId).removeAttr('disabled').attr('checked',true);
             }else{
             $('#sun_'+playlistId).removeAttr('checked').attr('disabled','disabled');
             $('#mon_'+playlistId).removeAttr('checked').attr('disabled','disabled');
             $('#tue_'+playlistId).removeAttr('checked').attr('disabled','disabled');
             $('#wed_'+playlistId).removeAttr('checked').attr('disabled','disabled');
             $('#thu_'+playlistId).removeAttr('checked').attr('disabled','disabled');
             $('#fri_'+playlistId).removeAttr('checked').attr('disabled','disabled');
             $('#sat_'+playlistId).removeAttr('checked').attr('disabled','disabled');
             }
             });*/
            /*if(ew.attr('checked')){
             $('#sun_'+playlistId).removeAttr('disabled');
             $('#mon_'+playlistId).removeAttr('disabled');
             $('#tue_'+playlistId).removeAttr('disabled');
             $('#wed_'+playlistId).removeAttr('disabled');
             $('#thu_'+playlistId).removeAttr('disabled');
             $('#fri_'+playlistId).removeAttr('disabled');
             $('#sat_'+playlistId).removeAttr('disabled');
             }*/
            schedule.form.initPlaylistTimeFlag(playlistId);
            
            var sd = $('#startDate_' + playlistId);
            var ed = $('#endDate_' + playlistId);
            sd.datepicker({
                dateFormat: 'yy-mm-dd'
            }).removeClass('gray');
            
            ed.datepicker({
                dateFormat: 'yy-mm-dd'
            }).removeClass('gray');
        },
        initPlaylistTimeFlag: function(playlistId){
            /*var tf = $('#enableTime_'+playlistId).change(function(event){
             if(this.checked){
             $('#startTime_'+playlistId).attr('readonly',false).removeClass('gray');
             $('#endTime_'+playlistId).attr('readonly',false).removeClass('gray');
             }else{
             $('#startTime_'+playlistId).attr('readonly',true).addClass('gray').val('');
             $('#endTime_'+playlistId).attr('readonly',true).addClass('gray').val('');
             }
             });
             
             if (tf.attr('checked')) {
             $('#startTime_'+playlistId).attr('readonly',false).removeClass('gray');
             $('#endTime_'+playlistId).attr('readonly',false).removeClass('gray');
             }*/
        },
        restScrollPosition: function(){
            var d = $(document);
            if (d.scrollTop() > 20) {
                d.scrollTop(0);
            }
        },
        savePlaylistFlag: function(playlistId, dateMsg, timeMsg, weekMsg){
        
            //保存播放列表标志位
            var startDate = $('#startDate_' + playlistId).val();
            var endDate = $('#endDate_' + playlistId).val();
            
            if (startDate.length == 0 || endDate.length == 0) {
                alert(dateMsg);
                return;
            }
            
            var timeFlag = $('#enableTime_' + playlistId).attr('checked') ? 1 : 0;
            var startTime = $('#startTime_' + playlistId).val();
            var endTime = $('#endTime_' + playlistId).val();
            
            if (timeFlag == 1) {
                if (startTime.length == 0 || endTime.length == 0) {
                    alert(timeMsg);
                    return;
                }
            }
            
            var weekFlag = $('#enableWeek_' + playlistId).attr('checked') ? 1 : 0;
            var sun = $('#sun_' + playlistId).attr('checked') ? 1 : 0;
            var mon = $('#mon_' + playlistId).attr('checked') ? 1 : 0;
            var tue = $('#tue_' + playlistId).attr('checked') ? 1 : 0;
            var wed = $('#wed_' + playlistId).attr('checked') ? 1 : 0;
            var thu = $('#thu_' + playlistId).attr('checked') ? 1 : 0;
            var fri = $('#fri_' + playlistId).attr('checked') ? 1 : 0;
            var sat = $('#sat_' + playlistId).attr('checked') ? 1 : 0;
            
            if (weekFlag == 1) {
                if (sun == 0 && mon == 0 && tue == 0 && wed == 0 && thu == 0 && fri == 0 && sat == 0) {
                    alert(weekMsg);
                    return;
                }
            }
            var scheduleId = $('#id').val();
            $.post('/schedule/save_schedule_playlist', {
                playlist_id: playlistId,
                schedule_id: scheduleId,
                start_date: startDate,
                end_date: endDate,
                time_flag: timeFlag,
                start_time: startTime,
                end_time: endTime,
                week_flag: weekFlag,
                sun: sun,
                mon: mon,
                tue: tue,
                thu: thu,
                wed: wed,
                fri: fri,
                sat: sat
            }, function(result){
                if (result.code == 1) {
                    alert(result.msg);
                }
                else {
                    $('#pl_' + playlistId).toggle();
                    var img = $('#img_' + playlistId);
                    if (img.attr('src').indexOf('16-06') > 0) {
                        img.attr('src', '/images/icons/16-05.gif');
                        img.attr('title', img.attr('tc'));
                    }
                    else {
                        img.attr('src', '/images/icons/16-06.gif');
                        img.attr('title', img.attr('te'));
                    }
                }
            }, 'json');
            
        },
        refreshInnerPlaylist: function(curpage){
            var id = $('#id').val();
            $.get('/schedule/refresh_inner_playlist?id=' + id + '&t=' + new Date().getTime(), function(data){
                $('#innerPlaylist').html(data);
				schedule.form.bindPlaylistMove();
            });
        },
        refreshInnerInteraction: function(curpage){
            var id = $('#id').val();
            $.get('/schedule/refresh_inner_interaction?id=' + id + '&t=' + new Date().getTime(), function(data){
                $('#innerInteraction').html(data);
				schedule.form.bindPlaylistMove();
            });
        },
        filterPlaylist: function(sch_type){
            schedule.form.playlistPage(1, sch_type);
        },
        playlistPage: function(curpage, sch_type){
            if (curpage == undefined) {
                curpage = 1;
            }
            
            var filterType = $('#filterType').val();
            var filter = $('#filter').val();
            var author = $('#author').val();
            
            $.post('/schedule/add_playlist_filter/' + curpage, {
                filter_type: filterType,
                filter: filter,
                author: author,
                sch_type: sch_type
            }, function(data){
                $('#playlistTable').html(data);
            });
        },
        changeFilterType: function(obj){
            var filter = $('#filter');
            filter.val('');
            if (obj.value == 'date') {
            
                filter.addClass('date-input');
                filter.attr('readonly', true);
                filter.datepicker({
                    dateFormat: 'yy-mm-dd'
                });
            }
            else {
                filter.removeClass('date-input');
                filter.attr('readonly', false);
                filter.datepicker('destory');
            }
        },
        addPlaylist: function(emptyMsg){
            var id = $('#id').val();
            var pids = new Array();
            
            $('input:checkbox[name="pid"]:checked').each(function(){
                pids.push(this.value);
            });
            if (pids.length == 0) {
                alert(emptyMsg);
                return;
            }
            
            $.post('/schedule/save_playlist', {
                id: id,
                pids: pids
            }, function(data){
                if (data.code == 0) {
                    showFormMsg(data.msg, 'success');
                    schedule.form.refreshInnerPlaylist();
                    setTimeout(tb_remove, 1000);
                }
                else {
                    showFormMsg(data.msg, 'error');
                }
            }, 'json');
        },
        bindPlaylistMove: function(){
            var up = $('img.up');
            var down = $('img.down');
            up.unbind('click').bind('click', function(){
                var img = $(this);
                var cid = img.attr('cid');
                var pos = parseInt(img.attr('pos'));
                schedule.form.changePlaylistOrder(cid, pos - 1);
            });
            down.unbind('click').bind('click', function(){
                var img = $(this);
                var cid = img.attr('cid');
                var pos = parseInt(img.attr('pos'));
                schedule.form.changePlaylistOrder(cid, pos + 1);
            });
        },
        changePlaylistOrder: function(cid, index){
            //first id  and second id
            var id = $('#id').val();
            $.post('/schedule/do_move_to', {
                id: id,
                cid: cid,
                index: index
            }, function(data){
                if (data.code == 0) {
                    schedule.form.refreshInnerPlaylist();
                }
                else {
                    showMsg(data.msg, 'error');
                }
            }, 'json');
        },
        removePlaylist: function(id, cfmMsg){
            if (confirm(cfmMsg)) {
                $.post('/schedule/remove_playlist', {
                    id: id
                }, function(data){
                    if (data.code == 0) {
                        showMsg(data.msg, 'success');
                        schedule.form.refreshInnerPlaylist();
                        setTimeout(function(){
                            hideMsg();
                        }, 1000)
                    }
                    else {
                        showMsg(data.msg, 'error');
                    }
                }, 'json');
            }
        },
        removeInteraction: function(id, cfmMsg){
            if (confirm(cfmMsg)) {
                $.post('/schedule/remove_interaction', {
                    id: id
                }, function(data){
                    if (data.code == 0) {
                        showMsg(data.msg, 'success');
                        schedule.form.refreshInnerInteraction();
                        setTimeout(function(){
                            hideMsg();
                        }, 1000)
                    }
                    else {
                        showMsg(data.msg, 'error');
                    }
                }, 'json');
            }
        },
        hideOpIcon: function(id){
            $('#' + id + ' a').fadeOut();
        },
        _check: function(){
            var id = $('#id').val();
            if (id == undefined) {
                id = 0;
            }
            var name = $('#name').val();
            var descr = $('#descr').val();
            var allDayFlag = $('#alldayFlag').val();
            var sch_type = $('#sch_type').val();
            //check parameter...
            var data = {
                id: id,
                name: name,
                descr: descr,
                allDayFlag: allDayFlag,
                sch_type: sch_type,
                publish: 0//未发布状态
            }
            
            if (id > 0) {
                data.start_date = $('#startDate').val();
                data.end_date = $('#endDate').val();
                data.start_time = $('#startTime').val();
                data.end_time = $('#endTime').val();
                
                var week = 0;
                if ($('#sun').attr('checked')) {
                    week |= 0x01;
                }
                if ($('#mon').attr('checked')) {
                    week |= 0x02;
                }
                if ($('#tue').attr('checked')) {
                    week |= 0x04;
                }
                if ($('#wed').attr('checked')) {
                    week |= 0x08;
                }
                if ($('#thu').attr('checked')) {
                    week |= 0x10;
                }
                if ($('#fri').attr('checked')) {
                    week |= 0x20;
                }
                if ($('#sat').attr('checked')) {
                    week |= 0x40;
                }
                data.week = week;
                data.action = $('#action').attr('checked') ? 1 : 0;
            }
            return data;
            
        },
        save: function(){
            var result = this._check();
            if (result) {
                this.restScrollPosition();
                $.post('/schedule/do_save', result, function(data){
                    if (data.code == 0) {
                        $('#id').val(data.schedule.id);
                        showMsg(data.msg, 'success');
                        setTimeout(function(){
                            hideMsg();
                        }, 1000)
                    }
                    else {
                        //$('#errorMsg').html(data.msg);
                        showMsg(data.msg, 'error');
                    }
                }, 'json');
            }
        },
        publish: function(){
            var result = this._check();
            if (result) {
                this.restScrollPosition();
                $.post('/schedule/do_check_group', result, function(cr){
				
				
                    result.publish = 1;
                    if(cr.code == 2) {
                    	alert(cr.msg);
                    	setTimeout(function(){
	                    	window.location.href = '/schedule/index';
	                    }, 1000);
                    }else {
                    	if (cr.code == 0) {
	                        $.post('/schedule/do_save', result, function(data){
	                            if (data.code == 0) {
	                                $('#id').val(data.schedule.id);
	                                showMsg(data.msg, 'success');
	                                setTimeout(function(){
	                                    window.location.href = '/schedule/index';
	                                }, 1000);
	                            }
	                            else {
	                                //$('#errorMsg').html(data.msg);
									showMsg(data.msg, 'error');
	                            }
	                        }, 'json');
	                    }else {
	                        if (confirm(cr.msg)) {
	                            result.overwrite = 1;
	                            $.post('/schedule/do_save', result, function(data){
	                                if (data.code == 0) {
	                                    $('#id').val(data.schedule.id);
	                                    showMsg(data.msg, 'success');
										tb_remove();
	                                    setTimeout(function(){
	                                        window.location.href = '/schedule/index';
	                                    }, 1000);
	                                }
	                                else {
	                                    //$('#errorMsg').html(data.msg);
										showMsg(data.msg, 'error');
	                                }
	                            }, 'json');
	                        }
	                    }
                    }
                    
                }, 'json');
                
            }
        },
        cancel: function(){
            window.location.href = '/schedule/index';
        },
		addInteractionPlaylist: function(emptyMsg){
            var id = $('#id').val();
            var pids = new Array();
            
            $('input:radio[name="pid"]:checked').each(function(){
                pids.push(this.value);
            });
            
            if (pids.length == 0) {
                alert(emptyMsg);
                return;
            }
            
            $.post('/schedule/save_interaction', {
                id: id,
                pids: pids,
            }, function(data){
                if (data.code == 0) {
                    showFormMsg(data.msg, 'success');
                    schedule.form.refreshInnerInteraction();
                    setTimeout(tb_remove, 1000);
                }
                else {
                    showFormMsg(data.msg, 'error');
                }
            }, 'json');
        }
    }
}
