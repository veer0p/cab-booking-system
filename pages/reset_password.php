<?php
include("../includes/db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $token = $_GET["token"];
    $source = $_GET["source"];

    if (empty($token) || empty($source) || ($source != 'users' && $source != 'drivers')) {
        header("Location: ../pages/login.php"); // Redirect to login page
        exit;
    }
}
elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = mysqli_real_escape_string($conn, $_POST["token"]);
    $source = mysqli_real_escape_string($conn, $_POST["source"]);
    $password1 = $_POST["password1"];
    $password2 = $_POST["password2"];

    if (empty($token) || empty($source) || ($source != 'users' && $source != 'drivers')) {
        header("Location: ../pages/login.php"); // Redirect to login page
        exit;
    }

    if ($password1 != $password2) {
        die("Passwords do not match. Please try again.");
    }

    // Hash the password
    $hashedPassword = password_hash($password1, PASSWORD_DEFAULT);

    // Update the password in the respective table
    $updateQuery = "UPDATE $source SET Password = '$hashedPassword', token = NULL WHERE token = '$token'";
    $conn->query($updateQuery);

    echo "Password reset successfully. You can now <a href='../pages/login.php'>login</a> with your new password.";
    exit;
}
else {
    header("Location: ../pages/login.php"); // Redirect to login page for other cases
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>
    <form action="reset_password.php" method="post">
        <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
        <input type="hidden" name="source" value="<?php echo $_GET['source']; ?>">
        
        <label for="password1">Enter your new password:</label>
        <input type="password" name="password1" required>

        <label for="password2">Confirm your new password:</label>
        <input type="password" name="password2" required>

        <input type="submit" value="Reset Password">
    </form>
</body>
</html>
