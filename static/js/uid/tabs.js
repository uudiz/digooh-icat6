$(function(){
	$(".tab-01 a").click(
		function(){
			$(this).addClass("on").siblings("a").removeClass("on");
			num = $(".tab-01 a").index($(this));
			$(".tab-01-in").eq(num).show().siblings(".tab-01-in").hide();
		}
	)
	
})
