<?php
require_once '../Helper FIles/my_database.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class TypesModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        if ($this->conn->connect_error) {
            error_log("Database connection failed: " . $this->conn->connect_error);
            die("Database connection failed: " . $this->conn->connect_error);
        }
        error_log("Database connection established successfully for TypesModel.");
    }

    // Insert Type details into the database
    public function insertType($data, $adminId) {
        $this->conn->begin_transaction();

        try {
            // Insert into surgery_types table
            $query = "INSERT INTO surgery_types (patient_id,surgery_type, stapler_type, number_of_staplers, black_staplers, green_staplers, yellow_staplers, blue_staplers, purple_staplers, tan_staplers, reinforcement_type, gastric_fixation, hiatus_hernia, other_surgery, other_surgery_details, estoma_size, estoma_color, bypassed_intestine_length, whole_intestine_length, roux_limb_length, perial_limb_length, closure_defect, index_surgery_type, index_surgery_time, redo_type, comments) 
                      VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param(
                "ssiiiiiiiiissssssiiiiissss",
                $data['patient_id'],
                $data['surgery_type'],
                $data['stapler_type'],
                $data['number_of_staplers'],
                $data['black_staplers'],
                $data['green_staplers'],
                $data['yellow_staplers'],
                $data['blue_staplers'],
                $data['purple_staplers'],
                $data['tan_staplers'],
                $data['reinforcement_type'],
                $data['gastric_fixation'],
                $data['hiatus_hernia'],
                $data['other_surgery'],
                $data['other_surgery_details'],
                $data['estoma_size'],
                $data['estoma_color'],
                $data['bypassed_intestine_length'],
                $data['whole_intestine_length'],
                $data['roux_limb_length'],
                $data['perial_limb_length'],
                $data['closure_defect'],
                $data['index_surgery_type'],
                $data['index_surgery_time'],
                $data['redo_type'],
                $data['comments']
            );
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert into surgery_types: " . $stmt->error);
            }
            $typeId = $this->conn->insert_id; // Get the new record ID

            // Log the admin action in add_type table
            $addTypeQuery = "INSERT INTO add_type (admin_id) VALUES (?)";
            $addTypeStmt = $this->conn->prepare($addTypeQuery);
            $addTypeStmt->bind_param("i", $adminId);
            if (!$addTypeStmt->execute()) {
                throw new Exception("Failed to insert into add_type: " . $addTypeStmt->error);
            }
            $addId = $this->conn->insert_id;

            // Insert log detail in add_type_detail table
            $detailDescription = "Added surgery details for patient ID: " . $data['patient_id'];
            $addTypeDetailQuery = "INSERT INTO add_type_detail (add_id, type_id, detail_description) 
                                    VALUES (?, ?, ?)";
            $addTypeDetailStmt = $this->conn->prepare($addTypeDetailQuery);
            $addTypeDetailStmt->bind_param("iis", $addId, $typeId, $detailDescription);
            if (!$addTypeDetailStmt->execute()) {
                throw new Exception("Failed to insert into add_type_detail: " . $addTypeDetailStmt->error);
            }

            // Commit transaction if all steps are successful
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Type insertion transaction failed: " . $e->getMessage());
            return false;
        }
    }

    // Retrieve all types
    public function getAllTypes() {
        $query = "SELECT * FROM surgery_types";
        $result = $this->conn->query($query);
        $types = [];
        while ($row = $result->fetch_assoc()) {
            $types[] = $row;
        }
        return $types;
    }

    // Retrieve a specific type by ID
    public function getTypeById($typeId) {
        $query = "SELECT * FROM surgery_types WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $typeId);
        $stmt->execute();
        $result = $stmt->get_result();
        $type = $result->fetch_assoc();
        $stmt->close();
        return $type;
    }

    // Update a specific type by ID
    public function updateType($typeId, $data) {
        $this->conn->begin_transaction();

        try {
            $query = "UPDATE surgery_types SET surgery_type = ?, stapler_type = ?, number_of_staplers = ?, black_staplers = ?, green_staplers = ?, yellow_staplers = ?, blue_staplers = ?, purple_staplers = ?, tan_staplers = ?, reinforcement_type = ?, gastric_fixation = ?, hiatus_hernia = ?, other_surgery = ?, other_surgery_details = ?, estoma_size = ?, estoma_color = ?, bypassed_intestine_length = ?, whole_intestine_length = ?, roux_limb_length = ?, perial_limb_length = ?, closure_defect = ?, index_surgery_type = ?, index_surgery_time = ?, redo_type = ?, comments = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param(
                "ssiiiiiiiiissssssiiiiissssi",
                $data['surgery_type'],
                $data['stapler_type'],
                $data['number_of_staplers'],
                $data['black_staplers'],
                $data['green_staplers'],
                $data['yellow_staplers'],
                $data['blue_staplers'],
                $data['purple_staplers'],
                $data['tan_staplers'],
                $data['reinforcement_type'],
                $data['gastric_fixation'],
                $data['hiatus_hernia'],
                $data['other_surgery'],
                $data['other_surgery_details'],
                $data['estoma_size'],
                $data['estoma_color'],
                $data['bypassed_intestine_length'],
                $data['whole_intestine_length'],
                $data['roux_limb_length'],
                $data['perial_limb_length'],
                $data['closure_defect'],
                $data['index_surgery_type'],
                $data['index_surgery_time'],
                $data['redo_type'],
                $data['comments'],
                $typeId
            );
            $stmt->execute();
            $stmt->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Type update failed: " . $e->getMessage());
            return false;
        }
    }

    // Delete a specific type by ID
    public function deleteType($typeId) {
        $this->conn->begin_transaction();

        try {
            $query = "DELETE FROM surgery_types WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $typeId);
            $stmt->execute();
            $stmt->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Type deletion failed: " . $e->getMessage());
            return false;
        }
    }
}
?>