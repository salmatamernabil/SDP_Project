<?php


require_once '../Model/doctor_model.php';

class DoctorController {
    private $doctorModel;

    public function __construct() {
        session_start(); // Start session
        $this->doctorModel = new DoctorModel();
    }

  


}
?>
