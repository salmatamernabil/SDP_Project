<?php
require_once '../Helper FIles/my_database.php';
require_once '../Design Patterns/UserFactory.php';

class SignInModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        if ($this->conn->connect_error) {
            error_log("Database connection failed: " . $this->conn->connect_error);
            die("Database connection failed: " . $this->conn->connect_error);
        }
        error_log("Database connection established successfully.");
    }

    public function signIn($username, $password) {
        // Debugging: Log the start of the sign-in process
        error_log("Attempting sign-in for username: $username");

        // Check if user is in the approved doctor table
        $query = "SELECT d.MemberID AS id, d.Username AS username, d.Email AS email, 
          d.Password, d.Specialty AS specialty, 'Doctor' AS account_type 
          FROM doctor d WHERE d.Username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            error_log("User found in doctor table with username: $username");
            if (password_verify($password, $row['Password'])) {
                error_log("Password verified for username: $username in doctor table.");
                return UserFactory::createUser('Doctor', $row); // Return Doctor object
            } else {
                error_log("Password mismatch for username: $username in doctor table.");
            }
        } else {
            error_log("No matching user found in doctor table for username: $username");
        }

        // Check if user is in the approved trainee table
        $query = "SELECT t.MemberID AS id, t.Username AS username, t.Email AS email, 
        t.Password, t.Specialty AS specialty, 'Trainee' AS account_type 
        FROM trainee t WHERE t.Username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            error_log("User found in trainee table with username: $username");
            if (password_verify($password, $row['Password'])) {
                error_log("Password verified for username: $username in trainee table.");
                return UserFactory::createUser('Trainee', $row); // Return Trainee object
            } else {
                error_log("Password mismatch for username: $username in trainee table.");
            }
        } else {
            error_log("No matching user found in trainee table for username: $username");
        }

        // Check if user is in the pending members table
        $query = "SELECT password FROM pending_members WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            error_log("User found in pending members table with username: $username");
            if (password_verify($password, $row['password'])) {
                error_log("Password verified for pending user: $username.");
                return 'pending'; // Return 'pending' for users under review
            } else {
                error_log("Password mismatch for pending user: $username.");
            }
        } else {
            error_log("No matching user found in pending members table for username: $username");
        }

        // If user is neither approved nor pending, log and return invalid
        error_log("Sign-in failed for username: $username. No valid records found.");
        return null; // Return null for invalid users
    }
}
?>
