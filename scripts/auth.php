<?php
session_start();

function checkAuthentication() {
    if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
        // Redirect to the login page if the user is not authenticated
        header("Location: ../pages/login.php");
        exit;
    }
}
?>
