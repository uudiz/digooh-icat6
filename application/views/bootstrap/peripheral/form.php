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
			<form id="dataForm" action="/Peripheral_controller/do_save">
				<div class="card-body">
					<div id="validateTips"> </div>
					<div class="row g-3">

						<div class="col-12">
							<label for="name"><?php echo lang('name'); ?></label>
							<input type="text" class="form-control" name="name" required value="<?php if (isset($data->name)) echo $data->name; ?>" />
						</div>
						<div class="col-12">
							<label for="descr"><?php echo lang("desc"); ?></label>
							<textarea class="form-control" name="descr" rows="2"><?php if (isset($data->descr)) echo $data->descr; ?></textarea>
						</div>
						<div class="row">
							<div class="col-auto">
								<label for="address"><?php echo lang('address'); ?></label>
								<input type="number" min=1 max=255 class="form-control" name="address" required value="<?php if (isset($data->address)) echo $data->address; ?>" />
							</div>

							<div class="col-auto">
								<label for="baudrate"><?php echo lang('baudrate'); ?></label>
								<input type="number" min=0 class="form-control" name="baudrate" required value="<?php if (isset($data->baudrate)) echo $data->baudrate; ?>" />
							</div>

							<div class="col-auto">
								<label for="parity"><?php echo lang('parity'); ?></label>
								<select class="form-select" name="parity" disabled>
									<option value=0 <?php if (isset($data->parity) && $data->parity == 0) : ?>selected<?php endif ?>>none</option>
									<option value=1 <?php if (isset($data->parity) && $data->parity == 1) : ?>selected<?php endif ?>>even</option>
									<option value=2 <?php if (isset($data->parity) && $data->parity == 2) : ?>selected<?php endif ?>>odd</option>
									<option value=3 <?php if (isset($data->parity) && $data->parity == 3) : ?>selected<?php endif ?>>mark</option>
									<option value=4 <?php if (isset($data->parity) && $data->parity == 4) : ?>selected<?php endif ?>>space</option>
								</select>
							</div>
							<div class="col-auto">
								<label for="stop_bits"><?php echo lang('stop_bit'); ?></label>
								<select class="form-select" name="stop_bits" disabled>
									<option <?php if (isset($data->stop_bits) && $data->stop_bits == "1") : ?>selected<?php endif ?>>1</option>
									<option <?php if (isset($data->stop_bits) && $data->stop_bits == "1.5") : ?>selected<?php endif ?>>1.5</option>
									<option <?php if (isset($data->stop_bits) && $data->stop_bits == "2") : ?>selected<?php endif ?>>2</option>

								</select>
							</div>
							<div class="col-auto">
								<label for="data_bits"><?php echo lang('data_bit'); ?></label>
								<select class="form-select" name="data_bits" disabled>
									<option <?php if (isset($data->data_bits) && $data->data_bits == "5") : ?>selected<?php endif ?>>5</option>
									<option <?php if (isset($data->data_bits) && $data->data_bits == "6") : ?>selected<?php endif ?>>6</option>
									<option <?php if (isset($data->data_bits) && $data->data_bits == "7") : ?>selected<?php endif ?>>7</option>
									<option <?php if (!isset($data->data_bits) || (isset($data->data_bits) && $data->data_bits == "8")) : ?>selected<?php endif ?>>8</option>
								</select>
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
						<a class="btn  btn-outline-primary" href="/Peripheral_controller"><i class="bi bi-x-circle"></i><?php echo lang('button.cancel'); ?></a>
					</div>
			</form>
		</div>
	</div>
</div>