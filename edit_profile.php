<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_GET['id'] ?? null;

// Fetch user data
$user = null;
if ($user_id) {
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
    } else {
        $password = isset($user['password']) ? $user['password'] : '';  // Keep the existing password if none is provided
    }
    $photo = !empty($_FILES['photo']['tmp_name']) ? addslashes(file_get_contents($_FILES['photo']['tmp_name'])) : (isset($user['photo']) ? $user['photo'] : '');

    $update_sql = "UPDATE users SET name=?, email=?, password=?, photo=? WHERE id=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $name, $email, $password, $photo, $user_id);

    if ($update_stmt->execute()) {
        echo "User updated successfully";
        // Refresh user data after update
        $user['name'] = $name;
        $user['email'] = $email;
        // Update other user data as needed
    } else {
        echo "Error updating user: " . $update_stmt->error;
    }
    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h1>Edit User</h1>
    <form method="POST" action="" enctype="multipart/form-data">
        Name: <input type="text" name="name" value="<?php echo isset($user['name']) ? $user['name'] : ''; ?>" required><br>
        Email: <input type="email" name="email" value="<?php echo isset($user['email']) ? $user['email'] : ''; ?>" required><br>
        Password: <input type="password" name="password" placeholder="Leave blank to keep current password"><br>
        Photo: <input type="file" name="photo" accept="image/*"><br>
        <button type="submit">Update User</button>
    </form>

    <a href="dashboard.php">Back to Dashboard</a>

    <footer>
        <p>Developed by Nischal Baidar</p>
    </footer>
</body>
</html>

<?php
$conn->close(); // Close the database connection
?>
