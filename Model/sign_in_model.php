<?php
require_once '../Helper Files/my_database.php';
require_once '../Design Patterns/UserFactory.php';

class SignInModel {
    private $conn;

    public function __construct() {
        // Establish the database connection
        $this->conn = Database::getInstance()->getConnection();
        if ($this->conn->connect_error) {
            error_log("Database connection failed: " . $this->conn->connect_error);
            die("Database connection failed: " . $this->conn->connect_error);
        }
        error_log("Database connection established successfully.");
    }

    public function signIn($username, $password) {
        error_log("Attempting sign-in for username: $username");

        // Define user tables and their corresponding queries
        $userTables = [
            'doctor' => "SELECT d.MemberID AS id, d.Username AS username, d.Email AS email, 
                            d.Password, d.Specialty AS specialty, 'Doctor' AS account_type 
                            FROM doctor d WHERE d.Username = ?",
            'trainee' => "SELECT t.MemberID AS id, t.Username AS username, t.Email AS email, 
                            t.Password, t.Specialty AS specialty, 'Trainee' AS account_type 
                            FROM trainee t WHERE t.Username = ?"
        ];

        // Iterate through user tables
        foreach ($userTables as $accountType => $query) {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                error_log("User found in $accountType table with username: $username");

                if (password_verify($password, $row['Password'])) {
                    error_log("Password verified for username: $username in $accountType table.");

                    // Use the factory to create a user object
                    try {
                        $user = UserFactory::createUser($accountType, $row);

                        // Store session variables
                        $_SESSION['MemberID'] = $user->id;
                        $_SESSION['account_type'] = ucfirst($accountType);
                        error_log("User object created and session set for $accountType: $username");

                        return $user;
                    } catch (Exception $e) {
                        error_log("[ERROR] Failed to create user object: " . $e->getMessage());
                        return null;
                    }
                } else {
                    error_log("Password mismatch for username: $username in $accountType table.");
                }
            } else {
                error_log("No matching user found in $accountType table for username: $username");
            }
        }

        // Check if the user is in the pending members table
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
                return 'pending'; // Indicate pending status
            } else {
                error_log("Password mismatch for pending user: $username.");
            }
        } else {
            error_log("No matching user found in pending members table for username: $username");
        }

        // Log and return null for invalid users
        error_log("Sign-in failed for username: $username. No valid records found.");
        return null;
    }
}
?>
