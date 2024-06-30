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
    <!-- <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .action-buttons {
            display: flex;
            justify-content: space-between;
        }
        .action-buttons a {
            margin-right: 10px;
        }
    </style> -->
</head>
<body>
    <h1>Registered Users</h1>

    <nav>
        <ul>
            <li><a href="dashboard.php">Back to Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

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
                                <a href="delete_user.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
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
</body>
</html>

<?php
$conn->close(); // Close the database connection
?>
