<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complication</title>
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

        /* Content Section */
        .content {
            padding-top: 125px;
            padding-bottom: 35px;
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Form Styling */
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

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .form-section {
            margin-bottom: 20px;
        }

        .form-section label {
            font-weight: 600;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }

        .form-section input[type="text"],
        .form-section input[type="number"],
        .form-section textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1em;
            background-color: #f9f9f9;
            color: #333;
            transition: border 0.3s;
            box-sizing: border-box;
        }

        .form-section input:focus,
        .form-section textarea:focus {
            border-color: #3366ff;
        }

        .form-section .radio-group {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .radio-group label, 
        .radio-group input[type="radio"] {
            vertical-align: middle;
            margin: 0; /* Remove extra margin if any */
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

        /* Toggle text fields */
        .other-text, .days-text {
            display: none;
            margin-top: 10px;
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
            <h1>Complication</h1>
            <form action="../Controller/complication_controller.php" method="POST">
            <input type="hidden" name="patient_id" value="<?php echo htmlspecialchars($_POST['patient_id'] ?? ''); ?>">
                <!-- Intraoperative Section -->
                <div class="form-section">
                    <label>Intraoperative:</label>
                    <div class="radio-group">
                        <input type="radio" id="none" name="intraoperative" value="none">
                        <label for="none">None</label>
                        <input type="radio" id="bleeding" name="intraoperative" value="bleeding">
                        <label for="bleeding">Bleeding</label>
                        <input type="radio" id="other" name="intraoperative" value="other">
                        <label for="other">Other</label>
                    </div>
                    <div class="other-text">
                        <label for="otherText">Please specify:</label>
                        <input type="text" id="otherText" name="otherText">
                    </div>
                </div>

                <!-- Postoperative Section -->
                <div class="form-section">
                    <label for="postoperative">Postoperative:</label>
                    <textarea id="postoperative" name="postoperative" rows="4" placeholder="Enter comments..."></textarea>
                </div>

                <!-- Discharge Section -->
                <div class="form-section">
                    <label>Discharge:</label>
                    <div class="radio-group">
                        <input type="radio" id="alive" name="discharge" value="alive">
                        <label for="alive">Alive</label>
                        <input type="radio" id="dead" name="discharge" value="dead">
                        <label for="dead">Dead</label>
                        <input type="radio" id="after_days" name="discharge" value="after_days">
                        <label for="after_days">After N days</label>
                    </div>
                    <div class="days-text">
                        <label for="days">Number of days:</label>
                        <input type="number" id="days" name="days" placeholder="Enter number of days...">
                    </div>
                </div>

                <!-- Submit Button -->
               
                <button type="submit">Submit</button>

            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Contact Us: info@ngo.org | © 2024 Bravo</p>
    </div>

    <script>
        document.getElementById('other').addEventListener('change', function() {
            document.querySelector('.other-text').style.display = 'block';
        });

        document.getElementById('none').addEventListener('change', function() {
            document.querySelector('.other-text').style.display = 'none';
        });

        document.getElementById('bleeding').addEventListener('change', function() {
            document.querySelector('.other-text').style.display = 'none';
        });

        document.getElementById('after_days').addEventListener('change', function() {
            document.querySelector('.days-text').style.display = 'block';
        });

        document.getElementById('alive').addEventListener('change', function() {
            document.querySelector('.days-text').style.display = 'none';
        });

        document.getElementById('dead').addEventListener('change', function() {
            document.querySelector('.days-text').style.display = 'none';
        });
    </script>

</body>
</html>