<?php
session_start(); // Make sure this is at the very top
if (isset($_SESSION['DoctorID'])) {
    
    error_log("doctor table with MemberID: " . $_SESSION['DoctorID']);
} else {
    echo "No MemberID found in session.";
}

// Fetch any success or error messages from the session
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
$errorMessage = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null;

// Clear session messages to prevent them from persisting
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Example data for dashboard statistics (replace with dynamic values from the database)
$allCourses = 25; // Example total courses
$activeCourses = 3; // Example active courses
$upcomingCourses = 5; // Example upcoming courses
$traineesEnrolled = 15; // Example total trainees
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Home</title>
    <style>
        /* General Styling */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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

        /* Sidebar */
        .sidebar {
            width: 200px;
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 1;
            margin-top: 60px;
        }
        .sidebar a {
            color: #ecf0f1;
            text-decoration: none;
            padding: 10px 15px;
            margin: 5px 0;
            border-radius: 4px;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
        }
        .sidebar a:hover {
            background-color: #34495e;
        }

        /* Main Content */
        .main-content {
            margin-left: 275px;
            margin-top: 100px;
            padding: 20px;
            flex-grow: 1;
            background-color: white;
            overflow: auto;
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

        /* Feedback Messages */
        .message {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            border-radius: 5px;
            font-size: 1.2em;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Footer */
        .footer {
            background-color: #3366ff;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 1em;
            width: 100%;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href="doctor_home_view.php" class="logo">Bravo</a>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="doctor_home_view.php">
            <img src="https://static-00.iconduck.com/assets.00/profile-icon-512x512-w0uaq4yr.png" alt="Profile" style="width:40px; height:40px;">
        </a>
       
        <a href="doctor_home_view.php">
            <img src="https://cdn-icons-png.flaticon.com/512/3239/3239958.png" alt="Notifications" style="width:20px; height:20px;"> Notifications
        </a>
      
        <a href="instruct_course_view.php">See All Courses</a>
        <a href="instruct_course_view.php">Instruct Course</a>
     <!--   <a href="course_details.php">See Course Details</a> -->
        <a href="home_view.php">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Welcome to the Doctor Dashboard</h1>

        <!-- Feedback Messages -->
        <?php if ($successMessage): ?>
            <div class="message success"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <div class="message error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <!-- Dashboard Statistics -->
        <div class="dashboard">
            <div class="widget">
                <h3>All Courses</h3>
                <p><?php echo htmlspecialchars($allCourses); ?></p>
            </div>
            <div class="widget">
                <h3>Active Courses</h3>
                <p><?php echo htmlspecialchars($activeCourses); ?></p>
            </div>
            <div class="widget">
                <h3>Upcoming Courses</h3>
                <p><?php echo htmlspecialchars($upcomingCourses); ?></p>
            </div>
            <div class="widget">
                <h3>Trainees Enrolled in Courses</h3>
                <p><?php echo htmlspecialchars($traineesEnrolled); ?></p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Contact Us: BRAVO@ngo.org | © 2024 Bravo</p>
    </div>
</body>
</html>
