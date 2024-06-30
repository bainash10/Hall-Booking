<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user']['role'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $user_role == 'PRINCIPAL') {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];
    $approval_letter = addslashes(file_get_contents($_FILES['approval_letter']['tmp_name']));

    $sql = "UPDATE bookings SET status='$status', approval_letter='$approval_letter' WHERE id='$booking_id'";

    if ($conn->query($sql) === TRUE) {
        echo "Booking request " . ($status == 'APPROVED' ? 'approved' : 'rejected');
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

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
            <th>Action</th>
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
                    <?php if ($row['status'] == 'PENDING' && $user_role == 'PRINCIPAL') { ?>
                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                            <select name="status" required>
                                <option value="APPROVED">Approve</option>
                                <option value="REJECTED">Reject</option>
                            </select><br>
                            Approval Letter: <input type="file" name="approval_letter" accept=".pdf" required><br>
                            <button type="submit">Submit</button>
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
