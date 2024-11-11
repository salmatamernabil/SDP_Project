<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainee Information</title>
    <style>
        /* General Styling */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 160vh;
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
            padding-bottom: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
        }

        .form-container {
            background-color: white;
            border-radius: 12px;
            padding: 40px;
            width: 450px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .form-container:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
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
        .form-group input[type="number"],
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

        .form-group input:focus,
        .form-group select:focus {
            border-color: #3366ff;
        }

        .form-group select {
            appearance: none;
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
        <a href="trainee_home.php" class="logo">Bravo</a>
    </div>

    <!-- Content Section -->
    <div class="content">
        <div class="form-container">
            <h2>Trainee Information</h2>
            <form action="trainee_home.php" method="POST">
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

                <!-- Assigned Course -->
                <div class="form-group">
                    <input type="text" name="assigned_course" placeholder="Assigned Course" required>
                </div>

                <!-- Training Hospital -->
                <div class="form-group">
                    <input type="text" name="training_hospital" placeholder="Training Hospital" required>
                </div>

                <!-- Completion Status (Dropdown) -->
                <div class="form-group">
                    <select name="completion_status" required>
                        <option value="" disabled selected>Select Completion Status</option>
                        <option value="Completed">Completed</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Not Started">Not Started</option>
                    </select>
                </div>

                <!-- Graduate Year -->
                <div class="form-group">
                    <input type="number" name="graduate_year" placeholder="Graduate Year" required>
                </div>

                <!-- Employment Year -->
                <div class="form-group">
                    <input type="number" name="employment_year" placeholder="Employment Year" required>
                </div>

                <!-- Last Degree -->
                <div class="form-group">
                    <input type="text" name="last_degree" placeholder="Last Degree" required>
                </div>

                <!-- Years of Experience in Laparoscopic Surgery -->
                <div class="form-group">
                    <input type="number" name="laparoscopic_experience" placeholder="Years of Experience in Laparoscopic Surgery" required>
                </div>

                <!-- Years of Experience in Bariatric Surgery -->
                <div class="form-group">
                    <input type="number" name="bariatric_experience" placeholder="Years of Experience in Bariatric Surgery" required>
                </div>

                <!-- Submit Button -->
                <button type="submit"  onclick="window.location.href='trainee_home.php'">Sign up for this Course!</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Contact Us: info@ngo.org | © 2024 Bravo</p>
    </div>

</body>
</html>
