<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] != 'HOD' && $_SESSION['user']['role'] != 'EXAMSECTION')) {
    header("Location: login.php");
    exit();
}

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
        $booking_id = $conn->insert_id; // Get the auto-generated booking ID
        echo "Booking request submitted successfully. Booking ID: $booking_id";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Hall</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <form method="POST" action="" enctype="multipart/form-data">
        Hall:
        <select name="hall_id" required>
            <?php
            // Select halls based on the user's college
            $college = $_SESSION['user']['college'];
            $sql = "SELECT * FROM halls WHERE college='$college'";
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
        Letter: <input type="file" name="letter" accept=".pdf,.doc,.docx" required><br>
        <button type="submit">Submit Booking</button>
    </form>
    <a href="dashboard.php">Back to Dashboard</a>
    <footer>
        <p>Developed by Nischal Baidar</p>
    </footer>
</body>
</html>
