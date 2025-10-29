<script src="/assets/bootstrap/js/jquery.validate.min.js"></script>
<?php if ($lang == 'germany') : ?>
	<script src="/assets/js/validation/messages_de.js"></script>
<?php endif ?>

<div class="row">
	<div class="col-12 col-lg-8 m-auto pt-3 pb-2 mb-3">
		<div class="card">
			<div class="card-header">
				<h2><?php echo $title ?></h2>
			</div>
			<form id="dataForm" action="/Firmware_controller/do_save">
				<div class="card-body">
					<div id="validateTips"> </div>
					<div class="row g-3">

						<div class="col-12">
							<label for="name"><?php echo lang('name'); ?></label>
							<input type="text" class="form-control" name="name" required value="<?php if (isset($data->name)) echo $data->name; ?>" />
						</div>
						<div class="col-12">
							<label for="name"><?php echo lang('version'); ?></label>
							<input type="text" class="form-control" readonly value="<?php if (isset($data->version)) echo $data->version; ?>" />
						</div>
						<div class="col-12">
							<label for="descr"><?php echo lang("desc"); ?></label>
							<textarea type="text" class="form-control" name="descr" rows="2"><?php if (isset($data->descr)) echo $data->descr; ?></textarea>
						</div>

						<input type="hidden" id="id" name="id" value="<?php echo isset($data->id) ? $data->id : 0; ?>" />

					</div>
					<div class="card-footer">
						<button class="btn btn-outline-primary" type="submit"><i class="bi bi-cloud-arrow-up"></i><?php echo lang('button.save'); ?></button>
						<a class="btn  btn-outline-primary" href="/Firmware_controller"><i class="bi bi-x-circle"></i><?php echo lang('button.cancel'); ?></a>
					</div>
			</form>
		</div>
	</div>
</div>