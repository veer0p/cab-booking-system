// Initialize the map control with your Bing Maps API key
Microsoft.Maps.loadModule('Microsoft.Maps.AutoSuggest', function () {
    const options = {
        maxResults: 5, // Adjust the number of suggestions as needed
        map: new Microsoft.Maps.Map(document.createElement('div')),
    };

    const manager = new Microsoft.Maps.AutosuggestManager(options);
    manager.attachAutosuggest('#pick-up-location', '#pick-up-location', selectedSuggestion);
    manager.attachAutosuggest('#drop-location', '#drop-location', selectedSuggestion);
});

// Global variables for markers
let pickupMarker, dropoffMarker;

// Function to handle the selected suggestion
function selectedSuggestion(suggestionResult) {
    const location = suggestionResult.location;
    const type = suggestionResult.source.charAt(0) === 'p' ? 'pickup' : 'dropoff';

    const pin = new Microsoft.Maps.Pushpin(location);

    if (type === 'pickup') {
        if (pickupMarker) {
            map.entities.remove(pickupMarker);
        }
        pickupMarker = pin;
        document.getElementById('pick-up-location').value = suggestionResult.formattedSuggestion;
    } else if (type === 'dropoff') {
        if (dropoffMarker) {
            map.entities.remove(dropoffMarker);
        }
        dropoffMarker = pin;
        document.getElementById('drop-location').value = suggestionResult.formattedSuggestion;

        // Trigger path calculation when drop-off location is selected
        calculateDirections();
    }

    map.setView({ center: location });
    map.entities.push(pin);
}

// Your existing calculateDirections function
function calculateDirections() {
    // Implement your logic to calculate directions here
    console.log('Calculating directions...');
}
