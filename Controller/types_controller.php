<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../Model/types_model.php';
require_once '../Model/admin_model.php';
require_once '../Design Patterns/Decorater.php'; // Include the AdminComponent interface and decorators

class TypesController {
    private $adminComponent;

    public function __construct() {
        $adminModel = new AdminModel();
        $this->adminComponent = $adminModel->getCurrentAdminInstance(); // Get the appropriate admin instance
        error_log("TypesController initialized.");
    }

    public function addType($data) {
        error_log("addType called with data: " . print_r($data, true));

        $patientId = $data['patient_id'] ?? null;
        if (!$patientId) {
            $_SESSION['message'] = "Patient ID is missing.";
            error_log("Patient ID is missing.");
            header("Location: ../View/types_view.php");
            exit();
        }

        $adminId = $_SESSION['admin_id'] ?? null;
        if (!$adminId) {
            $_SESSION['message'] = "Admin ID is missing.";
            error_log("Admin ID is missing.");
            header("Location: ../View/admin_login_view.php");
            exit();
        }

        // Use the AdminComponent (decorator) to add surgery type
        $success = $this->adminComponent->addSurgery($data, $adminId);
        if ($success) {
            $_SESSION['message'] = "Surgery type added successfully.";
            error_log("Surgery type inserted successfully.");
            header("Location: ../View/add_patient_details_view.php");
        } else {
            $_SESSION['message'] = "Failed to add surgery type.";
            error_log("Failed to insert surgery type.");
            header("Location: ../View/types_view.php");
        }
        exit();
    }

    public function getTypeById($id) {
        $typesModel = new TypesModel(); // Directly use the model for read-only operations
        return $typesModel->getTypeById($id);
    }

    public function getAllTypes() {
        $typesModel = new TypesModel(); // Directly use the model for read-only operations
        return $typesModel->getAllTypes();
    }

    public function updateType($id, $data) {
        error_log("updateType called for ID: $id with data: " . print_r($data, true));

        $typesModel = new TypesModel(); // Directly use the model for updates
        $success = $typesModel->updateType($id, $data);
        if ($success) {
            $_SESSION['message'] = "Type updated successfully.";
            error_log("Type updated successfully for ID: $id.");
            header("Location: ../View/types_view.php");
        } else {
            $_SESSION['message'] = "Failed to update type.";
            error_log("Failed to update type for ID: $id.");
            header("Location: ../View/types_view.php");
        }
        exit();
    }

    public function deleteType($id) {
        error_log("deleteType called for ID: $id.");

        $typesModel = new TypesModel(); // Directly use the model for deletions
        $success = $typesModel->deleteType($id);
        if ($success) {
            $_SESSION['message'] = "Type deleted successfully.";
            error_log("Type deleted successfully for ID: $id.");
            header("Location: ../View/types_view.php");
        } else {
            $_SESSION['message'] = "Failed to delete type.";
            error_log("Failed to delete type for ID: $id.");
            header("Location: ../View/types_view.php");
        }
        exit();
    }

    public function handleFormSubmission() {
        error_log("handleFormSubmission called.");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("Form submitted via POST.");

            $data = [
                'patient_id' => $_POST['patient_id'] ?? null,
                'surgery_type' => $_POST['surgery_type'] ?? null,
                'stapler_type' => $_POST['stapler_type'] ?? null,
                'number_of_staplers' => $_POST['number_of_staplers'] ?? 0,
                'black_staplers' => $_POST['black_staplers'] ?? 0,
                'green_staplers' => $_POST['green_staplers'] ?? 0,
                'yellow_staplers' => $_POST['yellow_staplers'] ?? 0,
                'blue_staplers' => $_POST['blue_staplers'] ?? 0,
                'purple_staplers' => $_POST['purple_staplers'] ?? 0,
                'tan_staplers' => $_POST['tan_staplers'] ?? 0,
                'reinforcement_type' => $_POST['reinforcement_type'] ?? null,
                'gastric_fixation' => $_POST['gastric_fixation'] ?? null,
                'hiatus_hernia' => $_POST['hiatus_hernia'] ?? null,
                'other_surgery' => $_POST['other_surgery'] ?? null,
                'other_surgery_details' => $_POST['other_surgery_details'] ?? null,
                'estoma_size' => $_POST['estoma_size'] ?? null,
                'estoma_color' => $_POST['estoma_color'] ?? null,
                'bypassed_intestine_length' => $_POST['bypassed_intestine_length'] ?? 0,
                'whole_intestine_length' => $_POST['whole_intestine_length'] ?? 0,
                'roux_limb_length' => $_POST['roux_limb_length'] ?? 0,
                'perial_limb_length' => $_POST['perial_limb_length'] ?? 0,
                'closure_defect' => $_POST['closure_defect'] ?? null,
                'index_surgery_type' => $_POST['index_surgery_type'] ?? null,
                'index_surgery_time' => $_POST['index_surgery_time'] ?? null,
                'redo_type' => $_POST['redo_type'] ?? null,
                'comments' => $_POST['comments'] ?? null,
            ];

            $action = $_POST['action'] ?? 'add';
            if ($action === 'add') {
                $this->addType($data);
            } elseif ($action === 'update') {
                $typeId = $_POST['id'] ?? null;
                if ($typeId) {
                    $this->updateType($typeId, $data);
                } else {
                    error_log("Type ID missing for update action.");
                }
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) {
            $typeId = $_GET['delete'];
            $this->deleteType($typeId);
        } else {
            error_log("Invalid form submission method.");
        }
    }
}

$typesController = new TypesController();
$typesController->handleFormSubmission();
?>