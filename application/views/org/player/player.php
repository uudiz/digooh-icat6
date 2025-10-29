<head>
	<link rel="stylesheet" href="/static/css/alertify.core.css" />
	<link rel="stylesheet" href="/static/css/alertify.default.css" />
	<script src="/static/js/alertify.min.js" type="text/javascript" charset="utf-8"></script>
	<script src='/static/js/player.js'></script>
</head>

<?php if ($auth >= 5 || $auth == 4 && !$pid) : ?>

	<div class="add-panel">
		<?php if (!$pid) : ?>
			<a href='' onclick="document.getElementById('import_excel').click();return false;">
				<img src='/images/icons/import.gif' /><?php echo lang('import'); ?>
			</a>

			<a href="javascript:void(0);" onclick="player.exportplayers();"><img src="/images/icons/export.gif" title="export" />Export</a>
			<?php if ($auth >= 5) : ?>
				<a href="/player/add?width=1280&height=800" id="create" class="thickbox" title="<?php echo lang('create.player'); ?>"><?php echo lang('create'); ?></a>
			<?php endif; ?>
			<input type=file id=import_excel style="display:none" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
		<?php endif; ?>
	</div>

<?php endif; ?>

<div class="clear"></div>
<h1 class="tit-01" style="position:relative;"><?php echo lang('player'); ?>
	<div class="filter" style="width:84%;">
		<?php echo lang('filter.by'); ?>:

		<input type="text" name="filter" id="filter" style="width:120px; margin-left:4px;">
		<?php if (!isset($usage_page)) : ?>
			<label style="margin-left:20px;"><?php echo lang('filter.online'); ?></label>
			<input type="checkbox" name="online" id="online" value="1" />
		<?php endif ?>
		<label style="margin-left:20px;"><?php echo lang('filter.criteria'); ?>:</label>

		<select id="filterCriteria" name="filterCriteria" class="select2" style="width: 150px; margin:0px 4px;" onchange="player.filter();">
			<option value="0"><?php echo lang('all'); ?></option>
			<?php if (isset($criteria)) : ?>
				<?php foreach ($criteria as $g) : ?>
					<option value="<?php echo $g->id; ?>"><?php echo $g->name; ?></option>
				<?php endforeach; ?>
			<?php endif; ?>
		</select>
		<label style="margin-left:20px;"><?php echo lang('filter.tag'); ?>:</label>

		<select id="filterTag" name="filterTag" class="select2" style="width: 150px; margin:0px 4px;" onchange="player.filter();">
			<option value="0"><?php echo lang('all'); ?></option>
			<?php if (isset($tags)) : ?>
				<?php foreach ($tags as $t) : ?>
					<option value="<?php echo $t->id; ?>"><?php echo $t->name; ?></option>
				<?php endforeach; ?>
			<?php endif; ?>
		</select>
		<a href="javascript:void(0);" style="margin-left:20px; " class="btn-go" onclick="player.filter();"><label><?php echo lang('filter'); ?></label></a>

	</div>
	<span></span>
</h1>
<div class="clear"></div>
<div id="playerContent">
	<?php
	if (isset($body_view)) {
		$this->load->view($body_view);
	}
	?>
</div>

<script type="text/javascript">
	$('.select2').select2();
	document.querySelector("#import_excel").addEventListener("change", function() {
		//获取到选中的文件
		var input = document.querySelector("#import_excel");
		var file = input.files[0];

		if (!file) {
			return;
		}
		var formdata = new FormData();
		formdata.append("file", file);

		input.value = '';
		$.ajax({
			url: "/player/import_players",
			type: "post",
			processData: false,
			contentType: false,
			data: formdata,
			dataType: 'json',

			success: function(res) {
				if (res.code == 0) {
					player.refresh();
				}
				alertify.alert(res.msg);

			},
			error: function(err) {
				alert("failed:", err);
			}

		})
	});
</script>