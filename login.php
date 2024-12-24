<?php
session_start(); // Start the session

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
    $email = trim($_POST['useremail']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        echo "<script>alert('Error: All fields are required.');</script>";
    } else {
        // Fetch user from database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Start session and store user data
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role']; // Store role in session
                $_SESSION['loggedin'] = true; // Indicate the user is logged in

                // Redirect based on user role
                if ($user['role'] === 'admin') {
                    header("Location: admin.php"); // Redirect to admin page
                } else {
                    header("Location: trademark_form.php"); // Redirect to trademark form page
                }
                exit; // Stop further execution
            } else {
                echo "<script>alert('Error: Invalid credentials.');</script>";
            }
        } else {
            echo "<script>alert('Error: User not found.');</script>";
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
    <!------<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">--->
    <title>Login</title>
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
        .login-container {
            max-width: 400px;
            width: 100%;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .login-container img {
            width: 150px;
            height: 75px;
            margin-bottom: 20px;
        }
        .btn-login {
            background-color: #007bff;
            border: none;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        .btn-login:hover {
            background-color: #0056b3;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: none;
        }
        .btn-register {
            margin-top: 15px;
            width: 100%;
            border: 1px solid #007bff;
            background-color: white;
            color: #007bff;
        }
        .btn-register:hover {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <img src="logo.png" alt="Logo"> <!-- Replace 'logo.png' with the path to your logo -->
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="useremail">User Email</label>
                <input type="text" id="useremail" name="useremail" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-login">Login</button>
        </form>
        <a href="registerpage.php" class="btn btn-register">Register</a> <!-- Button to navigate to registration page -->
    </div>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

