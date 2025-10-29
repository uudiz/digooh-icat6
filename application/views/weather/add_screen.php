




<div class="icon-list">
	<span class="page-name"><?php echo $template->name;?></span>
	<ul>

		<li><img id="bg" src="/images/icons/42-bg.png" title="<?php echo lang('bg');?>" /></li>

		<li><img id="logo" src="/images/icons/42-video.png" title="<?php echo lang('video');?>" /></li>

		<li><img id="image1" src="/images/icons/42-image.png"  title="<?php echo lang('image_1day');?>" /></li>
		<li><img id="image2" src="/images/icons/42-image.png"  title="<?php echo lang('image_2day');?>" /></li>
		<li><img id="image3" src="/images/icons/42-image.png"  title="<?php echo lang('image_3day');?>" /></li>
	</ul>
</div>
	


<div class="information">
			<p>
				<b><?php echo lang('area.move');?></b>:&nbsp;<?php echo lang('area.move.tip');?>
			</p>
			<p>
				<b><?php echo lang('area.enlarge');?></b>:&nbsp;<?php echo lang('area.enlarge.tip'); ?>
			</p>
</div>
<br />

	<table  >
		<tr>
			<td align="right">
				<div class="operate" style="display:none;">
					<?php if($area_list):?>
						<?php foreach($area_list as $area){
							$img_index = 1;
							switch($area->area_type){
										//case $this->config->item('area_type_bg'):
										//	echo '<img id="op_bg" src="/images/icons/42-08.gif" width="16" height="16" title="'.lang('bg').'" />';
										//	break;
								case $this->config->item('area_type_movie'):
								echo '<img id="op_movie" class="cursor" src="/images/icons/16-12.gif" width="16" height="16" title="'.lang('video').'" />';
								break;
								case $this->config->item('area_type_image'):
								echo '<img id="op_image_'.$img_index.'" src="/images/icons/16-07.gif" width="16" height="16" title="'.lang('image').$img_index.'" />';
								$img_index++;
								break;
								case $this->config->item('area_type_text'):
								echo '<img id="op_text" src="/images/icons/16-08.gif" width="16" height="16" title="'.lang('staff_name').'" />';
								break;
								case $this->config->item('area_type_date'):
								echo '<img id="op_date" src="/images/icons/16-09.gif" width="16" height="16" title="'.lang('date').'" />';
								break;
								case $this->config->item('area_type_time'):
								echo '<img id="op_time" src="/images/icons/16-10.gif" width="16" height="16" title="'.lang('time').'" />';
								break;
								case $this->config->item('area_type_weather'):
								echo '<img id="op_weather" src="/images/icons/16-11.gif" width="16" height="16" title="'.lang('weather').'" />';
								break;
								case $this->config->item('area_type_webpage'):
								echo '<img id="op_webpage" src="/images/icons/16-11.gif" width="16" height="16" title="'.lang('webpage').'" />';
								break;
								case $this->config->item('area_type_mask'):
								echo '<img id="op_mask" src="/images/icons/16-11.gif" width="16" height="16" title="'.lang('mask').'" />';
								break;
								case $this->config->item('area_type_logo'):
								echo '<img id="op_logo" src="/images/icons/16-13.gif" width="16" height="16" title="'.lang('logo').'" />';
								break;
							}
						}
						?>
					<?php endif;?>
				</div>
			</td>
			<td></td>
		</tr>
		<tr>
			<td>
				<div style="position:relative; width: <?php echo $width;?>px; height: <?php echo $height;?>px;" class="gray-area" id="screen">
					<img id="screenbg" style="position: absolute; top: 0px; left:0px; z-index:1;" width="0" height="0" />
				</div>
			</td>
			<td valign="top" >
				<div style="position:relative; width: 290px;  margin:0px 10px;">
					<div id="areaInfo" style="position:absolute;width:290px;overflow:hidden;  display:none;" class="gray-area from-panel">
						<p style="font-size:14px;font-weight:bold;"><?php echo lang('screen');?></p>
						<table>
							<tr>
								<td width="20px"><?php echo lang('width');?>:</td>
								<td><?php echo $template->width;?>px</td>
							</tr>
							<tr>
								<td><?php echo lang('height');?>:</td>
								<td><?php echo $template->height;?>px</td>
							</tr>
						</table>
						<p id="areaTitle" style="font-size:14px;font-weight:bold;"></p>
						<p>
							<table>
								<tr>
									<td><?php echo lang('x');?>:</td>
									<td>
										<input class="num-box" type="text" id="areaX" style="width:35px;" onblur="weather.index.changeX(<?php echo $template->width/2;?>,<?php echo $template->height/2;?>);"/><span>px</span>
									</td>
									<td></td>
									<td>
										<input type="hidden" id="areaChange" value=""/>
									</td>
								</tr>
								<tr>
									<td><?php echo lang('y');?>:</td>
									<td>
										<input class="num-box" id="areaY" style="width:35px;" onblur="weather.index.changeY(<?php echo $template->width/2;?>,<?php echo $template->height/2;?>);"/><span>px</span>
									</td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td><?php echo lang('width');?>:</td>
									<td>
										<input class="num-box" id="areaWidth" style="width:35px;" onblur="weather.index.changeW(<?php echo $template->width/2;?>,<?php echo $template->height/2;?>,1);"/><span>px</span>
									</td>
									<td><?php echo lang('percent');?>:</td>
									<td>
										<input class="num-box" id="areaWidthPercent" style="width:50px;" onblur="weather.index.changeW(<?php echo $template->width/2;?>,<?php echo $template->height/2;?>,2);"/><span>%</span>
									</td>
								</tr>
								<tr>
									<td><?php echo lang('height');?>:</td>
									<td>
										<input class="num-box" id="areaHeight" style="width:35px;" onblur="weather.index.changeH(<?php echo $template->width/2;?>,<?php echo $template->height/2;?>,1);"/><span>px</span>
									</td>
									<td><?php echo lang('percent');?>:</td>
									<td>
										<input class="num-box" id="areaHeightPercent" style="width:50px;" onblur="weather.index.changeH(<?php echo $template->width/2;?>,<?php echo $template->height/2;?>,2);"/><span>%</span>
									</td>
								</tr>
								<tr id="areabg" style="display: none">
									<td>Image:</td>
									<td><button class="btn btn-default btn-sm" id="selAreaBg">Browse</button></td>
									<td><button class="btn btn-default btn-sm" id="resetAreaBg">Clear</button></td>
								</tr>
							</table>
						</p>
					</div>
				</div>
			</td>
		</tr>
	</table>
	<div class="clear"></div>

	<p class="btn-center" style="margin-top:10px">
		<input type="hidden" id="is_active" name="is_active" value="<?php echo $template->flag;?>" />
		<?php if($auth > 0 && !$using):?>
			<a class="btn-01" href="javascript:void(0);" id="save" ><span><?php echo lang('button.save');?></span></a>
		<?php endif;?>
		<a class="btn-01" href="javascript:void(0);" onclick="weather.screen.goTemplate()" ><span><?php echo lang('button.back');?></span></a>
	</p>	



	<script type="text/javascript">

		weather.screen.id=<?php echo $id;?>;
		weather.screen.width =<?php echo $width;?>;
		weather.screen.height =<?php echo $height;?>;
		weather.screen.realWidth =<?php echo $template->width;?>;
		weather.screen.realHeight =<?php echo $template->height;?>;

		weather.screen.warnSpace = '<?php echo lang('warn.screen.space');?>';
		weather.screen.warnOverlap = '<?php echo lang('warn.screen.overlap');?>';
		weather.screen.warnLogo = '<?php echo lang('area.logo.tip');?>';
		weather.screen.warnVideo = '<?php echo lang('area.video.tip');?>';
		<?php if($using || $auth < $ADMIN):?>
			weather.screen.readonly=true;
		<?php endif;?>
		weather.screen.init();
		<?php
		if(isset($area_list) && !empty($area_list)){
			$img_index = 1;

			foreach($area_list as $area){
				switch($area->area_type){
					case $this->config->item('area_type_bg'):
					echo 'weather.screen.initBg('.$area->id.','.$area->media_id.',"'.$area->main_url.'",0);';
					break;

					case $this->config->item('area_type_image'):
					echo 'weather.screen.initImage('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->zindex.','.$img_index.','.$area->media_id.',"'.$area->main_url.'");';
					$img_index++;
					break;
					case $this->config->item('area_type_image2'):
					echo 'weather.screen.initImage('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->zindex.',2'.','.$area->media_id.',"'.$area->main_url.'");';
					break;
					case $this->config->item('area_type_image3'):
					echo 'weather.screen.initImage('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->zindex.',3'.','.$area->media_id.',"'.$area->main_url.'");';
					break;					
					case $this->config->item('area_type_text'):
					echo 'weather.screen.initText('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.',1);';
					break;
					case $this->config->item('area_type_staticText'):

					echo 'weather.screen.initStaticText('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.',1);';
					break;
					case $this->config->item('area_type_branchText'):
					echo 'weather.screen.initBranchText('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.',1);';
					break;
					case $this->config->item('area_type_date'):
					echo 'weather.screen.initDate('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.',1);';
					break;
					case $this->config->item('area_type_time'):
					echo 'weather.screen.initTime('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->zindex.');';
					break;
					case $this->config->item('area_type_weather'):
					echo 'weather.screen.initWeather('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.',1);';
					break;
					case $this->config->item('area_type_webpage'):
					echo 'weather.screen.initWebpage('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->zindex.');';
					break;
					case $this->config->item('area_type_mask'):
					echo 'weather.screen.initMask('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->zindex.');';
					break;

					case $this->config->item('area_type_logo'):
					echo 'weather.screen.initLogo('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.',100,'.$area->media_id.',"'.$area->main_url.'");';
					break;

				}
			}
		}
		?>
	</script>