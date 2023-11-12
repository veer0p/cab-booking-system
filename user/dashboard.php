<?php

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
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Responsive Navigation Menu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./dashboard.css">
    <script type="text/javascript" src="https://www.bing.com/api/maps/mapcontrol?callback=getMaps&key=Ak9O0_uk29mapIHmjtgj4MH_4dna5EQKlKAKoZtetwsEc7TxvUAJCPhEYmxwJ5CO"></script>
    <script src="../scripts/maps.js"></script>
    <script>
        // Call the getMaps function to initialize the map
        getMaps();
    </script>
</head>
<body>
    <section class="header">
        <nav>
            <div class="logo">MI LUXOR</div>
            <input type="checkbox" id="click">
            <label for="click" class="menu-btn">
                <i class="fas fa-bars"></i>
            </label>
            <ul>
                <li><a class="active" href="#">Home</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Services</a></li>
                <li><a href="#">Cab</a></li>
                <li><a href="#">Feedback</a></li>
            </ul>
        </nav>
        <div class="text-box">
            <h1>there are big news</h1>
            <p>midsugfic ugcubsdjcndsuhcgd ygcdsuscgh
                bcjdgcdbcdiuchdicndslgcdbcuydg hudsigcdnk
            </p>
        </div>
    </section>

    <div class="map-form-container">
        <div class="map-container" id="dropoff-map"></div>

        <section class="container">
            <div class="book-ride-form">
                <h1>Book Your Ride</h1>
                <form id="locationForm" action="dashboard.php" method="post">
                    <div class="input-group">
                        <label for="pick-up-location">Pick Up Location</label>
                        <input type="text" id="pick-up-location" name="pick-up-location" placeholder="Type Location" required>
                    </div>

                    <div class="input-group">
                        <label for="drop-location">Drop Off Location:</label>
                        <input type="text" id="drop-location" name="drop-location" placeholder="Type Location" required>
                    </div>

                    <div class="input-group">
                        <label for="cab-type">Cab Type</label>
                        <select name="cab_type" id="cab-type">
                            <option value="car">Car</option>
                            <option value="suv">SUV</option>
                            <option value="van">Van</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="pickup-date">Pick Up Date</label>
                        <input type="date" name="pickup_date" id="pickup-date" placeholder="Pick Up Date">
                    </div>

                    <div class="input-group">
                        <label for="pickup-time">Pick Up Time</label>
                        <input type="time" name="pickup_time" id="pickup-time" placeholder="Pick Up Time">
                    </div>

                    <div class="input-group">
                        <label for="cab-model">Cab Model</label>
                        <select name="cab_model" id="cab-model">
                            <option value="Toyota Camry">Toyota Camry</option>
                            <option value="Honda Accord">Honda Accord</option>
                            <option value="Hyundai Sonata">Hyundai Sonata</option>
                        </select>
                    </div>

                    <button type="submit" id="submit-button">BOOK CAB →</button>
                </form>
            </div>
        </section>
    </div>
</body>
</html>
