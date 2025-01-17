<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../Model/admin_model.php'; // Include AdminModel to get the current admin instance
require_once '../Design Patterns/Decorater.php'; // Include AdminModel to get the current admin instance 

class BMIController {
    private $adminComponent; // Use AdminComponent instead of BMIModel directly

    public function __construct() {
        // Get the current admin instance from the session
        $adminModel = new AdminModel();
        $this->adminComponent = $adminModel->getCurrentAdminInstance(); // Get the appropriate admin instance (BaseAdmin, SuperAdmin, or ChiefAdmin)
    }

    public function addBMI($weight, $height) {
        // Retrieve patient_id from the POST request
        $patientId = $_POST['patient_id'] ?? null;

        if (!$patientId) {
            $_SESSION['message'] = "Patient ID is missing. Please select a patient.";
            header("Location: ../View/bmi_view.php");
            exit();
        }

        // Calculate BMI
        $heightM = $height / 100;
        $bmiValue = $weight / ($heightM * $heightM);
        $bmiValue = round($bmiValue, 2);

        // Determine BMI category
        $result = $this->determineBMICategory($bmiValue);

        // Prepare BMI data
        $bmiData = [
            'patient_id' => $patientId,
            'weight' => $weight,
            'height' => $height,
            'bmi_value' => $bmiValue,
            'result' => $result
        ];

        // Use the AdminComponent's addBMI method
        $adminId = $_SESSION['admin_id'] ?? null;
        if ($adminId === null) {
            $_SESSION['message'] = "Admin ID is missing. Please sign in.";
            header("Location: ../View/admin_login_view.php");
            exit();
        }

        $success = $this->adminComponent->addBMI($bmiData, $adminId);

        if ($success) {
            $_SESSION['message'] = "BMI data added successfully.";
            header("Location: ../View/add_patient_details_view.php");
        } else {
            $_SESSION['message'] = "Failed to add BMI data.";
            header("Location: ../View/bmi_view.php");
        }
        exit();
    }

    // Function to determine BMI category
    private function determineBMICategory($bmiValue) {
        if ($bmiValue < 18.5) {
            return 'Underweight';
        } elseif ($bmiValue >= 18.5 && $bmiValue < 25) {
            return 'Normal weight';
        } elseif ($bmiValue >= 25 && $bmiValue < 30) {
            return 'Overweight';
        } else {
            return 'Obese';
        }
    }

    // New function to handle AJAX requests for BMI calculation
    public function calculateBMI($weight, $height) {
        $heightM = $height / 100;
        $bmiValue = $weight / ($heightM * $heightM);
        $bmiValue = round($bmiValue, 2);

        // Determine BMI category
        $result = $this->determineBMICategory($bmiValue);

        // Return response as JSON
        echo json_encode(['bmi' => $bmiValue, 'category' => $result]);
        exit();
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $weight = isset($_POST['weight']) ? floatval($_POST['weight']) : 0;
    $height = isset($_POST['height']) ? floatval($_POST['height']) : 0;

    $bmiController = new BMIController();
    $bmiController->addBMI($weight, $height);
}

// Handle AJAX request
if (isset($_GET['action']) && $_GET['action'] === 'calculate_bmi') {
    $weight = isset($_GET['weight']) ? floatval($_GET['weight']) : 0;
    $height = isset($_GET['height']) ? floatval($_GET['height']) : 0;

    error_log("Received AJAX request to calculate BMI with weight: $weight, height: $height");

    $bmiController = new BMIController();
    $bmiController->calculateBMI($weight, $height);
}
?>