<div class="modal fade" id="evsesModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select EV Charger from address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3 align-items-center justify-content-end">
                    <div class="col-6">
                        <div class="input-icon">
                            <input type="text" id="evsesSearch" class="form-control " placeholder="Address">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <circle cx="10" cy="10" r="7"></circle>
                                    <line x1="21" y1="21" x2="15" y2="15"></line>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <table id="tableEvsesModal" class="table table-sm table-striped table-responsive" data-search="false" data-pagination="false">
                        <thead>
                            <tr>
                                <th data-radio="true"></th>
                                <th data-field="name"><?php echo lang('name'); ?></th>
                                <th data-field="status"><?php echo lang('status'); ?></th>
                                <th data-field="address"><?php echo lang('address'); ?></th>
                                <th data-field="distance">Distance</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="select-charger"><?php echo lang('button.save'); ?></button>
                <button type="button" class="btn me-auto" data-bs-dismiss="modal" id="close-charger-modal"><?php echo lang('button.cancel'); ?></button>
            </div>
        </div>
    </div>
</div>
<script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD5ILgQ2vLjavzFASq5xHfuVYVneV9DBQk&callback=initAutocomplete&libraries=places&v=weekly" defer></script>
<script>
    var targetTableId = null;
    var evses_data = [];

    var chargerTable = $('#tableEvsesModal');

    chargerTable.bootstrapTable({
        data: evses_data
    });

    function initAutocomplete() {
        // Create the autocomplete object, restricting the search predictions to
        // geographical location types.
        autocomplete = new google.maps.places.Autocomplete(
            document.getElementById("evsesSearch"), {
                types: ["address"]
            }
        );

        // Avoid paying for data that you don't need by restricting the set of
        // place fields that are returned to just the address components.
        autocomplete.setFields(["address_component", "geometry"]);
        autocomplete.setComponentRestrictions({
            country: ["de"],
        });

        // When the user selects an address from the drop-down, populate the
        // address fields in the form.
        autocomplete.addListener("place_changed", fillInAddress);
    }

    async function fillInAddress() {
        // Get the place details from the autocomplete object.
        const place = autocomplete.getPlace();

        if (!place.geometry) {
            return;
        }
        chargerTable.bootstrapTable('showLoading');
        console.log(place.geometry.location.lat(), place.geometry.location.lng())
        evses_data = [];
        try {
            //const username = "hardwaretest";
            //const password = "#z8tEciWf";
            //const application_key = "3261774042893637860a705371f40479";

            const username = "kdh-api";
            const password = "ze7G*Ns*QbYrHcv9";
            const application_key = "e09132de76fafc5a91718ecc8c0d95ea";

            const url =
                "https://demo.chargecloud.de/rest:client/" +
                application_key +
                `/getEmobilityLocationsDataDetails?limit=100&offset=0&&latitude=${place.geometry.location.lat()}&longitude=${place.geometry.location.lng()}&&&radius=2000`;
            const response = await fetch(url, {
                method: "GET",
                headers: {
                    Authorization: "Basic " + btoa("client#" + username + ":" + password),
                    "Content-Type": "application/json",
                },
            });
            const res = await response.json();
            const data = res.data;

            data.forEach((element) => {
                if (element.evses) {
                    let chargers = element.evses.map(v => {
                        return {
                            id: v.id,
                            name: v.id,
                            status: v.status,
                            address: element.address + ", " + element.city,
                            distance: element.distance_in_m,
                        };
                    });
                    evses_data = [...evses_data, ...chargers];

                }
            });
        } catch (error) {
            console.log(error);
        }
        chargerTable.bootstrapTable("hideLoading")
        chargerTable.bootstrapTable('load', evses_data);
    }

    function getTargetTable() {
        return targetTableId;
    }
    var target_id_field = null;

    $('#select-charger').off('click').on('click', function() {
        {
            var selections = chargerTable.bootstrapTable('getSelections');
            if (selections.length == 0) {
                return;
            }

            if (target_id_field) {
                $("#" + target_id_field).val(selections[0].name);
            }
            $('#close-charger-modal').click();
        }
    });



    $('#evsesModal').on('show.bs.modal', function(e) {
        var button = e.relatedTarget
        target_id_field = button.getAttribute('data-target-field');
    });


    $('#evsesModal').on('hide.bs.modal', function(e) {
        target_id_field = null
    });

    $(document).ready(function() {

    });
</script>