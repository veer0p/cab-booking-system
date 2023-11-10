<?php
include("../includes/db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);

    // Check if the email exists in the 'users' table
    $checkQueryUsers = "SELECT * FROM users WHERE Email = '$email'";
    $checkResultUsers = $conn->query($checkQueryUsers);

    // Check if the email exists in the 'drivers' table
    $checkQueryDrivers = "SELECT * FROM drivers WHERE Email = '$email'";
    $checkResultDrivers = $conn->query($checkQueryDrivers);

    if (($checkResultUsers && $checkResultUsers->num_rows > 0) || ($checkResultDrivers && $checkResultDrivers->num_rows > 0)) {
        // Generate a unique token for password reset
        $token = bin2hex(random_bytes(32));

        // Determine the source (users or drivers) based on the query results
        $source = ($checkResultUsers && $checkResultUsers->num_rows > 0) ? 'users' : 'drivers';

        // Store the token and source in the respective table
        $updateQuery = "UPDATE $source SET token = '$token' WHERE Email = '$email'";
        $conn->query($updateQuery);

        // Construct the password reset link
        $resetLink = "http://localhost:3000/pages/reset_password.php?token=$token&source=$source";

        // Send the password reset link via email
        include('../otp/phpmailer_smtp/email.php');
        $subject = "Password Reset";
        $message = "Click the following link to reset your password: $resetLink";
        smtp_mailer($email, $subject, $message);

        echo "Password reset link sent to your email. Please check your inbox.";
    } else {
        echo "Email not found. Please enter a valid email address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>
<body>
    <h2>Forgot Password</h2>
    <form action="forgot_password.php" method="post">
        <label for="email">Enter your email:</label>
        <input type="email" name="email" required>
        <input type="submit" value="Reset Password">
    </form>
</body>
</html>
