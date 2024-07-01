<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

$message = ""; // Initialize message variable

// Check if deletion request was made
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['delete_message'])) {
    $message = $_GET['delete_message'];
}

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
    <h1>My Bookings</h1>

    <?php if (!empty($message)) : ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <table>
        <tr>
            <th>Event Name</th>
            <th>Speaker</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Hall</th>
            <th>Status</th>
            <th>Request Letter</th>
            <th>Approval Letter</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['event_name']; ?></td>
                <td><?php echo $row['speaker']; ?></td>
                <td><?php echo $row['start_time']; ?></td>
                <td><?php echo $row['end_time']; ?></td>
                <td><?php echo $row['hall_name']; ?></td>
                <td class="<?php 
                    if ($row['status'] == 'PENDING') {
                        echo 'status-pending';
                    } elseif ($row['status'] == 'REJECTED') {
                        echo 'status-rejected';
                    } elseif ($row['status'] == 'APPROVED' && !empty($row['approval_letter_path'])) {
                        echo 'status-approved';
                    } ?>">
                    <?php echo $row['status']; ?>
                </td>
                <td>
                    <?php if (!empty($row['letter_path'])) : ?>
                        <a href="view_letter.php?id=<?php echo $row['id']; ?>" target="_blank">View Request Letter</a>
                    <?php else : ?>
                        No Request Letter
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($row['status'] == 'APPROVED' && !empty($row['approval_letter_path'])) : ?>
                        <a href="<?php echo $row['approval_letter_path']; ?>" target="_blank">View Approval Letter</a>
                    <?php elseif ($row['status'] == 'APPROVED' && empty($row['approval_letter_path'])) : ?>
                        No Approval Letter
                    <?php elseif ($row['status'] == 'PENDING') : ?>
                        Pending Approval
                    <?php else : ?>
                        Approval Not Granted
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($row['status'] == 'PENDING') { ?>
                        <a href="edit_booking.php?id=<?php echo $row['id']; ?>">Edit</a>
                        <a href="delete_booking.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this booking?')">Delete</a>
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
