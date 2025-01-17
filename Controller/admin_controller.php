<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../Model/admin_model.php';
require_once '../Model/member_model.php';
require_once '../Design Patterns/Observer.php';
require_once '../Design Patterns/Decorater.php';
require_once '../Design Patterns/Facade.php';
require_once '../Design Patterns/Command.php';
require_once '../Model/admin_model.php'; 

if (!isset($_SESSION['commandInvoker'])) {
    $_SESSION['commandInvoker'] = new CommandInvoker();
}

class AdminController {

    private $adminModel;
    private $memberModel;

    private $adminComponent;

    public function __construct() {
        
        $this->adminModel = new AdminModel();
        $this->adminComponent = $this->adminModel->getCurrentAdminInstance();
        //global $globalNotificationSystem;
        $this->memberModel = new MemberModel();
        $this->memberModel->registerObserver($this->adminModel);
        

        
        // Add session log
        $_SESSION['logs'][] = "AdminController initialized.";
    }
    
    public function getPendingAccounts($adminId) {
        $_SESSION['logs'][] = "Fetching pending accounts for admin ID: $adminId.";
        error_log("[DEBUG] AdminController - Fetching pending accounts for admin ID: $adminId");
    
        // Use the model to get the iterator
        $iterator = $this->adminModel->getPendingAccountsIterator($adminId);
    
        $_SESSION['logs'][] = "[DEBUG] AdminController - Iterator created successfully for admin ID: $adminId.";
        error_log("[DEBUG] AdminController - Iterator created successfully for admin ID: $adminId");
    
        // Use the iterator to fetch pending accounts
        $pendingAccounts = [];
        $count = 0;
    
        while ($iterator->hasNext()) {
            $currentAccount = $iterator->next();
            $pendingAccounts[] = $currentAccount;
            $count++;
    
            $_SESSION['logs'][] = "[DEBUG] AdminController - Iterating: Current account fetched: " . json_encode($currentAccount);
            error_log("[DEBUG] AdminController - Iterating: Current account fetched: " . json_encode($currentAccount));
        }
    
        $_SESSION['logs'][] = "[DEBUG] AdminController - Total pending accounts fetched: $count.";
        error_log("[DEBUG] AdminController - Total pending accounts fetched: $count");
    
        $_SESSION['logs'][] = "Pending accounts fetched using Iterator.";
        error_log("[DEBUG] AdminController - Pending accounts fetched successfully using Iterator.");
    
        return $pendingAccounts;
    }
    
    

