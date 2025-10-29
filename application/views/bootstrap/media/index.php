<link type="text/css" href="/assets/bootstrap/css/select2totree.css" rel="stylesheet" />
<script src="/assets/bootstrap/js/bootstrap-table-custom-view.js"></script>

<div class="container-fluid">

  <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <div class="page-pretitle">
        </div>
        <h2 class="page-title">
          <?php echo lang('media'); ?>
        </h2>
      </div>

      <div class="col-auto ms-auto">
        <div class="btn-list">
          <button class="btn" onclick="toggle_view()"><i id="view_icon" <?php if (isset($media_view) && $media_view == 0) : ?>class="bi bi-list" <?php else : ?>class="bi bi-grid" <?php endif ?>></i></button>
          <a href="#" class=" btn btn-primary" id="uploader" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cloud-upload" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
              <path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1"></path>
              <polyline points="9 15 12 12 15 15"></polyline>
              <line x1="12" y1="12" x2="12" y2="21"></line>
            </svg>
            <?php echo lang('upload'); ?>
          </a>
        </div>
      </div>
    </div>
  </div>
  <div class="page-body">
    <div class='pb-2 '>
      <div class="row " id="batch_operation" style="display: none;">
        <div class="col-auto">
          <button id="batch_delete" class="btn btn-danger"><?php echo lang('delete') ?></button>
        </div>
        <div class="col row">
          <div class="col-auto">
            <button id="move_to" class="btn btn-secondary"><?php echo lang('move.to') ?></button>
          </div>
          <div class="col-md-5">
            <select class="form-select" id="move_to_folder"></select>
          </div>
        </div>
      </div>

      <form class="row align-items-center justify-content-end" id='toolbar'>
        <div class="col-lg-2 col-md-3 col-sm-4">
          <div class="form-group">
            <select class="form-select" name='approved' id="approved">
              <option value="-1"><?php echo lang('all'); ?></option>
              <option value="1"><?php echo lang('approved'); ?></option>
              <option value="0" <?php if (isset($approved) && $approved == 0) : ?>selected<?php endif ?>><?php echo lang('unapproved'); ?></option>
            </select>
          </div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-4">
          <div class="form-group row">
            <label class="form-label col-auto col-form-label"><?php echo lang('media_type'); ?></label>
            <div class="col">
              <select class="form-select" id="media_type" name='media_type'>
                <option value="-1"><?php echo lang('all'); ?></option>
                <option value="1"><?php echo lang('image'); ?></option>
                <option value="2"><?php echo lang('video'); ?></option>
              </select>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-4">
          <div class="form-group row">
            <label class="form-label col-auto col-form-label"><?php echo lang('folder'); ?></label>
            <div class="col">
              <select class="form-select" id="folders" name='folders'>
                <option value="-1"><?php echo lang('all') ?></option>
              </select>
            </div>
          </div>
        </div>

        <div class="col-auto">
          <div class="input-icon">
            <input type="text" id="search" name="search" class="form-control" placeholder="">
            <span class="input-icon-addon">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <circle cx="10" cy="10" r="7"></circle>
                <line x1="21" y1="21" x2="15" y2="15"></line>
              </svg>
            </span>
          </div>
        </div>

      </form>
    </div>
    <div>
      <table id="table" class="table table-striped table-responsive" data-toggle="table" data-url="/media/getTableData" data-sort-name="add_time" data-sort-order="desc" data-custom-view="customViewFormatter" <?php if (isset($media_view) && $media_view == 0) : ?>data-show-custom-view="true" <?php endif ?>>
        <thead>
          <tr>
            <th data-checkbox="true"></th>
            <th data-field="tiny_url" data-formatter="previewFormatter"><?php echo lang('media.image') ?></th>
            <th data-field="name" data-formatter="nameFormatter" data-sortable="true" data-width="200"><?php echo lang('name'); ?></th>
            <th data-field="approved" data-sortable="true" data-formatter="approvedFormatter"><?php echo lang('status'); ?></th>
            <th data-field="descr" data-sortable="true" data-formatter="descFormatter"> <?php echo lang('desc'); ?></th>
            <th data-field="file_size" data-sortable="true" data-formatter="sizeFormatter"><?php echo lang('file.size'); ?></th>
            <th data-field="folder_name"><?php echo lang('folder'); ?></th>
            <th data-field="width" data-sortable="true" data-formatter="resFormatter"><?php echo lang('resolution'); ?></th>
            <th data-field="source" data-sortable="true" data-formatter="sourceFormatter"> <?php echo lang('source'); ?></th>

            <th data-field="play_time" data-sortable="true"><?php echo lang('play_time'); ?></th>
            <th data-field="start_date" data-sortable="true" data-formatter="dateFormatter"><?php echo lang('date.range'); ?></th>
            <th data-formatter="operateFormatter"><?php echo lang('operate'); ?></th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

