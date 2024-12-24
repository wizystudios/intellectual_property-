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

// Function to handle file uploads
function uploadFile($fileKey) {
    global $uploadDir;
    $fileName = basename($_FILES[$fileKey]['name']);
    $targetFilePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetFilePath)) {
        return $fileName;
    }
    return null;
}

// Handle file uploads
$uploadDir = 'uploads/';

// Check if the form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name_of_proprietor = isset($_POST['name_of_proprietor']) ? trim($_POST['name_of_proprietor']) : null;
    $address_of_proprietor = isset($_POST['address_of_proprietor']) ? trim($_POST['address_of_proprietor']) : null;
    $mark_name = isset($_POST['mark_name']) ? trim($_POST['mark_name']) : null;
    $international_class = isset($_POST['international_class']) ? trim($_POST['international_class']) : null;
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $conditions = isset($_POST['conditions']) ? trim($_POST['conditions']) : null;
    $exam_report_date = isset($_POST['exam_report_date']) ? trim($_POST['exam_report_date']) : null;
    $registration_date = isset($_POST['registration_date']) ? trim($_POST['registration_date']) : null;
    $country = isset($_POST['country']) ? trim($_POST['country']) : null;
    $Lname = isset($_POST['Agent_Name']) ? trim($_POST['Agent_Name']) : null;
    $Anumber = isset($_POST['Anumber']) ? trim($_POST['Anumber']) : null;
    $priority_date = isset($_POST['priority_date']) ? trim($_POST['priority_date']) : null;
    $filing_date = isset($_POST['filing_date']) ? trim($_POST['filing_date']) : null;

    $upload_logo = uploadFile('upload_logo');
    $upload_exam_report = uploadFile('upload_exam_report');
    $cert_attachment = uploadFile('cert_attachment');
    $renewal_certificate = uploadFile('renewal_certificate');

    if (empty($country) || empty($filing_date) || empty($name_of_proprietor) || 
        empty($address_of_proprietor) || empty($mark_name) || 
        empty($international_class) || empty($Lname) || empty($description)) {
        echo "Error: All required fields must be filled.";
        exit;
    }

    // Determine renewal date
    if ($priority_date) {
        $priorityDateTime = new DateTime($priority_date);
        $renewalDateTime = $priorityDateTime->modify('+7 years');
    } else {
        $filingDateTime = new DateTime($filing_date);
        $renewalDateTime = $filingDateTime->modify('+7 years');
    }
    $renewal_date = $renewalDateTime->format('Y-m-d');

    // Check if application number already exists
    $checkAnumberStmt = $conn->prepare("SELECT * FROM trademarks WHERE Anumber = ? LIMIT 1");
    $checkAnumberStmt->bind_param("s", $Anumber);
    $checkAnumberStmt->execute();
    $checkAnumberResult = $checkAnumberStmt->get_result();

    if ($checkAnumberResult->num_rows > 0) {
        echo '<div style="text-align: center; font-weight: bold; font-size: 20px; color: red; font-family: Arial, sans-serif;">';
        echo "Error: Application number already exists.";
        echo '</div>';
        echo '<div style="text-align: center; margin-top: 20px;">';
        echo '<a href="trademark_form.php" style="padding: 10px 20px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;">Go Back</a>';
        echo '</div>';
    } else {
        $stmt = $conn->prepare("INSERT INTO trademarks (country, priority_date, filing_date, name_of_proprietor, address_of_proprietor, mark_name, international_class, Agent_Name, description, conditions, publication_date, exam_report_date, registration_date, renewal_date, upload_logo, Anumber, upload_exam_report, cert_attachment, renewal_certificate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            echo "Error preparing statement: " . $conn->error;
            exit;
        }

        $stmt->bind_param("ssssssssssssssssss", $country, $priority_date, $filing_date, $name_of_proprietor, $address_of_proprietor, $mark_name, $international_class, $Lname, $description, $conditions, $exam_report_date, $registration_date, $renewal_date, $upload_logo, $Anumber, $upload_exam_report, $cert_attachment, $renewal_certificate);

        if ($stmt->execute()) {
            $alertMessage = "Application number " . $Anumber . " submitted trademark successfully. Its renewal date is " . $renewal_date . ".";
            $alertStmt = $conn->prepare("INSERT INTO alerts (mark_name, name_of_proprietor, alert_message, is_unread) VALUES (?, ?, ?, 1)");

            if (!$alertStmt) {
                echo "Error preparing alert statement: " . $conn->error;
                exit;
            }

            $alertStmt->bind_param("sss", $mark_name, $name_of_proprietor, $alertMessage);
            $alertStmt->execute();
            $alertStmt->close();

            echo '<div style="text-align: center; font-weight: bold; font-size: 20px; color: green; font-family: Arial, sans-serif;">';
            echo "New trademark was submitted successfully.";
            echo '</div>';
            echo '<div style="text-align: center; margin-top: 20px;">';
            echo '<a href="trademark_form.php" style="padding: 10px 20px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;">Go Back</a>';
            echo '</div>';
        } else {
            echo "Error: " . $stmt->error;
        }
        
        $stmt->close();
    }
}

// Display Alerts with Status Color
$alertQuery = "SELECT id, mark_name, name_of_proprietor, alert_message, is_unread FROM alerts";
$result = $conn->query($alertQuery);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $alertColor = $row['is_unread'] ? 'green' : 'red'; // Use 'green' for unread, 'red' for read
        //echo "<div style='color: $alertColor; cursor: pointer;' onclick='markAsRead(" . $row['id'] . ")'>" . htmlspecialchars($row['alert_message']) . "</div>";
    }
}

// Close connection
$conn->close();
?>

<script>
function markAsRead(alertId) {
    // Make an AJAX request to mark the alert as read
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "mark_as_read.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            location.reload(); // Reload the page to update the alert colors
        }
    };
    xhr.send("alert_id=" + alertId);
}
</script>
