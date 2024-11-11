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

        .navbar a {
            color: #3366ff;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 1.1em;
            font-weight: 600;
        }

        .content {
            
    padding-top: 125px;
    padding-bottom: 25px;
    flex-grow: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center; /* Center-aligns the content inside */
}

.trainee-home-container {
    background-color: white;
    border-radius: 12px;
    padding: 40px;
    max-width: 450px;
    width: 100%; /* Make it responsive by allowing 100% width */
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    text-align: center;
    margin: 0 auto; /* Center-aligns the container */
}
        h2 {
            color: #333;
            margin-bottom: 30px;
            font-weight: 600;
        }

        /* Button Styling */
        .button-link {
            display: block;
            width: 400px;
            padding: 12px;
            margin-bottom: 15px;
            background-color: #3366ff;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1.1em;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            margin-left: 15px;
            
        }

        .button-link:hover {
            background-color: #254eda;
            transform: scale(1.02);
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
        <a href="trainee_home,php" class="logo">Bravo</a>
    </div>

    <!-- Content Section -->
    <div class="content">
        <div class="trainee-home-container">
            <h2>Trainee Dashboard</h2>

            
            <a href="all_courses.php" class="button-link">See All Courses</a>
            <a href="trainee.php" class="button-link">Instruct Course</a>
            <a href="course_details.php" class="button-link">See Course Details</a>

        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Contact Us: info@ngo.org | © 2024 Bravo</p>
    </div>

</body>
</html>
