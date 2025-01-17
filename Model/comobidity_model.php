<?php
require_once '../Helper FIles/my_database.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ComorbidityModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        if ($this->conn->connect_error) {
            error_log("Database connection failed: " . $this->conn->connect_error);
            die("Database connection failed: " . $this->conn->connect_error);
        }
        error_log("Database connection established successfully for ComorbidityModel.");
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

    // Insert Co-Morbidity details into the database
    public function insertComorbidity($data, $adminId) {
        $this->conn->begin_transaction();

        try {
            // Insert into co_morbidity table
            $query = "INSERT INTO comorbidity (patient_id, functional_status, diabetes, habic, diabetes_duration, hypertension, lipid_profile, reflux, fatty_liver, gynecological) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("issssissss", $data['patient_id'], $data['functional_status'], $data['diabetes'], $data['habic'], $data['diabetes_duration'], 
                             $data['hypertension'], $data['lipid_profile'], $data['reflux'], $data['fatty_liver'], $data['gynecological']);
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert into comorbidity: " . $stmt->error);
            }
            $comorbidityId = $this->conn->insert_id; // Get the new record ID

            // Log the admin action in add_comorbidity table
            $addComorbidityQuery = "INSERT INTO add_comorbidity (admin_id) VALUES (?)";
            $addComorbidityStmt = $this->conn->prepare($addComorbidityQuery);
            $addComorbidityStmt->bind_param("i", $adminId);
            if (!$addComorbidityStmt->execute()) {
                throw new Exception("Failed to insert into add_comorbidity: " . $addComorbidityStmt->error);
            }
            $addId = $this->conn->insert_id;

            // Insert log detail in add_comorbidity_detail table
            $detailDescription = "Added co-morbidity details for patient ID: " . $data['patient_id'];
            $addComorbidityDetailQuery = "INSERT INTO add_comorbidity_detail (add_id, comorbidity_id, detail_description) VALUES (?, ?, ?)";
            $addComorbidityDetailStmt = $this->conn->prepare($addComorbidityDetailQuery);
            $addComorbidityDetailStmt->bind_param("iis", $addId, $comorbidityId, $detailDescription);
            if (!$addComorbidityDetailStmt->execute()) {
                throw new Exception("Failed to insert into add_comorbidity_detail: " . $addComorbidityDetailStmt->error);
            }

            // Commit transaction if all steps are successful
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Co-morbidity insertion transaction failed: " . $e->getMessage());
            return false;
        }
    }

    // Retrieve a specific co-morbidity entry by ID
    public function getComorbidityById($comorbidityId) {
        $query = "SELECT * FROM co_morbidity WHERE comorbidity_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $comorbidityId);
        $stmt->execute();
        $result = $stmt->get_result();
        $comorbidity = $result->fetch_assoc();
        $stmt->close();
        return $comorbidity;
    }

    // Retrieve all co-morbidity entries
    public function getAllComorbidities() {
        $query = "SELECT * FROM co_morbidity";
        $result = $this->conn->query($query);
        $comorbidities = [];
        while ($row = $result->fetch_assoc()) {
            $comorbidities[] = $row;
        }
        return $comorbidities;
    }

    // Update an existing co-morbidity entry
    public function updateComorbidity($comorbidityId, $data) {
        $this->conn->begin_transaction();

        try {
            $query = "UPDATE co_morbidity SET functional_status = ?, diabetes = ?, habic = ?, diabetes_duration = ?, hypertension = ?, 
                      lipid_profile = ?, reflux = ?, fatty_liver = ?, gynecological = ? WHERE comorbidity_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sssssisssi", $data['functional_status'], $data['diabetes'], $data['habic'], $data['diabetes_duration'], 
                             $data['hypertension'], $data['lipid_profile'], $data['reflux'], $data['fatty_liver'], $data['gynecological'], $comorbidityId);
            $stmt->execute();
            $stmt->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Co-morbidity update failed: " . $e->getMessage());
            return false;
        }
    }

    // Delete a co-morbidity entry by ID
    public function deleteComorbidity($comorbidityId) {
        $this->conn->begin_transaction();

        try {
            // Delete log from add_comorbidity_detail table if there is a record
            $deleteDetailQuery = "DELETE FROM add_comorbidity_detail WHERE comorbidity_id = ?";
            $deleteDetailStmt = $this->conn->prepare($deleteDetailQuery);
            $deleteDetailStmt->bind_param("i", $comorbidityId);
            $deleteDetailStmt->execute();
            $deleteDetailStmt->close();

            // Delete from co_morbidity table
            $deleteComorbidityQuery = "DELETE FROM co_morbidity WHERE comorbidity_id = ?";
            $deleteComorbidityStmt = $this->conn->prepare($deleteComorbidityQuery);
            $deleteComorbidityStmt->bind_param("i", $comorbidityId);
            $deleteComorbidityStmt->execute();
            $deleteComorbidityStmt->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Co-morbidity deletion failed: " . $e->getMessage());
            return false;
        }
    }
}
?>