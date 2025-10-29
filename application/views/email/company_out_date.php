<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="utf-8" lang="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<head>
		<title>License will soon expire</title>
		<style>
		body{
			margin: 0px 0px;
			padding: 0px 0px;
		}
		.table-list {
		    font-size: 12px;
		    border: 1px solid #ccc;
			border-spacing: 0;
		}
		
		.table-list th {
		    border-top: 1px solid #ccc;
		    border-bottom: 1px solid #ccc;
		    font-size: 14px;
		    text-align: left;
		    height: 30px;
		    line-height: 30px;
		    text-align: center;
			padding-left: 20px;
		    background: url(http://148.251.126.77/static/english/images/table-th.gif);
		    white-space: nowrap;
		    font-weight: normal;
		}
		
		.table-list td {
		    font-size: 12px;
		    height: 30px;
		    padding: 4px 0px 4px 4px;
		    color: #4f6b72;
		    white-space: nowrap;
		    text-align: left;
			padding-left: 20px;
		}
		
		.table-list tr.even {
		    background: #f5f5f5;
		}
		</style>
	</head>
	<body>
		<p><strong>Dear Admin:</strong></p>
		<p>
			This message is to inform you that some company will expire within the next 7 days.
		</p>
		<table class="table-list">
			<tr>
				<th>CompanyName</th>
				<th width="120">EndDate</th>
			</tr>
			<?php 
				for($i=0; $i<count($company_name); $i++) {
			?>
			<tr <?php if($i%2 != 0):?>class="even"<?php endif;?>>
				<td><?php echo $company_name[$i];?></td>
				<td><?php echo $end[$i];?></td>
			</tr>
			<?php }?>
		</table>
		<p>
			Thank you<br/>
			iCAT Technical Support Staff<br/>
			<?php echo date('Y-m-d H:i:s'); ?>
		</p>
	</body>
</html>