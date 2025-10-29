
<?php if (isset($sel_players)&&$sel_players&&count($sel_players)):?>
	<div class="gray-area" >
		<h1>Selected Devices:<?php echo count($sel_players); ?></h1>
		<ul style="margin-top:10px">
			<?php foreach ($sel_players as $player):?>
			<li><?php echo $player->name;?></li>
			<?php endforeach?>
		
		</ul>
    </div>
<?php endif?>
	  