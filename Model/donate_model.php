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
            // Insert into donormember
            $queryDonor = "INSERT INTO donormember (name, email, phone) VALUES (?, ?, ?)";
            $stmtDonor = $this->conn->prepare($queryDonor);
            $stmtDonor->bind_param("sss", $donationData['name'], $donationData['email'], $donationData['phone']);
            $stmtDonor->execute();
            $donorId = $this->conn->insert_id;
            $stmtDonor->close();

            // Insert into donationobject
            $queryDonationObject = "INSERT INTO donationobject (course, amount, donation_type, date) VALUES (?, ?, ?, ?)";
            $stmtDonationObject = $this->conn->prepare($queryDonationObject);
            $stmtDonationObject->bind_param("sdss", $donationData['course'], $donationData['amount'], $donationData['payment_type'], $donationData['date']);
            $stmtDonationObject->execute();
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
            return true; // Indicate success
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollback();
            throw $e;
        }
    }

    // Retrieve a specific donation record by ID from the related tables
    public function getDonationById($donationId) {
        $query = "SELECT dm.id AS donor_id, dm.name, dm.email, dm.phone, dobj.course, dobj.amount, dobj.donation_type, dobj.date
                  FROM donate_verb_detail dvd
                  JOIN donationobject dobj ON dvd.donationobject_id = dobj.id
                  JOIN donateverb dv ON dvd.donateverb_id = dv.id
                  JOIN donormember dm ON dv.donor_id = dm.id
                  WHERE dvd.id = ?";
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

    // Update donation details
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

            // Update donationobject details
            $queryDonationObject = "UPDATE donationobject SET course = ?, amount = ?, donation_type = ?, date = ? WHERE id = ?";
            $stmtDonationObject = $this->conn->prepare($queryDonationObject);
            $stmtDonationObject->bind_param("sdssi", $updatedData['course'], $updatedData['amount'], $updatedData['payment_type'], $updatedData['date'], $updatedData['donationobject_id']);
            $stmtDonationObject->execute();
            $stmtDonationObject->close();

            // Commit transaction
            $this->conn->commit();
            return true; // Indicate success
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollback();
            throw $e;
        }
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
