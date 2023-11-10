<?php
$db_host = 'localhost';
$db_user = 'veer';
$db_pass = '@Veer.idk';
$db_name = 'veer';

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

