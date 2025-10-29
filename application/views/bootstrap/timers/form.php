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
			<form id="timerForm">
				<div class="card-body">


					<div class="row g-3">

						<div class="mb-3">
							<label for="name"><?php echo lang('name'); ?></label>
							<input type="text" class="form-control" name="name" id="name" required value="<?php if (isset($data->name)) echo $data->name; ?>" />
						</div>
						<div class="mb-32">
							<label for="descr"><?php echo lang("desc"); ?></label>
							<textarea type="text" class="form-control" name="descr" id="descr" rows="2"><?php if (isset($data->descr)) echo $data->descr; ?></textarea>
						</div>
						<div class="mb-3">
							<div class="form-label"><?php echo lang("timer.type"); ?></div>
							<div>
								<label class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="timerType" value='0'>
									<span class="form-check-label"><?php echo lang('timer.type.unity'); ?></span>
								</label>
								<label class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="timerType" value='1'>
									<span class="form-check-label"><?php echo lang('timer.type.week'); ?></span>
								</label>
							</div>
						</div>
						<div class="mb-3" id="div_daily" <?php if (isset($data) && $data->type != 0) : ?> style="display:none" <?php endif ?>>
							<div class="card card-sm col-md-5">
								<div class="card-header">
									<h3 class="card-title"><?php echo lang('timer.type.unity'); ?></h3>

								</div>
								<div class="card-body">
									<div id="<?php echo "div-w0" ?>">
										<?php for ($i = 0; $i < 3; $i++) : ?>
											<div class="row align-items-center g-0 pb-1">
												<div class="col-auto">
													<label class="form-check form-switch">
														<input class="form-check-input timer-item-check" type="checkbox" id="<?php echo 'check_w0-item-' . $i ?>">
													</label>
												</div>

												<div class="col">
													<input type="time" class="form-control <?php echo 'w0-item-' . $i ?>" id="<?php echo 'w0-start-' . $i ?>" required disabled class="form-control" />
												</div>
												<div class="col">
													<input type="time" class="form-control <?php echo 'w0-item-' . $i ?>" id="<?php echo 'w0-end-' . $i ?>" required disabled class=" form-control" />
												</div>
											</div>
										<?php endfor ?>
									</div>

								</div>
							</div>
						</div>

						<div class="mb-3 " id="div_weekly" <?php if (!isset($data) && (isset($data) && $data->type != 1)) : ?> style="display:none" <?php endif ?>>
							<?php for ($w = 1; $w <= 7; $w++) : ?>
								<div class="card card-sm col-md-5 mb-3">
									<div class="card-header">
										<h3 class="card-title"><?php echo lang('timer.type.week.' . $w); ?></h3>
										<div class="card-actions">
											<label class="form-check form-switch">
												<input class="form-check-input whole-day-off" type="checkbox" id="<?php echo "check_div-w" . $w ?>" <?php if (isset($data) && in_array($w, $data->offweekdays)) : ?> checked <?php endif ?>>
												<span class="form-check-label"><?php echo lang('timer.whole.day.off'); ?></span>
											</label>
										</div>
									</div>
									<div class="card-body">
										<div id="<?php echo "div-w" . $w ?>" <?php if (isset($data) && in_array($w, $data->offweekdays)) : ?> style="display:none;" <?php endif ?>>
											<?php for ($i = 0; $i < 3; $i++) : ?>
												<div class="row align-items-center g-0 pb-1">
													<div class="col-auto">
														<label class="form-check form-switch">
															<input class="form-check-input timer-item-check" type="checkbox" id="<?php echo 'check_w' . $w . '-item-' . $i ?>">
														</label>
													</div>

													<div class="col">
														<input type="time" class="form-control <?php echo 'w' . $w . '-item-' . $i ?>" id="<?php echo 'w' . $w . '-start-' . $i ?>" required disabled class="form-control" value="" />
													</div>
													<div class="col">
														<input type="time" class="form-control <?php echo 'w' . $w . '-item-' . $i ?>" id="<?php echo 'w' . $w . '-end-' . $i ?>" required disabled class=" form-control" />
													</div>
												</div>
											<?php endfor ?>
										</div>

									</div>
								</div>
							<?php endfor; ?>
						</div>
						<input type="hidden" id="id" name="id" value="<?php echo isset($data->id) ? $data->id : 0; ?>" />
					</div>
					<div class="card-footer">
						<button class="btn btn-outline-primary" type="submit"><i class="bi bi-cloud-arrow-up"></i><?php echo lang('button.save'); ?></button>
						<a class="btn  btn-outline-primary" href="/timersController"><i class="bi bi-x-circle"></i><?php echo lang('button.cancel'); ?></a>
					</div>
			</form>
		</div>
	</div>