    // Function to approve account
     // Function to approve account using the decorator pattern
     public function approveAccount($username) {
        // Get the current admin instance (decorated)
        $this->adminComponent = $this->adminModel->getCurrentAdminInstance();
    
        // Delegate the approval to the AdminComponent instance
        $success = $this->adminComponent->approveAccount($username);
    
        // Log the result and set session messages
        if ($success) {
            $_SESSION['logs'][] = "Account approved successfully.";
            error_log("Account approved successfully.");
            $_SESSION['message'] = "Account approved successfully.";
        } else {
            $_SESSION['logs'][] = "Failed to approve account.";
            error_log("Failed to approve account.");
            $_SESSION['message'] = "Failed to approve account.";
        }
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

    public function getCashDonations() {
        $_SESSION['logs'][] = "Fetching cash donations.";
        error_log("[DEBUG] AdminController - Fetching cash donations.");
        
        // Call the viewCashDonations function from the adminComponent
        $cashDonations = $this->adminComponent->viewCashDonations();
    
        // Check if the donations are retrieved successfully
        if (is_array($cashDonations)) {
            $_SESSION['cashDonations'] = $cashDonations; // Set the data in the session
            $_SESSION['logs'][] = "[DEBUG] Cash donations fetched and stored in session.";
            error_log("[DEBUG] Cash donations fetched and stored in session.");
        } else {
            $_SESSION['cashDonations'] = []; // Default to an empty array if no donations
            $_SESSION['logs'][] = "[ERROR] Failed to fetch cash donations.";
            error_log("[ERROR] Failed to fetch cash donations.");
        }
    }
    
    public function approveCashDonation($donationId) {
        $_SESSION['logs'][] = "Attempting to approve cash donation ID: $donationId.";
        error_log("[DEBUG] AdminController - Approving cash donation ID: $donationId.");
    
        // Get the current admin instance (decorated)
        $this->adminComponent = $this->adminModel->getCurrentAdminInstance();
    
        // Delegate the approval to the AdminComponent instance
        $success = $this->adminComponent->collectCashDonation($donationId);
    
        // Log the result and set session messages
        if ($success) {
            $_SESSION['logs'][] = "Cash donation approved successfully.";
            error_log("Cash donation approved successfully.");
            $_SESSION['message'] = "Cash donation approved successfully.";
        } else {
            $_SESSION['logs'][] = "Failed to approve cash donation.";
            error_log("Failed to approve cash donation.");
            $_SESSION['message'] = "Failed to approve cash donation.";
        }
    }
    


        // Function to get donation supplies items
        public function getDonationSupplies() {
            $_SESSION['logs'][] = "Fetching donation supplies items.";
            error_log("[DEBUG] AdminController - Fetching donation supplies items.");
            // Use the admin component to get the donation supplies
            $donationSupplies =  $this->adminComponent->getDonationSupplies();
    
            $_SESSION['logs'][] = "[DEBUG] AdminController - Donation supplies fetched successfully.";
            error_log("[DEBUG] AdminController - Donation supplies fetched successfully.");
    
            return $donationSupplies;
        }
    
        // Function to approve a donation supply
        public function approveDonationSupply($supplyId) {
            $_SESSION['logs'][] = "Attempting to approve donation supply ID: $supplyId.";
            error_log("[DEBUG] AdminController - Approving donation supply ID: $supplyId.");
    
            // Get the current admin instance (decorated)
            $this->adminComponent = $this->adminModel->getCurrentAdminInstance();
    
            // Delegate the approval to the AdminComponent instance
            $success = $this->adminComponent->approveDonationSupply($supplyId);
    
            // Log the result and set session messages
            if ($success) {
                $_SESSION['logs'][] = "Donation supply approved successfully.";
                error_log("Donation supply approved successfully.");
                $_SESSION['message'] = "Donation supply approved successfully.";
            } else {
                $_SESSION['logs'][] = "Failed to approve donation supply.";
                error_log("Failed to approve donation supply.");
                $_SESSION['message'] = "Failed to approve donation supply.";
            }
        }
    
        // Function to reject a donation supply
        public function rejectDonationSupply($supplyId) {
            $_SESSION['logs'][] = "Attempting to reject donation supply ID: $supplyId.";
            error_log("[DEBUG] AdminController - Rejecting donation supply ID: $supplyId.");
    
            // Get the current admin instance (decorated)
            $this->adminComponent = $this->adminModel->getCurrentAdminInstance();
    
            // Delegate the rejection to the AdminComponent instance
            $success = $this->adminComponent->rejectDonationSupply($supplyId);
    
            // Log the result and set session messages
            if ($success) {
                $_SESSION['logs'][] = "Donation supply rejected successfully.";
                error_log("Donation supply rejected successfully.");
                $_SESSION['message'] = "Donation supply rejected successfully.";
            } else {
                $_SESSION['logs'][] = "Failed to reject donation supply.";
                error_log("Failed to reject donation supply.");
                $_SESSION['message'] = "Failed to reject donation supply.";
            }
        }
        public function getDonationItemStatus($itemId) {
            $_SESSION['logs'][] = "Fetching delivery status for donation item ID: $itemId.";
            error_log("[DEBUG] AdminController - Fetching delivery status for donation item ID: $itemId.");
        
            try {
                // Get the current admin instance (decorated)
                $this->adminComponent = $this->adminModel->getCurrentAdminInstance();
        
                // Check the delivery status using the AdminComponent instance
                $deliveryStatus = $this->adminComponent->checkDeliveryStatus($itemId);
        
                // Log the result
                if ($deliveryStatus === true) {
                    $_SESSION['logs'][] = "Donation item ID: $itemId has been delivered.";
                    error_log("[INFO] Donation item ID: $itemId has been delivered.");
                    return "Delivered";
                } elseif ($deliveryStatus === false) {
                    $_SESSION['logs'][] = "Donation item ID: $itemId has not been delivered yet.";
                    error_log("[INFO] Donation item ID: $itemId has not been delivered yet.");
                    return "Not Delivered";
                } else {
                    $_SESSION['logs'][] = "Donation item ID: $itemId status could not be determined.";
                    error_log("[WARNING] Donation item ID: $itemId status could not be determined.");
                    return "Unknown";
                }
            } catch (Exception $e) {
                // Log the error
                $_SESSION['logs'][] = "Error fetching delivery status for donation item ID: $itemId - " . $e->getMessage();
                error_log("[ERROR] Error fetching delivery status for donation item ID: $itemId - " . $e->getMessage());
                return "Error";
            }
        }
}

$adminController = new AdminController();

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

// **Newly Moved: Handle Approve Donation Supply**
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approveCashDonation'])) {
    error_log("[DEBUG] POST Data: " . json_encode($_POST));

    $_SESSION['logs'][] = "POST request to approve cash donation triggered.";
    $donationId = $_POST['donation_id'] ?? null;

    if ($donationId) {
        $_SESSION['logs'][] = "Donation ID received: $donationId.";
        error_log("[DEBUG] Donation ID: $donationId");

        $adminController->approveCashDonation($donationId);
        $_SESSION['logs'][] = "Redirecting to admin_home_view.php after approval.";
        header("Location: ../View/admin_home_view.php");
        exit();
    } else {
        $_SESSION['logs'][] = "No Donation ID found in POST data.";
        error_log("[ERROR] Donation ID missing in POST request.");
    }
}


// **Newly Moved: Handle Reject Donation Supply**
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['rejectDonation'])) {
    $_SESSION['logs'][] = "POST request to reject donation supply.";
    $supplyId = $_POST['supply_id'];
    $_SESSION['logs'][] = "Supply ID from POST: $supplyId.";
    $adminController->rejectDonationSupply($supplyId);
    header("Location: ../View/admin_home_view.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approveDonation'])) {
    $_SESSION['logs'][] = "POST request to accept donation supply.";
    $supplyId = $_POST['supply_id'];
    $_SESSION['logs'][] = "Supply ID from POST: $supplyId.";
    $adminController->approveDonationSupply($supplyId);
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

  
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approveCashDonation'])) {
        $_SESSION['logs'][] = "POST request to approve cash donation.";
        $donationId = $_POST['donation_id'];
        $_SESSION['logs'][] = "Donation ID from POST: $donationId.";
        $adminController->approveCashDonation($donationId);
        $_SESSION['logs'][] = "Redirecting to admin_home_view.php.";
        header("Location: ../View/admin_home_view.php");
        exit();
    }
    
    // Fetch cash donations for PaymentAdmin
if ($_SESSION['admin_role'] === 'PaymentAdmin') {
    $_SESSION['logs'][] = "Current admin is PaymentAdmin. Fetching cash donations.";
    $adminController->getCashDonations();
}

}
?>
