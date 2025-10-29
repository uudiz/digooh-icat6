<div style="height:700px;overflow:auto;">
<table class="table-list"  width="100%" >
       <tr>
            <th> </th>
            <th width="200">
                <?php echo lang('player');?>
            </th>
			<th width="100" >
                 SDAW ID
            </th>
			<th >
                 <?php echo lang('player_contown');?>
	        </th>
			<th >
                 <?php echo lang('player_connzipcode');?>
	        </th>
			<th >
                 <?php echo lang('player_conaddr');?>
	        </th>
			<th >
                 <?php echo lang('view_direction');?>
	        </th>

        </tr>
		
		<?php if ($players == false): ?>
		<tr>
			<td colspan="8">
				<?php echo lang("empty"); ?>			
			</td>	
		</tr>
		<?php else:?>
		<?php foreach ($players as $row):?>
		<tr >
            <td><input type="checkbox" name="pid" value="<?php echo $row->id;?>" checked="checked"/></td>
			<td>
               <?php echo  $row->name; ?>
			</td>
			<td><?php echo $row->custom_sn1?></td>
			<td >
			 	<?php	echo $row->contown; ?>			
			</td>
			<td >
			 	<?php	echo $row->conzipcode; ?>			
			</td>
			<td >
			 	<?php	echo $row->conaddr; ?>			
			</td>
			<td >
			 	<?php	echo $row->viewdirection; ?>			
			</td>
	
			</tr>
			<?php
                endforeach;
            ?>
        <?php endif?>

</table>
</div>

<p class="btn-center">
	<a class="btn-01" href="javascript:void(0);" onclick="player_selection.update_player_selection('<?php echo $target;?>')"><span><?php echo lang('button.ok');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>

