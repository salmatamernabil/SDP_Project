<?php
session_start();

// Ensure searchResults uses allPatients if no search has been conducted
$searchResults = $_SESSION['searchResults'] ?? $_SESSION['allPatients'] ?? [];
$searchQuery = $_SESSION['searchQuery'] ?? '';
$reportFormat = $_SESSION['reportFormat'] ?? 'pdf';

// Debugging line to verify data availability
error_log("View searchResults data: " . print_r($searchResults, true));
?>


<!DOCTYPE html>
< lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Follow Up</title>
    <style>
        /* General Styling */
        body, html {
            margin: 0;
            
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            height: 120vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        .navbar .logo {
            color: #3366ff;
            font-size: 2em;
            font-weight: 800;
            text-decoration: none;
            transition: transform 0.3s;
        }
        .navbar .logo:hover {
            transform: scale(1.1);
        }

        /* Padding to prevent overlap with fixed navbar */
        .content {
            padding-top: 100px;
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Container for Follow-Up Content */
        .container {
            width: 80%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .container h1 {
            color: #3366ff;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Search Box Styling */
        .search-box {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
        }
        .search-box label {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        .search-box input {
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
            width: 100%;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        .search-box input:focus {
            border-color: #3366ff;
        }

        /* Patient Records Styling */
        .patient-records {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
        }
        .record {
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
        .record:last-child {
            border-bottom: none;
        }
        .record p {
            margin: 5px 0;
            font-size: 1em;
        }
        button {
            width: 100%;
            padding: 15px;
            background-color: #3366ff;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1.2em;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        button:hover {
            background-color: #254eda;
            transform: scale(1.02);
        }
        .form-group select {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1em;
            background-color: #f9f9f9;
            color: #333;
            transition: border 0.3s;
            box-sizing: border-box;
        }

        
        .form-group select:focus {
            border-color: #3366ff;
        }

        .form-group select {
            appearance: none;
        }

        /* Footer */
        .footer {
            background-color: #3366ff;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 1em;
        }
    </style>
</head>
<>

      <!-- Navbar -->
    <div class="navbar">
        <a href="admin_home_view.php" class="logo">Bravo</a>
    </div>

    <div class="content">
        <div class="container">
            <h1>Follow Up</h1>

            <!-- Search Form -->
            <form action="../Controller/follow_up_controller.php" method="POST">
                <div class="search-box">
                    <label for="search">Search Patient:</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="Enter surgery type or hospital name" required>
                    <button type="submit" name="action" value="searchPatients">Search</button>
                </div>
            </form>

            <!-- Display Patient Records -->
            <div class="patient-records">
                <?php if (!empty($searchResults)): ?>
                    <?php foreach ($searchResults as $patient): ?>
                        <div class="record">
                            <p><strong>Patient ID:</strong> <?php echo htmlspecialchars($patient['PatientId']); ?></p>
                            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($patient['FullName'] ?? 'N/A'); ?></p>
                            <p><strong>Surgery Date:</strong> <?php echo htmlspecialchars($patient['SurgeryDate']); ?></p>
                            <p><strong>Type of Surgery:</strong> <?php echo htmlspecialchars($patient['TypeOfSurgery']); ?></p>
                            <p><strong>Hospital Name:</strong> <?php echo htmlspecialchars($patient['HospitalName']); ?></p>
                            <p><strong>Member ID (MID):</strong> <?php echo htmlspecialchars($patient['MID']); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Please enter your query.</p>
                <?php endif; ?>
            </div>

            <!-- Report Generation Dropdown and Button -->
            <form action="../Controller/follow_up_controller.php" method="POST">
                <div class="form-group">
                    <label for="report_format">Select report format:</label>
                    <select name="report_format" id="report_format" required>
                        <option value="pdf" <?php echo ($reportFormat === 'pdf') ? 'selected' : ''; ?>>PDF</option>
                        <option value="excel" <?php echo ($reportFormat === 'excel') ? 'selected' : ''; ?>>Excel</option>
                        <option value="word" <?php echo ($reportFormat === 'word') ? 'selected' : ''; ?>>Word</option>
                    </select>
                    <button type="submit" name="action" value="generateReport">Generate Report</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('report_format').addEventListener('change', function() {
            const reportFormat = this.value;

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../Controller/follow_up_controller.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log("Report format updated to:", reportFormat);
                }
            };
            xhr.send("report_format=" + reportFormat + "&ajax=true");
        });
    </script>
</body>
</html>