</div>

<script>
	$("input[name='timerType' ]").on('change', function() {
		if ($(this).val() == '0') {
			$('#div_daily').show();
			$('#div_weekly').hide();
		} else {
			$('#div_daily').hide();
			$('#div_weekly').show();
		}
	});

	$(document).ready(function() {

		<?php if (isset($data)) : ?>
			var extra = eval(<?php echo $data->extra ?>);
			// console.log(extra);
			for (var w in extra) {
				for (var i in extra[w]) {
					var item = extra[w][i];
					if (item.status == '0') {
						$(`#check_w${w}-item-${i}`).prop("checked", true);
						$(`#w${w}-start-${i}`).val(item.start_time).attr('disabled', false);
						$(`#w${w}-end-${i}`).val(item.end_time).attr('disabled', false);
					}
				}
			}



		<?php endif ?>

		$('.whole-day-off').on('change', function() {
			var id = this.id;


			var split_id = id.split("_");
			var checked = $(this).is(":checked");
			if (checked) {
				$('#' + split_id[1]).hide();
			} else {
				$('#' + split_id[1]).show();
			}
		})

		$('.timer-item-check').on('change', function() {
			var id = this.id;

			var split_id = id.split("_");

			var checked = $(this).is(":checked");
			if (checked) {

				$('.' + split_id[1]).attr("disabled", false);
			} else {
				$('.' + split_id[1]).attr("disabled", true);
			}
		})

		var timerType = "<?php echo isset($data) ? $data->type : '0' ?>";
		$("input[name='timerType']").each(function() {
			if ($(this).val() == timerType) {
				$(this).click();
			}
		});


		$("#timerForm").validate({
			submitHandler: function(form) {
				var params = {
					id: $('#id').val(),
					name: $('#name').val(),
					descr: $('#descr').val(),
					type: $('input:radio[name="timerType"]:checked').val()
				};


				var extra = [];

				var startIndex = params.type == '0' ? 0 : 1;
				var endIndex = params.type == '0' ? 1 : 8;
				var offwds = [];
				var index = 0;
				for (var w = startIndex; w < endIndex; w++) {
					if (w > 0) {
						var wdo = $('#check_div-w' + w);
						if (wdo.is(':checked')) {
							offwds.push(w);
						}
					}
					var items = [];
					for (var i = 0; i < 3; i++) {
						$item = [];
						if ($(`#check_w${w}-item-${i}`).is(':checked')) {
							$item['status'] = 0;
							var start_time = $(`#w${w}-start-${i}`).val();
							var stop_time = $(`#w${w}-end-${i}`).val();
							if (start_time === '' || stop_time === '') {

							}
							$item = {
								status: 0,
								start_time: start_time,
								end_time: stop_time,
								week: w
							};
							items.push($item);
						}
					}
					if (items.length) {
						extra[index++] = items;
					}
				}
				params.off_weekdays = offwds.join(',');
				params.extra = extra;


				$.post("/timersController/do_save", params, function(data) {
					if (data.code != 0) {
						toastr.error(data.msg);
					} else {
						localStorage.setItem("Status", JSON.stringify({
							type: 'success',
							message: data.msg
						}));
						window.location = '/timersController';
					}
				}, 'json');
			}
		})


	});
	$(function() {

	});
</script>