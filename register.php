<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'ADMINISTRATIVE') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];
    $college = $_POST['college'] ?? null;
    $department = $_POST['department'] ?? null;
    $photo = addslashes(file_get_contents($_FILES['photo']['tmp_name']));

    $sql = "INSERT INTO users (name, email, password, role, college, department, photo) VALUES ('$name', '$email', '$password', '$role', '$college', '$department', '$photo')";

    if ($conn->query($sql) === TRUE) {
        echo "User registered successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register User</title>
    <script>
        function showFields() {
            const role = document.getElementById('role').value;
            const collegeFields = document.getElementById('collegeFields');
            const departmentField = document.getElementById('departmentField');
            const photoField = document.getElementById('photoField');

            if (role === 'HOD') {
                collegeFields.style.display = 'block';
                departmentField.style.display = 'block';
                photoField.style.display = 'block';
            } else if (role === 'PRINCIPAL' || role === 'EXAMSECTION') {
                collegeFields.style.display = 'block';
                departmentField.style.display = 'none';
                photoField.style.display = 'block';
            } else {
                collegeFields.style.display = 'none';
                departmentField.style.display = 'none';
                photoField.style.display = 'none';
            }
        }

        function showDepartmentFields() {
            const college = document.getElementById('college').value;
            const departmentFields = document.getElementById('department');
            const khwopaCollDept = ['Department of Civil Engineering', 'Department of Computer and Electronics Engineering', 'Department of Electrical Engineering'];
            const khwopaEngDept = ['DEPARTMENT OF ELECTRONICS, COMMUNICATION & AUTOMATION ENGINEERING', 'DEPARTMENT OF COMPUTER ENGINEERING', 'DEPARTMENT OF CIVIL ENGINEERING', 'DEPARTMENT OF ARCHITECTURE'];

            departmentFields.innerHTML = '';
            if (college === 'Khwopa Engineering College') {
                khwopaEngDept.forEach(dept => {
                    const option = document.createElement('option');
                    option.value = dept;
                    option.textContent = dept;
                    departmentFields.appendChild(option);
                });
            } else if (college === 'Khwopa College of Engineering') {
                khwopaCollDept.forEach(dept => {
                    const option = document.createElement('option');
                    option.value = dept;
                    option.textContent = dept;
                    departmentFields.appendChild(option);
                });
            }
        }
    </script>
</head>
<body>
    <form method="POST" action="" enctype="multipart/form-data">
        Name: <input type="text" name="name" required><br>
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        Role: 
        <select name="role" id="role" onchange="showFields()" required>
            <option value="HOD">HOD</option>
            <option value="PRINCIPAL">PRINCIPAL</option>
            <option value="EXAMSECTION">EXAMSECTION</option>
        </select><br>
        <div id="collegeFields" style="display:none;">
            College: 
            <select name="college" id="college" onchange="showDepartmentFields()">
                <option value="Khwopa Engineering College">Khwopa Engineering College</option>
                <option value="Khwopa College of Engineering">Khwopa College of Engineering</option>
            </select><br>
        </div>
        <div id="departmentField" style="display:none;">
            Department: 
            <select name="department" id="department">
                <!-- Options will be populated based on college selection -->
            </select><br>
        </div>
        <div id="photoField" style="display:none;">
            Photo: <input type="file" name="photo" accept="image/*" required><br>
        </div>
        <button type="submit">Register</button>
    </form>
</body>
</html>
