<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../Model/bmi_model.php';

class BMIController {
    private $bmiModel;

    public function __construct() {
        $this->bmiModel = new BMIModel();
    }

    public function addBMI($weight, $height) {
        // Validate inputs
        if ($weight <= 0 || $height <= 0) {
            $_SESSION['message'] = "Please enter valid weight and height.";
            $_SESSION['weight'] = $weight;
            $_SESSION['height'] = $height;
            header("Location: ../View/bmi_view.php");
            exit();
        }
    
        // Calculate BMI
        $heightM = $height / 100;
        $bmiValue = $weight / ($heightM * $heightM);
        $bmiValue = round($bmiValue, 2);
    
        // Determine BMI category
        if ($bmiValue < 18.5) {
            $result = 'Underweight';
        } elseif ($bmiValue >= 18.5 && $bmiValue < 25) {
            $result = 'Normal weight';
        } elseif ($bmiValue >= 25 && $bmiValue < 30) {
            $result = 'Overweight';
        } else {
            $result = 'Obese';
        }
    
        // Prepare data for insertion
        $patientId = $this->bmiModel->getLatestPatientId();
        if ($patientId === null) {
            $_SESSION['message'] = "No patient found. Please add a patient first.";
            header("Location: ../View/comorbidity_view.php");
            exit();
        }
    
        // Prepare data with the patient_id included
        $bmiData = [
            'patient_id' => $patientId,
            'weight' => $weight,
            'height' => $height,
            'bmi_value' => $bmiValue,
            'result' => $result
        ];
        
        // Retrieve the admin ID from the session
        $adminId = $_SESSION['admin_id'] ?? null;
        if ($adminId === null) {
            $_SESSION['message'] = "Admin ID is missing. Please sign in as an admin.";
            header("Location: ../View/admin_login_view.php");
            exit();
        }

        // Insert into database with the admin ID
        $success = $this->bmiModel->insertBMI($bmiData, $adminId);
    
        if ($success) {
            $_SESSION['message'] = "BMI data added successfully.";
            $_SESSION['bmi_value'] = $bmiValue;
            $_SESSION['result'] = $result;
            $_SESSION['weight'] = $weight;
            $_SESSION['height'] = $height;
    
            // Redirect to comorbidity_view.php upon success
            header("Location: ../View/comorbidity_view.php");
        } else {
            $_SESSION['message'] = "Failed to add BMI data.";
            header("Location: ../View/bmi_view.php");
        }
        exit();
    }
    
    // New function to handle AJAX requests for BMI calculation
    public function calculateBMI($weight, $height) {
        $heightM = $height / 100;
        $bmiValue = $weight / ($heightM * $heightM);
        $bmiValue = round($bmiValue, 2);

        // Determine BMI category
        if ($bmiValue < 18.5) {
            $result = 'Underweight';
        } elseif ($bmiValue >= 18.5 && $bmiValue < 25) {
            $result = 'Normal weight';
        } elseif ($bmiValue >= 25 && $bmiValue < 30) {
            $result = 'Overweight';
        } else {
            $result = 'Obese';
        }

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
