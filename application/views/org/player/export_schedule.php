<head>


<style>

	body{text-align:center} 

	#center {
		margin: 0 auto;
	}

</style>

 <script>
  $(function() {
	  $("#datepicker").datepicker({
          dateFormat:"yy-mm-dd",
          onSelect: function (dateText, inst) {
              window.location.href = '/player/get_reports?id='+<?php echo $id;?>+"&day="+dateText;
          },
      });
  });
  </script>
</head>

<body>
<div id='center'>
<label><Strong>Please click on date to get xls:</Strong></label>
<div id="datepicker"> </div>
</div>
</body>