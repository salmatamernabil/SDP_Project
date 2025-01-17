<?php
session_start();

require_once '../Model/donate_model.php';
require_once '../Model/course_model.php';
require_once '../Design Patterns/State.php';

class DonationController {
    private $donateModel;
    private $donationStateManager;

    public function __construct() {
        $this->donateModel = new DonateModel();
        $this->donationStateManager = new DonationStateManager();
        error_log("[DonationController] Controller initialized. Current state: " . get_class($this->donationStateManager->getState()));
    }

    public function handleFormSubmission() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proceedToPayment'])) {
            $formData = $_POST;
            error_log("[DonationController] Form data received: " . print_r($formData, true));
    
            // Validate the form data
            $errors = $this->validateInput($formData);
    
            if (!empty($errors)) {
                error_log("[DonationController] Validation errors: " . print_r($errors, true));
                $_SESSION['errors'] = $errors;
                $_SESSION['formData'] = $formData;
                header("Location: ../View/donate_view.php");
                exit;
            }
    
            // Check if the state is InitialState
            if (get_class($this->donationStateManager->getState()) === 'InitialState') {
                // Transition to PendingState
                $this->donationStateManager->proceed();
                error_log("[DonationController] State transitioned to PendingState.");
    
                // Store form data in session for the payment view
                $_SESSION['formData'] = $formData;
                error_log("[DonationController] Form data stored in session.");
    
                // Store the DonationStateManager object in the session
                $_SESSION['donationStateManager'] = serialize($this->donationStateManager);
                error_log("[DonationController] DonationStateManager stored in session.");
    
                // Transition to the payment view
                header("Location: ../View/payment_view.php");
                exit;
            } else {
                error_log("[DonationController] Invalid state transition. Current state: " . get_class($this->donationStateManager->getState()));
                $_SESSION['errors'] = ['state' => 'Invalid state transition.'];
                header("Location: ../View/donate_view.php");
                exit;
            }
        }
    }
    private function validateInput($formData) {
        $errors = [];

        if (empty($formData['name'])) {
            $errors['name'] = "Name is required.";
        }
        if (empty($formData['email']) || !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "A valid email is required.";
        }
        if (empty($formData['phone']) || !preg_match('/^\d{10,15}$/', $formData['phone'])) {
            $errors['phone'] = "A valid phone number is required (10-15 digits).";
        }
        if (empty($formData['course'])) {
            $errors['course'] = "Please select a course.";
        }
        if (empty($formData['amount']) || !is_numeric($formData['amount']) || $formData['amount'] <= 0) {
            $errors['amount'] = "Please enter a valid donation amount.";
        }

        return $errors;
    }
}

$controller = new DonationController();
$controller->handleFormSubmission();
?>
