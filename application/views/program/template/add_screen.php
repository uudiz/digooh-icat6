<div class="icon-list">
	<span class="page-name"><?php echo $template->name;?></span>
  <ul>
  	<li><img id="bg" src="/images/icons/42-bg.png" title="<?php echo lang('bg');?>" /></li>
     <li><img id="movie" src="/images/icons/42-video.png"  title="<?php echo lang('video');?>" /></li>
     <li><img id="image1" src="/images/icons/42-image.png"  title="<?php echo lang('image1');?>" /></li>
	 <li><img id="image2" src="/images/icons/42-image.png"  title="<?php echo lang('image2');?>" /></li>
	 <li><img id="image3" src="/images/icons/42-image.png"  title="<?php echo lang('image3');?>" /></li>
	 <?php
	 if($template->template_type):
	 ?>
	 <li><img id="image4" src="/images/icons/42-image.png"  title="<?php echo lang('image4');?>" /></li>
	 <li><img id="webpage" src="/images/icons/42-webpage.png"  title="<?php echo lang('webpage');?>" /></li>
	 <?php
	 endif;
	 ?>
	 <li><img id="date" src="/images/icons/42-date.png" title="<?php echo lang('date');?>" /></li>
	 <?php
	 if($template->template_type):
	 ?>
	 <li><img id="time" src="/images/icons/42-05.png" title="<?php echo lang('time');?>" /></li>
	 <?php
	 endif;
	 ?>
     <li><img id="weather" src="/images/icons/42-weather.png" title="<?php echo lang('weather');?>" /></li>
     <li><img id="logo" src="/images/icons/42-logo.png" title="<?php echo lang('logo');?>" /></li>
     <li><img id="text" src="/images/icons/42-text.png"  title="<?php echo lang('text');?>" /></li>
      <?php
	 if($template->template_type):
	 ?>
     <li><img id="staticText" src="/images/icons/43-stext.png"  title="<?php echo lang('static.text');?>" /></li>
     <li><img id="mask" src="/images/icons/43-mask.png"  title="<?php echo lang('mask');?>" /></li>
     <?php
	 endif;
	 ?>
  </ul>
</div>
<div class="clear"></div>
<?php if($using):?>
<div class="information"><?php echo lang('warn.template.readonly');?></div>
<br />
<?php else:?>
<div class="information">
	<p>
		<b><?php echo lang('area.move');?></b>:&nbsp;<?php echo lang('area.move.tip');?>
	</p>
	<p>
		<b><?php echo lang('area.enlarge');?></b>:&nbsp;<?php echo lang('area.enlarge.tip'); ?>
	</p>
</div>
<br />
<?php endif;?>

<table onselectstart="return false;" style="-moz-user-select:none;" >
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
							echo '<img id="op_text" src="/images/icons/16-08.gif" width="16" height="16" title="'.lang('text').'" />';
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
			<div style="position:relative; width: 290px; height: <?php echo $height;?>px; margin:0px 10px;">
				<div id="areaInfo" style="position:absolute;width:290px;height:300px;overflow:hidden;  display:none;" class="gray-area from-panel">
					<p style="font-size:14px;font-weight:bold;"><?php echo lang('screen');?></p>
					<p>
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
					</p>
					<p id="areaTitle" style="font-size:14px;font-weight:bold;"></p>
					<p>
						<table>
							<tr>
								<td><?php echo lang('x');?>:</td>
								<td>
									<input class="num-box" type="text" id="areaX" style="width:35px;" onblur="template.index.changeX(<?php echo $template->width/2;?>,<?php echo $template->height/2;?>);"/> px
								</td>
								<td></td>
								<td>
									<input type="hidden" id="areaChange" value=""/>
								</td>
							</tr>
							<tr>
								<td><?php echo lang('y');?>:</td>
								<td>
									<input class="num-box" id="areaY" style="width:35px;" onblur="template.index.changeY(<?php echo $template->width/2;?>,<?php echo $template->height/2;?>);"/> px
								</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td><?php echo lang('width');?>:</td>
								<td>
									<input class="num-box" id="areaWidth" style="width:35px;" onblur="template.index.changeW(<?php echo $template->width/2;?>,<?php echo $template->height/2;?>,1);"/> px
								</td>
								<td><?php echo lang('percent');?>:</td>
								<td>
									<input class="num-box" id="areaWidthPercent" style="width:50px;" onblur="template.index.changeW(<?php echo $template->width/2;?>,<?php echo $template->height/2;?>,2);"/> %
								</td>
							</tr>
							<tr>
								<td><?php echo lang('height');?>:</td>
								<td>
									<input class="num-box" id="areaHeight" style="width:35px;" onblur="template.index.changeH(<?php echo $template->width/2;?>,<?php echo $template->height/2;?>,1);"/> px
								</td>
								<td><?php echo lang('percent');?>:</td>
								<td>
									<input class="num-box" id="areaHeightPercent" style="width:50px;" onblur="template.index.changeH(<?php echo $template->width/2;?>,<?php echo $template->height/2;?>,2);"/> %
								</td>
							</tr>
						</table>
					</p>
				</div>
			</div>
		</td>
	</tr>
