<?php if(empty($area_list)):?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="left" valign="top" width="60%" id="plsTable">
			
       		<div class="clear"></div>
			<div>
  				<p class="btn-left">
					Empty page!
  					<input type="hidden" id="areaIds" name="areaIds" value="0" />
		  			<input type="hidden" id="playlistId" name="playlistId" value="<?php echo $interactionpls->id;?>" />
					<input type="hidden" id="templateId" name="templateId" value="<?php echo $interactionpls->interaction_id;?>" />
					<input type="hidden" id="screenId" name="screenId" value="<?php echo $screen_id;?>" />
				</p>
			</div>
    	</td>
		<td valign="top" class="gray-area from-panel" width="15%">
	    	<p><?php echo lang('playlist');?>:</p>
	       	<p><input name="name" id="name" type="text" style="width: 200px;" value="<?php echo $interactionpls->name;?>"/></p>
	       	<p><?php echo lang('desc');?>:</p>
		   	<p><textarea name="descr" id="descr"  rows="4"  style="width: 200px;"><?php echo $interactionpls->descr;?></textarea></p>
			<p><?php echo lang('touch.timeout.period') . ': <u>' . $interaction->period.'</u> (HH:MM)';?></p>
		   	<p>
		   		<?php
		   		$timeoutList = lang('touch.timeout.action.list');
		   		echo lang('touch.timeout.action') . ': <u>'. $timeoutList[$interaction->action].'</u>';
		   		?>
		   	</p>
	       	<p><?php echo lang('preview');?></p>
	       	<p><img id="previewImg" src="<?php echo $preview_url.'?t='.time();?>"/></p>
		   	<p><?php echo $interaction->name;?> (<?php echo $interaction->width.'X'.$interaction->height;?>)</p>
	    </td>
	    <td align="left" valign="top" width="15%">
    		<div class="zTreeDemoBackground left">
				<ul id="plstreeDemo" class="ztree"></ul>
			</div>
    	</td>
	</tr>
