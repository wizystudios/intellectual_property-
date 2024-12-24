<?php
// Start the session
session_start();

// Check if the user is already logged in
if (isset($_SESSION['username'])) {
    // Redirect to the dashboard or another page
    header("Location: login.php");
    exit();
} else {
    // Redirect to the login page
    header("Location: login.php");
    exit();
}
?>
