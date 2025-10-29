<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<form method="POST" id="cf" action="/rss/do_save" >
	<table width="500" cellspacing="0" cellpadding="0" border="0" class="from-panel">
		<tbody>
			<tr>
				<td width="100">
					<?php echo lang('text.name');?>
				</td>
				<td>
					<input type="text" id="name" name="name" class="text ui-widget-content ui-corner-all" style="width:200px;" value="<?php echo $rss->name;?>"/>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang("desc");?>
				</td>
				<td>
					<textarea name="descr" id="descr" class="ui-widget-content ui-corner-all" rows="2" style="width:200px;"><?php echo $rss->descr;?></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang('text.content');?>
				</td>
				<td>	
					<textarea name="url" id="url" class="ui-widget-content ui-corner-all" rows="6" cols="35"><?php echo $rss->url;?></textarea>
				</td>
			</tr>
		</tbody>
</table>
<p class="btn-center">
	<input type="hidden" id="type" name="type" value="1"/>
	<input type="hidden" id="id" name="id" value="<?php echo $rss->id;?>" />
	<a class="btn-01" href="javascript:void(0);" onclick="rss.doSave();"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>
