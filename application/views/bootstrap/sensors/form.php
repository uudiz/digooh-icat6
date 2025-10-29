<script src="/assets/bootstrap/js/jquery.validate.min.js"></script>
<?php if ($lang == 'germany') : ?>
	<script src="/assets/js/validation/messages_de.js"></script>
<?php endif ?>
<?php
$this->load->view("bootstrap/players/player_map");
?>
<style>
	.select2-selection__rendered {
		overflow: auto;
		max-height: 200px !important;
	}
</style>
<div class="row">
	<div class="col-12 col-lg-8 m-auto pt-3 pb-2 mb-3">
		<div class="card">
			<div class="card-header">
				<h2><?php echo $title ?></h2>
			</div>
			<form id="dataForm" action="/Threshold_controller/do_save">
				<div class="card-body">
					<div id="validateTips"> </div>
					<div class="row g-3">

						<div class="mb-3">
							<label for="name"><?php echo lang('name'); ?></label>
							<input type="text" class="form-control" name="name" required value="<?php if (isset($data->name)) echo $data->name; ?>" />
						</div>
						<div class="mb-3">
							<label for="descr"><?php echo lang("desc"); ?></label>
							<textarea class="form-control" name="descr" rows="2"><?php if (isset($data->descr)) echo $data->descr; ?></textarea>
						</div>
						<div class="mb-3">
							<div class="form-label"><?php echo lang('temperature'); ?></div>
							<div class="row">
								<div class="col-md-4">
									<label for="min_temp"><?php echo lang('minimum'); ?></label>
									<input type="text" class="form-control" name="min_temp" required value="<?php if (isset($data->min_temp)) echo $data->min_temp; ?>" />
								</div>
								<div class="col-md-4">
									<label for="max_temp"><?php echo lang('maximum'); ?></label>
									<input type="number" class="form-control" name="max_temp" required value="<?php if (isset($data->max_temp)) echo $data->max_temp; ?>" />
								</div>
							</div>
						</div>
						<div class="mb-3">
							<div class="form-label"><?php echo lang('humidity'); ?></div>
							<div class="row">
								<div class="col-md-4">
									<label for="min_humidity"><?php echo lang('minimum'); ?></label>
									<input type="text" class="form-control" name="min_humidity" required value="<?php if (isset($data->min_humidity)) echo $data->min_humidity; ?>" />
								</div>
								<div class="col-md-4">
									<label for="max_humidity"><?php echo lang('maximum'); ?></label>
									<input type="number" class="form-control" name="max_humidity" required value="<?php if (isset($data->max_humidity)) echo $data->max_humidity; ?>" />
								</div>
							</div>
						</div>
						<div class="mb-3">
							<div class="form-label"><?php echo lang('power_consumption'); ?></div>
							<div class="row">
								<div class="col-md-4">
									<label for="min_power"><?php echo lang('minimum'); ?></label>
									<input type="text" class="form-control" name="min_power" required value="<?php if (isset($data->min_power)) echo $data->min_power; ?>" />
								</div>
								<div class="col-md-4">
									<label for="max_power"><?php echo lang('maximum'); ?></label>
									<input type="number" class="form-control" name="max_power" required value="<?php if (isset($data->max_power)) echo $data->max_power; ?>" />
								</div>
							</div>
						</div>



						<div class="col-12">
							<label for="players-select-options"><?php echo lang('player'); ?>
								<a data-bs-toggle="modal" data-bs-target="#playerModal">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search text-blue" viewBox="0 0 16 16">
										<path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
									</svg>
								</a>
							</label>

							<select data-placeholder="" id="players-select-options" name="players" class="form-select select2" multiple style="max-height:200px">
								<?php foreach ($players as $player) : ?>
									<?php if ($player->name) : ?>
										<option value="<?php echo $player->id; ?>" <?php if (isset($data->players) && is_array($data->players) && in_array($player->id, $data->players)) : ?>selected<?php endif; ?>><?php echo $player->name; ?></option>
									<?php endif; ?>
								<?php endforeach; ?>

							</select>
						</div>
						<input type="hidden" id="id" name="id" value="<?php echo isset($data->id) ? $data->id : 0; ?>" />

					</div>
					<div class="card-footer">
						<button class="btn btn-outline-primary" type="submit"><i class="bi bi-cloud-arrow-up"></i><?php echo lang('button.save'); ?></button>
						<a class="btn  btn-outline-primary" href="/Threshold_controller"><i class="bi bi-x-circle"></i><?php echo lang('button.cancel'); ?></a>
					</div>
			</form>
		</div>
	</div>
</div>