<?php
require_once '../Model/course_model.php';
require_once '../Model/admin_model.php';
require_once '../Design Patterns/Decorater.php';

class AllCoursesController {
    private $courseModel;
    private $adminComponent; 
    public function __construct() {
        $adminModel = new AdminModel();
        $this->adminComponent = $adminModel->getCurrentAdminInstance();
      
        $this->courseModel = new CourseModel();
        $_SESSION['isSuperAdmin'] = $this->isSuperAdmin();
    }
   
        
   
        
    
    // Check if the current user is a superadmin
    public function isSuperAdmin() {
        // Check if the admin component is an instance of SuperAdmin
        if ($this->adminComponent instanceof SuperAdmin) {
            return true;
        }
        return false; // Return false if not a superadmin
    }

    public function getAllCourses() {
        try {
            return $this->courseModel->getAllCourses();
        } catch (Exception $e) {
            error_log("[ERROR] Failed to fetch all courses: " . $e->getMessage());
            return [];
        }
    }

    public function deleteCourse($courseId) {
        try {
            $success = $this->adminComponent->deleteCourse($courseId);

        } catch (Exception $e) {
         
        }
    }
        // Get details of a specific course
        public function getCourseDetails($courseId) {
            return $this->courseModel->getCourseById($courseId);
        }
    

}

// Handle the delete request


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $courseId = $_POST['courseId'];
    $controller = new AllCoursesController();
    try {
        error_log("[INFO] Attempting to delete course with ID: $courseId");
        $controller->deleteCourse($courseId);
        error_log("[INFO] Successfully deleted course with ID: $courseId");

        // Store success message in session
        $_SESSION['message'] = "Course deleted successfully";
     //   $_SESSION['message_type'] = "success";
    } catch (Exception $e) {
        error_log("[ERROR] Failed to delete course with ID: $courseId. Exception: " . $e->getMessage());

        // Store failure message in session
     //   $_SESSION['message'] = "Failed to delete course";
       // $_SESSION['message_type'] = "error";
    }

    // Redirect without query parameters
    header("Location: ../View/all_courses_view.php");
    exit();
}



// Fetch all courses for the view
$coursesController = new AllCoursesController();
$courses = $coursesController->getAllCourses();
