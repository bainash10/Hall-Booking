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

$sql = "SELECT * FROM bookings WHERE id=? AND user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "No such booking found";
    exit();
}

$booking = $result->fetch_assoc();

// Variables to hold form values
$event_name = $booking['event_name'];
$speaker = $booking['speaker'];
$start_time = date('Y-m-d\TH:i', strtotime($booking['start_time']));
$end_time = date('Y-m-d\TH:i', strtotime($booking['end_time']));
$letter = $booking['letter']; // Default to existing letter

// Check if form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = $_POST['event_name'];
    $speaker = $_POST['speaker'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Check if a new file was uploaded
    if (!empty($_FILES['letter']['tmp_name']) && is_uploaded_file($_FILES['letter']['tmp_name'])) {
        $letter = file_get_contents($_FILES['letter']['tmp_name']);
    }

    // Validate if a file was uploaded
    if (empty($_FILES['letter']['tmp_name']) && empty($letter)) {
        echo "Please choose a file for the letter.";
    } else {
        // Prepare and execute the SQL update query
        $sql = "UPDATE bookings SET event_name=?, speaker=?, start_time=?, end_time=?, letter=? WHERE id=? AND user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $event_name, $speaker, $start_time, $end_time, $letter, $booking_id, $user['id']);

        if ($stmt->execute()) {
            echo "Booking updated successfully";
            // Optionally redirect or perform other actions after successful update
        } else {
            echo "Error updating booking: " . $stmt->error;
        }

        $stmt->close();
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
        Event Name: <input type="text" name="event_name" value="<?php echo htmlspecialchars($event_name); ?>" required><br>
        Speaker: <input type="text" name="speaker" value="<?php echo htmlspecialchars($speaker); ?>" required><br>
        Start Time: <input type="datetime-local" name="start_time" value="<?php echo $start_time; ?>" required><br>
        End Time: <input type="datetime-local" name="end_time" value="<?php echo $end_time; ?>" required><br>
        <?php if (!empty($letter)) : ?>
        <p>Current Letter: <a href="download_letter.php?id=<?php echo $booking['id']; ?>"><?php echo "Download"; ?></a></p>
        <?php endif; ?>
        New Letter: <input type="file" name="letter" accept=".pdf,.doc,.docx"><br>
        <button type="submit">Update Booking</button>
    </form>

    <a href="view_bookings.php">Back to My Bookings</a>

    <footer>
        <p>Developed by Nischal Baidar</p>
    </footer>
</body>
</html>
