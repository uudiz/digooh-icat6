<head>
<meta charset='utf-8' />
<link href='/static/fullcalendar-3.1.0/fullcalendar.min.css' rel='stylesheet' />
<link href='/static/fullcalendar-3.1.0/fullcalendar.print.min.css' rel='stylesheet' media='print' />
<script src='/static/fullcalendar-3.1.0/lib/moment.min.js'></script>
<script src='/static/fullcalendar-3.1.0/fullcalendar.min.js'></script>
<script>

	$(document).ready(function() {
		var id = $('#pid').val();
		$('#calendar').fullCalendar({		
	    		
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'listDay,listWeek,month'
			},

			// customize the button names,
			// otherwise they'd all just say "list"

			views: {
				listDay: { buttonText: 'Daily' },
				listWeek: { buttonText: 'Weekly' },
				month: { buttonText: 'Monthly' },
			},
			
			defaultView: 'listDay',
			defaultDate: new Date(),
			navLinks: true, // can click day/week names to navigate views
			editable: false,
			eventLimit: false, // allow "more" link when too many events	
			allDaySlot: false,
				
			events: {
				url: '/campaign/prepare_events',
	
			}
			
		
		});
		
	});

</script>
<style>

	body {
		margin: 40px 10px;
		padding: 0;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
		font-size: 14px;
	}

	#calendar {
		max-width: 900px;
		margin: 0 auto;
	}

</style>
</head>

<body>

<input type="hidden" id="pid" value='<?php echo $id;?>' />
<div id='calendar'></div>
</body>