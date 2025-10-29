<script src="/assets/bootstrap/js/jquery.validate.min.js"></script>
<?php if ($lang == 'germany') : ?>
	<script src="/assets/js/validation/messages_de.js"></script>
<?php endif ?>
<script src="/assets/bootstrap/js/imask.js"></script>

<div class="row">
	<div class="col-12 col-lg-8 m-auto pt-3 pb-2 mb-3">
		<div class="card">
			<div class="card-header">
				<h2><?php echo $title ?></h2>
			</div>
			<form id="dataForm" action="/folder/do_save">
				<div class="card-body">
					<div id="validateTips"> </div>
					<div class="row g-3">

						<div class="col-12">
							<label for="name"><?php echo lang('name'); ?></label>
							<input type="text" class="form-control" name="name" required value="<?php if (isset($data->name)) echo $data->name; ?>" />
						</div>
						<div class="col-12">
							<label for="descr"><?php echo lang("desc"); ?></label>
							<textarea type="text" class="form-control" name="descr" rows="2"><?php if (isset($data->descr)) echo $data->descr; ?></textarea>
						</div>
						<div class="col-12">
							<label for="tags_select"><?php echo lang('tag'); ?></label>
							<select id="tags_select" name='tags_select[]' class="form-select select2" multiple>
								<option value="0"></option>
								<?php foreach ($tags as $tag) : ?>
									<option value="<?php echo $tag->id; ?>" <?php
																			if (isset($data->tags)) {
																				$tagary = explode(',', $data->tags);
																			}
																			if (isset($tagary) && in_array($tag->id, $tagary)) : ?>selected<?php endif; ?>><?php echo $tag->name
																																							?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-12 row">
							<div class="col-auto ">
								<label></label>
								<label class="form-check form-switch">
									<label><?php echo lang("date.range"); ?></label>
									<input type="checkbox" id='date_flag' name='date_flag' class="form-check-input" <?php if (isset($data) && $data->date_flag) : ?>checked <?php endif ?> />
								</label>
							</div>
							<div class="col date_range row" <?php if (!isset($data) || !$data->date_flag) : ?>style="display:none" <?php endif ?>>

								<div class="col-auto ">
									<label for="start_date"><?php echo lang('start_date'); ?></label>
									<input type="date" class="form-control" required name="start_date" value="<?php if (isset($data->start_date)) echo $data->start_date; ?>" />
								</div>
								<div class="col-auto">
									<label for="end_date"><?php echo lang('end_date'); ?></label>
									<input type="date" class="form-control" required name="end_date" value="<?php if (isset($data->end_date)) echo $data->end_date; ?>" />
								</div>
							</div>
						</div>
						<div class="col-12">
							<label for="play_time"><?php echo lang('playtime'); ?> (MM:SS) </label>
							<input type="text" class="form-control" required id="play_time" name="play_time" value="<?php echo isset($data->play_time) ? $data->play_time : "00:10"; ?>" />
						</div>
						<input type="hidden" id="id" name="id" value="<?php echo isset($data->id) ? $data->id : 0; ?>" />
						<input type="hidden" name="parent_id" value="<?php if (isset($parent_id)) echo $parent_id; ?>" />
					</div>
					<div class="card-footer">

						<button class="btn btn-outline-primary" type="submit"><i class="bi bi-cloud-arrow-up"></i><?php echo lang('button.save'); ?></button>
						<a class="btn  btn-outline-primary" href="/folder"><i class="bi bi-x-circle"></i><?php echo lang('button.cancel'); ?></a>
					</div>
			</form>

		</div>
	</div>
</div>


<input type="hidden" id="id" name="id" value="<?php echo isset($data->id) ? $data->id : 0; ?>" />
<script type="text/javascript">
	var element = document.getElementById('play_time');
	var maskOptions = {
		mask: '00:00',

	};
	var mask = IMask(element, maskOptions);
	$(document).ready(function() {

		$("#date_flag").change(function() {
			if ($("#date_flag").is(':checked')) {
				$('.date_range').show();
			} else {
				$('.date_range').hide();
			}
		});
	});
</script>