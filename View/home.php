<?php
require_once '../Controller/home_controller.php';
$controller = new HomeController();
$controller->showHomePage();
$courses = $_SESSION['courses'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bravo - NGO</title>
    <style>
        /* General Styling */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

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
            color: #3366ff; /* Blue for the logo */
            font-size: 2em; /* Larger and stylish logo */
            font-weight: 800;
            text-decoration: none;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1); /* Light shadow for logo */
            transition: transform 0.3s, font-weight 0.3s;
        }
        .navbar .logo:hover {
            transform: scale(1.1); /* Increase size on hover */
            font-weight: 900; /* Bolder on hover */
        }

        .navbar a {
            color: #3366ff; /* Blue for links */
            text-decoration: none;
            padding: 10px 20px;
            font-size: 1.1em;
            font-weight: 600;
            transition: all 0.3s ease-in-out; /* Smooth transition for hover effects */
        }
        .navbar a:hover {
            color: #254eda; /* Darker blue on hover */
            transform: scale(1.1); /* Increase size on hover */
            font-weight: 700; /* Bold on hover */
        }

        /* Hero Section */
        .hero {
            background-color: #f4f4f4; /* Light grey background for hero */
            height: 70vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
            box-shadow: inset 0 0 0 1000px rgba(0, 0, 0, 0.05); /* Soft shadow effect */
        }
        .hero h1 {
            font-size: 4em;
            color: #3366ff; /* Blue title */
            margin-bottom: 10px;
            font-weight: 700;
        }
        .hero p {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 20px;
        }

       
        /* Info Section */
        .info-section {
            display: flex;
            justify-content: center; /* Center the boxes horizontally */
            align-items: center; /* Center the boxes vertically */
            text-align: center;
            padding: 50px 0;
            background-color: white;
            gap: 30px; /* Add space between the boxes */
        }
        .info-box {
            width: 30%; /* Adjust width to fit two boxes side by side */
            background-color: #f9f9f9;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            border-radius: 10px;
        }
        .info-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        .info-box h3 {
            font-size: 1.5em;
            margin-bottom: 20px;
            color: #3366ff; /* Blue for headings */
        }
        .info-box p {
            font-size: 1em;
            color: #666;
            margin-bottom: 20px;
        }

        /* Stylish Buttons */
        .info-box a {
            text-decoration: none;
            color: white;
            background: linear-gradient(to right, #3366ff, #254eda); /* Blue gradient */
            padding: 10px 20px;
            border-radius: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            font-size: 1.1em;
            font-weight: 600;
            transition: all 0.3s ease-in-out;
        }
        .info-box a:hover {
            background: linear-gradient(to right, #254eda, #3366ff);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            transform: scale(1.1); /* Increase size on hover */
            font-weight: 700; /* Bold on hover */
        }

        /* Footer */
        .footer {
            background-color: #f4f4f4;
            color: #666;
            padding: 20px;
            text-align: center;
            font-size: 0.9em;
        }
        .footer p {
            margin: 0;
            margin-bottom: 20px; /* Increase vertical space */
        }
        .footer span {
            color: #666; /* Non-hoverable text */
            font-weight: 600;
        }
        .footer a {
            color: #3366ff; /* Blue color for login */
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease-in-out;
        }
        .footer a:hover {
            color: #254eda; /* Darker blue on hover */
            font-weight: 700;
            transform: scale(1.1);
        }

              /* Media Queries */
              @media (max-width: 768px) {
            .info-section {
                flex-direction: column;
                padding: 20px;
            }
            .info-box {
                width: 80%;
                margin-bottom: 30px;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="home_view.php" class="logo">Bravo</a> <!-- Stylish Bravo organization name on the left -->
        <div>
            <a href="donate_view.php">Donate</a> <!-- Join Us leads to donate.php -->
            <a href="member_view.php">Join Us</a> <!-- Update this link to donate.php -->
        </div>
    </div>

    <!-- Hero Section -->
    <div class="hero">
        <div>
            <h1>Donate to Support Our Mission</h1>
            <p>Help us make a difference in the lives of those in need.</p>
        </div>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <div class="info-box">
            <h3>Give Now</h3>
            <p>Take action by making a money donation to support our cause.</p>
            <a href="donate_view.php">Make a Donation</a>
        </div>
        
   
    <div class="info-box">
            <h3>Donate Supplies</h3>
            <p>Contribute essential supplies to help those in need.</p>
            <a href="donate_supplies_view.php">Donate Supplies</a>
       
    </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Contact Us: BRAVO@ngo.org | © 2024 Bravo</p>
        <p><span>One of our team?</span> <a href="admin_login_view.php">Login</a></p>
    </div>

</body>
</html>
