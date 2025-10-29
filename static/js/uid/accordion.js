uid.accordion = function(){
	var config={
		target:".accordion",
		content:".content",
		url:"../ndata/accordion"	}
	function init(){
		event();
	}
	function resize(t){
		size(t);
		resizeChildren($(t).children(config.content).children());
	}

	function event(){
	
		var obj=$(config.target);
		$(obj).children(".caption").click(function(){
			if(!$(this).hasClass("on")){
				$(this).parent().children(".content").hide();
				$(this).parent().children("h3.caption").removeClass("on");
				$(this).addClass("on");
				$(this).next().show();
				//var thisChildren=$(this).next().children().eq(0);
				//var thisChildrenType=$(this).next().children().eq(0).attr("class");
				
				var content=$(this).next();
				//alert($(content).attr("class")+"   "+$(content).children().attr("class"));
				//alert($(content).children().height()+"       "+$(content).height());
				//if($(t).next().children().height()!=$(t).next().height()){
					resizeChildren($(content).children());
				//}
				}
				return false;
			});
		
	}
	function size(obj){
		$(obj).height($(obj).parent().height()-10);
		$(obj).children(".content").height($(obj).height()-($(obj).children(".caption").height())*$(obj).children(".caption").size());
	}

	return {
		init:init,
		resize:resize,
		name:config.name
	}
}()
