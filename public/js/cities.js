let markers = [];
let map;

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 1,
        center: {lat: 0, lng: 0}
    });
}

function addMarkers(markers) {
    for (let i = 0; i < markers.length; i++) {
        markers[i].setMap(map);
    }
}

function deleteMarkers(markers) {
    for (let i = 0; i < markers.length; i++) {
        markers[i].setMap(null);
    }
}

function parseResponse(response) {
    deleteMarkers(markers);
    markers = [];
    $.each(response, function (key, city) {
        let latlng = new google.maps.LatLng(city.latitude, city.longitude);
        let marker = new google.maps.Marker({
            position: latlng,
            map: map,
            title: city.name,
            visible: true
        });
        markers.push(marker);
    });
    addMarkers(markers);
}

$(document).ready(function () {
    $.ajax({
        url: "/search",
        method: "GET",
        dataType: "json",
        success: function (result) {
            let obj = JSON.parse(result.data);
            $("#tags").autocomplete({
                source: function (request, response) {
                    let results = $.ui.autocomplete.filter(obj, request.term);
                    response(results.slice(0, 100));
                },
                select: function (event, ui) {
                    $(".alert-danger").addClass("hidden").text("");
                    $.ajax({
                        url: "/get-nearest-cities",
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {'city': ui.item.value},
                        dataType: "json",
                        success: function (result) {
                            parseResponse(JSON.parse(result.data))
                        },
                        error: function (errors) {
                            $(".alert-danger").removeClass("hidden").text(errors.responseJSON.data);
                        },
                    });
                }
            });
        },
    });
});