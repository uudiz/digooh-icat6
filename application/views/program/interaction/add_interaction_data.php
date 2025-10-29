<div class="icon-list">
	<span class="page-name"><?php echo $interaction->name;?></span>
 	<ul>
 		<li style="list-style: none;"><img id="bg" src="/images/icons/42-bg.png" title="<?php echo lang('bg');?>" /></li>
     	<!--
     	<li style="list-style: none;"><img id="logo" src="/images/icons/42-logo.png" title="<?php echo lang('logo');?>" /></li>
		-->
		<li style="list-style: none;"><img id="movie" src="/images/icons/42-video.png"  title="<?php echo lang('video');?>" /></li>
     	<li style="list-style: none;"><img id="image1" src="/images/icons/42-image.png"  title="<?php echo lang('image1');?>" /></li>
	 	<li style="list-style: none;"><img id="image2" src="/images/icons/42-image.png"  title="<?php echo lang('image2');?>" /></li>
	 	<li style="list-style: none;"><img id="image3" src="/images/icons/42-image.png"  title="<?php echo lang('image3');?>" /></li>
	 	<li style="list-style: none;"><img id="image4" src="/images/icons/42-image.png"  title="<?php echo lang('image4');?>" /></li>
     	<li style="list-style: none;"><img id="webpage" src="/images/icons/42-webpage.png"  title="<?php echo lang('webpage');?>" /></li>
     	<li style="list-style: none;"><img id="date" src="/images/icons/42-date.png" title="<?php echo lang('date');?>" /></li>
     	<li style="list-style: none;"><img id="time" src="/images/icons/42-05.png" title="<?php echo lang('time');?>" /></li>
     	<li style="list-style: none;"><img id="weather" src="/images/icons/42-weather.png" title="<?php echo lang('weather');?>" /></li>
     	<li style="list-style: none;"><img id="text" src="/images/icons/42-text.png"  title="<?php echo lang('text');?>" /></li>
     	<li style="list-style: none;"><img id="staticText" src="/images/icons/43-stext.png"  title="<?php echo lang('static.text');?>" /></li>
     	<li style="list-style: none;"><img id="bton" src="/images/icons/42-btn.png"  title="<?php echo lang('btn');?>" /></li>
     	<!--
     	<li style="list-style: none;"><img id="btnGroup" src="/images/icons/42-btn.png"  title="<?php echo lang('btnGroup');?>" /></li>
		-->
	</ul>
