<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient</title>
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
            padding-bottom: 35px;
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
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
        .form-group input[type="tel"], 
        .form-group select {
            width: 100%;
            padding: 12px;
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

        /* Horizontal Radio Button Group */
        .radio-group {
            display: flex;
            align-items: center;
            gap: 15px;
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
        <a href="admin_home_view.php" class="logo">Bravo</a>
    </div>

    <!-- Content Section -->
    <div class="content">
        <div class="form-container">
            <h2>Add New Patient</h2>
            <form action="../Controller/add_patient_controller.php" method="POST">
                <!-- Visit Date -->
                <div class="form-group">
                    <label for="Visit">Visit-date:</label>
                    <input type="date" id="Visit" name="Visit" required>
                </div>

                <!-- Name -->
                <div class="form-group">
                    <label for="fname">Name:</label>
                    <input type="text" id="fname" name="fname" required>
                </div>

                <!-- Gender -->
                <div class="form-group">
                    <label for="Gender">Gender:</label>
                    <div class="radio-group">
                        <input type="radio" id="Male" name="Gender" value="Male">
                        <label for="Male">Male</label>
                        <input type="radio" id="Female" name="Gender" value="Female">
                        <label for="Female">Female</label>
                    </div>
                </div>

                <!-- Birthdate -->
                <div class="form-group">
                    <label for="birthday">Birth-date:</label>
                    <input type="date" id="birthday" name="birthday" required>
                </div>

                <!-- Mobile Number -->
                <div class="form-group">
                    <label for="phone">Mobile number:</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>

                <!-- Surgery Date -->
                <div class="form-group">
                    <label for="Surgery">Surgery-date:</label>
                    <input type="date" id="Surgery" name="Surgery" required>
                </div>

                <!-- Type of Surgery -->
                <div class="form-group">
                    <label for="Tsurgery">Type of Surgery:</label>
                    <div class="radio-group">
                        <input type="radio" id="Private" name="Tsurgery" value="Private">
                        <label for="Private">Private</label>
                        <input type="radio" id="NPrivate" name="Tsurgery" value="NPrivate">
                        <label for="NPrivate">Not Private</label>
                    </div>
                </div>

                <!-- Hospital Name -->
                <div class="form-group">
                    <label for="Hospital">Hospital Name:</label>
                    <select name="Hospital" id="Hospital" required>
                        <option value="" disabled selected>Select Hospital</option>
                        <option value="Masr-Al-Dawly">Masr-Al-Dawly</option>
                        <option value="ABC">ABC</option>
                        <option value="Al-Zhoor">Al-Zhoor</option>
                        <option value="Al-Asema">Al-Asema</option>
                        <option value="Al-Zohery">Al-Zohery</option>
                        <option value="Al-Salama">Al-Salama</option>
                        <option value="Sant-Treiz">Sant-Treiz</option>
                    </select>
                </div>

                <!-- Add Patient Button -->
                <button type="submit">Add Patient</button>

            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Contact Us: BRAVO@ngo.org | © 2024 Bravo</p>
    </div>

</body>
</html>
