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

    // Retrieve all patients from the database with full name from member table
   public function getPatients() {
    $query = "SELECT p.*, m.FullName FROM patient p INNER JOIN member m ON p.MID = m.MemberID"; // Adjust column names if needed
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



    public function __destruct() {
        $this->conn->close();
    }
}


//  $model = new FollowUpModel();
//   $patients = $model->getPatients();

//   if (!empty($patients)) {
//       echo "<h2>Retrieved Patients:</h2>";
//       foreach ($patients as $patient) {
//     echo "Patient NAME: " . htmlspecialchars($patient['FullName']) . "<br>";
//           echo "Patient ID: " . htmlspecialchars($patient['PatientId']) . "<br>";
// //          echo "Surgery Date: " . htmlspecialchars($patient['SurgeryDate']) . "<br>";
// //          echo "Type of Surgery: " . htmlspecialchars($patient['TypeOfSurgery']) . "<br>";
// //          echo "Hospital Name: " . htmlspecialchars($patient['HospitalName']) . "<br>";
// //          echo "Member ID (MID): " . htmlspecialchars($patient['MID']) . "<br>";
// //          echo "----------------------<br>";
// }
// } else {
// //      echo "No patients found or failed to retrieve patients.";
//  }
// ?>
