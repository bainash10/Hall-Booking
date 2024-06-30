<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user']['role'];

// Handle booking status update (approve/reject)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $user_role == 'PRINCIPAL') {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];

    // Check if the booking exists and its current status
    $check_sql = "SELECT status, start_time, end_time FROM bookings WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $booking_id);
    $check_stmt->execute();
    $check_stmt->bind_result($current_status, $start_time, $end_time);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($current_status === null) {
        echo "Error: This booking does not exist.";
    } elseif ($current_status == 'APPROVED' && $status == 'REJECTED') {
        // Proceed with rejecting the booking
        $sql_update_booking = "UPDATE bookings SET status=? WHERE id=?";
        $update_stmt = $conn->prepare($sql_update_booking);
        $update_stmt->bind_param("si", $status, $booking_id);

        if ($update_stmt->execute()) {
            echo "Booking request rejected.";
        } else {
            echo "Error updating booking: " . $update_stmt->error;
        }
        $update_stmt->close();
    } elseif ($current_status == 'REJECTED' && $status == 'APPROVED') {
        // Handle approval letter submission if status is REJECTED
        if (isset($_FILES['approval_letter']) && $_FILES['approval_letter']['error'] === UPLOAD_ERR_OK) {
            $approval_letter = addslashes(file_get_contents($_FILES['approval_letter']['tmp_name']));
            if (empty($approval_letter)) {
                echo "Error: Approval letter is required for approval.";
                exit();
            } elseif ($_FILES['approval_letter']['size'] > 5242880) { // 5MB file size limit
                echo "Error: Approval letter file size exceeds the limit of 5MB.";
                exit();
            } elseif (!in_array($_FILES['approval_letter']['type'], array('application/pdf'))) {
                echo "Error: Only PDF files are allowed for approval letter.";
                exit();
            }
        } else {
            echo "Error uploading approval letter.";
            exit();
        }

        // Check for overlapping bookings if approving
        $overlap_found = false;
        $sql_overlap_check = "SELECT id FROM bookings 
                              WHERE id != ? 
                              AND status = 'APPROVED' 
                              AND NOT (end_time <= ? OR start_time >= ?)";
        $stmt_overlap_check = $conn->prepare($sql_overlap_check);
        $stmt_overlap_check->bind_param("iss", $booking_id, $start_time, $end_time);
        $stmt_overlap_check->execute();
        $stmt_overlap_check->store_result();

        if ($stmt_overlap_check->num_rows > 0) {
            $overlap_found = true;
        }

        $stmt_overlap_check->close();

        if ($overlap_found) {
            echo "Error: This booking overlaps with an existing approved booking.";
            exit();
        }

        // Proceed with updating the booking status and approval letter
        $sql_update_booking = "UPDATE bookings SET status=?, approval_letter=? WHERE id=?";
        $update_stmt = $conn->prepare($sql_update_booking);
        $update_stmt->bind_param("ssi", $status, $approval_letter, $booking_id);

        if ($update_stmt->execute()) {
            echo "Booking request approved.";
        } else {
            echo "Error updating booking: " . $update_stmt->error;
        }
        $update_stmt->close();
    } elseif ($current_status == 'REJECTED') {
        echo "Error: This booking has already been rejected.";
    } elseif ($current_status == 'PENDING' || ($current_status == 'APPROVED' && $status == 'APPROVED')) {
        // Handle re-approval without changing approval letter
        $sql_update_booking = "UPDATE bookings SET status=? WHERE id=?";
        $update_stmt = $conn->prepare($sql_update_booking);
        $update_stmt->bind_param("si", $status, $booking_id);

        if ($update_stmt->execute()) {
            echo "Booking request " . ($status == 'APPROVED' ? 'approved' : 'rejected');
        } else {
            echo "Error updating booking: " . $update_stmt->error;
        }
        $update_stmt->close();
    } else {
        echo "Error: Invalid operation for this booking.";
    }
}

// Fetch all bookings
$sql = "SELECT b.id, h.name as hall_name, b.event_name, b.speaker, b.start_time, b.end_time, b.status, b.letter, u.name as user_name, u.college, u.department 
        FROM bookings b 
        JOIN halls h ON b.hall_id = h.id 
        JOIN users u ON b.user_id = u.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve Requests</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
    <table border="1">
        <tr>
            <th>Booking ID</th>
            <th>Hall</th>
            <th>Event Name</th>
            <th>Speaker</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Status</th>
            <th>Letter</th>
            <th>User</th>
            <th>College</th>
            <th>Department</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['hall_name']; ?></td>
                <td><?php echo $row['event_name']; ?></td>
                <td><?php echo $row['speaker']; ?></td>
                <td><?php echo $row['start_time']; ?></td>
                <td><?php echo $row['end_time']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td><a href="view_letter.php?id=<?php echo $row['id']; ?>">View Letter</a></td>
                <td><?php echo $row['user_name']; ?></td>
                <td><?php echo $row['college']; ?></td>
                <td><?php echo $row['department']; ?></td>
                <td>
                    <?php if ($user_role == 'PRINCIPAL') { ?>
                        <?php if ($row['status'] == 'PENDING') { ?>
                            <form method="POST" action="approve_request.php" enctype="multipart/form-data">
                                <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                <select name="status" required>
                                    <option value="APPROVED">Approve</option>
                                    <option value="REJECTED">Reject</option>
                                </select><br>
                                Approval Letter: <input type="file" name="approval_letter" accept=".pdf" required><br>
                                <button type="submit">Submit</button>
                            </form>
                        <?php } elseif ($row['status'] == 'APPROVED') { ?>
                            <form method="POST" action="approve_request.php">
                                <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="status" value="REJECTED">
                                <button type="submit">Reject</button>
                            </form>
                            <form method="POST" action="approve_request.php">
                                <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="status" value="APPROVED">
                                <button type="submit">Approve Again</button>
                            </form>
                        <?php } elseif ($row['status'] == 'REJECTED') { ?>
                            <form method="POST" action="approve_request.php">
                                <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="status" value="APPROVED">
                                <button type="submit">Approve</button>
                            </form>
                        <?php } ?>
                        <form method="POST" action="delete_request.php" onsubmit="return confirm('Are you sure you want to delete this booking?');">
                            <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    <?php } elseif ($user_role == 'HOD') { ?>
                        <form method="POST" action="delete_request.php" onsubmit="return confirm('Are you sure you want to delete this booking?');">
                            <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    <?php } else { ?>
                        No actions available
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
    <a href="dashboard.php">Back to Dashboard</a>
    <footer>
        <p>Developed by Nischal Baidar</p>
    </footer>
</body>
</html>

<?php
$conn->close(); // Close the database connection
?>
