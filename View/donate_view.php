<?php
session_start();

// Retrieve the payment instance type and form data from the session, with defaults if not set
$paymentInstanceType = $_SESSION['paymentInstanceType'] ?? null;
$formData = $_SESSION['formData'] ?? [
    'name' => '',
    'email' => '',
    'phone' => '',
    'course' => '',
    'amount' => '',
];
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']); // Clear errors after displaying
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Donation</title>
    <style>
        /* [Your existing CSS styles here] */
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

        /* Payment Method Styling */
        .payment-option {
            margin: 15px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .payment-option label {
            font-size: 1.1em;
            color: #555;
            cursor: pointer;
        }
        input[type="radio"] {
            accent-color: #3366ff;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        /* Payment Information Sections */
        .payment-info {
            margin-top: 20px;
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
        <a href="home_view.php" class="logo">Bravo</a>
    </div>

    <!-- Content Section -->
    <div class="content">
        <div class="donation-container">
            <h2>Online Donation</h2>
            <form action="../Controller/donate_controller.php" method="POST">
                <div class="form-group">
                    <input type="text" name="name" placeholder="Name" value="<?php echo htmlspecialchars($formData['name']); ?>" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($formData['email']); ?>" required>
                </div>
                <div class="form-group">
                    <input type="tel" name="phone" placeholder="Phone Number" value="<?php echo htmlspecialchars($formData['phone']); ?>" required>
                </div>
                <div class="form-group">
                    <select name="course" required>
                        <option value="" disabled <?php echo ($formData['course'] === '') ? 'selected' : ''; ?>>Select a Course</option>
                        <option value="Course A" <?php echo ($formData['course'] === 'Course A') ? 'selected' : ''; ?>>Course A</option>
                        <option value="Course B" <?php echo ($formData['course'] === 'Course B') ? 'selected' : ''; ?>>Course B</option>
                        <option value="Course C" <?php echo ($formData['course'] === 'Course C') ? 'selected' : ''; ?>>Course C</option>
                    </select>
                </div>
                <div class="form-group">
                    <input type="number" name="amount" placeholder="Amount (in EGP)" min="1" value="<?php echo htmlspecialchars($formData['amount']); ?>" required>
                </div>
                <!-- Payment Type Selection -->
                <h2>Select Payment Method</h2>
                <div class="payment-option">
                    <input type="radio" name="paymentType" value="cash" id="cash" required <?php echo ($paymentInstanceType === 'cash') ? 'checked' : ''; ?>>
                    <label for="cash">Cash</label>
                </div>
                <div class="payment-option">
                    <input type="radio" name="paymentType" value="visa" id="visa" required <?php echo ($paymentInstanceType === 'visa') ? 'checked' : ''; ?>>
                    <label for="visa">Visa</label>
                </div>
                <div class="payment-option">
                    <input type="radio" name="paymentType" value="fawry" id="fawry" required <?php echo ($paymentInstanceType === 'fawry') ? 'checked' : ''; ?>>
                    <label for="fawry">Fawry</label>
                </div>

                <!-- Payment Information Sections -->
                <div id="payment-info-cash" class="payment-info" style="display: <?php echo ($paymentInstanceType === 'cash') ? 'block' : 'none'; ?>;">
                    <h2>Complete Your Donation with Cash</h2>
                    <p>Please visit our office or contact our representative to complete your donation in cash.</p>
                </div>

                <div id="payment-info-visa" class="payment-info" style="display: <?php echo ($paymentInstanceType === 'visa') ? 'block' : 'none'; ?>;">
                    <h2>Complete Your Donation with Visa</h2>
                    <div class="form-group">
                        <label for="card-name">Name on Card</label>
                        <input type="text" id="card-name" name="card_name" placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label for="card-number">Card Number</label>
                        <input type="text" id="card-number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="16">
                    </div>
                    <div class="form-group">
                        <label for="expiry-date">Expiration Date</label>
                        <input type="text" id="expiry-date" name="expiry_date" placeholder="MM/YY" maxlength="5">
                    </div>
                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="number" id="cvv" name="cvv" placeholder="123" maxlength="3">
                    </div>
                </div>

                <div id="payment-info-fawry" class="payment-info" style="display: <?php echo ($paymentInstanceType === 'fawry') ? 'block' : 'none'; ?>;">
                    <h2>Complete Your Donation with Fawry</h2>
                    <p>Use the following Fawry code at any Fawry outlet or online to complete your donation.</p>
                    <p><strong>Fawry Code: 12345678</strong></p>
                </div>

                <button type="submit" name="finalizeDonation">Proceed with Donation</button>
            </form>
        </div>
    </div>
    <div class="footer">
        <p>Contact Us: BRAVO@ngo.org | © 2024 Bravo</p>
    </div>

    <!-- JavaScript to handle payment type change with AJAX -->
    <script>
    document.querySelectorAll('input[name="paymentType"]').forEach((radio) => {
        radio.addEventListener('change', function() {
            const paymentType = this.value;

            // Send AJAX request to update payment type in the session
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../Controller/donate_controller.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        showPaymentInfo(paymentType);
                    }
                }
            };
            xhr.send("paymentType=" + paymentType + "&ajax=true");
        });
    });

    function showPaymentInfo(paymentType) {
        // Hide all payment info sections
        document.querySelectorAll('.payment-info').forEach((section) => {
            section.style.display = 'none';
        });
        // Show the selected payment info
        document.getElementById('payment-info-' + paymentType).style.display = 'block';
    }
    </script>
</body>
</html>
