<?php
require_once '../Helper FIles/my_database.php';
require_once '../Design Patterns/Observer.php';
require_once '../Design Patterns/Decorater.php';

class AdminModel implements IObserver {
    private $conn;
    private $currentAdminInstance;


    public function __construct() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->conn = Database::getInstance()->getConnection(); // Get the Singleton DB connection
        if ($this->conn->connect_error) {
            die("Database connection failed: " . $this->conn->connect_error);
        }

        // Initialize currentAdminInstance
        if (isset($_SESSION['admin_id'])) {
            $adminId = $_SESSION['admin_id'];
            $adminInstance = $this->getAdminInstanceById($adminId);
            if ($adminInstance) {
                $this->currentAdminInstance = $adminInstance;
            } else {
                // Default to BaseAdmin if not found
                $this->currentAdminInstance = new BaseAdmin();
            }
        } else {
            // Default to BaseAdmin if no admin_id in session
            $this->currentAdminInstance = new BaseAdmin();
        }
    }

    // Method to set the current admin instance
    public function setCurrentAdminInstance($adminInstance) {
        $this->currentAdminInstance = $adminInstance;
    }

    // Method to get the current admin instance
    public function getCurrentAdminInstance() {
        return $this->currentAdminInstance;
    }

    // Rest of your methods...

    public function getAdminInstanceById($adminId) {
        $query = "SELECT admin_id, username, role FROM admin WHERE admin_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $role = $row['role'];
    
            // Instantiate the admin object based on the role
            $adminInstance = new BaseAdmin(); // Start with BaseAdmin
            if ($role === 'SuperAdmin') {
                $adminInstance = new SuperAdmin($adminInstance);
            } elseif ($role === 'ChiefAdmin') {
                $adminInstance = new ChiefAdmin($adminInstance);
            }
    
            return $adminInstance;
        }
        return null; // Return null if admin not found
    }


    // Update method from the IObserver interface
    public function update($message) {
        // Initialize the session notifications array if not already set
        if (!isset($_SESSION['notifications'])) {
            $_SESSION['notifications'] = [];
        }
        // Add the notification message to the session
        $_SESSION['notifications'][] = $message;

        // Alternatively, you can increment a count variable in the session
        if (!isset($_SESSION['notification_count'])) {
            $_SESSION['notification_count'] = 0;
        }
        $_SESSION['notification_count'] += 1;
    }
    // Consolidated function to upgrade, wrap, and log
    // Upgrade admin's role, wrap with decorators, and log the action
    // Consolidated function to upgrade, wrap, and log
    public function upgradeAdminRole($adminId) {
        // Step 1: Retrieve the current role and instance from the database
        $query = "SELECT role FROM admin WHERE admin_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
        $result = $stmt->get_result();

        
    

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $currentRole = $row['role'];

            // Step 2: Retrieve the existing instance
            $existingAdminInstance = $this->getAdminInstanceById($adminId);
            
            if (!$existingAdminInstance) {
                throw new Exception("Admin instance not found for ID: $adminId");
            }
            
            // Step 3: Determine the new role and apply the appropriate decorator
            $newRole = null;
            if ($currentRole === 'BaseAdmin') {
                $existingAdminInstance = new SuperAdmin($existingAdminInstance);
                $newRole = 'SuperAdmin';
            } elseif ($currentRole === 'SuperAdmin') {
                $existingAdminInstance = new ChiefAdmin($existingAdminInstance);
                $newRole = 'ChiefAdmin';
            } else {
                return false; // Already ChiefAdmin or unsupported role
            }

            // Update the current admin instance for potential future use
            $this->setCurrentAdminInstance($existingAdminInstance);

            // Step 4: Update the role in the `admin` table
            $updateRoleQuery = "UPDATE admin SET role = ? WHERE admin_id = ?";
            $updateStmt = $this->conn->prepare($updateRoleQuery);
            $updateStmt->bind_param("si", $newRole, $adminId);
            $updateStmt->execute();

            // Step 5: Insert into `upgrade_admin` to log the upgrade action
            $upgradingAdminId = $_SESSION['admin_id'];
            $insertUpgradeQuery = "INSERT INTO upgrade_admin (admin_id, new_role) VALUES (?, ?)";
            $insertUpgradeStmt = $this->conn->prepare($insertUpgradeQuery);
            $insertUpgradeStmt->bind_param("is", $upgradingAdminId, $newRole);
            $insertUpgradeStmt->execute();
            $upgradeId = $this->conn->insert_id;

            // Step 6: Insert into `upgrade_admin_detail` to record the specific admin that was upgraded
            $detailDescription = "Upgraded admin with ID: $adminId to new role: $newRole";
            $insertDetailQuery = "INSERT INTO upgrade_admin_detail (upgrade_id, upgraded_admin_id, detail_description) VALUES (?, ?, ?)";
            $insertDetailStmt = $this->conn->prepare($insertDetailQuery);
            $insertDetailStmt->bind_param("iis", $upgradeId, $adminId, $detailDescription);
            $insertDetailStmt->execute();

            // Return the upgraded instance for further operations
            return $existingAdminInstance;
        }

        return null;
    }