</table>
<?php else:?>
<?php
$view_list = array();
foreach($area_list as $area){if($area->area_type == $this->config->item('area_type_text')) { $view_list[] = $area;}}
foreach($area_list as $area){if($area->area_type == $this->config->item('area_type_staticText')) { $view_list[] = $area;}}
foreach($area_list as $area){if($area->area_type == $this->config->item('area_type_bg')) { $view_list[] = $area;}}
foreach($area_list as $area){if($area->area_type == $this->config->item('area_type_logo')) { $view_list[] = $area;}}
foreach($area_list as $area){if($area->area_type == $this->config->item('area_type_movie')) { $view_list[] = $area;}}
foreach($area_list as $area){if($area->area_type == $this->config->item('area_type_image')) { $view_list[] = $area;}}
foreach($area_list as $area){if($area->area_type == $this->config->item('area_type_date')) { $view_list[] = $area;}}
foreach($area_list as $area){if($area->area_type == $this->config->item('area_type_time')) { $view_list[] = $area;}}
foreach($area_list as $area){if($area->area_type == $this->config->item('area_type_weather')) { $view_list[] = $area;}}
foreach($area_list as $area){if($area->area_type == $this->config->item('area_type_webpage')) { $view_list[] = $area;}}
foreach($area_list as $area){if($area->area_type == $this->config->item('area_type_mask')) { $view_list[] = $area;}}
if($auth == 0) { $area_list = $view_list;}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="left" valign="top" width="60%" id="plsTable">
	    	<div class="icon-list">
	    		<ul>
	          		<?php 
	          		if(!empty($area_list)):
	          			$area_btn = '';
	          		?>
	          		<?php foreach($area_list as $area):?>
					<?php if($area->area_type == $this->config->item('area_type_bg') && $auth > 0):?>
						<li><img id="<?php echo $area->id;?>" type="bg" src="/images/icons/42-08.gif" width="42" height="32" alt="" title="<?php echo $area->name;?>" /></li>
					<?php elseif($area->area_type == $this->config->item('area_type_logo') && $auth > 0):?>
						<li><img id="<?php echo $area->id;?>" type="logo" src="/images/icons/42-07.gif" width="42" height="32" alt="" title="<?php echo $area->name;?>" /></li>
					<?php elseif($area->area_type == $this->config->item('area_type_movie') && $auth > 0):?>
						<li><img id="<?php echo $area->id;?>" type="movie" src="/images/icons/42-01.gif" width="42" height="32" alt="" title="<?php echo $area->name;?>" /></li>	
					<?php elseif($area->area_type == $this->config->item('area_type_image') && $auth > 0):?>
						<li><img id="<?php echo $area->id;?>" type="image" src="/images/icons/42-02.gif" width="42" height="32" alt="" title="<?php echo $area->name;?>" /></li>
					<?php elseif($area->area_type == $this->config->item('area_type_text')):?>
						<li><img id="<?php echo $area->id;?>" type="text" src="/images/icons/42-03.gif" width="42" height="32" alt="" title="<?php echo $area->name;?>" /></li>
					<?php elseif($area->area_type == $this->config->item('area_type_staticText')):?>
						<li><img id="<?php echo $area->id;?>" type="text" src="/images/icons/42-13.gif" width="42" height="32" alt="" title="<?php echo $area->name;?>" /></li>
					<?php elseif($area->area_type == $this->config->item('area_type_date') && $auth > 0):?>
						<li><img id="<?php echo $area->id;?>" type="date" src="/images/icons/42-04.gif" width="42" height="32" alt="" title="<?php echo $area->name;?>" /></li>
					<?php elseif($area->area_type == $this->config->item('area_type_time') && $auth > 0):?>
						<li><img id="<?php echo $area->id;?>" type="time" src="/images/icons/42-05.gif" width="42" height="32" alt="" title="<?php echo $area->name;?>" /></li>
					<?php elseif($area->area_type == $this->config->item('area_type_weather') && $auth > 0):?>
						<li><img id="<?php echo $area->id;?>" type="weather" src="/images/icons/42-06.gif" width="42" height="32" alt="" title="<?php echo $area->name;?>" /></li>	
					<?php elseif($area->area_type == $this->config->item('area_type_webpage') && $auth > 0):?>
						<li><img id="<?php echo $area->id;?>" type="weather" src="/images/icons/42-10.gif" width="42" height="32" alt="" title="<?php echo $area->name;?>" /></li>
					<?php elseif($area->area_type == $this->config->item('area_type_btn') && $auth > 0):
						$area_btn = $area_btn.$area->id.',';
					?>
						<li><img id="<?php echo $area->id;?>" type="btn" src="/images/icons/42-11.gif" width="42" height="32" alt="" title="<?php echo $area->name;?>" /></li>
					<?php endif;?>
					<?php endforeach;?>
					<?php endif;?>
	     		</ul>
			</div>
       		<div class="clear"></div>
	   		<div id="publishing" class="information" style="display:none;margin:10;width:94%;">
				<?php echo lang('publishing');?>
	   		</div>
	   		<?php 
	   			if(!empty($area_list)):
	   			$index = 0;
	   			foreach($area_list as $area):
	   		?>
			<div <?php if($index > 0):?>style="display: none;"<?php endif;?> class="tab-area" id="content_<?php echo $area->id; ?>" ></div>
			<script type="text/javascript" >
				interactionpls.loadArea(<?php echo $interactionpls->id;?>, <?php echo $area->id;?>, 0,<?php echo $screen_id;?>);
			</script>
			<?php
				$index++; 
				endforeach;
				endif;
			?>
			<div>
  				<p class="btn-center">
  					<input type="hidden" id="areaIds" name="areaIds" value="<?php echo substr($area_btn, 0, -1);?>" />
		  			<input type="hidden" id="playlistId" name="playlistId" value="<?php echo $interactionpls->id;?>" />
					<input type="hidden" id="templateId" name="templateId" value="<?php echo $interactionpls->interaction_id;?>" />
					<input type="hidden" id="screenId" name="screenId" value="<?php echo $screen_id;?>" />
					<?php if($auth > 0):?>
					<a href="javascript:void(0);" id="publish" onclick="interactionpls.publishPlaylist('<?php echo lang("warn.publish.empty.media")?>',<?php if($interaction->w < $interaction->h){echo 'true';}else{echo 'false';}?>);" class="btn-01"><span style="color:red;"><?php echo lang('button.publish');?></span></a>
		        	<a href="javascript:void(0);" id="save" onclick="interactionpls.savePlaylist();" class="btn-01"><span><?php echo lang('button.save');?></span></a>
					<a href="/interactionpls" class="btn-01"><span><?php echo lang('button.return');?></span></a>
					<?php else:?>
					<a href="javascript:void(0);" id="publish" onclick="interactionpls.publishPlaylistTouch('<?php echo lang("warn.publish.empty.media")?>',<?php if($interaction->w < $interaction->h){echo 'true';}else{echo 'false';}?>);" class="btn-01"><span style="color:red;"><?php echo lang('button.publish');?></span></a>
		        	<a href="javascript:void(0);" id="save" onclick="interactionpls.savePlaylist();" class="btn-01"><span><?php echo lang('button.save');?></span></a>
					<a href="/interactionpls/view_touch" class="btn-01"><span><?php echo lang('button.return');?></span></a>
					<?php endif;?>
				</p>
			</div>
    	</td>
		<td valign="top" class="gray-area from-panel" width="15%">
	    	<p><?php echo lang('playlist');?>:</p>
	       	<p><input name="name" id="name" type="text" style="width: 200px;" value="<?php echo $interactionpls->name;?>"/></p>
	       	<p><?php echo lang('desc');?>:</p>
		   	<p><textarea name="descr" id="descr"  rows="4"  style="width: 200px;"><?php echo $interactionpls->descr;?></textarea></p>
			<p><?php echo lang('touch.timeout.period') . ': <u>' . $interaction->period.'</u> (HH:MM)';?></p>
		   	<p>
		   		<?php
		   		$timeoutList = lang('touch.timeout.action.list');
		   		echo lang('touch.timeout.action') . ': <u>'. $timeoutList[$interaction->action].'</u>';
		   		?>
		   	</p>
	       	<p><?php echo lang('preview');?></p>
	       	<p><img id="previewImg" src="<?php echo $preview_url.'?t='.time();?>"/></p>
		   	<p><?php echo $interaction->name;?> (<?php echo $interaction->width.'X'.$interaction->height;?>)</p>
	    </td>
	    <td align="left" valign="top" width="15%">
    		<div class="zTreeDemoBackground left">
				<ul id="plstreeDemo" class="ztree"></ul>
			</div>
    	</td>
	</tr>
