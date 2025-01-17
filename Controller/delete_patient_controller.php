<?php
// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



// Include the model
require_once '../Model/patient_model.php';
require_once '../Model/admin_model.php';
require_once '../Design Patterns/Decorater.php';

class DeletePatientController {
    private $patientModel;
    private $adminComponent; 

    public function __construct() {
        $adminModel = new AdminModel();
        $this->adminComponent = $adminModel->getCurrentAdminInstance();
        $this->patientModel = new PatientModel();
        $this->initializePatientsInSession();
    }

    private function initializePatientsInSession() {
        if (!isset($_SESSION['PatientsDelete']) || empty($_SESSION['PatientsDelete'])) {
            $patients = $this->patientModel->getPatients();
            error_log("[DEBUG] Patients fetched from database: " . print_r($patients, true));
            if (!empty($patients)) {
                $_SESSION['PatientsDelete'] = $patients;
            } else {
                $_SESSION['PatientsDelete'] = []; // Initialize as an empty array to avoid warnings
            }
            error_log("[DEBUG] Patients initialized in session: " . print_r($_SESSION['PatientsDelete'], true));
        }
    }

    public function getPatients() {
        $patients = $this->patientModel->getPatients();
        error_log("[DEBUG] Patients fetched from database: " . print_r($patients, true));
        return $patients;
    }

    public function deletePatient($patientId) {
        // Check if the patient ID is valid
        if (!$patientId) {
            $_SESSION['error'] = "Invalid patient ID.";
            header("Location: ../View/delete_patient_view.php");
            exit();
        }

        // Attempt to delete the patient using the AdminComponent instance
        $success = $this->adminComponent->deletePatient($patientId);

        if ($success) {
            // Refresh the patient data in the session
            $this->refreshPatientsInSession();

            $_SESSION['message'] = "Patient deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete patient.";
        }

        // Redirect back to the patient details view
        header("Location: ../View/delete_patient_view.php");
        exit();
    }

    private function refreshPatientsInSession() {
        // Fetch patients from the database and update the session
        $patients = $this->patientModel->getPatients();
        error_log("[DEBUG] Patients fetched from database: " . print_r($patients, true));
        $_SESSION['PatientsDelete'] = $patients ?: []; // Default to an empty array if no patients are found
    }
}

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['patient_id'])) {
    $patientId = $_POST['patient_id'];
    $deleteController = new DeletePatientController();
    $deleteController->deletePatient($patientId);
} else {
    // If the request is invalid, redirect to the patient details view
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../View/delete_patient_view.php");
    exit();
}

// Instantiate the controller
$controller = new DeletePatientController();

// Include the view
include '../View/delete_patient_view.php';
?>
