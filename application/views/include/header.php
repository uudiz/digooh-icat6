<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="utf-8" lang="utf-8">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<base href="<?php echo base_url()?>"/>
	<link type="text/css" href="/static/css/style.css" rel="stylesheet" ></link>
	<?php
		//加载css
		if(isset($cssList)){
			foreach($cssList as $css){
				echo '<link type="text/css" href="'.$css.'" rel="stylesheet" ></link>';
			}
		}
	?>
	
	<script language="JavaScript" type="text/javascript" src="/static/js/jquery/jquery-1.5.2.min.js" ></script>
	<!--
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
	<script src="http://cdn.jquerytools.org/1.2.6/jquery.tools.min.js"></script>
	-->
	<script language="JavaScript" type="text/javascript" src="/static/js/common.js" ></script>
	<?php
		//加载js
		if(isset($jsList)){
			foreach($jsList as $js){
				echo '<script language="JavaScript" type="text/javascript" src="'.$js.'" ></script>';
			}
		}
	?>
	<title>
		<?php
		if(isset($title)){
			echo $title;
		}else{
			echo 'iCAT System';
		}
		?>
	</title>
</head>
<body>
<div id="loading" class="loading" title="<?php echo lang('loading');?>" style="display:none;">
	<div class="mask"></div>
	<div class="img">Loading...</div>
	<div class="bar"></div>
</div>