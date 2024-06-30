<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("HTTP/1.1 403 Forbidden");
    exit("Error: Unauthorized access. Please log in.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_role = $_SESSION['user']['role'];
    $booking_id = $_POST['booking_id'];

    // Check user role and permission to delete
    if ($user_role == 'PRINCIPAL' || $user_role == 'HOD') {
        // Perform deletion
        $delete_sql = "DELETE FROM bookings WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $booking_id);

        if ($delete_stmt->execute()) {
            // Success message
            header("HTTP/1.1 200 OK");
            echo "Booking deleted successfully.";
        } else {
            // Error message
            header("HTTP/1.1 500 Internal Server Error");
            echo "Error deleting booking: " . $conn->error;
        }
        $delete_stmt->close();
    } else {
        header("HTTP/1.1 403 Forbidden");
        echo "Error: You do not have permission to delete this booking.";
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo "Error: Method not allowed.";
}

$conn->close();
?>
