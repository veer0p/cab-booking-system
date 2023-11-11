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

    document.getElementById('confirm-locations').addEventListener('click', function () {
        if (pickupMarker && dropoffMarker) {
            // Calculate and display the path
            directionsManager.clearAll();
            directionsManager.addWaypoint(new Microsoft.Maps.Directions.Waypoint({ location: pickupMarker.getLocation() }));
            directionsManager.addWaypoint(new Microsoft.Maps.Directions.Waypoint({ location: dropoffMarker.getLocation() }));
            directionsManager.calculateDirections();

            // Show the "Request Now" button
            document.getElementById('submit-button').style.display = 'block';
        } else {
            alert('Please select both pickup and drop-off locations.');
        }
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
                } else {
                    alert('Location not found');
                }
            }
        };

        searchManager.geocode(searchRequest);
    }
}

// Call the getMaps function to initialize the map
getMaps();
