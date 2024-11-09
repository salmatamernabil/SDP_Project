<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Course</title>
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
            padding-top: 125px; 
            padding-bottom: 25px;/* Adjust this value to create space below navbar */
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-container {
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

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }

        .form-group input[type="text"],
        .form-group input[type="date"],
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
            border-color: #3366ff; /* Blue border on focus */
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
        <a href="admin_home.php" class="logo">Bravo</a>
        <!-- You can add any additional links in the navbar here if needed -->
    </div>

    <!-- Content Section -->
    <div class="content">
        <div class="form-container">
            <h2>Create a New Course</h2>
            <form action="submit_course.php" method="POST">
                <!-- Course Name -->
                <div class="form-group">
                    <label for="courseName">Course Name</label>
                    <input type="text" id="courseName" name="courseName" placeholder="Enter Course Name" required>
                </div>

                <!-- Date of Course -->
                <div class="form-group">
                    <label for="dateOfCourse">Date of Course</label>
                    <input type="date" id="dateOfCourse" name="dateOfCourse" required>
                </div>

                <!-- Place of Course (Dropdown for cities) -->
                <div class="form-group">
                    <label for="placeOfCourse">Place of Course (City)</label>
                    <select id="placeOfCourse" name="placeOfCourse" required>
                        <option value="" disabled selected>Select City</option>
                        <option value="Cairo">Cairo</option>
                        <option value="Alexandria">Alexandria</option>
                        <option value="Giza">Giza</option>
                        <option value="Sharm El-Sheikh">Sharm El-Sheikh</option>
                        <option value="Luxor">Luxor</option>
                        <option value="Aswan">Aswan</option>
                        <!-- Add more cities as needed -->
                    </select>
                </div>

                <!-- Hospital of Hosting -->
                <div class="form-group">
                    <label for="hospitalOfHosting">Hospital of Hosting</label>
                    <select id="hospitalOfHosting" name="hospitalOfHosting" required>
                        <option value="" disabled selected>Select Hospital</option>
                        <option value="1">Hospital A</option>
                        <option value="2">Hospital B</option>
                        <option value="3">Hospital C</option>
                    </select>
                </div>

                <!-- Total Cost of the Course -->
                <div class="form-group">
                    <label for="totalCost">Total Cost (in EGP)</label>
                    <input type="number" id="totalCost" name="totalCost" placeholder="Enter Total Cost" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" onclick="location.href='admin_home.php'">Create Course</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Contact Us: info@ngo.org | © 2024 Bravo</p>
    </div>

</body>
</html>
