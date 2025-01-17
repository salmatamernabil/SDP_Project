<?php
require_once '../Model/member_model.php';
require_once '../Model/admin_model.php';
require_once "../Design Patterns/Observer.php";
require_once "../Design Patterns/Facade.php";

class SignupController {
    private $member;

    public function __construct() {
        
        $this->member = new MemberModel();
        $adminObserver = new AdminModel();
        // 6. Decide which recipients or admins you want to notify
//    For example, a single AdminObserver using "salma.tamer.nabil@gmail.com" (from your snippet)
        // Register observers (admins)
        $adminObserver1 = new AdminObserver("aubaiwofre@gmail.com", new MailFacade());
        $adminObserver2 = new AdminObserver("tolgasaritas99@gmail.com", new MailFacade());


// 7. Register them
$this->member->registerObserver($adminObserver1);
$this->member->registerObserver($adminObserver2);
// Create MemberModel, passing the same NotificationSystem:


    }

    // In SignupController.php
    // SignupController.php
    public function requestSignup($fullName, $birthDate, $gender, $mobileNumber, $username, $email, $password, $accountType, $specialty) {
        // Session already started, no need for another session_start()
        $adminModel = new AdminModel();
        $admins = $adminModel->getAllAdmins();
    
        if (!empty($admins)) {
            $assignedAdmin = $admins[array_rand($admins)]['admin_id'];
        } else {
            header("Location: ../View/member_view.php?error=No available admins to assign. Please try again later.");
            exit();
        }
    
        // Pass `specialty` as the 10th parameter
        if ($this->member->requestAccountCreation($fullName, $birthDate, $gender, $mobileNumber, $username, $email, $password, $accountType, $assignedAdmin, $specialty)) {
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
    $specialty = isset($_POST['specialty']) ? $_POST['specialty'] : null;

    // Process the signup request
    $signupController = new SignupController();
    $signupController->requestSignup($fullName, $birthDate, $gender, $mobileNumber, $username, $email, $password, $accountType, $specialty);
}
?>
