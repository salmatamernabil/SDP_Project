<?php
require_once '../Helper FIles/my_database.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class BMIModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        if ($this->conn->connect_error) {
            error_log("Database connection failed: " . $this->conn->connect_error);
            die("Database connection failed: " . $this->conn->connect_error);
        }
        error_log("Database connection established successfully for BMIModel.");
    }

    // Retrieve the latest patient_id
    public function getLatestPatientId() {
        $query = "SELECT PatientId FROM patient ORDER BY PatientId DESC LIMIT 1";
        $result = $this->conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            error_log("Latest patient ID retrieved: " . $row['PatientId']);
            return $row['PatientId'];
        } else {
            error_log("Failed to retrieve latest patient ID or no patients found.");
            return null;
        }
    }

    // Insert BMI with admin action logging
    public function insertBMI($data, $adminId) {
        $this->conn->begin_transaction();

        try {
            // Step 1: Insert into patient_bmi table
            $bmiQuery = "INSERT INTO patient_bmi (patient_id, weight, height, bmi_value, result) VALUES (?, ?, ?, ?, ?)";
            $bmiStmt = $this->conn->prepare($bmiQuery);
            $bmiStmt->bind_param("iidss", $data['patient_id'], $data['weight'], $data['height'], $data['bmi_value'], $data['result']);
            if (!$bmiStmt->execute()) {
                throw new Exception("Failed to insert into patient_bmi: " . $bmiStmt->error);
            }
            $bmiId = $this->conn->insert_id; // Get the new BMI record ID

            // Step 2: Log action in add_bmi table
            $addBmiQuery = "INSERT INTO add_bmi (admin_id) VALUES (?)";
            $addBmiStmt = $this->conn->prepare($addBmiQuery);
            $addBmiStmt->bind_param("i", $adminId);
            if (!$addBmiStmt->execute()) {
                throw new Exception("Failed to insert into add_bmi: " . $addBmiStmt->error);
            }
            $addId = $this->conn->insert_id;

            // Step 3: Insert log detail in add_bmi_detail table
            $detailDescription = "Added BMI for patient ID: " . $data['patient_id'];
            $addBmiDetailQuery = "INSERT INTO add_bmi_detail (add_id, bmi_id, detail_description) VALUES (?, ?, ?)";
            $addBmiDetailStmt = $this->conn->prepare($addBmiDetailQuery);
            $addBmiDetailStmt->bind_param("iis", $addId, $bmiId, $detailDescription);
            if (!$addBmiDetailStmt->execute()) {
                throw new Exception("Failed to insert into add_bmi_detail: " . $addBmiDetailStmt->error);
            }

            // Commit transaction if all steps are successful
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("BMI insertion transaction failed: " . $e->getMessage());
            return false;
        }
    }


       // Retrieve a specific BMI entry by ID
       public function getBMIById($bmiId) {
        $query = "SELECT * FROM patient_bmi WHERE bmi_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $bmiId);
        $stmt->execute();
        $result = $stmt->get_result();
        $bmi = $result->fetch_assoc();
        $stmt->close();
        return $bmi;
    }

    // Retrieve all BMI entries
    public function getAllBMIs() {
        $query = "SELECT * FROM patient_bmi";
        $result = $this->conn->query($query);
        $bmis = [];
        while ($row = $result->fetch_assoc()) {
            $bmis[] = $row;
        }
        return $bmis;
    }

    // Update an existing BMI entry
    public function updateBMI($bmiId, $data) {
        $this->conn->begin_transaction();

        try {
            $query = "UPDATE patient_bmi SET weight = ?, height = ?, bmi_value = ?, result = ? WHERE bmi_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("idssi", $data['weight'], $data['height'], $data['bmi_value'], $data['result'], $bmiId);
            $stmt->execute();
            $stmt->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("BMI update failed: " . $e->getMessage());
            return false;
        }
    }

    // Delete a BMI entry by ID
    public function deleteBMI($bmiId) {
        $this->conn->begin_transaction();

        try {
            // Step 1: Delete from add_bmi_detail table if there is a record
            $deleteDetailQuery = "DELETE FROM add_bmi_detail WHERE bmi_id = ?";
            $deleteDetailStmt = $this->conn->prepare($deleteDetailQuery);
            $deleteDetailStmt->bind_param("i", $bmiId);
            $deleteDetailStmt->execute();
            $deleteDetailStmt->close();

            // Step 2: Delete from patient_bmi table
            $deleteBMIQuery = "DELETE FROM patient_bmi WHERE bmi_id = ?";
            $deleteBMIStmt = $this->conn->prepare($deleteBMIQuery);
            $deleteBMIStmt->bind_param("i", $bmiId);
            $deleteBMIStmt->execute();
            $deleteBMIStmt->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("BMI deletion failed: " . $e->getMessage());
            return false;
        }
    }
}
?>
