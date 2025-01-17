<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../Model/comobidity_model.php';
require_once '../Model/admin_model.php';
require_once '../Design Patterns/Decorater.php'; // Include the AdminComponent interface and decorators

class ComorbidityController {
    private $adminComponent;

    public function __construct() {
        $adminModel = new AdminModel();
        $this->adminComponent = $adminModel->getCurrentAdminInstance();
        error_log("ComorbidityController initialized.");
    }

    public function addComorbidity($data) {
        error_log("addComorbidity called with data: " . print_r($data, true));

        $patientId = $data['patient_id'] ?? null;
        if (!$patientId) {
            $_SESSION['message'] = "Patient ID is missing.";
            error_log("Patient ID is missing.");
            header("Location: ../View/comorbidity_view.php");
            exit();
        }

        $adminId = $_SESSION['admin_id'] ?? null;
        if (!$adminId) {
            $_SESSION['message'] = "Admin ID is missing.";
            error_log("Admin ID is missing.");
            header("Location: ../View/admin_login_view.php");
            exit();
        }

        // Use the AdminComponent (decorator) to add comorbidity
        $success = $this->adminComponent->addComorbidity($data, $adminId);
        if ($success) {
            $_SESSION['message'] = "Data added successfully.";
            error_log("Data inserted successfully.");
            header("Location: ../View/add_patient_details_view.php");
        } else {
            $_SESSION['message'] = "Failed to add data.";
            error_log("Failed to insert data.");
            header("Location: ../View/comorbidity_view.php");
        }
        exit();
    }

    public function handleFormSubmission() {
        error_log("handleFormSubmission called.");
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("Form submitted via POST.");

            $data = [
                'patient_id' => $_POST['patient_id'] ?? null,
                'functional_status' => $_POST['functional_status'] ?? null,
                'diabetes' => $_POST['diabetes'] ?? null,
                'habic' => $_POST['habic'] ?? null,
                'diabetes_duration' => $_POST['diabetes_duration'] ?? null,
                'hypertension' => $_POST['hypertension'] ?? null,
                'lipid_profile' => $_POST['lipid_profile'] ?? null,
                'reflux' => $_POST['reflux'] ?? null,
                'fatty_liver' => $_POST['fatty_liver'] ?? null,
                'gynecological' => $_POST['gynecological'] ?? null
            ];

            $this->addComorbidity($data);
        } else {
            error_log("Form not submitted via POST.");
        }
    }
}

$comorbidityController = new ComorbidityController();
$comorbidityController->handleFormSubmission();
?>

