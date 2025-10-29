<head>
<link rel="stylesheet" href="/static/css/jquery/jquery-ui.min.css" />
<link rel="stylesheet" href="/static/css/alertify.core.css" />
<link rel="stylesheet" href="/static/css/alertify.default.css" />

<script src="/static/js/alertify.min.js" type="text/javascript" charset="utf-8"></script>
<script src='/static/js/jquery/jquery-ui.min.js'></script>


 <style>
  .ui-progressbar {
    position: relative;
  }
  .progress-label {
    position: absolute;
    left: 50%;
    top: 4px;
    font-weight: bold;
    text-shadow: 1px 1px 0 #fff;
  }
  </style>
  <script>
  $(function() {

	
    var progressbar = $( "#progressbar" );

      progressLabel = $( ".progress-label" );
      var total = <?php echo $total;?>;
 	  var index = 0;

 
    function progress(step) {
      progressbar.progressbar( "value", step );
 
    }

    <?php if(isset($campaigns)):?>

	   	  progressbar.progressbar({
	        value: false,
	        max: 100,
	        change: function() {
	          progressLabel.text( progressbar.progressbar( "value" ) + "%" );
	        },
	        complete: function() {
	         // progressLabel.text( "完成！" );
	          
	        }
	      });
	      
	   		<?php foreach($campaigns as $cam):?>
	   			index++;
	   			$("#infoid").val();
	   			document.getElementById('infoid').value = $("#infoid").val()+"<?php echo "publishing...$cam->name";?>";
		        $.post('/campaign/do_republish_campaign',  {playlist_id: <?php echo $cam->id;?>}, function(data){
		            if (data.code == 0) {
		            	document.getElementById('infoid').value = $("#infoid").val()+data.msg+'\n';
		            	//document.getElementById('infoid').value =data.msg;
		            }
		            else {
		            	document.getElementById('infoid').value = $("#infoid").val()+data.msg;
		            	//document.getElementById('infoid').value =data.msg;
		            }
		        }, 'json');


		        progressbar.progressbar( "value", parseInt((index/total)*100) );
		     //   document.getElementById('infoid').value = "<?php echo "publishing $cam->name ...publish success";?>";
		<?php endforeach;?> 
	//	tb_remove();      
    <?php endif;?>
 
    //setTimeout( progress, 3000 );
  });
  </script>
</head>
<body>
 
<div id="progressbar"><div class="progress-label">loading...</div></div>

<textarea id="infoid" class="ui-widget-content ui-corner-all" rows="6" style="width:320px;"></textarea>

</body>
 