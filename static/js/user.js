/**
 * @author User
 */
/**
 * 用户表单处理
 */
var u = {
	formId: 'user-form',
	doSave: function () {
		var auth = $('input:radio[name="auth"]:checked').val();
		var id = $('#id').val();
		var password = $('#password').val();
		var email = $('#email').val();
		var data_entry_text = $('#data_entry_text').val();
		// var groups = new Array();
		var folders = new Array();
		var criterias = new Array();
		var campaigns = new Array();
		var players = new Array();
		var use_player = 1;
		if (id == undefined) {
			id = 0;
		}
		if (data_entry_text == undefined) {
			data_entry_text = 0;
		}
		if (password == undefined) {
			password = '';
		}

		var can_publish = $('input:radio[name="publish"]:checked').val();


		var nAuth = parseInt(auth);

		if (nAuth == 0) {
			/*
			$('#assignFolderLine input[type="checkbox"]').each(function(){
				if (this.checked) {
					folders.push(this.value);
				}
			});
			$('#assignCriteriaLine input[type="checkbox"]').each(function(){
				if (this.checked) {
					criterias.push(this.value);
				}
			});
			*/
			//folders = $('#folder-select-options').val();


			criterias = $('#criteria-select-options').val();

			players = $('#player-select-options').val();

			use_player = $("input[name='useplayer']:checked").val();
			campaigns = $('#campaign-select-options').val();

		}
		else if (nAuth == 1) {
			//folders = $('#folder-select-options').val();
			campaigns = $('#campaign-select-options').val();
		} else if (nAuth == 2) {
			//folders = $('#folder-select-options').val();
			criterias = $('#criteria-select-options').val();

			players = $('#player-select-options').val();

			use_player = $("input[name='useplayer']:checked").val();
		}

		if (nAuth == 0 || nAuth == 1 || nAuth == 2) {

			var treeObj = $.fn.zTree.getZTreeObj("treeFolder");
			if (treeObj) {
				var nodes = treeObj.getCheckedNodes(true);
				if (nodes.length) {
					folders = new Array();
				}
				for (i = 0; i < nodes.length; i++) {
					folders.push(nodes[i].id);

				}
			}
		}



		{
			$('#waitting').show();
			$.post('/user/do_save', {
				name: $('#name').val(),
				password: password,
				email: email,
				descr: $('#descr').val(),
				cid: $('#cid').val(),
				auth: auth,
				folders: folders,
				criterias: criterias,
				campaigns: campaigns,
				use_player: use_player,
				players: players,
				id: id,
				logo: $('#custom_logo').val(),
				can_publish: can_publish,
				data_entry_text: data_entry_text,
				airtime: $('#air_time_input').val()
			}, function (data) {
				$('#waitting').hide();
				if (data.code != 0) {
					showFormMsg(data.msg, 'error');
				} else {
					showFormMsg(data.msg, 'success');
					setTimeout(function () {
						//remove
						tb_remove();
						//refresh 当添加用户时从公司来的时候，则不需要刷新

						if (!data.admin || id > 0) {
							u.refresh();
						}
					}, 1000);
				}
			}, 'json');
		}

	},
	changeAuth: function (obj) {
		//     var groupLine = $('#assignGroupLine');
		var folderLine = $('#assignFolderLine');
		var criLine = $('.assignCriteriaLine');
		var camLine = $('#assignCampaignLine');
		var publishLine = $('#canPublishLine');
		var airtimeLine = $('#airtimeLine');
		//       var dateLine = $('#assignDataLine');



		if (obj.value == 0) {
			folderLine.show();
			criLine.show();
			camLine.show();
			publishLine.show();
			airtimeLine.hide();

		} else if (obj.value == 1) {
			criLine.hide();
			camLine.show();
			folderLine.show();
			publishLine.hide();
			airtimeLine.hide();
		} else if (obj.value == 2) {
			criLine.hide();
			camLine.hide();
			folderLine.show();
			publishLine.hide();
			airtimeLine.show();
		}
		else {
			camLine.hide();
			folderLine.hide();
			criLine.hide();
			publishLine.hide();
			airtimeLine.hide();

		}

	},
	init: function () {
		//this.form = $('#user-form');	
	},
	refresh: function () {
		showLoading();
		this.page(1);

	},
	filter: function () {
		this.page();
	},
	page: function (curpage, orderItem, order) {
		curpage = curpage || 1;
		orderItem = orderItem || "id";
		order = order || "desc";

		var name = $('#filter').val();
		var type = $('#filterType').val();

		$.get("/user/refresh/" + curpage + "/" + orderItem + "/" + order + "?name=" + name + "&type=" + type, function (data) {

			$('#layoutContent').html(data);
			tb_init('#layoutContent a.thickbox');//pass where to apply thickbox	
			hideLoading();
		});
	},
	remove: function (id, curpage, order_item, order, msg) {
		if (confirm(msg)) {
			var req = {
				id: id
			}
			$.post('/user/do_delete', req, function (data) {
				if (data.code == 0) {
					showMsg(data.msg, 'success');
					u.page(curpage, order_item, order);
					setTimeout(hideMsg, 1000);

				}
				else {
					showMsg(data.msg, 'error');
				}
			}, 'json');
		}
	},
	doJson: function (event) {
		//取得div层
		var $search = $('#search');
		//取得输入框JQuery对象
		//var $searchInput = $search.find('#filter');
		var $searchInput = $('#filter');
		//关闭浏览器提供给输入框的自动完成
		$searchInput.attr('autocomplete', 'off');
		//创建自动完成的下拉列表，用于显示服务器返回的数据,插入在搜索按钮的后面，等显示的时候再调整位置 
		var $autocomplete = $('<div class="autocomplete"></div>').hide().insertAfter('#submit_json');
		//清空下拉列表的内容并且隐藏下拉列表区
		var clear = function () {
			$autocomplete.empty().hide();
		};
		//注册事件，当输入框失去焦点的时候清空下拉列表并隐藏
		$searchInput.blur(function () {
			setTimeout(clear, 500);
		});

		//下拉列表中高亮的项目的索引，当显示下拉列表项的时候，移动鼠标或者键盘的上下键就会移动高亮的项目，想百度搜索那样
		var selectedItem = null;
		var timeoutid = null;
		//设置下拉项的高亮背景
		var setSelectedItem = function (item) {
			//更新索引变量
			selectedItem = item;
			//按上下键是循环显示的，小于0就置成最大的值，大于最大值就置成0
			if (selectedItem < 0) {
				selectedItem = $autocomplete.find('li').length - 1;
			} else if (selectedItem > $autocomplete.find('li').length - 1) {
				selectedItem = 0;
			}
			//首先移除其他列表项的高亮背景，然后再高亮当前索引的背景 
			$autocomplete.find('li').removeClass('highlight').eq(selectedItem).addClass('highlight');
		};
		var ajax_request = function () {
			$.ajax({
				'url': '/user/json',
				'data': { 'filter_type': $('#filterType').val(), 'filter_name': $searchInput.val() },
				'dataType': 'json',
				'type': 'POST',
				'success': function (data) {
					if (data.length) {
						$.each(data, function (index, term) {
							$('<li></li>').text(term).appendTo($autocomplete).addClass('clickable').hover(function () {
								//下拉列表每一项的事件，鼠标移进去的操作 
								$(this).siblings().removeClass('highlight');
								$(this).addClass('highlight');
								selectedItem = index;
							},
								function () {
									//下拉列表每一项的事件，鼠标离开的操作 
									$(this).removeClass('highlight');
									//当鼠标离开时索引置-1，当作标记 
									selectedItem = -1;
								}).click(function () {
									//鼠标单击下拉列表的这一项的话，就将这一项的值添加到输入框中 
									$searchInput.val(term);
									//清空并隐藏下拉列表 
									$autocomplete.empty().hide();
								});
						});//事件注册完毕 

						//显示下拉列表 
						$autocomplete.show();
					}
				}
			});
		};
		//对输入框进行事件注册 
		$searchInput.keyup(function (event) {
			//字母数字，退格，空格 
			if (event.keyCode > 40 || event.keyCode == 8 || event.keyCode == 32) {
				//首先删除下拉列表中的信息 
				$autocomplete.empty().hide();
				clearTimeout(timeoutid);
				timeoutid = setTimeout(ajax_request, 100);
			} else if (event.keyCode == 38) {
				//上       selectedItem = -1 代表鼠标离开 
				if (selectedItem == -1) {
					setSelectedItem($autocomplete.find('li').length - 1);
				} else {
					//索引减1 
					setSelectedItem(selectedItem - 1);
				}
				event.preventDefault();
			} else if (event.keyCode == 40) {
				//下      selectedItem = -1 代表鼠标离开 
				if (selectedItem == -1) {
					setSelectedItem(0);
				} else {
					//索引加1 
					setSelectedItem(selectedItem + 1);
				}
				event.preventDefault();
			}
		}).keypress(function (event) {
			//enter键 
			if (event.keyCode == 13) {
				//列表为空或者鼠标离开导致当前没有索引值 
				if ($autocomplete.find('li').length == 0 || selectedItem == -1) {
					return;
				}
				$searchInput.val($autocomplete.find('li').eq(selectedItem).text());
				$autocomplete.empty().hide();
				event.preventDefault();
			}
		}).keydown(function (event) {
			//esc键 
			if (event.keyCode == 27) {
				$autocomplete.empty().hide();
				event.preventDefault();
			}
		});

		document.onkeyup = function (event) {
			u.filter();
		}
	},
	resetLogo: function () {
		$(".logo").css("background-image", "none")
		$("#custom_logo").val(null);
	},

};
