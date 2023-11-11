<?php
include("../scripts/auth.php");
checkAuthentication();
// Include the database connection script
require('../includes/db_connect.php');

// Handle form submission
if (isset($_POST['request_now'])) {
    // Get the input data from the form
    $pickUpLocation = $_POST['pick-up-location'];
    $dropLocation = $_POST['drop-location'];

    // Get the email from the URL
    $email = $_SESSION['email'];

    // Find the user ID based on the email from the Users table
    $sql = "SELECT UserID FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $user_id = $row['UserID'];

            // Get the current date and time
            $currentDate = date("Y-m-d");
            $currentTime = date("H:i:s");

            // Insert the booking data into the Bookings table using prepared statements
            $insertSql = "INSERT INTO Bookings (user_id, pickup_location, dropoff_location, booking_status, booking_time, booking_date)
                         VALUES (?, ?, ?, 'requested', ?, ?)";
            
            $stmt = mysqli_prepare($conn, $insertSql);
            mysqli_stmt_bind_param($stmt, "issss", $user_id, $pickUpLocation, $dropLocation, $currentTime, $currentDate);

            if (mysqli_stmt_execute($stmt)) {
                echo "Booking request successfully submitted.";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "User not found with the provided email.";
        }
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    // Close the statement
    mysqli_stmt_close($stmt);
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request a Pick Up and Drop</title>
    <!-- Include Bing Maps API script -->
    <script type="text/javascript" src="http://www.bing.com/api/maps/mapcontrol?callback=getMaps"></script>
    <style>
        /* Style the map containers */
        .map-container {
            height: 300px;
            width: 100%;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Request a Pick Up and Drop</h1>

    <!-- Pick Up Location Map container -->
    <div class="map-container" id="pickup-map"></div>

    <form id="locationForm" action="dashboard.php" method="post">
        <label for="pick-up-location">Pick Up Location:</label>
        <input type="text" id="pick-up-location" name="pick-up-location" required><br><br>

        <!-- Drop Off Location Map container -->
        <div class="map-container" id="dropoff-map"></div>

        <label for="drop-location">Drop Box Location:</label>
        <input type="text" id="drop-location" name="drop-location" required><br><br>
        
        <!-- Include the email in the URL as a query parameter -->
        <input type="hidden" name="email" value="<?php echo $_SESSION['email']; ?>">

        <input type="submit" name="request_now" value="Request Now">
    </form><br><br>

    <form action="../scripts/log_out.php" method="post">
        <input type="submit" value="Logout">
    </form>

    <script>
        let pickupMap, dropoffMap, pickupSearchManager, dropoffSearchManager;

        function getMaps() {
            // Initialize Pick Up Location Map
            pickupMap = new Microsoft.Maps.Map('#pickup-map', {
                credentials: 'Ak9O0_uk29mapIHmjtgj4MH_4dna5EQKlKAKoZtetwsEc7TxvUAJCPhEYmxwJ5CO'
            });
            Microsoft.Maps.loadModule('Microsoft.Maps.Search', function () {
                pickupSearchManager = new Microsoft.Maps.Search.SearchManager(pickupMap);
            });

            // Initialize Drop Off Location Map
            dropoffMap = new Microsoft.Maps.Map('#dropoff-map', {
                credentials: 'Ak9O0_uk29mapIHmjtgj4MH_4dna5EQKlKAKoZtetwsEc7TxvUAJCPhEYmxwJ5CO'
            });
            Microsoft.Maps.loadModule('Microsoft.Maps.Search', function () {
                dropoffSearchManager = new Microsoft.Maps.Search.SearchManager(dropoffMap);
            });

            // Event listener for Pick Up Location input
            document.getElementById('pick-up-location').addEventListener('input', function () {
                updateMap(this.value, pickupSearchManager, pickupMap);
            });

            // Event listener for Drop Off Location input
            document.getElementById('drop-location').addEventListener('input', function () {
                updateMap(this.value, dropoffSearchManager, dropoffMap);
            });
        }

        function updateMap(location, searchManager, map) {
            if (searchManager) {
                const searchRequest = {
                    where: location,
                    callback: function (results, userData) {
                        if (results && results.results && results.results.length > 0) {
                            const location = results.results[0].location;

                            // Set the map center to the location coordinates
                            map.setView({ center: location });

                            // Create a pushpin for the location
                            const pin = new Microsoft.Maps.Pushpin(location);
                            map.entities.clear();
                            map.entities.push(pin);
                        } else {
                            // Clear map if location is not found
                            map.entities.clear();
                            alert('Location not found');
                        }
                    }
                };

                searchManager.geocode(searchRequest);
            }
        }
    </script>

    <!-- Load the map asynchronously after the page has loaded -->
    <script async defer src="http://www.bing.com/api/maps/mapcontrol?callback=getMaps"></script>
</body>
</html>
