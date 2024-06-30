<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

$sql = "SELECT b.*, h.name as hall_name FROM bookings b 
        JOIN halls h ON b.hall_id = h.id 
        WHERE b.user_id=" . $user['id'];
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Bookings</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h1>My Bookings</h1>
    <table>
        <tr>
            <th>Event Name</th>
            <th>Speaker</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Hall</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['event_name']; ?></td>
                <td><?php echo $row['speaker']; ?></td>
                <td><?php echo $row['start_time']; ?></td>
                <td><?php echo $row['end_time']; ?></td>
                <td><?php echo $row['hall_name']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td>
                    <a href="view_letter.php?id=<?php echo $row['id']; ?>">View Letter</a>
                    <?php if ($row['status'] == 'PENDING') { ?>
                        <a href="edit_booking.php?id=<?php echo $row['id']; ?>">Edit</a>
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
