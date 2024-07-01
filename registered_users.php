<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

// Function to fetch all registered users excluding the admin
function getAllUsers($conn) {
    $user_id = $_SESSION['user']['id']; // Admin user ID
    $sql = "SELECT * FROM users WHERE id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $stmt->close();
    return $users;
}

// Fetch all users if the user role is ADMINISTRATIVE or PRINCIPAL
$registered_users = [];
if (in_array($user['role'], ['ADMINISTRATIVE', 'PRINCIPAL'])) {
    $registered_users = getAllUsers($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registered Users</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h1>Registered Users</h1>

    <nav>
        <ul>
            <li><a href="dashboard.php">Back to Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <?php if (isset($_SESSION['message'])): ?>
        <p><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
    <?php endif; ?>

    <section>
        <?php if (!empty($registered_users)) : ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>College</th>
                        <th>Department</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registered_users as $user) : ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo $user['name']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['role']; ?></td>
                            <td><?php echo $user['college']; ?></td>
                            <td><?php echo $user['department']; ?></td>
                            <td class="action-buttons">
                                <a href="edit_profile.php?id=<?php echo $user['id']; ?>">Edit</a>
                                <a href="#" onclick="confirmDelete(<?php echo $user['id']; ?>)">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No registered users found.</p>
        <?php endif; ?>
    </section>

    <footer>
        <p>Developed by Nischal Baidar</p>
    </footer>

    <script>
    function confirmDelete(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
            // Create a form and submit it
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'delete_user.php';

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'user_id';
            input.value = userId;

            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>
</body>
</html>

<?php
$conn->close(); // Close the database connection
?>
