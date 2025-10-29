<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
	<tbody>
		<tr>
			<td>
				Price Entry
			</td>
			<td>
				<select name="part_one" id="part_one" style="width:40px;">
					<option value="-1" <?php if($part_one == -1):?>selected<?php endif;?>>&nbsp;</option>
					<?php foreach($num as $k => $v):?>
					<option value="<?php echo $v;?>" <?php if($v.'_mia' == $part_one):?>selected<?php endif;?>><?php echo $k;?></option>
					<?php endforeach;?>
				</select>
				<select name="part_two" id="part_two" style="width:40px;">
					<option value="-1" <?php if($part_one == -1):?>selected<?php endif;?>>&nbsp;</option>
					<?php foreach($num as $k => $v):?>
					<option value="<?php echo $v;?>" <?php if($v.'_mia' == $part_two):?>selected<?php endif;?>><?php echo $k;?></option>
					<?php endforeach;?>
				</select>
				<select name="part_three" id="part_three" style="width:40px;">
					<option value="-1" <?php if($part_one == -1):?>selected<?php endif;?>>&nbsp;</option>
					<?php foreach($num as $k => $v):?>
					<option value="<?php echo $v;?>" <?php if($v.'_mia' == $part_three):?>selected<?php endif;?>><?php echo $k;?></option>
					<?php endforeach;?>
				</select>
				<select name="part_four" id="part_four" style="width:60px;">
					<?php foreach($symbols as $k => $v):?>
					<option value="<?php echo $v;?>" <?php if($v.'_mia' == $part_four):?>selected<?php endif;?>><?php echo $v;?></option>
					<?php endforeach;?>
				</select>
			</td>
		</tr>
	</tbody>
</table>
<p class="btn-center">
	<input type="hidden" id="id" name="id" value="<?php echo $id;?>" />
	<input type="hidden" id="template_id" name="template_id" value="<?php echo $template_id;?>" />
 	<a class="btn-01" href="javascript:void(0);" onclick="campaign.doSavePrice();"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>
