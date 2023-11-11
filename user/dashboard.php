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
    <script type="text/javascript" src="https://www.bing.com/api/maps/mapcontrol?callback=getMaps&key=Ak9O0_uk29mapIHmjtgj4MH_4dna5EQKlKAKoZtetwsEc7TxvUAJCPhEYmxwJ5CO"></script>
    <link rel="stylesheet" href="./dashboard.css">
</head>
<body>
    <h1>Request a Pick Up and Drop</h1>

    <div class="map-container" id="dropoff-map"></div>

    <form id="locationForm" action="dashboard.php" method="post" style="margin-bottom: 0;">
        <label for="pick-up-location">Pick Up Location:</label>
        <input type="text" id="pick-up-location" name="pick-up-location" required><br><br>

        <label for="drop-location">Drop Off Location:</label>
        <input type="text" id="drop-location" name="drop-location" required><br><br>

        <button type="button" id="confirm-locations">Confirm Locations</button>
        <!-- Container for displaying total distance and fare -->
        <div id="fare-container"></div>

        <!-- Submit button is now inside the fare-container div -->
        <input type="submit" id="submit-button" name="request_now" value="Request Now" style="display: none;">
    </form>

    <script src="../scripts/maps.js"></script>
    <script>
        // Call the getMaps function to initialize the map
        getMaps();
    </script>
</body>
</html>

</html>