</table>
<p class="btn-center" style="margin-top:10px">
	<?php if($auth > 0 && !$using):?>
	<a class="btn-01" href="javascript:void(0);" id="save" ><span><?php echo lang('button.save');?></span></a>
	<?php endif;?>
	<a class="btn-01" href="javascript:void(0);" onclick="template.screen.goTemplate(<?php echo $template->system;?>)" ><span><?php echo lang('button.back');?></span></a>
</p>

<script type="text/javascript">
	
	template.screen.id=<?php echo $id;?>;
	template.screen.template_type = <?php echo $template->template_type;?>;
	template.screen.width =<?php echo $width;?>;
	template.screen.height =<?php echo $height;?>;
	template.screen.realWidth =<?php echo $template->width;?>;
	template.screen.realHeight =<?php echo $template->height;?>;
	<?php if(false):?>
	template.screen.maxDateRealWidth =<?php echo $template->width;?>;
	template.screen.maxDateRealHeight =<?php echo $template->height;?>;
	template.screen.maxWeatherRealWidth =<?php echo $template->width;?>;
	template.screen.maxWeatherRealHeight =<?php echo $template->height;?>;
	<?php endif;?>
	template.screen.warnSpace = '<?php echo lang('warn.screen.space');?>';
	template.screen.warnOverlap = '<?php echo lang('warn.screen.overlap');?>';
	template.screen.warnLogo = '<?php echo lang('area.logo.tip');?>';
	template.screen.warnVideo = '<?php echo lang('area.video.tip');?>';
	<?php if($using || $auth < $GROUP):?>
	template.screen.readonly=true;
	<?php endif;?>
	template.screen.init();
	<?php
	if(isset($area_list) && !empty($area_list)){
		$img_index = 1;
		foreach($area_list as $area){
			switch($area->area_type){
				case $this->config->item('area_type_bg'):
					echo 'template.screen.initBg('.$area->id.','.$area->media_id.',"'.$area->main_url.'",0);';
					break;
				case $this->config->item('area_type_movie'):
					echo 'template.screen.initMovie('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->zindex.');';
					break;
				case $this->config->item('area_type_image'):
					echo 'template.screen.initImage('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->zindex.','.$img_index.');';
					$img_index++;
					break;
				case $this->config->item('area_type_text'):
					echo 'template.screen.initText('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.',1);';
					break;
				case $this->config->item('area_type_staticText'):
					echo 'template.screen.initStaticText('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.',1);';
					break;
				case $this->config->item('area_type_date'):
					echo 'template.screen.initDate('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.',1);';
					break;
				case $this->config->item('area_type_time'):
					echo 'template.screen.initTime('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->zindex.');';
					break;
				case $this->config->item('area_type_weather'):
					echo 'template.screen.initWeather('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.',1);';
					break;
				case $this->config->item('area_type_webpage'):
					echo 'template.screen.initWebpage('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->zindex.');';
					break;
				case $this->config->item('area_type_mask'):
					echo 'template.screen.initMask('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->zindex.');';
					break;
				case $this->config->item('area_type_logo'):
					echo 'template.screen.initLogo('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->media_id.',"'.$area->tiny_url.'",100);';
					break;
					
			}
		}
	}
	?>
</script>