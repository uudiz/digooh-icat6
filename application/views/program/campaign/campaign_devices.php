
<head>
<link rel="stylesheet" href="/static/css/jquery/jquery.dataTables.min.css" />
<script src='/static/js/jquery/jquery.dataTables.min.js'></script>
</head>

<div id="devicetabs" >
    <fieldset>
        <legend>Selected Devices: <?php  echo !$affect_players?0:count($affect_players) ?>   </legend>
        <div id ='tabs-selected'>
            <table id="affacted-players" class="display compact" >
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>SDAW ID</th>
                        <th>QID</th>
                        <th>Day Per Week</th>
                        <th>Days in Campaign</th>
                    </tr>
                </thead>
            </table>
        </div>
    </fieldset>
    <br/>
    <fieldset >
        <legend>Excluded Devices: <?php echo !$exclude_players?0:count($exclude_players)?>   </legend>

        <div   >
            <table id="excluded-players"  class="display compact">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>SDAW ID</th>
                        <th>QID</th>
                        <th>Day Per Week</th>
                        <th>Days in Campaign</th>                   
                    </tr>
                </thead>
            </table>
        </div>
        
    </fieldset>
    <p class="btn-center">
        <input type="hidden" id="id" name="id" value="0" />
        <a class="btn-01" href="/campaign/export_devices?id=<?php echo $id?>&name=<?php echo $name?>"><span><?php echo lang('export');?></span></a>
        <a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
    </p>    
</div>

<script>

$(document).ready(function() {
 
	var dataSet = <?php echo json_encode($affect_players); ?>;

	$('#affacted-players').DataTable( {
		scrollY:        "300",
        //scrollCollapse: true,
        paging:         false,
        info: false,
		dataType: "json",
        data: dataSet,
        columns: [
            { data: "name" },
            { data: "custom_sn1" },
            { data: "custom_sn2" },
            { data: "daysperweek" },
            { data: "wokringdays" },
        ],

      
    } );


    var ex_dataSet = <?php echo json_encode($exclude_players); ?>;

	$('#excluded-players').DataTable( {
		scrollY:        300,
        //scrollCollapse: true,
        paging:         false,
        info: false,
		dataType: "json",
        data: ex_dataSet,
        columns: [
            { data: "name" },
            { data: "custom_sn1" },
            { data: "custom_sn1" },
            { data: "daysperweek" },
            { data: "wokringdays" },
        ],
   
    } );
} );
</script>