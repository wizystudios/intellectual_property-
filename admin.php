<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

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

// Fetch user count
$userCountResult = $conn->query("SELECT COUNT(*) as total FROM users");
$userCount = $userCountResult->fetch_assoc()['total'];

// Fetch all users
$usersResult = $conn->query("SELECT * FROM users");

// Fetch all trademarks
$trademarksResult = $conn->query("SELECT * FROM trademarks");

// Handle adding new users
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    // Capture data
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
    $address = trim($_POST['address']);
    $phone_no = trim($_POST['phone_no']);
    $gender = trim($_POST['gender']);
    $role = trim($_POST['role']);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (full_name, username, email, password, address, phone_no, gender, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $full_name, $username, $email, $password, $address, $phone_no, $gender, $role);

    if ($stmt->execute()) {
        echo "<script>alert('User added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding user.');</script>";
    }
    $stmt->close();
}

// Handle user deletion
if (isset($_GET['delete_user'])) {
    $userId = $_GET['delete_user'];
    $conn->query("DELETE FROM users WHERE id = $userId");
    echo "<script>alert('User deleted successfully!');</script>";
}

// Handle user update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $userId = $_POST['user_id'];
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $phone_no = trim($_POST['phone_no']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
    $gender = trim($_POST['gender']);
    $role = trim($_POST['role']);

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, address = ?, phone_no = ?,password = ?, gender = ?, role = ? WHERE id = ?");
    $stmt->bind_param("ssssssssi", $full_name, $username, $email, $address, $phone_no, $password, $gender, $role, $userId);

    if ($stmt->execute()) {
        echo "<script>alert('User updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating user.');</script>";
    }
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <style>
       body {
    background:  url('law.jpg') no-repeat center center fixed;
    background-size: cover;
    margin: 0; /* Remove default body margin */
    height: 100vh; /* Set body height to viewport height */
}

.me {
    color: white;
}



.container {
    background:white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 100%; /* Full width */
    height:770px; /* Full height */
    display: flex; /* Use flexbox for alignment */
    flex-direction: column; /* Stack children vertically */
    justify-content: space-between; /* Space out children */
}

.card {
    margin-bottom: 20px;
}

.dropdown-menu {
    min-width: 300px; /* Ensures dropdown menu has a minimum width */
}

.table {
    margin-bottom: 0; /* Remove bottom margin for the table */
}

    </style>
</head>
<body>
    <div class="container text-center">
        <h1>Admin Dashboard</h1>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <h2><?php echo $userCount; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Trademarks</h5>
                        <h2><?php echo $trademarksResult->num_rows; ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
        <h2 class="mt-4">Manage Users</h2>
        <!-- Button to redirect to registration page -->
        <button class="btn btn-primary mb-3" type="button" onclick="redirectToRegisterPage('admin')">
            Add User
        </button>
        <button class="btn btn-primary mb-3" type="button" onclick="redirectTotrademark_form()">
            Go home Page
        </button>
    </div>

        <h3>All Users</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Phone No</th>
                    <th>Gender</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $usersResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['full_name']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['address']; ?></td>
                        <td><?php echo $user['phone_no']; ?></td>
                        <td><?php echo $user['gender']; ?></td>
                        <td><?php echo $user['role']; ?></td>
                        <td>
                            <a href="?delete_user=<?php echo $user['id']; ?>" class="btn btn-danger">Delete</a>
                            <button class="btn btn-warning" data-toggle="modal" data-target="#updateUserModal<?php echo $user['id']; ?>">Update</button>

                            <!-- Update User Modal -->
                            <div class="modal fade" id="updateUserModal<?php echo $user['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="updateUserModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="updateUserModalLabel">Update User</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>" required class="form-control mb-2">
                                                <input type="text" name="username" value="<?php echo $user['username']; ?>" required class="form-control mb-2">
                                                <input type="email" name="email" value="<?php echo $user['email']; ?>" required class="form-control mb-2">
                                                <input type="text" name="address" value="<?php echo $user['address']; ?>" required class="form-control mb-2">
                                                <input type="text" name="phone_no" value="<?php echo $user['phone_no']; ?>" required class="form-control mb-2">
                                                <input type="text" name="password" value="<?php echo $user['password']; ?>" required class="form-control mb-2">
                                                <select name="gender" required class="form-control mb-2">
                                                    <option value="Male" <?php echo $user['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                                                    <option value="Female" <?php echo $user['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                                                    <option value="Other" <?php echo $user['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                                                </select>
                                                <select name="role" required class="form-control mb-2">
                                                    <option value="normal user" <?php echo $user['role'] === 'normal user' ? 'selected' : ''; ?>>Normal User</option>
                                                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                </select>
                                                <button type="submit" name="update_user" class="btn btn-primary">Update User</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
          
       
        <h3 style="color: green; font-weight: bold;">All Trademarks</h3>
<div class="me" style="color: green; font-weight: bold;">   
    <table class="table">
        <thead>
            <tr style="color: green; font-weight: bold;">
                <th>A/No</th>
                <th>P.Name</th>
                <th>P.Address</th>
                <th>MarkName</th>
                <th>Class</th>
                <th>Description</th>
                <th>Condition</th>
                <th>P.Date</th>
                <th>E.R.Date</th>
                <th>R.Date</th>               
            </tr>
        </thead>
        <tbody>
            <?php while ($trademark = $trademarksResult->fetch_assoc()): ?>
                <tr style="color: Black; font-weight: bold;">
                    <td><?php echo $trademark['Anumber']; ?></td>
                    <td><?php echo $trademark['name_of_proprietor']; ?></td>
                    <td><?php echo $trademark['address_of_proprietor']; ?></td>
                    <td><?php echo $trademark['mark_name']; ?></td>
                    <td><?php echo $trademark['international_class']; ?></td>
                    <td><?php echo $trademark['description']; ?></td>
                    <td><?php echo $trademark['conditions']; ?></td>
                    <td><?php echo $trademark['publication_date']; ?></td>
                    <td><?php echo $trademark['exam_report_date']; ?></td>
                    <td><?php echo $trademark['registration_date']; ?></td>
                    <td>
                        <a href="?delete_trademark=<?php echo $trademark['id']; ?>" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<a href="login.php" class="btn btn-danger">Logout</a>
<script>
        function redirectToRegisterPage(role) {
            window.location.href = `registerpage.php?role=${role}`; // Change this to the actual URL of your registration page
        }
        function redirectTotrademark_form() {
            window.location.href = `trademark_form.php?role`; // Change this to the actual URL of your registration page
        }
    </script>



    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
