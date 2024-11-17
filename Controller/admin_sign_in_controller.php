<?php
session_start();
require_once '../Model/admin_model.php';

class AdminSignInController {
    private $adminModel;

    public function __construct() {
        $this->adminModel = new AdminModel();
        error_log("AdminSignInController initialized.");
    }

    public function signIn($username, $password) {
        error_log("Attempting sign-in for username: $username");

        // Attempt to authenticate the admin
        $role = $this->adminModel->authenticateAdmin($username, $password);

        if ($role) {
            error_log("Authentication successful for username: $username with role: $role");

            // Retrieve and validate the admin ID
            $adminId = $this->adminModel->getAdminId($username);
            if ($adminId !== null) {
                $_SESSION['admin_username'] = $username;
                $_SESSION['admin_role'] = $role;
                $_SESSION['admin_id'] = $adminId;

                // Redirect to the admin home view
                header("Location: ../View/admin_home_view.php");
                exit();
            } else {
                error_log("Failed to retrieve admin ID for username: $username");
                $_SESSION['error'] = "Failed to retrieve admin details. Please contact support.";
            }
        } else {
            error_log("Authentication failed for username: $username");
            $_SESSION['error'] = "Invalid username or password.";
        }

        // Redirect back to the login view with an error
        header("Location: ../View/admin_login_view.php");
        exit();
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log("Received POST request for admin sign-in.");

    $username = $_POST['username'];
    error_log("Username received from POST data: $username");

    $password = $_POST['password']; // Avoid logging passwords for security

    // Initialize the controller and attempt sign-in
    $adminSignInController = new AdminSignInController();
    $adminSignInController->signIn($username, $password);
} else {
    error_log("Non-POST request received; ignoring.");
}
?>
