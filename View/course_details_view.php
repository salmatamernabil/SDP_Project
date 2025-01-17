<?php
require_once '../Controller/all_courses_controller.php';

// Check if the courseId is passed in the URL
if (isset($_GET['courseId'])) {
    $courseId = $_GET['courseId'];

    // Instantiate the controller and fetch course details
    $controller = new AllCoursesController();
    $courseDetails = $controller->getCourseDetails($courseId);
} else {
    die("Course ID is missing.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Follow-Up Details</title>
    <style>
        /* General Styling */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            height: 100vh;
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
            padding-top: 125px;
            padding-bottom: 25px; /* Adjust this value to create space below navbar */
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .course-container {
            background-color: white;
            border-radius: 12px;
            padding: 40px;
            width: 600px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .course-details {
            margin-bottom: 40px; /* Increased space between details and button */
        }

        .course-details label {
            font-weight: 600;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }

        .course-details p {
            margin: 5px 0;
            font-size: 1.1em;
        }

        /* Progress Bar Styling */
        .progress-bar-container {
            background-color: #ddd;
            border-radius: 20px;
            padding: 3px;
            margin-bottom: 20px;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.2); /* Slight shadow inside */
        }

        .progress-bar {
            height: 20px;
            border-radius: 20px;
            background-color: #3366ff;
            width: 50%; /* Starting at 50% */
            transition: width 0.5s ease-in-out; /* Smooth transition */
        }

        .progress-label {
            text-align: right;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        /* Button Styling */
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
            transform: scale(1.02); /* Slight grow on hover */
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
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="admin_home_view.php" class="logo">Bravo</a>
    </div>

    <!-- Content Section -->
    <div class="content">
        <div class="course-container">
            <h2>Course Follow-Up Details</h2>

            <!-- Course Details -->
            <div class="course-details">
                <label>Course Name:</label>
                <p><?php echo htmlspecialchars($courseDetails['course_name']); ?></p>

                <label>Date of Course:</label>
                <p><?php echo htmlspecialchars($courseDetails['course_date']); ?></p>

                <label>Place of Course:</label>
                <p><?php echo htmlspecialchars($courseDetails['course_place']); ?></p>

                <label>Total Cost:</label>
                <p><?php echo htmlspecialchars($courseDetails['total_cost']); ?></p>

               
                <label>Current Funds:</label>
                <div class="progress-bar-container">
                    <div class="progress-bar" id="progressBar"></div>
                </div>
                <div class="progress-label" id="currentFundsLabel">EGP 0 / EGP <?php echo htmlspecialchars($courseDetails['total_cost']); ?></div>
           
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Contact Us: info@ngo.org | © 2024 Bravo</p>
    </div>

    <script>
        // Simulating total cost and current funds
        let totalCost = <?php echo $courseDetails['total_cost']; ?>;
        let currentFunds = <?php echo htmlspecialchars($courseDetails['donations_received']); ?>; // You can replace this with actual data if available

        // Function to update progress bar
        function updateProgressBar() {
            let progressPercentage = (currentFunds / totalCost) * 100;
            document.getElementById("progressBar").style.width = progressPercentage + "%";
            document.getElementById("currentFundsLabel").innerText = "EGP " + currentFunds + " / EGP " + totalCost;
        }

        // Initial update of progress bar to 50%
        updateProgressBar();
    </script>

</body>
</html>
