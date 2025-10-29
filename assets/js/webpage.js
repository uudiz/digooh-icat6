var $table = $("#table");

function operateFormatter(value, row) {
    return `<div class="btn-list flex-nowrap">

			<a href="#" onClick="remove_resource('webpages', ${row.id})" class="link-danger">
                <i class="bi bi-x-square"></i>
			</a>
		</div>`;
}

$("#batch_delete").click(function() {
    var selections = $table.bootstrapTable("getSelections");
    if (selections.length) {
        let id = selections.map((item) => {
            return item.id;
        });
        remove_resource("webpages", id);
    }
});