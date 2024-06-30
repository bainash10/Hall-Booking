<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Profile</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h1>Profile of <?php echo $user['name']; ?></h1>
    <p>Email: <?php echo $user['email']; ?></p>
    <p>Role: <?php echo $user['role']; ?></p>
    <p>College: <?php echo $user['college']; ?></p>
    <p>Department: <?php echo $user['department']; ?></p>
    <p>Photo:</p>
    <img src="data:image/jpeg;base64,<?php echo base64_encode($user['photo']); ?>" alt="Profile Photo" style="max-width: 200px; height: auto;"/>

    <a href="dashboard.php">Back to Dashboard</a>

    <footer>
        <p>Developed by Nischal Baidar</p>
    </footer>
</body>
</html>
