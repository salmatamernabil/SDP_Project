<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../Model/complication_model.php';
require_once '../Model/admin_model.php';
require_once '../Design Patterns/Decorater.php'; // Include the AdminComponent interface and decorators

class ComplicationController {
    private $adminComponent;

    public function __construct() {
        $adminModel = new AdminModel();
        $this->adminComponent = $adminModel->getCurrentAdminInstance(); // Get the appropriate admin instance
        error_log("ComplicationController initialized.");
    }

    public function addComplication($data) {
        error_log("addComplication called with data: " . print_r($data, true));

        $patientId = $data['patient_id'] ?? null;
        if (!$patientId) {
            $_SESSION['message'] = "Patient ID is missing.";
            error_log("Patient ID is missing.");
            header("Location: ../View/complication_view.php");
            exit();
        }

        $adminId = $_SESSION['admin_id'] ?? null;
        if (!$adminId) {
            $_SESSION['message'] = "Admin ID is missing.";
            error_log("Admin ID is missing.");
            header("Location: ../View/admin_login_view.php");
            exit();
        }

        // Use the AdminComponent (decorator) to add complication
        $success = $this->adminComponent->addComplication($data, $adminId);
        if ($success) {
            $_SESSION['message'] = "Complication added successfully.";
            error_log("Complication inserted successfully.");
            header("Location: ../View/add_patient_details_view.php");
        } else {
            $_SESSION['message'] = "Failed to add complication.";
            error_log("Failed to insert complication.");
            header("Location: ../View/complication_view.php");
        }
        exit();
    }

    public function getComplicationById($id) {
        $complicationModel = new ComplicationModel(); // Directly use the model for read-only operations
        return $complicationModel->getComplicationById($id);
    }

    public function getAllComplications() {
        $complicationModel = new ComplicationModel(); // Directly use the model for read-only operations
        return $complicationModel->getAllComplications();
    }

    public function updateComplication($id, $data) {
        error_log("updateComplication called for ID: $id with data: " . print_r($data, true));

        $complicationModel = new ComplicationModel(); // Directly use the model for updates
        $success = $complicationModel->updateComplication($id, $data);
        if ($success) {
            $_SESSION['message'] = "Complication updated successfully.";
            error_log("Complication updated successfully for ID: $id.");
            header("Location: ../View/complication_view.php");
        } else {
            $_SESSION['message'] = "Failed to update complication.";
            error_log("Failed to update complication for ID: $id.");
            header("Location: ../View/complication_view.php");
        }
        exit();
    }

    public function deleteComplication($id) {
        error_log("deleteComplication called for ID: $id.");

        $complicationModel = new ComplicationModel(); // Directly use the model for deletions
        $success = $complicationModel->deleteComplication($id);
        if ($success) {
            $_SESSION['message'] = "Complication deleted successfully.";
            error_log("Complication deleted successfully for ID: $id.");
            header("Location: ../View/add_patient_details_view.php");
        } else {
            $_SESSION['message'] = "Failed to delete complication.";
            error_log("Failed to delete complication for ID: $id.");
            header("Location: ../View/complication_view.php");
        }
        exit();
    }

    public function handleFormSubmission() {
        error_log("handleFormSubmission called.");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("Form submitted via POST.");

            $data = [
                'patient_id' => $_POST['patient_id'] ?? null,
                'intraoperative' => $_POST['intraoperative'] ?? null,
                'postoperative' => $_POST['postoperative'] ?? null,
                'discharge' => $_POST['discharge'] ?? null,
                'days' => $_POST['days'] ?? null,
            ];

            $action = $_POST['action'] ?? 'add';
            if ($action === 'add') {
                $this->addComplication($data);
            } elseif ($action === 'update') {
                $complicationId = $_POST['id'] ?? null;
                if ($complicationId) {
                    $this->updateComplication($complicationId, $data);
                } else {
                    error_log("Complication ID missing for update action.");
                }
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) {
            $complicationId = $_GET['delete'];
            $this->deleteComplication($complicationId);
        } else {
            error_log("Invalid form submission method.");
        }
    }
}

$complicationController = new ComplicationController();
$complicationController->handleFormSubmission();
?>