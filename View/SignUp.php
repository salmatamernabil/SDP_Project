<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
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
            height: 100vh;
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
        .form-group input[type="password"], 
        .form-group input[type="email"] {
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

        .form-group input:focus {
            border-color: #3366ff;
        }

        /* Account Type Radio Buttons */
        .account-type {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
        }
        .account-type label {
            margin-right: 15px;
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
            margin-bottom: 20px;
        }

        button:hover {
            background-color: #254eda;
            transform: scale(1.02);
        }

        .form-link {
            text-align: center;
            color: #3366ff;
            text-decoration: none;
            display: block;
        }

        .form-link:hover {
            color: #254eda;
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
        <div class="form-container">
            <h2>Sign Up</h2>
            <form id="signupForm" action="signup_process.php" method="POST" onsubmit="return validateForm()">
                <div class="form-group">
                    <input type="text" name="username" id="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" id="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                </div>
                <div class="form-group account-type">
                    <label>Account Type:</label>
                    <label><input type="radio" name="account_type" value="Trainee" required> Trainee</label>
                    <label><input type="radio" name="account_type" value="Doctor" required> Doctor</label>
                </div>
                <button type="submit">Sign Up</button>
            </form>
            <a href="SignIn.php" class="form-link">Already have an account? Sign In</a>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Contact Us: info@ngo.org | © 2024 Bravo</p>
    </div>

    <script>
        function validateForm() {
            // Username validation: Letters and numbers only
            const username = document.getElementById("username").value;
            const usernamePattern = /^[a-zA-Z0-9]+$/;
            if (!usernamePattern.test(username)) {
                alert("Username should contain only letters and numbers.");
                return false;
            }

            // Password validation: At least 8 characters, one uppercase, one lowercase, one number, one special character
            const password = document.getElementById("password").value;
            const passwordPattern = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$/;
            if (!passwordPattern.test(password)) {
                alert("Password must be at least 8 characters long, contain an uppercase letter, a lowercase letter, a number, and a special character.");
                return false;
            }

            // Check account type and redirect accordingly
            const accountType = document.querySelector('input[name="account_type"]:checked').value;
            if (accountType === "Doctor") {
                document.getElementById("signupForm").action = "doctor_home.php";
            } else {
                document.getElementById("signupForm").action = "trainee_home.php";
            }

            return true; // Proceed with form submission
        }
    </script>

</body>
</html>
