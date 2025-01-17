<?php
require_once '../Helper FIles/my_database.php';
require_once '../Design Patterns/Observer.php';
require_once '../Design Patterns/Decorater.php';
require_once '../Design Patterns/Iterator.php';
require_once '../Design Patterns/Proxy.php';

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

      

        $this->currentAdminInstance = null;
    }

    // Method to set the current admin instance
     public function setCurrentAdminInstance($adminInstance) {
        $this->currentAdminInstance = $adminInstance;
    }


    public function getAdminEmail($adminId) {
        // Prepare the SQL query to retrieve the admin's email
        $query = "SELECT email FROM admin WHERE admin_id = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $adminId);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return $row['email']; // Return the email address
            } else {
                error_log("Admin email not found for admin ID: $adminId.");
            }
    
            $stmt->close();
        } else {
            error_log("Failed to prepare statement for fetching admin email.");
        }
    
        return null; // Return null if email is not found
    }
    
    public function getAllAdminEmails() {
        // Prepare the SQL query to retrieve all admin emails
        $query = "SELECT email FROM admin";
        $stmt = $this->conn->prepare($query);
    
        $emails = [];
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
    
            while ($row = $result->fetch_assoc()) {
                $emails[] = $row['email'];
            }
    
            $stmt->close();
        } else {
            error_log("Failed to prepare statement for fetching all admin emails.");
        }
    
        return $emails; // Return the array of admin emails
    }

    
    public function getAdminInstanceById($adminId) {
        $query = "SELECT admin_id, role FROM admin WHERE admin_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $role = $row['role'];
            error_log("Retrieved role for admin ID $adminId: $role"); // Debug log
            switch ($role) {
                case 'SuperAdmin':
                    return new SuperAdmin(new BaseAdmin());
                case 'ChiefAdmin':
                    return new ChiefAdmin(new BaseAdmin());
                case 'DonationAdmin':
                    return new DonationAdmin(new BaseAdmin());
                case 'PaymentAdmin':
                    return new PaymentAdmin(new BaseAdmin());
                default:
                    return new BaseAdmin();
            }
        }
        return new BaseAdmin(); // Default instance if no result is found
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

    // Consolidated function to upgrade, wrap, and log
    // Upgrade admin's role, wrap with decorators, and log the action
    // Consolidated function to upgrade, wrap, and log
    public function upgradeAdminRole($adminId) {
        // 1. Get the current admin's role/class (e.g. 'BaseAdmin', 'SuperAdmin', 'ChiefAdmin')
        $adminInstance = $this->getCurrentAdminInstance();
        $adminRole     = get_class($adminInstance);
    
        // 2. Create a DBProxy with the current admin role
        $dbProxy = new DBProxy($adminRole);
    
        // -----------------------------
        // STEP 1: SELECT current role
        // -----------------------------
        $query  = "SELECT role FROM admin WHERE admin_id = ?";
        $result = $dbProxy->executeQuery($query, [$adminId]);
    
        // Check if DBProxy returned an error or a denial
        if (is_string($result)) {
            // "Access Denied" or some other error message
            error_log("[ERROR] upgradeAdminRole() - $result");
            return null;
        }
    
        // If the query fails or returns no rows
        if (!$result || $result->num_rows === 0) {
            return null; // No matching admin record
        }
    
        // Fetch the current role
        $row         = $result->fetch_assoc();
        $currentRole = $row['role'];
    
        // -----------------------------
        // STEP 2: Retrieve the existing instance
        // (Uses your existing getAdminInstanceById method)
        // -----------------------------
        $existingAdminInstance = $this->getAdminInstanceById($adminId);
        if (!$existingAdminInstance) {
            throw new Exception("Admin instance not found for ID: $adminId");
        }
    
        // -----------------------------
        // STEP 3: Apply the appropriate decorator
        // -----------------------------
        $newRole = null;
        if ($currentRole === 'BaseAdmin') {
            $existingAdminInstance = new SuperAdmin($existingAdminInstance);
            $newRole = 'SuperAdmin';
        } elseif ($currentRole === 'SuperAdmin') {
            $existingAdminInstance = new ChiefAdmin($existingAdminInstance);
            $newRole = 'ChiefAdmin';
        } else {
            // Already ChiefAdmin or unsupported role
            return false;
        }
    
        // Update the current admin instance for future use
        $this->setCurrentAdminInstance($existingAdminInstance);
    
        // -----------------------------
        // STEP 4: Update the `admin` table
        // -----------------------------
        $updateRoleQuery = "UPDATE admin SET role = ? WHERE admin_id = ?";
        $updateResult     = $dbProxy->executeQuery($updateRoleQuery, [$newRole, $adminId]);
    
        if (is_string($updateResult) || $updateResult === false) {
            // Handle error or access denied
            error_log("[ERROR] Failed to update role in admin table.");
            return null;
        }
    
        // -----------------------------
        // STEP 5: Insert into `upgrade_admin` table
        // -----------------------------
        $upgradingAdminId    = $_SESSION['admin_id']; // The admin performing the upgrade
        $insertUpgradeQuery  = "INSERT INTO upgrade_admin (admin_id, new_role) VALUES (?, ?)";
        $insertUpgradeResult = $dbProxy->executeQuery($insertUpgradeQuery, [$upgradingAdminId, $newRole]);
    
        if (is_string($insertUpgradeResult) || $insertUpgradeResult === false) {
            error_log("[ERROR] Failed to insert into upgrade_admin table.");
            return null;
        }
    
        // Get the newly inserted upgrade ID (last insert)
        // With DBProxy, you need a separate SELECT:
        $lastIdResult = $dbProxy->executeQuery("SELECT LAST_INSERT_ID()");
        if (!$lastIdResult || is_string($lastIdResult)) {
            error_log("[ERROR] Could not fetch LAST_INSERT_ID() for upgrade_admin.");
            return null;
        }
        $lastIdRow = $lastIdResult->fetch_assoc();
        $upgradeId = $lastIdRow["LAST_INSERT_ID()"];
    
        // -----------------------------
        // STEP 6: Insert into `upgrade_admin_detail`
        // -----------------------------
        $detailDescription  = "Upgraded admin with ID: $adminId to new role: $newRole";
        $insertDetailQuery  = "INSERT INTO upgrade_admin_detail (upgrade_id, upgraded_admin_id, detail_description) 
                               VALUES (?, ?, ?)";
        $insertDetailResult = $dbProxy->executeQuery(
            $insertDetailQuery,
            [$upgradeId, $adminId, $detailDescription]
        );
    
        if (is_string($insertDetailResult) || $insertDetailResult === false) {
            error_log("[ERROR] Failed to insert into upgrade_admin_detail table.");
            return null;
        }
    
        // Return the upgraded instance
        return $existingAdminInstance;
    }
    
