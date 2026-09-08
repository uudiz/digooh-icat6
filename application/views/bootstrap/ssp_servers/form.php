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
			<form id="dataForm" action="/sspServers/do_save">
				<div class="card-body">
					<div id="validateTips"> </div>
					<div class="row g-3">

						<div class="col-12">
							<label for="name"><?php echo lang('name'); ?></label>
							<input type="text" class="form-control" name="name" required value="<?php if (isset($data->name)) echo $data->name; ?>" />
						</div>
						<div class="col-12">
							<label for="host"><?php echo lang('ssp.host'); ?></label>
							<input type="text" class="form-control" name="host" required value="<?php if (isset($data->host)) echo $data->host; ?>" />
						</div>
						<div class="col-md-6">
							<label for="parser_class"><?php echo lang('ssp.parser.class'); ?></label>
							<input type="text" class="form-control" name="parser_class" required value="<?php if (isset($data->parser_class)) echo $data->parser_class; ?>" />
						</div>
						<div class="col-md-6">
							<label for="priority"><?php echo lang('ssp.priority'); ?></label>
							<input type="number" min="0" max="10" class="form-control" name="priority" required value="<?php echo isset($data->priority) ? $data->priority : 0; ?>" />
						</div>
						<div class="col-md-6">
							<label for="timeout"><?php echo lang('ssp.timeout'); ?> (s)</label>
							<input type="number" min="1" class="form-control" name="timeout" required value="<?php echo isset($data->timeout) ? $data->timeout : 5; ?>" />
						</div>
						<div class="col-md-6">
							<label for="is_active"><?php echo lang('ssp.active'); ?></label>
							<label class="form-check form-switch">
								<input class="form-check-input" type="checkbox" name="is_active" <?php if (!isset($data) || (isset($data) && $data->is_active)) : ?>checked<?php endif; ?>>
							</label>
						</div>
						<input type="hidden" id="id" name="id" value="<?php echo isset($data->id) ? $data->id : 0; ?>" />

					</div>
				</div>
				<div class="card-footer">
					<button class="btn btn-outline-primary" type="submit"><i class="bi bi-cloud-arrow-up"></i><?php echo lang('button.save'); ?></button>
					<a class="btn btn-outline-primary" href="/sspServers"><i class="bi bi-x-circle"></i><?php echo lang('button.cancel'); ?></a>
				</div>
			</form>
		</div>
	</div>
</div>