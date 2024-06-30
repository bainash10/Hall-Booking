<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "No booking ID provided";
    exit();
}

$booking_id = intval($_GET['id']);  // Ensure the ID is an integer

$sql = "SELECT * FROM bookings WHERE id=$booking_id AND user_id=" . $user['id'];
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "No such booking found";
    exit();
}

$booking = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = $_POST['event_name'];
    $speaker = $_POST['speaker'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $letter = !empty($_FILES['letter']['tmp_name']) ? addslashes(file_get_contents($_FILES['letter']['tmp_name'])) : $booking['letter'];

    $sql = "UPDATE bookings SET event_name='$event_name', speaker='$speaker', start_time='$start_time', end_time='$end_time', letter='$letter' WHERE id=$booking_id AND user_id=" . $user['id'];

    if ($conn->query($sql) === TRUE) {
        echo "Booking updated successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Booking</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h1>Edit Booking</h1>
    <form method="POST" action="" enctype="multipart/form-data">
        Event Name: <input type="text" name="event_name" value="<?php echo $booking['event_name']; ?>" required><br>
        Speaker: <input type="text" name="speaker" value="<?php echo $booking['speaker']; ?>" required><br>
        Start Time: <input type="datetime-local" name="start_time" value="<?php echo date('Y-m-d\TH:i', strtotime($booking['start_time'])); ?>" required><br>
        End Time: <input type="datetime-local" name="end_time" value="<?php echo date('Y-m-d\TH:i', strtotime($booking['end_time'])); ?>" required><br>
        Letter: <input type="file" name="letter" accept=".pdf,.doc,.docx"><br>
        <button type="submit">Update Booking</button>
    </form>

    <a href="view_bookings.php">Back to My Bookings</a>

    <footer>
        <p>Developed by Nischal Baidar</p>
    </footer>
</body>
</html>
