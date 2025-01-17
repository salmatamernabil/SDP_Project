<?php

require_once '../Design Patterns/Command.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../Model/plan_model.php';

class PlanController {
    private $planModel;

    public function __construct() {
        $this->planModel = new PlanModel();
        error_log("[DEBUG] PlanController instantiated.");
    }

    public function addPlan($planData) {
        if (!isset($_SESSION['admin_id'])) {
            error_log("[ERROR] Unauthorized access attempt to addPlan method.");
            header("Location: ../View/admin_login_view.php");
            exit();
        }

        $adminId = $_SESSION['admin_id'];
        error_log("[DEBUG] Admin ID for addPlan: $adminId.");

        try {
            // Create Command instance
            $command = new AddPlanCommand($this->planModel, $planData, $adminId);
            error_log("[DEBUG] AddPlanCommand created.");

            // Retrieve the CommandInvoker from the session
            $commandInvoker = $_SESSION['commandInvoker'];
            $commandInvoker->addCommand($command);

            // Execute the command and track it in executedCommands
            $planId = $commandInvoker->executeCommands()[0]; // Execute the command and get the result
            $_SESSION['commandInvoker'] = $commandInvoker; // Save back to session

            error_log("[INFO] Plan added successfully with ID: $planId by Admin ID: $adminId.");

            // Set success message and redirect
            $_SESSION['message'] = "Plan added successfully!";
            header("Location: ../View/admin_home_view.php");
        } catch (Exception $e) {
            // Log the error
            error_log("[ERROR] Failed to add plan: " . $e->getMessage());

            // Set error message and redirect
            $_SESSION['error'] = "Failed to add plan: " . $e->getMessage();
            header("Location: ../View/add_plan_view.php");
        }
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log("[DEBUG] Form submission detected for addPlan.");

    $planData = [
        'course_name' => $_POST['course'], 
        'start_date' => $_POST['startDate'],
        'end_date' => $_POST['endDate'],
        'place_of_course' => $_POST['placeOfCourse'],
        'transportation_mode' => $_POST['transportationMode'],
        'paid_transportation' => isset($_POST['paidTransportation']) ? 1 : 0,
        'paid_accommodation' => isset($_POST['paidAccommodation']) ? 1 : 0,
        'meeting_location' => $_POST['meetingLocation'],
        'meeting_time' => $_POST['meetingTime']
    ];

    // Log the received plan data
    error_log("[DEBUG] Plan data received: " . json_encode($planData));

    $planController = new PlanController();
    $planController->addPlan($planData);

    error_log("[DEBUG] addPlan method execution completed.");
}
?>
