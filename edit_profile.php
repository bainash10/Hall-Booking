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

    // Handle photo upload
    if (!empty($_FILES['photo']['tmp_name'])) {
        // Delete existing photo file if exists
        if (!empty($user['photo']) && file_exists($user['photo'])) {
            unlink($user['photo']);
        }

        // Upload new photo
        $upload_dir = 'uploads/users_photo/';
        $photo_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo_name = $user['roll_no'] . '.' . $photo_extension;
        $photo_path = $upload_dir . $photo_name;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
            // Update photo path in database
            $update_photo_sql = "UPDATE users SET photo = ? WHERE id = ?";
            $update_photo_stmt = $conn->prepare($update_photo_sql);
            $update_photo_stmt->bind_param("si", $photo_path, $user_id);
            if ($update_photo_stmt->execute()) {
                // Update $user['photo'] to reflect new path
                $user['photo'] = $photo_path;
            } else {
                echo "Failed to update photo path in database.";
            }
            $update_photo_stmt->close();
        } else {
            echo "Failed to upload photo.";
        }
    }

    // Update user details
    $update_sql = "UPDATE users SET name=?, email=?, password=? WHERE id=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $name, $email, $password, $user_id);

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
        <?php if (!empty($user['photo'])): ?>
            <p>Current Photo:</p>
            <img src="<?php echo htmlspecialchars($user['photo']); ?>" alt="Current Photo" style="max-width: 200px; height: auto;">
            <br><br>
        <?php endif; ?>
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
