/**
 * @author User
 */

/**
 * 用客户机单处理
 */
var p = {
	formId : 'player-form',
	doSave : function(){
		$.post('/player/do_save',{
				name : $('#name').val(),
				descr: $('#descr').val()
			},
			function(data){
				json = toJsonObj(data);
				if(json != null){
					if(json.code != 0){
						$('.validateTips').html(json.msg);
					}else{
						g.destoryFormDialog();
						gl.addItem(json);
					}
				}else{
					alert('System error....');
				}
			});
	}
	,
	init : function(){
		//this.form = $('#user-form');	
	},
	destory : function(){
			uf = $('#' + this.formId);
			uf.parent().remove();
			uf.remove();
		
	},
	destoryFormDialog : function(){
		$('#' + this.formId).dialog('destory');
		this.destory();
	}
	
};
/**
 * 客户机列表管理
 */
var pl = {
	cid : 0,
	totalLine:0,//客户机记录数
	init : function(){
		if (this.totalLine > 0) {
			this.initSorttable();
		}
		
		$("#create")
			.button()
			.click(function() {
				$.get('/player/add',function(data){
					$('.new-area').append(data);
					$("#" + p.formId).dialog({
											autoOpen : true,
											modal: true,
											width: 500,
											buttons:[{
												 text: "Ok",
        										 click: function() { 
												 	g.doSave(); 
													return false;
												 }
											},{
												 text: "Cancel",
        										 click: function() { 
												 	p.destoryFormDialog();
													return false;
												 }
											}
											],
											close : function(event, ui) {
												event.preventDefault();
												p.destory();
											}
										});
				});
				
			});
	},
	initSorttable : function(){
		$("#sorttable").tablesorter({
				debug: true,
				sortList: [[0, 1]],
				widgets: ['zebra'],
				headers: {			//0: {sorter: false}
				}
			}).tablesorterPager({
				container: $("#sorttable"),
				positionFixed: false
			});
	},
	addItem : function(item){
		if (this.totalLine == 0) {
			$( "#sorttable tbody >  tr:first" ).before( "<tr>" +
							"<td>" + item.id + "</td>" + 
							"<td>" + item.name + "</td>" +
							"<td>" + item.group_name + "</td>" +  
							"<td>" + item.descr + "</td>" +
							"<td>" + item.status + "</td>" +
							"<td>" + item.add_time + "</td>" +
							"</tr>" );
							
			this.initSorttable();
			this.totalLine++;
		}
		else {
			$("#sorttable").trigger("addItem", "<tr>" +
			"<td>" +
			item.id +
			"</td>" +
			"<td>" +
			item.name +
			"</td>" +
			"<td>" +
			item.group_name +
			"</td>" +
			"<td>" +
			item.descr +
			"</td>" +
			"<td>" +
			item.status +
			"</td>" +
			"<td>" +
			item.add_time +
			"</td>" +
			"</tr>");
		}
	}
}


