<?php
include 'config.php';
session_start();

$errors = []; // Initialize an array to store errors

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
    $letter = addslashes(file_get_contents($_FILES['letter']['tmp_name']));
    $user_id = $_SESSION['user']['id'];

    // Validate if start time is not in the past
    if (isPastDate($start_time)) {
        $errors[] = "Start time cannot be in the past.";
    }

    // Validate if start time is after current time
    if (strtotime($start_time) <= time()) {
        $errors[] = "Start time should be in the future.";
    }

    // Validate if end time is after start time
    if (strtotime($end_time) <= strtotime($start_time)) {
        $errors[] = "End time should be after start time.";
    }

    // Check for overlap with existing approved bookings
    foreach ($approved_bookings as $booking) {
        if ((strtotime($start_time) >= $booking['start_time'] && strtotime($start_time) < $booking['end_time']) ||
            (strtotime($end_time) > $booking['start_time'] && strtotime($end_time) <= $booking['end_time']) ||
            (strtotime($start_time) <= $booking['start_time'] && strtotime($end_time) >= $booking['end_time'])) {
            $errors[] = "Selected time overlaps with an existing approved booking.";
            break; // Exit the loop if overlap found
        }
    }

    // If no errors, proceed with inserting into database
    if (empty($errors)) {
        $sql = "INSERT INTO bookings (hall_id, user_id, event_name, speaker, start_time, end_time, description, letter) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissssss", $hall_id, $user_id, $event_name, $speaker, $start_time, $end_time, $description, $letter);

        if ($stmt->execute()) {
            $booking_id = $stmt->insert_id; // Get the auto-generated booking ID
            echo "Booking request submitted successfully. Booking ID: $booking_id";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Hall</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .mandatory {
            color: red;
        }
        .error-message {
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
    <div class="container">
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="hall_id">Hall<span class="mandatory">*</span>:</label>
            <select name="hall_id" id="hall_id" required>
                <!-- PHP code to fetch halls options -->
                <?php
                // Fetch halls based on user's college
                $college = $_SESSION['user']['college'];
                $sql = "SELECT * FROM halls WHERE college='$college'";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    $selected = ($row['id'] == $hall_id) ? 'selected' : '';
                    echo "<option value='" . $row['id'] . "' $selected>" . $row['name'] . "</option>";
                }
                ?>
            </select><br>
            <label for="event_name">Event Name<span class="mandatory">*</span>:</label>
            <input type="text" name="event_name" id="event_name" value="<?php echo isset($event_name) ? $event_name : ''; ?>" required><br>
            <label for="speaker">Speaker Name:</label>
            <input type="text" name="speaker" id="speaker" value="<?php echo isset($speaker) ? $speaker : ''; ?>"><br>
            <label for="start_time">Start Time<span class="mandatory">*</span>:</label>
            <input type="text" name="start_time" id="start_time" value="<?php echo isset($start_time) ? $start_time : ''; ?>" required><br>
            <label for="end_time">End Time<span class="mandatory">*</span>:</label>
            <input type="text" name="end_time" id="end_time" value="<?php echo isset($end_time) ? $end_time : ''; ?>" required><br>
            <label for="description">Description<span class="mandatory">*</span>:</label>
            <textarea name="description" id="description" rows="4" required><?php echo isset($description) ? $description : ''; ?></textarea><br>
            <label for="letter">Letter<span class="mandatory">*</span>:</label>
            <input type="file" name="letter" id="letter" accept=".pdf" required><br>
            <?php if (!empty($errors)) : ?>
                <div id="error-message" class="error-message">
                    <?php foreach ($errors as $error) : ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <button type="submit">Submit Booking</button>
        </form>
        
        <a href="dashboard.php">Back to Dashboard</a>
        
        <footer class="footer">
            <p>Developed by Nischal Baidar</p>
        </footer>
    </div>

    <!-- Include Flatpickr library and initialize -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr('#start_time', {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today",
            });

            flatpickr('#end_time', {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today",
            });
        });
    </script>
</body>
</html>
