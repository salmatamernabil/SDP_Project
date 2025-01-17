<?php
require_once '../Helper Files/my_database.php';
class DoctorModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        if ($this->conn->connect_error) {
            die("Database connection failed: " . $this->conn->connect_error);
        }
    }


    public function getAvailableCourses() {
        $query = "SELECT * FROM course WHERE instructor_id IS NULL ";

    
        error_log("[DEBUG] Executing query to fetch avaialbe courses: $query");

        $result = $this->conn->query($query);

        if (!$result) {
            error_log("[ERROR] Query failed: " . $this->conn->error);
            return [];
        }

        $courses = [];
        if ($result->num_rows === 0) {
            error_log("[INFO] No courses found in the database.");
        } else {
            while ($row = $result->fetch_assoc()) {
                $courses[] = $row;
            }
            error_log("[DEBUG] Courses fetched: " . json_encode($courses));
        }

        return $courses;
    }
    
   
    
    // Assign course to doctor
    public function assignCourseToDoctor($courseId, $doctorId) {
        $sql = "UPDATE course SET instructor_id = ? WHERE course_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $doctorId, $courseId);
        return $stmt->execute();
    }
}
?>
