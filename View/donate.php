<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Donation</title>
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

        .donation-container {
            background-color: white;
            border-radius: 12px;
            padding: 40px;
            width: 450px; /* Adjust width for better alignment */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); /* Soft shadow */
            transition: all 0.3s ease;
        }

        .donation-container:hover {
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
        .form-group input[type="email"], 
        .form-group input[type="tel"], 
        .form-group input[type="number"], 
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
        <a href="home.php" class="logo">Bravo</a>
    </div>

    <!-- Content Section -->
    <div class="content">
        <div class="donation-container">
            <h2>Online Donation</h2>
            <form action="receipt.php" method="POST" onsubmit="return validateForm()">
                <!-- Name -->
                <div class="form-group">
                    <input type="text" name="name" placeholder="Name" required>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>

                <!-- Phone Number -->
                <div class="form-group">
                    <input type="tel" name="phone" placeholder="Phone Number" required>
                </div>

                <!-- Course to Donate To (Dropdown) -->
                <div class="form-group">
                    <select name="course" required>
                        <option value="" disabled selected>Select a Course</option>
                        <option value="Course A">Course A</option>
                        <option value="Course B">Course B</option>
                        <option value="Course C">Course C</option>
                        <option value="Course D">Course D</option>
                    </select>
                </div>

                <!-- Donation Amount -->
                <div class="form-group">
                    <input type="number" name="amount" placeholder="Amount (in EGP)" min="1" required>
                </div>

                <!-- Submit Button -->
                <button type="submit">Proceed with Donation</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Contact Us: info@ngo.org | © 2024 Bravo</p>
    </div>

<script>
    function validateForm() {
        var name = document.getElementsByName('name')[0].value;
        var email = document.getElementsByName('email')[0].value;
        var phone = document.getElementsByName('phone')[0].value;
        var course = document.getElementsByName('course')[0].value;
        var amount = document.getElementsByName('amount')[0].value;

        if (!name || !email || !phone || !course || amount <= 0) {
            alert('Please fill in all fields correctly.');
            return false;
        }
        return true;
    }
</script>

</body>
</html>
