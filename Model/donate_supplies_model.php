<?php
require_once '../Helper Files/my_database.php';
date_default_timezone_set('Africa/Cairo');
class DonateSuppliesModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        if ($this->conn->connect_error) {
            die("Database connection failed: " . $this->conn->connect_error);
        }
    }

    /**
     * Create a new supplies donation.
     *
     * @param array $donationData The donation data.
     * @return bool True if successful, false otherwise.
     */
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

            // Generate a random delivery time between 1 and 5 minutes
            $deliveryTime = rand(1, 5);

            // Insert into donation_supplies_item
            $queryItem = "INSERT INTO donation_supplies_item (stapler_count, brand, serial_number, delivery_time) VALUES (?, ?, ?, ?)";
            $stmtItem = $this->conn->prepare($queryItem);
            $stmtItem->bind_param("issi", $donationData['staplerCount'], $donationData['brand'], $donationData['serialNumber'], $deliveryTime);
            $stmtItem->execute();
            $itemId = $this->conn->insert_id;
            $stmtItem->close();

            // Insert into donatesupplies_verb
            $queryVerb = "INSERT INTO donatesupplies_verb (date, donor_id) VALUES (?, ?)";
            $stmtVerb = $this->conn->prepare($queryVerb);
            $stmtVerb->bind_param("si", $donationData['date'], $donorId);
            $stmtVerb->execute();
            $verbId = $this->conn->insert_id;
            $stmtVerb->close();

            // Insert into donatesupplies_verb_detail
            $queryDetail = "INSERT INTO donatesupplies_verb_detail (donation_supplies_item_id, donatesupplies_verb_id) VALUES (?, ?)";
            $stmtDetail = $this->conn->prepare($queryDetail);
            $stmtDetail->bind_param("ii", $itemId, $verbId);
            $stmtDetail->execute();
            $stmtDetail->close();

            // Commit transaction
            $this->conn->commit();

            return true; // Indicate success
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollback();
            throw $e;
        }
    }

    /**
     * Check if the delivery time has passed and update the 'delivered' status.
     *
     * @param int $itemId The ID of the donation item.
     * @return bool True if delivered, false otherwise.
     */
    public function checkDeliveryStatus($itemId) {
        // Fetch the donation item record
        $query = "SELECT created_at, delivery_time, delivered FROM donation_supplies_item WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("[ERROR] checkDeliveryStatus: Failed to prepare statement for Item ID $itemId - " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("i", $itemId);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();
        $stmt->close();
    
        if (!$item) {
            error_log("[ERROR] checkDeliveryStatus: Item ID $itemId not found.");
            return false;
        }
    
        // Check if already delivered
        if ($item['delivered'] === 'yes') {
            error_log("[INFO] checkDeliveryStatus: Item ID $itemId is already marked as delivered.");
            return true;
        }
    
        // Calculate the delivery time
        $createdAt = strtotime($item['created_at']);
        if ($createdAt === false) {
            error_log("[ERROR] checkDeliveryStatus: Invalid created_at for Item ID $itemId.");
            return false;
        }
        $deliveryTime = $item['delivery_time'] * 60; // Convert minutes to seconds
        $currentTime = time();
    
        // Log the details
        error_log("[DEBUG] checkDeliveryStatus: Item ID $itemId, createdAt: " . date('Y-m-d H:i:s', $createdAt) . ", deliveryTime (seconds): $deliveryTime, currentTime: " . date('Y-m-d H:i:s', $currentTime) . ".");
    
        // Check if the delivery time has passed
        if ($currentTime >= ($createdAt + $deliveryTime)) {
            // Update the 'delivered' status to 'yes'
            $updateQuery = "UPDATE donation_supplies_item SET delivered = 'yes' WHERE id = ?";
            $updateStmt = $this->conn->prepare($updateQuery);
            if (!$updateStmt) {
                error_log("[ERROR] checkDeliveryStatus: Failed to prepare update statement for Item ID $itemId - " . $this->conn->error);
                return false;
            }
            $updateStmt->bind_param("i", $itemId);
            if ($updateStmt->execute()) {
                if ($updateStmt->affected_rows > 0) {
                    error_log("[INFO] checkDeliveryStatus: Item ID $itemId marked as delivered.");
                    $updateStmt->close();
                    return true;
                } else {
                    error_log("[WARNING] checkDeliveryStatus: No rows updated for Item ID $itemId.");
                }
            } else {
                error_log("[ERROR] checkDeliveryStatus: Failed to execute update for Item ID $itemId - " . $updateStmt->error);
            }
            $updateStmt->close();
        } else {
            error_log("[DEBUG] checkDeliveryStatus: Item ID $itemId not yet delivered. Current time: " . date('Y-m-d H:i:s', $currentTime) . " is less than expected delivery time: " . date('Y-m-d H:i:s', $createdAt + $deliveryTime) . ".");
        }
    
        return false;
    }
    

    /**
     * Retrieve a specific donation record by ID.
     *
     * @param int $donationId The ID of the donation.
     * @return array|null The donation data or null if not found.
     */
    /**
 * Retrieve a specific donation record by ID.
 *
 * @param int $donationId The ID of the donation.
 * @return array|null The donation data or null if not found.
 */
