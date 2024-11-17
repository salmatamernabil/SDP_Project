<?php
session_start(); // Ensure the session is started to access session variables
require_once '../Model/patient_model.php';

class PatientController {
    private $patientModel;

    public function __construct() {
        $this->patientModel = new PatientModel();
    }

    // Function to add a new patient
    public function addPatient() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Log that addPatient function is called
            error_log("PatientController: addPatient function called");

            // Get data from POST request
            $data = [
                'Visit' => $_POST['Visit'] ?? '',
                'FullName' => $_POST['fname'] ?? '',
                'Gender' => $_POST['Gender'] ?? '',
                'BirthDate' => $_POST['birthday'] ?? '',
                'MobileNumber' => $_POST['phone'] ?? '',
                'SurgeryDate' => $_POST['Surgery'] ?? '',
                'TypeOfSurgery' => $_POST['Tsurgery'] ?? '',
                'HospitalName' => $_POST['Hospital'] ?? ''
            ];

            // Log received data
            error_log("Data received: " . print_r($data, true));

            // Retrieve the admin ID from the session
            $adminId = $_SESSION['admin_id'] ?? null;

            // Log the value of admin ID for debugging
            error_log("Retrieved admin ID from session: " . print_r($adminId, true));

            // Check if admin ID is set before proceeding
            if (!$adminId) {
                error_log("Error: Admin ID is not set in the session.");
                echo "Error: Unable to add patient. Admin ID is missing.";
                return;
            }

            // Pass data and admin ID to the model and log the result
            if ($this->patientModel->addPatient($data, $adminId)) {
                error_log("Patient added successfully.");
                header("Location: ../View/bmi_view.php");
                exit();
            } else {
                error_log("Failed to add patient.");
                echo "Failed to add patient. Please try again.";
            }
        }
    }
}

// Instantiate the controller and call the addPatient function if accessed via POST
$patientController = new PatientController();
$patientController->addPatient();
