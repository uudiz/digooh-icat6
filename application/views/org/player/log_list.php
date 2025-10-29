<?php
$indexCount	= count($array);
if($indexCount <= 2) {
	echo 'No Current Files!';
}else {
?>
<table width=560 border="0" align="center" cellpadding="5" cellspacing="1" bgcolor="#add3ef" class="list">
	<tr align="center">
		<td></td>
		<td>ID</td>
		<td>Name</td>
	</tr>
	<?php
	for($index = 0; $index < $indexCount; $index++) {
		if (substr("$array[$index]", 0, 1) != ".") {  // don't list hidden files
	?> 
	<tr bgcolor="#eff3ff" align="center">
		<td></td>
		<td><?php echo $index-1;?></td>
		<td>
			<a href="../<?php echo $this->config->item('cached_errorlog_path').$array[$index];?>" class="subdel"><?php echo $array[$index];?></a>
		</td>
	</tr>
	<?php
		}
	}
	?>
</table>
<?php
}
?>