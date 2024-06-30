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

// Assuming 'letter' field stores the file content, adjust according to your database schema
$letter_content = $booking['letter'];
$letter_name = $booking['event_name'] . '_' . $booking_id . '.pdf'; // Example: Use event name or other identifier as the file name

// Set appropriate headers for file download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $letter_name . '"');

// Output the file content
echo $letter_content;

$stmt->close();
?>
