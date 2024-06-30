<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
        $sql = "UPDATE users SET name='$name', password='$password' WHERE id='$user_id'";
    } else {
        $sql = "UPDATE users SET name='$name' WHERE id='$user_id'";
    }

    if (isset($_FILES['photo']) && $_FILES['photo']['size'] > 0) {
        $photo = addslashes(file_get_contents($_FILES['photo']['tmp_name']));
        $sql = "UPDATE users SET name='$name', photo='$photo' WHERE id='$user_id'";
    }

    if ($conn->query($sql) === TRUE) {
        echo "Profile updated successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <form method="POST" action="" enctype="multipart/form-data">
        Name: <input type="text" name="name" value="<?php echo $user['name']; ?>" required><br>
        Password: <input type="password" name="password" placeholder="Leave blank to keep current password"><br>
        Photo: <input type="file" name="photo" accept="image/*"><br>
        <img src="data:image/jpeg;base64,<?php echo base64_encode($user['photo']); ?>" alt="Profile Photo" width="100" height="100"><br>
        <button type="submit">Update Profile</button>
    </form>
    <a href="dashboard.php">Back to Dashboard</a>
    <footer>
        <p>Developed by Nischal Baidar</p>
    </footer>
</body>
</html>
