<?php
session_start();
include 'db.php';

if (isset($_SESSION['user_id']) && $_SESSION['username'] === 'admin' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM games WHERE id = $id");
}

header("Location: admin.php");
exit;
?>