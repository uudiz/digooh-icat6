uid.favorite= function(){
	var config={
		target:".favorite"	
	}
	function init(){
		event();
	}
	
	function resize(t){
	}

	function event(){
	
		var obj=$(config.target);
		$(obj).find("ul").sortable({
			connectWith: [".favorite ul"] ,
			cursor: "move",
			revert: true ,
			stop:function(){
				//$.each($(this).find("li"),function(i,n){alert(i+"          "+$(n).text())})
			}
		}).disableSelection();
		
		$(obj).find("a").live("click",function(){
			uid.tabs.open(this,'tabs1');
			return false;
		})
		$(obj).find(".delete").live("click",function(){
			$(this).parent().remove();
			$(".msg-text span").html("delete ok")
			if($(".msg-text").css("display")=="block"){
				$(".msg-text").stop();
			}
			$(".msg-text").animate({top:10},400).animate({top:-20},4000)

		})
		$(obj).find("li").live("mouseover",function(){
			$(this).find(".delete").show();
		})
		$(obj).find("li").live("mouseout",function(){
			$(this).find(".delete").hide();
		})
		
	}

	return {
		init:init,
		resize:resize	
	}
}()
