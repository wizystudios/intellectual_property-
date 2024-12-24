<?php
session_start();

// Debugging tip: Check if the session is starting correctly
if (session_status() == PHP_SESSION_NONE) {
    die('Session did not start correctly.');
}

// Debugging tip: Check if the session variable is set
if (!isset($_SESSION['loggedin'])) {
    // For debugging only - this should not be on a live site
    error_log('Session variable "loggedin" is not set.');
} else {
    // For debugging only - this should not be on a live site
    error_log('Session variable "loggedin" is set to: ' . $_SESSION['loggedin']);
}

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // User is not logged in, redirect to the login page
    header('Location: login.php');
    exit;
}

// Fetch the username from the session
$user = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// The rest of your protected page code goes here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eipsys</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
	 <!-- Favicon -->
    <link rel="icon" href="fav.png" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            background:  url('law.png') no-repeat center center fixed;
            background-size: cover;
        }
        .logo {
            width: 200px;
            height: 100px;
            display: block;
            margin: 0 auto 20px;
            cursor: pointer;
        }
        .button-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }
        .button {
            width: 150px;
            height: 75px;
            font-size: 14px;
            font-weight: bold;
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        .trademark { background-color: Darkblue; }
        .patents { background-color: Darkblue; }
        .industrial-designs { background-color: Darkblue; color: white; }
        .trade-secrets { background-color: Darkblue; }
        .plant-breeders { background-color:Darkblue; }
        .search { background-color: green; color: white; }
        .extra { background-color: darkblue; }
        .form-container,
        .search-container {
            display: none; /* Hidden by default */
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
            position: relative;
        }
        .form-container.active,
        .search-container.active {
            display: block;
        }
        .blue-ribbon {
            color: white;
            padding: 10px;
            font-size: 18px;
            text-align: center;
            position: absolute;
            top: -40px;
            left: 0;
            width: 100%;
            border-radius: 5px 5px 0 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-group select,
        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group input[type="file"],
        .form-group textarea {
            padding: 10px;
            border-radius: 5px;
        }
        .btn-submit {
            background-color: #4CAF50;
            border: none;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-submit:hover {
            background-color: #45a049;
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }
        .form-actions .btn-secondary {
            margin-left: 10px;
        }
        .search-container {
            margin-top: 20px;
        }
        .form-control-file {
            padding: 10px;
        }
        /* Custom widths for the form fields */
        .line-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }
        .exam-report-width {
            width: 5cm;
        }
        .condition-width {
            width: 2cm;
        }
        .publication-width {
            width: 5cm;
        }

        .blue-ribbon {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 18px;
        }

        .button.extra {
            background-color: cadetblue;
            height: 50px;
        } 
        .logout-container {
            margin-top: 20px;
            text-align: center;
        }
        .logout-button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            background-color: #DC3545;
            color: white;
            border-radius: 5px;
        }
    
        .alert-dropdown {
            position: relative;
            display: inline-block;
        }
        .alert-dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 300px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .alert-dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .alert-dropdown-content a:hover {
            background-color: green;
        }
        .alert-dropdown:hover .alert-dropdown-content {
            display: block;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: #333;
        }
        .logo {
            height: 50px; /* Adjust the logo size as needed */
        }
        .user-info {
            display: flex;
            align-items: center;
        }
        .username {
            margin-right: 20px;
            color: white;
            font-size: 16px;
        }
        .logout-button {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .container {
    width: 100%;
    max-width: 1900px;/* Adjust as necessary */
    margin: 0 auto;
    padding: 0 20px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    background-color: #333;
    width: 100%;
    height: 50px; /* Set the height of the header */
}
.dropdown-container {
        position: relative;
        display: inline-block;
    }
    .dropdown-content {
        display: none;
        position: absolute;
        top: 100%; /* Position below the Trademark button */
        left: 0;
        background-color: darkblue;
        min-width: 160px;
        border-radius: 10px;
        box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
        z-index: 1;
    }
    .dropdown-content button {
        color: white;
        padding: 8px 12px;
        text-align: left;
        background-color: darkblue;
        border: none;
        width: 100%;
        cursor: pointer;
        border-radius: 5px;
    }
    .dropdown-content button:hover {
        background-color: #0056b3;
    }
    
    </style>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
         <!-- Include jQuery and Select2 CSS/JS for better styling (optional) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
        <script>
        function showForm(formId, formTitle) {
            document.querySelector('.form-container').classList.add('active');
            document.querySelector('.search-container').classList.remove('active');
            const ribbon = document.querySelector('.blue-ribbon');
            ribbon.textContent = formTitle;
            switch (formId) {
                case 'trademark':
                    ribbon.style.backgroundColor = 'grey';
                    ribbon.style.color = 'white';
                    break;
                case 'patents':
                    ribbon.style.backgroundColor = 'lightblue';
                    ribbon.style.color = 'black';
                    break;
                case 'industrial_designs':
                    ribbon.style.backgroundColor = 'black';
                    ribbon.style.color = 'white';
                    break;
                case 'trade_secrets':
                    ribbon.style.backgroundColor = 'red';
                    ribbon.style.color = 'white';
                    break;
                case 'plant_breeders':
                    ribbon.style.backgroundColor = 'green';
                    ribbon.style.color = 'white';
                    break;
                default:
                    ribbon.style.backgroundColor = 'blue';
                    ribbon.style.color = 'white';
            }
            document.getElementById('form-action').setAttribute('action', formId + '_form.php');
        }
        function showSearch() {
            document.querySelector('.form-container').classList.remove('active');
            document.querySelector('.search-container').classList.add('active');
        }
        function goBack() {
            document.querySelector('.button-container').style.display = 'flex';
            document.querySelector('.form-container').classList.remove('active');
            document.querySelector('.search-container').classList.remove('active');
        }
    </script>
</head>
<body>
    <div class="container">
    <header class="header">
            <a href="homepage.php"><img src="logo.png" alt="Logo" class="logo"></a>
            <div class="user-info">
                <span class="username">Welcome, <?php echo htmlspecialchars($user); ?></span>
                <button class="logout-button" onclick="logout()">Logout</button>
            </div>
        </header>
        
        <h2 class="text-center" style="font-size: 36px; color: white; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);">Experience, Respect, Results</h2>

        <div class="button-container">
<!-- Trademark button with dropdown -->
    <div class="dropdown-container">
        <button class="button trademark" onclick="toggleTrademarkDropdown()" style="background-color: dark-blue; color: white; border: none; border-radius: 1200px; padding: 5px 10px; font-size: small; cursor: pointer;">
            <i class="fas fa-copyright"></i> Trademark
        </button>
        <div id="trademarkDropdown" class="dropdown-content">
            <button onclick="showForm('application_trademark', 'Application Trademark Form')">New Trademark</button>
             <button onclick="window.location.href='renewal_form.php';">Renewal</button> <!-- Update this line -->
            <button onclick="showSearch()">Update Trademark</button>
        </div>
    </div>
            <button class="button patents" onclick="showForm('patents', 'Patents Form')" style="background-color: dark-blue; color: white; border: none; border-radius: 1200px; padding: 5px 10px; font-size: small; cursor: pointer;">
                <i class="fas fa-file-alt"></i> Patents
            </button>
            <button class="button industrial-designs" onclick="showForm('industrial_designs', 'Industrial Designs Form')" style="background-color: dark-blue; color: white; border: none; border-radius: 1200px; padding: 5px 10px; font-size: small; cursor: pointer;">
                <i class="fas fa-drafting-compass"></i> Industrial Designs
            </button>
            <button class="button trade-secrets" onclick="showForm('trade_secrets', 'Trade Secrets Form')" style="background-color: dark-blue; color: white; border: none; border-radius: 1200px; padding: 5px 10px; font-size: small; cursor: pointer;">
                <i class="fas fa-user-secret"></i> Trade Secrets
            </button>
            <button class="button plant-breeders" onclick="showForm('plant_breeders', 'Plant Breeders Form')" style="background-color: dark-blue; color: white; border: none; border-radius: 1200px; padding: 5px 10px; font-size: small; cursor: pointer;">
                <i class="fas fa-seedling"></i> Plant Breeders
            </button>
            <button class="button search" onclick="showSearch()" style="background-color: black; color: white; border: none; border-radius: 1200px; padding: 5px 10px; font-size: small; cursor: pointer;">
                <i class="fas fa-search"></i> Search
            </button>
        </div>

        <div class="button-container">
            <button class="button extra" onclick="location.href='https://taxpayerportal.tra.go.tz/'" style="background: url('images/Asset 2@3x.png') no-repeat center center; background-size: contain; color: white; border: none; border-radius: 1200px; padding: 5px 10px; font-size: small; cursor: pointer;"></button>
            <button class="button extra" onclick="location.href='https://ors.brela.go.tz/'" style="background: url('images/Asset 3@4x.png') no-repeat center center; background-size: contain; color: white; border: none; border-radius: 1200px; padding: 5px 10px; font-size: small; cursor: pointer;"></button>
            <button class="button extra" onclick="location.href='https://tausi.tamisemi.go.tz/#/welcome'" style="background: url('images/Asset 1@2x.png') no-repeat center center; background-size: contain; color: white; border: none; border-radius: 1200px; padding: 5px 10px; font-size: small; cursor: pointer;"></button>
        
                <div class="alert-dropdown">
                    <button class="button alerts" style="background: url('images/messa 1.png') no-repeat center center; background-size: contain; color: black; border: none; border-radius: 2000px; padding: 5px 10px; font-size: small; cursor: pointer;"></button>
                    <div class="alert-dropdown-content">
        <!-- PHP code to dynamically populate alerts -->
        <?php
        // Database configuration
        $host = 'sql207.infinityfree.com';
        $dbname = 'if0_37560109_698';
        $username = 'if0_37560109';
        $password = '5112kharifa';

        // Create connection
        $conn = new mysqli($host, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Fetch alerts from database
        $sql = "SELECT id, alert_message, created_at, is_unread FROM alerts"; // Updated to is_unread
        $result = $conn->query($sql);

        if ($result === false) {
            echo "Error: " . $conn->error;
        } else {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Format date for better readability
                    $alertDate = new DateTime($row["created_at"]);
                    
                    // Determine the color based on the unread status
                    // 1 for unread (green), 0 for read (red)
                    $color = ($row["is_unread"] == 1) ? 'green' : 'red'; 
                    
                    // Display the alert with a click event to mark as read
                    echo "<a href='?mark_as_read=" . $row["id"] . "' style='color: $color;'>" . $alertDate->format('Y-m-d H:i:s') . ": " .                        htmlspecialchars($row["alert_message"]) . "</a><br>";
                }
            } else {
                echo "<a href='#'>No alerts</a>";
            }
        }

        // Mark alert as read if requested
        if (isset($_GET['mark_as_read'])) {
            $alert_id = intval($_GET['mark_as_read']);
            $update_sql = "UPDATE alerts SET is_unread = 0 WHERE id = ?"; // Updated to is_unread
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("i", $alert_id);
            $stmt->execute();

            // Redirect to avoid re-triggering on refresh
            //header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
        $conn->close();
        ?>


                            </div>
                        </div>
                    </div>
                </div>

        </div> 
<!--------------------------------------------------------first Block----------------------------------------------------------------------------->            
        <div id="form-container" class="form-container">
            <div class="blue-ribbon"></div>
            <form action="submit_form.php" method="POST" enctype="multipart/form-data">
    
    <div class="line-group">
        <div>
        <label for="country" style="font-weight: bold;">Filing Country<span style="color: red;">*</span></label>
            <select id="country" name="country" class="form-control" required>
                <option value="">Select Country</option>
                <option value="tanzania" data-flag="http://upload.wikimedia.org/wikipedia/commons/thumb/3/38/Flag_of_Tanzania.svg/320px-Flag_of_Tanzania.svg.png">Tanganyika</option>
                <option value="zanzibar" data-flag="https://flagsweb.com/Subnational_Flags/PNG/Flag_of_Zanzibar.png">Zanzibar</option>
                <option value="kenya" data-flag="https://cdn.britannica.com/15/15-004-B5D6BF80/Flag-Kenya.jpg">Kenya</option>
                <option value="uganda" data-flag="http://s1.bwallpapers.com/wallpapers/2014/05/29/uganda-flag_121432219.jpg">Uganda</option>
                <option value="rwanda" data-flag="http://www.theflagman.co.uk/wp-content/uploads/2017/03/flag-of-Rwanda.jpg">Rwanda</option>
                <option value="zambia" data-flag="https://wallpapercave.com/wp/wp4216071.jpg">Zambia</option>
                <option value="congo_drc" data-flag="http://s1.bwallpapers.com/wallpapers/2014/05/29/democratic-republic-of-the-congo-flag_121251834.jpg">Congo DRC</option>
                <option value="oapi" data-flag="https://www.musicinafrica.net/sites/default/files/styles/profile_images_large/public/images/music_professional_profile/201512/oapi.png?itok=OX6SHHRW">OAPI</option> <!-- Updated OAPI flag -->
                <option value="south_sudan" data-flag="https://www.worldatlas.com/upload/b1/9f/c9/ss-flag.jpg">South Sudan</option>
                <option value="aripo" data-flag="https://secureservercdn.net/198.71.233.185/abi.b28.myftpupload.com/wp-content/uploads/2020/07/ARIPO-Logo.png">ARIPO</option> <!-- Updated ARIPO flag -->
                <option value="angola" data-flag="https://upload.wikimedia.org/wikipedia/commons/9/9d/Flag_of_Angola.svg">Angola</option>
                <option value="india" data-flag="https://upload.wikimedia.org/wikipedia/en/4/41/Flag_of_India.svg">India</option>
                <option value="china" data-flag="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Flag_of_the_People%27s_Republic_of_China.svg/1200px-Flag_of_the_People%27s_Republic_of_China.svg.png">China</option>
            </select>
            <img id="selected_flag" src="" alt="Selected Flag" style="display:none; width: 50px; height: auto; margin-left: 10px;">
        </div>
        <div class="form-group publication-width" id="priority_date_group">
            <label for="priority_date">Priority Date</label>
            <input type="date" id="priority_date" name="priority_date" class="form-control">
        </div>
        <div class="form-group publication-width" id="filing_date_group">
            <label for="filing_date">Filing Date<span style="color: red;">*</span></label>
            <input type="date" id="filing_date" name="filing_date" class="form-control">
        </div>
    </div>

    <!----------------------------------------------------------second Block------------------------------------------------------------------>
    <div class="line-group">
        <div class="form-group" style="width: 10cm;">
            <label for="name_of_proprietor">Name of Proprietor<span style="color: red;">*</span></label>
            <input type="text" id="name_of_proprietor" name="name_of_proprietor" class="form-control" required>
        </div>
        <div class="form-group" style="width: 17cm;">
            <label for="address_of_proprietor">Address of Proprietor<span style="color: red;">*</span></label>
            <input type="text" id="address_of_proprietor" name="address_of_proprietor" class="form-control" required>
        </div>
    </div>
    
    <!-----------------------------------------------------------Third Block----------------------------------------------------------------------------->

    <div class="line-group">
        <div class="form-group" style="width: 17cm;">
            <label for="mark_name">Mark Name<span style="color: red;">*</span></label>
            <input type="text" id="mark_name" name="mark_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="upload_logo">Upload Logo</label>
            <input type="file" id="upload_logo" name="upload_logo" class="form-control-file">
        </div>
    </div>

    <!-----------------------------------------------------------Fourth Block----------------------------------------------------------------------------->
    <div style="display: flex; gap: 10px;">
        <!-- International Class -->
        <div class="form-group" style="flex: 1;">
            <label for="international_class">International Class<span style="color: red;">*</span></label>
            <select id="international_class" name="international_class" class="form-control" required>
                    <option value="class_1">Class 1</option>
                    <option value="class_2">Class 2</option>
                    <option value="class_3">Class 3</option>
                    <option value="class_4">Class 4</option>
                    <option value="class_5">Class 5</option>
                    <option value="class_6">Class 6</option>
                    <option value="class_7">Class 7</option>
                    <option value="class_8">Class 8</option>
                    <option value="class_9">Class 9</option>
                    <option value="class_10">Class 10</option>
                    <option value="class_11">Class 11</option>
                    <option value="class_12">Class 12</option>
                    <option value="class_13">Class 13</option>
                    <option value="class_14">Class 14</option>
                    <option value="class_15">Class 15</option>
                    <option value="class_16">Class 16</option>
                    <option value="class_17">Class 17</option>
                    <option value="class_18">Class 18</option>
                    <option value="class_19">Class 19</option>
                    <option value="class_20">Class 20</option>
                    <option value="class_21">Class 21</option>
                    <option value="class_22">Class 22</option>
                    <option value="class_23">Class 23</option>
                    <option value="class_24">Class 24</option>
                    <option value="class_25">Class 25</option>
                    <option value="class_26">Class 26</option>
                    <option value="class_27">Class 27</option>
                    <option value="class_28">Class 28</option>
                    <option value="class_29">Class 29</option>
                    <option value="class_30">Class 30</option>
                    <option value="class_31">Class 31</option>
                    <option value="class_32">Class 32</option>
                    <option value="class_33">Class 33</option>
                    <option value="class_34">Class 34</option>
                    <option value="class_35">Class 35</option>
                    <option value="class_36">Class 36</option>
                    <option value="class_37">Class 37</option>
                    <option value="class_38">Class 38</option>
                    <option value="class_39">Class 39</option>
                    <option value="class_40">Class 40</option>
                    <option value="class_41">Class 41</option>
                    <option value="class_42">Class 42</option>
                    <option value="class_43">Class 43</option>
                    <option value="Class_44">Class 44</option>
                    <option value="Class_45">Class 45</option>
            </select>
        </div>

        <!-- Agent Name -->
        <div class="form-group" style="flex: 1;">
            <label for="Agent_Name">Agent Name<span style="color: red;">*</span></label>
            <input type="text" id="Agent_Name" name="Agent_Name" class="form-control" required>
        </div>
    </div>

    <!-----------------------------------------------------------Fifth Block----------------------------------------------------------------------------->

    <div class="form-group">
        <label for="description">Description<span style="color: red;">*</span></label>
        <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
    </div>

    <!-----------------------------------------------------------sixth Block----------------------------------------------------------------------------->

    <div class="line-group">
       <div class="form-group" style="width: 10cm;">
            <label for="name">Application Number<span style="color: red;">*</span></label>
            <input type="text" id="Anumber" name="Anumber" class="form-control" required>
        </div>
        
        <!--<div class="form-group">
            <label for="upload_poa">Upload POA</label>
            <input type="file" id="upload_poa" name="upload_poa" class="form-control-file">
        </div>----->
        <div class="form-group exam-report-width">
            <label for="exam_report_date">Exam Report Date</label>
            <input type="date" id="exam_report_date" name="exam_report_date" class="form-control" required>
        </div>
    </div>

    <!-----------------------------------------------------------seventh Block----------------------------------------------------------------------------->

    <div class="line-group">
        <div class="form-group" style="width: 4cm;">
            <label for="conditions">Conditions</label>
            <select id="conditions" name="conditions" class="form-control" onchange="togglePublicationDate()" required>
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>
        </div>
        <div class="form-group publication-width" id="publication_date_group" style="display: none;">
            <label for="publication_date">Publication Date</label>
            <input type="date" id="publication_date" name="publication_date" class="form-control">
        </div>
        <div class="form-group">
            <label for="upload_exam_report">Upload Exam Report</label>
            <input type="file" id="upload_exam_report" name="upload_exam_report" class="form-control-file">
        </div>
    </div>

    <!-----------------------------------------------------------Eight Block----------------------------------------------------------------------------->

    <div class="line-group">
        <div class="form-group">
            <label for="registration_date">Registration Date<span style="color: red;">*</span></label>
            <input type="date" id="registration_date" name="registration_date" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="cert_attachment">Cert Attachment</label>
            <input type="file" id="cert_attachment" name="cert_attachment" class="form-control-file">
        </div>
    </div>

    <div class="line-group">
        <div class="form-group">
            <label for="renewal_date">Renewal DueDate<span style="color: red;">haijalishi tarehe utayoweka mfumo utaweka tarehe sahihi</span><span style="color: red;">*</span></label>
            <input type="date" id="renewal_date" name="renewal_date" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="renewal_certificate">Renewal Certificate</label>
            <input type="file" id="renewal_certificate" name="renewal_certificate" class="form-control-file">
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-submit">Submit</button>
        <button type="button" class="btn btn-secondary" onclick="goBack()">Back</button>
    </div>
</form>
       </div>

        <div class="centered-container">
        <div class="container mt-5">
            <div id="search-container" class="search-container text-center">
                <div class="blue-ribbon mb-3">Search Form</div>
                <form id="search-action" action="search_form.php" method="POST">
                    <div class="form-group">
                        <label for="search_term">Search Application Number</label>
                        <input type="text" id="search_term" name="search_term" class="form-control" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>
            </div>
        </div>  
    </div>


       <script>
    function togglePublicationDate() {
        const conditions = document.getElementById("conditions").value;
        const publicationDateGroup = document.getElementById("publication_date_group");

        // Show Publication Date if the condition is "no", otherwise hide it
        if (conditions === "no") {
            publicationDateGroup.style.display = "block";
        } else {
            publicationDateGroup.style.display = "none";
        }
    }



    $(document).ready(function() {
        // Initialize Select2
        $('#country').select2({
            templateResult: function(option) {
                if (!option.id) {
                    return option.text; // Return the text for placeholder
                }
                var img = $('<img src="' + $(option.element).data('flag') + '" style="width: 20px; height: auto; margin-right: 10px;">');
                return img.add(option.text);
            },
            minimumResultsForSearch: Infinity // Disable search
        });
    });
</script>

<script>
    const countrySelect = document.getElementById('country');
    const selectedFlagImage = document.getElementById('selected_flag');

    countrySelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const flagImage = selectedOption.getAttribute('data-flag');
        
        if (flagImage) {
            selectedFlagImage.src = flagImage;
            selectedFlagImage.style.display = 'inline';
        } else {
            selectedFlagImage.style.display = 'none';
        }
    });

    function logout() {
            // Redirect to the login page
            window.location.href = 'login.php'; // Replace 'loginpage.html' with the actual login page URL
        }
        document.addEventListener('DOMContentLoaded', populateAlerts);
</script>


<script>
    function toggleTrademarkDropdown() {
        const dropdown = document.getElementById('trademarkDropdown');
        dropdown.style.display = dropdown.style.display === 'none' || dropdown.style.display === '' ? 'block' : 'none';
    }

    // Optional: Close the dropdown if clicking outside
    window.onclick = function(event) {
        if (!event.target.matches('.trademark')) {
            const dropdowns = document.getElementsByClassName("dropdown-content");
            for (let i = 0; i < dropdowns.length; i++) {
                const openDropdown = dropdowns[i];
                if (openDropdown.style.display === 'block') {
                    openDropdown.style.display = 'none';
                }
            }
        }
    }

</script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
