var playback = {
	initCalendar : function(){
		$('#postStartDate').datepicker({
		    changeMonth: true,
		    changeYear: true,
			dateFormat : 'yy-mm-dd'
		});
		$('#postEndDate').datepicker({
		    changeMonth: true,
		    changeYear: true,
			dateFormat : 'yy-mm-dd'
		});
	},
	_createFilterStr:function(){
		var startDate = $('#postStartDate').val();
		var endDate = $('#postEndDate').val();
		
		var req ='?start_date='+startDate+'&end_date='+endDate;
		var option=$("#campaign option:selected");
		if(option.val()>0){
			 req += '&campaign=' + option.text();
		}
		var option=$("#media option:selected");
		if(option.val()>0){
			 req += '&media=' + option.text();
		}
		var option=$("#player option:selected");
		if(option.val()>0){
			 req += '&player=' + option.val();
		}
		return req;
	},

	query : function(curpage, order_item, order){
		order_item = order_item || 'post_date';
		order = order || 'asc';
		if(curpage == undefined){
			curpage = 1;
		}
		showLoading();
		var req = this._createFilterStr();
		$.get('/playback/query/' + curpage +'/'+order_item+'/'+order+req, function(data){
			hideLoading();
			$('#playbackContent').html(data);
		});
	},
	export:function(){
		var req = this._createFilterStr();
		window.location.href = '/playback/excel'+ req;
	}
}

$(function(){
	document.onkeyup = function(event){
		if (event.keyCode == 13) {
			playback.query(1);
		}
	}

})