</div>
<div class="clear"></div>
<?php if($using):?>
<div class="information"><?php echo lang('warn.interaction.readonly');?></div>
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
						case $this->config->item('area_type_bg'):
							echo '<img id="op_bg" src="/images/icons/42-08.gif" width="16" height="16" title="'.lang('bg').'" />';
							break;
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
						case $this->config->item('area_type_interaction'):
							echo '<img id="op_interaction" src="/images/icons/16-11.gif" width="16" height="16" title="'.lang('interaction').'" />';
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
			<div id="centerframe" style="position:relative; width: <?php echo $width;?>px; height: <?php echo $height;?>px;"></div>
			<!--
			<div style="position:relative; width: <?php echo $width;?>px; height: <?php echo $height;?>px;" class="gray-area" id="screen">
				<img id="screenbg" style="position: absolute; top: 0px; left:0px; z-index:1;" width="0" height="0" />
			</div>
			-->
		</td>
		<td valign="top" >
			<div style="width: 320px; height: <?php echo $height;?>px; margin:0px 10px;">
				<div id="areaInfo" style="width:320px;height:260px;overflow:hidden;" class="gray-area from-panel">
					<!--
					<p style="font-size:14px;font-weight:bold;"><?php echo lang('screen');?></p>
					-->
					<p style="font-size:14px;font-weight:bold;">Attribute</p>
					<p>
						<table>
							<tr>
								<td width="20px"><?php echo lang('width');?>:</td>
								<td><?php echo $interaction->width;?>px</td>
								<td><?php echo lang('height');?>:</td>
								<td><?php echo $interaction->height;?>px</td>
							</tr>
						</table>
					</p>
					<p id="areaTitle" style="font-size:14px;font-weight:bold;"></p>
					<p>
						<table>
							<tr>
								<td><?php echo lang('x');?>:</td>
								<td>
									<input class="num-box" type="text" id="areaX" style="width:35px;" onblur="interaction.changeX(<?php echo $interaction->width/2;?>,<?php echo $interaction->height/2;?>);"/> px
								</td>
								<td></td>
								<td>
									<input type="hidden" id="areaChange" value=""/>
								</td>
							</tr>
							<tr>
								<td><?php echo lang('y');?>:</td>
								<td>
									<input class="num-box" id="areaY" style="width:35px;" onblur="interaction.changeY(<?php echo $interaction->width/2;?>,<?php echo $interaction->height/2;?>);"/> px
								</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td><?php echo lang('width');?>:</td>
								<td>
									<input class="num-box" id="areaWidth" style="width:35px;" onblur="interaction.changeW(<?php echo $interaction->width/2;?>,<?php echo $interaction->height/2;?>,1);"/> px
								</td>
								<td><?php echo lang('percent');?>:</td>
								<td>
									<input class="num-box" id="areaWidthPercent" style="width:50px;" onblur="interaction.changeW(<?php echo $interaction->width/2;?>,<?php echo $interaction->height/2;?>,2);"/> %
								</td>
							</tr>
							<tr>
								<td><?php echo lang('height');?>:</td>
								<td>
									<input class="num-box" id="areaHeight" style="width:35px;" onblur="interaction.changeH(<?php echo $interaction->width/2;?>,<?php echo $interaction->height/2;?>,1);"/> px
								</td>
								<td><?php echo lang('percent');?>:</td>
								<td>
									<input class="num-box" id="areaHeightPercent" style="width:50px;" onblur="interaction.changeH(<?php echo $interaction->width/2;?>,<?php echo $interaction->height/2;?>,2);"/> %
								</td>
							</tr>
						</table>
					</p>
				</div>
				<div style="width:320px;height:280px;overflow:hidden; display: none;" id="div_touch" class="gray-area from-panel">
					<table>
						<tr>
							<td width="110px"><?php echo lang('interaction.name');?>:</td>
							<td>
								<input type="text" id="touchName" name="touchName" style="width:150px;" value="<?php echo $interaction->name;?>"/>
							</td>
						</tr>

						<tr>
							<td width="110px"><?php echo lang('touch.resolution');?>:</td>
							<td>
								<input type="text" id="resolution" name="resolution" style="width:150px;" readonly value="<?php echo $interaction->width.'X'.$interaction->height;?>"/>
							</td>
						</tr>

						<tr>
							<td width="110px"><?php echo lang('touch.timeout.period');?>:</td>
							<td>
								<input type="text" id="period" name="period" style="width:100px;" value="<?php echo $interaction->period; ?>"/>(HH:MM)
							</td>
						</tr>
						<tr>
							<td width="110px"><?php echo lang('touch.timeout.action');?>:</td>
							<td>
								<select id="action" name="action">
									<?php for($i = 1; $i <= count($action_list); $i++):?>
									<option value="<?php echo $i;?>" <?php if($i == $interaction->action):?>selected="selected"<?php endif;?>><?php echo $action_list[$i];?></option>
									<?php endfor;?>
								</select>
							</td>
						</tr>
					</table>
				</div>
				<div style="width:320px;height:280px;overflow:hidden; display: none;" id="div_page2" class="gray-area from-panel">
					<table>
						<tr>
							<td width="110px"><?php echo lang('page.color');?>:</td>
							<td>
								<input type="text" id="pageColor" name="pageColor" style="width:150px;" value=""/>
							</td>
						</tr>
						<tr>
							<td width="110px"><?php echo lang('page.model');?>:</td>
							<td>
								<select id="pageModel" name="pageModel">
									<?php for($i = 1; $i <= count($model_list); $i++):?>
									<option value="<?php echo $i;?>"><?php echo $model_list[$i];?></option>
									<?php endfor;?>
								</select>
							</td>
						</tr>
						<tr>
							<td width="110px"><?php echo lang('page.fill');?>:</td>
							<td>
								<select id="pageFill" name="pageFill">
									<?php for($i = 1; $i <= count($fill_list); $i++):?>
									<option value="<?php echo $i;?>"><?php echo $fill_list[$i];?></option>
									<?php endfor;?>
								</select>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</td>
		<td valign="top">
			<div class="content_wrap" id="areaTree" style="position:absolute;width:280px; margin:0px 0px;" class="gray-area from-panel">
				<div class="zTreeDemoBackground left">
					<ul id="tree" class="ztree"></ul>
				</div>
			</div>
		</td>
	</tr>
</table>

<p class="btn-center" style="margin-top:10px">
	<?php if($auth > 0 && !$using):?>
	<a class="btn-01" href="javascript:void(0);" id="save" ><span><?php echo lang('button.save');?></span></a>
	<?php endif;?>
	<a class="btn-01" onclick="interaction.screen.goTemplate(0);" href="javascript:void(0);" ><span><?php echo lang('button.back');?></span></a>
</p>
<?php if($auth > 0 && !$using):?>
<div id="rMenu">
	<ul>
		<li id="m_del"><a href="javascript:void(0)" onclick="interaction.screen.removeTreeNode('<?php echo $id;?>');"><?php echo lang('del');?></a></li>
		<li id="m_copy"><a href="javascript:void(0)" onclick="interaction.screen.copyTreeNode('<?php echo $id;?>');"><?php echo lang('dulicate');?></a></li>
	</ul>
