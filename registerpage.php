<?php
session_start();

// Database configuration
$host = 'sql207.infinityfree.com';
$dbname = 'if0_37560109_698';
$username = 'if0_37560109';
$password = '5112kharifa';

// Create a connection to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $hashed_password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
    $address = trim($_POST['address']);
    $phone_no = trim($_POST['phone_no']);
    $gender = trim($_POST['gender']);
    // Default role for normal users
    $role = isset($_POST['role']) ? trim($_POST['role']) : 'normal user';

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Error: Username or email already exists.');</script>";
    } else {
        // Insert new user into the database
        $stmt = $conn->prepare("INSERT INTO users (full_name, username, email, password, address, phone_no, gender, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $full_name, $username, $email, $hashed_password, $address, $phone_no, $gender, $role);
        
        if ($stmt->execute()) {
            echo "<script>alert('Registration successful! You can now log in.'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Error: Could not register user.');</script>";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('law.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .registration-container {
            max-width: 800px; /* Increased width */
            width: 90%; /* Responsive width */
            background: rgba(255, 255, 255, 0.9);
            padding: 40px; /* Increased padding */
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            display: flex;
            flex-direction: column; /* Arrange elements in a column */
            gap: 15px; /* Space between elements */
        }
        .registration-container img {
            width: 150px;
            height: 75px;
            margin-bottom: 20px;
        }
        .btn-register {
            background-color: #007bff;
            border: none;
            color: white;
            font-size: 18px; /* Increased font size */
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        .btn-register:hover {
            background-color: #0056b3;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: none;
        }
    </style>
</head>
<body>

    <div class="registration-container">
        <img src="logo.png" alt="Logo"> <!-- Replace 'logo.png' with the path to your logo -->
        <form action="registerpage.php" method="POST">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="phone_no">Phone Number</label>
                <input type="text" id="phone_no" name="phone_no" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" class="form-control" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <!-- Role field appears only for admin -->
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="normal user">Normal User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            <?php endif; ?>
            <button type="submit" class="btn btn-register">Register</button>
        </form>
        <!-- Back Button -->
        <button class="btn btn-secondary mt-3" onclick="goBack()">Back</button>
    </div>

    <!-- Back button JavaScript function -->
    <script>
        function goBack() {
            window.history.back(); // Go back to the previous page
        }
    </script>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
