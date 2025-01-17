<?php
require_once '../Helper Files/my_database.php';

class DonateModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();// Retrieve connection from db_connection.php
        if ($this->conn->connect_error) {
            die("Database connection failed: " . $this->conn->connect_error);
        }
    }

    // Create a new donor and insert donation data into related tables
    public function createDonation($donationData) {
        // Start transaction to ensure atomic operations
        $this->conn->begin_transaction();
    
        try {
            // Get the course data from the CourseModel
            $courseModel = new CourseModel();
            $course = $courseModel->getCourseById($donationData['course_id']); // Retrieve the course using course_id
    
            // If the course doesn't exist, throw an error
            if (!$course) {
                throw new Exception("Course not found.");
            }
    
            // Insert into donormember
            $queryDonor = "INSERT INTO donormember (name, email, phone) VALUES (?, ?, ?)";
            $stmtDonor = $this->conn->prepare($queryDonor);
            $stmtDonor->bind_param("sss", $donationData['name'], $donationData['email'], $donationData['phone']);
            $stmtDonor->execute();
            $donorId = $this->conn->insert_id;
            $stmtDonor->close();
    
            $queryDonationObject = "INSERT INTO donationobject (course_id, course, amount, donation_type, date) VALUES (?, ?, ?, ?, ?)";
            $stmtDonationObject = $this->conn->prepare($queryDonationObject);
            
            // Bind parameters to the statement
            $stmtDonationObject->bind_param(
                "issds", 
                $donationData['course_id'],  // Integer: course_id
                $donationData['course_name'], // String: course_name
                $donationData['amount'],      // Double: amount
                $donationData['payment_type'], // String: donation_type
                $donationData['date']         // String: date
            );
            
            // Execute the statement
            $stmtDonationObject->execute();
            
            // Retrieve the insert ID
            $donationObjectId = $this->conn->insert_id;
            
           
            
            
          
            $stmtDonationObject->close();

            // Insert into donateverb
            $queryDonateVerb = "INSERT INTO donateverb (date, donor_id) VALUES (?, ?)";
            $stmtDonateVerb = $this->conn->prepare($queryDonateVerb);
            $stmtDonateVerb->bind_param("si", $donationData['date'], $donorId);
            $stmtDonateVerb->execute();
            $donateVerbId = $this->conn->insert_id;
            $stmtDonateVerb->close();
    
            // Insert into donate_verb_detail
            $queryDonateVerbDetail = "INSERT INTO donate_verb_detail (donationobject_id, donateverb_id) VALUES (?, ?)";
            $stmtDonateVerbDetail = $this->conn->prepare($queryDonateVerbDetail);
            $stmtDonateVerbDetail->bind_param("ii", $donationObjectId, $donateVerbId);
            $stmtDonateVerbDetail->execute();
            $stmtDonateVerbDetail->close();
    
            // Commit transaction
            $this->conn->commit();
    
            // Update donations_received in the course model
           
           ///////////////////
           /////////////////////
           ///////////////////////////////////////////////
            //$courseModel->updateCourseDonation($course['course_id'], $donationData['amount']); // Add donation amount to course donations_received
    ///////////////////////////////////
            /////////////////////////////
            //////////////////////////////
            return true; // Indicate success
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollback();
            throw $e;
        }
    }

    public function updateDonationApprovalStatus($donationId) {
        $query = "UPDATE donationobject SET approved = 'yes' WHERE id = ?";
        $stmt = $this->conn->prepare($query);
    
        if (!$stmt) {
            error_log("[ERROR] Failed to prepare statement for updating donation approval: " . $this->conn->error);
            return false;
        }
    
        $stmt->bind_param("i", $donationId);
    
        $success = $stmt->execute();
    
        if ($success) {
            error_log("[INFO] Donation ID $donationId marked as approved.");
        } else {
            error_log("[ERROR] Failed to update approval status for donation ID $donationId: " . $stmt->error);
        }
    
        $stmt->close();
    
        return $success;
    }
    
 
    public function updateCourseDonation($courseId, $amount) {
        $query = "UPDATE course SET donations_received = donations_received + ? WHERE course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("di", $amount, $courseId);
        $success = $stmt->execute();
        $stmt->close();
    
        return $success;
    }
    
    public function getDonationsByType($donationType) {
        $query = "SELECT dobj.id AS id, dm.id AS donor_id, dm.name, dm.email, dm.phone, 
          dobj.course_id, dobj.course, dobj.amount, dobj.donation_type, dobj.date, dobj.approved
          FROM donationobject dobj
          JOIN donormember dm ON dobj.id = dm.id
          WHERE dobj.donation_type = ? AND dobj.approved = 'no'";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $donationType);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $donations = [];
        while ($row = $result->fetch_assoc()) {
            $donations[] = $row;
        }
        $stmt->close();
    
        return $donations;
    }

    
    public function updateDonation($donationId, $updatedData) {
        // Begin transaction to maintain data consistency across tables
        $this->conn->begin_transaction();
        try {
            // Update donormember details
            $queryDonor = "UPDATE donormember SET name = ?, email = ?, phone = ? WHERE id = ?";
            $stmtDonor = $this->conn->prepare($queryDonor);
            $stmtDonor->bind_param("sssi", $updatedData['name'], $updatedData['email'], $updatedData['phone'], $updatedData['donor_id']);
            $stmtDonor->execute();
            $stmtDonor->close();
    
            // Get the course data for the update (this is the same logic as for createDonation)
            $courseModel = new CourseModel();
            $course = $courseModel->getCourseById($updatedData['course_id']); // Retrieve the course by course_id
    
            // If the course doesn't exist, throw an error
            if (!$course) {
                throw new Exception("Course not found.");
            }
    
            // Update donationobject details (including course_id and course_name)
            $queryDonationObject = "UPDATE donationobject SET course_id = ?, course_name = ?, amount = ?, donation_type = ?, date = ? WHERE id = ?";
            $stmtDonationObject = $this->conn->prepare($queryDonationObject);
            $stmtDonationObject->bind_param("isdsdi", $course['course_id'], $course['course_name'], $updatedData['amount'], $updatedData['payment_type'], $updatedData['date'], $updatedData['donationobject_id']);
            $stmtDonationObject->execute();
            $stmtDonationObject->close();
    
            // Commit transaction
            $this->conn->commit();
    
            // Update donations_received in the course model
            $courseModel->updateCourseDonation($course['course_id'], $updatedData['amount']); // Update the donations_received
    
            return true; // Indicate success
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollback();
            throw $e;
        }
    }

    // Retrieve a specific donation record by ID from the related tables
    public function getDonationById($donationId) {
        $query = "SELECT dm.id AS donor_id, dm.name, dm.email, dm.phone, dobj.course_id, dobj.course, dobj.amount, dobj.donation_type, dobj.date
        FROM donate_verb_detail dvd
        JOIN donationobject dobj ON dvd.donationobject_id = dobj.id
        JOIN donormember dm ON dvd.donateverb_id = dm.id
        WHERE dvd.id = ?
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $donationId);
        $stmt->execute();
        $result = $stmt->get_result();
        $donation = $result->fetch_assoc();
        $stmt->close();
        return $donation;
    }

    // Retrieve all donations
    public function getAllDonations() {
        $query = "SELECT dm.id AS donor_id, dm.name, dm.email, dm.phone, dobj.course, dobj.amount, dobj.donation_type, dobj.date
                  FROM donate_verb_detail dvd
                  JOIN donationobject dobj ON dvd.donationobject_id = dobj.id
                  JOIN donateverb dv ON dvd.donateverb_id = dv.id
                  JOIN donormember dm ON dv.donor_id = dm.id";
        $result = $this->conn->query($query);
        $donations = [];
        while ($row = $result->fetch_assoc()) {
            $donations[] = $row;
        }
        return $donations;
    }

   
    // Delete a donation by ID
    public function deleteDonation($donationId) {
        // Begin transaction to maintain data consistency across tables
        $this->conn->begin_transaction();
        try {
            // Delete from donate_verb_detail
            $queryDonateVerbDetail = "DELETE FROM donate_verb_detail WHERE id = ?";
            $stmtDonateVerbDetail = $this->conn->prepare($queryDonateVerbDetail);
            $stmtDonateVerbDetail->bind_param("i", $donationId);
            $stmtDonateVerbDetail->execute();
            $stmtDonateVerbDetail->close();

            // Delete related records in other tables will cascade due to foreign keys
            $this->conn->commit();
            return true; // Indicate success
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollback();
            throw $e;
        }
    }
}
?>
