let map, searchManager, directionsManager;
let pickupMarker, dropoffMarker;

function getMaps() {
    map = new Microsoft.Maps.Map('#dropoff-map', {
        credentials: 'Ak9O0_uk29mapIHmjtgj4MH_4dna5EQKlKAKoZtetwsEc7TxvUAJCPhEYmxwJ5CO'
    });

    Microsoft.Maps.loadModule('Microsoft.Maps.Search', function () {
        searchManager = new Microsoft.Maps.Search.SearchManager(map);
    });

    Microsoft.Maps.loadModule('Microsoft.Maps.Directions', function () {
        directionsManager = new Microsoft.Maps.Directions.DirectionsManager(map);
        directionsManager.setRenderOptions({ itineraryContainer: null }); // Disable rendering directions on the page
    });

    document.getElementById('pick-up-location').addEventListener('input', function () {
        updateMap(this.value, 'pickup');
    });

    document.getElementById('drop-location').addEventListener('input', function () {
        updateMap(this.value, 'dropoff');
    });
}

function updateMap(location, type) {
    if (searchManager) {
        const searchRequest = {
            where: location,
            callback: function (results) {
                if (results && results.results && results.results.length > 0) {
                    const location = results.results[0].location;
                    map.setView({ center: location });

                    const pin = new Microsoft.Maps.Pushpin(location);

                    if (type === 'pickup') {
                        if (pickupMarker) {
                            map.entities.remove(pickupMarker);
                        }
                        pickupMarker = pin;
                    } else if (type === 'dropoff') {
                        if (dropoffMarker) {
                            map.entities.remove(dropoffMarker);
                        }
                        dropoffMarker = pin;
                    }

                    map.entities.push(pin);

                    // Add event listeners to both markers for the location change
                    Microsoft.Maps.Events.addHandler(pickupMarker, 'dragend', displayPathIfBothMarkersSet);
                    Microsoft.Maps.Events.addHandler(dropoffMarker, 'dragend', displayPathIfBothMarkersSet);

                    // Call the displayPathIfBothMarkersSet function after updating the markers
                    displayPathIfBothMarkersSet();
                } else {
                    alert('Location not found');
                }
            }
        };

        searchManager.geocode(searchRequest);
    }
}

function displayPathIfBothMarkersSet() {
    if (pickupMarker && dropoffMarker) {
        // Calculate and display the path
        directionsManager.clearAll();
        directionsManager.addWaypoint(new Microsoft.Maps.Directions.Waypoint({ location: pickupMarker.getLocation() }));
        directionsManager.addWaypoint(new Microsoft.Maps.Directions.Waypoint({ location: dropoffMarker.getLocation() }));
        directionsManager.calculateDirections();
    }
}

// Call the getMaps function to initialize the map
getMaps();
