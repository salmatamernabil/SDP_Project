<?php
session_start(); // Start the session at the beginning of the file

// Check if the session variables are set, and handle the case if they are not
if (!isset($_SESSION['admin_role']) || !isset($_SESSION['admin_id'])) {
    header("Location: ../View/admin_login_view.php");
    exit();
}

$upgradeableAdmins = $_SESSION['upgradeableAdmins'] ?? []; // Use an empty array as default if not set


// Include the AdminController
require_once '../Controller/admin_controller.php';

// Create an instance of AdminController
$adminController = new AdminController();

// Check and update admin role in session, if it has changed in the database
$adminController->checkAndUpdateAdminRole();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Control Panel</title>
    <style>
        /* General Styling */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column; /* Allow footer to stick to bottom */
            min-height: 100vh; /* Ensure full height of the viewport */
        }

        /* Sidebar */
        .sidebar {
            width: 200px;
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            position: fixed; /* Fixed position for the sidebar */
            height: 100vh; /* Full height */
            z-index: 1; /* Keep it on top */
            margin-top: 60px; /* Leave space for navbar */
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar a {
            color: #ecf0f1;
            text-decoration: none;
            padding: 10px 15px;
            margin: 5px 0;
            border-radius: 4px;
            transition: background-color 0.3s;
            display: flex; /* Align icon and text */
            align-items: center; /* Center vertically */
        }

        .sidebar a:hover {
            background-color: #34495e;
        }

        .notification, .profile {
            display: flex;
            align-items: center; /* Center icon vertically */
            margin: 5px 0;
        }

        .profile img {
            width: 40px;
            height: 40px;
        }

        .notification img {
            width: 20px; /* Adjust icon size */
            height: 20px; /* Adjust icon size */
            margin-right: 8px; /* Space between icon and text */
        }

        /* Center the Profile Icon */
        .profile {
            justify-content: center; /* Center the profile icon horizontally */
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            margin-top: 30px; /* Leave space for the sidebar */
            padding: 20px;
            flex-grow: 1; /* Allow this area to grow */
            background-color: white;
            overflow: auto; /* Add scrolling if content is too long */
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
            z-index: 10;
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

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        /* Dashboard Stats */
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .widget {
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* New Users Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
           
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
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            background-color: #3366ff;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 1em;
            width: 100%;
            z-index:2222;
        }
    </style>
</head>
<body>

    <!-- Navbar -->

    <!-- Navbar -->
    <div class="navbar">
        <a href="admin_home_view.php" class="logo">Bravo</a>
    </div>

    <div class="sidebar">
    <div class="profile">
        <a href="admin_home_view.php">
            <img src="https://static-00.iconduck.com/assets.00/profile-icon-512x512-w0uaq4yr.png" alt="Profile">
        </a>
    </div>
    <div class="notification">
        <a href="#">
            <img src="https://cdn-icons-png.flaticon.com/512/3239/3239958.png" alt="Notification">
            Notifications  
            <?php if (isset($_SESSION['notification_count']) && $_SESSION['notification_count'] > 0) : ?>
                <span class="badge"><?php echo $_SESSION['notification_count']; ?></span>
            <?php endif; ?>
        </a>
    </div>
    
    <!-- Links for Chief Admin functionalities -->
    <?php if ($_SESSION['admin_role'] === 'ChiefAdmin'): ?>
        <a href="../View/add_patient_view.php">Add Patient</a>
        <a href="../View/edit_patient_view.php">Edit Patient</a>
        <a href="delete_patient.php">Delete Patient</a>
        <a href="edit_patient.php">Add Course</a>
        <a href="edit_course.php">Edit Course</a>
        <a href="delete_course.php">Delete Course</a>
    <?php elseif ($_SESSION['admin_role'] === 'SuperAdmin'): ?>
        <!-- Links for Super Admin -->
        <a href="../View/add_patient_view.php">Add Patient</a>
        <a href="../View/edit_patient_view.php">Edit Patient</a>
        <a href="edit_patient.php">Add Course</a>
        <a href="edit_patient.php">Edit Course</a>
    <?php elseif ($_SESSION['admin_role'] === 'BaseAdmin'): ?>
        <!-- Link for Base Admin -->
        <a href="add_patient_view.php">Add Patient</a>
        <a href="edit_patient.php">Add Course</a>
        <!-- Logout Button in Sidebar -->
        

    <?php endif; ?>
    <a href="home_view.php">Logout</a>
</div>


    <!-- Main Content -->
    <div class="main-content">
        <h1>Welcome to the Admin Control Panel</h1>

     <!-- Display functionality based on admin role -->
     <?php if ($_SESSION['admin_role'] === 'ChiefAdmin'): ?>
            <!-- Chief Admin functionalities -->
            <h2>Upgradeable Admin Accounts</h2>
            
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Current Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['upgradeableAdmins'] as $admin): ?>
                        <tr>
                            <td><?php echo $admin['username']; ?></td>
                            <td><?php echo $admin['role']; ?></td>
                            <td>
                                <form action="../Controller/admin_controller.php" method="POST">
                                    <input type="hidden" name="admin_id" value="<?php echo $admin['admin_id']; ?>">
                                    <button type="submit" name="upgrade">Upgrade</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php endif; ?>
         
       <!-- Dashboard Statistics -->
        <div class="dashboard">
            <div class="widget">
                <h3>Total Patients</h3>
                <p>25</p>
            </div>
            <div class="widget">
                <h3>Pending Approvals</h3>
                <p>5</p>
            </div>
            <div class="widget">
                <h3>Active Courses</h3>
                <p>3</p>
            </div>
            <div class="widget">
                <h3>Comorbidities Managed</h3>
                <p>15</p>
            </div>
            <div class="widget">
                <h3>Donations Made</h3>
                <p>$1,500</p>
            </div>
            <div class="widget">
                <h3>Reports Generated</h3>
                <p>12</p>
            </div>
        </div>

<!-- Pending Signup Requests -->
<h2>Pending Signup Requests</h2>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Account Type</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="pendingAccountsTable">
                <!-- Pending accounts will be dynamically loaded here -->
            </tbody>
        </table>
    </div>

 

    <script>
        // Fetch pending accounts every 5 seconds
        function fetchPendingAccounts() {
            fetch('../Controller/admin_controller.php?action=getPendingAccounts')
                .then(response => response.json())
                .then(data => {
                    let tableBody = document.getElementById('pendingAccountsTable');
                    tableBody.innerHTML = ''; // Clear existing rows

                    data.forEach(account => {
                        let row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${account.username}</td>
                            <td>${account.email}</td>
                            <td>${account.account_type}</td>
                            <td>
                                <form action="../Controller/admin_controller.php" method="POST">
                                    <input type="hidden" name="username" value="${account.username}">
                                    <button type="submit" name="approve">Approve</button>
                                </form>
                            </td>`;
                        tableBody.appendChild(row);
                    });

                    // Update the notification badge to show the number of pending accounts
                    let notificationBadge = document.getElementById('notificationBadge');
                    notificationBadge.textContent = data.length > 0 ? data.length : '';
                })
                .catch(error => console.error('Error fetching pending accounts:', error));
        }

        setInterval(fetchPendingAccounts, 10000); // Call the function every 5 seconds
        fetchPendingAccounts(); // Initial load

        
    </script>

</body>
</html>

