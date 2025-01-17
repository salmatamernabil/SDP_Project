<?php

// Base User class (optional, for shared logic if needed)
abstract class User {
    public $id;
    public $username;
    public $email;
    public $specialty;

    public function __construct($data) {
        $this->id = $data['id'];
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->specialty = $data['specialty'];
    }

    abstract public function getDetails();
}

// Doctor class
class Doctor extends User {
    public function getDetails() {
        return "Doctor: $this->username, Specialty: $this->specialty";
    }
}

// Trainee class
class Trainee extends User {
    public function getDetails() {
        return "Trainee: $this->username, Specialty: $this->specialty";
    }
}

// Factory for creating users
class UserFactory {
    public static function createUser($accountType, $userData) {
        // Define required keys based on account type
        $requiredKeys = ['id', 'username', 'email', 'specialty'];
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $userData)) {
                $errorMessage = "Missing key '$key' in user data";
                error_log("[ERROR] " . $errorMessage);
                throw new Exception($errorMessage);
            }
        }

        // Map account types to classes dynamically
        $accountType = strtolower($accountType);
        $userClassMap = [
            'doctor' => Doctor::class,
            'trainee' => Trainee::class
        ];

        if (isset($userClassMap[$accountType])) {
            error_log("[INFO] Creating user of type '$accountType'.");
            return new $userClassMap[$accountType]($userData);
        }

        $errorMessage = "Invalid account type provided: $accountType";
        error_log("[ERROR] " . $errorMessage);
        throw new Exception($errorMessage);
    }
}

