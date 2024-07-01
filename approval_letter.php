<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'PRINCIPAL') {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['booking_id'])) {
    echo "No booking ID found.";
    exit();
}

$booking_id = $_SESSION['booking_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['approval_letter']) && $_FILES['approval_letter']['error'] === UPLOAD_ERR_OK) {
        $approval_letter_tmp = $_FILES['approval_letter']['tmp_name'];
        $file_type = mime_content_type($approval_letter_tmp);

        // Restrict file type to .pdf
        if ($file_type != 'application/pdf') {
            echo "Error: Only .pdf files are allowed.";
            exit();
        }

        // Ensure the upload directory exists
        $upload_dir = 'uploads/approval_letters/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $approval_letter_path = $upload_dir . 'approval_letter_' . $booking_id . '.pdf';

        if (move_uploaded_file($approval_letter_tmp, $approval_letter_path)) {
            // Update the booking status and approval letter path in the database
            $status = 'APPROVED';
            $sql_update_booking = "UPDATE bookings SET status=?, approval_letter_path=? WHERE id=?";
            $update_stmt = $conn->prepare($sql_update_booking);
            $update_stmt->bind_param("ssi", $status, $approval_letter_path, $booking_id);

            if ($update_stmt->execute()) {
                echo "Booking request approved.";
                unset($_SESSION['booking_id']);
                header("Location: approve_request.php");
                exit();
            } else {
                echo "Error updating booking: " . $update_stmt->error;
            }
            $update_stmt->close();
        } else {
            echo "Error moving uploaded file.";
        }
    } else {
        echo "Error: Approval letter is required for approval.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Approval Letter</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h2>Upload Approval Letter</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="approval_letter">Approval Letter:</label>
        <input type="file" name="approval_letter" id="approval_letter" accept=".pdf" required><br>
        <button type="submit">Submit</button>
    </form>
    <a href="approve_request.php">Cancel</a>
</body>
</html>

<?php
$conn->close(); // Close the database connection
?>
