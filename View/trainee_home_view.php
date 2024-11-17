<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainee Home</title>
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

        .navbar a {
            color: #3366ff;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 1.1em;
            font-weight: 600;
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

        /* Main Content */
        .main-content {
            margin-left: 275px;
            margin-top: 100px; /* Leave space for the sidebar */
            padding: 20px;
            flex-grow: 1; /* Allow this area to grow */
            background-color: white;
            overflow: auto; /* Add scrolling if content is too long */
            padding-top: 20px; /* Top padding for main content */
        }
        
        .notification, .profile {
            display: flex;
            align-items: center; /* Center icon vertically */
            margin: 5px 0;


        }
        .profile img{

            width:40px;
            height: 40px;
        }
        .profile {
            justify-content: center; /* Center the profile icon horizontally */
        }

        .notification img {
            width: 20px; /* Adjust icon size */
            height: 20px; /* Adjust icon size */
            margin-right: 8px; /* Space between icon and text */
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

        /* Footer */
        .footer {
            background-color: #3366ff;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 1em;
            width: 100%; 
            z-index: 2; /* Full width */
        }
    </style>
    </head>
<body>
<div class="navbar">
        <a href="trainee_home_view.php" class="logo">Bravo</a>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="profile">
            <a href="trainee_home_view.php">
                <img src="https://static-00.iconduck.com/assets.00/profile-icon-512x512-w0uaq4yr.png" alt="Profile"> <!-- Profile Icon -->
                
            </a>
        </div>
    <div class="notification">
            <a href="approve_accounts_view.php">
                <img src="https://cdn-icons-png.flaticon.com/512/3239/3239958.png" alt="Notification"> <!-- Notification Icon -->
                Notifications
            
            </a>
        </div>
        <a href="all_courses.php">See All Courses</a>
        <a href="doctor.php">Enroll in Course</a>
        <a href="course_details.php">See Course Details</a>
        <a href="home_view.php">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Welcome to the Trainee Dashboard</h1>

        <!-- Dashboard Statistics -->
        <div class="dashboard">
            <div class="widget">
                <h3>All Courses</h3>
                <p>25</p>
            </div>
            <div class="widget">
                <h3>Active Courses</h3>
                <p>3</p>
            </div>
            <div class="widget">
                <h3>Upcoming Courses</h3>
                <p>5</p>
            </div>
            <div class="widget">
                <h3>Trainees Enrolled in Courses</h3>
                <p>15</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Contact Us: BRAVO@ngo.org | © 2024 Bravo</p>
    </div>

</body>
</html>
