var usage = {
	initCalendar : function(){
		$('#startDate').datepicker({
		    changeMonth: true,
		    changeYear: true,
			dateFormat : 'yy-mm-dd'
		});
		$('#endDate').datepicker({
		    changeMonth: true,
		    changeYear: true,
			dateFormat : 'yy-mm-dd'
		});
	},
	query : function(curpage, order_item, order){

		
		var filter =  $('#filter').val();
		filterCri = $('#criterionId').val();
	

		req = '';
		if (filter.length > 0) {
			req += '&filter=' + filter;
		}

		if (parseInt(filterCri) > 0) {
			req += '&filterCri=' + filterCri;
		}
		
		if($('#checkDate').is(':checked')){
			var startDate = $('#startDate').val();
			var endDate = $('#endDate').val();

			if(endDate<startDate){
				return;
			}
			req+="&hasdate=1&start_date="+startDate+'&end_date='+endDate;
		}else{

			req+="&hasdate=0";
		}

		order_item = order_item || 'name';
		order = order || 'asc';
		if(curpage == undefined){
			curpage = 1;
		}
		showLoading();


		$.get('/usage/query/' + curpage +'/'+order_item+'/'+order+ '?t=' + new Date().getTime()+req, function(data){
			hideLoading();
			$('#usageContent').html(data);
		});
	},

	exportusages: function(){

		
		var filter =  $('#filter').val();
		filterCri = $('#criterionId').val();
	

		req = '';

		if($('#checkDate').is(':checked')){
			var startDate = $('#startDate').val();
			var endDate = $('#endDate').val();

			if(endDate<startDate){
				return;
			}
			req="?hasdate=1&start_date="+startDate+'&end_date='+endDate;
		}else{
			req="?hasdate=0";
		}

		if (filter.length > 0) {
			req += '&filter=' + filter;
		}

		if (parseInt(filterCri) > 0) {
			req += '&filterCri=' + filterCri;
		}


		//window.location.href = '/usage/export_usages/' + req;
		
		var url = '/usage/export_usages/' + req;
		var xhr=null;
		try {
			xhr=new XMLHttpRequest()
		}catch(e) {
			xhr=new ActiveXObject("Microsoft.XMLHTTP")
		}

		xhr.open('get', url, true);
		xhr.responseType = "blob"; // 返回类型blob

		xhr.onload = function () {
			// 请求完成
			if (this.status === 200) {//返回200
				
	           var response = this.response;
                var URL = window.URL || window.webkitURL || window;
                    var link = document.createElement('a');
                    link.href = URL.createObjectURL(response);
                    link.download = this.getResponseHeader('File-Name');
                    var event = document.createEvent('MouseEvents');
                    event.initMouseEvent('click', true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
                    link.dispatchEvent(event);
			}};
			xhr.send();
		

	},

	checkboxOnclick:function (checkbox){

		if ( checkbox.checked == true){
			  $('#startDate').attr("disabled", false);
			  $('#endDate').attr("disabled", false);
		}else{
			$('#startDate').attr("disabled", true);
			$('#endDate').attr("disabled", true);
		}

	}
}

$(function(){
	document.onkeyup = function(event){
		if (event.key == 13) {
			usage.query(1);
		}
	}
})
