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

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the search term and sanitize input
    $search_term = isset($_POST['search_term']) ? trim($_POST['search_term']) : '';

    // Prepare the SQL statement with a wildcard search
    $stmt = $conn->prepare("SELECT * FROM trademarks WHERE mark_name LIKE ? OR Anumber LIKE ?");
    $like_search_term = '%' . $search_term . '%';
    $stmt->bind_param("ss", $like_search_term, $like_search_term);
    
    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are results
    if ($result->num_rows > 0) {
        echo '<div style="max-width: 1200px; margin: auto; padding-top: 50px;">'; // Center the container
        echo '<h2 style="text-align: center;">Search Results</h2>'; // Centered title
        echo '<div style="display: flex; flex-wrap: wrap; justify-content: center;">'; // Flexbox for centering cards

        // Fetch and display each row as a card
        while ($row = $result->fetch_assoc()) {
            echo '<div style="width: 500px; margin: 30px; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); text-align: center; background-color: white">'; // Increased card width and center text
            echo '<div style="background-color: #007bff; padding: 10px; color: white;">'; // Header styling
            echo '<h5 style="margin: 0; font-size: 1.5em;">' . htmlspecialchars($row['mark_name']) . '</h5>'; // Title
            echo '</div>'; // End header
            echo '<img src="uploads/' . htmlspecialchars($row['upload_logo']) . '" alt="Logo" style="height: 200px; width: auto; display: block; margin: 20px auto; border-radius: 50%; object-fit: contain;">'; // Circular logo
            echo '<div style="padding: 20px; text-align: left;">'; // Card body
            echo '<p><strong>Name of Proprietor:</strong> ' . htmlspecialchars($row['name_of_proprietor']) . '</p>';
            echo '<p><strong>Country:</strong> ' . htmlspecialchars($row['country']) . '</p>';
            echo '<p><strong>Priority Date:</strong> ' . htmlspecialchars($row['priority_date']) . '</p>';
            echo '<p><strong>Filing Date:</strong> ' . htmlspecialchars($row['filing_date']) . '</p>';
            echo '<p><strong>Address:</strong> ' . htmlspecialchars($row['address_of_proprietor']) . '</p>';
            echo '<p><strong>International Class:</strong> ' . htmlspecialchars($row['international_class']) . '</p>';
            echo '<p><strong>Representative:</strong> ' . htmlspecialchars($row['Agent_Name']) . '</p>';
            echo '<p><strong>Description:</strong> ' . htmlspecialchars($row['description']) . '</p>';
            echo '<p><strong>Application Number:</strong> ' . htmlspecialchars($row['Anumber']) . '</p>';
            echo '<p><strong>Conditions:</strong> ' . htmlspecialchars($row['conditions']) . '</p>';
            echo '<p><strong>Exam Report Date:</strong> ' . htmlspecialchars($row['exam_report_date']) . '</p>';
            echo '<p><strong>Registration Date:</strong> ' . htmlspecialchars($row['registration_date']) . '</p>';
            echo '<p><strong>Renewal Date:</strong> ' . htmlspecialchars($row['renewal_date']) . '</p>';

            // Display the documents data
            echo '<p style="display: inline;"><strong>Documents:</strong></p>'; // Inline to keep it on the same line
            if (!empty($row['upload_exam_report']) || !empty($row['cert_attachment']) || !empty($row['renewal_certificate'])) {
                $documents_id = isset($row['id']) ? 'documents_' . $row['id'] : 'documents_unknown'; // Unique ID for each document section
                echo '<button onclick="toggleDocuments(\'' . $documents_id . '\')" style="padding: 5px 10px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">Display Documents</button>'; // Button positioned inline
                echo '<div id="' . $documents_id . '" style="display: none; margin-top: 10px; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">'; // Dropdown styling

                // Create an associative array for documents
                $documents = [
                    //'Power of Attorney' => $row['upload_poa'],
                    'Exam Report' => $row['upload_exam_report'],
                    'Certificate Attachment' => $row['cert_attachment'],
                    'Renewal Certificate' => $row['renewal_certificate'],
                ];
                
                foreach ($documents as $label => $document) {
                    if (!empty($document)) {
                        echo '<p><strong>' . htmlspecialchars($label) . ':</strong> <a href="uploads/' . htmlspecialchars(trim($document)) . '" target="_blank">' . htmlspecialchars(trim($document)) . '</a></p>';
                    }
                }

                echo '</div>'; // End document dropdown
            }

                // Add the update button here
    echo '<div style="margin-top: 20px;">';
    echo '<a href="trademark_edit.php?Anumber=' . htmlspecialchars($row['Anumber']) . '" style="padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;">Update</a>'; // Update button
    echo '</div>'; // End button container

    echo '</div>'; // End card body
    echo '</div>'; // End card

            echo '</div>'; // End card body
            echo '</div>'; // End card
        }
        echo '</div>'; // End flex container

        // Back to homepage button
        echo '<div style="text-align: center; margin-top: 20px;">';
        echo '<a href="trademark_form.php" style="padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">Back to Homepage</a>'; // Button
        echo '</div>';

        echo '</div>'; // End container
    } else {
        echo '<div style="max-width: 1200px; margin: auto; padding-top: 50px;"><h2 style="text-align: center;">No results found for "' . htmlspecialchars($search_term) . '"</h2>';
        // Back to homepage button for no results
        echo '<div style="text-align: center; margin-top: 20px;">';
        echo '<a href="trademark_form.php" style="padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">Back to Homepage</a>'; // Button
        echo '</div></div>';
    }



    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
<style>
body {
    background:  url('law.jpg') no-repeat center center fixed;
    background-size: cover;
}
</style>

<script>
function toggleDocuments(id) {
    var documentsDiv = document.getElementById(id);
    if (documentsDiv.style.display === 'none') {
        documentsDiv.style.display = 'block';
    } else {
        documentsDiv.style.display = 'none';
    }
}
</script>
