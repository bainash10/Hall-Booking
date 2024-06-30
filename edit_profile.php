<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
    } else {
        $password = $user['password'];  // Keep the existing password if none is provided
    }
    $photo = !empty($_FILES['photo']['tmp_name']) ? addslashes(file_get_contents($_FILES['photo']['tmp_name'])) : $user['photo'];

    $sql = "UPDATE users SET name='$name', email='$email', password='$password', photo='$photo' WHERE id=" . $user['id'];

    if ($conn->query($sql) === TRUE) {
        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['photo'] = $photo;

        echo "Profile updated successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h1>Edit Profile</h1>
    <form method="POST" action="" enctype="multipart/form-data">
        Name: <input type="text" name="name" value="<?php echo $user['name']; ?>" required><br>
        Email: <input type="email" name="email" value="<?php echo $user['email']; ?>" required><br>
        Password: <input type="password" name="password" placeholder="Leave blank to keep current password"><br>
        Photo: <input type="file" name="photo" accept="image/*"><br>
        <button type="submit">Update Profile</button>
    </form>

    <a href="dashboard.php">Back to Dashboard</a>

    <footer>
        <p>Developed by Nischal Baidar</p>
    </footer>
</body>
</html>
