<?php

class UserFactory {
    public static function createUser($accountType, $userData) {
        // Ensure the required keys are in $userData
        $requiredKeys = ['id', 'username', 'email'];
        if (strtolower($accountType) === 'doctor' || strtolower($accountType) === 'trainee') {
            $requiredKeys[] = 'specialty'; // Both doctor and trainee need specialty
        }
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $userData)) {
                throw new Exception("Missing key '$key' in user data");
            }
        }
    
        if (strtolower($accountType) === 'doctor') {
            return new Doctor($userData);
        } elseif (strtolower($accountType) === 'trainee') {
            return new Trainee($userData);
        } else {
            throw new Exception("Invalid account type provided.");
        }
    }
    
}

class Doctor {
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

    public function getDetails() {
        return "Doctor: $this->username, Specialty: $this->specialty";
    }
}

class Trainee {
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

    public function getDetails() {
        return "Trainee: $this->username, Specialty: $this->specialty";
    }
}
