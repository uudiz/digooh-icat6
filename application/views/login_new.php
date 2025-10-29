
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">
  <!--  
  	<link rel="shortcut icon" href="images/favicon.png" type="image/png">
	-->
  <title><?php echo lang('login_title')?></title>

  <link href="/assets/css/style.default.css" rel="stylesheet">

  <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src="js/html5shiv.js"></script>
  <script src="js/respond.min.js"></script>
  <![endif]-->
</head>

<body class="signin">


<section>

    <div class="signinpanel">
        
        <div class="row">
            
            
      	 <div class="col-md-offset-3 col-md-6">


            	
            	
                <form action="/login/doLogin" method="post" id="loginForm">
                    <h4 class="nomargin">Sign In</h4>
                    <p class="mt5 mb20">Login to access your account.</p>
                <?php if (isset($errMsg)):?>
              		<div class="alert alert-warning" >
					     <?php echo $errMsg;?>
				  	</div>
				<?php endif;?>
                    <input type="text" required autofocus class="form-control uname" placeholder="<?php echo lang('user_name')?>" id="username" name="username" <?php if(isset($username))echo "value="."\"".$username."\"";else set_value("username");?>/>
                    <input type="password" required class="form-control pword" placeholder="<?php echo lang('password')?>" id="password" name="password" <?php if(isset($password))echo "value="."\"".$password."\"";?>/>
                    <a  id="forgetpwd_btn"><small><?php echo lang('forget_pwd_info1'); ?></small></a>
                    <button class="btn btn-success btn-block" type="submit" name="submit" id="submit">Sign In</button>
                    
                </form>
                
                
                <form class="form-horizontal" id="pwdForm" style="display:none"> 
					<h4 class="nomargin">Reset password</h4>
                    <p class="mt5 mb20">enter the account name you use to sign in.</p>
                    <div id="pwdmsg"> </div>
                    <div id="pwdalert"></div>

                   	 <input required type="text" class="form-control uname" placeholder="<?php echo lang('user_name')?>" id="name" name="name" />
	
		
					<a  class="btn btn-default btn-large" id="back_btn">Back </a>
								
					<a class="pull-right btn btn-success btn-large "  id="getpwemail">Next</a>
				
							
			   </form>
                
            	<div class="signup-footer">
            		<div class="pull-left">
               			 &copy; 2017. All Rights Reserved.       		 	
           		 	</div>
            </div>


        </div><!-- col-sm-5 -->   
        </div><!-- row -->
        

        
    </div><!-- signin -->
  
</section>
<script src="/assets/js/jquery.min.js"></script>
<script src="/assets/js/bootstrap.min.js"></script>
 <script>
		
            $("#forgetpwd_btn").click(function() {  
                $("#pwdForm").css("display", "block");  
                $("#loginForm").css("display", "none");  
            });  
            $("#back_btn").click(function() {  
                $("#pwdForm").css("display", "none");  
                $("#loginForm").css("display", "block");  
            });  

	
        
			var username = document.getElementById('username').value;
			var password = document.getElementById('password').value;
			if(username != '' && password != ''){
				setTimeout(function(){
					document.getElementById('submit').click();
				}, 888);
			}

			
			$("#getpwemail").on('click',function(){
				console.log("clicked");
				$.post('/login/get_pwd', {
		            name: $('#name').val()
		        }, function(data){
			        	console.log(data);
			    		if(data.code == false){
							
							$("#pwdalert").addClass("alert alert-warning");
							$("#pwdalert").html(data.msg);
						}else{
							alert(data.msg);
							$("#back_btn").click();
						}
		        },'json');
			 }); 
			
</script>

</body>
</html>
