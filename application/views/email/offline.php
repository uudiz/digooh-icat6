<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="utf-8" lang="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<head>
		<title>Report player status</title>
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
		<p><strong>Dear <?php echo $company_name; ?></strong></p>
		<p>
			<?php echo $this->lang->line('email.player.offline.content');?>
		</p>
		<table class="table-list">
			<tr>
				<th><?php echo lang('player');?></th>
				<th width="80"><?php echo lang('sn');?></th>
				<th width="120"><?php echo lang('last.connect'); ?></th>
		
			</tr>
			<?php $index = 0; foreach($players as $p):?>
			<tr <?php if($index%2 != 0):?>class="even"<?php endif;?>>
				<td><?php echo $p->name;?></td>
				<td><?php echo format_sn($p->sn);?></td>
				<td><?php if(isset($p->last_connect)) echo $p->last_connect;?></td>
			</tr>
			<?php $index++; endforeach;?>
		</table>
		<p>
			 <?php echo $this->lang->line('email.player.offline.Sender');?><br/><?php echo date('Y-m-d H:i:s'); ?>
		</p>
	</body>
</html>