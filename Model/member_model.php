<?php

require_once '../Helper FIles/my_database.php';
require_once "../Design Patterns/Observer.php";

class MemberModel implements ISubject {
    private $conn;
    private $observers = [];

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection(); // Get the Singleton DB connection
        if ($this->conn->connect_error) {
            error_log("Database connection failed: " . $this->conn->connect_error);
            die("Database connection failed: " . $this->conn->connect_error);
        }
        error_log("Database connection established successfully.");
    }

    // Register an observer
    public function registerObserver(IObserver $observer) {
        $this->observers[] = $observer;
        error_log("Observer registered successfully.");
    }

    // Notify all observers about a state change
    public function notifyObservers($message) {
        foreach ($this->observers as $observer) {
            $observer->update($message);
            error_log("Observer notified with message: {$message}");
        }
    }

    public function removeObserver(IObserver $observer) {
        foreach ($this->observers as $key => $obs) {
            if ($obs === $observer) {
                unset($this->observers[$key]);
            }
        }
    }

    // Request account creation, notifying observers
    public function requestAccountCreation($fullName, $birthDate, $gender, $mobileNumber, $username, $email, $password, $accountType, $adminId, $specialty) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
        $query = "INSERT INTO pending_members (FullName, BirthDate, Gender, MobileNumber, username, email, password, account_type, admin_id, Specialty) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("ssssssssss", $fullName, $birthDate, $gender, $mobileNumber, $username, $email, $hashedPassword, $accountType, $adminId, $specialty);
            if ($stmt->execute()) {
                $this->notifyObservers("New user signup request: $username ($email) for account type $accountType with specialty $specialty");
                $stmt->close();
                return true;
            }
            $stmt->close();
        }
    
        return false;
    }
    


        // Retrieve a specific member by ID
        public function getMemberById($memberId) {
            $query = "SELECT * FROM member WHERE MemberID = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $memberId);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            } else {
                error_log("No member found with ID: " . $memberId);
                return null;
            }
        }
    
        // Retrieve all members
        public function getAllMembers() {
            $query = "SELECT * FROM member";
            $result = $this->conn->query($query);
            
            $members = [];
            while ($row = $result->fetch_assoc()) {
                $members[] = $row;
            }
            return $members;
        }
    
        // Update an existing member's details
        public function updateMember($memberId, $data) {
            $query = "UPDATE member SET FullName = ?, BirthDate = ?, Gender = ?, MobileNumber = ?, email = ?, account_type = ? WHERE MemberID = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssssssi", $data['FullName'], $data['BirthDate'], $data['Gender'], $data['MobileNumber'], $data['email'], $data['account_type'], $memberId);
            
            if ($stmt->execute()) {
                $stmt->close();
                return true;
            } else {
                error_log("Failed to update member: " . $stmt->error);
                $stmt->close();
                return false;
            }
        }
    
        // Delete a member record
        public function deleteMember($memberId) {
            $query = "DELETE FROM member WHERE MemberID = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $memberId);
            
            if ($stmt->execute()) {
                $stmt->close();
                return true;
            } else {
                error_log("Failed to delete member: " . $stmt->error);
                $stmt->close();
                return false;
            }
        }
}

?>