</table>
<?php endif;?>

<div id="rotateConfirm" title="<?php echo lang('rotate.confirm.title')?>" style="display:none;">
	<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 100px 0;"></span><?php echo lang("rotate.confirm")?></p>
</div>
<SCRIPT type="text/javascript">
		var setting = {
			data: {
				simpleData: {
					enable: true
				}
			},
			callback: {
				onMouseUp: onMouseUp
			}
		};

		var zNodes = [<?php echo $treejson;?>];

		function onMouseUp(event, treeId, treeNode) {
			if(treeNode.iconSkin == 'page' || treeNode.iconSkin == 'mainPage') {
				interactionpls.saveOneScreen(<?php echo $interactionpls->id;?>, treeNode.id);
			}else {
				if(treeNode.iconSkin == 'touch' || treeNode.iconSkin == 'folder') {
					//interactionpls.saveOneScreen(<?php echo $interactionpls->id;?>, <?php echo $screen_list[0]->page_id;?>);
				}else {
					var node = treeNode.getParentNode();
					interactionpls.saveOneScreen(<?php echo $interactionpls->id;?>, node.id);
				}
			}
		}

		$(document).ready(function(){
			$.fn.zTree.init($("#plstreeDemo"), setting, zNodes);
		});
	</SCRIPT>
<script type="text/javascript" >
	interactionpls.initScreenOp('<?php echo $content;?>', '<?php echo $html_content;?>');
</script>