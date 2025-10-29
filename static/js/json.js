$(document).ready(function(){
	$("#my").click(function(event){
		//防止执行默认操作
		event.preventDefault();
		var jqxhr = $.get("/json",function(data) {
			alert(data);
			var json = toJsonObj(data);
			alert(json.name +"full:" + json.full_name.first);
		});
		jqxhr.error(function(){
			alert("error...");
		});
	});
});