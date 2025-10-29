<script src="/assets/bootstrap/js/jquery.validate.min.js"></script>
<?php if ($lang == 'germany') : ?>
	<script src="/assets/js/validation/messages_de.js"></script>
<?php endif ?>

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
			<form id="dataForm" action="/ftpSites/do_save">
				<div class="card-body">
					<div id="validateTips"> </div>
					<div class="g-3">
						<div class="mb-3">
							<label for="name"><?php echo lang('ftp.profile'); ?></label>
							<input type="text" class="form-control" name="profile" required value="<?php if (isset($data->profile)) echo $data->profile; ?>" />
						</div>
						<div class="mb-3">
							<label for="descr"><?php echo lang("ftp.server"); ?></label>
							<input type="text" class="form-control" name="server" required value="<?php if (isset($data->server)) echo $data->server; ?>" />
						</div>
						<div class="mb-3 row">
							<div class="col">
								<label class="form-label"><?php echo lang("ftp.port"); ?></label>
								<input type="number" name="port" min='1' max='65536' required class="form-control" autocomplete="off" value="<?php echo isset($data->server) ? $data->port : 21; ?>" />
							</div>
							<div class="col">
								<div class="form-label"><?php echo lang('ftp.pasv'); ?></div>
								<label class="form-check form-switch">
									<input class="form-check-input" type="checkbox" name='pasv' <?php if (!isset($data) || (isset($data) && $data->pasv)) : ?> checked<?php endif; ?>>
								</label>
							</div>
						</div>
						<div class="mb-3">
							<label><?php echo lang("ftp.account"); ?></label>
							<input type="text" class="form-control" name="account" required value="<?php if (isset($data->account)) echo $data->account; ?>" />
						</div>
						<div class="mb-3">
							<label><?php echo lang("ftp.password"); ?></label>
							<input type="password" class="form-control" name="password" required value="<?php if (isset($data->server)) echo $data->password; ?>" />
						</div>
						<input type="hidden" id="id" name="id" value="<?php echo isset($data->id) ? $data->id : 0; ?>" />

					</div>
					<div class="card-footer">
						<button class="btn btn-outline-primary" type="submit"><i class="bi bi-cloud-arrow-up"></i><?php echo lang('button.save'); ?></button>
						<a class="btn  btn-outline-primary" href="/ftpSites"><i class="bi bi-x-circle"></i><?php echo lang('button.cancel'); ?></a>
					</div>
			</form>
		</div>
	</div>
</div>