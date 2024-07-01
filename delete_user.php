<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("HTTP/1.1 403 Forbidden");
    exit("Error: Unauthorized access. Please log in.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_role = $_SESSION['user']['role'];
    $user_id = $_POST['user_id'];

    // Check user role and permission to delete
    if ($user_role == 'PRINCIPAL' || $user_role == 'ADMINISTRATIVE' ) {
        // Fetch user details to get the roll_no
        $sql = "SELECT roll_no FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($roll_no);
        $stmt->fetch();
        $stmt->close();

        if ($roll_no) {
            // Delete user photo
            $photo_path = 'uploads/users_photo/' . $roll_no . '.jpg'; // Adjust the extension if needed
            if (file_exists($photo_path)) {
                unlink($photo_path);
            }

            // Delete related bookings
            $sql = "DELETE FROM bookings WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();

            // Delete user from database
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                // Success message
                header("HTTP/1.1 200 OK");
                echo "User and their bookings have been deleted successfully.";
            } else {
                // Error message
                header("HTTP/1.1 500 Internal Server Error");
                echo "Error deleting user: " . $conn->error;
            }
            $stmt->close();
        } else {
            // Set error message if user not found
            header("HTTP/1.1 404 Not Found");
            echo "User not found.";
        }
    } else {
        header("HTTP/1.1 403 Forbidden");
        echo "Error: You do not have permission to delete this user.";
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo "Error: Method not allowed.";
}

$conn->close();
?>
