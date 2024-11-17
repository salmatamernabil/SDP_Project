<?php
require_once '../Helper FIles/my_database.php';

class SignInModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // Function to check the user status
    public function checkUserStatus($username, $password) {
        // Check if user is in approved users table
        $query = "SELECT password FROM account_member WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Verify the password against the hash
            if (password_verify($password, $row['password'])) {
                return 'approved';
            }
        }

        // Check if user is in pending members table
        $query = "SELECT password FROM pending_members WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Verify the password against the hash
            if (password_verify($password, $row['password'])) {
                return 'pending';
            }
        }

        // If user is neither approved nor pending, return invalid
        return 'invalid';
    }
}
?>
