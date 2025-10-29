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
			
		</style>
	</head>
	<body>
		<div id="container" style="position:relative;">
				<div style="margin-top:30px;">
					<p style="font-family:Arial, Helvetica, sans-serif; font-size:22px;text-align:center;"><?php echo lang('forget_pwd_info3')?></p>
					<HR style="FILTER: progid:DXImageTransform.Microsoft.Glow(color=#987cb9,strength=10)" width="90%" color=#CCC SIZE=1></HR>
				</div>
				<div style="margin-top:30px;margin-left:200px;">
					<img src="/images/success.png" align="absbottom">
					<span style="text-align:center;font-family:Arial, Helvetica, sans-serif; font-size:16px;"><b><?php echo lang('your_email')?></b>&nbsp;<?php echo $email_addr?></span>
				</div>
				<div class="foot-text" style="position:absolute;bottom:0px; margin-left:260px;">
				<p style="text-align:center;"><font face="’Times New Roman’, Times, serif">Copyright 2012 &copy; All rights reserved</font></p>
				</div>
		</div>
	</body>
</html>

