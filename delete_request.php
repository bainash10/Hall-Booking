<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user']['role'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($user_role == 'PRINCIPAL' || $user_role == 'HOD')) {
    $booking_id = $_POST['booking_id'];

    // Check if the user is authorized to delete the booking
    $check_sql = "SELECT user_id FROM bookings WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $booking_id);
    $check_stmt->execute();
    $check_stmt->bind_result($requester_id);
    $check_stmt->fetch();
    $check_stmt->close();

    // Ensure only PRINCIPAL, HOD who requested it, or the original requester can delete
    $current_user_id = $_SESSION['user']['id'];

    if ($user_role == 'PRINCIPAL' || $user_role == 'HOD' || $requester_id == $current_user_id) {
        // Proceed with deletion
        $delete_sql = "DELETE FROM bookings WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $booking_id);

        if ($delete_stmt->execute()) {
            $_SESSION['message'] = "Booking deleted successfully.";
        } else {
            $_SESSION['message'] = "Error deleting booking: " . $delete_stmt->error;
        }
        $delete_stmt->close();
    } else {
        $_SESSION['message'] = "Unauthorized access to delete this booking.";
    }
} else {
    $_SESSION['message'] = "Unauthorized access or missing booking ID.";
}

header("Location: approve_requests.php");
exit();
?>
