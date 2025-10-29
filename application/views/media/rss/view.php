<?php
if($type) {
?>
	<table width="500" cellspacing="0" cellpadding="0" border="0" class="from-panel">
		<tbody>
			<tr>
				<td width="100">
					<?php echo lang('text.name');?>
				</td>
				<td>
					<input readonly type="text" id="name" name="name" class="text ui-widget-content ui-corner-all" style="width:200px;" value="<?php echo $rss->name;?>"/>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang("desc");?>
				</td>
				<td>
					<textarea readonly name="descr" id="descr" class="ui-widget-content ui-corner-all" rows="2" style="width:200px;"><?php echo $rss->descr;?></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo lang('text.content');?>
				</td>
				<td>	
					<textarea name="url" id="url" rows="6" cols="35" readonly><?php echo $rss->url;?></textarea>
				</td>
			</tr>
		</tbody>
	</table>
<?php
}else {
?>
<table border="1"  bordercolor="#e5e5e5" cellpadding=10 cellspacing=10 height=100%>
	<tr>
    	<td width="20%"><?php echo lang('name');?>:</td>
    	<td><font style="font-size: 14px;"><?php echo $rss['name'];?></font></td>
  	</tr>
  	<tr>
    	<td><?php echo lang('desc');?>:</td>
    	<td><font style="font-size: 14px;"><?php echo $rss['descr'];?></font></td>
  	</tr>
	<tr>
    	<td><?php echo lang('text.content');?>:</td>
    	<td><font style="font-size: 14px;"><?php echo $rss['url'];?></font></td>
 	</tr>
  	<tr>
    	<td><?php echo lang('rss.content');?>:</td>
    	<td>
			<div style="overflow-y: scroll; height:200px;">
			<?php
			$rss_array = explode('<=!=>', $rss['content']);
			for($i=0 ; $i<count($rss_array); $i++) {
				$rss_array_one = explode('<==>', $rss_array[$i]);
			?>
				<div style="padding: 4px 0px; font-size:14px;"><?php echo $rss_array_one[0];?></div>
				<div style="font-size:13px;"><?php echo $rss_array_one[1];?></div>
				<legend style="padding: 5px 0px;"></legend>
			<?php
			}
			?>
			</div>
		</td>
  	</tr>
	<?php
  	}
 	 ?>
</table>