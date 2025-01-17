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

class AddPatientDetailsController {
    private $patientModel;

    public function __construct() {
        $this->patientModel = new PatientModel();
        $this->initializePatientsInSession();
    }

    private function initializePatientsInSession() {
        if (!isset($_SESSION['PatientsDetails']) || empty($_SESSION['PatientsDetails'])) {
            $patients = $this->patientModel->getPatients();
            error_log("[DEBUG] Patients fetched from database: " . print_r($patients, true));
            if (!empty($patients)) {
                $_SESSION['PatientsDetails'] = $patients;
            } else {
                $_SESSION['PatientsDetails'] = []; // Initialize as an empty array to avoid warnings
            }
            error_log("[DEBUG] Patients initialized in session: " . print_r($_SESSION['PatientsDetails'], true));
        }
    }

    public function getPatients() {
        $patients = $this->patientModel->getPatients();
        error_log("[DEBUG] Patients fetched from database: " . print_r($patients, true));
        return $patients;
    }
}

// Instantiate the controller
$controller = new AddPatientDetailsController();
error_log("[]");
$patients = $controller->getPatients();
error_log("[]");

// Include the view
include '../View/add_patient_details_view.php';
?>
