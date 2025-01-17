<?php


require_once '../Design Patterns/Command.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../Model/course_model.php';

class CourseController {
    private $courseModel;

    public function __construct() {
        $this->courseModel = new CourseModel();
        error_log("[DEBUG] CourseController instantiated.");
    }

    public function addCourse($courseData) {
        if (!isset($_SESSION['admin_id'])) {
            error_log("[ERROR] Unauthorized access attempt to addCourse method.");
            header("Location: ../View/admin_login_view.php");
            exit();
        }

        $adminId = $_SESSION['admin_id'];
        error_log("[DEBUG] Admin ID for addCourse: $adminId.");

        try {
            // Create Command instance
            $command = new AddCourseCommand($this->courseModel, $courseData, $adminId);
            error_log("[DEBUG] AddCourseCommand created.");

            // Retrieve the CommandInvoker from the session
            $commandInvoker = $_SESSION['commandInvoker'];
            $commandInvoker->addCommand($command);

            // Execute the command and track it in executedCommands
            $courseId = $commandInvoker->executeCommands()[0]; // Execute the command and get the result
            $_SESSION['commandInvoker'] = $commandInvoker; // Save back to session

            error_log("[INFO] Course added successfully with ID: $courseId by Admin ID: $adminId.");

            // Set success message and redirect
            $_SESSION['message'] = "Course added successfully!";
            header("Location: ../View/admin_home_view.php");
        } catch (Exception $e) {
            // Log the error
            error_log("[ERROR] Failed to add course: " . $e->getMessage());

            // Set error message and redirect
            $_SESSION['error'] = "Failed to add course: " . $e->getMessage();
            header("Location: ../View/add_course_view.php");
        }
    }
}
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log("[DEBUG] Form submission detected for addCourse.");

    $courseData = [
        'course_name' => $_POST['courseName'],
        'course_date' => $_POST['dateOfCourse'],
        'course_place' => $_POST['placeOfCourse'],
        'hosting_hospital' => $_POST['hospitalOfHosting'],
        'total_cost' => $_POST['totalCost']
    ];

    // Log the received course data
    error_log("[DEBUG] Course data received: " . json_encode($courseData));

    $courseController = new CourseController();
    $courseController->addCourse($courseData);

    error_log("[DEBUG] addCourse method execution completed.");
}
?>