// Fetch all pending accounts
    // In AdminController.php
public function getPendingAccounts($adminId) {
    $_SESSION['logs'][] = "Fetching pending accounts for admin ID: $adminId.";
    error_log("[DEBUG] AdminController - Fetching pending accounts for admin ID: $adminId");

    // Fetch Admin instance based on the session admin_id
    $admin = $this->getAdminInstanceById($adminId);  // Retrieve the Admin object from the database
    if (!$admin) {
        $_SESSION['logs'][] = "Error: Admin instance not found for admin ID: $adminId.";
        error_log("[ERROR] AdminController - Admin instance not found for admin ID: $adminId.");
        return [];  // Return empty if admin instance is not found
    }
  
    // Create a DBProxy instance with the role from the Admin object (role is automatically retrieved from Admin instance)
    $admin = $this->getCurrentAdminInstance(); // Ensure this returns an instance like ChiefAdmin, SuperAdmin, or BaseAdmin
    
    // Get the role from the admin instance
    $adminRole = get_class($admin); // This will give you the class name, e.g., 'ChiefAdmin'
    
    // Log the role to see which role is being passed
    $_SESSION['logs'][] = "[INFO] Admin role: $adminRole";
    error_log("[INFO] Admin role: $adminRole");
   // Create a DBProxy instance with the admin role

   // Create a DBProxy instance with the admin role
$dbProxy = new DBProxy($adminRole);

// Prepare the query with a placeholder for admin_id
$query = "SELECT * FROM pending_members WHERE admin_id = ?";

// Log the query execution
$_SESSION['logs'][] = "[INFO] Executing query: $query with admin ID: {$_SESSION['admin_id']}";
error_log("[INFO] Executing query: $query with admin ID: {$_SESSION['admin_id']}");

// Call executeQuery with the parameters (passing the actual parameter value)
$pendingAccounts = $dbProxy->executeQuery($query, [$_SESSION['admin_id']]);

// Check if access was denied or successful
if (is_string($pendingAccounts)) {
    $_SESSION['logs'][] = "[ERROR] Error executing query: $pendingAccounts";
    error_log("[ERROR] Error executing query: $pendingAccounts");
} else {
    $_SESSION['logs'][] = "[INFO] Successfully retrieved pending accounts.";
    error_log("[INFO] Successfully retrieved pending accounts.");
}


    

    if (is_string($pendingAccounts)) {
        $_SESSION['logs'][] = "Error: " . $pendingAccounts;  // Access denied message from DBProxy
        return [];
    }

    $_SESSION['logs'][] = "[DEBUG] AdminController - Pending accounts fetched successfully.";
    return $pendingAccounts;
}


