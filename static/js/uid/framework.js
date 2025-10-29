uid.framework=function(){
	var config={
		name:"主框架",
		content:".r3 .c1,.r3 .c2"
	}
	function resize(){
			$(".r3").height($("body").height()-$(".r1").height()-$(".r4").height()-$(".r2").height()-2);
			if($("#navBtn").size()!=0){$(".r3 .c1").height($(".r3").height()*0.8)};
			resizeChildren($(config.content).children());
		}

	function init(){
	
		event();
		resize();
		
	}
	var view={
		topShow:function(){
		
			$(".r1").height(70);
			
		},
		topHidden:function(){
			$(".r1").height(0);
			
		},
		leftShow:function(){
			$(".r3 .c1").show();
			
		},
		leftHidden:function(){
			$(".r3 .c1").hide();
		}
			
	}
	function event(){
		
		$(".tabs-nav ul li").live("mouseover",function(){$(this).disableSelection()});
		$(".tabs-nav ul li").live("dblclick",function(){
			if($(".r1").height()!=0){
				view.topHidden();	
				view.leftHidden();
				$(".togglePrev").addClass("togglePrevEnd");
				resize();
				
			}
			else{
				view.topShow();	
				view.leftShow();
				$(".togglePrev").removeClass("togglePrevEnd");
				resize();
			}
		})
		
		$(".togglePrev").hover(function(){$(this).addClass("togglePrevOver")},function(){$(this).removeClass("togglePrevOver")})
		$(".togglePrev").click(function(){
			$(this).toggleClass("togglePrevEnd");
			var nav=$(this).prev();
			if($(nav).css("display")=="block"){
				uid.accordion.resize($(nav).children());
				$(nav).hide();
			}else{
				$(nav).show();
				uid.accordion.resize($(nav).children());
			}
	
		})
	}
	return {
		resize:resize,
		init:init,
		name:config.name	}
}()


