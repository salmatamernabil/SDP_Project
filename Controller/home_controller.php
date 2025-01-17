<?php
session_start();

// Include the CourseModel to get the courses
require_once '../Model/course_model.php';

class HomeController {
    
    public function showHomePage() {
        // Get all courses from the model
        $courseModel = new CourseModel();
        $courses = $courseModel->getAllCourses();  // Retrieve all courses

        // Store courses in session so they can be accessed on the donate page
        $_SESSION['courses'] = $courses;

        // Include home view
       // include('../View/home_view.php');
    }
}
?>