// Fetch all pending accounts
    public function getPendingAccounts($adminId) {
        $query = "SELECT * FROM pending_members WHERE admin_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
        $result = $stmt->get_result();
        $pendingAccounts = [];
    
        while ($row = $result->fetch_assoc()) {
            $pendingAccounts[] = $row;
        }
    
        return $pendingAccounts;
    }
    

       // Method to get the current role of an admin by their ID
       public function getCurrentAdminRole($adminId) {
        $query = "SELECT role FROM admin WHERE admin_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['role'];
        }
        return null; // Return null if no role is found
    }

    public function getUpgradeableAdmins($currentAdminId) {
        $query = "SELECT admin_id, username, role FROM admin WHERE role != 'ChiefAdmin' AND admin_id != ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $currentAdminId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAdminById($adminId) {
        $query = "SELECT admin_id, username, role FROM admin WHERE admin_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null; // Return null if admin not found
    }
    
    // Upgrade admin's role in the database
    public function updateAdminRole($adminId, $newRole) {
        $query = "UPDATE admin SET role = ? WHERE admin_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $newRole, $adminId);
        $stmt->execute();
    }

    public function getAllAdmins() {
        $query = "SELECT admin_id, username FROM admin"; // Select `admin_id`
        $result = $this->conn->query($query);
        $admins = [];
    
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $admins[] = $row; // Each row will now include 'admin_id' and 'username'
            }
        }
        return $admins;
    }
    
    
