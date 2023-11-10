<?php
include("../includes/db_connect.php");
include('../otp/phpmailer_smtp/email.php');
include("../scripts/auth.php");
checkAuthentication();

$email = $_SESSION['email'];

// Ensure the email parameter is not empty and is a valid email address
if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {

    // Create a database connection
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

    // Check for a successful connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Generate a random OTP
    $otp = rand(1000, 9999);

    // Customize the message based on the user type
    $subject = "OTP for ";
    $sql = "";
    $message = "";
    $redirect_url = "";

    if ($_SESSION['user_type'] === "user") {
        $sql = "UPDATE users SET otp = ? WHERE email = ?";
        $subject .= "User Sign-up";
        $message = "Welcome to our cab booking system! Your OTP for user registration is: " . $otp;
        smtp_mailer($email, $subject, $message);
        $redirect_url = "verify_otp.php";
    } elseif ($_SESSION['user_type'] === "driver") {
        $sql = "UPDATE drivers SET otp = ? WHERE email = ?";
        $subject .= "Driver Sign-up";
        $message = "Thank you for joining our driver network! Your OTP for driver registration is: " . $otp;
        smtp_mailer($email, $subject, $message);

        $redirect_url = "verify_otp.php";
    } else {
        echo "Invalid user type.";
        exit;
    }

    // Use prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $otp, $email);

    if (mysqli_stmt_execute($stmt)) {
        // Redirect to OTP verification page
        header("Location: $redirect_url?email=" . urlencode($email));
        exit();
    } else {
        echo "Error updating OTP: " . mysqli_stmt_error($stmt);
    }

    // Close the prepared statement and the database connection
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

} else {
    echo "Invalid or missing email address.";
}
?>
