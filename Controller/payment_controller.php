<?php
session_start();

require_once '../Model/donate_model.php';
require_once '../Model/course_model.php';
require_once '../Design Patterns/State.php';

class PaymentController {
    private $donateModel;
    private $donationStateManager;

    public function __construct() {
        $this->donateModel = new DonateModel();
    
        // Ensure the DonationStateManager class is loaded before unserializing
        if (isset($_SESSION['donationStateManager'])) {
            $this->donationStateManager =unserialize( $_SESSION['donationStateManager']);
            error_log("[PaymentController] Controller initialized. Current state: " . get_class($this->donationStateManager->getState()));
        } else {
            error_log("[PaymentController] DonationStateManager not found in session.");
            // Handle the case where the object is not found (e.g., redirect or initialize a new object)
            $this->donationStateManager = new DonationStateManager();
        }
    }

    public function handleFormSubmission() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['finalizeDonation'])) {
                $formData = $_POST;
                error_log("[PaymentController] Form data received: " . print_r($formData, true));

                // Set the payment instance type based on the selected payment method
                $this->setInstanceType($formData['paymentType']);

                // Check if the state is PendingState
                if (get_class($this->donationStateManager->getState()) === 'PendingState') {
                    // Transition to ProcessingState
                    $this->donationStateManager->proceed();
                    error_log("[PaymentController] State transitioned to ProcessingState.");

                    // Simulate payment processing
                    $paymentSuccessful = $this->simulatePaymentProcessing($formData['paymentType']);
                    error_log("[PaymentController] Payment processing result: " . ($paymentSuccessful ? "Success" : "Failure"));

                    if ($paymentSuccessful) {
                        // Transition to CompletedState
                        $this->donationStateManager->complete();
                        error_log("[PaymentController] State transitioned to CompletedState.");

                        // Save donation to database
                        $this->saveDonation($formData);

                        // Redirect to receipt page
                        header("Location: ../View/receipt_view.php");
                        exit;
                    } else {
                        $this->donationStateManager->fail();
                        $_SESSION['errors'] = ['payment' => 'Payment failed. Please try again.'];
                        header("Location: ../View/payment_view.php");
                        exit;
                    }
                } else {
                    error_log("[PaymentController] Invalid state transition. Current state: " . get_class($this->donationStateManager->getState()));
                    $_SESSION['errors'] = ['state' => 'Invalid state transition.'];
                    header("Location: ../View/payment_view.php");
                    exit;
                }
            } elseif (isset($_POST['ajax']) && $_POST['ajax'] === 'true') {
                // Handle AJAX request to update payment type
                $paymentType = $_POST['paymentType'];
                $this->setInstanceType($paymentType);

                // Return JSON response
                echo json_encode(['status' => 'success']);
                exit;
            }
        }
    }

    private function setInstanceType($paymentType) {
        switch ($paymentType) {
            case 'cash':
                $_SESSION['paymentInstanceType'] = 'cash';
                error_log("[PaymentController] Payment type set to: cash");
                break;
            case 'visa':
                $_SESSION['paymentInstanceType'] = 'visa';
                error_log("[PaymentController] Payment type set to: visa");
                break;
            case 'fawry':
                $_SESSION['paymentInstanceType'] = 'fawry';
                error_log("[PaymentController] Payment type set to: fawry");
                break;
            default:
                error_log("[PaymentController] Invalid payment type selected: " . $paymentType);
                $_SESSION['errors'] = ['paymentType' => 'Invalid payment type selected.'];
                header("Location: ../View/payment_view.php");
                exit;
        }
    }

    private function simulatePaymentProcessing($paymentType) {
        return rand(1, 1) === 1; // Simulate payment success or failure
    }

    private function saveDonation($formData) {
        $courseModel = new CourseModel();
        $course = $courseModel->getCourseById($formData['course']);

        if (!$course) {
            error_log("[PaymentController] Invalid course selected: " . $formData['course']);
            $_SESSION['errors'] = ['course' => 'Invalid course selected.'];
            header("Location: ../View/payment_view.php");
            exit;
        }

        $donationData = [
            'name'         => $formData['name'],
            'email'        => $formData['email'],
            'phone'        => $formData['phone'],
            'course_id'    => $course['course_id'],
            'course_name'  => $course['course_name'],
            'amount'       => $formData['amount'],
            'payment_type' => $formData['paymentType'],
            'date'         => date("Y-m-d"),
        ];

        try {
            $this->donateModel->createDonation($donationData);
            error_log("[PaymentController] Donation data saved successfully.");
        } catch (Exception $e) {
            error_log("[PaymentController] Error saving donation: " . $e->getMessage());
            $_SESSION['errors'] = ['database' => 'An error occurred while saving the donation.'];
            header("Location: ../View/payment_view.php");
            exit;
        }
    }
}

$controller = new PaymentController();
$controller->handleFormSubmission();
