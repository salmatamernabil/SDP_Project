<?php
require_once '../Helper FIles/my_database.php';

class PlanModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        if ($this->conn->connect_error) {
            error_log("[ERROR] Database connection failed: " . $this->conn->connect_error);
            die("Database connection failed: " . $this->conn->connect_error);
        }
        error_log("[DEBUG] PlanModel instantiated with active database connection.");
    }

    // Add a new plan
    public function addPlan($planData) {
        $query = "INSERT INTO plan (
                    course_name, start_date, end_date, place_of_course, 
                    transportation_mode, paid_transportation, paid_accommodation, 
                    meeting_location, meeting_time
                  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            "sssssssss",
            $planData['course_name'],
            $planData['start_date'],
            $planData['end_date'],
            $planData['place_of_course'],
            $planData['transportation_mode'],
            $planData['paid_transportation'],
            $planData['paid_accommodation'],
            $planData['meeting_location'],
            $planData['meeting_time']
        );

        if (!$stmt->execute()) {
            error_log("[ERROR] Failed to add plan: " . $stmt->error);
            throw new Exception("Failed to add plan: " . $stmt->error);
        }

        $planId = $this->conn->insert_id; 
        error_log("[INFO] Plan added successfully with ID: $planId.");
        return $planId; 
    }

    // Retrieve all plans
    public function getAllPlans() {
        $query = "SELECT * FROM plan";
        $result = $this->conn->query($query);

        if (!$result) {
            error_log("[ERROR] Query failed: " . $this->conn->error);
            return [];
        }

        $plans = [];
        while ($row = $result->fetch_assoc()) {
            $plans[] = $row;
        }

        error_log("[DEBUG] Plans fetched: " . json_encode($plans));
        return $plans;
    }

    // Retrieve a specific plan by ID
    public function getPlanById($planId) {
        $query = "SELECT * FROM plan WHERE plan_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $planId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            error_log("[INFO] No plan found with ID: $planId.");
            return null;
        }

        $plan = $result->fetch_assoc();
        error_log("[DEBUG] Plan fetched for ID $planId: " . json_encode($plan));
        return $plan;
    }

    // Update an existing plan
    public function updatePlan($planId, $updatedData) {
        $query = "UPDATE plan SET 
                    course_name = ?, 
                    start_date = ?, 
                    end_date = ?, 
                    place_of_course = ?, 
                    transportation_mode = ?, 
                    paid_transportation = ?, 
                    paid_accommodation = ?, 
                    meeting_location = ?, 
                    meeting_time = ? 
                  WHERE plan_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            "sssssssssi",
            $updatedData['course_name'],
            $updatedData['start_date'],
            $updatedData['end_date'],
            $updatedData['place_of_course'],
            $updatedData['transportation_mode'],
            $updatedData['paid_transportation'],
            $updatedData['paid_accommodation'],
            $updatedData['meeting_location'],
            $updatedData['meeting_time'],
            $planId
        );

        if (!$stmt->execute()) {
            error_log("[ERROR] Failed to update plan ID $planId: " . $stmt->error);
            throw new Exception("Failed to update plan: " . $stmt->error);
        }

        error_log("[INFO] Plan ID $planId updated successfully.");
    }

    // Delete a plan
    public function deletePlan($planId) {
        $query = "DELETE FROM plan WHERE plan_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $planId);

        if (!$stmt->execute()) {
            error_log("[ERROR] Failed to delete plan ID $planId: " . $stmt->error);
            throw new Exception("Failed to delete plan: " . $stmt->error);
        }

        error_log("[INFO] Plan ID $planId deleted successfully.");
    }

    public function logAddPlanVerb($adminId) {
        $query = "INSERT INTO add_plan_verb (admin_id) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $adminId);

        if (!$stmt->execute()) {
            error_log("[ERROR] Failed to log add plan verb: " . $stmt->error);
            throw new Exception("Failed to log add plan verb: " . $stmt->error);
        }

        $verbId = $this->conn->insert_id; 
        error_log("[INFO] Add plan verb logged with ID: $verbId for Admin ID: $adminId.");
        return $verbId; 
    }

    public function logAddPlanDetail($verbId, $planId, $detailDescription) {
        // Validate input parameters
        if (empty($verbId) || empty($planId) || empty($detailDescription)) {
            error_log("[ERROR] Invalid input parameters for logAddPlanDetail: verbId=$verbId, planId=$planId, detailDescription=$detailDescription");
            throw new Exception("Invalid input parameters for logAddPlanDetail.");
        }
    
        // Prepare the SQL query
        $query = "INSERT INTO add_plan_detail (verb_id, plan_id, detailed_description) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
    
        // Check if prepare() failed
        if (!$stmt) {
            error_log("[ERROR] Failed to prepare query: " . $this->conn->error);
            throw new Exception("Failed to prepare query: " . $this->conn->error);
        }
    
        // Bind parameters and execute the query
        $stmt->bind_param("iis", $verbId, $planId, $detailDescription);
        if (!$stmt->execute()) {
            error_log("[ERROR] Failed to log add plan detail: " . $stmt->error);
            throw new Exception("Failed to log add plan detail: " . $stmt->error);
        }
    
        error_log("[INFO] Add plan detail logged for Verb ID: $verbId, Plan ID: $planId.");
    }
}
?>
