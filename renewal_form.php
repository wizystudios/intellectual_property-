<?php
// Database configuration
$host = 'sql207.infinityfree.com';
$dbname = 'if0_37560109_698';
$username = 'if0_37560109';
$password = '5112kharifa';
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$application_exists = false;
$renewal_date = '';
$stage = '';
$error_message = '';
$success_message = '';
$show_modal = false;
$renewals = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_number = isset($_POST['application_number']) ? trim($_POST['application_number']) : '';
    
    // Search for the application number in the database
    $stmt = $conn->prepare("SELECT renewal_date, renewal_certificate FROM trademarks WHERE Anumber = ? LIMIT 1");
    $stmt->bind_param("s", $application_number);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_renewal_date, $db_renewal_certificate);
        $stmt->fetch();
        $application_exists = true;

        if (isset($_POST['renewal_stage'])) {
            $stage = intval($_POST['renewal_stage']);
            
        // Check if the selected stage already exists for this application number
$check_stage_stmt = $conn->prepare("SELECT stage FROM renewals WHERE Anumber = ? AND stage = ? LIMIT 1");
$check_stage_stmt->bind_param("si", $application_number, $stage);
$check_stage_stmt->execute();
$check_stage_stmt->store_result();

if ($check_stage_stmt->num_rows > 0) {
    $error_message = "Stage $stage has already been registered for this application number.";
    $show_modal = true;
} else {
    // For Stage 1, directly use the renewal date from the trademarks table
    if ($stage == 1) {
        $renewal_date = $db_renewal_date; // Use the renewal date from the trademarks table
    } else {
        // Check if the previous stage has been registered
        $previous_stage = $stage - 1;
        $check_previous_stage_stmt = $conn->prepare("SELECT calculated_renewal_date FROM renewals WHERE Anumber = ? AND stage = ? LIMIT 1");
        $check_previous_stage_stmt->bind_param("si", $application_number, $previous_stage);
        $check_previous_stage_stmt->execute();
        $check_previous_stage_stmt->store_result();

        if ($check_previous_stage_stmt->num_rows == 0) {
            $error_message = "You must register stage $previous_stage first before registering stage $stage.";
            $show_modal = true;
        } else {
            // Fetch the calculated renewal date of the previous stage
            $check_previous_stage_stmt->bind_result($prev_renewal_date);
            $check_previous_stage_stmt->fetch();

            // Calculate renewal date based on the previous stage's calculated renewal date
            $renewalDateTime = new DateTime($prev_renewal_date);
            $renewalDateTime->modify("+10 years"); // Add 10 years to the previous stage's renewal date
            $renewal_date = $renewalDateTime->format('Y-m-d');
        }

        // Close the previous stage check statement
        $check_previous_stage_stmt->close();
    }

    // Handle file upload for renewal certificate
    if (isset($_FILES['renewal_certificate']) && $_FILES['renewal_certificate']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $upload_file = $upload_dir . basename($_FILES['renewal_certificate']['name']);
        
        // Move the uploaded file to the desired directory
        if (move_uploaded_file($_FILES['renewal_certificate']['tmp_name'], $upload_file)) {
            $renewal_certificate = $upload_file;

            // Insert the calculated renewal date into the renewals table with status 'Active'
            if (!empty($renewal_date)) {
                $insert_stmt = $conn->prepare("INSERT INTO renewals (Anumber, stage, calculated_renewal_date, renewal_certificate, created_at, status) VALUES (?, ?, ?, ?, NOW(), 'Active')");
                $insert_stmt->bind_param("siss", $application_number, $stage, $renewal_date, $renewal_certificate);
                $insert_stmt->execute();
                $insert_stmt->close();

                // Update the renewal date and renewal certificate in the trademarks table
                $update_stmt = $conn->prepare("UPDATE trademarks SET renewal_date = ?, renewal_certificate = ? WHERE Anumber = ?");
                $update_stmt->bind_param("sss", $renewal_date, $renewal_certificate, $application_number);
                $update_stmt->execute();
                $update_stmt->close();

                // Delete the previous renewal certificate file if it exists
                if (!empty($db_renewal_certificate) && file_exists($db_renewal_certificate)) {
                    unlink($db_renewal_certificate);
                }

                $success_message = "Stage $stage registered successfully with renewal date: $renewal_date.";
                $show_modal = true; // Show success modal
            }
        } else {
            $error_message = "Failed to upload the renewal certificate.";
            $show_modal = true; // Show error modal
        }
    } else {
        $error_message = "Please upload a valid renewal certificate.";
        $show_modal = true; // Show error modal
    }
}

// Close the stage check statement
$check_stage_stmt->close();

        }

        // Retrieve all renewals for the application number
        if (isset($_POST['retrieve_renewals'])) {
            $retrieve_stmt = $conn->prepare("SELECT stage, calculated_renewal_date, renewal_certificate, created_at, status FROM renewals WHERE Anumber = ?");
            $retrieve_stmt->bind_param("s", $application_number);
            $retrieve_stmt->execute();
            $result = $retrieve_stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $renewals[] = $row;
            }
            $retrieve_stmt->close();
        }
    } else {
        $error_message = 'Application number not found.';
        $show_modal = true; // Show error modal
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renewal Form</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            padding: 20px;
        }
 body {
            background: url('law.png') no-repeat center center fixed;
            background-size: cover;
            height: 100vh; /* Ensure the body takes the full viewport height */
            display: flex; /* Use flexbox for centering */
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            margin: 0; /* Remove default margin */
        }

        .container {
            max-width: 500px; /* Set a max width for the form */
            background: rgba(255, 255, 255, 0.8); /* Optional: add a semi-transparent background */
            padding: 20px; /* Padding around the form */
            border-radius: 10px; /* Optional: rounded corners */
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5); /* Optional: shadow effect */
        }

        .error {
            color: red;
        }

        .tick-mark {
            color: green;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2 class="card-title">Renewal Form</h2>
            <form method="post" action="renewal_form.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="application_number">Application Number</label>
                    <input type="text" class="form-control" id="application_number" name="application_number" required>
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
                <button type="submit" name="retrieve_renewals" class="btn btn-info">Retrieve Renewals</button>
                <a href="trademark_form.php" class="btn btn-secondary">Back</a> <!-- Back button -->
            </form>

            <?php if ($application_exists): ?>
                <hr>
                <form method="post" action="renewal_form.php" enctype="multipart/form-data">
                    <input type="hidden" name="application_number" value="<?php echo htmlspecialchars($application_number); ?>">
                    <div class="form-group">
                        <label for="renewal_stage">Choose Renewal Stage (1-10 years)</label>
                        <select class="form-control" id="renewal_stage" name="renewal_stage" required>
                            <?php for ($i = 1; $i <= 100; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo ($stage == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="renewal_certificate">Upload Renewal Certificate</label>
                        <input type="file" class="form-control" id="renewal_certificate" name="renewal_certificate" required>
                    </div>
                    <button type="submit" class="btn btn-success">Renewal trademark</button>
                </form>

                <?php if (!empty($renewals)): ?>
                    <h3>Renewals History</h3>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Stage</th>
                                <th>Calculated Renewal Date</th>
                                <th>Created At</th>
                                <th>Status</th>
                                <th>Certificate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($renewals as $renewal): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($renewal['stage']); ?></td>
                                    <td><?php echo htmlspecialchars($renewal['calculated_renewal_date']); ?></td>
                                    <td><?php echo htmlspecialchars($renewal['created_at']); ?></td>
                                    <td>
                                        <?php if ($renewal['status'] === 'Active'): ?>
                                            <span class="tick-mark">&#10003; Active</span>
                                        <?php else: ?>
                                            Inactive
                                        <?php endif; ?>
                                    </td>
                                    <td><a href="<?php echo htmlspecialchars($renewal['renewal_certificate']); ?>" target="_blank">View Certificate</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal -->
    <?php if ($show_modal): ?>
    <div class="modal fade" id="responseModal" tabindex="-1" role="dialog" aria-labelledby="responseModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="responseModalLabel"><?php echo empty($error_message) ? 'Success' : 'Error'; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php
                    if (!empty($success_message)) {
                        echo '<p class="text-success">' . $success_message . '</p>';
                    }
                    if (!empty($error_message)) {
                        echo '<p class="text-danger">' . $error_message . '</p>';
                    }
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    <?php if ($show_modal): ?>
        $(document).ready(function() {
            $('#responseModal').modal('show');
        });
    <?php endif; ?>
    </script>
</body>
</html>
