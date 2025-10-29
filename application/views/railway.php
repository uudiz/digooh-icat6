<script type="text/javascript">
	$(document).ready(function(){
		check();
	});
	
	function check(){
		$.get("https://dynamic.12306.cn/otsweb/loginAction.do?method=initForMy12306",function(data){
			//alert('data');
			setTimeout(check, 30000);
		}).success(function(){
			alert('success');
		})
		.error(function(){
			//alert('check...');
			setTimeout(check, 30000);
		});
	}
</script>