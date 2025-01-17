<?php
session_start();
require_once '../Model/sign_in_model.php';

class SignInController {
    private $signInModel;

    public function __construct() {
        $this->signInModel = new SignInModel();
        error_log("SignInController initialized.");
    }

    public function signIn($username, $password) {
        // Debug: Log the start of sign-in
        error_log("Sign-in attempt for username: $username");

        // Attempt to sign in the user
        $user = $this->signInModel->signIn($username, $password);
     
        if ($user === 'pending') {
            // Handle pending users
            error_log("User $username is pending approval.");
            $_SESSION['pending_notice'] = "Please wait, your account is under review. Check back later.";
            header("Location: ../View/sign_in_view.php");
            exit();
        } elseif ($user instanceof Doctor || $user instanceof Trainee) {
            // Handle approved users (Doctor or Trainee)
            $userType = ($user instanceof Doctor) ? "Doctor" : "Trainee";
            error_log("User $username signed in successfully as $userType.");
            $_SESSION['user'] = serialize($user); // Store user object in session
            $redirectPage = ($user instanceof Doctor) ? "doctor_home_view.php" : "trainee_home_view.php";
            header("Location: ../View/$redirectPage"); // Redirect based on user type
            exit();
        } else {
            // Handle invalid users
            error_log("Sign-in failed for username: $username. Invalid credentials.");
            $_SESSION['error'] = "Invalid username or password.";
            header("Location: ../View/sign_in_view.php");
            exit();
        }
    }
}

// Handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Debug: Log the received form data
    error_log("Received POST data: username=$username");

    $signInController = new SignInController();
    $signInController->signIn($username, $password);
}
?>
