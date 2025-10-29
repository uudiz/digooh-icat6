<link rel="stylesheet" href="/assets/bootstrap/css/select2totree.css" />
<script src="/assets/bootstrap/js/jquery.validate.min.js"></script>
<?php if ($lang == 'germany') : ?>
	<script src="/assets/js/validation/messages_de.js"></script>
<?php endif ?>
<script src="https://unpkg.com/imask"></script>
<link href="/assets/bootstrap/css/tom-select.css" rel="stylesheet">
<link href="/assets/bootstrap/css/tom-select.bootstrap5.css" rel="stylesheet">
<script src="/assets/bootstrap/js/popper.min.js"></script>
<script src="/assets/bootstrap/js/tom-select.complete.min.js"></script>

<div class="row">
	<div class="col-12 col-lg-10 m-auto pt-3 pb-2 mb-3">

		<div class="card">
			<div class="card-header">
				<h2><?php echo $title ?></h2>
			</div>
			<form id="dataForm" action="/media/do_save">
				<div class="card-body">
					<div class="card d-flex flex-column">
						<div class="row row-0 flex-fill">
							<div class="col-md-4">
								<?php if ($data->media_type == 1) : ?>
									<img src="<?php if (isset($data->full_path)) echo substr($data->full_path, 1); ?>" class="w-100 h-100 object-contain" alt="Card side image" onerror="javascript:this.remove()" />
								<?php elseif ($data->media_type == 2) : ?>
									<video autoplay controls class="w-100 h-100 object-contain">
										<source src="<?php echo substr($data->full_path, 1); ?>" type='video/mp4'>
									</video>
								<?php endif ?>

							</div>
							<div class="col-md-8">
								<div class="card-body">

									<h3 class="card-title"><a href="#">Details</a></h3>

									<div class="row d-flex align-items-center pt-4 mt-auto">

										<div class="col-12">
											<label for="name"><?php echo lang('name'); ?></label>
											<input type="text" class="form-control" name="name" required value="<?php if (isset($data->name)) echo $data->name; ?>" />
										</div>
										<div class="col-12">
											<label for="descr"><?php echo lang("desc"); ?></label>
											<textarea type="text" class="form-control" name="descr" rows="2"><?php if (isset($data->descr)) echo $data->descr; ?></textarea>
										</div>
										<div class="col-12">
											<label for="folders"><?php echo lang("folder"); ?></label>
											<select class="form-select" id="folders" name='folder_id'></select>
										</div>
										<div class="col-12">
											<label for="tags_select"><?php echo $this->config->item("with_template") ? lang('categories') : lang('tag'); ?></label>
											<select id="tags_select" name='tags_select[]' class="form-select select2" multiple>
												<option value="0"></option>
												<?php foreach ($tags as $tag) : ?>
													<option value="<?php echo $tag->id; ?>" <?php
																							if (isset($tagstr)) {
																								$tagary = explode(',', $tagstr);
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


										<div class="mb-3">
											<label for="play_time"><?php echo lang('play_time'); ?> (MM:SS)</label>
											<input type="text" class="form-control" id="play_time" name="play_time" <?php if ($data->media_type == 2 || $auth < 5) : ?>readonly<?php endif ?> value="<?php if (isset($data->play_time)) echo $data->play_time; ?>" />
										</div>
										<?php if (0 && $auth == 5 && !$pid) : ?>
											<div class="mb-3">
												<div class="col-auto ">
													<label><?php echo lang("approval"); ?></label>
													<label class="form-check form-switch">
														<input type="checkbox" id='approved' name='approved' class="form-check-input" <?php if (isset($data) && $data->approved) : ?>checked <?php endif ?> />
													</label>
												</div>
											</div>
										<?php endif ?>
										<?php if ($this->config->item("with_register_feature") && isset($register_feature) && $register_feature) : ?>
											<div class="mb-3 row">
												<div class='col-auto'>

													<label class="form-label"><?php echo lang("store"); ?></label>

													<select class="form-select select2-opt" id="store_id" name="store_id" aria-placeholder="Stores">
														<option value="0" disabled selected hidden>Please select a store</option>
														<?php if ($this->config->item("multi_providers")) : ?>
															<?php foreach ($stores as $key => $values) : ?>
																<optgroup label="<?php echo $key; ?>">
																	<?php foreach ($values as $store) : ?>
																		<option value="<?php echo $store->id; ?>"><?php echo $store->name; ?></option>
																	<?php endforeach; ?>
																</optgroup>
															<?php endforeach; ?>
														<?php else : ?>
															<?php foreach ($stores as $store) : ?>
																<option value="<?php echo $store->id; ?>"><?php echo $store->name; ?></option>
															<?php endforeach; ?>
														<?php endif ?>
													</select>


												</div>
												<div class='col'>
													<label class="form-label"><?php echo lang("product"); ?></label>
													<select id="product_id" name="product_id" class="form-control">
													</select>
												</div>
											</div>
										<?php endif ?>
										<input type="hidden" id="id" name="id" value="<?php echo isset($data->id) ? $data->id : 0; ?>" />
									</div>

								</div>
							</div>
						</div>
					</div>
				</div>


				<div class="card-footer">

					<button class="btn btn-outline-primary" type="submit"><i class="bi bi-cloud-arrow-up"></i><?php echo lang('button.save'); ?></button>
					<a class="btn  btn-outline-primary" href="/media"><i class="bi bi-x-circle"></i><?php echo lang('button.cancel'); ?></a>

				</div>
			</form>
		</div>

	</div>
</div>
<script>
	var element = document.getElementById('play_time');
	var maskOptions = {
		mask: '00:00',

	};
	var mask = IMask(element, maskOptions);

	$('#date_flag').on('change', function() {
		if ($("#date_flag").is(':checked')) {
			$('.date_range').show();
		} else {
			$('.date_range').hide();
		}
	});
	<?php if ($this->config->item("with_register_feature") && isset($register_feature) && $register_feature) : ?>
		/*
		var storeSelect = new TomSelect("#store_id", {
			allowEmptyOption: true,
			onChange: function(value) {
				fetch_products(value);
			}
		});
		*/
		$("#store_id").on('change', function() {
			fetch_products($(this).val());
		});


		var productSelect = new TomSelect('#product_id', {
			valueField: 'id',
			labelField: 'name',
			searchField: ['name', 'ean_code', 'plu_code'],
			disabled: true,
			allowEmptyOption: true,
			maxOptions: null,
			plugins: {
				'dropdown_header': {
					html: function(data) {
						return '<div class="dropdown-header d-flex align-items-center"><span class="flex-grow-1">Product</span><span class="flex-grow-1">Artikelnummer</span><span class="flex-grow-1">Price</span> </div>';
					}
				},
				'clear_button': {}
			},

			render: {
				option: function(data) {

					const div = document.createElement('div');
					div.className = 'row';

					const span = document.createElement('span');
					span.className = 'col-lg-4';
					span.innerText = data.name;
					div.append(span);

					const itemno = document.createElement('span');
					itemno.className = 'col-lg-4';
					itemno.innerText = data.product_id;
					div.append(itemno);

					const price = document.createElement('span');
					price.className = 'col-lg-4';

					price.innerText = data.ena_price && data.ena_price > 0 ? data.ena_price : data.price;

					div.append(price);

					return div;
				}

			}
		});

		function fetch_products(store_id) {
			productSelect.clear();
			productSelect.clearOptions();
			if (!store_id) {
				return;
			}

			fetch('/api/products?store_id=' + store_id)
				.then(function(response) {
					return response.json();
				})
				.then(function(products) {
					// Add the products to the product select
					productSelect.addOption(products.data);

					// Enable the product select
					productSelect.enable();
					<?php if (isset($data->product_id)) : ?>
						productSelect.setValue(<?php echo $data->product_id ?>);
					<?php endif; ?>
				});
		}
		<?php if (isset($store_id) && $store_id) : ?>
			$('#store_id').val("<?php echo $store_id ?>").trigger('change');
		<?php endif ?>
	<?php endif ?>
	$(document).ready(function() {

		$.ajax({
			url: '/player/getNestedFolders',
			dataType: "json",
			success: function(res) {
				$("#folders").select2ToTree({
					width: '100%',
					treeData: {
						dataArr: res.data
					}
				});
				<?php if (isset($data->folder_id)) : ?>
					$('#folders').val(<?php echo $data->folder_id ?>).trigger('change');
				<?php endif ?>
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});
</script>