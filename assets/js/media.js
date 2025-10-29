var $table = $("#table");

function operateFormatter(value, row) {
    return `<div class="btn-list flex-nowrap">
			<a href="#" onClick="remove_resource('media', ${row.id})" class="link-danger">
                <i class="bi bi-x-square"></i>
			</a>
		</div>`;
}

function sizeFormatter(value, row) {
    return fileSizeSI(value);
}

$("#batch_delete").click(function() {
    var selections = $table.bootstrapTable("getSelections");
    if (selections.length) {
        let id = selections.map((item) => {
            return item.id;
        });
        remove_resource("media", id);
    }
});

$("#move_to").click(function() {
    var selections = $table.bootstrapTable("getSelections");

    let id = selections.map((item) => {
        return item.id;
    });
    $.post(
        "/media/do_move_to", {
            folder_id: $("#move_to_folder").val(),
            ids: id,
        },
        function(data) {
            if (data.code == 0) {
                toastr.success(data.msg);
                $table.bootstrapTable("refresh");
            } else {
                toastr.error(data.msg);
            }
        },
        "json"
    );

    $table.bootstrapTable("uncheckAll");
});

function dateFormatter(value, row) {
    if (row.date_flag == "1") {
        var CurrentDate = new Date();
        var SelectedDate = new Date(row.end_date);
        var color = "";
        if (CurrentDate > SelectedDate) {
            color = "text-red";
        }

        return `<span class="${color}">${value}~${row.end_date}</span>`;
    }
    return "";
}