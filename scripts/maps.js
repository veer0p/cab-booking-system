"use strict";

const pickupInput = document.getElementById("pickupLocation");
const dropoffInput = document.getElementById("dropoffLocation");
const searchBtn = document.querySelector(".search_btn");
let map, searchManager;

searchBtn.addEventListener("click", () => {
    clearMapEntities();
    geocodeQuery(pickupInput.value, "pickupMap");
    geocodeQuery(dropoffInput.value, "dropoffMap");
});

function getMap() {
    map = new Microsoft.Maps.Map('#pickupMap', {
        credentials: 'Ak9O0_uk29mapIHmjtgj4MH_4dna5EQKlKAKoZtetwsEc7TxvUAJCPhEYmxwJ5CO',
    });

    // Initialize map for drop-off location
    new Microsoft.Maps.Map('#dropoffMap', {
        credentials: 'Ak9O0_uk29mapIHmjtgj4MH_4dna5EQKlKAKoZtetwsEc7TxvUAJCPhEYmxwJ5CO',
    });
}

function geocodeQuery(query, mapId) {
    if (!searchManager) {
        Microsoft.Maps.loadModule('Microsoft.Maps.Search', function () {
            searchManager = new Microsoft.Maps.Search.SearchManager(map);
            geocodeQuery(query, mapId);
        });
    } else {
        let searchRequest = {
            where: query,
            callback: function (r) {
                if (r && r.results && r.results.length > 0) {
                    const pin = new Microsoft.Maps.Pushpin(r.results[0].location);
                    map.entities.push(pin);

                    const targetMap = new Microsoft.Maps.Map(`#${mapId}`, {
                        credentials: 'Ak9O0_uk29mapIHmjtgj4MH_4dna5EQKlKAKoZtetwsEc7TxvUAJCPhEYmxwJ5CO',
                        center: r.results[0].location,
                        zoom: 15,
                    });

                    const targetPin = new Microsoft.Maps.Pushpin(r.results[0].location);
                    targetMap.entities.push(targetPin);

                    targetMap.setView({ bounds: r.results[0].bestView });
                } else {
                    alert(`No results found for ${query}.`);
                }
            },
            errorCallback: function (e) {
                alert(`Error: ${e.message}`);
            }
        };

        searchManager.geocode(searchRequest);
    }
}

function clearMapEntities() {
    map.entities.clear();
    document.getElementById("pickupMap").innerHTML = "";
    document.getElementById("dropoffMap").innerHTML = "";
}
