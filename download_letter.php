<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "No booking ID provided";
    exit();
}

$booking_id = intval($_GET['id']);  // Ensure the ID is an integer

$sql = "SELECT letter_path FROM bookings WHERE id=? AND user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "No such booking found";
    exit();
}

$booking = $result->fetch_assoc();
$letter_path = $booking['letter_path'];

if (file_exists($letter_path)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($letter_path) . '"');
    header('Content-Length: ' . filesize($letter_path));
    readfile($letter_path);
    exit();
} else {
    echo "File not found.";
    exit();
}
?>
