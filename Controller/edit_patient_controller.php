<?php
session_start();
require_once '../Model/patient_model.php';

class EditPatientController {
    private $patientModel;

    public function __construct() {
        $this->patientModel = new PatientModel();
    }

    public function getPatients() {
        echo json_encode($this->patientModel->getPatients());
    }

    public function getPatientById($patientId) {
        $data = $this->patientModel->getPatientDetails($patientId);
        error_log("Data for patient ID $patientId: " . print_r($data, true)); // Log data for debugging
        header('Content-Type: application/json');
        echo json_encode($data);
    }
    

    public function updatePatient($patientId) {
        // Decode JSON data from frontend
        $data = json_decode(file_get_contents('php://input'), true);
    
        // Log decoded data to check if it was received correctly
        error_log("Decoded JSON data: " . print_r($data, true));
    
        if (is_null($data)) {
            error_log("Failed to decode JSON data. Raw input: " . file_get_contents('php://input'));
        }
    
        // Check if patientId is provided and valid
        if (is_null($patientId)) {
            error_log("PatientId is missing or invalid.");
            return ['success' => false, 'error' => 'PatientId is required'];
        }
    
        // Pass data and patientId to updatePatientDetails in the model
        $result = $this->patientModel->updatePatientDetails($data, $patientId);
    
        // Log the result of updatePatientDetails for debugging
        error_log("Result from updatePatientDetails: " . print_r($result, true));
    
            echo json_encode(['success' => $result ? true : false]);
            exit(); // Ensure no further output interferes with the JSON response
        
        
    }
    
    
}


$controller = new EditPatientController();


if (isset($_GET['action']) && $_GET['action'] === 'getPatients') {
    $editPatientController = new EditPatientController();
    $editPatientController->getPatients();

    header('Content-Type: application/json');
    //echo json_encode($patients, JSON_PRETTY_PRINT); // Add JSON_PRETTY_PRINT for readable formatting
    exit();
}

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'getPatients':
            $controller->getPatients();
            break;
        case 'getPatientById':
            $controller->getPatientById($_GET['patient_id']);
            break;
        case 'updatePatient':
            // Capture `patientId` from the request and pass it to `updatePatient`
            $patientId = isset($_GET['patient_id']) ? $_GET['patient_id'] : null;
            if ($patientId !== null) {
                $controller->updatePatient($patientId);
            } else {
                error_log("PatientId is missing from the request.");
            }
            break;
    }
}




// Check for AJAX request with action=getPatients
?>