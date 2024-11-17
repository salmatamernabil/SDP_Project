<?php
session_start();

require_once '../Model/admin_model.php';
require_once '../Model/member_model.php';
require_once '../Design Patterns/Observer.php';

class AdminController {

    private $adminModel;
    private $memberModel;

    public function __construct() {
        $this->adminModel = new AdminModel();
        $this->memberModel = new MemberModel();
        $this->memberModel->registerObserver($this->adminModel);

        // Add session log
        $_SESSION['logs'][] = "AdminController initialized.";
    }

    // Function to get all pending accounts
    public function getPendingAccounts($adminId) {
        $_SESSION['logs'][] = "Fetching pending accounts for admin ID: $adminId.";
        error_log("Fetching pending accounts for admin ID: $adminId.");
        $pendingAccounts = $this->adminModel->getPendingAccounts($adminId);
        $_SESSION['logs'][] = "Pending accounts fetched.";
        error_log("Pending accounts fetched.");
        return $pendingAccounts;
    }

    // Function to approve account
    public function approveAccount($username) {
        $_SESSION['logs'][] = "Approving account for username: $username.";
        error_log("Approving account for username: $username.");
        $this->adminModel->approveAccount($username);
        $_SESSION['logs'][] = "Account approved.";
        error_log("Account approved.");
    }

    // Function to get upgradeable admins for Chief Admin view
    public function getUpgradeableAdmins($currentAdminId) {
        $_SESSION['logs'][] = "Fetching upgradeable admins for Chief Admin ID: $currentAdminId.";
        $_SESSION['upgradeableAdmins'] = $this->adminModel->getUpgradeableAdmins($currentAdminId);
        $_SESSION['logs'][] = "Upgradeable admins fetched.";
    }

    // Function to upgrade an admin using existing model function
    public function upgradeAdmin($adminIdToUpgrade) {
        $_SESSION['logs'][] = "Attempting to upgrade admin ID: $adminIdToUpgrade.";


        $adminInstance = $this->adminModel->getCurrentAdminInstance();
        
        if (!($adminInstance instanceof ChiefAdmin)) {
            error_log("THIS ADMIN INSTANCE,  You do not have permission to upgrade admins.");
            $_SESSION['message'] = "You do not have permission to upgrade admins.";
            $_SESSION['logs'][] = "Permission denied: Admin instance is not a ChiefAdmin.";
            return false;
        }

        // Check if the current admin is a ChiefAdmin
        if ($_SESSION['admin_role'] !== 'ChiefAdmin') {
            $_SESSION['message'] = "You do not have permission to upgrade admins.";
            $_SESSION['logs'][] = "Permission denied: Current admin is not a ChiefAdmin.";
            return false;
        }
    
        // Proceed with the upgrade
        $upgradedInstance = $this->adminModel->upgradeAdminRole($adminIdToUpgrade);
        $_SESSION['logs'][] = "Called upgradeAdminRole for admin ID: $adminIdToUpgrade.";
            
        
        
            error_log("Permission authorized, ChiefAdmin.");
            
        if ($upgradedInstance) {
            $_SESSION['logs'][] = "Admin ID: $adminIdToUpgrade upgraded successfully.";
            error_log("Admin ID: $adminIdToUpgrade upgraded successfully.");

            // Update session role if this is the currently logged-in admin
            if (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] == $adminIdToUpgrade) {
                $_SESSION['admin_role'] = $this->adminModel->getCurrentAdminRole($adminIdToUpgrade);
                $_SESSION['role_updated'] = true; // Set flag to reload view with updated role
                $_SESSION['logs'][] = "Session admin_role updated for admin ID: $adminIdToUpgrade.";
            }
            return true;
        }

        $_SESSION['logs'][] = "Failed to upgrade admin ID: $adminIdToUpgrade.";
        return false;
    
    }
        
    // Check and update admin role in session, if upgraded
    public function checkAndUpdateAdminRole() {
        if (isset($_SESSION['admin_id'])) {
            $_SESSION['logs'][] = "Checking and updating admin role for admin ID: {$_SESSION['admin_id']}.";
            $currentRoleFromDb = $this->adminModel->getCurrentAdminRole($_SESSION['admin_id']);
            if ($currentRoleFromDb && $_SESSION['admin_role'] !== $currentRoleFromDb) {
                $_SESSION['admin_role'] = $currentRoleFromDb;
                $_SESSION['logs'][] = "Session admin_role updated to: $currentRoleFromDb.";
            } else {
                $_SESSION['logs'][] = "No update needed for admin_role.";
            }
        } else {
            $_SESSION['logs'][] = "Admin ID not set in session.";
        }
    }
    
}

// Handle approval of accounts
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approve'])) {
    $_SESSION['logs'][] = "POST request to approve account.";
    $username = $_POST['username'];
    $_SESSION['logs'][] = "Username from POST: $username.";
    $adminController = new AdminController();
    $adminController->approveAccount($username);
    $_SESSION['logs'][] = "Redirecting to admin_home_view.php.";
    header("Location: ../View/admin_home_view.php");
    exit();
}

// Handle upgrading of an admin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upgrade'])) {
    $_SESSION['logs'][] = "POST request to upgrade admin.";
    error_log("POST request to upgrade admin.");
    $adminIdToUpgrade = $_POST['admin_id'];
    $_SESSION['logs'][] = "Admin ID to upgrade from POST: $adminIdToUpgrade.";
    $adminController = new AdminController();
    if ($adminController->upgradeAdmin($adminIdToUpgrade)) {
        error_log("Admin upgraded successfully.");
        $_SESSION['message'] = "Admin upgraded successfully.";
        $_SESSION['logs'][] = "Admin upgraded successfully.";
    } else {
        $_SESSION['message'] = "Failed to upgrade admin.";
        $_SESSION['logs'][] = "Failed to upgrade admin.";
    }
    $_SESSION['logs'][] = "Redirecting to admin_home_view.php.";
    header("Location: ../View/admin_home_view.php");
    exit();
}

// Fetch upgradeable admins for Chief Admin
if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'ChiefAdmin') {
    $_SESSION['logs'][] = "Current admin is ChiefAdmin. Fetching upgradeable admins.";
    $adminController = new AdminController();
    $adminController->getUpgradeableAdmins($_SESSION['admin_id']);
} else {
    $_SESSION['logs'][] = "Current admin is not ChiefAdmin or admin_role not set.";
}

// Handling AJAX request for getting pending accounts
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] === 'getPendingAccounts') {
    $_SESSION['logs'][] = "GET request for action getPendingAccounts.";
    if (isset($_SESSION['admin_id'])) {
        $adminId = $_SESSION['admin_id'];
        $_SESSION['logs'][] = "Admin ID from session: $adminId.";
        $adminController = new AdminController();
        $pendingAccounts = $adminController->getPendingAccounts($adminId);
            
        $_SESSION['logs'][] = "Pending accounts retrieved and sent as JSON.";
        header('Content-Type: application/json');
        echo json_encode($pendingAccounts);
    } else {
        error_log("Admin ID not set in session.");
        $_SESSION['logs'][] = "Error: Admin ID not set in session.";
        echo json_encode([]);
    }
}
?>
