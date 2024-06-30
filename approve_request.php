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
    $check_sql = "SELECT status FROM bookings WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $booking_id);
    $check_stmt->execute();
    $check_stmt->bind_result($current_status);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($current_status === null) {
        $_SESSION['message'] = "Error: This booking does not exist.";
    } elseif ($current_status == 'APPROVED' && $status == 'REJECTED') {
        // Proceed with rejecting the booking
        $sql_update_booking = "UPDATE bookings SET status=? WHERE id=?";
        $update_stmt = $conn->prepare($sql_update_booking);
        $update_stmt->bind_param("si", $status, $booking_id);

        if ($update_stmt->execute()) {
            $_SESSION['message'] = "Booking request rejected.";
        } else {
            $_SESSION['message'] = "Error updating booking: " . $update_stmt->error;
        }
        $update_stmt->close();
    } elseif ($current_status == 'REJECTED' && $status == 'APPROVED') {
        // Redirect to approval letter upload
        $_SESSION['booking_id'] = $booking_id;
        header("Location: approval_letter.php");
        exit();
    } elseif ($current_status == 'REJECTED') {
        $_SESSION['message'] = "Error: This booking has already been rejected.";
    } elseif ($current_status == 'PENDING' && $status == 'APPROVED') {
        // Redirect to approval letter upload for new approvals
        $_SESSION['booking_id'] = $booking_id;
        header("Location: approval_letter.php");
        exit();
    } elseif ($current_status == 'PENDING' || ($current_status == 'APPROVED' && $status == 'APPROVED')) {
        // Handle re-approval without changing approval letter
        $sql_update_booking = "UPDATE bookings SET status=? WHERE id=?";
        $update_stmt = $conn->prepare($sql_update_booking);
        $update_stmt->bind_param("si", $status, $booking_id);

        if ($update_stmt->execute()) {
            $_SESSION['message'] = "Booking request " . ($status == 'APPROVED' ? 'approved' : 'rejected');
        } else {
            $_SESSION['message'] = "Error updating booking: " . $update_stmt->error;
        }
        $update_stmt->close();
    } else {
        $_SESSION['message'] = "Error: Invalid operation for this booking.";
    }
}

// Fetch all bookings
$sql = "SELECT b.id, h.name as hall_name, b.event_name, b.speaker, b.start_time, b.end_time, b.status, u.name as user_name, u.college, u.department 
        FROM bookings b 
        JOIN halls h ON b.hall_id = h.id 
        JOIN users u ON b.user_id = u.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve Requests</title>
    <link rel="stylesheet" type="text/css" href="css/apstyle.css">
    <style>
      .status-pending {
    color: white;
    background-color: orange;
    font-weight: bold;
}

.status-rejected {
    color: white;
    background-color: red;
    font-weight: bold;
}

.status-approved {
    color: white;
    background-color: green;
    font-weight: bold;
}

    </style>
</head>

<body>
    <h2>Approve Requests</h2>
    <?php if (isset($_SESSION['message'])) { ?>
        <div class="message">
            <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']); // Clear the message after displaying it
            ?>
        </div>
    <?php } ?>
    <table>
        <tr>
            <th>Booking ID</th>
            <th>Hall</th>
            <th>Event Name</th>
            <th>Speaker</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Status</th>
            <th>User</th>
            <th>College</th>
            <th>Department</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { 
            // Determine the status class
            $status_class = '';
            if ($row['status'] == 'PENDING') {
                $status_class = 'status-pending';
            } elseif ($row['status'] == 'REJECTED') {
                $status_class = 'status-rejected';
            } elseif ($row['status'] == 'APPROVED') {
                $status_class = 'status-approved';
            }
        ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['hall_name']; ?></td>
                <td><?php echo $row['event_name']; ?></td>
                <td><?php echo $row['speaker']; ?></td>
                <td><?php echo $row['start_time']; ?></td>
                <td><?php echo $row['end_time']; ?></td>
                <td class="<?php echo $status_class; ?>"><?php echo $row['status']; ?></td>
                <td><?php echo $row['user_name']; ?></td>
                <td><?php echo $row['college']; ?></td>
                <td><?php echo $row['department']; ?></td>
                <td>
                    <?php if ($user_role == 'PRINCIPAL') { ?>
                        <?php if ($row['status'] == 'PENDING') { ?>
                            <form method="POST" action="approve_request.php">
                                <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                <select name="status" required>
                                    <option value="APPROVED">Approve</option>
                                    <option value="REJECTED">Reject</option>
                                </select>
                                <button type="submit">Submit</button>
                            </form>
                        <?php } elseif ($row['status'] == 'APPROVED') { ?>
                            <form method="POST" action="approve_request.php">
                                <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="status" value="REJECTED">
                                <button type="submit">Reject</button>
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
