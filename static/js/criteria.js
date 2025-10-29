/**
 * @author User
 */

/**
 * �û�������
 */
var criteria = {
	formId: 'criteria-form',

	doSave: function () {
		var id = $('#id').val();
		var name = $('#name').val();
		if (id == undefined) {
			id = 0;
		}
		if (name.indexOf("&") >= 0 || name.indexOf("<") >= 0 || name.indexOf(">") >= 0 || name.indexOf("'") >= 0 || name.indexOf("\\") >= 0 || name.indexOf("%") >= 0) {
			showFormMsg("Special symbols (& < > ' \\ %) are not allowed in the criteria name.", 'danger');
			return false;
		}
		$.post('/criteria/do_save', {
			name: name,
			descr: $('#descr').val(),
			id: id,
			players: $('#players-select-options').val()
		},
			function (data) {
				if (data.code != 0) {
					showFormMsg(data.msg,"danger");
				} else {

						var alertMsg = {type:'success',message:data.msg};
						localStorage.setItem("Status",JSON.stringify(alertMsg));
						window.location = '/criteria';

				}
			}, 'json');
	},
	init: function () {
	},
	destory: function () {
		var uf = $('#' + this.formId);
		uf.parent().remove();
		uf.remove();

	},
	destoryFormDialog: function () {
		$('#' + this.formId).dialog('destory');
		this.destory();
	},
	refresh: function () {
		showLoading();
		var name = $('#filter').val();

		$.get('/criteria/refresh/?name=' + name, function (data) {
			$('#layoutContent').html(data);
			hideLoading();
			//reinit this box~
			tb_init('td > a.thickbox');
		});
	},
	page: function (curpage, orderItem, order) {
		showLoading();
		orderItem = orderItem || 'name';
		order = order || 'asc';

		var name = $('#filter').val();
		
		$.get('/criteria/refresh/' + curpage + "/" + orderItem + "/" + order + '?name=' + name, function (data) {
			$('#layoutContent').html(data);
			hideLoading();
		});
	},
	remove: function (id, msg) {
		if (confirm(msg)) {
			var req = {
				id: id
			}
			$.post('/criteria/do_delete', req, function (data) {
				if (data.code == 0) {
					showMsg(data.msg, 'success');
					criteria.refresh();
					setTimeout(hideMsg, 1000);
					if (data.needPublish == 1) {
						alertify.alert(data.repubmsg);
					}

				} else {
					showMsg(data.msg, 'error');
				}
			}, 'json');
		}
	}

};