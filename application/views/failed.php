<html>
	<head>
		<style>
			body{
			margin:0;padding:0;
			background:#fbfbfb;
			}
			#container{
				width:760px;
				height:100%;
				border:1px #dddddd solid;
				margin:auto;
			}
			.blue {  
				color: #d9eef7;  
				border: solid 1px #0076a3;  
				background: #0095cd;  
				background: -webkit-gradient(linear, left top, left bottom, from(#00adee), to(#0078a5));  
				background: -moz-linear-gradient(top,  #00adee,  #0078a5);  
				filter:  progid:DXImageTransform.Microsoft.gradient(startColorstr='#00adee', endColorstr='#0078a5');  
				font: 14px/100% Arial, Helvetica, sans-serif;
				padding: .5em 2em .55em;
				margin:.6em 0 0 26.5em;
    
			}  
			.blue:hover {  
				background: #007ead;  
				background: -webkit-gradient(linear, left top, left bottom, from(#0095cc), to(#00678e));  
				background: -moz-linear-gradient(top,  #0095cc,  #00678e);  
				filter:  progid:DXImageTransform.Microsoft.gradient(startColorstr='#0095cc', endColorstr='#00678e');  
			}  
			.blue:active {  
				color: #80bed6;  
				background: -webkit-gradient(linear, left top, left bottom, from(#0078a5), to(#00adee));  
				background: -moz-linear-gradient(top,  #0078a5,  #00adee);  
				filter:  progid:DXImageTransform.Microsoft.gradient(startColorstr='#0078a5', endColorstr='#00adee');  
			}  
			
		</style>
		<script src="/static/js/jquery/jquery-1.7.1.js"></script>
		<script>
			function doValidation(){
				var name=$('#name').val();
				if(name==""){
					$('.error').show();
					return false;
				}
			}
			function upperCase(){
				$('.error').hide();
			}
		</script>
	</head>
	<body>
		<div id="container" style="position:relative;">
			<form action="/login/get_pwd" method="POST" onsubmit="return doValidation();"> 

				<div style="margin-top:30px;">
					<img src="/images/warning.png" align="absbottom" style="float:left;">
					<p style=" font-family:Arial, Helvetica, sans-serif; font-size:22px;text-align:center;"><?php echo $msg?></p>
				</div>
				<HR style="FILTER: progid:DXImageTransform.Microsoft.Glow(color=#987cb9,strength=10)" width="90%" color=#CCC SIZE=1></HR>
				<div style="margin-top:30px;margin-left:80px;">
					<?php echo lang('input_name')?>
					<input type="text" id="name" name="name" style="width:262px;height:32px;" onfocus="upperCase()"><span class="error" style="display:none; color:#f00">&nbsp;<?php echo lang('login_code_4')?></span>
				</div>
					<p><input class="blue" type="submit" value="next" /></p>
			</form>
			<div class="foot-text" style="position:absolute;bottom:0px; margin-left:260px;">
				<p style="text-align:center;"><font face="’Times New Roman’, Times, serif">Copyright 2012 &copy; All rights reserved</font></p>
			</div>
		</div>
	</body>
</html>