public function getDonationById($donationId) {
    $query = "SELECT dm.id AS donor_id, dm.name, dm.email, dm.phone, dsi.stapler_count, dsi.brand, dsi.serial_number, dsi.created_at, dsi.delivery_time, dsi.delivered
              FROM donatesupplies_verb_detail dvd
              JOIN donation_supplies_item dsi ON dvd.donation_supplies_item_id = dsi.id
              JOIN donatesupplies_verb dv ON dvd.donatesupplies_verb_id = dv.id
              JOIN donormember dm ON dv.donor_id = dm.id
              WHERE dvd.id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $donationId);
    $stmt->execute();
    $result = $stmt->get_result();
    $donation = $result->fetch_assoc();
    $stmt->close();

    // Check delivery status if not already delivered
    if ($donation && $donation['delivered'] === 'no') {
        $this->checkDeliveryStatus($donation['id']);
    }

    return $donation;
}

    /**
     * Retrieve all donations.
     *
     * @return array An array of all donations.
     */
    public function getAllDonations(): array {
        $query = "SELECT * FROM donation_supplies_item";
        $result = $this->conn->query($query);
        $donations = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $donations[] = $row;
            }
            $result->free();
        } else {
            error_log("DonateSuppliesModel: Query failed - " . $this->conn->error);
        }

        return $donations;
    }
   

    /**
 * Approve a donation item by setting the 'approved' field to 'yes'.
 *
 * @param int $itemId The ID of the donation item.
 * @return bool True if successful, false otherwise.
 */
public function approveDonationItem($itemId) {
    $query = "UPDATE donation_supplies_item SET approved = 'yes' WHERE id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $success = $stmt->affected_rows > 0;
    $stmt->close();
    return $success;
}


/**
 * Reject a donation item by setting the 'approved' field to 'no'.
 *
 * @param int $itemId The ID of the donation item.
 * @return bool True if successful, false otherwise.
 */
public function rejectDonationItem($itemId) {
    $query = "UPDATE donation_supplies_item SET rejected = 'yes' WHERE id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $success = $stmt->affected_rows > 0;
    $stmt->close();
    return $success;
}
    /**
     * Delete a donation by ID.
     *
     * @param int $donationId The ID of the donation.
     * @return bool True if successful, false otherwise.
     */
    public function deleteDonation($donationId) {
        // Begin transaction to maintain data consistency across tables
        $this->conn->begin_transaction();
        try {
            // Delete from donatesupplies_verb_detail
            $queryDetail = "DELETE FROM donatesupplies_verb_detail WHERE id = ?";
            $stmtDetail = $this->conn->prepare($queryDetail);
            $stmtDetail->bind_param("i", $donationId);
            $stmtDetail->execute();
            $stmtDetail->close();

            // Commit transaction
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
