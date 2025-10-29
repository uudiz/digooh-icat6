uid.tree = function(){
	var config={
		target:".tree",
		url:"../ndata/tree"		
	}
	function init(){
		
		event();
	}
	
	function resize(t){
		size(t);
	}

	function size(obj){
		$(obj).height($(obj).parent().height());
		$(obj).children(".root").height($(obj).parent().height()-$(obj).children(".option").height()-12);
	
	}
	function event(){
	
		var obj=$(config.target);
			$(obj).children(".root").find(".node").children("span").click(function(){
				$(this).next().toggle();
				$(this).parent().toggleClass("on");
			})
			$(obj).children(".option").children(".openAll").click(function(){
				$(this).parent().parent().find(".root .node").addClass("on");
				$(this).parent().parent().find(".root .node").children("span").next().show();
			})
			$(obj).children(".option").children(".closeAll").click(function(){
				$(this).parent().parent().find(".root .node").removeClass("on");
				$(this).parent().parent().find(".root .node").children("span").next().hide();
			})
			$(obj).children(".root").find("a").click(function(){
				uid.tabs.open(this,'tabs1');
				return false;
			})
			$(obj).children(".root").find("a").contextMenu(
							'contextMenu',
							{	shadow:true,
							
								bindings:{
									'addFavorite':function(t){
										var text=$(t).text();
										var url=$(t).attr("href");
										var id=$(t).attr("id");
										$(".favorite ul").append('<li><a href="'+url+'" >'+text+'</a><span class="delete"></span></li>');
										$(".favorite ul").sortable({
											connectWith: [".favorite ul"] ,
											cursor: "move",
											revert: true 
										}).disableSelection();

										$(".msg-text span").html("add ok")
										if($(".msg-text").css("display")=="block"){
											$(".msg-text").stop();
										}
										$(".msg-text").animate({top:10},400).animate({top:-20},4000)
									},
									'openTabs':function(t){
										$(t).click();
									}
								}
						
			});
			
	}

	return {
		init:init,
		resize:resize,
		name:config.name
	}
}()
