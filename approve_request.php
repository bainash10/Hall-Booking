<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'ADMINISTRATIVE') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];
    $approval_letter = $_FILES['approval_letter']['name'];

    $target_dir = "uploads/letters/";
    $target_file = $target_dir . basename($_FILES['approval_letter']['name']);
    move_uploaded_file($_FILES['approval_letter']['tmp_name'], $target_file);

    $sql = "UPDATE bookings SET status='$status', approval_letter='$approval_letter' WHERE id='$booking_id'";

    if ($conn->query($sql) === TRUE) {
        echo "Booking request " . ($status == 'APPROVED' ? 'approved' : 'rejected');
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$sql = "SELECT b.id, h.name as hall_name, u.name as user_name, b.event_name, b.speaker, b.start_time, b.end_time, b.letter, b.status 
        FROM bookings b 
        JOIN halls h ON b.hall_id = h.id 
        JOIN users u ON b.user_id = u.id 
        WHERE b.status = 'PENDING'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve Requests</title>
</head>
<body>
    <h1>Approve Booking Requests</h1>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <div>
            <h2>Booking Request #<?php echo $row['id']; ?></h2>
            <p>Hall: <?php echo $row['hall_name']; ?></p>
            <p>User: <?php echo $row['user_name']; ?></p>
            <p>Event Name: <?php echo $row['event_name']; ?></p>
            <p>Speaker: <?php echo $row['speaker']; ?></p>
            <p>Start Time: <?php echo $row['start_time']; ?></p>
            <p>End Time: <?php echo $row['end_time']; ?></p>
            <p><a href="uploads/letters/<?php echo $row['letter']; ?>" target="_blank">View Letter</a></p>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                <input type="file" name="approval_letter" accept="application/pdf" required><br>
                <button type="submit" name="status" value="APPROVED">Approve</button>
                <button type="submit" name="status" value="REJECTED">Reject</button>
            </form>
        </div>
        <hr>
    <?php } ?>
</body>
</html>

