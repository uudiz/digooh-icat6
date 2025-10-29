<table width="98%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
    		<div class="operate">
    			<?php echo $area->page_name.' >> '.$area->name;?>
				<?php if($area->area_type == $this->config->item('area_type_movie')):?>
				<div style="float:right;">
					<a href="javascript:void(0);"><img src="/images/icons/16-07.gif" width="16" height="16" title="<?php echo lang('image');?>" onclick="interactionpls.addAreaMedia(<?php echo $playlist_id;?>, <?php echo $area->id;?>, <?php echo $area->area_type;?>, <?php echo $this->config->item('media_type_image');?>,1,'<?php echo lang('image.library')?>', <?php echo $screen_id;?>);" /></a>
					<a href="javascript:void(0);"><img src="/images/icons/16-12.gif" width="16" height="16" title="<?php echo lang('video');?>" onclick="interactionpls.addAreaMedia(<?php echo $playlist_id;?>, <?php echo $area->id;?>, <?php echo $area->area_type;?>, <?php echo $this->config->item('media_type_video');?>,1,'<?php echo lang('video.library')?>', <?php echo $screen_id;?>);" /></a>
					<a href="javascript:void(0);"><img src="/images/icons/16-04.gif" width="16" height="16" title="<?php echo lang('delete');?>" onclick="interactionpls.removeAreaAllMedia(<?php echo $playlist_id;?>, <?php echo $area->id;?>,'<?php echo lang('warn.choose.empty.tip');?>','<?php echo lang('tip.remove.choose.item');?>', <?php echo $screen_id;?>);"/></a>
				</div>
				<?php elseif($area->area_type == $this->config->item('area_type_logo') || $area->area_type == $this->config->item('area_type_bg') || $area->area_type == $this->config->item('area_type_image')):?>
				<div style="float:right;">
					<a href="javascript:void(0);"><img src="/images/icons/16-07.gif" width="16" height="16" title="<?php echo lang('image');?>" onclick="interactionpls.addAreaMedia(<?php echo $playlist_id;?>, <?php echo $area->id;?>, <?php echo $area->area_type;?>, <?php echo $this->config->item('media_type_image');?>,1,'<?php echo lang('image.library')?>', <?php echo $screen_id;?>);" /></a>
					<a href="javascript:void(0);"><img src="/images/icons/16-04.gif" width="16" height="16" title="<?php echo lang('delete');?>" onclick="interactionpls.removeAreaAllMedia(<?php echo $playlist_id;?>, <?php echo $area->id;?>,'<?php echo lang('warn.choose.empty.tip');?>','<?php echo lang('tip.remove.choose.item');?>', <?php echo $screen_id;?>);"/></a>
				</div>
				<?php elseif($area->area_type == $this->config->item('area_type_webpage')):?>
				<div class="add-panel">
					<a href="javascript:void(0);" onclick="interactionpls.addAreaMedia(<?php echo $playlist_id;?>, <?php echo $area->id;?>, <?php echo $area->area_type;?>, <?php echo $this->config->item('media_type_webpage');?>,1,'<?php echo lang('webpage.library')?>');" class="webpage" title="Webpage"><?php echo lang('create');?></a>
				</div>
				<?php endif;?>
         	</div>
    	</td>
	</tr>
  	<?php if($area->area_type == $this->config->item('area_type_movie') || $area->area_type == $this->config->item('area_type_image') || $area->area_type == $this->config->item('area_type_bg') || $area->area_type == $this->config->item('area_type_logo')):?>
  	<?php
  		$class = "";
  		switch($area->area_type){
		  	case $this->config->item('area_type_movie'):
				$class="table_movie";
			break;
			case $this->config->item('area_type_image'):
				$class="table_image";
			break;
			case $this->config->item('area_type_bg'):
				$class="table_bg";
			break;
			case $this->config->item('area_type_logo'):
				$class="table_logo";
			break;
	  	}
  	?>
  	<tr>
		<td valign="top" class="content">
			<table width="100%" class="table-list <?php echo $class;?>">
    			<thead>
    				<tr>
	    				<th width="8">&nbsp;</th>
			            <th width="50"><input type="checkbox" onchange="interactionpls.chooseAreaAllMedia(this,<?php echo $area->id;?>);" value="0" /></th>
			            <th width="80"><?php echo lang('media_type');?></th>
			            <th><?php echo lang('media_name');?></th>
			            <th width="120"><?php echo lang('play_time');?>
			            	<?php
							if($area->area_type == $this->config->item('area_type_movie') || $area->area_type == $this->config->item('area_type_image')) {
							?>
							<a href="/interactionpls/edit_playlist_area_media?playlist_id=<?php echo $playlist_id;?>&area_id=<?php echo $area->id;?>&media_type=<?php echo $area->area_type;?>&type=playtime&width=600&height=200" class="thickbox" title="<?php echo lang('edit.media.config');?>" ><img src="/images/icons/16-03.gif" width="16" height="16" title="<?php echo lang('edit');?>" /></a>
							<?php
							}
							?>  
						</th>
			            <th width="120"><?php echo lang('transition_mode');?>
			            	<?php
							if($area->area_type == $this->config->item('area_type_movie') || $area->area_type == $this->config->item('area_type_image')) {
							?>
							<a href="/interactionpls/edit_playlist_area_media?playlist_id=<?php echo $playlist_id;?>&area_id=<?php echo $area->id;?>&media_type=<?php echo $area->area_type;?>&type=transition&width=600&height=300" class="thickbox" title="<?php echo lang('edit.media.config');?>" ><img src="/images/icons/16-03.gif" width="16" height="16" title="<?php echo lang('edit');?>" /></a>
							<?php
							}
							?>
			            </th>

						<th width="80"><?php echo lang('order');?></th>
						<th width="80"><?php echo lang('move.to');?></th>
						<!--
						<?php if($playlist_one->pls_type !=1 && $portrait && ($area->area_type == $this->config->item('area_type_movie') || $area->area_type == $this->config->item('area_type_image'))):?>
						<th width="80"><?php echo lang('rotate');?></th>
						<?php endif;?>
						-->
						<?php
						if($area->area_type == $this->config->item('area_type_movie') || $area->area_type == $this->config->item('area_type_image')) {
						?>
			            <th>
			            	<input type="checkbox" onchange="interactionpls.chooseAreaAllExclude(this,<?php echo $area->id;?>, <?php echo $playlist_id;?>);" value="0" />
							<?php echo lang('playlist.area_forget');?>
			            </th>
			            <?php
						}
			            ?>
			            <th width="80">Reload</th>
			            <th width="80"><?php echo lang('operate');?></th>
			        </tr>
				</thead>
	        	<tbody>
		        	<?php if($media['total'] == 0): ?>
					<tr>
						<td <?php if($portrait && $area->area_type == $this->config->item('area_type_movie')):?>colspan="13"<?php else:?>colspan="12"<?php endif;?>>
							<?php echo lang("empty");?>
						</td>
					</tr>
					<?php else:
				  		$index = 0;
				  		$total = count($media['data']);
				  		foreach($media['data'] as $row):
				  	?>
					<tr <?php if($index%2 != 0):?>class="even"<?php endif;?>>
						<td><?php echo $index + 1;?></td>
						<td><input type="checkbox" name="id" value="<?php echo $row->id;?>" /></td>
						<td>
					  	<?php if($row->media_type == $this->config->item('media_type_image')):?>
							<img src="/images/icons/16-07.gif" alt="" width="16" height="16" />						
							<?php else:?>
							<img src="/images/icons/16-12.gif" alt="" width="16" height="16" />
							<?php endif;?>
							<?php if($row->preview_status > 0 && $area->area_type != $this->config->item('area_type_bg')):?>
							<a href="/media/preview?id=<?php echo $row->media_id;?>&action=play&width=800&height=400" class="thickbox" title="<?php echo lang('preview');?>">
								<?php
									if($row->ext=='png' || $row->ext=='bmp') {
								?>
								<img alt="<?php echo $row->main_url;?>" src="<?php echo $row->main_url;?>" width="32" height="24" />
								<?php
									}else {
								?>
								<img alt="<?php echo $row->main_url;?>" src="<?php echo $row->tiny_url;?>" width="32" height="24" />
								<?php
									}
								?>
							</a>
						<?php else:?>
							<a class="thickbox" href="<?php if(file_exists($row->publish_url)) {echo $row->publish_url.'?t='.time();}else {echo $row->main_url.'?t='.time();}?>">
			  					<img alt="<?php echo $row->main_url;?>" src="<?php echo $row->tiny_url;?>" width="32" height="24" />
			  				</a>
						<?php endif;?>
					  	</td>
					  	<td><?php echo $row->name;?></td>
					  	<td>
						  	<?php if($row->media_type == $this->config->item('media_type_image')):?>
						  	<?php
								if($row->duration == '00:00') {
									echo '--';
								}else {
									echo $row->duration;
								}
							?>
							<?php else:?>
							--
							<?php endif;?>
						</td>
					  	<td>
						  	<?php if($row->transmode == -1 || $row->media_type == $this->config->item('media_type_video')): ?>
							--
							<?php else:?>
								<img  src="/images/transfer/Transfer_Mode_<?php if($row->transmode>9){echo $row->transmode;}else{echo '0'.$row->transmode;} ?>.png" width="32" height="24" title="" />
							<?php endif;?>
					  	</td>

					  <td>
					  	<?php if($total > 1):?>
						  	<?php if($index == 0 && $total > 1):?>
								<img src="/images/icons/dir-blank.gif" alt=""/>&nbsp;
								<img src="/images/icons/arrow_down.png" cid="<?php echo $row->id;?>" nid="<?php echo $media['data'][$index + 1]->id;?>" class="move down" alt="" title="<?php echo lang('move.down');?>">
							<?php elseif($index == $total - 1):?>
								<img src="/images/icons/arrow_up.png" cid="<?php echo $row->id;?>" pid="<?php echo $media['data'][$index - 1]->id;?>" class="move up" alt="" title="<?php echo lang('move.up');?>">&nbsp;
								<img src="/images/icons/dir-blank.gif" alt=""/>
							<?php else:?>
								<img src="/images/icons/arrow_up.png" cid="<?php echo $row->id;?>" pid="<?php echo $media['data'][$index - 1]->id;?>" class="move up" alt="" title="<?php echo lang('move.up');?>">&nbsp;
								<img src="/images/icons/arrow_down.png" cid="<?php echo $row->id;?>" nid="<?php echo $media['data'][$index + 1]->id;?>" class="move down" alt="" title="<?php echo lang('move.down');?>">
							<?php endif;?>
						<?php endif;?>
					  </td>
					  <td>
					  	<?php if($total > 1):?>
					  		<input type="text" size="2" onchange="interactionpls.moveTo(this, <?php echo $area_id.','.$row->id.','.$total.',\''.lang('warn.number').'\',\''.lang('warn.outbound').'\'';?>)" value="<?php echo $row->position;?>" position="<?php echo $row->position;?>"/>
						<?php endif;?>
					  </td>
					  <!--
					  <?php if($playlist_one->pls_type !=1 && $portrait && ($area->area_type == $this->config->item('area_type_movie') || $area->area_type == $this->config->item('area_type_image'))):?>
					  <td>
					  	<input type="checkbox" name="rotate" <?php if($row->rotate):?>checked="checked"<?php endif;?>  onclick="playlist.rotateMedia(this, <?php echo $row->id;?>);" />
					  </td>
					  <?php endif;?>
					  -->
					  <?php
						if($area->area_type == $this->config->item('area_type_movie') || $area->area_type == $this->config->item('area_type_image')) {
					  ?>
					  <td>
					  	 <input type="checkbox" name="status" id="status" value="<?php echo $row->status;?>" onchange="interactionpls.editStatus(this, <?php echo $area_id.','.$row->id;?>)" <?php if($row->status) {echo 'checked="checked"';} ?> />
					  </td>
					  <?php };?>
					  <td>
					  <?php if($row->source):?>
					  	 <input type="checkbox" name="reload" id="reload" value="<?php echo $row->reload;?>" onchange="interactionpls.editReload(this, <?php echo $area_id.','.$row->id;?>)" <?php if($row->reload) {echo 'checked="checked"';} ?> />
					  <?php endif;?>
					  </td>
					  <td>
					  <?php if(($area->area_type == $this->config->item('area_type_movie') || $area->area_type == $this->config->item('area_type_image')) && $row->media_type == $this->config->item('media_type_image')):?>
					  	<a href="/interactionpls/edit_playlist_media?playlist_id=<?php echo $playlist_id;?>&area_id=<?php echo $area_id;?>&id=<?php echo $row->id;?>&width=600&height=360" class="thickbox" title="<?php echo lang('edit.media.config');?>" ><img src="/images/icons/16-03.gif" width="16" height="16" title="<?php echo lang('edit');?>" /></a>
					  <?php else:?>
					  	<img src="/images/icons/blank.gif">
					  <?php endif;?>
					  <?php if($area->area_type != $this->config->item('area_type_bg')):?>
					  	<a href="javascript:void(0);" onclick="interactionpls.removeAreaMedia(<?php echo $playlist_id;?>,<?php echo $area_id;?>,<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>', <?php echo $screen_id;?>);"><img src="/images/icons/16-04.gif" width="16" height="16" title="<?php echo lang('delete');?>" /></a>
					  <?php endif;?>
					  </td>
					</tr>
					<?php
					 	$index++; 
						endforeach; 
						endif;
					?>
				</tbody>
			</table>
    	</td>
  	</tr>
  <?php elseif($area->area_type == $this->config->item('area_type_text')):
  ?>
	<tr>
  		<td>
  			<div>
				<table class="gray-area from-panel" style="width:100%" border="0">
					<tr>
						<td style="vertical-align:middle;" width="60" ><?php echo lang('rss');?></td>
						<td colspan="9">
							<textarea style="width:100%; height:60px; <?php if($area->setting->direction==2) {echo 'direction:rtl;unicode-bidi:embed;';}?>;" id="ticker" name="ticker" <?php if($media['total'] > 0): ?>readonly="readonly"<?php endif;?> ><?php if($after_media != 2):?><?php echo $area->setting->content;?><?php endif;?></textarea>
						</td>
					</tr>
					<tr>
						<td><?php echo lang('color');?></td>
						<td>
							<div id="colorSelector" class="color"><div style="background-color: <?php echo $area->setting->color;?>"></div></div>
							<input type="hidden" id="color" name="color" value="<?php echo $area->setting->color;?>" />
						</td>
						<td width="60" ><?php echo lang('bg.color');?></td>
						<td>
							<div id="bgColorSelector" class="color"><div style="background-color: <?php echo $area->setting->bg_color;?>"></div></div>
							<input type="hidden" id="bgColor" name="bgColor" value="<?php echo $area->setting->bg_color;?>" />
						</td>
						<td width="60" ><?php echo lang('direction');?></td>
						<td>
							<select id="direction" name="direction" onchange="interactionpls.font_direction(this);">
							<?php foreach($directions as $k => $v):?>
								<option value="<?php echo $k?>" <?php if($k == $area->setting->direction){echo 'selected="selected"';}?>><?php echo $v?></option>
							<?php endforeach;?>
							</select>
						</td>
						<td width="80" ><?php echo lang('font.size');?></td>
						<td>
							<select id="fontSize" name="font_size" >
							<?php foreach($font_sizes as $k => $v):?>
								<option value="<?php echo $k?>" <?php if($k == $area->setting->font_size){echo 'selected="selected"';}?>><?php echo $v?></option>
							<?php endforeach;?>
							</select>
						</td>
						<?php
						if(isset($rss->type) && $rss->type > 0) {
						?>
						<input type="hidden" id="rssFormat" name="rssFormat" value="0" />
						<?php
						}else {
						?>
						<td width="100" id="formatLabel" <?php if($media['total'] == 0): ?>style="display:none;"<?php endif;?>><?php echo lang('rss.format');?></td>
						<td id="formatOption" <?php if($media['total'] == 0): ?>style="display:none;"<?php endif;?>>
							<select id="rssFormat" onchange="interactionpls.changeRssFormat(this,<?php echo $playlist_id;?>, <?php echo $area->id;?>);">
								<option value="0" <?php if(0 == $area->setting->rss_format){echo 'selected="selected"';}?> ><?php echo lang('rss.title');?></option>
								<option value="1" <?php if(1 == $area->setting->rss_format){echo 'selected="selected"';}?> ><?php echo lang('rss.detail');?></option>
								<option value="2" <?php if(2 == $area->setting->rss_format){echo 'selected="selected"';}?> ><?php echo lang('rss.title').'&nbsp;&amp;&nbsp;'.lang('rss.detail');?></option>
							</select>
						</td>
						<?php
						}?>
					</tr>
					<tr>
						<td><?php echo lang('text.speed');?></td>
						<td>
							<select id="speed" name="speed">
							<?php foreach($speeds as $k => $v):?>
								<option value="<?php echo $k?>" <?php if($k == $area->setting->speed){echo 'selected="selected"';}?>><?php echo $v?></option>
							<?php endforeach;?>
							</select>
						</td>
						<td><?php echo lang('text.transparent');?></td>
						<td>
							<select id="text_transparent" name="text_transparent">
							<?php foreach($transparents as $k => $v):?>
								<option value="<?php echo $k?>" <?php if($k == $area->setting->transparent){echo 'selected="selected"';}?>><?php echo $v?></option>
							<?php endforeach;?>
							</select>
						</td>
						<?php
						if($this->config->item('text.font_family')):
						?>
						<td><?php echo lang('font.font');?></td>
						<td>
							<select id="font_font" name="font_font" >
							<?php foreach($font_font as $k => $v):?>
								<option value="<?php echo $k?>" <?php if($k == $area->setting->font){echo 'selected="selected"';}?>><?php echo $v?></option>
							<?php endforeach;?>
							</select>
						</td>
						<td colspan="4">
						<?php
						else:
						?>
						<input type="hidden" id="font_font" name="font_font" value="0" />
						<td colspan="6">
						<?php
						endif;
						?>
							Separator&nbsp;&nbsp;&nbsp;<input type="text" name="rss_delimiter" id="rss_delimiter" value="<?php echo $rss_delimiter;?>" onchange="interactionpls.updateRssFlag(this,<?php echo $area->id;?>, <?php echo $playlist_id;?>);" size="8">
							&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);"><img src="/images/icons/rss.gif" width="16" height="16" title="<?php echo lang('rss');?>" onclick="interactionpls.addAreaMedia(<?php echo $playlist_id;?>, <?php echo $area->id;?>, <?php echo $area->area_type;?>, <?php echo $this->config->item('media_type_rss');?>,1,'<?php echo lang('rss.library')?>', <?php echo $screen_id;?>);" /></a>
							<?php if($media['total'] > 0):?>
								<span><?php echo $media['data'][0]->name;?></span>
								<input type="hidden" id="rssId" name="rssId" value="<?php echo $rss->id;?>" />
								<a href="javascript:void(0);"><img src="/images/icons/16-04.gif" width="16" height="16" title="<?php echo lang('delete');?>" onclick="interactionpls.removeAreaMedia(<?php echo $playlist_id;?>,<?php echo $area_id;?>,<?php echo $rss->id;?>,'<?php echo lang('tip.remove.item');?>', <?php echo $screen_id;?>);" <?php if($media['total'] == 0): ?>style="display:none;"<?php endif;?> /></a>
							<?php endif;?>
						</td>
					</tr>
				</table>
				<input type="hidden" id="font_font" name="font_font" value="0" />
				<input type="hidden" id="textId" name="textId" value="<?php echo $area->setting->id;?>" />
				<input type="hidden" id="textAreaId" name="textAreaId" value="<?php echo $area->id;?>" />
			</div>
			<script type="text/javascript">
				interactionpls.initTextArea();
			</script>
  		</td>
 	</tr>
  	<?php elseif($area->area_type == $this->config->item('area_type_staticText')):
  	?>
  	<tr>
  		<td>
  			<div>
				<table class="gray-area from-panel" style="width:100%" border="0">
					<tr>
						<td style="vertical-align:middle;" width="60" >StaticText</td>
						<td colspan="15">
							<?php
							$text_algin= 'left';
							$bg_color = '#000000';
							if($area->setting->position == 2) {
								$text_algin = 'center';
							}
							if($area->setting->position == 3) {
								$text_algin = 'right';
							}
							if($area->setting->transparent == 1) {
								$bg_color = $area->setting->bg_color;
							}
							$html_content = str_replace('<br/>', '&#13;&#10', $area->setting->html_content);
							$html_content = str_replace('&#039', '\'', $html_content);
							?>
							<textarea style="resize: none; overflow: hidden; background-color:<?php echo $bg_color;?>; text-align: <?php echo $text_algin;?>; width:<?php echo 2*$area->w;?>px; height:<?php echo 2*$area->h;?>px; font-size:<?php echo $area->setting->font_size;?>px; font-family:'<?php echo $area->setting->font_family;?>'; color:<?php echo $area->setting->color;?>; text-decoration:<?php if($area->setting->underline){echo 'underline';}else {echo 'none';}?>; font-weight:<?php if($area->setting->bold){echo 'bold';}else {echo 'normal';}?>; font-style:<?php if($area->setting->italic){echo 'italic';}else {echo 'normal';}?>" id="static_ticker" name="ticker" ><?php echo $html_content;?></textarea>
						</td>
					</tr>
					<tr>
						<td width="60"></td>
						<td width="20" style="text-align:right;"><?php echo lang('color');?></td>
						<td width="70" style="text-align:left;">
							<div id="STextcolorSelector" class="color">
								<div style="background-color: <?php echo $area->setting->color;?>">
								</div>
							</div>
							<input type="hidden" id="static_Color" name="color" value="<?php echo $area->setting->color;?>" />
						</td>
						<td width="30" style="text-align:right;"><?php echo lang('bg.color');?></td>
						<td width="70">
							<div id="STextbgColorSelector" class="color"><div style="background-color: <?php echo $area->setting->bg_color;?>"></div></div>
							<input type="hidden" id="static_BgColor" name="bgColor" value="<?php echo $area->setting->bg_color;?>" />
						</td>
						<!--
						<td width="90"><input type="checkbox" name="transparent" id="transparent" <?php if($area->setting->transparent == 2){echo 'checked="checked"';}?>onchange="playlist.sfont_transparent(this,<?php echo $playlist_id;?>, <?php echo $area->id;?>);">transparent</td>
						-->
						<td width="50" style="text-align:right;"><?php echo lang('font.align');?></td>
						<td width="120">
							<select id="sfont_position" name="sfont_position" onchange="interactionpls.font_position(this,<?php echo $playlist_id;?>, <?php echo $area->id;?>);">
							<?php foreach($font_position as $k => $v):?>
								<option value="<?php echo $k?>" <?php if($k == $area->setting->position){echo 'selected="selected"';}?>><?php echo $v?></option>
							<?php endforeach;?>
							</select>
						</td>
						<td width="30" style="text-align: right;"><?php echo lang('font.set');?></td>
						<td width="150">
							<select id="sfont_family" name="sfont_family" onchange="interactionpls.font_family(this,<?php echo $playlist_id;?>, <?php echo $area->id;?>);">
							<?php foreach($font_familys as $k => $v):?>
								<option value="<?php echo $v?>" <?php if($v == $area->setting->font_family){echo 'selected="selected"';}?>><?php echo $v?></option>
							<?php endforeach;?>
							</select>
						</td>
						<td width="30" style="text-align:right;"><?php echo lang('font.size');?></td>
						<td width="120">
							<select id="sfont_size" name="sfont_size" onchange="interactionpls.font_size(this,<?php echo $playlist_id;?>, <?php echo $area->id;?>);">
							<?php for($i = 10; $i <= 100; $i++) {?>
								<option value="<?php echo $i?>" <?php if($i == $area->setting->font_size){echo 'selected="selected"';}?>><?php echo $i?></option>
							<?php }?>
							</select>
						</td>
						<td width="90"><input type="checkbox" name="sfont_underline" id="sfont_underline" <?php if($area->setting->underline == 1){echo 'checked="checked"';}?>onchange="interactionpls.font_underline(this,<?php echo $playlist_id;?>, <?php echo $area->id;?>);"><?php echo lang('font.u');?></td>
						<td width="50"><input type="checkbox" name="sfont_blod" id="sfont_blod" <?php if($area->setting->bold == 1){echo 'checked="checked"';}?> onchange="interactionpls.font_bold(this,<?php echo $playlist_id;?>, <?php echo $area->id;?>);"><?php echo lang('font.b');?></td>
						<td width="50"><input type="checkbox" name="sfont_italic" id="sfont_italic" <?php if($area->setting->italic == 1){echo 'checked="checked"';}?> onchange="interactionpls.font_italic(this,<?php echo $playlist_id;?>, <?php echo $area->id;?>);"><?php echo lang('font.i');?></td>
						<td></td>
					</tr>
				</table>
				<input type="hidden" id="static_textId" name="textId" value="<?php echo $area->setting->id;?>" />
				<input type="hidden" id="static_textAreaId" name="textAreaId" value="<?php echo $area->id;?>" />
				<span id="testSpanForCheck" style="visibility:hidden;width:<?php echo 2*$area->w;?>px; height:<?php echo 2*$area->h;?>px;">
			</div>
			<script type="text/javascript">
				interactionpls.initStaticTextArea();
			</script>
  		</td>
	</tr>
  	<?php elseif($area->area_type == $this->config->item('area_type_date')):?>
	<tr>
  		<td>
	  		<table>
	  			<tr>
					<td height="40">
						<?php echo lang('font.size');?>
					</td>
					<td>
						<select id="dateFontSize" name="fontSize" style="width: 150px;" onchange="interactionpls.dateChange();">
							<?php foreach($font_sizes as $v):?>
								<option value="<?php echo $v;?>" <?php if($v==$font_size):?>selected<?php endif;?> ><?php echo $v;?></option>
							<?php endforeach;?>
						</select>
					</td>
				</tr>
				<tr>
					<td height="40">
						<?php echo lang('color');?>
					</td>
					<td>
						<input type="hidden" id="dateColor" name="color" value="<?php echo $color;?>" />
						<input type="hidden" id="dateId"  value="<?php echo $sid;?>" />
						<div id="dateColorSelector" class="color"><div style="background-color: <?php echo $color;?>"></div></div>
					</td>
				</tr>
				<tr>
					<td height="40">
						<?php echo lang('bg.color');?>
					</td>
					<td>
						<input type="hidden" id="dateBgColor" name="color" value="<?php echo $bg_color;?>" />
						<div id="dateBgColorSelector" class="color"><div style="background-color: <?php echo $bg_color;?>"></div></div>
					</td>
				</tr>
				<tr>
					<td height="40">
						Style
					</td>
					<td>
						<select id="dataStyle" name="dataStyle" style="width: 170px;" onchange="interactionpls.dateChange();">
							<option value="1" <?php if($style == 1):?>selected<?php endif;?>>mm/dd/yyyy weekday</option>
							<option value="2" <?php if($style == 2):?>selected<?php endif;?>>dd/mm/yyyy weekday</option>
							<option value="3" <?php if($style == 3):?>selected<?php endif;?>>yyyy/mm/dd weekday</option>
							<option value="4" <?php if($style == 4):?>selected<?php endif;?>>mm/dd/yyyy</option>
							<option value="5" <?php if($style == 5):?>selected<?php endif;?>>dd/mm/yyyy</option>
							<option value="6" <?php if($style == 6):?>selected<?php endif;?>>yyyy/mm/dd</option>
						</select>
						<font>
						<?php 
						$week = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
						?>
						</font>
					</td>
				</tr>
				<tr>
					<td height="40"><?php echo lang('text.transparent');?></td>
					<td>
						<select id="dtransparent" name="dtransparent" onchange="interactionpls.dateChange();">
							<?php foreach($transparents as $k => $v):?>
							<option value="<?php echo $k?>" <?php if($k == $transparent){echo 'selected="selected"';}?>><?php echo $v?></option>
							<?php endforeach;?>
						</select>
					</td>
				</tr>
				<tr>
					<td height="20">&nbsp;</td><td></td>
				</tr>
				<tr>
					<td>Preview</td>
					<?php
					$red   = intval('0x'.substr($bg_color, 1, 2), 16);
					$green = intval('0x'.substr($bg_color, 3, 2), 16);
					$blue  = intval('0x'.substr($bg_color, 5, 2), 16);
					?>
					<td id="datePreview" style="border: solid #000 1px;text-align:center;background-color:rgba(<?php echo $red;?>,<?php echo $green;?>,<?php echo $blue;?>,<?php echo 1-$transparent/100;?>); width:<?php echo 2*$area->w+20;?>px; height:<?php echo 2*$area->h;?>px;">
						<font style="font-size:<?php echo $font_size;?>px; color: <?php echo $color;?>">
							<?php
							$arr_week = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
							switch($style) {
								case 1:
									echo date("n/j/Y").'&nbsp;'.$arr_week[date('w')];
								break;
								case 2:
									echo date("j/n/Y").'&nbsp;'.$arr_week[date('w')];
								break;
								case 3:
									echo date("Y/n/j").'&nbsp;'.$arr_week[date('w')];
								break;
								case 4:
									echo date("n/j/Y");
								break;
								case 5:
									echo date("j/n/Y");
								break;
								case 6:
									echo date("Y/n/j");
								break;
							}
							?>
						</font>
					</td>
				</tr>
				<?php
				if(TRUE):
				?>
				<tr>
					<td height="40">
						<?php echo lang('language');?>
					</td>
					<td>
						<select id="dateLanguage" name="dateLanguage" style="width: 150px;">
							<option value="0" <?php if(0==$language):?>selected<?php endif;?> >English</option>
							<option value="1" <?php if(1==$language):?>selected<?php endif;?> >Español</option>
						</select>
					</td>
				</tr>
				<?php
				else:
				?>
				<input type="hidden" value="0" id="dateLanguage" name="dateLanguage" />
				<?php
				endif;
				?>
  			</table>
			<script type="text/javascript">
				interactionpls.initDateArea();
			</script>
  		</td>
	</tr>
	<?php elseif($area->area_type == $this->config->item('area_type_weather')):?>
	<tr>
  		<td>
  			<table>
	  			<tr>
					<td height="40">
						<?php echo lang('font.size');?>
					</td>
					<td>
						<select id="weatherFontSize" name="fontSize" style="width: 150px;">
							<?php foreach($font_sizes as $v):?>
								<option value="<?php echo $v;?>" <?php if($v==$font_size):?>selected<?php endif;?>><?php echo $v;?></option>
							<?php endforeach;?>
						</select>
					</td>
				</tr>
				<tr>
					<td height="40">
						<?php echo lang('color');?>
					</td>
					<td>
						<input type="hidden" id="weatherColor" name="color" value="<?php echo $color;?>" />
						<input type="hidden" id="weatherId"  value="<?php echo $sid;?>" />
						<div id="weatherColorSelector" class="color"><div style="background-color: <?php echo $color;?>"></div></div>
					</td>
				</tr>
				<tr>
					<td height="40">
						<?php echo lang('bg.color');?>
					</td>
					<td>
						<input type="hidden" id="weatherBgColor" name="color" value="<?php echo $bg_color;?>" />
						<div id="weatherBgColorSelector" class="color"><div style="background-color: <?php echo $bg_color;?>"></div></div>
					</td>
				</tr>
				<tr>
					<td height="40">
						Style
					</td>
					<td>
						<select id="weatherStyle" name="weatherStyle" onchange="interactionpls.wImage(this,<?php echo $area->id;?>);" style="width: 160px;">
							<option value="5" <?php if($style == 5):?>selected<?php endif;?>>Style 1 (Today)</option>
							<option value="4" <?php if($style == 4):?>selected<?php endif;?>>Style 2 (3 days)</option>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<td id="wImage" style="display: none;">
						<?php if($style == 5) {?>
							<img src="/images/wstyle5.jpg" />
						<?php }else { ?>
							<img src="/images/wstyle4.jpg" />
						<?php
						}?>
					</td>
				</tr>
				<tr>
					<td height="40"><?php echo lang('text.transparent');?></td>
					<td>
						<select id="wtransparent" name="wtransparent">
							<?php foreach($transparents as $k => $v):?>
							<option value="<?php echo $k?>" <?php if($k == $transparent){echo 'selected="selected"';}?>><?php echo $v?></option>
							<?php endforeach;?>
						</select>
					</td>
				</tr>
				<?php
				if(TRUE):
				?>
				<tr>
					<td height="40">
						<?php echo lang('language');?>
					</td>
					<td>
						<select id="weatherLanguage" name="weatherLanguage" style="width: 150px;">
							<option value="0" <?php if(0==$language):?>selected<?php endif;?> >English</option>
							<option value="1" <?php if(1==$language):?>selected<?php endif;?> >Español</option>
						</select>
					</td>
				</tr>
				<?php
				else:
				?>
				<input type="hidden" id="weatherLanguage" name="weatherLanguage" value="0" />
				<?php
				endif;
				?>
	  		</table>
			<script type="text/javascript">
				interactionpls.initWeatherArea();
			</script>
  		</td>
  	</tr>
	<?php elseif($area->area_type == $this->config->item('area_type_webpage')):?>
	<tr class= "table_webpage_list">
		<td>
			<table width="100%" class="table-list table_webpage">
				<thead>
	    			<tr>
	    				<th width="8">&nbsp;</th>
			            <th width="50"><input type="checkbox" onchange="interactionpls.chooseAreaAllMedia(this,<?php echo $area->id;?>);" value="0" /></th>
			            <th>URL</th>
			            <th>Play Time(HH:MM)</th>
			            <th>Update Frequency</th>
			            <th>Move To</th>
			            <th>Exclude</th>
			            <th width="80"><?php echo lang('operate');?></th>
			        </tr>
	    		</thead>
		        <tbody>
		        	<?php if($media['total'] == 0): ?>
					<tr>
						<td <?php if($portrait && $area->area_type == $this->config->item('area_type_movie')):?>colspan="13"<?php else:?>colspan="12"<?php endif;?>>
							<?php echo lang("empty");?>
						</td>
					</tr>
					<?php else:
					  $index = 0;
					  $total = count($media['data']);
					?>
					<?php foreach($media['data'] as $row):?>
						<tr <?php if($index%2 != 0):?>class="even"<?php endif;?>>
						  <td><?php echo $index + 1;?></td>
						  <td><input type="checkbox" name="id" value="<?php echo $row->id;?>" /></td>
						  <td><?php echo $row->publish_url;?></td>
						  <td>
						  	<?php
						  	$stmp = explode(':', $row->duration);
						  	$sh = $stmp[0];
						  	$sm = $stmp[1];
						  	?>
						  	<select onchange="interactionpls.editPlayTime_h(this, <?php echo $area_id.','.$row->id;?>)" name="playTimeh" id="<?php echo $row->id;?>_playTimeh" >
						  		<?php for($i = 0; $i < 24; $i++):?>
								<?php if($i < 10){$hour = '0'.$i;}else{$hour = $i;}?>
								<option value="<?php echo $hour;?>" <?php if($sh == $hour):?>selected="selected"<?php endif;?>><?php echo $hour;?></option>
								<?php endfor;?>
						    </select>
						    :
						    <select onchange="interactionpls.editPlayTime_m(this, <?php echo $area_id.','.$row->id;?>)" name="playTimem" id="<?php echo $row->id;?>_playTimem" >
						  		<?php for($i = 0; $i < 60; $i++):?>
								<?php if($i < 10){$minute = '0'.$i;}else{$minute = $i;}?>
								<option value="<?php echo $minute;?>" <?php if($sm == $minute):?>selected="selected"<?php endif;?>><?php echo $minute;?></option>
								<?php endfor;?>
						    </select>
						  </td>
						  <td>
							<?php echo $row->updateF;?>
						  </td>
						  <td>
						  	<?php if($total > 1):?>
						  		<input type="text" size="2" onchange="interactionpls.moveTo(this, <?php echo $area_id.','.$row->id.','.$total.',\''.lang('warn.number').'\',\''.lang('warn.outbound').'\'';?>)" value="<?php echo $row->position;?>" position="<?php echo $row->position;?>"/>
							<?php endif;?>
						  </td>
						  <td>
						  	 <input type="checkbox" name="status" id="status" value="<?php echo $row->status;?>" onchange="interactionpls.editStatus(this, <?php echo $area_id.','.$row->id;?>)" <?php if($row->status) {echo 'checked="checked"';} ?> />
						  </td>
						  <td>
						  	<a href="/interactionpls/edit_playlist_media_webpage?playlist_id=<?php echo $playlist_id;?>&area_id=<?php echo $area_id;?>&id=<?php echo $row->id;?>&width=430&height=220" class="thickbox" title="<?php echo lang('edit.webpage.config');?>" ><img src="/images/icons/16-03.gif" width="16" height="16" title="<?php echo lang('edit');?>" /></a>
						  	&nbsp;&nbsp;<a href="javascript:void(0);" onclick="interactionpls.removeAreaMedia(<?php echo $playlist_id;?>,<?php echo $area_id;?>,<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>', <?php echo $screen_id;?>);"><img src="/images/icons/16-04.gif" width="16" height="16" title="<?php echo lang('delete');?>" /></a>
						  </td>
						  <input type="hidden" id="webpageDate" name="webpageDate" value="true"/>
						</tr>
						<?php
						 	$index++; 
							endforeach; 
						?>
					<?php endif;?>
				</tbody>
			</table>
  		</td>
	</tr>
	<tr class="table_webpage_grid" style="display: none;">
  		<td>
	  		<table width="100%">
	    		<thead>
				<tr>
					<td>
						<div class="video-panel" style="max-width:900px;">
							<ul>
								<?php if($media['total'] == 0): ?>
								<tr>
									<td <?php if($portrait && $area->area_type == $this->config->item('area_type_movie')):?>colspan="13"<?php else:?>colspan="12"<?php endif;?>>
										<?php echo lang("empty");?>
									</td>
								</tr>
								<?php else:
								  $index = 0;
								  $total = count($media['data']);
								?>
								<?php foreach($media['data'] as $row):?>
								<input type="hidden" id="webpageDate" name="webpageDate" value="true"/>
								<li>
									<table width="300" border="0" cellspacing="0" cellpadding="0">
						              <tr>
						                <td valign="top" class="content">
						                	<div class="pic">
												<a href=""></a><img src="/images/media/video.gif" width="120" height="90" />
						                    </div>
						                </td>
						              </tr>
						            </table>
								</li>
								<?php endforeach;
								endif;?>
							</ul>
						</div>
					</td>
				</tr>
				</thead>
			</table>
			<script type="text/javascript">
				interactionpls.initFormDate();
			</script>
		</td>
	</tr>
  	<?php elseif($area->area_type == $this->config->item('area_type_time')):?>
	<tr>
		<td>
	  		<table>
	  			<tr>
					<td height="40">
						<?php echo lang('font.size');?>
					</td>
					<td>
						<select id="timeFontSize" name="fontSize" style="width: 150px;" onchange="interactionpls.timeChange();">
							<?php foreach($font_sizes as $v):?>
								<option value="<?php echo $v;?>" <?php if($v==$font_size):?>selected<?php endif;?> ><?php echo $v;?></option>
							<?php endforeach;?>
						</select>
					</td>
				</tr>
				<tr>
					<td height="40">
						<?php echo lang('color');?>
					</td>
					<td>
						<input type="hidden" id="timeColor" name="color" value="<?php echo $color;?>" />
						<input type="hidden" id="timeId"  value="<?php echo $sid;?>" />
						<div id="timeColorSelector" class="color"><div style="background-color: <?php echo $color;?>"></div></div>
					</td>
				</tr>
				<tr>
					<td height="40">
						<?php echo lang('bg.color');?>
					</td>
					<td>
						<input type="hidden" id="timeBgColor" name="color" value="<?php echo $bg_color;?>" />
						<div id="timeBgColorSelector" class="color"><div style="background-color: <?php echo $bg_color;?>"></div></div>
						<input type="hidden" id="timeLanguage" name="timeLanguage" value="0" />
					</td>
				</tr>
				<tr>
					<td height="40">Style</td>
					<td>
						<select id="timeStyle" name="timeStyle" style="width: 120px;" onchange="interactionpls.timeChange();">
							<option value="1" <?php if($style == 1):?>selected<?php endif;?>>HH:MM</option>
							<option value="2" <?php if($style == 2):?>selected<?php endif;?>>HH:MM PM/AM</option>
						</select>
					</td>
				</tr>
				<tr>
					<td height="40"><?php echo lang('text.transparent');?></td>
					<td>
						<select id="ttransparent" name="ttransparent" onchange="interactionpls.timeChange();">
							<?php foreach($transparents as $k => $v):?>
							<option value="<?php echo $k?>" <?php if($k == $transparent){echo 'selected="selected"';}?>><?php echo $v?></option>
							<?php endforeach;?>
						</select>
					</td>
				</tr>
				<tr>
					<td height="20">&nbsp;</td>
					<td></td>
				</tr>
				<tr>
					<td>Preview</td>
					<?php
					$red   = intval('0x'.substr($bg_color, 1, 2), 16);
					$green = intval('0x'.substr($bg_color, 3, 2), 16);
					$blue  = intval('0x'.substr($bg_color, 5, 2), 16);
					?>
					<td id="timePreview" style="border: solid #000 1px;text-align:center;background-color:rgba(<?php echo $red;?>,<?php echo $green;?>,<?php echo $blue;?>,<?php echo 1-$transparent/100;?>);width:<?php echo 2*$area->w+20;?>px; height:<?php echo 2*$area->h;?>px;">
						<font style="position:relative;font-size:<?php echo $font_size;?>px; color: <?php echo $color;?>;">
						<?php
						switch($style) {
							case 1:
								//echo date("H:i");
								echo preg_replace('/^0+/','',date("H")).':'.date("i");
							break;
							case 2:
								if(date("H")>12) {
									echo (date("H")-12).':'.date("i").' PM';
								}else {
									//echo date("H:i").' AM';
									echo preg_replace('/^0+/','',date("H")).':'.date("i").' AM';
								}
							break;
						}
						?>
						</font>
					</td>
				</tr>
	  		</table>
			<script type="text/javascript">
				interactionpls.initTimeArea();
			</script>
		</td>
	</tr>
  	<?php elseif($area->area_type == $this->config->item('area_type_btn')):?>
  	<tr>
  		<td>
  			<table>
				<tr>
					<td width="150px" height="40"><?php echo lang('btn.action');?>:</td>
					<td>
						<select id="<?php echo $area->id;?>_btnAction" name="btnAction" onchange="interactionpls.changeBtnAction(this, <?php echo $area->id;?>, <?php echo $playlist_id;?>);">
							<?php for($i = 1; $i <= count($btn_action_list); $i++):?>
							<option value="<?php echo $i;?>" <?php if($i == $action):?>selected="selected"<?php endif;?>><?php echo $btn_action_list[$i];?></option>
							<?php endfor;?>
						</select>
						<input type="hidden" id="<?php echo $area->id;?>_sid" name="sid" value="<?php echo $sid;?>"/>
					</td>
				</tr>
				<tr <?php if($action != 3):?>style="display: none;"<?php endif;?> id="<?php echo $area->id;?>_target_3">
					<td width="150px" height="40"><?php echo lang('btn.target');?>:</td>
					<td>
						<input type="text" id="<?php echo $area->id;?>_btnTarget_3" name="btnTarget" style="width:180px;" value="<?php if(!is_numeric($goal)) {echo $goal;}?>"/>
					</td>
				</tr>
				<tr <?php if($action != 1):?>style="display: none;"<?php endif;?> id="<?php echo $area->id;?>_target_1">
					<td width="150px" height="40"><?php echo lang('btn.target');?>:</td>
					<td>
						<?php
						if(!empty($pages_list)):
						?>
						<select id="<?php echo $area->id;?>_btnTarget_1" name="btnTarget">
							<?php 
							foreach($pages_list as $page_list):
							?>
							<option value="<?php echo $page_list['id'];?>" <?php if($page_list['id'] == $goal):?>selected="selected"<?php endif;?>>&nbsp;<?php echo $page_list['name'];?></option>
							<?php
							endforeach;
							?>
						</select>
						<?php 
						else:
						?>
						<input type="text" id="<?php echo $area->id;?>_btnTarget" name="btnTarget" style="width:180px;" value="<?php echo $goal;?>"/>
						<?php
						endif;
						?>
					</td>
				</tr>
				<tr <?php if($action != 2):?>style="display: none;"<?php endif;?> id="<?php echo $area->id;?>_target_2">
					<td width="150px" height="40"><?php echo lang('btn.target');?>:</td>
					<td>
						<?php
						if(!empty($areas_list)):
						?>
						<select id="<?php echo $area->id;?>_btnTarget_2" name="btnTarget" onchange="interactionpls.changeTarget(this, <?php echo $area->id;?>, <?php echo $playlist_id;?>,<?php echo $screen_id;?>);">
							<?php 
							foreach($areas_list as $area_list):
							?>
							<option value="<?php echo $area_list['id'];?>" <?php if($area_list['id'] == $goal):?>selected="selected"<?php endif;?> areaType="<?php echo $area_list['type'];?>">&nbsp;<?php echo $area_list['name'];?> zone</option>
							<?php
							endforeach;
							?>
						</select>
						<?php 
						else:
						?>
						<input type="text" id="<?php echo $area->id;?>_btnTarget" name="btnTarget" style="width:180px;" value=""/>
						<?php
						endif;
						?>
					</td>
				</tr>
				<!--
				<tr>
					<td width="150px" height="40"><?php echo lang('btn.style');?>:</td>
					<td>
						<select id="<?php echo $area->id;?>_btnStyle" name="btnStyle">
							<?php for($i = 1; $i <= count($style_list); $i++):?>
							<option value="<?php echo $i;?>" <?php if($i == $style):?>selected="selected"<?php endif;?>><?php echo $style_list[$i];?></option>
							<?php endfor;?>
						</select>
					</td>
				</tr>
				-->
				<tr id="<?php echo $area->id;?>_Screen" <?php if($action ==1 || $action == 2):?>style="display: none;"<?php endif;?>>
					<td width="150px" height="40"><?php echo lang('btn.full.screen');?>:</td>
					<td>
						<select id="<?php echo $area->id;?>_btnScreen" name="btnScreen" onchange="interactionpls.changeScreen(this, <?php echo $area->id;?>);">
							<?php for($i = 1; $i <= count($screen_list); $i++):?>
							<option value="<?php echo $i;?>" <?php if($i == $fullScreen):?>selected="selected"<?php endif;?>><?php echo $screen_list[$i];?></option>
							<?php endfor;?>
						</select>
					</td>
				</tr>
				<tr id="<?php echo $area->id;?>_screenx" <?php if($action ==1 || $action == 2 || $fullScreen == 1):?>style="display: none;"<?php endif;?>>
					<td height="40">Left:</td>
					<td><input type="text" id="<?php echo $area->id;?>_x" name="screenx" style="width:100px;" value="<?php echo $x;?>"/> &nbsp;Px</td>
				</tr>
				<tr id="<?php echo $area->id;?>_screeny" <?php if($action ==1 || $action == 2 || $fullScreen == 1):?>style="display: none;"<?php endif;?>>
					<td height="40">Top:</td>
					<td><input type="text" id="<?php echo $area->id;?>_y" name="screeny" style="width:100px;" value="<?php echo $y;?>"/> &nbsp;Px</td>
				</tr>
				<tr id="<?php echo $area->id;?>_screenw" <?php if($action ==1 || $action == 2 || $fullScreen == 1):?>style="display: none;"<?php endif;?>>
					<td height="40">Width:</td>
					<td><input type="text" id="<?php echo $area->id;?>_w" name="screenw" style="width:100px;" value="<?php echo $w;?>"/> &nbsp;Px</td>
				</tr>
				<tr id="<?php echo $area->id;?>_screenh" <?php if($action ==1 || $action == 2 || $fullScreen == 1):?>style="display: none;"<?php endif;?>>
					<td height="40">Height</td>
					<td><input type="text" id="<?php echo $area->id;?>_h" name="screenh" style="width:100px;" value="<?php echo $h;?>"/> &nbsp;Px</td>
				</tr>
				<tr id="<?php echo $area->id;?>_Close" <?php if($action ==1 || $action == 2):?>style="display: none;"<?php endif;?>>
					<td width="150px" height="40"><?php echo lang('btn.close');?>:</td>
					<td>
						<select id="<?php echo $area->id;?>_btnClose" name="btnClose">
							<?php for($i = 1; $i <= count($close_list); $i++):?>
							<option value="<?php echo $i;?>" <?php if($i == $closeFlag):?>selected="selected"<?php endif;?>><?php echo $close_list[$i];?></option>
							<?php endfor;?>
						</select>
					</td>
				</tr>
				<tr id="<?php echo $area->id;?>_timeout" <?php if($action == 1 || $action == 2 || $fullScreen == 2):?>style="display: none;"<?php endif;?>>
					<td width="130px" height="40"><?php echo lang('timeout.period');?>:</td>
					<td>
						<input type="text" id="<?php echo $area->id;?>_btnTimeout" name="btnTimeout" style="width:150px;" value="<?php echo $timeout;?>"/> <?php echo lang('fromat.touch.hh.mm.ss');?>
					</td>
				</tr>
				<tr>
					<td width="150px" height="40"><?php echo lang('btn.show');?>:</td>
					<td>
						<input type="text" id="<?php echo $area->id;?>_btnShowName" name="btnShow" style="width:200px;" value="<?php echo $showName;?>"/>
						<input type="hidden" id="<?php echo $area->id;?>_btnShow" name="btnShow" value="<?php echo $show;?>"/>
						<a href="javascript:void(0);"><img src="/images/icons/16-07.gif" width="16" height="16" title="<?php echo lang('image');?>" onclick="interactionpls.addShowMedia(<?php echo $playlist_id;?>, <?php echo $area->id;?>, <?php echo $area->area_type;?>, <?php echo $this->config->item('media_type_image');?>,1,'<?php echo lang('image.library')?>');" /></a>
					</td>
				</tr>
				<tr id="<?php echo $area->id;?>_play" <?php if($action ==1 || $action ==3):?>style="display: none;"<?php endif;?>>
					<td width="150px" height="40"><?php echo lang('btn.play');?>:</td>
					<td width="800px">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td>
						    		<div class="operate">
										<div style="float:right;">
											<a href="javascript:void(0);"><img src="/images/icons/16-07.gif" width="16" height="16" title="<?php echo lang('image');?>" onclick="interactionpls.addAreaMedia(<?php echo $playlist_id;?>, <?php echo $area->id;?>, <?php echo $area->area_type;?>, <?php echo $this->config->item('media_type_image');?>,1,'<?php echo lang('image.library')?>', <?php echo $screen_id;?>);" /></a>
											<a id="media0" <?php if($goal_type):?>style="display: none;"<?php endif;?> href="javascript:void(0);"><img src="/images/icons/16-12.gif" width="16" height="16" title="<?php echo lang('video');?>" onclick="interactionpls.addAreaMedia(<?php echo $playlist_id;?>, <?php echo $area->id;?>, <?php echo $area->area_type;?>, <?php echo $this->config->item('media_type_video');?>,1,'<?php echo lang('video.library')?>', <?php echo $screen_id;?>);" /></a>
											<a href="javascript:void(0);"><img src="/images/icons/16-04.gif" width="16" height="16" title="<?php echo lang('delete');?>" onclick="interactionpls.removeAreaAllMedia(<?php echo $playlist_id;?>, <?php echo $area->id;?>,'<?php echo lang('warn.choose.empty.tip');?>','<?php echo lang('tip.remove.choose.item');?>', <?php echo $screen_id;?>);"/></a>
										</div>
						         	</div>
						    	</td>
							</tr>
							<tr>
								<td valign="top" class="content">
									<table width="100%" class="table-list">
    									<thead>
    										<tr>
							    				<th width="8">&nbsp;</th>
									            <th width="50"><input type="checkbox" onchange="interactionpls.chooseAreaAllMedia(this,<?php echo $area->id;?>);" value="0" /></th>
									            <th width="80"><?php echo lang('media_type');?></th>
									            <th><?php echo lang('media_name');?></th>
									            <th width="120"><?php echo lang('play_time');?>
									            	<?php
													if($area->area_type == $this->config->item('area_type_movie') || $area->area_type == $this->config->item('area_type_image')) {
													?>
													<a href="/interactionpls/edit_playlist_area_media?playlist_id=<?php echo $playlist_id;?>&area_id=<?php echo $area->id;?>&media_type=<?php echo $area->area_type;?>&type=playtime&width=600&height=200" class="thickbox" title="<?php echo lang('edit.media.config');?>" ><img src="/images/icons/16-03.gif" width="16" height="16" title="<?php echo lang('edit');?>" /></a>
													<?php
													}
													?> 
												</th>
									            <th width="120"><?php echo lang('transition_mode');?>
									            	<?php
													if($area->area_type == $this->config->item('area_type_movie') || $area->area_type == $this->config->item('area_type_image')) {
													?>
													<a href="/interactionpls/edit_playlist_area_media?playlist_id=<?php echo $playlist_id;?>&area_id=<?php echo $area->id;?>&media_type=<?php echo $area->area_type;?>&type=transition&width=600&height=300" class="thickbox" title="<?php echo lang('edit.media.config');?>" ><img src="/images/icons/16-03.gif" width="16" height="16" title="<?php echo lang('edit');?>" /></a>
													<?php
													}
													?>
									            </th>
												<th width="80"><?php echo lang('order');?></th>
												<th width="80"><?php echo lang('move.to');?></th>
									            <th>
									            	<input type="checkbox" onchange="interactionpls.chooseAreaAllExclude(this,<?php echo $area->id;?>, <?php echo $playlist_id;?>);" value="0" />
													<?php echo lang('playlist.area_forget');?>
									            </th>
									            <th width="80"><?php echo lang('operate');?></th>
									      	</tr>
									    </thead>
									    <tbody>
								        	<?php if($media['total'] == 0): ?>
											<tr>
												<td <?php if($portrait && $area->area_type == $this->config->item('area_type_movie')):?>colspan="13"<?php else:?>colspan="12"<?php endif;?>>
													<?php echo lang("empty");?>
												</td>
											</tr>
											<?php else:
										  		$index = 0;
										  		$total = count($media['data']);
										  		foreach($media['data'] as $row):
										  	?>
											<tr <?php if($index%2 != 0):?>class="even"<?php endif;?>>
												<td><?php echo $index + 1;?></td>
												<td><input type="checkbox" name="id" value="<?php echo $row->id;?>" /></td>
												<td>
											  	<?php if($row->media_type == $this->config->item('media_type_image')):?>
													<img src="/images/icons/16-07.gif" alt="" width="16" height="16" />						
													<?php else:?>
													<img src="/images/icons/16-12.gif" alt="" width="16" height="16" />
													<?php endif;?>
													<?php if($row->preview_status > 0 && $area->area_type != $this->config->item('area_type_bg')):?>
													<a href="/media/preview?id=<?php echo $row->media_id;?>&action=play&width=800&height=400" class="thickbox" title="<?php echo lang('preview');?>">
														<?php
															if($row->ext=='png' || $row->ext=='bmp') {
														?>
														<img alt="<?php echo $row->main_url;?>" src="<?php echo $row->main_url;?>" width="32" height="24" />
														<?php
															}else {
														?>
														<img alt="<?php echo $row->main_url;?>" src="<?php echo $row->tiny_url;?>" width="32" height="24" />
														<?php
															}
														?>
													</a>
												<?php else:?>
													<a class="thickbox" href="<?php if(file_exists($row->publish_url)) {echo $row->publish_url.'?t='.time();}else {echo $row->main_url.'?t='.time();}?>">
									  					<img alt="<?php echo $row->main_url;?>" src="<?php echo $row->tiny_url;?>" width="32" height="24" />
									  				</a>
												<?php endif;?>
											  	</td>
											  	<td><?php echo $row->name;?></td>
											  	<td>
												  	<?php if($row->media_type == $this->config->item('media_type_image')):?>
												  	<?php
														if($row->duration == '00:00') {
															echo '--';
														}else {
															echo $row->duration;
														}
													?>
													<?php else:?>
													--
													<?php endif;?>
												</td>
											  	<td>
												  	<?php if($row->transmode == -1 || $row->media_type == $this->config->item('media_type_video')): ?>
													--
													<?php else:?>
														<img  src="/images/transfer/Transfer_Mode_<?php if($row->transmode>9){echo $row->transmode;}else{echo '0'.$row->transmode;} ?>.png" width="32" height="24" title="" />
													<?php endif;?>
											  	</td>
						
											  <td>
											  	<?php if($total > 1):?>
												  	<?php if($index == 0 && $total > 1):?>
														<img src="/images/icons/dir-blank.gif" alt=""/>&nbsp;
														<img src="/images/icons/arrow_down.png" areaId="<?php echo $row->area_id;?>" pos="<?php echo $row->position;?>" cid="<?php echo $row->id;?>" nid="<?php echo $media['data'][$index + 1]->id;?>" class="move bdown" alt="" title="<?php echo lang('move.down');?>">
													<?php elseif($index == $total - 1):?>
														<img src="/images/icons/arrow_up.png" areaId="<?php echo $row->area_id;?>" pos="<?php echo $row->position;?>" cid="<?php echo $row->id;?>" pid="<?php echo $media['data'][$index - 1]->id;?>" class="move bup" alt="" title="<?php echo lang('move.up');?>">&nbsp;
														<img src="/images/icons/dir-blank.gif" alt=""/>
													<?php else:?>
														<img src="/images/icons/arrow_up.png" areaId="<?php echo $row->area_id;?>" pos="<?php echo $row->position;?>" cid="<?php echo $row->id;?>" pid="<?php echo $media['data'][$index - 1]->id;?>" class="move bup" alt="" title="<?php echo lang('move.up');?>">&nbsp;
														<img src="/images/icons/arrow_down.png" areaId="<?php echo $row->area_id;?>" pos="<?php echo $row->position;?>" cid="<?php echo $row->id;?>" nid="<?php echo $media['data'][$index + 1]->id;?>" class="move bdown" alt="" title="<?php echo lang('move.down');?>">
													<?php endif;?>
												<?php endif;?>
											  </td>
											  <td>
											  	<?php if($total > 1):?>
											  		<input type="text" size="2" onchange="interactionpls.moveTo(this, <?php echo $area_id.','.$row->id.','.$total.',\''.lang('warn.number').'\',\''.lang('warn.outbound').'\'';?>)" value="<?php echo $row->position;?>" position="<?php echo $row->position;?>"/>
												<?php endif;?>
											  </td>
											  <td>
											  	 <input type="checkbox" name="status" id="status" value="<?php echo $row->status;?>" onchange="interactionpls.editStatus(this, <?php echo $area_id.','.$row->id;?>)" <?php if($row->status) {echo 'checked="checked"';} ?> />
											  </td>
											  <td>
											  	<a href="/interactionpls/edit_playlist_media?playlist_id=<?php echo $playlist_id;?>&area_id=<?php echo $area_id;?>&id=<?php echo $row->id;?>&width=600&height=360" class="thickbox" title="<?php echo lang('edit.media.config');?>" ><img src="/images/icons/16-03.gif" width="16" height="16" title="<?php echo lang('edit');?>" /></a>
											  <?php if($area->area_type != $this->config->item('area_type_bg')):?>
											  	<a href="javascript:void(0);" onclick="interactionpls.removeAreaMedia(<?php echo $playlist_id;?>,<?php echo $area_id;?>,<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>', <?php echo $screen_id;?>);"><img src="/images/icons/16-04.gif" width="16" height="16" title="<?php echo lang('delete');?>" /></a>
											  <?php endif;?>
											  </td>
											</tr>
											<?php
											 	$index++; 
												endforeach; 
												endif;
											?>
										</tbody>
									</table>
								</td>
					        </tr>
						</table>
					</td>
				</tr>
			</table>
			<script type="text/javascript">
				interactionpls.bindPlaylistMove();
			</script>
  		</td>
  	</tr>
  	<?php endif;?>  	
</table>