public function getPendingAccountsIterator($adminId): PendingAccountsIterator {
    $result = $this->getPendingAccounts($adminId);

    // Convert mysqli_result to array if needed
    $accounts = [];
    if ($result instanceof mysqli_result) {
        while ($row = $result->fetch_assoc()) {
            $accounts[] = $row;
        }
    }

    // Return the iterator with the array of accounts
    return new PendingAccountsIterator($accounts);
}



    

       // Method to get the current role of an admin by their ID
       public function getCurrentAdminInstance() {
        if (!$this->currentAdminInstance) {
            if (isset($_SESSION['admin_id'])) {
                $adminId = $_SESSION['admin_id'];
                error_log("Fetching admin instance for admin ID: $adminId"); // Debug log
                $this->currentAdminInstance = $this->getAdminInstanceById($adminId);
            } else {
                error_log("No admin ID in session. Defaulting to BaseAdmin."); // Debug log
                $this->currentAdminInstance = new BaseAdmin(); // Default if no admin ID is set
            }
        }
        return $this->currentAdminInstance;
    }

    public function getUpgradeableAdmins($currentAdminId) {
        // 1. Determine the current admin's role. We assume you already have:
        //    $this->getCurrentAdminInstance() => returns BaseAdmin, SuperAdmin, or ChiefAdmin
        $adminInstance = $this->getCurrentAdminInstance();
        $adminRole     = get_class($adminInstance); // e.g., 'BaseAdmin', 'SuperAdmin', 'ChiefAdmin'
        
        // 2. Create a DBProxy using the current role
        $dbProxy = new DBProxy($adminRole);
    
        // 3. Build your query
        $query = "SELECT admin_id, username, role FROM admin WHERE role != 'ChiefAdmin' AND role != 'PaymentAdmin' AND  role != 'DonationAdmin' AND admin_id != ?";
    
        // 4. Execute the query through DBProxy
        $result = $dbProxy->executeQuery($query, [$currentAdminId]);
    
        // If DBProxy returns a string, it's likely "Access Denied" or an error message
        if (is_string($result)) {
            // You can log or handle the error as you wish
            error_log("[ERROR] getUpgradeableAdmins: $result");
            return []; // Return empty or handle differently
        }
    
        // 5. Convert the mysqli_result to an associative array
        if ($result instanceof mysqli_result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    
        // If something else happens, return empty array
        return [];
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
    $_SESSION['logs'][] = "Attempting to approve account for username: $username.";
    error_log("[INFO] Attempting to approve account for username: $username.");

    $admin = $this->getCurrentAdminInstance();
    $_SESSION['logs'][] = "Current admin role: " . get_class($admin);
    error_log("[INFO] Current admin role: " . get_class($admin));

    $dbProxy = new DBProxy(get_class($admin)); // Use the role/class name


    // Fetch user from pending_members
    $user = $dbProxy->executeQuery("SELECT * FROM pending_members WHERE username = ?", [$username])->fetch_assoc();
    
    if (!$user) {
        $_SESSION['logs'][] = "User $username not found in pending_members.";
        error_log("[ERROR] User $username not found in pending_members.");
        return false;
    }

    // Insert into member table
    $insertMemberSuccess = $dbProxy->executeQuery(
        "INSERT INTO member (FullName, BirthDate, Gender, MobileNumber) VALUES (?, ?, ?, ?)",
        [$user['FullName'], $user['BirthDate'], $user['Gender'], $user['MobileNumber']]
    );
    
    // Now $insertMemberSuccess will be true if inserted successfully, false if it really failed.
    if (!$insertMemberSuccess) {
        error_log("[ERROR] Actual error inserting into `member` table: " . $this->conn->error);
        return false;
    }    

    $memberId = $this->conn->insert_id;
    $accountType = strtolower($user['account_type']);

    // Insert into specific account type table
    $specificQuery = $accountType === 'doctor' ? 
        "INSERT INTO doctor (MemberID, Username, Email, Password, Specialty) VALUES (?, ?, ?, ?, ?)" : 
        "INSERT INTO trainee (MemberID, Username, Email, Password, Specialty) VALUES (?, ?, ?, ?, ?)";

    $insertSpecificSuccess = $dbProxy->executeQuery(
        $specificQuery,
        [$memberId, $user['username'], $user['email'], $user['password'], $user['Specialty']]
    );

    if (!$insertSpecificSuccess) {
        $_SESSION['logs'][] = "Failed to insert $username into $accountType table.";
        error_log("[ERROR] Failed to insert $username into $accountType table.");
        return false;
    
    }

    // Log approval in approve_account_member
    $insertApproveSuccess = $dbProxy->executeQuery(
        "INSERT INTO approve_account_member (admin_id) VALUES (?)",
        [$_SESSION['admin_id']]
    );

    if (!$insertApproveSuccess) {
        $_SESSION['logs'][] = "Failed to log approval for $username.";
        error_log("[ERROR] Failed to log approval for $username.");
        return false;
    }

    $approveId = $this->conn->insert_id;

    // Log details in approve_account_member_detail
    $detailDescription = "Approved account for user: $username";
    $doctorId = ($accountType === 'doctor') ? $memberId : null;
    $traineeId = ($accountType === 'trainee') ? $memberId : null;

    $insertDetailSuccess = $dbProxy->executeQuery(
        "INSERT INTO approve_account_member_detail (approve_id, doctor_id, trainee_id, detail_description) VALUES (?, ?, ?, ?)",
        [$approveId, $doctorId, $traineeId, $detailDescription]
    );

    if (!$insertDetailSuccess) {
        $_SESSION['logs'][] = "Failed to log approval details for $username.";
        error_log("[ERROR] Failed to log approval details for $username.");
        return false;
    }

    // Delete from pending_members
    $deleteSuccess = $dbProxy->executeQuery("DELETE FROM pending_members WHERE username = ?", [$username]);

    if (!$deleteSuccess) {
        $_SESSION['logs'][] = "Failed to delete $username from pending_members.";
        error_log("[ERROR] Failed to delete $username from pending_members.");
        return false;
    }

    $_SESSION['logs'][] = "Successfully approved account for $username.";
    error_log("[INFO] Successfully approved account for $username.");
    return true;
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
