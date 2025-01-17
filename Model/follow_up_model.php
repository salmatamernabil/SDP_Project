<?php
require_once '../Helper FIles\my_database.php'; // Adjust the path to your database connection file

class FollowUpModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection(); // Assumes returnConnection() is defined in your database helper file
        if ($this->conn->connect_error) {
            die("Database connection failed: " . $this->conn->connect_error);
        }
    }


     /**
     * Retrieve the latest follow-up data for a specific patient.
     *
     * @param int $patientId The ID of the patient.
     * @return array The follow-up data for the patient.
     */
    public function getFollowUpDataByPatientId($patientId) {
        error_log("PatientId received: " . $patientId);
              
        if (!$this->conn) {
            error_log("Database connection is null or not initialized.");
            throw new Exception("Database connection is not established.");
        }
       
        $sqlTest = "SELECT 1";
        if (!$this->conn->query($sqlTest)) {
            error_log("Database connection test failed: " . $this->conn->error);
            throw new Exception("Database connection test failed: " . $this->conn->error);
        }
        $query = "
        WITH LatestBMI AS (
            SELECT patient_id, MAX(bmi_id) AS latest_bmi_id
            FROM patient_bmi
            GROUP BY patient_id
        ),
        LatestComorbidity AS (
            SELECT patient_id, MAX(comorbidity_id) AS latest_comorbidity_id
            FROM comorbidity
            GROUP BY patient_id
        ),
        LatestSurgeryTypes AS (
            SELECT patient_id, MAX(id) AS latest_surgery_type_id
            FROM surgery_types
            GROUP BY patient_id
        ),
        LatestComplications AS (
            SELECT PatientId, MAX(ComplicationId) AS latest_complication_id
            FROM patient_complcation
            GROUP BY PatientId
        )
        SELECT 
            p.PatientId, 
            m.FullName, 
            m.BirthDate, 
            m.Gender, 
            m.MobileNumber,
            p.SurgeryDate, 
            p.TypeOfSurgery, 
            p.HospitalName,
            bmi.bmi_value, 
            bmi.result,
            com.functional_status, 
            com.diabetes, 
            com.habic, 
            com.diabetes_duration, 
            com.hypertension, 
            com.lipid_profile, 
            com.reflux, 
            com.fatty_liver, 
            com.gynecological,
            types.surgery_type, 
            types.stapler_type, 
            types.number_of_staplers, 
            types.comments,
            comp.Intraoperative, 
            comp.Postoperative, 
            comp.Discharge, 
            comp.NumberOfDays
        FROM patient p
        INNER JOIN member m ON p.MID = m.MemberID
        LEFT JOIN LatestBMI lbmi ON p.PatientId = lbmi.patient_id
        LEFT JOIN patient_bmi bmi ON bmi.bmi_id = lbmi.latest_bmi_id
        LEFT JOIN LatestComorbidity lcom ON p.PatientId = lcom.patient_id
        LEFT JOIN comorbidity com ON com.comorbidity_id = lcom.latest_comorbidity_id
        LEFT JOIN LatestSurgeryTypes ltypes ON p.PatientId = ltypes.patient_id
        LEFT JOIN surgery_types types ON types.id = ltypes.latest_surgery_type_id
        LEFT JOIN LatestComplications lcomp ON p.PatientId = lcomp.PatientId
        LEFT JOIN patient_complcation comp ON comp.ComplicationId = lcomp.latest_complication_id
        WHERE p.PatientId = ?
        GROUP BY 
            p.PatientId, m.FullName, m.BirthDate, m.Gender, m.MobileNumber,
            p.SurgeryDate, p.TypeOfSurgery, p.HospitalName,
            bmi.bmi_value, bmi.result,
            com.functional_status, com.diabetes, com.habic, com.diabetes_duration, 
            com.hypertension, com.lipid_profile, com.reflux, com.fatty_liver, com.gynecological,
            types.surgery_type, types.stapler_type, types.number_of_staplers, types.comments,
            comp.Intraoperative, comp.Postoperative, comp.Discharge, comp.NumberOfDays;
                
                
              ";
              $stmt = $this->conn->prepare($query);
              $stmt->bind_param("i", $patientId);
              $stmt->execute();
              $result = $stmt->get_result();
              if ($result) {
                  $data = $result->fetch_all(MYSQLI_ASSOC);
                  error_log("Query Result: " . print_r($data, true));
                  return $data;
              } else {
                  error_log("Query failed or returned no results.");
                  return [];
              }
              
    }
    
    // Retrieve all patients from the database with full name from member table
    public function getPatients() {
        $query = "
       WITH LatestBMI AS (
    SELECT patient_id, MAX(bmi_id) AS latest_bmi_id
    FROM patient_bmi
    GROUP BY patient_id
),
LatestComorbidity AS (
    SELECT patient_id, MAX(comorbidity_id) AS latest_comorbidity_id
    FROM comorbidity
    GROUP BY patient_id
),
LatestSurgeryTypes AS (
    SELECT patient_id, MAX(id) AS latest_surgery_type_id
    FROM surgery_types
    GROUP BY patient_id
),
LatestComplications AS (
    SELECT PatientId, MAX(ComplicationId) AS latest_complication_id
    FROM patient_complcation
    GROUP BY PatientId
)
SELECT 
    p.PatientId, 
    m.FullName, 
    m.BirthDate, 
    m.Gender, 
    m.MobileNumber,
    p.SurgeryDate, 
    p.TypeOfSurgery, 
    p.HospitalName,
    bmi.bmi_value, 
    bmi.result,
    com.functional_status, 
    com.diabetes, 
    com.habic, 
    com.diabetes_duration, 
    com.hypertension, 
    com.lipid_profile, 
    com.reflux, 
    com.fatty_liver, 
    com.gynecological,
    types.surgery_type, 
    types.stapler_type, 
    types.number_of_staplers, 
    types.comments,
    comp.Intraoperative, 
    comp.Postoperative, 
    comp.Discharge, 
    comp.NumberOfDays
FROM patient p
INNER JOIN member m ON p.MID = m.MemberID
LEFT JOIN LatestBMI lbmi ON p.PatientId = lbmi.patient_id
LEFT JOIN patient_bmi bmi ON bmi.bmi_id = lbmi.latest_bmi_id
LEFT JOIN LatestComorbidity lcom ON p.PatientId = lcom.patient_id
LEFT JOIN comorbidity com ON com.comorbidity_id = lcom.latest_comorbidity_id
LEFT JOIN LatestSurgeryTypes ltypes ON p.PatientId = ltypes.patient_id
LEFT JOIN surgery_types types ON types.id = ltypes.latest_surgery_type_id
LEFT JOIN LatestComplications lcomp ON p.PatientId = lcomp.PatientId
LEFT JOIN patient_complcation comp ON comp.ComplicationId = lcomp.latest_complication_id
GROUP BY 
    p.PatientId, m.FullName, m.BirthDate, m.Gender, m.MobileNumber,
    p.SurgeryDate, p.TypeOfSurgery, p.HospitalName,
    bmi.bmi_value, bmi.result,
    com.functional_status, com.diabetes, com.habic, com.diabetes_duration, 
    com.hypertension, com.lipid_profile, com.reflux, com.fatty_liver, com.gynecological,
    types.surgery_type, types.stapler_type, types.number_of_staplers, types.comments,
    comp.Intraoperative, comp.Postoperative, comp.Discharge, comp.NumberOfDays;
        
        
        
        ";
        $result = $this->conn->query($query);
    
        $patients = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $patients[] = $row;
            }
        }
        return $patients;
    }
    

    // Create a new patient record and related member record
    public function createPatient($data) {
        $this->conn->begin_transaction();

        try {
            // Step 1: Insert into member table
            $memberQuery = "INSERT INTO member (FullName, BirthDate, Gender, MobileNumber) VALUES (?, ?, ?, ?)";
            $memberStmt = $this->conn->prepare($memberQuery);
            $memberStmt->bind_param("ssss", $data['FullName'], $data['BirthDate'], $data['Gender'], $data['MobileNumber']);
            
            if (!$memberStmt->execute()) {
                throw new Exception("Failed to insert into member: " . $memberStmt->error);
            }
            $memberId = $this->conn->insert_id; // Get the new MemberID

            // Step 2: Insert into patient table with MID reference
            $patientQuery = "INSERT INTO patient (SurgeryDate, TypeOfSurgery, HospitalName, MID) VALUES (?, ?, ?, ?)";
            $patientStmt = $this->conn->prepare($patientQuery);
            $patientStmt->bind_param("sssi", $data['SurgeryDate'], $data['TypeOfSurgery'], $data['HospitalName'], $memberId);
            
            if (!$patientStmt->execute()) {
                throw new Exception("Failed to insert into patient: " . $patientStmt->error);
            }
            $patientId = $this->conn->insert_id; // Get the new PatientId

            // Commit transaction if all inserts are successful
            $this->conn->commit();
            return $patientId;
        } catch (Exception $e) {
            // Rollback transaction if there was an error
            $this->conn->rollback();
            error_log("Failed to create patient: " . $e->getMessage());
            return false;
        }
    }

    // Retrieve a specific patient by ID
    public function getPatientById($patientId) {
        $query = "SELECT p.*, m.FullName FROM patient p INNER JOIN member m ON p.MID = m.MemberID WHERE p.PatientId = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null; // Return null if patient not found
        }
    }

    // Update an existing patient and member record
    public function updatePatient($patientId, $data) {
        $this->conn->begin_transaction();

        try {
            // Step 1: Update member table
            $memberQuery = "UPDATE member SET FullName = ?, BirthDate = ?, Gender = ?, MobileNumber = ? WHERE MemberID = (SELECT MID FROM patient WHERE PatientId = ?)";
            $memberStmt = $this->conn->prepare($memberQuery);
            $memberStmt->bind_param("sssii", $data['FullName'], $data['BirthDate'], $data['Gender'], $data['MobileNumber'], $patientId);
            
            if (!$memberStmt->execute()) {
                throw new Exception("Failed to update member: " . $memberStmt->error);
            }

            // Step 2: Update patient table
            $patientQuery = "UPDATE patient SET SurgeryDate = ?, TypeOfSurgery = ?, HospitalName = ? WHERE PatientId = ?";
            $patientStmt = $this->conn->prepare($patientQuery);
            $patientStmt->bind_param("sssi", $data['SurgeryDate'], $data['TypeOfSurgery'], $data['HospitalName'], $patientId);
            
            if (!$patientStmt->execute()) {
                throw new Exception("Failed to update patient: " . $patientStmt->error);
            }

            // Commit transaction if both updates are successful
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Rollback transaction if there was an error
            $this->conn->rollback();
            error_log("Failed to update patient: " . $e->getMessage());
            return false;
        }
    }

    // Delete a patient and their corresponding member record
    public function deletePatient($patientId) {
        $this->conn->begin_transaction();

        try {
            // Step 1: Delete from patient table
            $deletePatientQuery = "DELETE FROM patient WHERE PatientId = ?";
            $deletePatientStmt = $this->conn->prepare($deletePatientQuery);
            $deletePatientStmt->bind_param("i", $patientId);
            
            if (!$deletePatientStmt->execute()) {
                throw new Exception("Failed to delete patient: " . $deletePatientStmt->error);
            }

            // Step 2: Delete from member table (assuming cascade delete is not enabled)
            $deleteMemberQuery = "DELETE FROM member WHERE MemberID = (SELECT MID FROM patient WHERE PatientId = ?)";
            $deleteMemberStmt = $this->conn->prepare($deleteMemberQuery);
            $deleteMemberStmt->bind_param("i", $patientId);
            
            if (!$deleteMemberStmt->execute()) {
                throw new Exception("Failed to delete member: " . $deleteMemberStmt->error);
            }

            // Commit transaction if both deletions are successful
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Rollback transaction if there was an error
            $this->conn->rollback();
            error_log("Failed to delete patient: " . $e->getMessage());
            return false;
        }
    }



}


// ?>
