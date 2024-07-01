<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

// Fetch user data including photo path and roll number from database
$sql = "SELECT name, email, role, college, department, user_photo, roll_no FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$stmt->bind_result($name, $email, $role, $college, $department, $photo_path, $roll_no);

if ($stmt->fetch()) {
    // User data fetched successfully
} else {
    echo "Error fetching user data.";
    exit();
}
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Profile</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color:#FFFFFF;
            margin: 0;
            padding: 0;
        }
        .profile-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f0f0f0;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .profile-container h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .profile-photo {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-photo img {
            display: block;
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            box-shadow: 0 0 8px rgba(0,0,0,0.2);
        }
        .profile-details {
            margin-top: 20px;
            font-size: 16px;
            line-height: 1.6;
        }
        .profile-details p {
            margin-bottom: 10px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #333;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        footer {
            text-align: center;
            margin-top: 20px;
            color: #888;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h1>Profile of <?php echo htmlspecialchars($name); ?></h1>
        <div class="profile-photo">
            <?php if ($photo_path): ?>
                <img src="<?php echo htmlspecialchars($photo_path); ?>" alt="Profile Photo">
            <?php else: ?>
                <p>No photo available</p>
            <?php endif; ?>
        </div>
        <div class="profile-details">
            <p><strong>Roll Number:</strong> <?php echo htmlspecialchars($roll_no); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($role); ?></p>
            <p><strong>College:</strong> <?php echo htmlspecialchars($college); ?></p>
            <p><strong>Department:</strong> <?php echo htmlspecialchars($department); ?></p>
        </div>

        <a href="dashboard.php" class="back-link">Back to Dashboard</a>
    </div>

    <footer>
        <p style="color:#FFFFFF">Developed by Nischal Baidar</p>
    </footer>
</body>
</html>
