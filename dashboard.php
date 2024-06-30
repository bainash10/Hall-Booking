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
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h1>Welcome, <?php echo $user['name']; ?></h1>
    <p>Role: <?php echo $user['role']; ?></p>

    <nav>
        <ul>
            <?php if (in_array($user['role'], ['HOD', 'EXAMSECTION'])) { ?>
                <li><a href="book_hall.php">Book Hall</a></li>
            <?php } ?>
            <?php if (in_array($user['role'], ['ADMINISTRATIVE', 'PRINCIPAL'])) { ?>
                <li><a href="approve_request.php">View Requests</a></li>
            <?php } ?>
            <?php if ($user['role'] == 'ADMINISTRATIVE') { ?>
                <li><a href="register.php">Register User</a></li>
            <?php } ?>
            <li><a href="view_profile.php">View Profile</a></li>
            <li><a href="edit_profile.php">Edit Profile</a></li>
            <?php if (in_array($user['role'], ['HOD', 'EXAMSECTION'])) { ?>
            <li><a href="view_bookings.php">My Bookings</a></li>
            <?php } ?>
            
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <footer>
        <p>Developed by Nischal Baidar</p>
    </footer>
</body>
</html>