</div>
<?php endif;?>
<script type="text/javascript">
	$(function() {
		$('#touchName').keyup(function() {
			$('#tree_1_span').html($('#touchName').val());
		});
		<?php if($auth > 0 && !$using):?>
		var addStr = '<a href="javascript:void(0);" onClick="interaction.screen.addTreePage()" class="addPage" title="Add Page">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Add Page</a>';
		$('#tree_1_a').after(addStr);
		<?php endif;?>
	});
	interaction.screen.id=<?php echo $id;?>;
	interaction.screen.width =<?php echo $width;?>;
	interaction.screen.height =<?php echo $height;?>;
	interaction.screen.realWidth =<?php echo $interaction->width;?>;
	interaction.screen.realHeight =<?php echo $interaction->height;?>;
	<?php if(false):?>
	interaction.screen.maxDateRealWidth =<?php echo $interaction->width;?>;
	interaction.screen.maxDateRealHeight =<?php echo $interaction->height;?>;
	interaction.screen.maxWeatherRealWidth =<?php echo $interaction->width;?>;
	interaction.screen.maxWeatherRealHeight =<?php echo $interaction->height;?>;
	<?php endif;?>
	interaction.screen.warnSpace = '<?php echo lang('warn.screen.space');?>';
	interaction.screen.warnOverlap = '<?php echo lang('warn.screen.overlap');?>';
	interaction.screen.warnLogo = '<?php echo lang('area.logo.tip');?>';
	interaction.screen.warnVideo = '<?php echo lang('area.video.tip');?>';
	<?php if($using || $auth < $GROUP):?>
	interaction.screen.readonly=true;
	<?php endif;?>
	interaction.screen.init();
	<?php
	if($treejson):
	?>
	var nodes = [<?php echo $treejson;?>];
	<?php else:?>
	var nodes = [
				{ id:1, pId:0, name:"Project", open:true, iconSkin:"touch", noR:true},
				{ id:2, pId:1, name:"Page1", open:true, iconSkin:"mainPage"},
			];
	<?php endif;?>
	interaction.screen.initTree(nodes, <?php echo $screenID; ?>, <?php echo $treeNodeCount; ?>);
	<?php
	if(isset($area_list) && !empty($area_list)){
		foreach($area_list as $area){
			switch($area->area_type){
				case $this->config->item('area_type_bg'):
					echo 'interaction.screen.initBg('.$area->id.','.$area->media_id.',"'.$area->main_url.'",'.$area->page_id.',0);';
					break;
				case $this->config->item('area_type_movie'):
					echo 'interaction.screen.initMovie('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->zindex.','.$area->page_id.');';
					break;
				case $this->config->item('area_type_image'):
					echo 'interaction.screen.initImage('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->zindex.','.$area->num.','.$area->page_id.');';
					break;
				case $this->config->item('area_type_text'):
					echo 'interaction.screen.initText('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->zindex.','.$area->page_id.');';
					break;
				case $this->config->item('area_type_staticText'):
					echo 'interaction.screen.initStaticText('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->zindex.','.$area->page_id.');';
					break;
				case $this->config->item('area_type_date'):
					echo 'interaction.screen.initDate('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->zindex.','.$area->page_id.');';
					break;
				case $this->config->item('area_type_time'):
					echo 'interaction.screen.initTime('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->zindex.','.$area->page_id.');';
					break;
				case $this->config->item('area_type_weather'):
					echo 'interaction.screen.initWeather('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->zindex.','.$area->page_id.');';
					break;
				case $this->config->item('area_type_webpage'):
					echo 'interaction.screen.initWebpage('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->zindex.','.$area->page_id.');';
					break;
				case $this->config->item('area_type_btn'):
					echo 'interaction.screen.initBtn('.$area->id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->zindex.','.$area->num.','.$area->page_id.');';
					break;
				/*
				case $this->config->item('area_type_interaction'):
					echo 'interaction.screen.initInteraction('.$area->id.',"'.$area->page_id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->zindex.');';
					break;
				case $this->config->item('area_type_logo'):
					echo 'interaction.screen.initLogo('.$area->id.',"'.$area->page_id.',"'.$area->name.'",'.$area->x.','.$area->y.','.$area->w.','.$area->h.','.$area->media_id.',"'.$area->tiny_url.'",100);';
					break;	
				*/
			}
		}
	}
	?>
</script>
<style type="text/css">
	div#rMenu{position:absolute; visibility:hidden;}
	div#rMenu ul{margin:0px; padding:0px; list-style:none;border: 0;}
	div#rMenu ul li{ margin: 0px 2px 0px 0px; padding: 0px; width: 120px; height: 35px; display: block; border-radius: 0px; box-shadow: #000 0px 0px 1px; border: 0; background-color: #F3F3F3; }
	div#rMenu ul li:hover ul{display:block;}
	div#rMenu ul li a{text-align:center; width:121px; height:37px; line-height:35px; display:block; text-decoration:none;}
	div#rMenu ul li ul{display:none;position:relative; top:-37px; left:121px;}
	div#rMenu ul li ul li{margin:0px 0px 2px 0px; padding:0px; background-color:#FFFFFF;}
	.addPage{
		background:url(../images/icons/icon-list.gif) 0 -2px no-repeat;
		margin: 0 0;
	}
</style>