<div class="container-fluid">
  <!-- Page title -->
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <div class="page-pretitle">
        </div>
        <h2 class="page-title">
          <?php echo lang('folders'); ?>
        </h2>
      </div>
      <?php if ($auth >= $ADMIN) : ?>
        <div class="col-auto ms-auto">
          <div class="btn-list">
            <a href="/folder/edit" class=" btn btn-primary">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
              <?php echo lang('create'); ?>
            </a>
            <input type=file id=import_excel style="display:none" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
          </div>
        </div>
      <?php endif ?>
    </div>
  </div>
  <div class="page-body">
    <table id="table" class="table table-striped table-responsive" data-toggle="table" data-url="/folder/getTableData" data-pagination="false" data-sort-name="name" data-sort-order="asc">
      <thead>
        <tr>
          <th data-field="name" data-sortable="true" data-formatter="nameFormatter"><?php echo lang('name'); ?></th>
          <th data-field="descr" data-sortable="true"><?php echo lang('desc'); ?></th>
          <th data-field="start_date" data-sortable="true" data-formatter="dateFormatter"><?php echo lang('date.range'); ?></th>
          <th data-formatter="operateFormatter"><?php echo lang('operate'); ?></th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<script>
  var $table = $('#table');

  function nameFormatter(value, row) {
    var depth = row._depth || 0;
    var indent = '<span style="display:inline-block;width:' + (depth * 24) + 'px"></span>';
    var toggle = '';

    if (row.has_children > 0) {
      var expanded = row._expanded ? true : false;
      var icon = expanded ? 'bi-chevron-down' : 'bi-chevron-right';
      toggle = '<span class="folder-toggle" data-id="' + row.id + '" style="cursor:pointer;margin-right:6px;"><i class="bi ' + icon + '"></i></span>';
    } else {
      toggle = '<span style="display:inline-block;width:22px;margin-right:6px;"></span>';
    }

    var name = value;
    if (!row.noDel) {
      name = '<a href="/folder/edit?id=' + row.id + '" class="link-primary">' + value + '</a>';
    }

    return indent + toggle + name;
  }

  function dateFormatter(value, row) {
    if (row.date_flag == '1') {
      var CurrentDate = new Date();
      var SelectedDate = new Date(row.end_date);
      var color = '';
      if (CurrentDate.getTime() > SelectedDate.getTime) {
        color = "text-red";
      }
      return '<span class="' + color + '">' + value + '~' + row.end_date + '</span>';
    }
    return '';
  }

  function operateFormatter(value, row) {
    var html = '<div class="btn-list flex-nowrap">';

    if (!row.noDel || (row.noDel && row.noDel == '0')) {
      html += '<a href="#" onClick="remove_resource(\'folder\', ' + row.id + ')" class="link-danger" title="<?php echo lang("delete") ?>" >' +
        '<i class="bi bi-x-square"></i></a>';
    }

    html += '<a href="/folder/edit?parent_id=' + row.id + '" class="link-primary">' +
      '<i class="bi bi-folder-plus"></i></a></div>';

    return html;
  }

  $(document).ready(function() {
    // Handle expand/collapse toggle clicks
    $(document).on('click', '.folder-toggle', function(e) {
      e.stopPropagation();
      var $toggle = $(this);
      var folderId = $toggle.data('id');
      var $icon = $toggle.find('i');

      if ($icon.hasClass('bi-chevron-right')) {
        // Expand: load children
        loadChildren(folderId, $toggle);
      } else {
        // Collapse: remove children
        collapseFolder(folderId, $toggle);
      }
    });
  });

  function loadChildren(parentId, $toggle) {
    var $row = $toggle.closest('tr');
    var parentDepth = parseInt($row.data('depth') || '0', 10);

    // Update toggle icon
    $toggle.find('i').removeClass('bi-chevron-right').addClass('bi-chevron-down');

    // Mark as expanded
    $toggle.data('expanded', true);

    // Find existing data in the table to get the row object
    var rowData = findRowData(parentId);
    if (rowData) {
      rowData._expanded = true;
    }

    $.get('/folder/getChildren', {
      pId: parentId
    }, function(response) {
      if (!response.rows || response.rows.length === 0) return;

      var $insertAfter = $row;
      var depth = parentDepth + 1;

      // Set depth on child rows
      var childRows = response.rows;
      for (var i = 0; i < childRows.length; i++) {
        childRows[i]._depth = depth;
        childRows[i]._parentId = parentId;
      }

      // Insert rows after the parent (or after the last child of parent)
      var $lastSibling = $row;
      $row.nextAll('tr').each(function() {
        var sibDepth = parseInt($(this).data('depth') || '0', 10);
        if (sibDepth > parentDepth) {
          $lastSibling = $(this);
        } else {
          return false; // stop iterating — we've passed the siblings
        }
      });

      // Build HTML rows and insert
      var html = '';
      for (var i = 0; i < childRows.length; i++) {
        var child = childRows[i];
        html += buildRowHtml(child, depth);
      }
      $(html).insertAfter($lastSibling);

      // Store depth on the new tr elements
      $lastSibling.nextAll('tr.folder-row-' + parentId).each(function() {
        $(this).data('depth', depth);
      });
    }, 'json');
  }

  function collapseFolder(parentId, $toggle) {
    // Update toggle icon
    $toggle.find('i').removeClass('bi-chevron-down').addClass('bi-chevron-right');
    $toggle.data('expanded', false);

    // Mark as collapsed in data
    var rowData = findRowData(parentId);
    if (rowData) {
      rowData._expanded = false;
    }

    // Remove all descendant rows
    var $row = $toggle.closest('tr');
    var parentDepth = parseInt($row.data('depth') || '0', 10);

    $row.nextAll('tr').each(function() {
      var sibDepth = parseInt($(this).data('depth') || '0', 10);
      if (sibDepth > parentDepth) {
        $(this).remove();
      } else {
        return false;
      }
    });
  }

  function findRowData(id) {
    var data = $table.bootstrapTable('getData', true);
    for (var i = 0; i < data.length; i++) {
      if (data[i].id == id) {
        return data[i];
      }
    }
    return null;
  }

  function buildRowHtml(row, depth) {
    var nameHtml = nameFormatter(row.name || '', row);
    var descHtml = row.descr || '';
    var dateHtml = dateFormatter(row.start_date || '', row);
    var opsHtml = operateFormatter('', row);

    return '<tr class="folder-row-' + row._parentId + '" data-depth="' + depth + '" data-id="' + row.id + '">' +
      '<td>' + nameHtml + '</td>' +
      '<td>' + descHtml + '</td>' +
      '<td>' + dateHtml + '</td>' +
      '<td>' + opsHtml + '</td>' +
      '</tr>';
  }
</script>