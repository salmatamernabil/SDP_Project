<?php
require_once '../Helper FIles/my_database.php';

class PatientModel {
    private $conn;


    public function __construct() {
        $this->conn = Database::getInstance()->getConnection(); // Get the Singleton DB connection
        if ($this->conn->connect_error) {
            die("Database connection failed: " . $this->conn->connect_error);
        }
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
        
        $patients = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $patients[] = $row;
            }
        } else {
            error_log("Error in query or no results found: " . $this->conn->error);
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
    
            // Update patient details
            $stmt2 = $this->conn->prepare("UPDATE patient SET HospitalName = ? WHERE PatientId = ?");
            $stmt2->bind_param("si", $data['hospital_name'], $patientId);
            $stmt2->execute();
    
            // Update BMI if provided
            $stmt3 = $this->conn->prepare("UPDATE patient_bmi SET bmi_value = ? WHERE patient_id = ?");
            $stmt3->bind_param("di", $data['bmi_value'], $patientId);
            $stmt3->execute();
    
            // Commit transaction
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Update transaction failed: " . $e->getMessage());
            return false;
        }
    }
    



    // Function to add a new patient
    public function addPatient($data, $adminId) {
        // Start transaction
        $this->conn->begin_transaction();
    
        try {
            // Insert into member table
            $memberQuery = "INSERT INTO member (FullName, BirthDate, Gender, MobileNumber) VALUES (?, ?, ?, ?)";
            $memberStmt = $this->conn->prepare($memberQuery);
            $memberStmt->bind_param("ssss", $data['FullName'], $data['BirthDate'], $data['Gender'], $data['MobileNumber']);
            
            if ($memberStmt->execute()) {
                $memberId = $this->conn->insert_id;
    
                // Insert into patient table with MID reference
                $patientQuery = "INSERT INTO patient (SurgeryDate, TypeOfSurgery, HospitalName, MID) VALUES (?, ?, ?, ?)";
                $patientStmt = $this->conn->prepare($patientQuery);
                $patientStmt->bind_param("sssi", $data['SurgeryDate'], $data['TypeOfSurgery'], $data['HospitalName'], $memberId);
                
                if ($patientStmt->execute()) {
                    $patientId = $this->conn->insert_id;
    
                    // Log action in add_patient table
                    $addPatientQuery = "INSERT INTO add_patient (admin_id) VALUES (?)";
                    $addPatientStmt = $this->conn->prepare($addPatientQuery);
                    $addPatientStmt->bind_param("i", $adminId);
                    
                    if ($addPatientStmt->execute()) {
                        $addId = $this->conn->insert_id;
    
                        // Log details in add_patient_detail table
                        $detailDescription = "Added patient with ID: $patientId";
                        $addPatientDetailQuery = "INSERT INTO add_patient_detail (add_id, patient_id, detail_description) VALUES (?, ?, ?)";
                        $addPatientDetailStmt = $this->conn->prepare($addPatientDetailQuery);
                        $addPatientDetailStmt->bind_param("iis", $addId, $patientId, $detailDescription);
                        
                        if ($addPatientDetailStmt->execute()) {
                            // Commit transaction if all inserts are successful
                            $this->conn->commit();
                            return true;
                        } else {
                            throw new Exception("Failed to insert into add_patient_detail table.");
                        }
                    } else {
                        throw new Exception("Failed to insert into add_patient table.");
                    }
                } else {
                    throw new Exception("Failed to insert into patient table.");
                }
            } else {
                throw new Exception("Failed to insert into member table.");
            }
        } catch (Exception $e) {
            // Rollback transaction if there was an error
            $this->conn->rollback();
            error_log("Transaction rolled back: " . $e->getMessage());
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
            if (!$memberStmt->execute()) throw new Exception("Failed to update member");

            // Update patient details
            $patientQuery = "UPDATE patient SET SurgeryDate = ?, TypeOfSurgery = ?, HospitalName = ? WHERE PatientId = ?";
            $patientStmt = $this->conn->prepare($patientQuery);
            $patientStmt->bind_param("sssi", $data['SurgeryDate'], $data['TypeOfSurgery'], $data['HospitalName'], $patientId);
            if (!$patientStmt->execute()) throw new Exception("Failed to update patient");

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Update failed: " . $e->getMessage());
            return false;
        }
    }

    // Delete a patient record
    public function deletePatient($patientId) {
        $this->conn->begin_transaction();
        try {
            // Delete from patient table
            $patientQuery = "DELETE FROM patient WHERE PatientId = ?";
            $patientStmt = $this->conn->prepare($patientQuery);
            $patientStmt->bind_param("i", $patientId);
            if (!$patientStmt->execute()) throw new Exception("Failed to delete from patient");

            // Delete from member table if no other patients are linked
            $memberQuery = "DELETE FROM member WHERE MemberID = (SELECT MID FROM patient WHERE PatientId = ?)";
            $memberStmt = $this->conn->prepare($memberQuery);
            $memberStmt->bind_param("i", $patientId);
            if (!$memberStmt->execute()) throw new Exception("Failed to delete from member");

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Delete failed: " . $e->getMessage());
            return false;
        }
    }
    
    }
