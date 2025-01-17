<?php
require_once '../Model/doctor_model.php';
require_once '../Model/course_model.php';

session_start();

class CourseController {
    private $doctorModel;
    private $courseModel;

    public function __construct() {
        $this->doctorModel = new DoctorModel();
        $this->courseModel = new CourseModel();
    }

    public function getAvailableCourses() {
        return $this->doctorModel->getAvailableCourses();
    }

    public function assignCourse($doctorId, $courseId) {
        // Assign course to doctor
        if (!$this->doctorModel->assignCourseToDoctor($courseId, $doctorId)) {
            throw new Exception("Failed to assign course.");
        }

        // Log the action
        $verbId = $this->courseModel->instructcourseVerb($doctorId);
        $this->courseModel->instructCourseDetail($verbId, $courseId, "Assigned course ID: $courseId");
    }
}

$controller = new CourseController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $doctorId = $_SESSION['DoctorID'] ?? null;
        $courseId = $_POST['course_id'] ?? null;

        if (!$doctorId || !$courseId) {
            throw new Exception("Invalid request. Doctor or course ID is missing.");
        }

        $controller->assignCourse($doctorId, $courseId);

        // Redirect after successful assignment to avoid reloading the view
        header("Location: ../View/doctor_home_view.php");
        exit();
    } catch (Exception $e) {
        error_log("[ERROR] " . $e->getMessage());
        // Redirect with error message in case of failure
        header("Location: ../View/instruct_course_view.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}

$CourseController = new CourseController();
$courses = $CourseController->getAvailableCourses();
