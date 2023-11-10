<?php
include("../includes/db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = $_POST["password"];

    $query = "SELECT * FROM users WHERE Email = '$email'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['Password'])) {
            // Login successful, set up session
            session_start();
            $_SESSION['user_type'] = $row['UserType']; // Assuming 'UserType' is the column in your database
            $_SESSION['email'] = $email;

            if ($_SESSION['user_type'] == 'driver') {
                header("Location: ../driver/dashboard.php");
            } else {
                header("Location: ../user/dashboard.php");
            }
            exit;
        } else {
            echo "Invalid password. Please try again.";
        }
    } else {
        echo "User not found. Please check your email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <label for="email">Email:</label>
        <input type="email" name="email" required><br><br>
        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>
        <input type="submit" value="Log In">
    </form>
    <p>Don't have an account? <a href="register.php">Sign up</a></p>
    <p>Forgot password? <a href="./forgot_password.php">click here</a></p>
</body>
</html>
