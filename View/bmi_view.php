<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Patient BMI</title>
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
        .form-group input[type="number"] {
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
        <a href="admin_home.php" class="logo">Bravo</a>
    </div>

    <!-- Content Section -->
    <div class="content">
        <div class="form-container">
            <h2>Add Patient BMI</h2>

            <!-- Display message if exists -->
            <?php
            session_start();
            if (isset($_SESSION['message'])) {
                echo "<p style='color: red; text-align: center;'>{$_SESSION['message']}</p>";
                unset($_SESSION['message']);
            }
            ?>

            <form action="../Controller/bmi_controller.php" method="POST">
                <div class="form-group">
                    <input type="number" name="weight" id="weight" placeholder="Weight (kg)" step="0.1" oninput="updateBMI()" required>
                </div>
                <div class="form-group">
                    <input type="number" name="height" id="height" placeholder="Height (cm)" step="0.1" oninput="updateBMI()" required>
                </div>
                <div class="form-group">
                    <input type="text" name="bmi" id="bmi" placeholder="BMI" readonly>
                </div>
                <div class="form-group">
                    <input type="text" name="result" id="result" placeholder="Category" readonly>
                </div>
                <button type="submit">Add Patient BMI</button> 
            </form>

        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Contact Us: BRAVO@ngo.org | © 2024 Bravo</p>
    </div>

    <script>
        function updateBMI() {
    const weight = document.getElementById("weight").value;
    const height = document.getElementById("height").value;

    if (weight && height) {
        console.log(`Sending request with weight: ${weight}, height: ${height}`);
        const xhr = new XMLHttpRequest();
        xhr.open("GET", `../Controller/bmi_controller.php?action=calculate_bmi&weight=${weight}&height=${height}`, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    console.log("Received response:", response);
                    document.getElementById("bmi").value = response.bmi;
                    document.getElementById("result").value = response.category;
                } catch (error) {
                    console.error("Error parsing JSON response:", error);
                }
            } else if (xhr.readyState === 4) {
                console.error("Request failed with status:", xhr.status);
            }
        };
        xhr.send();
    } else {
        document.getElementById("bmi").value = '';
        document.getElementById("result").value = '';
    }
}

    </script>
</body>
</html>
