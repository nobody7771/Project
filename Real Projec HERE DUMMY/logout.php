<?php
session_start(); // Find the session
session_unset(); // Remove all variables (Cart, User ID)
session_destroy(); // Destroy the session completely

// Redirect back to Home
header("Location: Home.php");
exit;
?>