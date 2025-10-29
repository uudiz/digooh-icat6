<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
		<tbody>
			<tr>
				<td width="120">
					<?php echo lang('tag');?>
				</td>
				<td>
					<input type="text" id="name" name="name" style="width:150px;" />
				</td>
				<td>
					<div class="attention" id="errorName" style="display:none;">
						<?php echo lang('warn.tag.name');?>
					</div>
				</td>
			</tr>

			<tr>
				<td>
					<?php echo lang("desc");?>
				</td>
				<td>
					<textarea name="descr" id="descr" rows="3" style="width:150px;"></textarea>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td >
						<?php echo lang('player');?>
				</td>
				<td>
					<select  data-placeholder="" id="players-select-options" class="chosen-select" multiple style="max-height:100px">

						<?php foreach ($players as $player):?>
							<option value="<?php echo $player->id;?>"  ><?php echo $player->name;?></option>
						<?php endforeach;?>
					</select>   
					<span  >
						<a data-fancybox  data-type="ajax" data-src="/player/player_map/players-select-options" href="javascript:;">
							<svg class='search-player-icon'  title = "search devices" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path></svg>
						</a>
					</span>     
				</span>  
				</td>

			</tr>  						
		</tbody>
</table>
<p class="btn-center">
	<input type="hidden" id="id" name="id" value="0" />
 	<a class="btn-01" href="javascript:void(0);" onclick="tag.doSave();"><span><?php echo lang('button.save');?></span></a>
	<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>	
</p>
<<script>
	$('.chosen-select').chosen({width: "200px"});
	$("ul.chosen-choices").css({'overflow': 'auto', 'max-height': '200px'});
</script>