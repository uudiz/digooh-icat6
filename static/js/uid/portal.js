
$(function(){

	$(".widget .item-col").sortable({ 
			connectWith: [".widget .item-col"],
			handle: ".caption",
			cursor:"move",
			revert: true ,
			placeholder:'placeholder',
	
			over:function(){
				$(".placeholder").height($(".ui-sortable-helper").height()-2);
			},
			stop:function(){
	
				$('.widget .item-col .caption .max').click(model.max);
				$('.widget .item-col .caption .min').click(model.min);	 
				$('.widget .item-col .caption .close').click(model.close);	 
				
				//alert(getlocal());
		
			}
	}); 
	$(".widget .item-col").disableSelection();
  	$('.widget .item-col .caption .max').click(model.max);
	$('.widget .item-col .caption .min').click(model.min);
	$('.widget .item-col .caption .close').click(model.close);	 

	
	$(".addWidget").click(function(){$(this).next().toggle()});
	 
})  

function getlocal(){
	var local="0";

	$(".widget .col-1 .item").each(function(i){
		local+=","+$(this).attr("id")	;	
	})
	local+=";1";

	$(".widget .col-2 .item").each(function(i){
		local+=","+$(this).attr("id");	
		
	})
	return local;
}
var model={

	max:function(){
		
			$("select").hide(); 
			
			var box = $("<div id='maskbox' style='z-index:1000;position:absolute;top:0;left:0;width:100%;height:"+$(document).height()+"px;background:#efefef'></div>");
			var obj=$(this).parents(".caption").parent();
			var clone=$(obj).clone();
			$(obj).children(".content").html("");
			$(clone).css({margin:" 10px",width:"auto"});
			
			$(clone).children(".content").height($(window).height()-60).show();

			$(clone).find(".max,.close").remove();
			$(clone).find("select").show();
			//$(clone).find(".max").attr("src","/images/reset.gif");
			$(clone).find(".min").click(function(){
								$(obj).children(".content").html($(clone).children(".content").html());
								$("#maskbox").remove();
								$("select").show();
								
								//$(box).parent().css("height",$(window).height());
								//$(box).parent().css("overflow","auto");
												
							
							})
			$(clone).appendTo(box);
			$(box).appendTo("body");
			//$(box).parent().css("height",$(window).height());
			//$(box).parent().css("overflow","hidden");
			
			

	},
	min:function(){
			$(this).parents(".caption").next().toggle(200);
	},
	close:function(){
		$(this).parents(".item").hide();
	}
}
