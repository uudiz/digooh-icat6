<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="utf-8" lang="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<head>
		<title>Report player status</title>
		<style>
		body{
			margin: 0px 0px;
			padding: 0px 0px;
		}
		</style>
	</head>
	<body>
		<p><strong>Dear <?php echo $company->name; ?></strong></p>
		<p>
			Player <strong><?php echo $player->name;?>(<?php echo format_sn($player->sn);?>)</strong> login again.
		</p>
		<p>
			 iCAT WebManager<br/>
			 <?php echo date('Y-m-d H:i:s'); ?>
		</p>
	</body>
</html>