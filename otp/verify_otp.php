<?php
include("../includes/db_connect.php");
include("../scripts/auth.php");
checkAuthentication();

$email = $_GET['email'];

// Ensure the email parameter is not empty and is a valid email address
if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {

    // Create a database connection

    // Check for a successful connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    if (isset($_POST['verify'])) {
        $user_type = $_SESSION['user_type'];
        $entered_otp = mysqli_real_escape_string($conn, $_POST['otp']);

        $sql = "SELECT otp FROM " . ($user_type === "user" ? "users" : "drivers") . " WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $stored_otp = $row['otp'];

            if ($entered_otp == $stored_otp) {
                // Verification successful, perform actions and redirect
                echo "Verification successful!";

                // Delete OTP from the database
                $deleteSql = "UPDATE " . ($user_type === "user" ? "users" : "drivers") . " SET otp = NULL WHERE email = '$email'";
                if (mysqli_query($conn, $deleteSql)) {
                    echo " OTP deleted from the database.";

                    // Redirect to the user's dashboard
                    if ($user_type === "user") {
                        header("Location: ../user/dashboard.php");
                    } elseif ($user_type === "driver") {
                        header("Location: ../driver/dashboard.php");
                    } else {
                        echo "Invalid user type.";
                    }
                    exit;
                } else {
                    echo "Error deleting OTP from the database: " . mysqli_error($conn);
                }
            } else {
                echo "Incorrect OTP. Please try again.";
            }
        } else {
            echo "Error retrieving OTP from the database.";
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>OTP Verification</title>
    </head>
    <body>
        <h1>OTP Verification</h1>
        <p>Please enter the OTP sent to your email:</p>
        <form action="" method="post">
            <label for="otp">OTP:</label>
            <input type="text" name="otp" required>
            <input type="submit" name="verify" value="Verify">
        </form>
    </body>
    </html>

    <?php
    // Close the database connection
    mysqli_close($conn);
} else {
    echo "Invalid or missing email address.";
}
?>
