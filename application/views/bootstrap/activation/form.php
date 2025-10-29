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
			<form id="dataForm" action="/ActivationController/do_save">
				<div class="card-body">
					<div id="validateTips"> </div>
					<div class="row g-3">

						<div class="md-3">
							<label for="mac" requied>MAC Address</label>
							<input type="text" class="form-control" name="mac" required value="<?php if (isset($data->mac)) echo $data->mac; ?>" />
						</div>

						<div class="md-3">
							<label for="descr"><?php echo lang("desc"); ?></label>
							<textarea type="text" class="form-control" name="descr" rows="2"><?php if (isset($data->descr)) echo $data->descr; ?></textarea>
						</div>
						<div class="md-3">
							<label for="mac">Company</label>
							<input type="text" class="form-control" name="company" value="<?php if (isset($data->company)) echo $data->company; ?>" />
						</div>

						<div class="md-3">
							<label class="form-check form-switch">
								<input class="form-check-input" name="is_active" type="checkbox" <?php if (!isset($data) || (isset($data->is_active) && $data->is_active)) : ?>checked<?php endif ?>>
								<span class="form-check-label">Active</span>
							</label>
						</div>
						<div class="md-3">
							<label for="name">Valid Till</label>
							<input type="date" class="form-control" name="expire_at" value="<?php if (isset($data->expire_at)) echo $data->expire_at; ?>" />
						</div>
						<input type="hidden" id="id" name="id" value="<?php echo isset($data->id) ? $data->id : 0; ?>" />

					</div>
					<div class="card-footer">
						<button class="btn btn-outline-primary" type="submit"><i class="bi bi-cloud-arrow-up"></i><?php echo lang('button.save'); ?></button>
						<a class="btn  btn-outline-primary" href="/ActivationController"><i class="bi bi-x-circle"></i><?php echo lang('button.cancel'); ?></a>
					</div>
			</form>
		</div>
	</div>
</div>
<script>

</script>