// Approve an account
public function approveAccount($username) {
    // Retrieve user details from `pending_members`
    $selectQuery = "SELECT * FROM pending_members WHERE username = ?";
    $selectStmt = $this->conn->prepare($selectQuery);
    if ($selectStmt) {
        $selectStmt->bind_param("s", $username);
        $selectStmt->execute();
        $result = $selectStmt->get_result();
        $user = $result->fetch_assoc();
        $selectStmt->close();

        if ($user) {
            // Insert into `member` table
            $insertMemberQuery = "INSERT INTO member (FullName, BirthDate, Gender, MobileNumber) VALUES (?, ?, ?, ?)";
            $insertMemberStmt = $this->conn->prepare($insertMemberQuery);
            if ($insertMemberStmt) {
                $insertMemberStmt->bind_param("ssss", $user['FullName'], $user['BirthDate'], $user['Gender'], $user['MobileNumber']);
                if ($insertMemberStmt->execute()) {
                    $memberId = $this->conn->insert_id; // Get the new MemberID

                    // Determine account type and insert into respective table
                    $accountType = strtolower($user['account_type']);
                    $insertSpecificTable = null;

                    if ($accountType === 'doctor') {
                        $insertSpecificTable = "INSERT INTO doctor (MemberID, Username, Email, Password, Specialty) VALUES (?, ?, ?, ?, ?)";
                    } elseif ($accountType === 'trainee') {
                        $insertSpecificTable = "INSERT INTO trainee (MemberID, Username, Email, Password, Specialty) VALUES (?, ?, ?, ?, ?)";
                    }
                    
                    if ($insertSpecificTable) {
                        $specificStmt = $this->conn->prepare($insertSpecificTable);
                        $specificStmt->bind_param(
                            "issss",
                            $memberId,
                            $user['username'],
                            $user['email'],
                            $user['password'], // Assuming the password is already hashed
                            $user['Specialty']
                        );
                    
                        if (!$specificStmt->execute()) {
                            throw new Exception("Failed to insert into {$accountType} table: " . $specificStmt->error);
                        }
                    
                        $specificStmt->close();
                    }
                    

                    // Log the approval in `approve_account_member`
                    $adminId = $_SESSION['admin_id']; // Assuming the admin's ID is stored in the session
                    $insertApproveQuery = "INSERT INTO approve_account_member (admin_id) VALUES (?)";
                    $insertApproveStmt = $this->conn->prepare($insertApproveQuery);
                    $insertApproveStmt->bind_param("i", $adminId);
                    if (!$insertApproveStmt->execute()) {
                        throw new Exception("Failed to insert into approve_account_member");
                    }
                    $approveId = $this->conn->insert_id; // Get the newly created approve_id
                    $insertApproveStmt->close();

                    // Log details in `approve_account_member_detail`
                    $detailDescription = "Approved account for user: $username";
                    $insertDetailQuery = "INSERT INTO approve_account_member_detail (approve_id, doctor_id, trainee_id, detail_description) VALUES (?, ?, ?, ?)";
                    $insertDetailStmt = $this->conn->prepare($insertDetailQuery);

                    $doctorId = ($accountType === 'doctor') ? $memberId : null;
                    $traineeId = ($accountType === 'trainee') ? $memberId : null;
                    $insertDetailStmt->bind_param("iiis", $approveId, $doctorId, $traineeId, $detailDescription);

                    if (!$insertDetailStmt->execute()) {
                        throw new Exception("Failed to insert into approve_account_member_detail");
                    }
                    $insertDetailStmt->close();

                    // Delete the user from `pending_members`
                    $deleteQuery = "DELETE FROM pending_members WHERE username = ?";
                    $deleteStmt = $this->conn->prepare($deleteQuery);
                    if ($deleteStmt) {
                        $deleteStmt->bind_param("s", $username);
                        if ($deleteStmt->execute()) {
                            error_log("Account for username {$username} deleted from pending_members.");
                        } else {
                            error_log("Failed to delete username {$username} from pending_members.");
                        }
                        $deleteStmt->close();
                    }

                    return true; // Successfully approved
                } else {
                    error_log("Failed to insert username {$username} into member.");
                }
                $insertMemberStmt->close();
            }
        } else {
            error_log("User {$username} not found in pending_members.");
        }
    } else {
        error_log("Failed to prepare select statement for username {$username}.");
    }

    return false; // Return false if anything fails
}

    public function getAdminId($username) {
        $query = "SELECT admin_id FROM admin WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['admin_id'];
        } else {
            return null; // Return null if no matching admin is found
        }
    }
    

    // Authenticate admin by verifying username and hashed password
    public function authenticateAdmin($username, $password) {
        $query = "SELECT password, role FROM admin WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                // Authentication successful, return admin role
                return $row['role'];
            }
        }
        // Authentication failed
        return false;
    }

    // Update an admin
    public function updateAdmin($adminId, $updatedData) {
        $this->conn->begin_transaction();

        try {
            $query = "UPDATE admin SET username = ?, email = ?, password = ?, role = ? WHERE admin_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssssi", $updatedData['username'], $updatedData['email'], $updatedData['password'], $updatedData['role'], $adminId);
            $stmt->execute();
            $stmt->close();

            // Optionally log the update (if a logging table is used)
            $logQuery = "INSERT INTO update_admin_log (admin_id, description) VALUES (?, ?)";
            $logStmt = $this->conn->prepare($logQuery);
            $description = "Updated admin with ID: $adminId";
            $logStmt->bind_param("is", $adminId, $description);
            $logStmt->execute();
            $logStmt->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    // Delete an admin by ID
    public function deleteAdmin($adminId) {
        $this->conn->begin_transaction();

        try {
            // Optionally log the deletion (if a logging table is used)
            $logQuery = "INSERT INTO delete_admin_log (admin_id, description) VALUES (?, ?)";
            $logStmt = $this->conn->prepare($logQuery);
            $description = "Deleted admin with ID: $adminId";
            $logStmt->bind_param("is", $adminId, $description);
            $logStmt->execute();
            $logStmt->close();

            // Delete the admin record
            $query = "DELETE FROM admin WHERE admin_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $adminId);
            $stmt->execute();
            $stmt->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

}
?>
