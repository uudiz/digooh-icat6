<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<base href="<?php echo base_url()?>"/>
	

	<link href="/static/css/jquery/jquery-ui.min.css" rel="stylesheet" type="text/css" />
	<link type="text/css" rel="stylesheet" href="/static/css/listpage.css" />
	<link type="text/css" rel="stylesheet" href="/static/css/tooltip/tooltip.css" />
	<?php
		//加载css
		if(isset($cssList)){
			foreach($cssList as $css){
				echo '<link type="text/css" href="'.$css.'" rel="stylesheet" />'.chr(10).chr(13);
			}
		}
	?>

	<script type="text/javascript" src="/static/js/jquery/jquery-3.4.1.js" ></script>

	<script type="text/javascript" src="/static/js/jquery/jquery-migrate-1.4.1.min.js" ></script>
	<script src="/static/js/jquery/jquery-ui.min.js" type="text/javascript"></script>	
	<script type="text/javascript" src="/static/js/jquery/thickbox31.js"></script>	
	<script type="text/javascript" src="/static/js/common.js" charset="UTF-8"></script>
	<?php
		//加载js
		if(isset($jsList)){
			foreach($jsList as $js){
				echo '<script language="JavaScript" type="text/javascript" src="'.$js.'?t='.$date.'" charset="UTF-8" ></script>';
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
<body >
<div id="loadingLayer" style="display:none;">
	<div class="TB_overlayBG"></div>
	<div class="loading-01" id="loading" style="top: 40%; left:30%; z-index:999;">Loading ......</div>
</div>

<div id="msgLayer" style="display:none; margin-top: 8px;">
	<div >
      <div id="msgContent"></div>
    </div>	
</div>

<div class="wrap">