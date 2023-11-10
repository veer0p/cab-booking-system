<?php
include("../includes/db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_type = mysqli_real_escape_string($conn, $_POST["user_type"]);
    $first_name = mysqli_real_escape_string($conn, $_POST["first_name"]);
    $last_name = mysqli_real_escape_string($conn, $_POST["last_name"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $gender = mysqli_real_escape_string($conn, $_POST["gender"]);
    $contact_number = mysqli_real_escape_string($conn, $_POST["ContactNumber"]);
    $address = mysqli_real_escape_string($conn, $_POST["address"]);
    $vehicle_name = mysqli_real_escape_string($conn, $_POST["VehicleName"]);
    $vehicle_number = mysqli_real_escape_string($conn, $_POST["VehicleNumber"]);

    // Check if the user with the same email already exists
    $checkQuery = "SELECT * FROM " . ($user_type == 'driver' ? 'drivers' : 'users') . " WHERE Email = '$email'";
    $checkResult = $conn->query($checkQuery);

    if ($checkResult && $checkResult->num_rows > 0) {
        echo "User with this email already exists. Please use a different email.";
    } else {
        // Insert a new user if the email is not registered
        $table = $user_type == 'driver' ? 'drivers' : 'users';
        $insertQuery = "INSERT INTO $table (FirstName, LastName, Email, Password, Gender, ContactNumber, Address,VehicleNumber,VehicleName) 
              VALUES ('$first_name', '$last_name', '$email', '$password', '$gender', '$contact_number', '$address','$vehicle_number', '$vehicle_name')";

        if ($conn->query($insertQuery) === TRUE) {
            // Registration is successful, set up session and redirect
            session_start();
            $_SESSION['user_type'] = $user_type;
            $_SESSION['email'] = $email;
            header("Location: ../otp/send_otp.php");
            exit;
        } else {
            echo "Registration failed. Please check your inputs.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
</head>
<body>
    <h1>User Registration</h1>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <!-- User type selection dropdown -->
        <label for="user_type">Register as:</label>
        <select name="user_type" required>
            <option value="user">User</option>
            <option value="driver">Driver</option>
        </select><br><br>

        <!-- User registration form fields -->
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" required><br><br>
        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" required><br><br>
        <label for="email">Email:</label>
        <input type="email" name="email" required><br><br>
        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>
        <label for="gender">Gender:</label>
        <input type="radio" name="gender" value="male" required>Male
        <input type="radio" name="gender" value="female" required>Female
        <input type="radio" name="gender" value="other" required>Other<br><br>
        <label for="ContactNumber">Contact Number:</label>
        <input type="tel" name="ContactNumber" required><br><br>
        <label for="address">Address:</label>
        <textarea name="address" rows="4" required></textarea><br><br>

        <!-- Vehicle details for drivers -->
        <div id="vehicle_fields" style="display: none;">
            <label for="VehicleName">Vehicle Name:</label>
            <input type="text" name="VehicleName"><br><br>
            <label for="VehicleNumber">Vehicle Number:</label>
            <input type="text" name="VehicleNumber"><br><br>
        </div>

        <input type="submit" value="Sign Up">
    </form>
    <p>Already have an account? <a href="login.php">Log in</a></p>

    <script>
        // Show/hide vehicle fields based on user type selection
        document.querySelector('select[name="user_type"]').addEventListener('change', function () {
            document.getElementById('vehicle_fields').style.display = (this.value === 'driver') ? 'block' : 'none';
        });
    </script>
</body>
</html>
