<?php
require_once '../Helper FIles/my_database.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ComplicationModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        if ($this->conn->connect_error) {
            error_log("Database connection failed: " . $this->conn->connect_error);
            die("Database connection failed: " . $this->conn->connect_error);
        }
        error_log("Database connection established successfully for ComplicationModel.");
    }

    // Insert Complication details into the database
    public function insertComplication($data, $adminId) {
        $this->conn->begin_transaction();

        try {
            // Insert into patient_complcation table
            $query = "INSERT INTO patient_complcation (PatientId, Intraoperative, Postoperative, Discharge, NumberOfDays) 
                      VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param(
                "isssi",
                $data['patient_id'],
                $data['intraoperative'],
                $data['postoperative'],
                $data['discharge'],
                $data['number_of_days']
            );
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert into patient_complcation: " . $stmt->error);
            }
            $complicationId = $this->conn->insert_id; // Get the new record ID

            // Log the admin action in add_complication table
            $addComplicationQuery = "INSERT INTO add_complication (AdminId) VALUES (?)";
            $addComplicationStmt = $this->conn->prepare($addComplicationQuery);
            $addComplicationStmt->bind_param("i", $adminId);
            if (!$addComplicationStmt->execute()) {
                throw new Exception("Failed to insert into add_complication: " . $addComplicationStmt->error);
            }
            $addId = $this->conn->insert_id;

            // Insert log detail in add_complication_detail table
            $detailDescription = "Added complication details for patient ID: " . $data['patient_id'];
            $addComplicationDetailQuery = "INSERT INTO add_complication_detail (AddId, ComplicationId, DetailDescription) 
                                            VALUES (?, ?, ?)";
            $addComplicationDetailStmt = $this->conn->prepare($addComplicationDetailQuery);
            $addComplicationDetailStmt->bind_param("iis", $addId, $complicationId, $detailDescription);
            if (!$addComplicationDetailStmt->execute()) {
                throw new Exception("Failed to insert into add_complication_detail: " . $addComplicationDetailStmt->error);
            }

            // Commit transaction if all steps are successful
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Complication insertion transaction failed: " . $e->getMessage());
            return false;
        }
    }

    // Retrieve all complications
    public function getAllComplications() {
        $query = "SELECT * FROM patient_complcation";
        $result = $this->conn->query($query);
        $complications = [];
        while ($row = $result->fetch_assoc()) {
            $complications[] = $row;
        }
        return $complications;
    }

    // Retrieve a specific complication by ID
    public function getComplicationById($complicationId) {
        $query = "SELECT * FROM patient_complcation WHERE complication_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $complicationId);
        $stmt->execute();
        $result = $stmt->get_result();
        $complication = $result->fetch_assoc();
        $stmt->close();
        return $complication;
    }

    // Update a specific complication by ID
    public function updateComplication($complicationId, $data) {
        $this->conn->begin_transaction();

        try {
            $query = "UPDATE patient_complcation 
                      SET intraoperative = ?, postoperative = ?, discharge = ?, number_of_days = ? 
                      WHERE complication_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param(
                "sssii",
                $data['intraoperative'],
                $data['postoperative'],
                $data['discharge'],
                $data['number_of_days'],
                $complicationId
            );
            $stmt->execute();
            $stmt->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Complication update failed: " . $e->getMessage());
            return false;
        }
    }

    // Delete a specific complication by ID
    public function deleteComplication($complicationId) {
        $this->conn->begin_transaction();

        try {
            // Delete from add_complication_detail table if there is a record
            $deleteDetailQuery = "DELETE FROM add_complication_detail WHERE complication_id = ?";
            $deleteDetailStmt = $this->conn->prepare($deleteDetailQuery);
            $deleteDetailStmt->bind_param("i", $complicationId);
            $deleteDetailStmt->execute();
            $deleteDetailStmt->close();

            // Delete from patient_complcation table
            $deleteComplicationQuery = "DELETE FROM patient_complcation WHERE complication_id = ?";
            $deleteComplicationStmt = $this->conn->prepare($deleteComplicationQuery);
            $deleteComplicationStmt->bind_param("i", $complicationId);
            $deleteComplicationStmt->execute();
            $deleteComplicationStmt->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Complication deletion failed: " . $e->getMessage());
            return false;
        }
    }
}
?>
