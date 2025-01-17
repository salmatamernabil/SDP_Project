<?php
require_once '../Model/course_model.php';
require_once '../Model/admin_model.php';
require_once '../Design Patterns/Decorater.php';

class EditCourseController {
    private $courseModel;
    private $adminComponent; 

    public function __construct() {
        $this->courseModel = new CourseModel();
        $adminModel = new AdminModel();
        $this->adminComponent = $adminModel->getCurrentAdminInstance();
    }

    public function updateCourse($courseId, $courseName, $courseDate, $coursePlace, $hospitalOfHosting, $totalCost) {
        $updatedData = [
            'course_name' => $courseName,
            'course_date' => $courseDate,
            'course_place' => $coursePlace,
            'hosting_hospital' => $hospitalOfHosting,
            'total_cost' => $totalCost
        ];
    
        $this->adminComponent->editCourse($courseId, $updatedData);
      
    }
    
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['course_id'], $_POST['courseName'], $_POST['dateOfCourse'], $_POST['placeOfCourse'], $_POST['hospitalOfHosting'], $_POST['totalCost'])) {
        $courseId = $_POST['course_id'];
        $courseName = $_POST['courseName'];
        $courseDate = $_POST['dateOfCourse'];
        $coursePlace = $_POST['placeOfCourse'];
        $hospitalOfHosting = $_POST['hospitalOfHosting'];
        $totalCost = $_POST['totalCost'];

        // Create an instance of the controller
        $controller = new EditCourseController();
        // Call the updateCourse method
        $controller->updateCourse($courseId, $courseName, $courseDate, $coursePlace, $hospitalOfHosting, $totalCost);

        // Redirect to the "All Courses" view after successful update
        header("Location: ../View/all_courses_view.php");
        exit();
    } else {
        die("Required parameters are missing.");
    }
}
?>
