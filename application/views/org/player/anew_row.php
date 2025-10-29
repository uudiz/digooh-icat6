<tr>
	<td>
		<?php echo format_sn($sn);?>
		<input type="hidden" name="sn" value="<?php echo $sn;?>" />
	</td>
	<td>
		<?php echo $mac;?>
		<input type="hidden" name="rowMac" value="<?php echo $mac;?>" />
	</td>
	<td>
		<?php echo $cname;?>
		<input type="hidden" name="cid" value="<?php echo $cid;?>" />
		<input type="hidden" name="code" value="<?php if(isset($code)){echo $code;}?>" />
		<input type="hidden" name="desc" value="<?php if(isset($descr)){echo $descr;}?>" />
	</td>
	<td>
		<a href="javascript:void(0);" onclick="player.removePlayerRow(this);"><img id="del_465" src="/images/icons/24-del.png" title="Delete"></a>
	</td>
</tr>
