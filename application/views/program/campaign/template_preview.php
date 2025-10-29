<!doctype html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">

	<script type="text/javascript" src="/static/js/flowplayer/flowplayer-3.2.6.min.js"></script>
	<script type="text/javascript" src="/static/js/jquery/jquery-1.7.1.js"></script>
	<script type="text/javascript" src="/static/js/flowplayer/flowplayer.playlist-3.2.10.min.js"></script>
	<script type="text/javascript" src="/static/js/flowplayer/jscroller-0.4.js"></script>
<style type="text/css">
	#screen { background: #000000}
	.btn-01,.btn-01:hover{
		background:url(/static/english/images/btn-bg.gif) no-repeat right -29px;
		padding-right:20px;
		display:inline-block;
		zoom:1;
		*display:inline;
		text-decoration: none;
		height:29px;
		line-height:27px;
		font-size:14px;
		font-weight:bold;
		color:#fff;
		margin-left:<?php echo $width*0.75;?>px;
		cursor: pointer;
	}

	.btn-01 span{
		background:url(/static/english/images/btn-bg.gif) no-repeat left 0;
		padding-left:20px;
		display:inline-block;
		zoom:1;
		color: #fff;
		*display:inline;
		height:29px;
		line-height:27px;
	}
</style>
</head>
<body>
<script type="text/javascript" src="/static/js/flowplayer/cycle2.js"></script>
<script src="/static/js/flowplayer/Transition/tile.js"></script>
<script src="/static/js/flowplayer/Transition/flip.js"></script>
<script src="/static/js/flowplayer/Transition/scrollvert.js"></script>
<div style="position:relative; z-index: -2; width: <?php echo $width;?>px; height: <?php echo $height;?>px; border:10px solid #1b1b1b;" id="screen">
	<?php foreach($area_list as $item):?>	
		<?php if($item->name=='BG'):?>
		<img id="screenbg" style="position: absolute; top: 0px; left:0px; z-index: -1;" width="<?php echo $width;?>" height="<?php echo $height?>" src="<?php echo $item->main_url?>" />
		<?php endif;?>
		
		<?php if($item->name=='Movie/Photo' || $item->name=='Movie'):?>
		<!--
		<div id="player"
		style="display:block;width:<?php echo $item->w;?>px;height:<?php echo $item->h;?>px;position:absolute;z-index:<?php echo $item->zindex?>;top:<?php echo $item->y;?>px;left:<?php echo $item->x;?>px"> 
		</div>-->
		<?php
		if(!isset($item->main_url)) {
		?>
		<div id="player" style="display:block;width:<?php echo $item->w;?>px;height:<?php echo $item->h;?>px;position:absolute;z-index:<?php echo $item->zindex?>;top:<?php echo $item->y;?>px;left:<?php echo $item->x;?>px"> 
			<font style="color: #fff; font-size: 18px;">
				No preview due to content not stored in the local server!
			</font>
		</div>
		<?php
		}else {
		?>
		<div id="player" style="display:block;width:<?php echo $item->w;?>px;height:<?php echo $item->h;?>px;position:absolute;z-index:<?php echo $item->zindex?>;top:<?php echo $item->y;?>px;left:<?php echo $item->x;?>px"> 
		</div>
		<?php
		}
		?>
		
		<script>
		$(function() {
			$f("player", "/static/js/flowplayer/flowplayer-3.2.7.swf", {
			clip:{ autoPlay: true,  autoBuffering: true},plugins:{controls: null},
			playlist: [
				<?php foreach ($item->main_url as $arr){
					$path=pathinfo($arr);
					if($arr != end($item->main_url)){
						if($path['extension']=='jpg') echo "{url: '$arr',duration: 10},";
						else echo "{url: '$arr'},";
					}else{
						if($path['extension']=='jpg') echo "{url: '$arr',duration: 10},";
						else echo "{url: '$arr'},";
					}
				}?>
			]
			}).playlist("#myplaylist", {loop:true});
		});
		</script>
		<div class="clips petrol" style="display:none" id="myplaylist">
			<a href="${url}"></a>
		</div>
		<?php endif;?>

		<?php if($item->name=='Image1' || $item->name=='Image2' || $item->name=='Image3' || $item->name=='Image4'):?>
			<div class="cycle-slideshow" id="image_<?php echo $item->id?>">
				<?php for ($i = 0; $i < count($item->main_url); $i++){
					$duration = isset($item->duration[$i])?($item->duration[$i]*1000):0;
					$trans=isset($item->transmode[$i])?$item->transmode[$i]:0;
					$vert='true';
					switch ($trans){
						case 1:
						case 2:
							$transmode = 'scrollHorz';
							break;
						case 3:
						case 4:
							$transmode = 'scrollVert';
							break;
						case 9:
							$transmode = 'tileBlind';
							break;
						case 10:
							$transmode = 'tileBlind';
							$vert = 'false';
							break;
						default:
							$transmode = '';
							break;
						}
						$image_url=isset($item->main_url[$i])?($item->main_url[$i]):'';
						echo "<img src='$image_url' data-cycle-fx=$transmode data-tile-vertical=$vert data-cycle-timeout=$duration>";
					}
				?>
			</div>
		<?php endif;?>
		
		<?php if($item->name=='Image1'):?>
			<style>
				#image_<?php echo $item->id?> { position:absolute;z-index:<?php echo $item->zindex?>;top:<?php echo $item->y;?>px;left:<?php echo $item->x;?>px;width:<?php echo $item->w;?>px; height:<?php echo $item->h;?>px}
				#image_<?php echo $item->id?> img { position:absolute;z-index:<?php echo $item->zindex?>;top:<?php echo $item->y;?>px;left:<?php echo $item->x;?>px;width:<?php echo $item->w;?>px; height:<?php echo $item->h;?>px}
			</style>
		<?php endif;?>
		
		<?php if($item->name=='Image2'):?>
			<style>
				#image_<?php echo $item->id?> { position:absolute;z-index:<?php echo $item->zindex?>;top:<?php echo $item->y;?>px;left:<?php echo $item->x;?>px;width:<?php echo $item->w;?>px; height:<?php echo $item->h;?>px}
				#image_<?php echo $item->id?> img { position:absolute;z-index:<?php echo $item->zindex?>;top:<?php echo $item->y;?>px;left:<?php echo $item->x;?>px;width:<?php echo $item->w;?>px; height:<?php echo $item->h;?>px}
			</style>
		<?php endif;?>
		
		<?php if($item->name=='Image3'):?>
			<style>
				#image_<?php echo $item->id?> { position:absolute;z-index:<?php echo $item->zindex?>;top:<?php echo $item->y;?>px;left:<?php echo $item->x;?>px;width:<?php echo $item->w;?>px; height:<?php echo $item->h;?>px}
				#image_<?php echo $item->id?> img { position:absolute;z-index:<?php echo $item->zindex?>;top:<?php echo $item->y;?>px;left:<?php echo $item->x;?>px;width:<?php echo $item->w;?>px; height:<?php echo $item->h;?>px}
			</style>
		<?php endif;?>
		<?php if($item->name=='Image4'):?>
			<style>
				#image_<?php echo $item->id?> { position:absolute;z-index:<?php echo $item->zindex?>;top:<?php echo $item->y;?>px;left:<?php echo $item->x;?>px;width:<?php echo $item->w;?>px; height:<?php echo $item->h;?>px}
				#image_<?php echo $item->id?> img { position:absolute;z-index:<?php echo $item->zindex?>;top:<?php echo $item->y;?>px;left:<?php echo $item->x;?>px;width:<?php echo $item->w;?>px; height:<?php echo $item->h;?>px}
			</style>
		<?php endif;?>

		<?php if($item->name=='Logo'):?>
			<img src="<?php echo $item->tiny_url;?>" ID="logo" style="position:absolute;z-index:<?php echo $item->zindex?>;top:<?php echo $item->y;?>px;left:<?php echo $item->x;?>px;width:<?php echo $item->w;?>px; height:<?php echo $item->h;?>px;">
		<?php endif;?>
		
		<?php if($item->name=='Text'):?>
		
		<div id="scroller_container" style="overflow:hidden;position:absolute;z-index:<?php echo $item->zindex?>;top:<?php echo $item->y;?>px;left:<?php echo $item->x;?>px;width:<?php echo $item->w+1;?>px; height:<?php echo $item->h;?>px;background:<?php echo $item->setting->bg_color;?>;">
		 <div id="scroller" style="white-space:nowrap;overflow:hidden;line-height:<?php echo $item->h;?>px;font-family:<?php echo $item->setting->font_family;?>;font-size: 16px; color:<?php echo $item->setting->color;?>;">
		   <span>
			<?php 
			  $n = $item->w > 560 ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			  if(isset($item->setting->content) && !empty($item->setting->content)){
			  	 echo $n.$item->setting->content;
			  }else {
			  	 echo "&nbsp;";
			  }
			?>
		  </span>
		 </div>
		</div>
		<script type="text/javascript">
		 //$(document).ready(function(){

		  // Add Scroller Object
		  $jScroller.add("#scroller_container","#scroller","left",<?php echo $item->setting->speed;?>);

		  // Start Autoscroller
		  $jScroller.start();
		// });
		</script>
		
		<?php endif;?>
		
		<?php if($item->name=='Date'):?>
		<div id="date" style="position:absolute;z-index:<?php echo $item->zindex?>;width:<?php echo $item->w;?>px;height:<?php echo $item->h;?>px;top:<?php echo $item->y;?>px;left:<?php echo $item->x;?>px;background:<?php echo $item->setting->bg_color;?>;">
			<p style=" font-family:<?php echo $item->setting->font_family;?>;text-align:center;color:<?php echo $item->setting->color;?>">
				<strong>
				<?php
				if($pls_type) {
					$arr_week = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
					switch($item->setting->style) {
						case 1:
							echo date("n/j/Y").'&nbsp;&nbsp;'.$arr_week[date('w')];
						break;
						case 2:
							echo date("j/n/Y").'&nbsp;&nbsp;'.$arr_week[date('w')];
						break;
						case 3:
							echo date("Y/n/j").'&nbsp;&nbsp;'.$arr_week[date('w')];
						break;
					}
				}else {
					echo date("Y/m/d").'&nbsp;&nbsp;'.date('H:i');
				}
				?>
				</strong>
			</p>
		</div>
		<?php endif;?>
		<?php if($item->name=='Time'):?>
		<div id="time" style="position:absolute;z-index:<?php echo $item->zindex?>;width:<?php echo $item->w;?>px;height:<?php echo $item->h;?>px;top:<?php echo $item->y;?>px;left:<?php echo $item->x;?>px;background:<?php echo $item->setting->bg_color;?>;">
			<p style=" font-family:<?php echo $item->setting->font_family;?>;text-align:center;color:<?php echo $item->setting->color;?>">
				<strong>
				<?php
				switch($item->setting->style) {
					case 1:
						echo date("H:i");
					break;
					case 2:
						if(date("H")>12) {
							echo (date("H")-12).':'.date("i").' PM';
						}else {
							echo date("H:i").' AM';
						}
					break;
				}
				?>
				</strong>
			</p>
		</div>
		<?php endif;?>
		
		<?php if($item->name=='Webpage'):?>
		<div id="webpage" style="position:absolute;z-index:<?php echo $item->zindex?>;width:<?php echo $item->w;?>px;height:<?php echo $item->h;?>px;top:<?php echo $item->y;?>px;left:<?php echo $item->x;?>px;">
			<img src="/images/icons/web.jpg" height="100%" width="100%">
		</div>
		<?php endif;?>

		<?php if($item->name=='Weather'):?>
		<div id="weather" style="position:absolute;z-index:<?php echo $item->zindex?>;width:<?php echo $item->w;?>px;height:<?php echo $item->h;?>px;top:<?php echo $item->y;?>px;left:<?php echo $item->x;?>px;background:<?php echo $item->setting->bg_color;?>;">
			<?php if(!$pls_type) {?>
			<p style="font-family:<?php echo $item->setting->font_family;?>;text-align:center; color:<?php echo $item->setting->color;?>">
				<?php
				if($w_city) {
				?>
				<strong><?php echo $w_city;?><br/>
				<?php echo $w_low;?>~<?php echo $w_high;?>
				<img style="ling-height:10px;" src="http://l.yimg.com/a/i/us/we/52/<?php echo $w_icon;?>.gif" width="26" height="26" />
				</strong>
				<?php }?>
			</p>
			<?php }else{
					if($item->setting->style == 4) {
			?>
			<table width="100%" style="font-family:<?php echo $item->setting->font_family;?>;text-align:center; color:<?php echo $item->setting->color;?>;">
				<tr>
					<td>Today</td>
					<td>NextOneDay</td>
					<td>NextTwoDay</td>
				</tr>
				<tr>
					<td><?php echo $w_low[0];?>~<?php echo $w_high[0];?></td>
					<td><?php echo $w_low[1];?>~<?php echo $w_high[1];?></td>
					<td><?php echo $w_low[2];?>~<?php echo $w_high[2];?></td>
				</tr>
				<tr>
					<td>
						<img style="ling-height:10px;" src="http://l.yimg.com/a/i/us/we/52/<?php echo $w_icon[0];?>.gif" width="23" height="23"/>
					</td>
					<td>
						<img style="ling-height:10px;" src="http://l.yimg.com/a/i/us/we/52/<?php echo $w_icon[1];?>.gif" width="23" height="23"/>
					</td>
					<td>
						<img style="ling-height:10px;" src="http://l.yimg.com/a/i/us/we/52/<?php echo $w_icon[2];?>.gif" width="23" height="23"/>
					</td>
				</tr>
			</table>
			<?php
					}else {
			?>
			<p style="font-family:<?php echo $item->setting->font_family;?>;text-align:center; color:<?php echo $item->setting->color;?>">
			<strong>Jinan<br/>
				<?php echo $w_low[0];?>~<?php echo $w_high[0];?>
				<img style="ling-height:10px;" src="http://l.yimg.com/a/i/us/we/52/<?php echo $w_icon[0];?>.gif" width="26" height="26" />
			</strong>
			<p>
			<?php
					}
			}?>
		</div>
		<?php endif;?>
		<?php endforeach;?>
	</div>
	<p class="btn-center">
		<a href="/campaign/" class="btn-01"><span><?php echo lang('button.back');?></span></a>
    </p>
</body>
</html>