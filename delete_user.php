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
    if ($user_role == 'PRINCIPAL' || $user_role == 'ADMINISTRATIVE') {
        // Check if user exists and fetch roll number
        $check_user_sql = "SELECT roll_no FROM users WHERE id = ?";
        $stmt_check_user = $conn->prepare($check_user_sql);
        $stmt_check_user->bind_param("i", $user_id);
        $stmt_check_user->execute();
        $stmt_check_user->store_result();

        if ($stmt_check_user->num_rows > 0) {
            $stmt_check_user->bind_result($roll_no);
            $stmt_check_user->fetch();
            $stmt_check_user->close();

            // Begin transaction for atomic operations
            $conn->begin_transaction();

            try {
                // Step 1: Delete related bookings
                $delete_bookings_sql = "DELETE FROM bookings WHERE user_id = ?";
                $stmt_delete_bookings = $conn->prepare($delete_bookings_sql);
                $stmt_delete_bookings->bind_param("i", $user_id);
                $stmt_delete_bookings->execute();
                $stmt_delete_bookings->close();

                // Step 2: Delete user photo if exists
                if ($roll_no) {
                    $photo_path = 'uploads/users_photo/' . $roll_no . '.jpg';
                    if (file_exists($photo_path)) {
                        unlink($photo_path);
                    }
                }

                // Step 3: Delete user from users table
                $delete_user_sql = "DELETE FROM users WHERE id = ?";
                $stmt_delete_user = $conn->prepare($delete_user_sql);
                $stmt_delete_user->bind_param("i", $user_id);
                $stmt_delete_user->execute();

                // Commit transaction if all steps succeed
                $conn->commit();

                // Success message
                header("HTTP/1.1 200 OK");
                echo "User and their associated bookings have been deleted successfully.";
            } catch (Exception $e) {
                // Rollback transaction if any step fails
                $conn->rollback();

                // Error message
                header("HTTP/1.1 500 Internal Server Error");
                echo "Error deleting user: " . $e->getMessage();
            }

            $stmt_delete_user->close();
        } else {
            // User not found
            header("HTTP/1.1 404 Not Found");
            echo "User not found.";
        }
    } else {
        // Permission denied
        header("HTTP/1.1 403 Forbidden");
        echo "Error: You do not have permission to delete this user.";
    }
} else {
    // Method not allowed
    header("HTTP/1.1 405 Method Not Allowed");
    echo "Error: Method not allowed.";
}

$conn->close();
?>
