<!DOCTYPE html>

<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
    <title><?php echo lang('app.title'); ?></title>
  
	<link href="/assets/css/style.default.css" rel="stylesheet">
 
   
  <link rel="stylesheet" href="/static/english/css/reset.css">
  <link rel="stylesheet" href="/assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="/assets/css/bootstrap-colorpicker.min.css">
    <link href="/assets/css/pygments.css" type="text/css" rel="stylesheet" />
<link href="/assets/prettify/prettify.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet/less" type="text/css" href="/assets/css/timepicker.less" />
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
	<?php
		//加载css
		if(isset($cssList)){
			foreach($cssList as $css){
				echo '<link type="text/css" href="'.$css.'" rel="stylesheet" />'.chr(10).chr(13);
			}
		}
	?>


	
	
<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 3 -->

<!--
<script src="https://code.jquery.com/jquery-migrate-3.0.0.js"></script>
 Bootstrap 3.3.7 -->


<script src="/assets/js/jquery.min.js"></script>
<script src="/assets/js/jquery-ui.min.js"></script>
<script src="/assets/js/bootstrap.min.js"></script>
<script src="/assets/js/select2.min.js"></script>
<script src="/assets/js/custom.js"></script>
<script src="/assets/js/bootstrap-colorpicker.min.js"></script> 
<script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>  
<script type="text/javascript" src="/assets/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="/assets/prettify/prettify.js"></script>
<script type="text/javascript" src="/assets/js/bootstrap-timepicker.js"></script>	
  
   <script type="text/javascript">
 $.fn.modal.Constructor.prototype.enforceFocus = function () {};
 </script>
<!-- AdminLTE App -->

<script src="/assets/js/bootstrap-dialog.min.js"></script>
<link href="/assets/css/fullcalendar.min.css" rel="stylesheet">
<script src="/assets/js/moment.min.js"></script>
<script src="/assets/js/fullcalendar.min.js"></script>

<script src="/assets/js/mia.js"></script>

	<?php
		//加载js
		if(isset($jsList)){
			foreach($jsList as $js){
				echo '<script  type="text/javascript" src="'.$js.'" charset="UTF-8" ></script>';
				
			}
		}
	?>
	
	<style>
		.custom-logo {
			<?php if(isset($custom_logo)){?>
			background-image:url(/images/logos/<?php echo $custom_logo; ?>);
			background-repeat:no-repeat;

			<?php 
			}else {
				if($this->config->item('mia_system_set') >= $this->config->item('mia_system_np200')) {
			?>
			background-image:url(/static/<?php echo $lang;?>/images/top.png);
			left:0px;
			top:0px;
			<?php		
				}else {
			?>
			background-image:url(/static/<?php echo $lang;?>/images/3top.png);
			left:0px;
			top:0px;
			<?php	
				}
			}
			?>
		}
	</style>	
</head>

<body>
<!-- Preloader -->


