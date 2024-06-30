<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['HOD', 'EXAMSECTION'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$allowed_college = $user['college'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hall_id = $_POST['hall_id'];
    $event_name = $_POST['event_name'];
    $speaker = $_POST['speaker'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $letter = addslashes(file_get_contents($_FILES['letter']['tmp_name']));

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
            $sql = "SELECT * FROM halls WHERE college='$allowed_college'";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
            }
            ?>
        </select><br>
        Event Name: <input type="text" name="event_name" required><br>
        Speaker: <input type="text" name="speaker" required><br>
        Start Time: <input type="datetime-local" name="start_time" required><br>
        End Time: <input type="datetime-local" name="end_time" required><br>
        Letter: <input type="file" name="letter" accept=".pdf" required><br>
        <button type="submit">Book Hall</button>
    </form>
</body>
</html>
