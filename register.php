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
    $college_id = $_POST['college_id'] ?? null; // New field for College ID
    $department = null; // Initialize department to null

    // Set department only if role is 'HOD'
    if ($role === 'HOD') {
        $department = $_POST['department'] ?? null;
    }
    
    // Check if email already exists
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows > 0) {
        echo "Error: Email already exists.";
        $check_stmt->close();
        exit(); // Stop further execution
    }
    $check_stmt->close();

    // Check if a file was uploaded
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        // Get file extension
        $photo_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        // Generate filename based on college ID
        $photo_filename = $college_id . "." . $photo_extension;
        // Upload directory
        $upload_directory = "uploads/users_photo/";
        // Path to store in database
        $photo_path = $upload_directory . $photo_filename;
        // Move uploaded file to directory
        move_uploaded_file($_FILES['photo']['tmp_name'], $upload_directory . $photo_filename);
    } else {
        $photo_path = null; // or handle this case according to your requirements
    }

    // Insert new user
    $sql = "INSERT INTO users (name, email, password, role, college, department, user_photo, roll_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $name, $email, $password, $role, $college, $department, $photo_path, $college_id);

    if ($stmt->execute()) {
        echo "User registered successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register User</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <style>
        .required::after {
            content: '*';
            color: red;
        }
    </style>
    <script>
        // Function to initialize form fields based on selected role
        function initializeForm() {
            const role = document.getElementById('role').value;
            const collegeFields = document.getElementById('collegeFields');
            const departmentField = document.getElementById('departmentField');
            const photoField = document.getElementById('photoField');
            const collegeIdField = document.getElementById('collegeIdField'); // New field for College ID

            if (role === 'HOD') {
                collegeFields.style.display = 'block';
                departmentField.style.display = 'block';
                photoField.style.display = 'block';
                collegeIdField.style.display = 'block'; // Show College ID field
            } else if (role === 'PRINCIPAL' || role === 'EXAMSECTION') {
                collegeFields.style.display = 'block';
                departmentField.style.display = 'none';
                photoField.style.display = 'block';
                collegeIdField.style.display = 'none'; // Hide College ID field for Principal and Exam Section
            } else {
                collegeFields.style.display = 'none';
                departmentField.style.display = 'none';
                photoField.style.display = 'none';
                collegeIdField.style.display = 'none'; // Hide College ID field for other roles
            }

            // Call the function to show departments based on the initially selected college
            showDepartmentFields();
        }

        // Function to populate department options based on selected college
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

        // Initialize form on page load
        window.onload = function() {
            initializeForm();
        };
    </script>
</head>
<body>
    <form method="POST" action="" enctype="multipart/form-data">
        <div id="collegeIdField" style="display:none;">
            College ID<span class="required">*</span>: 
            <input type="text" name="college_id" required pattern="[0-9]*" inputmode="numeric" onkeypress="return event.charCode >= 48 && event.charCode <= 57"><br>
        </div>

        Name<span class="required">*</span>: <input type="text" name="name" required><br>
        Email<span class="required">*</span>: <input type="email" name="email" required><br>
        Password<span class="required">*</span>: <input type="password" name="password" required><br>
        Role<span class="required">*</span>: 
        <select name="role" id="role" onchange="initializeForm()" required>
            <option value="HOD">HOD</option>
            <option value="PRINCIPAL">PRINCIPAL</option>
            <option value="EXAMSECTION">EXAMSECTION</option>
        </select><br>
        <div id="collegeFields" style="display:none;">
            College<span class="required">*</span>: 
            <select name="college" id="college" onchange="showDepartmentFields()">
                <option value="Khwopa Engineering College">Khwopa Engineering College</option>
                <option value="Khwopa College of Engineering">Khwopa College of Engineering</option>
            </select><br>
        </div>
        <div id="departmentField" style="display:none;">
            Department<span class="required">*</span>: 
            <select name="department" id="department">
                <!-- Options will be populated based on college selection -->
            </select><br>
        </div>
       
        <div id="photoField" style="display:none;">
            Photo: <input type="file" name="photo" accept="image/*"><br>
        </div>
        <button type="submit">Register</button>
    </form>
    <a href="dashboard.php">Back to Dashboard</a>
    <footer>
        <p>Developed by Nischal Baidar</p>
    </footer>
</body>
</html>
