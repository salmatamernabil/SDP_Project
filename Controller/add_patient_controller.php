<?php

require_once '../Design Patterns/Command.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../Model/patient_model.php';
class PatientController {
    private $patientModel;

    public function __construct() {
        $this->patientModel = new PatientModel();
        error_log("[DEBUG] PatientController instantiated.");
    }

    public function addPatient() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("[DEBUG] PatientController: addPatient function called");
    
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
            error_log("[DEBUG] Data received: " . print_r($data, true));
    
            // Retrieve the admin ID from the session
            $adminId = $_SESSION['admin_id'] ?? null;
    
            // Log the value of admin ID for debugging
            error_log("[DEBUG] Retrieved admin ID from session: " . print_r($adminId, true));
    
            // Check if admin ID is set before proceeding
            if (!$adminId) {
                error_log("[ERROR] Admin ID is not set in the session.");
                echo "Error: Unable to add patient. Admin ID is missing.";
                return;
            }
    
            // Create the AddPatientCommand
            $addPatientCommand = new AddPatientCommand($this->patientModel, $data, $adminId);
            error_log("[DEBUG] AddPatientCommand created.");
    
            // Add the command to the CommandInvoker
            $commandInvoker = $_SESSION['commandInvoker']; // Reuse the existing CommandInvoker
            $commandInvoker->addCommand($addPatientCommand);
    
// After executing commands, save the updated CommandInvoker back to the session
$patientId = $commandInvoker->executeCommands()[0]; // Execute the command and get the result
$_SESSION['commandInvoker'] = $commandInvoker; // Save the updated CommandInvoker back to the session
    
            // Log the outcome of command execution
            if ($patientId) {
                error_log("[INFO] Patient added successfully with ID: $patientId.");
                header("Location: ../View/admin_home_view.php");
                exit();
            } else {
                error_log("[ERROR] Failed to add patient through Command Pattern.");
                echo "Failed to add patient. Please try again.";
            }
        }
    }
}

// Instantiate the controller and call the addPatient function if accessed via POST
$patientController = new PatientController();
$patientController->addPatient();
?>
