<?php
session_start();

// Retrieve receipt data from session
$receiptData = $_SESSION['formData'] ?? [
    'name' => 'N/A',
    'email' => 'N/A',
    'phone' => 'N/A',
    'course' => 'N/A',
    'amount' => 'N/A',
];

// Optional: Clear receipt data after displaying to prevent reuse
//unset($_SESSION['receiptData']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Receipt</title>
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
            padding-bottom: 25px;
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .receipt-container {
            background-color: white;
            border-radius: 12px;
            padding: 40px;
            width: 450px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .receipt-details {
            text-align: left;
            margin: 20px 0;
        }

        .receipt-details p {
            font-size: 1em;
            color: #333;
            margin: 8px 0;
        }

        /* Button Styling */
        .button-back {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3366ff;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1em;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .button-back:hover {
            background-color: #254eda;
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
        <a href="home_view.php" class="logo">Bravo</a>
    </div>

<!-- Content Section -->
<div class="content">
    <div class="receipt-container">
        <h2>Donation Receipt</h2>
        <p>Thank you for your generous donation!</p>

        <div class="receipt-details">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($receiptData['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($receiptData['email']); ?></p>
            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($receiptData['phone']); ?></p>
            <p><strong>Course Donated To:</strong> <?php echo htmlspecialchars($receiptData['course']); ?></p>
            <p><strong>Amount:</strong> EGP <?php echo htmlspecialchars($receiptData['amount']); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars(date("Y-m-d")); ?></p>
        </div>

        <!-- Button to Download PDF -->
        <a href="../Helper Files/generate_receipt_pdf.php" class="button-back">Download PDF</a>
        <a href="../View/home_view.php" class="button-back">Back to Home</a>
    </div>
</div>


    <!-- Footer -->
    <div class="footer">
        <p>Contact Us: BRAVO@ngo.org | © 2024 Bravo</p>
    </div>

</body>
</html>
