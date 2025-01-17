<?php
session_start();

// Check if the pending notice exists in the session and display 
?>

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

        /* Content Section */
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
        .form-group input[type="email"], 
        .form-group input[type="date"], 
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

        .form-group input:focus, .form-group select:focus {
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
<script>
    <?php
    // Check if the pending notice exists in the session
    if (isset($_SESSION['pending_notice'])) {
        echo "alert('" . $_SESSION['pending_notice'] . "');";
        echo "window.location.href = 'home_view.php';"; // Redirect to home after the alert
        unset($_SESSION['pending_notice']); // Clear after displaying
    }
    if (isset($_SESSION['error'])) {
        echo "alert('" . $_SESSION['error'] . "');";
        unset($_SESSION['error']); // Clear error message after displaying
    }
    ?>
</script>

<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="home_view.php" class="logo">Bravo</a>
    </div>

    <!-- Content Section -->
    <div class="content">
        <div class="form-container">
            <h2>Sign Up</h2>
            <form id="signupForm" action="../Controller/member_controller.php" method="POST">
            
                <div class="form-group">
                    <input type="text" name="full_name" id="full_name" placeholder="Full Name" required>
                </div>
                <div class="form-group">
                    <input type="date" name="birth_date" id="birth_date" placeholder="Birth Date" required>
                </div>
                <div class="form-group">
                    <select name="gender" id="gender" required>
                        <option value="" disabled selected>Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <input type="text" name="mobile_number" id="mobile_number" placeholder="Mobile Number" required>
                </div>
                <div class="form-group">
                    <input type="text" name="username" id="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" id="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                </div>
                <div class="form-group">
    <label for="specialty">Specialty</label>
    <select name="specialty" id="specialty" required>
        <option value="" disabled selected>Select Specialty</option>
        <option value="Cardiology">Cardiology</option>
        <option value="Dermatology">Dermatology</option>
        <option value="Pediatrics">Pediatrics</option>
        <option value="Neurology">Neurology</option>
        <option value="Orthopedics">Orthopedics</option>
    </select>
</div>


                <div class="form-group account-type">
                    <label>Account Type:</label>
                    <label><input type="radio" name="account_type" value="Trainee" required> Trainee</label>
                    <label><input type="radio" name="account_type" value="Doctor" required> Doctor</label>
                </div>
                <button type="submit">Sign Up</button>
            </form>
            <a href="sign_in_view.php" class="form-link">Already have an account? Sign In</a>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Contact Us: BRAVO@ngo.org | © 2024 Bravo</p>
    </div>
    <script>
    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        alert("Your account registration request has been submitted and will be reviewed shortly.");
        window.location.href = 'home_view.php'; // Redirect to home page after alert
    <?php elseif (isset($_GET['error'])): ?>
        alert("<?php echo htmlspecialchars($_GET['error']); ?>");
    <?php endif; ?>
</script>
   
</body>
</html>
