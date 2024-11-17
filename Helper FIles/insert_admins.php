<?php
require_once '../Helper FIles/my_database.php';

class AdminSeeder {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        if ($this->conn->connect_error) {
            die("Database connection failed: " . $this->conn->connect_error);
        }
    }

    public function seedAdmins() {
        $admins = [
                 // Adding 10 more admins with BaseAdmin and SuperAdmin roles
                 ["FullName" => "Admin Eleven", "BirthDate" => "1995-11-11", "Gender" => "Male", "MobileNumber" => "2234567890", "username" => "admin11", "email" => "admin11@example.com", "password" => password_hash("password11", PASSWORD_DEFAULT), "role" => "BaseAdmin"],
                 ["FullName" => "Admin Twelve", "BirthDate" => "1996-12-12", "Gender" => "Female", "MobileNumber" => "3345678901", "username" => "admin12", "email" => "admin12@example.com", "password" => password_hash("password12", PASSWORD_DEFAULT), "role" => "BaseAdmin"],
                 ["FullName" => "Admin Thirteen", "BirthDate" => "1997-01-01", "Gender" => "Male", "MobileNumber" => "4456789012", "username" => "admin13", "email" => "admin13@example.com", "password" => password_hash("password13", PASSWORD_DEFAULT), "role" => "BaseAdmin"],
                 ["FullName" => "Admin Fourteen", "BirthDate" => "1998-02-02", "Gender" => "Female", "MobileNumber" => "5567890123", "username" => "admin14", "email" => "admin14@example.com", "password" => password_hash("password14", PASSWORD_DEFAULT), "role" => "BaseAdmin"],
                 ["FullName" => "Admin Fifteen", "BirthDate" => "1999-03-03", "Gender" => "Male", "MobileNumber" => "6678901234", "username" => "admin15", "email" => "admin15@example.com", "password" => password_hash("password15", PASSWORD_DEFAULT), "role" => "BaseAdmin"],
                 ["FullName" => "Admin Sixteen", "BirthDate" => "2000-04-04", "Gender" => "Female", "MobileNumber" => "7789012345", "username" => "admin16", "email" => "admin16@example.com", "password" => password_hash("password16", PASSWORD_DEFAULT), "role" => "SuperAdmin"],
                 ["FullName" => "Admin Seventeen", "BirthDate" => "2001-05-05", "Gender" => "Male", "MobileNumber" => "8890123456", "username" => "admin17", "email" => "admin17@example.com", "password" => password_hash("password17", PASSWORD_DEFAULT), "role" => "SuperAdmin"],
                 ["FullName" => "Admin Eighteen", "BirthDate" => "2002-06-06", "Gender" => "Female", "MobileNumber" => "9901234567", "username" => "admin18", "email" => "admin18@example.com", "password" => password_hash("password18", PASSWORD_DEFAULT), "role" => "SuperAdmin"],
                 ["FullName" => "Admin Nineteen", "BirthDate" => "2003-07-07", "Gender" => "Male", "MobileNumber" => "1012345678", "username" => "admin19", "email" => "admin19@example.com", "password" => password_hash("password19", PASSWORD_DEFAULT), "role" => "BaseAdmin"],
                 ["FullName" => "Admin Twenty", "BirthDate" => "2004-08-08", "Gender" => "Female", "MobileNumber" => "2123456789", "username" => "admin20", "email" => "admin20@example.com", "password" => password_hash("password20", PASSWORD_DEFAULT), "role" => "BaseAdmin"]
             ];
     

        foreach ($admins as $admin) {
            // Insert into member table
            $memberQuery = "INSERT INTO member (FullName, BirthDate, Gender, MobileNumber) VALUES (?, ?, ?, ?)";
            $stmtMember = $this->conn->prepare($memberQuery);
            $stmtMember->bind_param("ssss", $admin["FullName"], $admin["BirthDate"], $admin["Gender"], $admin["MobileNumber"]);
            $stmtMember->execute();
            $memberId = $this->conn->insert_id; // Get the MemberID for the admin

            // Insert into admin table
            $adminQuery = "INSERT INTO admin (MemberID, username, email, password, role, assigned_pending_members) VALUES (?, ?, ?, ?, ?, 0)";
            $stmtAdmin = $this->conn->prepare($adminQuery);
            $stmtAdmin->bind_param("issss", $memberId, $admin["username"], $admin["email"], $admin["password"], $admin["role"]);
            $stmtAdmin->execute();

            // Close statements
            $stmtMember->close();
            $stmtAdmin->close();

            echo "Inserted admin: " . $admin["username"] . " with MemberID: " . $memberId . "<br>";
        }
    }
}

// Run the seeder
$seeder = new AdminSeeder();
$seeder->seedAdmins();
?>
