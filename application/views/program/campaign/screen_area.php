<div >
	<div id ="media" >
		<table  border="0" cellspacing="0" cellpadding="0" width="98%">
		<tr>
			<td>
				<div class="operate">
					<?php if ($area->area_type == $this->config->item('area_type_movie')):?>
					<div style="float:right;">
						<a href="javascript:void(0);"><img src="/images/icons/16-07.gif" width="16" height="16" title="<?php echo lang('image');?>" onclick="campaign.addAreaMedia(<?php echo $playlist_id;?>, <?php echo $area->id;?>, <?php echo $area->area_type;?>, <?php echo $this->config->item('media_type_image');?>,1,'<?php echo lang('image.library')?>');" /></a>
						<a href="javascript:void(0);"><img src="/images/icons/16-12.gif" width="16" height="16" title="<?php echo lang('video');?>" onclick="campaign.addAreaMedia(<?php echo $playlist_id;?>, <?php echo $area->id;?>, <?php echo $area->area_type;?>, <?php echo $this->config->item('media_type_video');?>,1,'<?php echo lang('video.library')?>');" /></a>
						<a href="javascript:void(0);"><img src="/images/icons/16-04.gif" width="16" height="16" title="<?php echo lang('delete');?>" onclick="campaign.removeAreaAllMedia(<?php echo $playlist_id;?>, <?php echo $area->id;?>,'<?php echo lang('warn.choose.empty.tip');?>','<?php echo lang('tip.remove.choose.item');?>');"/></a>

						<a  href="javascript:void(0);"> <img src="/images/icons/24-add.png" width="16" height="16" title="<?php echo lang('upload.medias');?>" onclick="campaign.uploadAreaMedia(<?php echo $playlist_id;?>, <?php echo $area->id;?>, <?php echo $area->area_type;?>);" /></a>

					</div>
					<?php elseif ($area->area_type == $this->config->item('area_type_logo') || $area->area_type == $this->config->item('area_type_bg') || $area->area_type == $this->config->item('area_type_image') || $area->area_type == $this->config->item('area_type_mask')):?>
					<div style="float:right;">
						<a href="javascript:void(0);"><img src="/images/icons/16-07.gif" width="16" height="16" title="<?php echo lang('image');?>" onclick="campaign.addAreaMedia(<?php echo $playlist_id;?>, <?php echo $area->id;?>, <?php echo $area->area_type;?>, <?php echo $this->config->item('media_type_image');?>,1,'<?php echo lang('image.library')?>');" /></a>
						<a href="javascript:void(0);"><img src="/images/icons/16-04.gif" width="16" height="16" title="<?php echo lang('delete');?>" onclick="campaign.removeAreaAllMedia(<?php echo $playlist_id;?>, <?php echo $area->id;?>,'<?php echo lang('warn.choose.empty.tip');?>','<?php echo lang('tip.remove.choose.item');?>');"/></a>
						<a  href="javascript:void(0);"> <img src="/images/icons/24-add.png" width="16" height="16" title="<?php echo lang('upload.medias');?>" onclick="campaign.uploadAreaMedia(<?php echo $playlist_id;?>, <?php echo $area->id;?>, <?php echo $area->area_type;?>);" /></a>

					</div>
					<?php elseif ($area->area_type == $this->config->item('area_type_webpage')):?>

						<div class="add-panel">
							<a href="javascript:void(0);" onclick="campaign.addAreaMedia(<?php echo $playlist_id;?>, <?php echo $area->id;?>, <?php echo $area->area_type;?>, <?php echo $this->config->item('media_type_webpage');?>,1,'<?php echo lang('webpage.library')?>');" class="webpage" title="Webpage"><?php echo lang('create');?></a>
						</div>
					<?php endif;?>
				</div>
			</td>
		</tr>
		<?php if ($area->area_type == $this->config->item('area_type_movie') || $area->area_type == $this->config->item('area_type_image') || $area->area_type == $this->config->item('area_type_bg') || $area->area_type == $this->config->item('area_type_logo') || $area->area_type == $this->config->item('area_type_mask')):?>
		<?php
        $class = "";
        switch ($area->area_type) {
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
            case $this->config->item('area_type_mask'):
                $class="table_mask";
            break;
        }
        ?>
		<tr>
			<td valign="top" class="content">
				<table width="100%" class="table-list <?php echo $class;?>">
					<thead>
						<tr>
							<th width="8">&nbsp;</th>
							<th width="50"><input type="checkbox" onchange="campaign.chooseAreaAllMedia(this,<?php echo $area->id;?>);" value="0" /></th>
							<th width="80"><?php echo lang('media_type');?></th>
							<th><?php echo lang('media_name');?></th>
							<th width="120"><?php echo lang('play_time');?>
								<!-- 
								<?php
                                if ($area->area_type == $this->config->item('area_type_movie') || $area->area_type == $this->config->item('area_type_image') || $area->area_type == $this->config->item('area_type_mask')) {
                                    ?>
								<a href="/campaign/edit_playlist_area_media?playlist_id=<?php echo $playlist_id; ?>&area_id=<?php echo $area->id; ?>&media_type=<?php echo $area->area_type; ?>&type=playtime&width=600&height=500" class="thickbox" title="<?php echo lang('edit.media.config'); ?>" ><img src="/images/icons/16-03.gif" width="16" height="16" title="<?php echo lang('edit'); ?>" /></a>
								<?php
                                }
                                ?>  
								-->
							</th>

							<th width="120"><?php echo lang('transition_mode');?>
								<?php
                                if ($area->area_type == $this->config->item('area_type_movie') || $area->area_type == $this->config->item('area_type_image') || $area->area_type == $this->config->item('area_type_mask')) {
                                    ?>
								<a href="/campaign/edit_playlist_area_media?playlist_id=<?php echo $playlist_id; ?>&area_id=<?php echo $area->id; ?>&media_type=<?php echo $area->area_type; ?>&type=transition&width=600&height=500" class="thickbox" title="<?php echo lang('edit.media.config'); ?>" ><img src="/images/icons/16-03.gif" width="16" height="16" title="<?php echo lang('edit'); ?>" /></a>
								<?php
                                }
                                ?>
							</th>
							
							
							<th width="80"><?php echo lang('order');?></th>
							<th width="80"><?php echo lang('move.to');?></th>
							<?php if ($playlist_one->pls_type !=1 && $portrait && ($area->area_type == $this->config->item('area_type_movie') || $area->area_type == $this->config->item('area_type_image'))):?>
							<th width="80"><?php echo lang('rotate');?></th>
							<?php endif;?>
							<?php
                            if ($area->area_type == $this->config->item('area_type_movie') || $area->area_type == $this->config->item('area_type_image')) {
                                ?>
							<th>
								<input type="checkbox" onchange="campaign.chooseAreaAllExclude(this,<?php echo $area->id; ?>, <?php echo $playlist_id; ?>);" value="0" />
								<?php echo lang('playlist.area_forget'); ?>
							</th>
							<?php
                            }
                            ?>
							<th width="60">Reload</th>
							<th width="80"><?php echo lang('end.date');?></th>
							<th width="80"><?php echo lang('operate');?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ($media['total'] == 0): ?>
						<tr>
							<td <?php if ($portrait && $area->area_type == $this->config->item('area_type_movie')):?>colspan="13"<?php else:?>colspan="12"<?php endif;?>>
								<?php echo lang("empty");?>
							</td>
						</tr>
						<?php else:
                        $index = 0;
                        $total = count($media['data']);
                        ?>
						<?php foreach ($media['data'] as $row):?>
							<tr <?php if ($index%2 != 0):?>class="even"<?php endif;?>>
							<td><?php echo $index + 1;?></td>
							<td><input type="checkbox" name="id" value="<?php echo $row->id;?>" /></td>
							<td>
								<!--
								<?php if ($row->media_type == $this->config->item('media_type_image')):?>
									<img src="/images/icons/16-07.gif" alt="" width="16" height="16" />						
								<?php else:?>
									<img src="/images/icons/16-12.gif" alt="" width="16" height="16" />
								<?php endif;?>
								-->
								<?php if (in_array(strtolower($row->ext), array('ppt', 'pptx'))):?>
								
								<?php else:?>
								<?php if ($row->preview_status > 0 && $area->area_type != $this->config->item('area_type_bg') && !in_array($row->ext, array('ppt', 'pptx'))):?>
								<a href="/media/preview?id=<?php echo $row->media_id;?>&action=play&width=800&height=500" class="thickbox" title="<?php echo lang('preview');?>">
									<?php
                                    if ($row->ext=='bmp') {
                                        ?>
									<img alt="<?php echo $row->main_url; ?>" src="<?php echo $row->main_url; ?>" height='24' style = "max-width:32;max-height:24;" />
									<?php
                                    } else {
                                        ?>
									<img alt="<?php echo $row->main_url; ?>" src="<?php echo $row->tiny_url; ?>" height='24' style = "max-width:32;max-height:24;" />
									<?php
                                    }
                                    ?>
								</a>
								<?php else:?>
							
								<a class="thickbox" href="<?php if (file_exists($row->publish_url)) {
                                        echo $row->publish_url.'?t='.time();
                                    } else {
                                        echo $row->main_url.'?t='.time();
                                    }?>">
									<img alt="<?php echo $row->main_url;?>" src="<?php echo $row->tiny_url;?>"  height='24' style = "max-width:32;max-height:24;" />
								</a>
							
								<?php endif;?>
								<?php endif;?>
								
							</td>
							<td><?php echo $row->name;?></td>
							<td>
								<?php if ($row->media_type == $this->config->item('media_type_image') ||$row->media_type == $this->config->item('media_type_video')|| $area->area_type == $this->config->item('area_type_mask')):?>
									<?php if ($row->play_time) {
                                        if ($row->play_time>59) {
                                            $times = sprintf("%02d:%02d", ($row->play_time/60), ($row->play_time%60));
                                        } else {
                                            $times = sprintf("00:%02d", $row->play_time);
                                        }
                                        echo $times;
                                    }
                                    ?>
								<?php else:?>
								<?php
                                $ext = strtolower($row->ext);
                                    if ($ext=='ppt' || $ext=='pptx') {
                                        echo $row->duration;
                                    } else {
                                        echo '--';
                                    }
                                    ?>
								
								<?php endif;?>
								</td>
							
							<td>
								<?php if ($row->transmode == -1 || $row->media_type == $this->config->item('media_type_video')): ?>
								--
								<?php else:?>
									<img  src="/images/transfer/Transfer_Mode_<?php if ($row->transmode>9) {
                                        echo $row->transmode;
                                    } else {
                                        echo '0'.$row->transmode;
                                    } ?>.png" width="32" height="24" title="" />
								<?php endif;?>
							</td>
							<?php
                                if ($area->area_type == $this->config->item('area_type_movie') || $area->area_type == $this->config->item('area_type_image')) {
                                    ?>

							<!-- 
							<td>
								<?php if ($row->play_count!="0") {
                                        echo $row->play_count;
                                    } ?>
							</td>
							<td>
							<?php
                                    if (!$row->all_day_flag) {
                                        echo $row->start_time.".".$row->end_time;
                                    } ?>


							</td>
							<td>
								<?php
                                    if ($row->date_flag) {
                                        echo $row->start_date.'~'.$row->end_date;
                                    } ?>
							</td>
							-->  
							<?php
                                }
                            ?>

							<td>
								<?php if ($total > 1):?>
									<?php if ($index == 0 && $total > 1):?>
										<img src="/images/icons/dir-blank.gif" alt=""/>&nbsp;
										<img src="/images/icons/arrow_down.png" cid="<?php echo $row->id;?>" nid="<?php echo $media['data'][$index + 1]->id;?>" class="move down" alt="" title="<?php echo lang('move.down');?>">
									<?php elseif ($index == $total - 1):?>
										<img src="/images/icons/arrow_up.png" cid="<?php echo $row->id;?>" pid="<?php echo $media['data'][$index - 1]->id;?>" class="move up" alt="" title="<?php echo lang('move.up');?>">&nbsp;
										<img src="/images/icons/dir-blank.gif" alt=""/>
									<?php else:?>
										<img src="/images/icons/arrow_up.png" cid="<?php echo $row->id;?>" pid="<?php echo $media['data'][$index - 1]->id;?>" class="move up" alt="" title="<?php echo lang('move.up');?>">&nbsp;
										<img src="/images/icons/arrow_down.png" cid="<?php echo $row->id;?>" nid="<?php echo $media['data'][$index + 1]->id;?>" class="move down" alt="" title="<?php echo lang('move.down');?>">
									<?php endif;?>
								<?php endif;?>
							</td>
							<td>
								<?php if ($total > 1):?>
									<input type="text" size="2" onchange="campaign.moveTo(this, <?php echo $area_id.','.$row->id.','.$total.',\''.lang('warn.number').'\',\''.lang('warn.outbound').'\'';?>)" value="<?php echo $row->position;?>" position="<?php echo $row->position;?>"/>
								<?php endif;?>
							</td>
							<?php if ($playlist_one->pls_type !=1 && $portrait && ($area->area_type == $this->config->item('area_type_movie') || $area->area_type == $this->config->item('area_type_image'))):?>
							<td>
								<input type="checkbox" name="rotate" <?php if ($row->rotate):?>checked="checked"<?php endif;?>  onclick="campaign.rotateMedia(this, <?php echo $row->id;?>);" />
							</td>
							<?php endif;?>
							<?php
                                if ($area->area_type == $this->config->item('area_type_movie') || $area->area_type == $this->config->item('area_type_image')) {
                                    ?>
							<td>
								<input type="checkbox" name="status" id="status" value="<?php echo $row->status; ?>" onchange="campaign.editStatus(this, <?php echo $area_id.','.$row->id; ?>)" <?php if ($row->status) {
                                        echo 'checked="checked"';
                                    } ?> />
							</td>
							<?php
                                };?>
							<?php
                                if ($area->area_type == $this->config->item('area_type_movie') || $area->area_type == $this->config->item('area_type_image')) {
                                    ?>
							
							<td>
							<?php if ($row->source):?>
								<input type="checkbox" name="reload" id="reload" value="<?php echo $row->reload; ?>" onchange="campaign.editReload(this, <?php echo $area_id.','.$row->id; ?>)" <?php if ($row->reload) {
                                        echo 'checked="checked"';
                                    } ?> />
							<?php endif; ?>
							</td>
							
							<?php
                                }?>
								<td>

								<?php if ($row->date_flag&&date('Y-m-d') > $row->end_date):?> 
										<font style="color: red;"> <?php echo $row->end_date;?></font> 
								<?php elseif ($row->date_flag): ?>
										<?php echo $row->end_date;?>
								<?php endif;?>
							</td>
							
							<td>
							
							<?php if (($area->area_type == $this->config->item('area_type_movie') || $area->area_type == $this->config->item('area_type_mask') || $area->area_type == $this->config->item('area_type_image')) && $row->media_type == $this->config->item('media_type_image')):?>
								<a href="/campaign/edit_playlist_media?playlist_id=<?php echo $playlist_id;?>&area_id=<?php echo $area_id;?>&id=<?php echo $row->id;?>&width=600&height=500" class="thickbox" title="<?php echo lang('edit.media.config');?>" ><img src="/images/icons/16-03.gif" width="16" height="16" title="<?php echo lang('edit');?>" /></a>
							<?php else:?>
								<img src="/images/icons/blank.gif">
							<?php endif;?>
							<?php if ($area->area_type != $this->config->item('area_type_bg')):?>
								<a href="javascript:void(0);" onclick="campaign.removeAreaMedia(<?php echo $playlist_id;?>,<?php echo $area_id;?>,<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>');"><img src="/images/icons/16-04.gif" width="16" height="16" title="<?php echo lang('delete');?>" /></a>
							<?php endif;?>
							</td>
							
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
		<?php elseif ($area->area_type == $this->config->item('area_type_text')):?>
		<tr>
			<td>
				<div>
					
						<table class="gray-area from-panel" style="width:100%" border="0">
							<tr>
								<td style="vertical-align:middle;" width="60" ><?php echo lang('rss');?></td>
								<td colspan="9">
									<textarea style="width:100%; height:60px; <?php if ($area->setting->direction==2) {
                                echo 'direction:rtl;unicode-bidi:embed;';
                            }?>;" id="ticker" name="ticker" <?php if ($media['total'] > 0): ?>readonly="readonly"<?php endif;?> ><?php if ($after_media != 2):?><?php echo $area->setting->content;?><?php endif;?></textarea>
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
									<select id="direction" name="direction" onchange="campaign.font_direction(this);">
									<?php foreach ($directions as $k => $v):?>
										<option value="<?php echo $k?>" <?php if ($k == $area->setting->direction) {
                                echo 'selected="selected"';
                            }?>><?php echo $v?></option>
									<?php endforeach;?>
									</select>
								</td>
								<td width="80" ><?php echo lang('font.size');?></td>
								<td>
									<select id="fontSize" name="font_size" >
									<?php foreach ($font_sizes as $k => $v):?>
										<option value="<?php echo $k?>" <?php if ($k == $area->setting->font_size) {
                                echo 'selected="selected"';
                            }?>><?php echo $v?></option>
									<?php endforeach;?>
									</select>
								</td>
								<?php
                                if (isset($rss->type) && $rss->type > 0) {
                                    ?>
								<input type="hidden" id="rssFormat" name="rssFormat" value="0" />
								<?php
                                } else {
                                    ?>
								<td width="100" id="formatLabel" <?php if ($media['total'] == 0): ?>style="display:none;"<?php endif; ?>><?php echo lang('rss.format'); ?></td>
								<td id="formatOption" <?php if ($media['total'] == 0): ?>style="display:none;"<?php endif; ?>>
									<select id="rssFormat" onchange="campaign.changeRssFormat(this,<?php echo $playlist_id; ?>, <?php echo $area->id; ?>);">
										<option value="0" <?php if (0 == $area->setting->rss_format) {
                                        echo 'selected="selected"';
                                    } ?> ><?php echo lang('rss.title'); ?></option>
										<option value="1" <?php if (1 == $area->setting->rss_format) {
                                        echo 'selected="selected"';
                                    } ?> ><?php echo lang('rss.detail'); ?></option>
										<option value="2" <?php if (2 == $area->setting->rss_format) {
                                        echo 'selected="selected"';
                                    } ?> ><?php echo lang('rss.title').'&nbsp;&amp;&nbsp;'.lang('rss.detail'); ?></option>
									</select>
								</td>
								<?php
                                }?>
							</tr>
							<tr>
								<td><?php echo lang('text.speed');?></td>
								<td>
									<select id="speed" name="speed">
									<?php foreach ($speeds as $k => $v):?>
										<option value="<?php echo $k?>" <?php if ($k == $area->setting->speed) {
                                    echo 'selected="selected"';
                                }?>><?php echo $v?></option>
									<?php endforeach;?>
									</select>
								</td>
								<td><?php echo lang('text.transparent');?></td>
								<td>
									<select id="text_transparent" name="text_transparent">
									<?php foreach ($transparents as $k => $v):?>
										<option value="<?php echo $k?>" <?php if ($k == $area->setting->transparent) {
                                    echo 'selected="selected"';
                                }?>><?php echo $v?></option>
									<?php endforeach;?>
									</select>
								</td>
			
								<?php
                                if ($this->config->item('text.font_family')):
                                ?>
								<td><?php echo lang('font.font');?></td>
								<td>
									<select id="font_font" name="font_font" >
									<?php foreach ($font_font as $k => $v):?>
										<option value="<?php echo $k?>" <?php if ($k == $area->setting->font) {
                                    echo 'selected="selected"';
                                }?>><?php echo $v?></option>
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
									Separator&nbsp;&nbsp;&nbsp;<input type="text" name="rss_delimiter" id="rss_delimiter" value="<?php echo $rss_delimiter;?>" onchange="campaign.updateRssFlag(this,<?php echo $area->id;?>, <?php echo $playlist_id;?>);" size="8">
									&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);"><img src="/images/icons/rss.gif" width="16" height="16" title="<?php echo lang('rss');?>" onclick="campaign.addAreaMedia(<?php echo $playlist_id;?>, <?php echo $area->id;?>, <?php echo $area->area_type;?>, <?php echo $this->config->item('media_type_rss');?>,1,'<?php echo lang('rss.library')?>');" /></a>
									<?php if ($media['total'] > 0):?>
										<span><?php echo $media['data'][0]->name;?></span>
										<input type="hidden" id="rssId" name="rssId" value="<?php echo $rss->id;?>" />
										<a href="javascript:void(0);"><img src="/images/icons/16-04.gif" width="16" height="16" title="<?php echo lang('delete');?>" onclick="campaign.removeAreaMedia(<?php echo $playlist_id;?>,<?php echo $area_id;?>,<?php echo $rss->id;?>,'<?php echo lang('tip.remove.item');?>');" <?php if ($media['total'] == 0): ?>style="display:none;"<?php endif;?> /></a>
									<?php endif;?>
								</td>
							</tr>
						</table>
					<input type="hidden" id="textId" name="textId" value="<?php echo $area->setting->id;?>" />
					<input type="hidden" id="textAreaId" name="textAreaId" value="<?php echo $area->id;?>" />
				</div>
				<script type="text/javascript">
					campaign.initTextArea();
				</script>
			</td>
		</tr>
		<?php elseif ($area->area_type == $this->config->item('area_type_staticText')):?>
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
                                    if ($area->setting->position == 2) {
                                        $text_algin = 'center';
                                    }
                                    if ($area->setting->position == 3) {
                                        $text_algin = 'right';
                                    }
                                    if ($area->setting->transparent == 1) {
                                        $bg_color = $area->setting->bg_color;
                                    }
                                    $html_content = str_replace('<br/>', '&#13;&#10', $area->setting->html_content);
                                    $html_content = str_replace('&#039', '\'', $html_content);
                                    ?>
									<textarea style="resize: none; overflow: hidden; background-color:<?php echo $bg_color;?>; text-align: <?php echo $text_algin;?>; width:<?php echo 2*$area->w;?>px; height:<?php echo 2*$area->h;?>px; font-size:<?php echo $area->setting->font_size;?>px; font-family:'<?php echo $area->setting->font_family;?>'; color:<?php echo $area->setting->color;?>; text-decoration:<?php if ($area->setting->underline) {
                                        echo 'underline';
                                    } else {
                                        echo 'none';
                                    }?>; font-weight:<?php if ($area->setting->bold) {
                                        echo 'bold';
                                    } else {
                                        echo 'normal';
                                    }?>; font-style:<?php if ($area->setting->italic) {
                                        echo 'italic';
                                    } else {
                                        echo 'normal';
                                    }?>" id="static_ticker" name="ticker" ><?php echo $html_content;?></textarea>
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
								<td width="90"><input type="checkbox" name="transparent" id="transparent" <?php if ($area->setting->transparent == 2) {
                                        echo 'checked="checked"';
                                    }?>onchange="campaign.sfont_transparent(this,<?php echo $playlist_id;?>, <?php echo $area->id;?>);">transparent</td>
								-->
								<td width="50" style="text-align:right;"><?php echo lang('font.align');?></td>
								<td width="120">
									<select id="sfont_position" name="sfont_position" onchange="campaign.font_position(this,<?php echo $playlist_id;?>, <?php echo $area->id;?>);">
									<?php foreach ($font_position as $k => $v):?>
										<option value="<?php echo $k?>" <?php if ($k == $area->setting->position) {
                                        echo 'selected="selected"';
                                    }?>><?php echo $v?></option>
									<?php endforeach;?>
									</select>
								</td>
								<td width="30" style="text-align: right;"><?php echo lang('font.set');?></td>
								<td width="150">
									<select id="sfont_family" name="sfont_family" onchange="campaign.font_family(this,<?php echo $playlist_id;?>, <?php echo $area->id;?>);">
									<?php foreach ($font_familys as $k => $v):?>
										<option value="<?php echo $v?>" <?php if ($v == $area->setting->font_family) {
                                        echo 'selected="selected"';
                                    }?>><?php echo $v?></option>
									<?php endforeach;?>
									</select>
								</td>
								<td width="30" style="text-align:right;"><?php echo lang('font.size');?></td>
								<td width="120">
									<select id="sfont_size" name="sfont_size" onchange="campaign.font_size(this,<?php echo $playlist_id;?>, <?php echo $area->id;?>);">
									<?php for ($i = 10; $i <= 100; $i++) {?>
										<option value="<?php echo $i?>" <?php if ($i == $area->setting->font_size) {
                                        echo 'selected="selected"';
                                    }?>><?php echo $i?></option>
									<?php }?>
									</select>
								</td>
								<td width="90"><input type="checkbox" name="sfont_underline" id="sfont_underline" <?php if ($area->setting->underline == 1) {
                                        echo 'checked="checked"';
                                    }?>onchange="campaign.font_underline(this,<?php echo $playlist_id;?>, <?php echo $area->id;?>);"><?php echo lang('font.u');?></td>
								<td width="50"><input type="checkbox" name="sfont_blod" id="sfont_blod" <?php if ($area->setting->bold == 1) {
                                        echo 'checked="checked"';
                                    }?> onchange="campaign.font_bold(this,<?php echo $playlist_id;?>, <?php echo $area->id;?>);"><?php echo lang('font.b');?></td>
								<td width="50"><input type="checkbox" name="sfont_italic" id="sfont_italic" <?php if ($area->setting->italic == 1) {
                                        echo 'checked="checked"';
                                    }?> onchange="campaign.font_italic(this,<?php echo $playlist_id;?>, <?php echo $area->id;?>);"><?php echo lang('font.i');?></td>
								<td></td>
							</tr>
						</table>
					<input type="hidden" id="static_textId" name="textId" value="<?php echo $area->setting->id;?>" />
					<input type="hidden" id="static_textAreaId" name="textAreaId" value="<?php echo $area->id;?>" />
					<span id="testSpanForCheck" style="visibility:hidden;width:<?php echo 2*$area->w;?>px; height:<?php echo 2*$area->h;?>px;">
					
				</div>
				<script type="text/javascript">
					campaign.initStaticTextArea();
				</script>
			</td>
		</tr>
		<?php elseif ($area->area_type == $this->config->item('area_type_date')):?>
		<tr>
			<td>
				<table>
					<tr class="time30" <?php if ($style ==9):?>style="display: none;"<?php endif;?>>
						<td height="40">
							<?php echo lang('font.size');?>
						</td>
						<td>
							<select id="dateFontSize" name="fontSize" style="width: 150px;" onchange="campaign.dateChange();">
								<?php foreach ($font_sizes as $v):?>
									<option value="<?php echo $v;?>" <?php if ($v==$font_size):?>selected<?php endif;?> ><?php echo $v;?></option>
								<?php endforeach;?>
							</select>
						</td>
					</tr>
					<tr class="countdown" <?php if ($style !=9):?>style="display: none;"<?php endif;?>>
						<td height="40">
							<?php echo lang('font.size');?>
						</td>
						<td>
							<select id="dateFontSize2" name="fontSize" style="width: 150px;" onchange="campaign.dateChange();">
								<?php foreach ($count_font_sizes as $v):?>
									<option value="<?php echo $v;?>" <?php if ($v==$font_size):?>selected<?php endif;?> ><?php echo $v;?></option>
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
					<?php if ($playlist_one->pls_type ==1):?>
					<tr>
						<td height="40">
							Style
						</td>
						<td>
							<select id="dataStyle" name="dataStyle" style="width: 170px;" onchange="campaign.dateChange();">
								<option value="1" <?php if ($style == 1):?>selected<?php endif;?>>mm/dd/yyyy weekday</option>
								<option value="2" <?php if ($style == 2):?>selected<?php endif;?>>dd/mm/yyyy weekday</option>
								<option value="3" <?php if ($style == 3):?>selected<?php endif;?>>yyyy/mm/dd weekday</option>
								<option value="4" <?php if ($style == 4):?>selected<?php endif;?>>mm/dd/yyyy</option>
								<option value="5" <?php if ($style == 5):?>selected<?php endif;?>>dd/mm/yyyy</option>
								<option value="6" <?php if ($style == 6):?>selected<?php endif;?>>yyyy/mm/dd</option>
								<option value="9" <?php if ($style == 9):?>selected<?php endif;?>>Countdown</option>
							</select>
							<font>
							<?php
                            $week = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
                            ?>
								<!--&nbsp;&nbsp;&nbsp;eg: <?php echo date('m/d/Y').'   '.$week[date('w')];?>-->
							</font>
						</td>
					</tr>
					<tr class="countdown" <?php if ($style !=9):?>style="display: none;"<?php endif;?>>
						<td>Countdown</td>
						<td>
							<input type="text" id="countdown" name="countdown" style="width: 130px;" value="<?php echo $countdown;?>" readonly="readonly" class="date-input"/>
						</td>
					</tr>
					<tr>
						<td height="40"><?php echo lang('text.transparent');?></td>
						<td>
							<select id="dtransparent" name="dtransparent" onchange="campaign.dateChange();">
								<?php foreach ($transparents as $k => $v):?>
								<option value="<?php echo $k?>" <?php if ($k == $transparent) {
                                echo 'selected="selected"';
                            }?>><?php echo $v?></option>
								<?php endforeach;?>
							</select>
						</td>
					</tr>
					<tr>
						<td height="20">&nbsp;</td><td></td>
					</tr>
					<tr id="dpreview" <?php if ($style ==9):?>style="display: none;"<?php endif;?>>
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
                                switch ($style) {
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
					<?php endif;?>
					<?php
                    if (true):
                    ?>
					<tr id="dlang" <?php if ($style ==9):?>style="display: none;"<?php endif;?>>
						<td height="40">
							<?php echo lang('language');?>
						</td>
						<td>
							<select id="dateLanguage" name="dateLanguage" style="width: 150px;">
								<option value="0" <?php if (0==$language):?>selected<?php endif;?> >English</option>
								<option value="1" <?php if (1==$language):?>selected<?php endif;?> >Español</option>
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
					campaign.initDateArea();
				</script>
			</td>
		</tr>
		<?php elseif ($area->area_type == $this->config->item('area_type_weather')):?>
		<tr>
			<td>
				<table>
					<tr>
						<td height="40">
							<?php echo lang('font.size');?>
						</td>
						<td>
							<select id="weatherFontSize" name="fontSize" style="width: 150px;">
								<?php foreach ($font_sizes as $v):?>
									<option value="<?php echo $v;?>" <?php if ($v==$font_size):?>selected<?php endif;?>><?php echo $v;?></option>
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
					<?php if ($playlist_one->pls_type ==1):?>
					<tr>
						<td height="40">
							Style
						</td>
						<td>
							<select id="weatherStyle" name="weatherStyle" onchange="campaign.wImage(this,<?php echo $area->id;?>);" style="width: 160px;">
								<option value="5" <?php if ($style == 5):?>selected<?php endif;?>>Style 1 (Today)</option>
								<option value="4" <?php if ($style == 4):?>selected<?php endif;?>>Style 2 (3 days)</option>
							</select>
						</td>
					</tr>
					<tr>
						<td></td>
						<td id="wImage" style="display: none;">
							<?php if ($style == 5) {?>
								<img src="/images/wstyle5.jpg" />
							<?php } else { ?>
								<img src="/images/wstyle4.jpg" />
							<?php
                            }?>
						</td>
					</tr>
					<tr>
						<td height="40"><?php echo lang('text.transparent');?></td>
						<td>
							<select id="wtransparent" name="wtransparent">
								<?php foreach ($transparents as $k => $v):?>
								<option value="<?php echo $k?>" <?php if ($k == $transparent) {
                                echo 'selected="selected"';
                            }?>><?php echo $v?></option>
								<?php endforeach;?>
							</select>
						</td>
					</tr>
					<?php endif;?>
					<?php
                    if (true):
                    ?>
					<tr>
						<td height="40">
							<?php echo lang('language');?>
						</td>
						<td>
							<select id="weatherLanguage" name="weatherLanguage" style="width: 150px;">
								<option value="0" <?php if (0==$language):?>selected<?php endif;?> >English</option>
								<option value="1" <?php if (1==$language):?>selected<?php endif;?> >Español</option>
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
					campaign.initWeatherArea();
				</script>
			</td>
		</tr>
		<?php endif ?>
		</table>
	</div>

</div>
