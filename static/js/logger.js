var log = {
	init : function(){
		$('#filterStartDate').datepicker({
		    changeMonth: true,
		    changeYear: true,
			dateFormat : 'yy-mm-dd'
		});
		$('#filterEndDate').datepicker({
		    changeMonth: true,
		    changeYear: true,
			dateFormat : 'yy-mm-dd'
		});
	},
	page : function(curpage, orderItem, order){
		curpage = curpage||1;
		orderItem = orderItem || "id";
		order     = order || "desc";
		
		var startDate = $('#startDate').val();
		var endDate = $('#endDate').val();
		var cid = $('#cid').val();
		showLoading();
		$.get("/logger/refresh/"+curpage+"/"+orderItem+"/"+order+"?cid="+cid+"&start_date="+startDate+"&end_date="+endDate, 
			function(data){
				hideLoading();
				$('#logContent').html(data);			
		});
	},
	filter : function(){
		$('#startDate').val($('#filterStartDate').val());
		$('#endDate').val($('#filterEndDate').val());
		$('#cid').val($('#filterCompany').val());
		this.page();
	}
}
