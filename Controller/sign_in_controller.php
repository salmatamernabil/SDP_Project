<?php
session_start();
require_once '../Model/sign_in_model.php';

class SignInController {
    private $signInModel;

    public function __construct() {
        $this->signInModel = new SignInModel();
    }

    public function signIn($username, $password) {
        // Check the user's status
        $status = $this->signInModel->checkUserStatus($username, $password);

        switch ($status) {
            case 'approved':
                $_SESSION['user'] = $username;
                header("Location: ../View/home_view.php"); // Redirect to the dashboard
                exit();

            case 'pending':
                $_SESSION['pending_notice'] = "Please wait, your account is under review. Check back later.";
                header("Location: ../View/sign_in_view.php"); // Redirect back to sign-in view with message
                exit();

            case 'invalid':
            default:
                $_SESSION['error'] = "Invalid username or password.";
                header("Location: ../View/sign_in_view.php"); // Redirect to sign-in page with error
                exit();
        }
    }
}

// Handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $signInController = new SignInController();
    $signInController->signIn($username, $password);
}
?>
