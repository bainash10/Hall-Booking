<?php
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
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo $user['name']; ?></h1>
    <p>Role: <?php echo $user['role']; ?></p>
    <a href="book_hall.php">Book Hall</a><br>
    <?php if ($user['role'] == 'ADMINISTRATIVE') { ?>
        <a href="register.php">Register User</a><br>
        <a href="approve_request.php">Approve Requests</a>
    <?php } ?>
    <a href="logout.php">Logout</a>
</body>
</html>
