<div>
  <div class="row pb-2">
    <div class="col-auto">
      <label><?php echo lang('player.selected'); ?></label>
      <input type="text" class="form-control" id="player_sel" readonly value="<?php echo $player_num; ?> ">
    </div>
    <div class="col-auto">
      <label><?php echo lang('avarage.usage'); ?></label>
      <input type="text" class="form-control" id="ava_usage" readonly value="<?php echo isset($ava_used) ? $ava_used : 0 ?> ">
    </div>
    <div class="col-auto">
      <label><?php echo lang('ob.player.cnt'); ?></label>
      <input type="text" class="form-control" id="total_free" readonly value="<?php echo isset($ob_cnt) ? $ob_cnt : 0 ?> ">
    </div>
    <div class="col-auto">
      <label><?php echo lang('least.common'); ?></label>
      <input type="text" class="form-control" id="least_common" readonly value="<?php echo isset($lease_times) ? $lease_times : 0 ?>  ">
    </div>

    <?php if (isset($total_times)) : ?>
      <div class="col-auto">
        <label><?php echo lang('total.times'); ?></label>
        <input type="text" class="form-control" id="total_times" readonly value=" <?php echo isset($total_times) ? $total_times : 0 ?>">
      </div>

      <div class="col-auto">
        <label><?php echo lang('cost'); ?></label>
        <div class="input-group">
          <span class="input-group-text">€ </span>
          <input type="text" class="form-control" id="cost" readonly value="<?php echo isset($cost) ? $cost : 0 ?>">
        </div>
      </div>
    <?php endif ?>
  </div>
  <?php if (isset($players)) : ?>
    <H3 class="pt-3"><?php echo lang('selected.players') ?></H3>
    <div style="max-height:300px;overflow:auto;padding-bottom:5px;" class="card-table table-responsive">
      <table class="table table-vcenter" data-pagination="false">
        <thead>
          <tr>
            <th><?php echo lang('name') ?></th>
            <th><?php echo lang('least.free') ?></th>
          </tr>
        </thead>
        <?php foreach ($players as $player) : ?>
          <tr>
            <td><?php echo $player['name'] ?></td>
            <td><?php echo $player['least_free'] ?></td>
          </tr>
        <?php endforeach ?>
      </table>
    </div>
    <?php if (isset($ob_players) && count($ob_players)) : ?>
      <H3 class="pt-3">Overbooked:</H3>
      <div style="max-height:300px;overflow:auto;" class="card-table table-responsive">
        <table class="table table-vcenter">
          <thead>
            <tr>
              <th><?php echo lang('exclude.players') ?></th>
              <th><?php echo lang('name') ?></th>
              <th><?php echo lang('least.free') ?></th>
              <th>Shared Quota</th>
            </tr>
          </thead>
          <?php foreach ($ob_players as $player) : ?>

            <tr>
              <td><input type="checkbox" name="obIds" value="<?php echo $player['id']; ?>" checked="checked" /> </td>
              <td><?php echo $player['name'] ?></td>
              <td><?php echo $player['least_free'] ?></td>
              <td><?php echo $player['quota'] . "%"; ?></td>
            </tr>


          <?php endforeach ?>

        </table>
      </div>
    <?php endif; ?>
</div>
<?php endif; ?>