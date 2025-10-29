var configxml = {
	uploading: false,
	dosave: function (control) {
		var id = $('#id').val();
		var save_type = $('#save_type').val();

		var name = $('#name').val();
		var descr = $('#descr').val();

		var dateformat = $('#dateformat').val();
		var timeformat = $('#timeformat').val();
		var timezone = $('#timezone').val();
		var synctime = $('#synctime').val();
		var clockpos = $('#clockpos').val();
		var storagepri = $('#storagepri').val();
		var sn = $('#sn').val();
		var ip = $('#ip').val();
		var connectionMode = $('#connectionMode').val();
		var domain = $('#domain').val();
		var videomode = $('#hdmi').val();
		var port = $('#port').val();
		var player_type = $('#player_type').val();
		var sync_playback = $('#sync_playback').val();
		var daily_restart_time = $('#daily_restart_time_h').val() + ':' + $('#daily_restart_time_m').val();
		var drflag = 1;
		var orientation = $('#orientation').val();

		var tcpport = $('#tcpport').val();
		var playbackreport = $('#playbackreport').val();
		var network_mode = $('#networkmode').val();
		var wifissid = $('#wifissid').val();
		var wifipwd = $('#wifipwd').val();
		var menulock = $('#desktoplock').val();
		var lockkey = $('#lockkey').val();


		if (orientation == undefined) {
			orientation = 0;
		}
		$('input:checkbox[id="drflag"]').each(function () {
			if (!this.checked) {
				drflag = 2;
			}
		});
		if (port == '') {
			port = 0;
		}
		if (name == '') {
			$('#errorName').show();
			return;
		} else {
			$('#errorName').hide();
		}
		if (player_type == 1) {
			storagepri = $('#storagepri2').val();
			videomode = $('#hdmi2').val();
		}

		$.post('/configxml/do_save', {
			id: id,
			name: name,
			save_type: save_type,
			descr: descr,
			dateformat: dateformat,
			timeformat: timeformat,
			timezone: timezone,
			synctime: synctime,
			clockpos: clockpos,
			storagepri: storagepri,
			videomode: videomode,
			sn: sn,
			ip: ip,
			domain: domain,
			drflag: drflag,
			port: port,
			connectionMode: connectionMode,
			player_type: player_type,
			sync_playback: sync_playback,
			daily_restart_time: daily_restart_time,
			orientation: orientation,
			tcpport: tcpport,
			playbackreport: playbackreport,
			networkmode: network_mode,
			wifissid: wifissid,
			wifipwd: wifipwd,
			menulock: menulock,
			lockkey: lockkey,
			hotssid: $('#hotssid').val(),
			hotpwd: $('#hotpwd').val(),
			brightness: $('#brightness').val(),

		}, function (data) {
			if (data.code == 0) {
				showFormMsg(data.msg, 'success');
				setTimeout(function () {
					//remove
					tb_remove();
					/*
					if(control) {
						
					}else
					*/
					{
						configxml.refresh();
					}

				}, 1000);
			} else {
				showFormMsg(data.msg, 'error');
			}
		}, 'json');

	},
	refresh: function () {
		showLoading();
		var name = $('#filter').val();

		$.get('/configxml/refresh?name=' + name + '&t=' + new Date().getTime(), function (data) {
			$('#layoutContent').html(data);
			hideLoading();
			//reinit this box~
			tb_init('td > a.thickbox');
		});
	},
	pages: function (curpage, orderItem, order) {
		showLoading();
		var name = $('#filter').val();
		$.get('/configxml/refresh/' + curpage + '/' + orderItem + '/' + order + '?name=' + name + '&t=' + new Date().getTime(), function (data) {
			$('#layoutContent').html(data);
			hideLoading();
			//reinit this box~
			tb_init('td > a.thickbox');
		});
	},

	remove: function (id, msg) {
		if (confirm(msg)) {

			$.post('/configxml/delete', { id: id }, function (data) {
				if (data.code == 0) {
					showMsg(data.msg, 'success');
					//setTimeout(function dodelete(){location.href="/configxml/index";}, 1000);
					configxml.refresh();
				} else {
					showMsg(data.msg, 'error');
				}
			}, 'json');

		}
	},
	toggle: function (obj) {
		var $this = $(obj);
		var status = $this.attr('status');
		var id = $this.attr('id');
		var pp = $this.parent().parent();
		var img = $this.children('img');
		var ptype = $this.attr('ptype');
		if (status == 0) {
			//set working status
			$this.attr('status', 2);
			configxml.refreshOnlines(pp, id, ptype, function () {
				$this.attr('status', 1);
				img.attr('src', '/images/icons/16-06.gif');
			});

		} else if (status == 1) {
			//collapse
			var next = pp.next();
			if ('panel_' + id == next.attr('id')) {
				//next.fadeOut();
				next.remove();
			}

			$this.attr('status', 0);
			img.attr('src', '/images/icons/16-05.gif');
		}
	},
	refreshOnlines: function (pp, id, ptype, callback) {
		var next = pp.next();
		if ('panel_' + id == next.attr('id')) {
			//next.fadeOut();
			next.remove();
		}
		//set loading status
		var loading = '<tr id="detailLoading" height="80"><td><div style="left: 50%;" class="loading-01">Loading ......</div></td></tr>';
		pp.after(loading);
		//expland
		$.get('/player/configxml_onlines?id=' + id + '&ptype=' + ptype + '&t=' + new Date().getTime(), function (data) {
			//append detail
			pp.next().remove();
			var css = pp.attr('class');
			if (css == 'onSelected') {
				css = '';
			}
			var line = '<tr id="panel_' + id + '"' + ((css != undefined && css != '') ? 'class="' + css + '"' : '') + '><td colspan="6">' + data + '</td></tr>';
			pp.after(line);
			configxml.bindCheckButton(id);
			if (callback != undefined) {
				callback();
			}
		});
	},
	page: function (ptype, curpage, orderItem, order, id, pid) {
		$.get('/player/configxml_onlines/' + curpage + '/' + orderItem + '/' + order + '?t=' + new Date().getTime() + '&id=' + id + '&pid=' + pid + '&ptype=' + ptype,
			function (data) {
				var panel = $('#pan_' + pid);
				var pp = panel.parent();
				pp.next().remove();
				pp.html(data);
				configxml.bindCheckButton(id);
			}
		);
	},

	bindCheckButton: function (id) {
		var cb = $("#panel_" + id + " input:checkbox[name='checkall']");
		cb.click(function () {
			var checked = this.checked;
			$("#panel_" + id + " input:checkbox[name='checkbox']").each(function () {
				this.checked = checked;
			});
		});

		var upgrade = $("#panel_" + id + " .upgrade").click(function () {
			var bu = $(this);
			var pid = bu.attr('pid');
			$.post('/configxml/do_upgrade_config', {
				"ids": pid,
				"id": id
			}, function (data) {
				if (data.code == 0) {
					showMsg(data.msg, 'success');
					//setTimeout(hideMsg, 3000);
				} else {
					showMsg(data.msg, 'error');

					setTimeout(hideMsg, 3000);
				}
			}, 'json');
		});

		var upgradeall = $("#panel_" + id + " .upgradeall").click(function () {
			var ids = new Array();
			$("#panel_" + id + " input:checkbox[name='checkbox']:checked").each(function () {
				if (this.checked) {
					ids.push(this.value);
				}
			});
			$.post('/configxml/do_upgrade_config', {
				"ids": ids,
				"id": id
			}, function (data) {
				if (data.code == 0) {
					showMsg(data.msg, 'success');
					//setTimeout(hideMsg, 3000);
				} else {
					showMsg(data.msg, 'error');
					setTimeout(hideMsg, 3000);
				}
			}, 'json');
		});
	}
};
