<?php if (isset($players)):?>

<div>
<H3 style="padding-bottom:5px;">Selected Players</H3>
<div style="height:600px;width:650px;overflow:auto;padding-bottom:5px;">
<table class="table-list" width="100%" >
<tr>
<th>Name</th>
<th>Least Free</th>
</tr>
<?php foreach ($players as $player):?>

<tr>
<td><?php echo $player['name']?></td>
<td><?php echo $player['least_free']?></td>
</tr>

<?php endforeach?>
</table>
</div>
<?php if (isset($ob_players)&&count($ob_players)):?>
<H3 style="padding-bottom:5px;">Overbooked Players</H3>
<div style="height:500px;width:650px;overflow:auto;">
<table class="table-list" width="100%" >
<tr>
<th></th>
<th>Name</th>
<th>Least Free</th>
<th>Shared Quota</th>
</tr>
<?php foreach ($ob_players as $player):?>

<tr>
<td><input type="checkbox" name="obids" value="<?php echo $player['id'];?>" checked="checked"/></td>
<td><?php echo $player['name']?></td>
<td><?php echo $player['least_free']?></td>
<td><?php echo $player['quota']."%";?></td>
</tr>


<?php endforeach?>
  <!--
			<tr>
			  <td>
              <a href="javascript:void(0);" class="button"  onclick="campaign.exclude_player();">Exclude Player</a>
              </td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
   -->
</table>

<?php endif;?>
</div>
</div>
<?php endif;?>
