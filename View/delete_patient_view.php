<?php
// Start the session (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fetch patients from the session
$patients = $_SESSION['PatientsDelete'] ?? [];
error_log("[DEBUG] Patients in session: " . print_r($patients, true));
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Patient Details</title>
    <style>
        /* General Styling */
        body, html {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
        }

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

        .content {
            padding-top: 80px;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #3366ff;
            color: white;
        }

        button {
            padding: 8px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            margin-right: 5px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        .footer {
            background-color: #3366ff;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 1em;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href="admin_home_view.php" class="logo">Bravo</a>
    </div>

    <div class="content">
        <h1>Add Patient Details</h1>

        <?php if (isset($patients) && !empty($patients)): ?>
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Surgery Date</th>
                    <th>Type Of Surgery</th>
                    <th>Hospital Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                    <td><?php echo htmlspecialchars($patient['full_name'] ?? 'N/A'); ?></td>
<td><?php echo htmlspecialchars($patient['birth_date'] ?? 'N/A'); ?></td>
<td><?php echo htmlspecialchars($patient['gender'] ?? 'N/A'); ?></td>
<td><?php echo htmlspecialchars($patient['hospital_name'] ?? 'N/A'); ?></td>
                        <td>
                            
                            <form action="../Controller/delete_patient_controller.php" method="POST" style="display:inline;">
    <input type="hidden" name="patient_id" value="<?php echo htmlspecialchars($patient['patient_id'] ?? ''); ?>">
    <button type="submit" <?php echo isset($patient['patient_id']) ? '' : 'disabled'; ?>>Delete Patient</button>
</form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No patients found.</p>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div class="footer">
        &copy; 2025 Bravo. All rights reserved.
    </div>
</body>
</html>
