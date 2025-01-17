<?php
require_once '../Helper FIles/my_database.php';

class PatientModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection(); // Get the Singleton DB connection
        if ($this->conn->connect_error) {
            die("Database connection failed: " . $this->conn->connect_error);
        }
        error_log("[DEBUG] PatientModel instantiated.");
    }

    // Retrieve all patients
    public function getPatients() {
        $query = "SELECT p.PatientId AS patient_id, 
                         m.FullName AS full_name, 
                         m.BirthDate AS birth_date, 
                         m.Gender AS gender, 
                         m.MobileNumber AS mobile_number,
                         p.HospitalName AS hospital_name
                  FROM patient p 
                  INNER JOIN member m ON p.MID = m.MemberID";
    
        $result = $this->conn->query($query);
    
        // Log the query and the number of rows returned
        error_log("[DEBUG] Query executed: " . $query);
        error_log("[DEBUG] Number of rows returned: " . $result->num_rows);
    
        $patients = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $patients[] = $row;
            }
        } else {
            error_log("[ERROR] Error in query or no results found: " . $this->conn->error);
        }
    
        return $patients;
    }

    // Retrieve patient data by ID
    public function getPatientById($patientId) {
        $query = "SELECT * FROM patient WHERE PatientId = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        $result = $stmt->get_result();
        $patient = $result->fetch_assoc();
        error_log("[DEBUG] Fetched patient with ID: $patientId");
        return $patient;
    }

    public function getPatientDetails($patientId) {
        $query = "SELECT m.FullName, m.BirthDate, m.Gender, m.MobileNumber, p.HospitalName, b.bmi_value
                  FROM member m 
                  INNER JOIN patient p ON p.MID = m.MemberID
                  LEFT JOIN patient_bmi b ON b.patient_id = p.PatientId
                  WHERE p.PatientId = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        error_log("[DEBUG] Fetched patient details for patient ID: $patientId");
        return $stmt->get_result()->fetch_assoc();
    }

    public function updatePatientDetails($data, $patientId) {
        // Start transaction
        $this->conn->begin_transaction();
    
        try {
            // Update member details using patientId
            $stmt1 = $this->conn->prepare("UPDATE member SET FullName = ?, BirthDate = ?, Gender = ?, MobileNumber = ? WHERE MemberID = (SELECT MID FROM patient WHERE PatientId = ?)");
            $stmt1->bind_param("sssii", $data['full_name'], $data['birth_date'], $data['gender'], $data['mobile_number'], $patientId);
            $stmt1->execute();
            error_log("[DEBUG] Updated member details for patient ID: $patientId");
    
            // Update patient details
            $stmt2 = $this->conn->prepare("UPDATE patient SET HospitalName = ? WHERE PatientId = ?");
            $stmt2->bind_param("si", $data['hospital_name'], $patientId);
            $stmt2->execute();
            error_log("[DEBUG] Updated hospital name for patient ID: $patientId");
    
            // Update BMI if provided
            $stmt3 = $this->conn->prepare("UPDATE patient_bmi SET bmi_value = ? WHERE patient_id = ?");
            $stmt3->bind_param("di", $data['bmi_value'], $patientId);
            $stmt3->execute();
            error_log("[DEBUG] Updated BMI for patient ID: $patientId");
    
            // Commit transaction
            $this->conn->commit();
            error_log("[INFO] Transaction committed for patient ID: $patientId");
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("[ERROR] Update transaction failed for patient ID: $patientId - " . $e->getMessage());
            return false;
        }
    }

    public function addPatient($data, $adminId) {
        $this->conn->begin_transaction();
        try {
            error_log("[DEBUG] addPatient: Starting transaction.");
    
            // Insert into member table
            $memberQuery = "INSERT INTO member (FullName, BirthDate, Gender, MobileNumber) VALUES (?, ?, ?, ?)";
            $memberStmt = $this->conn->prepare($memberQuery);
            $memberStmt->bind_param("ssss", $data['FullName'], $data['BirthDate'], $data['Gender'], $data['MobileNumber']);
            if (!$memberStmt->execute()) {
                throw new Exception("[ERROR] Failed to insert into member table: " . $this->conn->error);
            }
            $memberId = $this->conn->insert_id;
            error_log("[INFO] Member added with ID: $memberId");
    
            // Insert into patient table
            $patientQuery = "INSERT INTO patient (SurgeryDate, TypeOfSurgery, HospitalName, MID) VALUES (?, ?, ?, ?)";
            $patientStmt = $this->conn->prepare($patientQuery);
            $patientStmt->bind_param("sssi", $data['SurgeryDate'], $data['TypeOfSurgery'], $data['HospitalName'], $memberId);
            if (!$patientStmt->execute()) {
                throw new Exception("[ERROR] Failed to insert into patient table: " . $this->conn->error);
            }
            $patientId = $this->conn->insert_id;
            error_log("[INFO] Patient added with ID: $patientId");
    
            // Log action in add_patient table
            $addPatientQuery = "INSERT INTO add_patient (admin_id) VALUES (?)";
            $addPatientStmt = $this->conn->prepare($addPatientQuery);
            $addPatientStmt->bind_param("i", $adminId);
            if (!$addPatientStmt->execute()) {
                throw new Exception("[ERROR] Failed to insert into add_patient table: " . $this->conn->error);
            }
            $addId = $this->conn->insert_id;
            error_log("[INFO] Action logged with ID: $addId");
    
            // Log details in add_patient_detail table
            $detailDescription = "Added patient with ID: $patientId";
            $addPatientDetailQuery = "INSERT INTO add_patient_detail (add_id, patient_id, detail_description) VALUES (?, ?, ?)";
            $addPatientDetailStmt = $this->conn->prepare($addPatientDetailQuery);
            $addPatientDetailStmt->bind_param("iis", $addId, $patientId, $detailDescription);
            if (!$addPatientDetailStmt->execute()) {
                throw new Exception("[ERROR] Failed to insert into add_patient_detail table: " . $this->conn->error);
            }
    
            // Commit transaction
            $this->conn->commit();
            error_log("[INFO] Transaction committed successfully for Patient ID: $patientId.");
            return true;
    
        } catch (Exception $e) {
            // Rollback transaction on failure
            $this->conn->rollback();
            error_log("[ERROR] Transaction rolled back: " . $e->getMessage());
            return false;
        }
    }
    

    // Update an existing patient record
    public function updatePatient($patientId, $data) {
        $this->conn->begin_transaction();
        try {
            // Update member details
            $memberQuery = "UPDATE member SET FullName = ?, BirthDate = ?, Gender = ?, MobileNumber = ? WHERE MemberID = (SELECT MID FROM patient WHERE PatientId = ?)";
            $memberStmt = $this->conn->prepare($memberQuery);
            $memberStmt->bind_param("sssii", $data['FullName'], $data['BirthDate'], $data['Gender'], $data['MobileNumber'], $patientId);
            if (!$memberStmt->execute()) throw new Exception("[ERROR] Failed to update member");

            // Update patient details
            $patientQuery = "UPDATE patient SET SurgeryDate = ?, TypeOfSurgery = ?, HospitalName = ? WHERE PatientId = ?";
            $patientStmt = $this->conn->prepare($patientQuery);
            $patientStmt->bind_param("sssi", $data['SurgeryDate'], $data['TypeOfSurgery'], $data['HospitalName'], $patientId);
            if (!$patientStmt->execute()) throw new Exception("[ERROR] Failed to update patient");

            $this->conn->commit();
            error_log("[INFO] Patient updated successfully with ID: $patientId");
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("[ERROR] Update failed for patient ID: $patientId - " . $e->getMessage());
            return false;
        }
    }

    // Delete a patient record
    public function deletePatient($patientId) {
        $this->conn->begin_transaction();
        try {
            // Delete from add_patient_detail table
            $detailQuery = "DELETE FROM add_patient_detail WHERE patient_id = ?";
            $detailStmt = $this->conn->prepare($detailQuery);
            $detailStmt->bind_param("i", $patientId);
            if (!$detailStmt->execute()) throw new Exception("[ERROR] Failed to delete from add_patient_detail");
    
            // Delete from patient table
            $patientQuery = "DELETE FROM patient WHERE PatientId = ?";
            $patientStmt = $this->conn->prepare($patientQuery);
            $patientStmt->bind_param("i", $patientId);
            if (!$patientStmt->execute()) throw new Exception("[ERROR] Failed to delete from patient");
    
            // Delete from member table if no other patients are linked
            $memberQuery = "DELETE FROM member WHERE MemberID = (SELECT MID FROM patient WHERE PatientId = ?)";
            $memberStmt = $this->conn->prepare($memberQuery);
            $memberStmt->bind_param("i", $patientId);
            if (!$memberStmt->execute()) throw new Exception("[ERROR] Failed to delete from member");
    
            $this->conn->commit();
            error_log("[INFO] Patient deleted successfully with ID: $patientId");
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("[ERROR] Deletion failed for patient ID: $patientId - " . $e->getMessage());
            return false;
        }
    }
    
}
