<html xmlns:o="urn:schemas-microsoft-com:office:office"
                xmlns:x="urn:schemas-microsoft-com:office:excel"
                xmlns="http://www.w3.org/TR/REC-html40">
<head>
        <meta http-equiv="expires" content="Mon, 06 Jan 1999 00:00:01 GMT">
        <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
        <!--[if gte mso 9]><xml>
        <x:ExcelWorkbook>
        <x:ExcelWorksheets>
                   <x:ExcelWorksheet>
                   <x:Name></x:Name>
                   <x:WorksheetOptions>
                                   <x:DisplayGridlines/>
                   </x:WorksheetOptions>
                   </x:ExcelWorksheet>
        </x:ExcelWorksheets>
        </x:ExcelWorkbook>
        </xml><![endif]-->
</head>
<body>
<table>
    <tr height="25" style="height:20.75pt">
       <th><?php echo lang('post.date');?></th>
	   <th><?php echo lang('group');?></th>
	   <th><?php echo lang('player');?></th>
	   <th><?php echo lang('sn');?></th>
	   <th><?php echo lang('media');?></th>
	   <th><?php echo lang('times');?></th>
	   <th><?php echo lang('duration');?></th>
    </tr>
	<?php if(!isset($data)): ?>
	<tr>
		<td colspan="7">
			<?php echo lang("empty.result");?>
		</td>
	</tr>
	<?php else:?>
	<?php foreach($data as $row):?>
    <tr>
	  <td><?php echo $row->post_date;?></td>
	  <td><?php echo $row->group_name;?></td>
	  <td><?php echo $row->player_name;?></td>
	  <td><?php echo format_sn($row->sn);?></td>
	  <td><?php echo $row->media_name;?></td>
	  <td><?php echo $row->times; ?></td>
	  <td><?php echo $row->duration; ?></td>
	</tr>
	<?php endforeach;?>
	<?php endif;?>	
</table>
</body>
</html>