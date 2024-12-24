<?php
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

// Check if the Anumber is provided
if (isset($_GET['Anumber'])) {
    $anumber = $_GET['Anumber'];

    // Prepare the SQL statement to fetch the trademark details
    $stmt = $conn->prepare("SELECT * FROM trademarks WHERE Anumber = ?");
    $stmt->bind_param("s", $anumber);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the trademark details
    $trademark = $result->fetch_assoc();
    
    if (!$trademark) {
        die("Trademark not found.");
    }
} else {
    die("No trademark selected.");
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and get the form input
    $mark_name = $_POST['mark_name'];
    $name_of_proprietor = $_POST['name_of_proprietor'];
    $country = $_POST['country'];
    $priority_date = $_POST['priority_date'];
    $filing_date = $_POST['filing_date'];
    $address_of_proprietor = $_POST['address_of_proprietor'];
    $international_class = $_POST['international_class'];
    $agent_name = $_POST['agent_name'];
    $description = $_POST['description'];
    $conditions = $_POST['conditions'];
    $exam_report_date = $_POST['exam_report_date'];
    $registration_date = $_POST['registration_date'];
    $renewal_date = $_POST['renewal_date'];

    // Prepare the SQL statement to update the trademark
    $update_stmt = $conn->prepare("UPDATE trademarks SET mark_name=?, name_of_proprietor=?, country=?, priority_date=?, filing_date=?, address_of_proprietor=?, international_class=?, Agent_Name=?, description=?, conditions=?, exam_report_date=?, registration_date=?, renewal_date=? WHERE Anumber=?");
    $update_stmt->bind_param("ssssssssssssss", $mark_name, $name_of_proprietor, $country, $priority_date, $filing_date, $address_of_proprietor, $international_class, $agent_name, $description, $conditions, $exam_report_date, $registration_date, $renewal_date, $anumber);

    if ($update_stmt->execute()) {
        echo "<div class='alert alert-success'>Trademark updated successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error updating trademark: " . $conn->error . "</div>";
    }

    $update_stmt->close();
}

// Close the statement
$stmt->close();

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Trademark</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: url('law.png') no-repeat center center fixed;
            background-size: cover;
            color: white;
        }
        .container {
            max-width: 600px;
            background: rgba(0, 0, 0, 0.8);
            padding: 30px;
            border-radius: 10px;
            margin-top: 100px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        input, textarea {
            margin-bottom: 15px;
        }
        button {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Edit Trademark</h2>
        <form method="POST" action="">
            <input type="text" class="form-control" name="mark_name" value="<?php echo htmlspecialchars($trademark['mark_name']); ?>" placeholder="Mark Name" required>
            <input type="text" class="form-control" name="name_of_proprietor" value="<?php echo htmlspecialchars($trademark['name_of_proprietor']); ?>" placeholder="Name of Proprietor" required>
            <input type="text" class="form-control" name="country" value="<?php echo htmlspecialchars($trademark['country']); ?>" placeholder="Country" required>
            <input type="date" class="form-control" name="priority_date" value="<?php echo htmlspecialchars($trademark['priority_date']); ?>" required>
            <input type="date" class="form-control" name="filing_date" value="<?php echo htmlspecialchars($trademark['filing_date']); ?>" required>
            <textarea class="form-control" name="address_of_proprietor" placeholder="Address of Proprietor" required><?php echo htmlspecialchars($trademark['address_of_proprietor']); ?></textarea>
            
            <!-- International Class Selection -->
            <select class="form-control" name="international_class" required>
                <option value="" disabled selected>Select International Class (1-43)</option>
                <?php for ($i = 1; $i <= 43; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($trademark['international_class'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>

            <input type="text" class="form-control" name="agent_name" value="<?php echo htmlspecialchars($trademark['Agent_Name']); ?>" placeholder="Representative" required>
            <textarea class="form-control" name="description" placeholder="Description" required><?php echo htmlspecialchars($trademark['description']); ?></textarea>
            
            <!-- Condition Selection -->
            <div class="form-group">
                <label>Conditions:</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="conditions" value="Yes" <?php echo ($trademark['conditions'] == 'Yes') ? 'checked' : ''; ?> required>
                    <label class="form-check-label">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="conditions" value="No" <?php echo ($trademark['conditions'] == 'No') ? 'checked' : ''; ?> required>
                    <label class="form-check-label">No</label>
                </div>
            </div>

            <input type="date" class="form-control" name="exam_report_date" value="<?php echo htmlspecialchars($trademark['exam_report_date']); ?>" required>
            <input type="date" class="form-control" name="registration_date" value="<?php echo htmlspecialchars($trademark['registration_date']); ?>" required>
            <input type="date" class="form-control" name="renewal_date" value="<?php echo htmlspecialchars($trademark['renewal_date']); ?>" required>
            <button type="submit" class="btn btn-primary btn-block">Update Trademark</button>
            <button class="btn btn-secondary btn-block" type="button" onclick="redirectTotrademark_form()">Go Home Page</button>
        </form>
    </div>

    <script>
        function redirectTotrademark_form() {
            window.location.href = `trademark_form.php?role`; // Change this to the actual URL of your registration page
        }
    </script>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
