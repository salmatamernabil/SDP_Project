<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register your Account</title>
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
            align-items: flex-start;
        }

        /* Form Container */
        .form-container {
            width: 90%;
            max-width: 800px;
            background-color: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
        }

        label {
            font-weight: 600;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }

        select, input[type="text"], input[type="number"], textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background-color: #f9f9f9;
            font-size: 1em;
            color: #333;
            transition: border 0.3s;
            box-sizing: border-box;
        }

        input[type="radio"] {
            margin-right: 10px;
        }

        .radio-group {
    display: flex;
    gap: 15px;
    padding: 8px;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow-x: auto;
    white-space: nowrap;
}

.radio-option {
    display: inline-flex;
    align-items: center; /* Align text vertically with the radio button */
    gap: 5px;
    vertical-align: middle; /* Ensures text is aligned with the button */
}

.radio-option label {
    font-weight: 600;
    margin: 0; /* Remove any default margin that might affect alignment */
    line-height: 1; /* Ensure consistent spacing */
}

.surgery-info h4 {
    margin-top: 20px;
    font-size: 1.1em;
    color: #333;
}

        .Patient-info {
            display: none;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 6px;
            background-color: #f2f2f2;
            margin-bottom: 20px;
        }
        .trainee-info {
            display: none;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 6px;
            background-color: #f2f2f2;
            margin-bottom: 20px;
        }   .doctor-info {
            display: none;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 6px;
            background-color: #f2f2f2;
            margin-bottom: 20px;
        }
        #comments-section {
            display: none;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 6px;
            background-color: #f9f9f9;
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
    </div>

    <!-- Content Section -->
     <section>
    <div class="content">
        <div class="form-container">
        <h2>User Types</h2>

       

    <form action="/complication.php">
        <label for="surg">User Type:</label>
        <select name="User" id="User">
            <option style="display: none;">Choose</option>
            <option value="Patient">Patient</option>
            <option value="Trainee">Trainee</option>
            <option value="Doctor">Doctor/option>
         
        </select>
        <br><br>
   

    <!-- patient Information -->
    <div id="Patient" class="patient-info">
    <div class="form-container">
            <h2>Add New Patient</h2>
            <form action="/action_page.php" method="POST">
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
               <button type="button">Register</button>

            </form>
    </div>
       <!-- Trainee Information -->
       <div id="Trainee" class="trainee-info">
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
                <button type="submit">Register</button>
            </form>
        </div>

        </div>
               <!-- doctor Information -->
       <div id="doctor" class="doctor-info">
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
                <button type="submit">Register</button>
            </form>
        </div>
        </div>

</form>
</section>

<script>
    document.getElementById('Patient').addEventListener('change', function () {
        var UserType = this.value;
        var sections = document.querySelectorAll('.patient-info');
        var commentsSection = document.getElementById('comments-section');

        // Hide all User sections initially
        sections.forEach(function (section) {
            section.style.display = 'none';
        });

        // Always show patient 
        if (UserType === 'patient' || UserType === 'Trainee' || UserType === 'doctor') {
            document.getElementById('patient').style.display = 'block';
        }

        // Show specific surgery section for SASI
        if (UserType === 'Trainee') {
            document.getElementById('Trainee').style.display = 'block';
        }
        
        // Show specific surgery section for RNY
        if (UserType === 'doctor') {
            document.getElementById('doctor').style.display = 'block';
        }
        
        // Show comments section for any User type selected
        if (UserType !== 'Choose') {
            commentsSection.style.display = 'block';
        } else {
            commentsSection.style.display = 'none';
        }
    });

    document.getElementById('Others').addEventListener('change', function () {
        var otherSurgeryText = document.getElementById('other-User-text');
        if (this.checked) {
            otherSurgeryText.style.display = 'block';
        } else {
            otherSurgeryText.style.display = 'none';
        }
    });
</script>

</body>
</html>
