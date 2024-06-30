<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Check if user has permission to delete users
if ($_SESSION['user']['role'] !== 'PRINCIPAL') {
    // Redirect to dashboard or show error message
    header("Location: dashboard.php");
    exit();
}

// Check if ID is provided and is numeric
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = $_GET['id'];

    // Delete user from database
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Redirect back to registered users page
    header("Location: registered_users.php");
    exit();
} else {
    // Handle invalid ID or no ID provided
    header("Location: registered_users.php");
    exit();
}

$conn->close();
?>
