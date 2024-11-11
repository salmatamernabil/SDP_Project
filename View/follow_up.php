<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Follow Up</title>
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
            padding-top: 100px;
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Container for Follow-Up Content */
        .container {
            width: 80%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .container h1 {
            color: #3366ff;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Search Box Styling */
        .search-box {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
        }
        .search-box label {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        .search-box input {
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
            width: 100%;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        .search-box input:focus {
            border-color: #3366ff;
        }

        /* Patient Records Styling */
        .patient-records {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
        }
        .record {
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
        .record:last-child {
            border-bottom: none;
        }
        .record p {
            margin: 5px 0;
            font-size: 1em;
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
        <div class="container">
            <h1>Follow Up</h1>
            
            <div class="search-box">
                <label for="search">Search Patient:</label>
                <input type="text" id="search" name="search" placeholder="Enter patient name or birth date">
            </div>
            
            <div class="patient-records" id="patient-records">
                <div class="record">
                    <p><strong>Name:</strong> John Doe</p>
                    <p><strong>Birth Date:</strong> 1985-04-12</p>
                </div>
                <div class="record">
                    <p><strong>Name:</strong> Jane Smith</p>
                    <p><strong>Birth Date:</strong> 1990-05-23</p>
                </div>
                <div class="record">
                    <p><strong>Name:</strong> Mohamed John</p>
                    <p><strong>Birth Date:</strong> 1990-05-23</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Contact Us: info@ngo.org | © 2024 Bravo</p>
    </div>

    <script>
        document.getElementById('search').addEventListener('input', function() {
            var searchValue = this.value.toLowerCase();
            var records = document.querySelectorAll('.record');
            
            records.forEach(function(record) {
                var patientName = record.querySelector('p:nth-child(1)').textContent.toLowerCase();
                var birthDate = record.querySelector('p:nth-child(2)').textContent.toLowerCase();
                
                // Check if patientName or birthDate includes the searchValue
                if (patientName.includes(searchValue) || birthDate.includes(searchValue)) {
                    record.style.display = 'block';
                } else {
                    record.style.display = 'none';
                }
            });
        });
    </script>

</body>
</html>
