<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'HOD') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hall_id = $_POST['hall_id'];
    $event_name = $_POST['event_name'];
    $speaker = $_POST['speaker'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $letter = $_FILES['letter']['name'];

    $target_dir = "uploads/letters/";
    $target_file = $target_dir . basename($_FILES['letter']['name']);
    move_uploaded_file($_FILES['letter']['tmp_name'], $target_file);

    $user_id = $_SESSION['user']['id'];

    $sql = "INSERT INTO bookings (hall_id, user_id, event_name, speaker, start_time, end_time, letter) 
            VALUES ('$hall_id', '$user_id', '$event_name', '$speaker', '$start_time', '$end_time', '$letter')";

    if ($conn->query($sql) === TRUE) {
        echo "Booking request submitted";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Hall</title>
</head>
<body>
    <form method="POST" action="" enctype="multipart/form-data">
        Hall:
        <select name="hall_id" required>
            <?php
            $sql = "SELECT * FROM halls";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['name'] . " (" . $row['college'] . ")</option>";
            }
            ?>
        </select><br>
        Event Name: <input type="text" name="event_name" required><br>
        Speaker: <input type="text" name="speaker" required><br>
        Start Time: <input type="datetime-local" name="start_time" required><br>
        End Time: <input type="datetime-local" name="end_time" required><br>
        Letter: <input type="file" name="letter" accept="application/pdf" required><br>
        <button type="submit">Book Hall</button>
    </form>
</body>
</html>
