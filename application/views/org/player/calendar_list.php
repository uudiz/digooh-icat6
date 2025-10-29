<head>
	<meta charset='utf-8' />
	<link href='/static/fullcalendar-3.1.0/fullcalendar.min.css' rel='stylesheet' />
	<link href='/static/fullcalendar-3.1.0/fullcalendar.print.min.css' rel='stylesheet' media='print' />

	<script src='/static/fullcalendar-3.1.0/lib/moment.min.js'></script>
	<script src='/static/fullcalendar-3.1.0/fullcalendar.min.js'></script>
	<script src='/static/js/jquery/jquery-ui.min.js'></script>


	<style>
		body {
			margin: 40px 10px;
			padding: 0;
			font-family: "Lucida Grande", Helvetica, Arial, Verdana, sans-serif;
			font-size: 14px;
		}


		#calendar {
			max-width: 900px;
			margin: 0 auto;
		}
	</style>
</head>

<body>

	<input type="hidden" id="pid" value='<?php echo $id; ?>' />
	<div id='calendar'></div>
</body>

<script>
	$(document).ready(function() {
		var id = $('#pid').val();
		var calendarEl = document.getElementById('calendar');
		var calendar = new FullCalendar.Calendar(calendarEl, {
			initialView: 'agendaDay',
			customButtons: {
				datePickerButton: {
					text: 'Export xls',
					click: function() {


						var $btnCustom = $('.fc-datePickerButton-button'); // name of custom  button in the generated code
						$btnCustom.after('<input type="hidden" id="hiddenDate" class="datepicker"/>');

						$("#hiddenDate").datepicker({
							showOn: "button",

							dateFormat: "yy-mm-dd",
							onSelect: function(dateText, inst) {
								$('#calendar').fullCalendar('gotoDate', dateText);
								window.location.href = '/player/get_reports?id=' + id + "&day=" + dateText;
							},
						});

						var $btnDatepicker = $(".ui-datepicker-trigger"); // name of the generated datepicker UI 
						//Below are required for manipulating dynamically created datepicker on custom button click
						$("#hiddenDate").show().focus().hide();
						$btnDatepicker.trigger("click"); //dynamically generated button for datepicker when clicked on input textbox
						$btnDatepicker.hide();
						$btnDatepicker.remove();
						$("input.datepicker").not(":first").remove(); //dynamically appended every time on custom button click

					}
				}
			},

			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'agendaDay,agendaWeek,month'
			},

			// customize the button names,
			// otherwise they'd all just say "list"
			views: {
				agendaDay: {
					buttonText: 'Daily'
				},
				agendaWeek: {
					buttonText: 'Weekly'
				},
				month: {
					buttonText: 'Monthly'
				}
			},


			defaultDate: new Date(),
			navLinks: true, // can click day/week names to navigate views
			editable: false,
			eventLimit: false, // allow "more" link when too many events	
			allDaySlot: false,

			events: {
				url: '/player/prepare_events?id=' + id,

			}
		});
		calendar.render();
	});
</script>