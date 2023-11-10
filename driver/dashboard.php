<?php
include("../scripts/auth.php");
checkAuthentication();
// user Dashboard
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form action="../scripts/log_out.php" method="post">
    <input type="submit" value="Logout">
</form>
</body>
</html>