<template id="profileTemplate">
  <div class="col-sm-6 col-lg-3">
    <div class="card card-sm">
      <a class="d-block cursor-pointer" data-bs-toggle="modal" data-bs-target="#modal-medium-preview" data-bs-mediumId="%BS_IMAGE_ID%" data-bs-mediumType="%BS_IMAGE_TYPE%" data-bs-showApproval="1"><img src="%IMAGE%" class="card-img-top object-contain" style="max-height:200px" onerror="javascript:this.src='/assets/img/load_fail_pic.svg'"></a>
      <div class="card-body">
        <div class="d-flex align-items-center text-center " style="overflow:hidden;text-overflow: ellipsis">
          <label class="form-check text-truncate">
            <input class="form-check-input" type="checkbox" checked="checked" id="%IMAGE_ID%" onchange="check_img(this)">
            <a href="%THUMB_HREF%" class="form-check-label text-truncate">%TITLE%</a>
          </label>

        </div>
      </div>
    </div>
  </div>
</template>
<?php
$this->load->view("bootstrap/media/uploader");
$this->load->view("bootstrap/media/preview_modal");
?>
<script src="/assets/bootstrap/js/select2totree.js"></script>

<script>
  function descFormatter(value, row, index) {
    if (value) {
      var desc = value;
      if (value.length > 50) {
        desc = value.substring(0, 50) + "...";
      }
      var tooltip = escapeHtml(value)

      return `<span data-bs-toggle="tooltip" data-bs-html="true" data-placement="bottom" data-bs-container="body" title="${tooltip}" >${desc}</span>`;
    } else {
      return '';
    }
  }

  function previewFormatter(value, row) {
    var source = '';
    switch (row.source) {
      case '0':
        source = "<?php echo lang('local'); ?>";
        break;
      case '1':
        source = "<?php echo lang('ftp'); ?>";
        break;
      case '2':
        source = "<?php echo lang('http'); ?>";
        break;
    }

    var file_size = fileSizeSI(row.file_size);

    var tooltips = `<ul class="list-group align-items-start pl-0" style="white-space: nowrap">
			<li class="list-group-item text-white border-0 py-0" >
				<?php echo lang("author") ?>
				<span>${row.author?row.author:''}</span>
			</li>
			<li class="list-group-item text-white border-0 py-0" >
				<?php echo lang("upload.date") ?>
				${row.add_time}
			</li>
			<li class="list-group-item text-white border-0 py-0" >
				<?php echo lang("file.size") ?>
				<span>${file_size}</span>
			</li>
			<li class="list-group-item text-white border-0 py-0">
				<?php echo lang("file.ext") ?>
				<span>${row.ext}</span>
			</li>
			<li class="list-group-item text-white border-0 py-0">
				<?php echo lang("source") ?>
				<span>${source}</span>
			</li>
			<li class="list-group-item text-white border-0 py-0">
				<?php echo lang("dimension") ?>
				<span>${row.width}X${row.height}</span>
			</li>
			<li class="list-group-item text-white border-0 py-0">
				<?php echo lang("folder") ?>
				<span>${(row.folder_id=='0')?"<?php lang('folder.default') ?>":row.folder_name}</span>
			</li>
			<li class="list-group-item text-white border-0 py-0">
				Media ID
				<span>${row.id}</span>
			</li>
			<li class="list-group-item text-white border-0">
				${row.name}
			</li>
		</ul>`;
    tooltips = escapeHtml(tooltips);
    return `<span class="d-inline-block cursor-pointer" tabindex="0" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-html="true"  data-bs-container="body" title='${tooltips}'>
            <img src="${row.tiny_url}" class="rounded" style="max-width:40px; max-height:40px" onerror="javascript:this.src='/assets/img/load_fail_pic.svg'"  data-bs-toggle="modal" data-bs-target="#modal-medium-preview" data-bs-mediumId="${row.id}" data-bs-mediumType="${row.media_type}" data-bs-showApproval="1">
            </span>
          `;
  }

  function sourceFormatter(value, row) {
    return value === '0' ? "<?php echo lang('local') ?>" : "<?php echo lang('ftp') ?>";
  }

  function resFormatter(value, row) {
    return row.width ? `${row.width}X${row.height}` : 'N/A';
  }

  function approvedFormatter(value, row) {
    var color = row.approved >= '1' ? 'status-green' : 'status-orange';
    var text = '';
    if (row.approved === '0' || !row.approved) {
      text = "<?php echo lang('unapproved') ?>";
    } else {
      text = row.approved === '1' ? "<?php echo lang('approved') ?>" : "<?php echo lang('approveAsP') ?>";
    }

    var status_str = `<span class="status-indicator status-indicator-animated ${color}" title="${text}" data-bs-toggle="tooltip" data-bs-placement="top">
    <span class="status-indicator-circle"></span>
    <span class="status-indicator-circle"></span>
    <span class="status-indicator-circle"></span>
    </span>`

    return status_str;

  }

  function nameFormatter(value, row) {

    if (value && value.length > 50) {
      value = value.substring(0, 50) + "...";
    }

    return ` <a href="/media/edit?id=${row.id}" class="link-primary">
        ${value}
    </a>`;
  };

  function customViewFormatter(data) {
    //data-show-custom-view="true" data-custom-view="customViewFormatter" data-show-custom-view-button="true"
    var template = $('#profileTemplate').html()
    var view = ''

    $.each(data, function(i, row) {

      var previewPath = '';
      if (row.media_type == 2) {
        previewPath = row.main_url ? row.main_url : row.tiny_url;
      } else {
        previewPath = row.main_url ? row.main_url : row.full_path;
      }

      view += template.replace('%TITLE%', row.name)
        .replace('%IMAGE%', previewPath)
        .replace('%THUMB_HREF%', `/media/edit?id=${row.id}`)
        .replace('%IMAGE_ID%', row.id)
        .replace('%BS_IMAGE_ID%', row.id)
        .replace('%BS_IMAGE_TYPE%', row.media_type)
        .replace('checked="checked"', row['0'] == true ? 'checked="checked"' : " ")
    })

    return `<div class="row row-cards">${view}</div>`
  }

  function toggle_view() {
    var view_type = 1;
    var view_icon = $('#view_icon');
    if (view_icon.hasClass('bi-list')) {
      view_type = 1;
      view_icon.removeClass('bi-list');
      view_icon.addClass('bi-grid');
    } else {
      view_type = 0;
      view_icon.removeClass('bi-grid');
      view_icon.addClass('bi-list');
    }

    $('#table').bootstrapTable('toggleCustomView');
    $.get(`/media/update_media_view?media_view=${view_type}`);

  }

  function check_img(e) {
    var id = $(e).attr('id');
    var ids = [];
    ids.push(id);

    if ($(e).is(':checked')) {
      $('#table').bootstrapTable('checkBy', {
        field: 'id',
        values: ids
      });
    } else {
      $('#table').bootstrapTable('uncheckBy', {
        field: 'id',
        values: ids
      });
    }

  }

  function go_preview(row) {
    var html =
      `<div class="card">
        <div>
          <img class="img-responsive-16by9" src="${row.full_path}"/>
        </div>
        <div class="card-body">
        <div class="list-group list-group-flush overflow-auto" style="max-height: 35rem">
          <div class="list-group-item">
            <table class="table table-vcenter">
              <tbody>
                <tr>
                  <td class="w-1">
                    name
                  </td>
                  <td class="td-truncate">
                    <div class="text-truncate">
                      ${row.name}
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="w-1">
                    name
                  </td>
                  <td class="td-truncate">
                    <div class="text-truncate">
                      ${row.add_time}
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
        </div>
      </div>`;

    $('#preview-body').html(html);
    $('#previewModal').modal('show');
  }
  $(document).ready(function() {
    <?php if ($auth == 5) : ?>
      $table.on("post-body.bs.table", function(e, name) {
        if ($("#notification-zone").length) {
          $.getJSON("/media/check_unapproved", function(ret) {
            if (ret === 0) {
              $("#notification-zone").hide();
            }
          });
        }
      });
    <?php endif ?>

    $("#date_flag").change(function() {
      if ($("#date_flag").is(":checked")) {
        $(".date_range").show();
      } else {
        $(".date_range").hide();
      }
    });
    $.ajax({
      url: "/player/getNestedFolders",
      dataType: "json",
      success: function(res) {
        $("#folders").select2ToTree({
          width: "100%",
          treeData: {
            dataArr: res.data,
          },
        });
        $("#move_to_folder").select2ToTree({
          width: "100%",
          treeData: {
            dataArr: res.data,
          },
        });
      },
      cache: false,
      contentType: false,
      processData: false,
    });

  });
</script>