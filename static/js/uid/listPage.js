uid.listPage = function(){
	var config,init,resize,event;
	config={
		target:".listpage"
	}
	init=function(){
		event();
		resize();
	}
	resize=function(){
		$.each($(".r2"),function(i){
				var obj=$(this);
				var prev=$(obj).prev();
				var next=$(obj).next();
				var parent=$("body");//$(obj).parent();
				$(obj).height($(obj).parent().height()-$(prev).height()-$(next).height());
	
		})
	}
	event=function(){
		$(".tip").each(function(){
			$(this).simpletip({fixed: true,content:$(this).next().text()})
		})
		
	}
	return {
		init:init,
		resize:resize
	}
}()