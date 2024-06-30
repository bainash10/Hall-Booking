<?php
include 'config.php'; // Ensure your database connection details are in 'config.php'
session_start();

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] != 'HOD' && $_SESSION['user']['role'] != 'EXAMSECTION')) {
    header("Location: login.php");
    exit();
}

// Function to check if a date is in the past
function isPastDate($date) {
    return (strtotime($date) < strtotime('today'));
}

// Fetch all approved bookings to check for available times
$college = $_SESSION['user']['college'];
$sql = "SELECT start_time, end_time FROM bookings b
        JOIN halls h ON b.hall_id = h.id
        WHERE h.college = '$college' AND b.status = 'APPROVED'";
$result = $conn->query($sql);

$approved_bookings = [];
while ($row = $result->fetch_assoc()) {
    $approved_bookings[] = [
        'start_time' => strtotime($row['start_time']),
        'end_time' => strtotime($row['end_time'])
    ];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hall_id = $_POST['hall_id'];
    $event_name = $_POST['event_name'];
    $speaker = isset($_POST['speaker']) ? $_POST['speaker'] : '';
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $description = $_POST['description'];
    
    // File upload handling
    $letter_filename = $_FILES['letter']['name'];
    $letter_tmpname = $_FILES['letter']['tmp_name'];
    $letter_size = $_FILES['letter']['size'];
    $letter_type = $_FILES['letter']['type'];

    // Validate if the uploaded file is a PDF
    $allowed_types = ['application/pdf'];
    if (!in_array($letter_type, $allowed_types)) {
        echo "Error: Only PDF files are allowed.";
        exit();
    }

    // Validate if start time is not in the past
    if (isPastDate($start_time)) {
        echo "Error: Start time cannot be in the past.";
        exit();
    }

    // Validate if start time is after current time
    if (strtotime($start_time) <= time()) {
        echo "Error: Start time should be in the future.";
        exit();
    }

    // Validate if end time is after start time
    if (strtotime($end_time) <= strtotime($start_time)) {
        echo "Error: End time should be after start time.";
        exit();
    }

    // Check for overlap with existing approved bookings
    foreach ($approved_bookings as $booking) {
        if ((strtotime($start_time) >= $booking['start_time'] && strtotime($start_time) < $booking['end_time']) ||
            (strtotime($end_time) > $booking['start_time'] && strtotime($end_time) <= $booking['end_time']) ||
            (strtotime($start_time) <= $booking['start_time'] && strtotime($end_time) >= $booking['end_time'])) {
            echo "Error: Selected time overlaps with an existing approved booking.";
            exit();
        }
    }

    // Handle file upload and store in database
    $letter_content = file_get_contents($letter_tmpname);
    $letter_content = mysqli_real_escape_string($conn, $letter_content);

    $sql = "INSERT INTO bookings (hall_id, user_id, event_name, speaker, start_time, end_time, description, letter, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'PENDING')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissssss", $hall_id, $_SESSION['user']['id'], $event_name, $speaker, $start_time, $end_time, $description, $letter_content);

    if ($stmt->execute()) {
        $booking_id = $stmt->insert_id; // Get the auto-generated booking ID
        echo "Booking request submitted successfully. Booking ID: $booking_id";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Hall</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <style>
        .mandatory {
            color: red;
        }
        .past-time {
            color: red;
        }
        .available-time {
            color: blue;
        }
    </style>
</head>
<body>
    <form method="POST" action="" enctype="multipart/form-data">
        <label for="hall_id">Hall<span class="mandatory">*</span>:</label>
        <select name="hall_id" id="hall_id" required>
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
        <label for="event_name">Event Name<span class="mandatory">*</span>:</label>
        <input type="text" name="event_name" id="event_name" required><br>
        <label for="speaker">Speaker:</label>
        <input type="text" name="speaker" id="speaker"><br>
        <label for="start_time">Start Time<span class="mandatory">*</span>:</label>
        <input type="datetime-local" name="start_time" id="start_time" required><br>
        <label for="end_time">End Time<span class="mandatory">*</span>:</label>
        <input type="datetime-local" name="end_time" id="end_time" required><br>
        <label for="description">Description<span class="mandatory">*</span>:</label>
        <textarea name="description" id="description" rows="4" required></textarea><br>
        <label for="letter">Letter<span class="mandatory">*</span> (PDF only):</label>
        <input type="file" name="letter" id="letter" accept=".pdf" required><br>
        <button type="submit">Submit Booking</button>
    </form>
    <a href="dashboard.php">Back to Dashboard</a>
    <footer>
        <p>Developed by Nischal Baidar</p>
    </footer>
</body>
</html>
