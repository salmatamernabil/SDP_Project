<?php
require_once '../Helper FIles/my_database.php';

class CourseModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        if ($this->conn->connect_error) {
            error_log("[ERROR] Database connection failed: " . $this->conn->connect_error);
            die("Database connection failed: " . $this->conn->connect_error);
        }
        error_log("[DEBUG] CourseModel instantiated with active database connection.");
    }

    public function addCourse($courseData) {
        $query = "INSERT INTO course (course_name, course_date, course_place, hosting_hospital, total_cost) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssd", 
            $courseData['course_name'], 
            $courseData['course_date'], 
            $courseData['course_place'], 
            $courseData['hosting_hospital'], 
            $courseData['total_cost']
        );

        if (!$stmt->execute()) {
            error_log("[ERROR] Failed to add course: " . $stmt->error);
            throw new Exception("Failed to add course: " . $stmt->error);
        }

        $courseId = $this->conn->insert_id; 
        error_log("[INFO] Course added successfully with ID: $courseId.");
        return $courseId; 
    }

    public function logAddCourseVerb($adminId) {
        $query = "INSERT INTO add_course_verb (admin_id) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $adminId);

        if (!$stmt->execute()) {
            error_log("[ERROR] Failed to log add course verb: " . $stmt->error);
            throw new Exception("Failed to log add course verb: " . $stmt->error);
        }

        $verbId = $this->conn->insert_id; 
        error_log("[INFO] Add course verb logged with ID: $verbId for Admin ID: $adminId.");
        return $verbId; 
    }

    public function logAddCourseDetail($verbId, $courseId, $detailDescription) {
        $query = "INSERT INTO add_course_detail (verb_id, course_id, detail_description) 
                  VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iis", $verbId, $courseId, $detailDescription);

        if (!$stmt->execute()) {
            error_log("[ERROR] Failed to log add course detail: " . $stmt->error);
            throw new Exception("Failed to log add course detail: " . $stmt->error);
        }

        error_log("[INFO] Add course detail logged for Verb ID: $verbId, Course ID: $courseId.");
    }

    public function getAllCourses() {
        $query = "SELECT * FROM course";
        error_log("[DEBUG] Executing query to fetch all courses: $query");

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

    public function updateCourse($courseId, $updatedData) {
        $query = "UPDATE course SET 
                    course_name = ?, 
                    course_date = ?, 
                    course_place = ?, 
                    hosting_hospital = ?, 
                    total_cost = ? 
                  WHERE course_id = ?";

        if (!isset($updatedData['course_name'], $updatedData['course_date'], $updatedData['course_place'], $updatedData['hosting_hospital'], $updatedData['total_cost'])) {
            error_log("[ERROR] Missing data to update course.");
            throw new Exception("Missing data to update course.");
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            "ssssdi", 
            $updatedData['course_name'], 
            $updatedData['course_date'], 
            $updatedData['course_place'], 
            $updatedData['hosting_hospital'], 
            $updatedData['total_cost'], 
            $courseId
        );

        if (!$stmt->execute()) {
            error_log("[ERROR] Failed to update course ID $courseId: " . $stmt->error);
            throw new Exception("Failed to update course: " . $stmt->error);
        }

        error_log("[INFO] Course ID $courseId updated successfully.");
    }

    public function deleteCourse($courseId) {
        $query = "DELETE FROM course WHERE course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $courseId);

        if (!$stmt->execute()) {
            error_log("[ERROR] Failed to delete course ID $courseId: " . $stmt->error);
            throw new Exception("Failed to delete course: " . $stmt->error);
        }

        error_log("[INFO] Course ID $courseId deleted successfully.");
    }

    public function getCourseById($courseId) {
        $query = "SELECT * FROM course WHERE course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            error_log("[INFO] No course found with ID: $courseId.");
            return null;
        }

        $course = $result->fetch_assoc();
        error_log("[DEBUG] Course fetched for ID $courseId: " . json_encode($course));
        return $course;
    }
    public function updateCourseDonation($courseId, $donatedAmount) {
        // Query to update the donations_received field by adding the donated amount
        $query = "UPDATE course SET donations_received = donations_received + ? WHERE course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("di", $donatedAmount, $courseId);  // Bind parameters (donated amount, course_id)
        
        if ($stmt->execute()) {
            error_log("Course donations_received updated successfully.");
        } else {
            error_log("Failed to update course donations_received: " . $stmt->error);
        }
    }

    public function instructcourseVerb($doctor_id) {
        $query = "INSERT INTO instruct_course_verb (instructor_id) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $doctor_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to log add course verb: " . $stmt->error);
        }
        
        return $this->conn->insert_id; // Return the verb ID
    }

    public function instructCourseDetail($verbId, $courseId, $detailDescription) {
        $query = "INSERT INTO instruct_verb_detail (verb_id, course_id, detail_description) 
                  VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
    
        if (!$stmt) {
            throw new Exception("Failed to prepare query: " . $this->conn->error);
        }
    
        $stmt->bind_param("iis", $verbId, $courseId, $detailDescription);
    
        // Log values being bound to the query
        error_log("InstructCourseDetail - Binding values: Verb ID = $verbId, Course ID = $courseId, Description = $detailDescription");
    
        if (!$stmt->execute()) {
            throw new Exception("Failed to log add course detail: " . $stmt->error);
        }
    
        error_log("Successfully logged course detail for Verb ID: $verbId and Course ID: $courseId.");
    }


    public function enrollCourse($course_id, $trainee_id) {
        $query = "INSERT INTO enrolledcourses (course_id, trainee_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
    
        if (!$stmt) {
            throw new Exception("Failed to prepare query: " . $this->conn->error);
        }
    
        $stmt->bind_param("ii", $course_id, $trainee_id);
    
        // Log values being bound to the query
        error_log("EnrollCourse - Binding values: Course ID = $course_id, Trainee ID = $trainee_id");
    
        if (!$stmt->execute()) {
            throw new Exception("Failed to enroll course: " . $stmt->error);
        }
    
        error_log("Successfully enrolled Trainee ID: $trainee_id in Course ID: $course_id.");
        
        return $this->conn->insert_id; // Return the inserted ID if needed
    }
    
    public function enrollVerb($trainee_id) {
        $query = "INSERT INTO enroll_course_verb (trainee_id) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $trainee_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to log enroll verb: " . $stmt->error);
        }
        
        return $this->conn->insert_id; // Return the verb ID
    }

    public function enrollCourseDetail($verbId, $courseId, $detailDescription) {
        $query = "INSERT INTO enroll_verb_detail (verb_id, course_id, detail_description) 
                  VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
    
        if (!$stmt) {
            throw new Exception("Failed to prepare query: " . $this->conn->error);
        }
    
        $stmt->bind_param("iis", $verbId, $courseId, $detailDescription);
    
        // Log values being bound to the query
        error_log("EnrollCourseDetail - Binding values: Verb ID = $verbId, Course ID = $courseId, Description = $detailDescription");
    
        if (!$stmt->execute()) {
            throw new Exception("Failed to log enroll course detail: " . $stmt->error);
        }
    
        error_log("Successfully logged course detail for Verb ID: $verbId and Course ID: $courseId.");
    }
    

    public function getAvailableCourses() {
        // Get the trainee_id from session
        $trainee_id = $_SESSION['MemberID'] ?? null;
        
        if (!$trainee_id) {
            throw new Exception("Trainee ID is missing from session.");
        }
    
        // Prepare the query to fetch courses the trainee is not enrolled in
        $query = "SELECT * 
                  FROM course 
                  WHERE course_id NOT IN (
                      SELECT course_id 
                      FROM enrolledcourses 
                      WHERE trainee_id = ?
                  )";
    
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare query: " . $this->conn->error);
        }
    
        $stmt->bind_param("i", $trainee_id);
        $stmt->execute();
    
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // Fetch and return the available courses
            $courses = [];
            while ($row = $result->fetch_assoc()) {
                $courses[] = $row;
            }
            return $courses;
        } else {
            return []; // No courses available
        }
    }
    
    

}
?>
