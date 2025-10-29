var bandwidth = {
	initCalendar : function(){
		$('#postStartDate').datepicker({
			dateFormat : 'yy-mm-dd'
		});
		$('#postEndDate').datepicker({
			dateFormat : 'yy-mm-dd'
		});
	},
	query : function(curpage, order_item, order){
		var startDate = $('#postStartDate').val();
		var endDate = $('#postEndDate').val();
		var playerId = $('#playerId').val();
		order_item = order_item || 'id';
		order = order || 'desc';
		if(curpage == undefined){
			curpage = 1;
		}
		showLoading();
		$.get('/playbandwidth/query/' + curpage +'/'+order_item+'/'+order+'?start_date='+startDate+'&end_date='+endDate+'&player_id='+playerId, function(data){
			hideLoading();
			$('#bandwidthContent').html(data);
		});
	}
}

$(function(){
	document.onkeyup = function(event){
		if (event.keyCode == 13) {
			bandwidth.query(1);
		}
	}
})
