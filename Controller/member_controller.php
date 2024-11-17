<?php
require_once '../Model/member_model.php';
require_once '../Model/admin_model.php';
require_once "../Design Patterns/Observer.php";

class SignupController {
    private $member;

    public function __construct() {
        $this->member = new MemberModel();
        $adminObserver = new AdminModel();
        $this->member->registerObserver($adminObserver);
    }

    // In SignupController.php
    // SignupController.php
public function requestSignup($fullName, $birthDate, $gender, $mobileNumber, $username, $email, $password, $accountType) {
    session_start();

    // Fetch all admins to randomly assign one to the new signup
    $adminModel = new AdminModel();
    $admins = $adminModel->getAllAdmins();

    // Ensure we have admins to assign; otherwise, handle the error
    if (!empty($admins)) {
        $assignedAdmin = $admins[array_rand($admins)]['admin_id'];
    } else {
        header("Location: ../View/member_view.php?error=No available admins to assign. Please try again later.");
        exit();
    }

    // Save the signup request with the assigned admin
    if ($this->member->requestAccountCreation($fullName, $birthDate, $gender, $mobileNumber, $username, $email, $password, $accountType, $assignedAdmin)) {
        header("Location: ../View/member_view.php?status=success");
    } else {
        header("Location: ../View/member_view.php?error=Signup request failed. Please try again.");
    }
    exit();
}

    




    private function validateSignupData($fullName, $username, $password) {
        // Username validation: Letters and numbers only
        if (!preg_match("/^[a-zA-Z0-9]+$/", $username)) {
            return false;
        }

        // Password validation: At least 8 characters, one uppercase, one lowercase, one number, one special character
        if (!preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$/", $password)) {
            return false;
        }

        return true; // All validations passed
    }}



// Handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST['full_name'];
    $birthDate = $_POST['birth_date'];
    $gender = $_POST['gender'];
    $mobileNumber = $_POST['mobile_number'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $accountType = $_POST['account_type'];

    // Process the signup request
    $signupController = new SignupController();
    $signupController->requestSignup($fullName, $birthDate, $gender, $mobileNumber, $username, $email, $password, $accountType);
}
?>