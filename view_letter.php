<?php
include 'config.php'; // Ensure config.php is correctly included with database connection setup

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Invalid request";
    exit();
}

$booking_id = intval($_GET['id']);

// Fetch the booking record from the database based on $booking_id
$sql = "SELECT * FROM bookings WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Booking not found";
    exit();
}

$booking = $result->fetch_assoc();

// Assuming 'letter_path' field stores the file path, adjust according to your database schema
$letter_path = $booking['letter_path'];

if (file_exists($letter_path)) {
    // Set appropriate headers for file display
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . basename($letter_path) . '"');
    
    // Output the file content
    readfile($letter_path);
} else {
    echo "Error: File not found.";
}

$stmt->close();
?>
