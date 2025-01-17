<?php
session_start();
require_once '../Model/donate_supplies_model.php';


class DonateSuppliesController {
    private $model;

    public function __construct() {
        $this->model = new DonateSuppliesModel();
    }

    /**
     * Handle the donation process.
     *
     * @param array $data The donation data.
     * @return bool True if successful, false otherwise.
     */
    public function handleDonation($data) {
        // Insert the donation into the database
        $success = $this->model->createDonation($data);

        // Check the delivery status (optional, can be done later)
        if ($success) {
            $this->model->checkDeliveryStatus($data['itemId']);
        }

        return $success;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $staplerCount = $_POST['staplerCount'] ?? '';
    $brand = $_POST['brand'] ?? '';
    $serialNumber = $_POST['serialNumber'] ?? '';

    // Validate form data
    $errors = [];
    if (empty($name)) $errors[] = "Name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (empty($phone)) $errors[] = "Phone number is required.";
    if (empty($staplerCount) || $staplerCount < 1) $errors[] = "Number of staplers must be at least 1.";
    if (empty($brand)) $errors[] = "Brand is required.";
    if (empty($serialNumber)) $errors[] = "Serial number is required.";

    if (empty($errors)) {
        // Create controller instance
        $controller = new DonateSuppliesController();
        $data = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'staplerCount' => $staplerCount,
            'brand' => $brand,
            'serialNumber' => $serialNumber,
            'date' => date("Y-m-d H:i:s"), // Current timestamp
        ];
    
        // Store form data in the session
        $_SESSION['formData'] = $data;
        error_log("[DEBUG] Form data stored in session: " . print_r($data, true));
    
    // Store form data in the session
    
    error_log("[DEBUG] Form data stored in session: " . print_r($data, true));
        // Handle the donation
        $success = $controller->handleDonation($data);

        if ($success) {
            // Redirect to success page
            $_SESSION['formData'] = $data;
            header("Location: ../View/donation_supplies_success_view.php");
            
            exit();
        } else {
            // Handle failure
            $_SESSION['error'] = "Failed to process donation.";
            header("Location: ../View/donate_supplies_view.php");
            exit();
        }
    } else {
        // Store errors and form data in session
        $_SESSION['errors'] = $errors;
        $_SESSION['formData'] = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'staplerCount' => $staplerCount,
            'brand' => $brand,
            'serialNumber' => $serialNumber,
        ];
        // Redirect back to the form
        header("Location: ../View/donate_supplies_view.php");
        exit();
    }
}
?>
