<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Information</title>
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

        /* Padding to prevent overlap with fixed navbar */
        .content {
            padding-bottom: 25px;
            padding-top: 125px; /* Adjust this value to create space below navbar */
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-container {
            background-color: white;
            border-radius: 12px;
            padding: 40px;
            width: 450px; /* Adjust width for better alignment */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); /* Soft shadow */
            transition: all 0.3s ease;
        }

        .form-container:hover {
            transform: translateY(-10px); /* Slightly lift when hovered */
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2); /* Slightly bigger shadow on hover */
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .form-group {
            position: relative;
            margin-bottom: 20px;
        }

        .form-group input[type="text"], 
        .form-group input[type="date"], 
        .form-group input[type="tel"], 
        .form-group select {
            width: 100%; /* Ensure full width for all inputs */
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1em;
            background-color: #f9f9f9;
            color: #333;
            transition: border 0.3s;
            box-sizing: border-box; /* Makes sure padding and width fit nicely */
        }

        .form-group input:focus, 
        .form-group select:focus {
            border-color: #3366ff; /* Blue border on focus */
        }

        .form-group select {
            appearance: none; /* Remove default dropdown arrow */
        }

        /* Button Styling */
        button {
            width: 100%; /* Full width button */
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
        <a href="doctor_home.php" class="logo">Bravo</a>
        <!-- You can add any additional links in the navbar here if needed -->
    </div>

    <!-- Content Section -->
    <div class="content">
        <div class="form-container">
            <h2>Enter Information to instruct course</h2>
            <form action="doctor_home.php" method="POST">
                <!-- Name -->
                <div class="form-group">
                    <input type="text" name="name" placeholder="Name" required>
                </div>

                <!-- Birthdate -->
                <div class="form-group">
                    <input type="date" name="birthdate" required>
                </div>

                <!-- Mobile Number -->
                <div class="form-group">
                    <input type="tel" name="mobile" placeholder="Mobile Number" required>
                </div>

                <!-- Gender (Dropdown) -->
                <div class="form-group">
                    <select name="gender" required>
                        <option value="" disabled selected>Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <!-- Specialization (Dropdown) -->
                <div class="form-group">
                    <select name="specialization" required>
                        <option value="" disabled selected>Select Specialization</option>
                        <option value="Cardiology">Cardiology</option>
                        <option value="Pediatrics">Pediatrics</option>
                        <option value="Neurology">Neurology</option>
                        <option value="Orthopedics">Orthopedics</option>
                        <option value="General Surgery">General Surgery</option>
                    </select>
                </div>

                <!-- Medical Degree (Dropdown) -->
                <div class="form-group">
                    <select name="medical_degree" required>
                        <option value="" disabled selected>Select Medical Degree</option>
                        <option value="MD">MD</option>
                        <option value="DO">DO</option>
                        <option value="MBBS">MBBS</option>
                        <option value="MSc">MSc</option>
                        <option value="PhD">PhD</option>
                    </select>
                </div>

                <!-- Assigned Courses (Dropdown) -->
                <div class="form-group">
                    <select name="assigned_courses" required>
                        <option value="" disabled selected>Select Assigned Courses</option>
                        <option value="Laparoscopic Surgery">Laparoscopic Surgery</option>
                        <option value="Bariatric Surgery">Bariatric Surgery</option>
                        <option value="Cardiology Basics">Cardiology Basics</option>
                        <option value="Neurological Procedures">Neurological Procedures</option>
                        <option value="Orthopedic Techniques">Orthopedic Techniques</option>
                    </select>
                </div>

                <!-- Hospital Affiliation (Dropdown) -->
                <div class="form-group">
                    <select name="hospital_affiliation" required>
                        <option value="" disabled selected>Select Hospital Affiliation</option>
                        <option value="City Hospital">City Hospital</option>
                        <option value="General Hospital">General Hospital</option>
                        <option value="Apollo Hospital">Apollo Hospital</option>
                        <option value="Mayo Clinic">Mayo Clinic</option>
                        <option value="Johns Hopkins Hospital">Johns Hopkins Hospital</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <button type="submit" onclick="window.location.href='doctor_home.php'">Sign up to instruct this course</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Contact Us: info@ngo.org | © 2024 Bravo</p>
    </div>

</body>
</html>
