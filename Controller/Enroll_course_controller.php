<?php

require_once '../Model/course_model.php';

session_start();

class CourseController {

    private $courseModel;

    public function __construct() {
        $this->courseModel = new CourseModel();
    }

    public function getAvailableCourses() {
        return $this->courseModel->getAvailableCourses();
    }

    public function assignCourse($trainee_id, $courseId) {
        // Log the action
        $verbId = $this->courseModel->enrollVerb($trainee_id);
        $this->courseModel->enrollCourseDetail($verbId, $courseId, "Assigned course ID: $courseId");
        $this->courseModel->enrollCourse($courseId, $trainee_id); 
    }
}

$controller = new CourseController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $trainee_id = $_SESSION['MemberID'] ?? null;
        $courseId = $_POST['course_id'] ?? null;

        if (!$trainee_id || !$courseId) {
            throw new Exception("Invalid request. Trainee or course ID is missing.");
        }

        $controller->assignCourse($trainee_id, $courseId);

        // Redirect after successful assignment to avoid reloading the view
        header("Location: ../View/trainee_home_view.php");
        exit();
    } catch (Exception $e) {
        error_log("[ERROR] " . $e->getMessage());
        // Redirect with error message in case of failure
        header("Location: ../View/Enroll_course_view.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} 

$CourseController = new CourseController();
$courses = $CourseController->getAvailableCourses();
