<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

$message = ""; // Initialize message variable

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $booking_id = $_GET['id'];

    // Check if the user is the requester of the booking
    $check_sql = "SELECT user_id FROM bookings WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $booking_id);
    $check_stmt->execute();
    $check_stmt->bind_result($requester_id);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($requester_id == $user_id) {
        // Proceed with deletion
        $delete_sql = "DELETE FROM bookings WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $booking_id);

        if ($delete_stmt->execute()) {
            $message = "Booking deleted successfully";
        } else {
            $message = "Error deleting booking: " . $delete_stmt->error;
        }
        $delete_stmt->close();
    } else {
        $message = "Unauthorized access to delete this booking.";
    }
} else {
    $message = "Unauthorized access or missing booking ID.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Booking</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h1>Delete Booking</h1>

    <div class="message">
        <?php echo $message; ?>
    </div>

    <a href="view_bookings.php">Back to My Bookings</a>

    <footer>
        <p>Developed by Nischal Baidar</p>
    </footer>
</body>
</html>