<section>
  <div class="leftpanel">
    
    
    <div>

	 <a href="/index" >
        <img width=100% height=100% src="/images/logos/<?php if(isset($custom_logo)) echo $custom_logo; else echo 'default_logo.png'; ?>">
   	 </a>
    </div><!-- logopanel -->
	
	<script type="text/javascript">
	  <?php 
	  $path = $_SERVER["REQUEST_URI"];
	  
	  if(strpos($_SERVER["REQUEST_URI"], "?")){
	  	$path= "/".explode("/",$_SERVER["REQUEST_URI"])[1];
	  }
	  
	  $startclass = "";
	  $startstyle = "";
	  $mediaclass = "";
	  $mediastyle = "";
	  $setupclass = "";
	  $setupstyle = "";
	  
	 
	  
	  if(in_array($path, array('/criteria','/group','/player','/template','/playlist','/schedule','/index','/'))){
	  	$startclass = "nav-active active";
	  	$startstyle = "display: block;";
	  }
	  else if(in_array($path, array('/tag','/media/images','/media/videos','/folder','/rss'))){
	  	$mediaclass = "nav-active active";
	  	$mediastyle = "display: block;";
	  }
	  else if(in_array($path, array('/config/timers','/ftp','/configxml','/company','/user','/player/company_player','/player/anew_player'))){
	  	$setupclass= "nav-active active";
	  	$setupstyle= "display: block;";
	  }
	  
	  ?>
	
	</script>
  
     <div class="leftpanelinner">
         <ul class="nav nav-pills nav-stacked nav-bracket">		
    		<?php if($auth < $SYSTEM):?> 
     	
        		<li  class="nav-parent <?php echo $startclass;?>">
        		   <a href=""><i class="fa fa-dashboard"></i> <span><?php echo lang("start")?></span></a>
		         	 <ul class="children" style="<?php echo $startstyle?>">
			            <li ><a href="/criteria"><i class="fa fa-caret-right"></i> <?php echo lang('criteria');?></a></li>
			            <li id="nav_player"><a href="/player"><i class="fa fa-caret-right"></i> <?php echo lang('player');?></a></li>
			            <?php if($auth >= $FRANCHISE):?>
			            <li><a href="/template"><i class="fa fa-caret-right"></i> <?php echo lang('template');?></a></li>
			            <?php endif?>
			            <?php if($auth > $VIEW):?>
			            <li id="nav_playlist"><a href="/playlist"><i class="fa fa-caret-right"></i> <?php echo lang('playlist');?></a></li>
			            <?php endif?>

		
	          	  </ul>
        	</li>
            <?php endif?>
            
            
     
            <?php if($auth < $SYSTEM && $auth >= $FRANCHISE):?>
            <li class="nav-parent <?php echo $mediaclass;?>" >
                <a href=""><i class="fa fa-file"></i> <span><?php echo lang("material")?></span></a>
         	 	<ul class="children" style="<?php echo $mediastyle?>" >
         	 		<li><a href="/tag"><i class="fa fa-caret-right"></i> <?php echo lang('tag');?></a></li>
         	 		<li><a href="/folder"><i class="fa fa-caret-right"></i> <?php echo lang('folder');?></a></li>
		            <li><a href="/media/images"><i class="fa fa-caret-right"></i> <?php echo lang('image');?></a></li>
		            <li><a href="/media/videos"><i class="fa fa-caret-right"></i><?php echo lang('video');?></a></li>

		            <li><a href="/rss"><i class="fa fa-caret-right"></i> <?php echo lang('rss');?></a></li>
	          </ul>
	        </li>        	
	        <?php endif?>
            <li class="nav-parent <?php echo $setupclass;?>" >
            	<a href=""><i class="fa fa-cogs"></i> <span><?php echo lang("setup")?></span></a>
         	 	<ul class="children" style="<?php echo $setupstyle?>">
	       
	             <?php if($auth < $SYSTEM):?>
        	 	<li><a href="/config/timers"><i class="fa fa-caret-right"></i><?php echo lang('timer.settings');?></a></li>
        	 	<li><a href="/ftp"><i class="fa fa-caret-right"></i><?php echo lang('ftp');?></a></li>
        	 	<li><a href="/configxml"><i class="fa fa-caret-right"></i><?php echo lang('device.setup');?></a></li>
			 	<?php endif;?>
	           <?php if ($auth >= $ADMIN):?>
	          	<?php if ($auth == $SYSTEM): ?>
	           		 <li><a href="/company"><i class="fa fa-caret-right"></i><?php echo lang('company');?></a></li>
	           	 <?php endif?>
	           		 <li><a href="/user"><i class="fa fa-caret-right"></i><?php echo lang('account');?></a></li>
	               <?php if ($auth == $SYSTEM): ?>
	           			  <li><a href="/player/company_player"><i class="fa fa-caret-right"></i>Online Player</a></li>
	             	      <li><a href="/player/anew_player"><i class="fa fa-caret-right"></i>New player</a></li>
	            	 <?php endif;?>
	       	    <?php endif;?>
	          </ul>
	        </li>      
	         <?php if($auth > $GROUP):?>      
   			 <li <?php if(in_array($_SERVER["REQUEST_URI"], array('/playback','/playbandwidth','/configxml/configration','/software','/player/player_online'))) echo 'class="nav-parent nav-active active"'; else echo 'class="nav-parent"'?>><a href=""><i class="fa fa-tasks"></i> <span><?php echo lang("advanced")?></span></a>
         	 	<ul class="children" <?php if(in_array($_SERVER["REQUEST_URI"], array('/playback','/playbandwidth','/configxml/configration','/software','/player/player_online'))) echo 'style="display: block"'; ?>>

		          	<?php if($auth < $SYSTEM):?>
							<li><a href="/playback" target="main-frame"><i class="fa fa-caret-right"></i><?php echo lang('playback');?></a></li>
					<?php if($this->config->item('bandwidth_open')):?>
							<li><a href="/playbandwidth" ><i class="fa fa-caret-right"></i><?php echo lang('playbandwidth');?></a></li>
					<?php endif;?>
							<li><a href="/configxml/configration" ><i class="fa fa-caret-right"></i><?php echo lang('device.configration');?></a></li>
					<?php endif;?>
					<?php if($auth >= $ADMIN):?>
									<li><a href="/software" ><i class="fa fa-caret-right"></i><?php echo lang('software');?></a></li>
					<?php endif;?>				<!--Special Command Set  2013-10-10-->
					<?php if($auth < $SYSTEM):?>
								<li>	<a href="/player/player_online"><i class="fa fa-caret-right"></i><?php echo lang('special');?></a></li>
					<?php endif;?>
	          </ul>
	        </li>  
	        
	        <?php endif?> 
        </ul>
     </div> 
  	</div>      
        
     <div class="mainpanel">
          <div class="headerbar">

      			<a class="menutoggle"><i class="fa fa-bars"></i></a>
      			
  				<div class="header-right">
  
        			<ul class="headermenu">
            		  <li> 
            			  <div class="btn-group">
				              <button type="button" class="btn btn-default dropdown-toggle tp-icon" data-toggle="dropdown">
				               <i class="glyphicon glyphicon-user"></i>
				                <?php echo $username; ?>
				                <span class="caret"></span>
				              </button>
				              <ul class="dropdown-menu dropdown-menu-usermenu pull-right">
				             <?php if($auth < $SYSTEM):?>
				                <li><a target='dialog' href="/sysconfig/edit"><i class="glyphicon glyphicon-cog"></i> <?php echo lang('account'); ?></a></li>
				             <?php endif?>
				                <li><a target='dialog' href="/sysconfig/password"><i class="glyphicon glyphicon-lock"></i> <?php echo lang('password'); ?></a></li>
				              </ul>
				            </div>
            		  
            		  
            		  </li>
          
			          <li>
			               <a href="/login/doLogout" class="btn btn-default tp-icon">
                                    <i class="glyphicon glyphicon-log-out"></i>
                                    <span><?php echo lang('button.logout'); ?></span>
                          </a>
			          
			          </li>
	          		
            		 </ul> 			
      	  		</div>
      	  </div>
      	  <div id="rightpage">
      	  <?php 
		          if (isset($body_file)) {
		             $this->load->view($body_file);
		          }
		  ?>
         </div>     

     
     </div>
    

</section>

</body>



 <script type="text/javascript">

   $(function() {

		var pathname = this.location.pathname;
		if(this.location.search.indexOf("?")>=0){
			pathname = '/'+this.location.pathname.split("/")[1];
		}
		
		console.log(pathname);
		
		$('.nav li a[href="' + pathname + '"]').parent('li').addClass('active');
	
	    // dialogs
	    if ($.fn.ajaxTodialog) {
	        $("a[target=dialog]").ajaxTodialog();
	    }

	});
   

</script> 
<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. -->

</html>

