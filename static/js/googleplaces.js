function initAutocomplete() {
  // Create the autocomplete object, restricting the search predictions to
  // geographical location types.
  autocomplete = new google.maps.places.Autocomplete(
    document.getElementById("conaddr"),
    { types: ["address"] }
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

function fillInAddress() {
  // Get the place details from the autocomplete object.
  const place = autocomplete.getPlace();

  if (!place.geometry) {
    return;
  }

  document.getElementById("geox").value = "";
  document.getElementById("geoy").value = "";
  document.getElementById("zipcode").value = "";
  document.getElementById("contown").value = "";

  document.getElementById("geox").value = place.geometry.location
    .lat()
    .toFixed(4);
  document.getElementById("geoy").value = place.geometry.location
    .lng()
    .toFixed(4);

  var street_num = "";
  var route = "";

  for (const component of place.address_components) {
    const addressType = component.types[0];

    
    if (component.types[0] == "street_num") {
      street_num = component.long_name;
      document.getElementById("stree_num").value = street_num;
    } else if (component.types[0] == "route") {
      route = component["long_name"];
      document.getElementById("conaddr").value = route;
    } else if (component.types[0] == "postal_code") {
      document.getElementById("zipcode").value = component["short_name"];
    } else if (component.types[0] == "locality") {
      document.getElementById("contown").value = component["long_name"];
    } else if (component.types[0] == "administrative_area_level_1") {
      document.getElementById("state").value = component["long_name"];
    } else if (component.types[0] == "country") {
      document.getElementById("country").value = component["long_name"];
    }
  }
}
