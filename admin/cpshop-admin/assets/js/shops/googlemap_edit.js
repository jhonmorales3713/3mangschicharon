var marker = "";
const geocoder = "";
var infowindow = "";
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else {
       alert("Geolocation is not supported by this browser.");
    }
}

function showPosition(position) {
    var lat =  position.coords.latitude;
    var lng = position.coords.longitude;
    $('#loc_latitude').val(lat);
    $('#loc_longitude').val(lng);
}
function initMap() {
    var map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: parseFloat($('#loc_latitude').val()),
                lng: parseFloat($('#loc_longitude').val())},
        zoom: 17
    });

    var card = document.getElementById('pac-card');
    var input = document.getElementById('pin_address');
    var types = document.getElementById('type-selector');
    var strictBounds = document.getElementById('strict-bounds-selector');

    map.controls[google.maps.ControlPosition.TOP_RIGHT].push(card);

    // var options = {
    //     componentRestrictions: {country: "ph"}
    // };
    // console.log(options.componentRestrictions);
    var autocomplete = new google.maps.places.Autocomplete(input);
    const geocoder = new google.maps.Geocoder();
    // autocomplete.setComponentRestrictions({'country': ['ph']});

    // Bind the map's bounds (viewport) property to the autocomplete object,
    // so that the autocomplete requests use the current map bounds for the
    // bounds option in the request.
    infowindow = new google.maps.InfoWindow();
    autocomplete.bindTo('bounds', map);

    var infowindowContent = document.getElementById('infowindow-content');
        infowindow.setContent(infowindowContent);
        marker = new google.maps.Marker({
              map: map,
              anchorPoint: new google.maps.Point(0, -29),
              draggable: true
    });

    google.maps.event.addListener(marker, 'dragend', function(event){
      document.getElementById('loc_latitude').value = event.latLng.lat();
      document.getElementById('loc_longitude').value = event.latLng.lng();
      var pos = {
                  lat: event.latLng.lat(),
                  lng: event.latLng.lng()
                };
      geocodeLatLng(geocoder, map, infowindow, pos);
    });


    if (navigator.geolocation) {//HERE WE PIN THE DEFAULT OR YOU WANT TO LOAD VALUES OF LONG LAT
        navigator.geolocation.getCurrentPosition(function(position) {
            if($('#loc_latitude').val() != "" && $('#loc_longitude').val() != ""){
                var pos = {
                    lat: parseFloat($('#loc_latitude').val()),
                    lng: parseFloat($('#loc_longitude').val())
                };
            }else{
                var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
            }

            infowindow.setPosition(pos);
            map.setCenter(pos);
            map.setZoom(17);
            marker.setPosition(pos);
            marker.setVisible(true);
            geocodeLatLng(geocoder, map, infowindow, pos);

        }, function() {
            handleLocationError(true, infoWindow, map.getCenter());
        });
    } else {
        // Browser doesn't support Geolocation
        handleLocationError(false, infoWindow, map.getCenter());
    }
    function handleLocationError(browserHasGeolocation, infoWindow, pos) {
        infoWindow.setPosition(pos);
        alert(browserHasGeolocation ?
                              'Error: The Geolocation service failed.' :
                              'Error: Your browser doesn\'t support geolocation.');
    }

    autocomplete.addListener('place_changed', function() {
        infowindow.close();
        marker.setVisible(false);
        var place = autocomplete.getPlace();
        // console.log(place);
        var latitude = place.geometry.location.lat();
        var longitude = place.geometry.location.lng();

        if (!place.geometry) {
            // User entered the name of a Place that was not suggested and
            // pressed the Enter key, or the Place Details request failed.
            window.alert("No details available for input: '" + place.name + "'");
            return;
        }

        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  // Why 17? Because it looks good.
        }
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);
        var changed_latlong = {
                lat: latitude,
                lng: longitude
            };
        geocodeLatLng(geocoder, map, infowindow, changed_latlong);
        document.getElementById('loc_latitude').value = latitude;
        document.getElementById('loc_longitude').value = longitude;

    });
  }

function initializeMaps(){
    $("#map").addClass( "maploader" );

    setTimeout(function(){ 
        initMap();
        $("#map").removeClass( "maploader" ); 
    }, 2000);
}

function geocodeLatLng(geocoder, map, infowindow, pos) {// REVERSE GEOCODE FOR DISPLAYING THE ADDRESS USING THE LATLONG
  const latlng = pos;
    geocoder.geocode(
      {
        location: latlng
      },
      (results, status) => {
        if (status === "OK") {
          console.log(results);
          if (results[1]) {
            map.setZoom(17);
            marker.setPosition(latlng);
            marker.setVisible(true);
            infowindow.setContent(results[1].formatted_address);
            $("#pin_address").val(results[1].formatted_address);
            infowindow.open(map, marker);
          } else {
            // window.alert("No results found");
          }
        } else {
          // window.alert("Geocoder failed due to: " + status);
        }
      }
    );
}