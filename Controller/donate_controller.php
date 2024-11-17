<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Include necessary files
require_once '../Design Patterns/Strategy.php';
require_once '../Model/donate_model.php'; // Ensure the model is included

class PaymentController {
    private $donationContext;
    private $donateModel; // Add the model as a property

    public function __construct() {
        $this->donationContext = new DonationContext();
        $this->donateModel = new DonateModel(); // Initialize the model
    }

    // Set the instance type and strategy based on payment type
    public function setInstanceType($paymentType) {
        error_log("setInstanceType called with paymentType: " . $paymentType); // Debug log
        switch ($paymentType) {
            case 'cash':
                $this->donationContext->setStrategy(new CashDonation());
                $_SESSION['paymentInstanceType'] = 'cash';
                break;
            case 'visa':
                $this->donationContext->setStrategy(new VisaDonation());
                $_SESSION['paymentInstanceType'] = 'visa';
                break;
            case 'fawry':
                $this->donationContext->setStrategy(new FawryDonation());
                $_SESSION['paymentInstanceType'] = 'fawry';
                break;
            default:
                echo json_encode(['status' => 'error', 'message' => 'Invalid payment type selected']);
                return;
        }

        // Store the strategy information to be used by the view
        $_SESSION['strategyInfo'] = $this->donationContext->getStrategyInfo();
        error_log("Strategy set and session updated with strategyInfo: " . $_SESSION['strategyInfo']); // Debug log
    }

    // Process the donation based on the selected amount and strategy
    public function processDonation($amount) {
        error_log("processDonation called with amount: " . $amount); // Debug log
        $this->donationContext->executeDonation($amount);
    }

    // Validate input data
    public function validateInput($formData) {
        $errors = [];

        // Check name
        if (empty($formData['name'])) {
            $errors['name'] = "Name is required.";
        }

        // Validate email
        if (empty($formData['email']) || !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "A valid email is required.";
        }

        // Validate phone number (simple regex example for digits only)
        if (empty($formData['phone']) || !preg_match('/^\d{10,15}$/', $formData['phone'])) {
            $errors['phone'] = "A valid phone number is required (10-15 digits).";
        }

        // Check if course is selected
        if (empty($formData['course'])) {
            $errors['course'] = "Please select a course.";
        }

        // Validate amount
        if (empty($formData['amount']) || !is_numeric($formData['amount']) || $formData['amount'] <= 0) {
            $errors['amount'] = "Please enter a valid donation amount.";
        }

        // Validate payment type
        if (empty($formData['paymentType']) || !in_array($formData['paymentType'], ['cash', 'visa', 'fawry'])) {
            $errors['paymentType'] = "Please select a valid payment method.";
        }

        return $errors;
    }

    // Handle AJAX requests to dynamically update the instance type
    public function handleAjaxRequest() {
        if (isset($_POST['paymentType']) && isset($_POST['ajax'])) {
            error_log("AJAX request received with paymentType: " . $_POST['paymentType']); // Debug log
            $this->setInstanceType($_POST['paymentType']);

            echo json_encode([
                'status' => 'success',
                'paymentType' => $_POST['paymentType'],
                'strategyInfo' => $_SESSION['strategyInfo']
            ]);
            exit;
        }
    }

    // Handle full form submission
    public function handleFormSubmission() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['finalizeDonation'])) {
                error_log("Full form submission received"); // Debug log
                $formData = $_POST;

                // Validate the input data
                $errors = $this->validateInput($formData);

                if (!empty($errors)) {
                    // If there are validation errors, store them in the session and redirect back to the form
                    $_SESSION['errors'] = $errors;
                    $_SESSION['formData'] = $formData; // Retain the form data to repopulate on error
                    header("Location: ../View/donate_view.php");
                    exit;
                }

                // Set instance type and process the donation if validation passes
                $this->setInstanceType($formData['paymentType']);
                $this->processDonation($formData['amount']);

                // Prepare data for the model
                $donationData = [
                    'name'         => $formData['name'],
                    'email'        => $formData['email'],
                    'phone'        => $formData['phone'],
                    'course'       => $formData['course'],
                    'amount'       => $formData['amount'],
                    'payment_type' => $formData['paymentType'],
                    'date'         => date("Y-m-d"),
                ];

                // Send data to the model to save in the database
                try {
                    $this->donateModel->createDonation($donationData);
                    error_log("Donation data saved successfully.");
                } catch (Exception $e) {
                    error_log("Error saving donation: " . $e->getMessage());
                    $_SESSION['errors'] = ['database' => 'There was an error processing your donation. Please try again later.'];
                    $_SESSION['formData'] = $formData;
                    header("Location: ../View/donate_view.php");
                    exit;
                }

                // Store form data in session for the receipt view
                $_SESSION['receiptData'] = [
                    'name'   => $formData['name'],
                    'email'  => $formData['email'],
                    'phone'  => $formData['phone'],
                    'course' => $formData['course'],
                    'amount' => $formData['amount'],
                    'date'   => date("Y-m-d"),
                ];

                // Clear form data from session after successful processing
                unset($_SESSION['formData']);

                // Redirect to the receipt page
                header("Location: ../View/receipt_view.php");
                exit;
            }
        }
    }
}

// Instantiate the controller
$controller = new PaymentController();

// Handle AJAX requests
$controller->handleAjaxRequest();

// Handle form submission
$controller->handleFormSubmission();
?>
