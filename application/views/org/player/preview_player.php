<style type="text/css">
	#screen { background: #000000}
	#TB_window{background-color: #1b1b1b;}
</style>
	<?php
if($pls_id) {
?>
<div style="position:relative; z-index: -2; width: <?php echo 0.7*$width; ?>px; height: <?php echo 0.7*$height; ?>px; " id="screen">
	<?php foreach($area_list as $item):?>	
		<?php if($item->name=='BG'):?>
		<img id="screenbg" style="position: absolute; top: 0px; left:0px; z-index: 1;" width="<?php echo 0.7*$width; ?>" height="<?php echo 0.7*$height; ?>" src="<?php echo $item->main_url; ?>" />
		<?php endif;?>
		
		<?php if($item->name=='Movie/Photo' || $item->name=='Movie'):?>
		<?php
		if(!isset($item->main_url)) {
		?>
		<div id="player" style="display:block;width:<?php echo 0.7*$item->w; ?>px;height:<?php echo 0.7*$item->h; ?>px;position:absolute;z-index:<?php echo $item->zindex; ?>;top:<?php echo 0.7*$item->y; ?>px;left:<?php echo 0.7*$item->x; ?>px"> 
			<font style="color: #fff; font-size: 18px;">
				No preview due to content not stored in the local server!
			</font>
		</div>	
		<?php
		}else{
		?>
		<div id="player" style="display:block;width:<?php echo 0.7*$item->w; ?>px;height:<?php echo 0.7*$item->h; ?>px;position:absolute;z-index:<?php echo $item->zindex; ?>;top:<?php echo 0.7*$item->y; ?>px;left:<?php echo 0.7*$item->x; ?>px"> 
		</div>
		<?php
		}?>
		
		<script>
		$(function() {
			flowplayer("player", "/static/js/flowplayer/flowplayer-3.2.7.swf", {
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
		
		<?php if($item->name=='Image1'):?>
		<div id="image1" style="display:block;width:<?php echo 0.7*$item->w; ?>px;height:<?php echo 0.7*$item->h; ?>px;position:absolute;z-index:<?php echo $item->zindex; ?>;top:<?php echo 0.7*$item->y; ?>px;left:<?php echo 0.7*$item->x; ?>px"> 
		</div>
		
		<script>
		$(function() {
			flowplayer("image1", "/static/js/flowplayer/flowplayer-3.2.7.swf", {
			clip:{ autoPlay: true,  autoBuffering: true},plugins:{controls: null},
			playlist: [
				<?php foreach ($item->main_url as $arr){
					$path=pathinfo($arr);
					if($arr != end($item->main_url)){
						if($path['extension']=='jpg') echo "{url: '$arr',duration: 10},";
						else echo "{url: '$arr'},";
					}else{
						if($path['extension']=='jpg') echo "{url: '$arr',duration: 10}";
						else echo "{url: '$arr'}";
					}
				}?>
			]
			}).playlist("#myimage1", {loop:true});
		});
		</script>
		<div class="clips petrol" style="display:none" id="myimage1">
			<a href="${url}"></a>
		</div>	
		<?php endif;?>
		
		<?php if($item->name=='Image2'):?>
		<div id="image2" style="display:block;width:<?php echo 0.7*$item->w; ?>px;height:<?php echo 0.7*$item->h; ?>px;position:absolute;z-index:<?php echo $item->zindex; ?>;top:<?php echo 0.7*$item->y; ?>px;left:<?php echo 0.7*$item->x; ?>px"> 
		</div>
		
		<script>
		$(function() {
			flowplayer("image2", "/static/js/flowplayer/flowplayer-3.2.7.swf", {
			clip:{ autoPlay: true,  autoBuffering: true},plugins:{controls: null},
			playlist: [
				<?php foreach ($item->main_url as $arr){
					$path=pathinfo($arr);
					if($arr != end($item->main_url)){
						if($path['extension']=='jpg') echo "{url: '$arr',duration: 10},";
						else echo "{url: '$arr'},";
					}else{
						if($path['extension']=='jpg') echo "{url: '$arr',duration: 10}";
						else echo "{url: '$arr'}";
					}
				}?>
			]
			}).playlist("#myimage2", {loop:true});
		});
		</script>
		<div class="clips petrol" style="display:none" id="myimage2">
			<a href="${url}"></a>
		</div>	
		<?php endif;?>
		
		<?php if($item->name=='Image3'):?>
		<div id="image3" style="display:block;width:<?php echo 0.7*$item->w; ?>px;height:<?php echo 0.7*$item->h; ?>px;position:absolute;z-index:<?php echo $item->zindex; ?>;top:<?php echo 0.7*$item->y; ?>px;left:<?php echo 0.7*$item->x; ?>px"> 
		</div>
		
		<script>
		$(function() {
			flowplayer("image3", "/static/js/flowplayer/flowplayer-3.2.7.swf", {
			clip:{ autoPlay: true,  autoBuffering: true},plugins:{controls: null},
			playlist: [
				<?php foreach ($item->main_url as $arr){
					$path=pathinfo($arr);
					if($arr != end($item->main_url)){
						if($path['extension']=='jpg') echo "{url: '$arr',duration: 10},";
						else echo "{url: '$arr'},";
					}else{
						if($path['extension']=='jpg') echo "{url: '$arr',duration: 10}";
						else echo "{url: '$arr'}";
					}
				}?>
			]
			}).playlist("#myimage3", {loop:true});
		});
		</script>
		<div class="clips petrol" style="display:none" id="myimage3">
			<a href="${url}"></a>
		</div>	
		<?php endif;?>

		<?php if($item->name=='Image4'):?>
		<div id="image4" style="display:block;width:<?php echo 0.7*$item->w; ?>px;height:<?php echo 0.7*$item->h; ?>px;position:absolute;z-index:<?php echo $item->zindex; ?>;top:<?php echo 0.7*$item->y; ?>px;left:<?php echo 0.7*$item->x; ?>px"> 
		</div>
		
		<script>
		$(function() {
			flowplayer("image4", "/static/js/flowplayer/flowplayer-3.2.7.swf", {
			clip:{ autoPlay: true,  autoBuffering: true},plugins:{controls: null},
			playlist: [
				<?php foreach ($item->main_url as $arr){
					$path=pathinfo($arr);
					if($arr != end($item->main_url)){
						if($path['extension']=='jpg') echo "{url: '$arr',duration: 10},";
						else echo "{url: '$arr'},";
					}else{
						if($path['extension']=='jpg') echo "{url: '$arr',duration: 10}";
						else echo "{url: '$arr'}";
					}
				}?>
			]
			}).playlist("#myimage4", {loop:true});
		});
		</script>
		<div class="clips petrol" style="display:none" id="myimage4">
			<a href="${url}"></a>
		</div>	
		<?php endif;?>
		
		<?php if($item->name=='Logo'):?>
			<img src="<?php echo $item->tiny_url; ?>" ID="logo" style="position:absolute;z-index:999;top:<?php echo 0.7*$item->y; ?>px;left:<?php echo 0.7*$item->x; ?>px;width:<?php echo 0.7*$item->w; ?>px; height:<?php echo 0.7*$item->h; ?>px;">
		<?php endif;?>
		
		<?php if($item->name=='Text'):?>
		
		<div id="scroller_container" style="overflow:hidden;position:absolute;z-index:<?php echo $item->zindex?>;top:<?php echo 0.7*$item->y;?>px;left:<?php echo 0.7*$item->x;?>px;width:<?php echo 0.7*($item->w+1);?>px; height:<?php echo 0.7*$item->h;?>px;background:<?php echo $item->setting->bg_color;?>;">
		 	<div id="scroller" style="white-space:nowrap;overflow:hidden;line-height:<?php echo 0.7*$item->h;?>px;font-family:<?php echo $item->setting->font_family;?>;font-size: 16px; color:<?php echo $item->setting->color;?>;">
		   		<marquee scrollAmount=2 width="<?php echo 0.7*($item->w+1);?>" height="<?php echo 0.7*$item->h;?>" direction="left">
					<span>
						<?php 
						if(isset($item->setting->content) && !empty($item->setting->content)) {
							echo $item->setting->content;
						}else {
							echo "&nbsp;";
						}
						?>
					</span>
				</marquee>
		 	</div>
		</div>
		<?php endif;?>
		<?php if($item->name=='Webpage'):?>
		<div id="webpage" style="position:absolute;z-index:<?php echo $item->zindex?>;width:<?php echo 0.7*$item->w;?>px;height:<?php echo 0.7*$item->h;?>px;top:<?php echo 0.7*$item->y;?>px;left:<?php echo 0.7*$item->x;?>px;">
			<img src="/images/icons/web.jpg" height="100%" width="100%">
		</div>
		<?php endif;?>
		<?php if($item->name=='Date'):?>
		<div id="date" style="position:absolute; z-index:<?php echo $item->zindex; ?>; width:<?php echo 0.7*$item->w; ?>px; height:<?php echo 0.7*$item->h; ?>px; top:<?php echo 0.7*$item->y; ?>px; left:<?php echo 0.7*$item->x; ?>px; background:<?php echo $item->setting->bg_color;?>;">
			<p style=" font-family:<?php echo $item->setting->font_family;?>;text-align:center;color:<?php echo $item->setting->color;?>">
				<strong>
					<?php
					if($player_type) {
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
		<div id="time" style="position:absolute; z-index:<?php echo $item->zindex; ?>; width:<?php echo 0.7*$item->w; ?>px; height:<?php echo 0.7*$item->h; ?>px; top:<?php echo 0.7*$item->y; ?>px; left:<?php echo 0.7*$item->x; ?>px; background:<?php echo $item->setting->bg_color;?>;">
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
		<?php if($item->name=='Weather'):?>
		<div id="weather" style="position:absolute; z-index:<?php echo $item->zindex?>; width:<?php echo 0.7*$item->w;?>px; height:<?php echo 0.7*$item->h;?>px; top:<?php echo 0.7*$item->y;?>px; left:<?php echo 0.7*$item->x;?>px; background:<?php echo $item->setting->bg_color;?>;">
			<?php if(!$player_type) {?>
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
<?php	
}else {
?>
<div style="position:relative; z-index: -2; width: 702px; height: 395px;" id="screen">
	<font style="color: #fff; size: 18px;">No preview due to content not stored in the local server!</font>
</div>
<?php
}
?>