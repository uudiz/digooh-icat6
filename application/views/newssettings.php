<div class="clear"></div>
<h1 class="tit-01"><?php echo lang('news');?></h1>
<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<script type="text/javascript">
 function saveInterval(){
	
	$.post('/news/do_save', {
		interval : $('#intervalhours').val()
	}, function(data){
		if(data.code == 0){
			showMsg(data.msg, 'success');
		}else{
			showMsg(data.msg, 'error');
		}
	},'json');
}
</script>

<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
	<tbody>
		<tr>
			<td><?php echo lang('xmlUrl');?></td>
			<td><?php echo $data->url?> </td>
		</tr>
		<tr>
			<td><?php echo lang('lastPub');?></td>
			<td><?php if(isset($data->last_pub_time)&&$data->last_pub_time) echo date('D, d M Y H:i:s',$data->last_pub_time)?></td>
		</tr>
		<tr>
			<td><?php echo lang('interval');?></td>
			<td><select id='intervalhours'>
				<?php for($i=1;$i<=24;$i++):?>
				<option value=<?php echo $i?> <?php if($data->check_interval==$i):?>selected="selected"<?php endif;?>><?php echo $i?></option>
				<?php endfor;?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<a class="btn-01" onclick="saveInterval();" href="javascript:void(0);">
					<span><?php echo lang('button.save');?></span>
				</a>
			</td>
		</tr>
	</tbody>
</table>


