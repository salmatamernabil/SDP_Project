<?php
require_once '../Model/patient_model.php';
require_once '../Model/comobidity_model.php';
require_once '../Model/complication_model.php';
require_once '../Model/bmi_model.php';
require_once '../Model/types_model.php';
require_once '../Model/course_model.php';
require_once '../Model/admin_model.php';
require_once '../Model/follow_up_model.php';
require_once '../Model/donate_supplies_model.php';
require_once '../Model/donate_model.php';

// AdminComponent Interface
interface AdminComponent {
    public function addPatient($data, $adminId);
    public function addComorbidity($data, $adminId);
    public function addComplication($data, $adminId);
    public function addBMI($data, $adminId);
    public function addSurgery($data, $adminId);
    public function approveAccount($username);
    public function addCourse($data, $adminId);
    public function followUpPatient(); 
}

// BaseAdmin Class
class BaseAdmin implements AdminComponent {
    private $patientModel;
    private $comorbidityModel;
    private $complicationModel;
    private $bmiModel;
    private $typesModel;
    private $courseModel;
    private $adminModel;
    private $followUpModel;
    

   
    public function __construct() {
        // Instantiate the PatientModel
        $this->patientModel = new PatientModel();
        $this->comorbidityModel = new ComorbidityModel();
        $this->complicationModel = new ComplicationModel();
        $this->bmiModel = new BMIModel();
        $this->typesModel = new TypesModel();
        $this->courseModel = new CourseModel();
        $this->adminModel = new AdminModel();
        $this->followUpModel = new FollowUpModel();
    }

    
    public function addPatient($data, $adminId) {
       
        // Log that the addPatient function is called
        error_log("[DEBUG] Admin: addPatient function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }

        $addPatientCommand = new AddPatientCommand($this->patientModel, $data, $adminId);
            error_log("[DEBUG] AddPatientCommand created.");

            // Execute the command
            $patientId = $addPatientCommand->execute();

        // Log the result
        if ($patientId) {
            error_log("[INFO] Patient added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add patient by admin ID: $adminId.");
        }

        return $patientId;
    }

   

    public function addComorbidity($data, $adminId) {
        
        // Log that the addComorbidity function is called
        error_log("[DEBUG] Admin: addComorbidity function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }

        // Call the PatientModel's addPatient function
        $success = $this->comorbidityModel->insertComorbidity($data, $adminId);

        // Log the result
        if ($success) {
            error_log("[INFO] Comobidity added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add comorbidity by admin ID: $adminId.");
        }

        return $success;
    }

    public function addComplication($data, $adminId) {
        // Log that the addComplication function is called
        error_log("[DEBUG] Admin: addComplication function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }

        // Call the PatientModel's addPatient function
        $success = $this->complicationModel->insertComplication($data, $adminId);

        // Log the result
        if ($success) {
            error_log("[INFO] Complication added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add complication by admin ID: $adminId.");
        }

        return $success;
    }

    public function addBMI($data, $adminId) {
        // Log that the addBMI function is called
        error_log("[DEBUG] Admin: addBMI function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }

        // Call the PatientModel's addPatient function
        $success = $this->bmiModel->insertBMI($data, $adminId);

        // Log the result
        if ($success) {
            error_log("[INFO] Complication added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add complication by admin ID: $adminId.");
        }

        return $success;
    }

    public function addSurgery($data, $adminId) {
         // Log that the addSurgery function is called
         error_log("[DEBUG] Admin: addSurgery function called.");

         // Validate the admin ID
         if (!$adminId) {
             error_log("[ERROR] Admin ID is missing.");
             return false;
         }
 
         // Call the PatientModel's addPatient function
         $success = $this->typesModel->insertType($data, $adminId);
 
         // Log the result
         if ($success) {
             error_log("[INFO] Surgery added successfully by admin ID: $adminId.");
         } else {
             error_log("[ERROR] Failed to add Surgery by admin ID: $adminId.");
         }
 
         return $success;
    }

    public function approveAccount($username) {
        
         // Log that the approveAccount function is called
         error_log("[DEBUG] Admin: approveAccount function called.");

        
         $this->adminModel->approveAccount($username);
 
 
     }
 
        
    
    public function addCourse($data, $adminId) {
        
        // Log that the addCourse function is called
        error_log("[DEBUG] Admin: addPatient function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }
        $command = new AddCourseCommand($this->courseModel, $data, $adminId);
        error_log("[DEBUG] AddCourseCommand created.");

        // Execute the command
        $courseId = $command->execute();


        // Log the result
        if ($courseId) {
            error_log("[INFO] Course added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add course by admin ID: $adminId.");
        }

        return $courseId;
    }

    public function followUpPatient() { 
        error_log("[DEBUG] Admin: followUpPatient function called.");
        $patients= $this->followUpModel->getPatients(); 
        return $patients;
    }
}

// AdminDecorator Abstract Class
abstract class AdminDecorator implements AdminComponent {
    protected $admin;

    public function __construct(AdminComponent $admin) {
        error_log("[DECORATOR] Wrapping " . get_class($admin) . " with " . get_class($this) . ".");
        $this->admin = $admin;
    }

    abstract public function addPatient($data, $adminId);
    abstract public function addComorbidity($data, $adminId);
   
    abstract public function addComplication($data, $adminId);
    abstract  public function addBMI($data, $adminId);
    abstract public function addSurgery($data, $adminId);
    abstract public function approveAccount($username);
    abstract public function addCourse($data, $adminId);
    abstract public function followUpPatient(); 
}

// SuperAdminDecorator Class
class SuperAdmin extends AdminDecorator {
    private $patientModel;
    private $comorbidityModel;
    private $complicationModel;
    private $bmiModel;
    private $typesModel;
    private $courseModel;
    private $adminModel;
    private $followUpModel;
    

   
    public function __construct(AdminComponent $admin) {
        error_log("[DECORATOR] SuperAdmin decorator applied to " . get_class($admin) . ".");
        $this->admin = $admin;
        $this->patientModel = new PatientModel();
        $this->comorbidityModel = new ComorbidityModel();
        $this->complicationModel = new ComplicationModel();
        $this->bmiModel = new BMIModel();
        $this->typesModel = new TypesModel();
        $this->courseModel = new CourseModel();
        $this->adminModel = new AdminModel();
        $this->followUpModel = new FollowUpModel();
    }

    

    public function addPatient($data, $adminId) {
       
        // Log that the addPatient function is called
        error_log("[DEBUG] Admin: addPatient function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }

        $addPatientCommand = new AddPatientCommand($this->patientModel, $data, $adminId);
            error_log("[DEBUG] AddPatientCommand created.");

            // Execute the command
            $patientId = $addPatientCommand->execute();

        // Log the result
        if ($patientId) {
            error_log("[INFO] Patient added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add patient by admin ID: $adminId.");
        }

        return $patientId;
    }

   

    public function addComorbidity($data, $adminId) {
        
        // Log that the addComorbidity function is called
        error_log("[DEBUG] Admin: addComorbidity function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }

        // Call the PatientModel's addPatient function
        $success = $this->comorbidityModel->insertComorbidity($data, $adminId);

        // Log the result
        if ($success) {
            error_log("[INFO] Comobidity added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add comorbidity by admin ID: $adminId.");
        }

        return $success;
    }

    public function addComplication($data, $adminId) {
        // Log that the addComplication function is called
        error_log("[DEBUG] Admin: addComplication function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }

        // Call the PatientModel's addPatient function
        $success = $this->complicationModel->insertComplication($data, $adminId);

        // Log the result
        if ($success) {
            error_log("[INFO] Complication added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add complication by admin ID: $adminId.");
        }

        return $success;
    }

    public function addBMI($data, $adminId) {
        // Log that the addBMI function is called
        error_log("[DEBUG] Admin: addBMI function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }

        // Call the PatientModel's addPatient function
        $success = $this->bmiModel->insertBMI($data, $adminId);

        // Log the result
        if ($success) {
            error_log("[INFO] Complication added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add complication by admin ID: $adminId.");
        }

        return $success;
    }

    public function addSurgery($data, $adminId) {
         // Log that the addSurgery function is called
         error_log("[DEBUG] Admin: addSurgery function called.");

         // Validate the admin ID
         if (!$adminId) {
             error_log("[ERROR] Admin ID is missing.");
             return false;
         }
 
         // Call the PatientModel's addPatient function
         $success = $this->typesModel->insertType($data, $adminId);
 
         // Log the result
         if ($success) {
             error_log("[INFO] Surgery added successfully by admin ID: $adminId.");
         } else {
             error_log("[ERROR] Failed to add Surgery by admin ID: $adminId.");
         }
 
         return $success;
    }

    public function approveAccount($username) {
        
         // Log that the approveAccount function is called
         error_log("[DEBUG] Admin: approveAccount function called.");

        
         $this->adminModel->approveAccount($username);
 
 
     }
 
    public function addCourse($data, $adminId) {
        
        // Log that the addCourse function is called
        error_log("[DEBUG] Admin: addPatient function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }
        $command = new AddCourseCommand($this->courseModel, $data, $adminId);
        error_log("[DEBUG] AddCourseCommand created.");

        // Execute the command
        $courseId = $command->execute();


        // Log the result
        if ($courseId) {
            error_log("[INFO] Course added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add course by admin ID: $adminId.");
        }

        return $courseId;
    }

    public function editCourse($courseId, $updatedData) {
        
        // Log that the editCourse function is called
        error_log("[DEBUG] Admin: editCourse function called.");
        try {
        // Call the CourseModel's editCourse function
        $this->courseModel->updateCourse($courseId, $updatedData);
        error_log("[INFO] Course $courseId edited successfully by admin.");
        }catch (Exception $e) {
            error_log("[ERROR] Failed to edited course with ID $courseId: " . $e->getMessage());
            throw new Exception("Failed to edit course");
        }

       
    }

    public function followUpPatient() { 
        error_log("[DEBUG] Admin: followUpPatient function called.");
        $patients= $this->followUpModel->getPatients(); 
        return $patients;
    }

    public function editPatient($data, $patientId) {
        
        // Log that the editPatient function is called
        error_log("[DEBUG] Admin: editPatient function called.");

        // Call the PatientModel's editPatient function
        $result = $this->patientModel->updatePatientDetails($data, $patientId);

        // Log the result
        if ($result) {
            error_log("[INFO] Patient $patientId deleted successfully by admin.");
        } else {
            error_log("[ERROR] Failed to delete patient $patientId by admin.");
        }

        return $result;
    }
}

// ChiefAdminDecorator Class
class ChiefAdmin extends AdminDecorator {
    private $patientModel;
    private $comorbidityModel;
    private $complicationModel;
    private $bmiModel;
    private $typesModel;
    private $courseModel;
    private $adminModel;
    private $followUpModel;
    

   
    public function __construct(AdminComponent $admin) {
        error_log("[DECORATOR] ChiefAdmin decorator applied to " . get_class($admin) . ".");
        $this->admin = $admin;
        $this->patientModel = new PatientModel();
        $this->comorbidityModel = new ComorbidityModel();
        $this->complicationModel = new ComplicationModel();
        $this->bmiModel = new BMIModel();
        $this->typesModel = new TypesModel();
        $this->courseModel = new CourseModel();
        $this->adminModel = new AdminModel();
        $this->followUpModel = new FollowUpModel();
    }

    
    public function addPatient($data, $adminId) {
       
        // Log that the addPatient function is called
        error_log("[DEBUG] Admin: addPatient function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }

        $addPatientCommand = new AddPatientCommand($this->patientModel, $data, $adminId);
            error_log("[DEBUG] AddPatientCommand created.");

            // Execute the command
            $patientId = $addPatientCommand->execute();

        // Log the result
        if ($patientId) {
            error_log("[INFO] Patient added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add patient by admin ID: $adminId.");
        }

        return $patientId;
    }

   

    public function addComorbidity($data, $adminId) {
        
        // Log that the addComorbidity function is called
        error_log("[DEBUG] Admin: addComorbidity function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }

        // Call the PatientModel's addPatient function
        $success = $this->comorbidityModel->insertComorbidity($data, $adminId);

        // Log the result
        if ($success) {
            error_log("[INFO] Comobidity added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add comorbidity by admin ID: $adminId.");
        }

        return $success;
    }

    public function addComplication($data, $adminId) {
        // Log that the addComplication function is called
        error_log("[DEBUG] Admin: addComplication function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }

        // Call the PatientModel's addPatient function
        $success = $this->complicationModel->insertComplication($data, $adminId);

        // Log the result
        if ($success) {
            error_log("[INFO] Complication added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add complication by admin ID: $adminId.");
        }

        return $success;
    }

    public function addBMI($data, $adminId) {
        // Log that the addBMI function is called
        error_log("[DEBUG] Admin: addBMI function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }

        // Call the PatientModel's addPatient function
        $success = $this->bmiModel->insertBMI($data, $adminId);

        // Log the result
        if ($success) {
            error_log("[INFO] BMI added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add BMI by admin ID: $adminId.");
        }

        return $success;
    }

    public function addSurgery($data, $adminId) {
         // Log that the addSurgery function is called
         error_log("[DEBUG] Admin: addSurgery function called.");

         // Validate the admin ID
         if (!$adminId) {
             error_log("[ERROR] Admin ID is missing.");
             return false;
         }
 
         // Call the PatientModel's addPatient function
         $success = $this->typesModel->insertType($data, $adminId);
 
         // Log the result
         if ($success) {
             error_log("[INFO] Surgery added successfully by admin ID: $adminId.");
         } else {
             error_log("[ERROR] Failed to add Surgery by admin ID: $adminId.");
         }
 
         return $success;
    }

    public function approveAccount($username) {
        
         // Log that the approveAccount function is called
         error_log("[DEBUG] Admin: approveAccount function called.");

        
         $this->adminModel->approveAccount($username);
 
 
     }
 
        
    
    public function addCourse($data, $adminId) {
        
        // Log that the addCourse function is called
        error_log("[DEBUG] Admin: addPatient function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }
        $command = new AddCourseCommand($this->courseModel, $data, $adminId);
        error_log("[DEBUG] AddCourseCommand created.");

        // Execute the command
        $courseId = $command->execute();


        // Log the result
        if ($courseId) {
            error_log("[INFO] Course added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add course by admin ID: $adminId.");
        }

        return $courseId;
    }

    public function followUpPatient() { 
        error_log("[DEBUG] Admin: followUpPatient function called.");
        $patients= $this->followUpModel->getPatients(); 
        return $patients;
    }

    
    public function upgradeAdmin(AdminComponent $adminToUpgrade) {
        error_log("[DEBUG] Admin: upgradeAdmin function called.");
        error_log("[DEBUG] Admin to upgrade: " . get_class($adminToUpgrade));
        if ($adminToUpgrade instanceof BaseAdmin) {
            // Upgrade BaseAdmin to SuperAdmin
            error_log("[DECORATOR] Upgrading BaseAdmin to SuperAdmin.");
            return new SuperAdmin($adminToUpgrade); // Return a new instance of SuperAdmin
        } elseif ($adminToUpgrade instanceof SuperAdmin) {
            error_log("[DECORATOR] Upgrading SuperAdmin to ChiefAdmin.");
            return new ChiefAdmin($adminToUpgrade); // Return a new instance of SuperAdmin
        }
        return "Chief Admin: Unable to upgrade this admin.";
    }


    public function deletePatient($patientId) {
        
        // Log that the deletePatient function is called
        error_log("[DEBUG] Admin: deletePatient function called.");

        // Call the PatientModel's deletePatient function
        $success =$this->patientModel->deletePatient($patientId);

        // Log the result
        if ($success) {
            error_log("[INFO] Patient $patientId deleted successfully by admin.");
        } else {
            error_log("[ERROR] Failed to delete patient $patientId by admin.");
        }

        return $success;
    }

    public function editPatient($data, $patientId) {
        
        // Log that the editPatient function is called
        error_log("[DEBUG] Admin: editPatient function called.");

        // Call the PatientModel's editPatient function
        $result = $this->patientModel->updatePatientDetails($data, $patientId);

        // Log the result
        if ($result) {
            error_log("[INFO] Patient $patientId edited successfully by admin.");
        } else {
            error_log("[ERROR] Failed to edited patient $patientId by admin.");
        }

        return $result;
    }

    public function deleteCourse($courseId) {
        
        // Log that the deletePatient function is called
        error_log("[DEBUG] Admin: deleteCourse function called.");
        try {
        // Call the PatientModel's deletePatient function
        $this->courseModel->deleteCourse($courseId);
        error_log("[INFO] Course $courseId deleted successfully by admin.");
        }catch (Exception $e) {
            error_log("[ERROR] Failed to delete course with ID $courseId: " . $e->getMessage());
            throw new Exception("Failed to delete course");
        }

       
    }

    public function editCourse($courseId, $updatedData) {
        
        // Log that the editCourse function is called
        error_log("[DEBUG] Admin: editCourse function called.");
        try {
        // Call the CourseModel's editCourse function
        $this->courseModel->updateCourse($courseId, $updatedData);
        error_log("[INFO] Course $courseId edited successfully by admin.");
        }catch (Exception $e) {
            error_log("[ERROR] Failed to edited course with ID $courseId: " . $e->getMessage());
            throw new Exception("Failed to edit course");
        }

       
    }



}


// PaymentAdmin Class
class PaymentAdmin extends AdminDecorator {
    private $patientModel;
    private $comorbidityModel;
    private $complicationModel;
    private $bmiModel;
    private $typesModel;
    private $courseModel;
    private $adminModel;
    private $followUpModel;
    private $donateModel;

   
    public function __construct(AdminComponent $admin) {
        $this->admin = $admin;
        $this->patientModel = new PatientModel();
        $this->comorbidityModel = new ComorbidityModel();
        $this->complicationModel = new ComplicationModel();
        $this->bmiModel = new BMIModel();
        $this->typesModel = new TypesModel();
        $this->courseModel = new CourseModel();
        $this->adminModel = new AdminModel();
        $this->followUpModel = new FollowUpModel();
        $this->donateModel = new DonateModel();
    }

    
    public function addPatient($data, $adminId) {
       
        // Log that the addPatient function is called
        error_log("[DEBUG] Admin: addPatient function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }

        $addPatientCommand = new AddPatientCommand($this->patientModel, $data, $adminId);
            error_log("[DEBUG] AddPatientCommand created.");

            // Execute the command
            $patientId = $addPatientCommand->execute();

        // Log the result
        if ($patientId) {
            error_log("[INFO] Patient added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add patient by admin ID: $adminId.");
        }

        return $patientId;
    }

   

    public function addComorbidity($data, $adminId) {
        
        // Log that the addComorbidity function is called
        error_log("[DEBUG] Admin: addComorbidity function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }

        // Call the PatientModel's addPatient function
        $success = $this->comorbidityModel->insertComorbidity($data, $adminId);

        // Log the result
        if ($success) {
            error_log("[INFO] Comobidity added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add comorbidity by admin ID: $adminId.");
        }

        return $success;
    }

    public function addComplication($data, $adminId) {
        // Log that the addComplication function is called
        error_log("[DEBUG] Admin: addComplication function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }

        // Call the PatientModel's addPatient function
        $success = $this->complicationModel->insertComplication($data, $adminId);

        // Log the result
        if ($success) {
            error_log("[INFO] Complication added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add complication by admin ID: $adminId.");
        }

        return $success;
    }

    public function addBMI($data, $adminId) {
        // Log that the addBMI function is called
        error_log("[DEBUG] Admin: addBMI function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }

        // Call the PatientModel's addPatient function
        $success = $this->bmiModel->insertBMI($data, $adminId);

        // Log the result
        if ($success) {
            error_log("[INFO] BMI added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add BMI by admin ID: $adminId.");
        }

        return $success;
    }

    public function addSurgery($data, $adminId) {
         // Log that the addSurgery function is called
         error_log("[DEBUG] Admin: addSurgery function called.");

         // Validate the admin ID
         if (!$adminId) {
             error_log("[ERROR] Admin ID is missing.");
             return false;
         }
 
         // Call the PatientModel's addPatient function
         $success = $this->typesModel->insertType($data, $adminId);
 
         // Log the result
         if ($success) {
             error_log("[INFO] Surgery added successfully by admin ID: $adminId.");
         } else {
             error_log("[ERROR] Failed to add Surgery by admin ID: $adminId.");
         }
 
         return $success;
    }

    public function approveAccount($username) {
        
         // Log that the approveAccount function is called
         error_log("[DEBUG] Admin: approveAccount function called.");

        
         $this->adminModel->approveAccount($username);
 
 
     }
 
        
    
    public function addCourse($data, $adminId) {
        
        // Log that the addCourse function is called
        error_log("[DEBUG] Admin: addPatient function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }
        $command = new AddCourseCommand($this->courseModel, $data, $adminId);
        error_log("[DEBUG] AddCourseCommand created.");

        // Execute the command
        $courseId = $command->execute();


        // Log the result
        if ($courseId) {
            error_log("[INFO] Course added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add course by admin ID: $adminId.");
        }

        return $courseId;
    }

    public function followUpPatient() { 
        error_log("[DEBUG] Admin: followUpPatient function called.");
        $patients= $this->followUpModel->getPatients(); 
        return $patients;
    }

    public function viewCashDonations() {
        error_log("[DEBUG] PaymentAdmin: viewCashDonations function called.");

        // Fetch all donations made in cash
        $donations = $this->donateModel->getDonationsByType('cash');

        if (!empty($donations)) {
            error_log("[INFO] Cash donations retrieved successfully.");
        } else {
            error_log("[INFO] No cash donations found.");
        }
        error_log("[DEBUG] Cash donations: " . json_encode($donations));

        return $donations;
    }


    public function collectCashDonation($donationId) {
        error_log("[DEBUG] PaymentAdmin: collectCashDonation function called with donation ID: $donationId.");

        // Fetch the donation details by ID
        $donation = $this->donateModel->getDonationById($donationId);
            // Update the approval status
    $success = $this->donateModel->updateDonationApprovalStatus($donationId);
    if ($success) {
        $_SESSION['message'] = "Donation ID $donationId approved successfully.";
        error_log("[INFO] Donation ID $donationId approved successfully.");
    } else {
        $_SESSION['message'] = "Failed to approve donation ID $donationId.";
        error_log("[ERROR] Failed to approve donation ID $donationId.");
    }


        if (!$donation) {
            error_log("[ERROR] Donation not found for ID: $donationId.");
            return false;
        }

        // Ensure the donation type is 'cash'
        if ($donation['donation_type'] !== 'cash') {
            error_log("[ERROR] Donation ID: $donationId is not a cash donation.");
            return false;
        }

        $courseId = $donation['course_id'] ?? null; // Handle undefined course_id
if (!$courseId) {
    error_log("[ERROR] Donation ID $donationId has no associated course ID.");
    return false;
}

$amount = $donation['amount'] ?? 0; // Handle undefined amount
if ($amount <= 0) {
    error_log("[ERROR] Invalid donation amount for ID $donationId.");
    return false;
}

$success = $this->donateModel->updateCourseDonation($courseId, $amount);


        if ($success) {
            error_log("[INFO] Cash donation of $amount collected for course ID: $courseId.");
        } else {
            error_log("[ERROR] Failed to update course donations received for course ID: $courseId.");
        }

        return $success;
    }

}





// DonationAdmin Class
class DonationAdmin extends AdminDecorator {
    private $patientModel;
    private $comorbidityModel;
    private $complicationModel;
    private $bmiModel;
    private $typesModel;
    private $courseModel;
    private $adminModel;
    private $followUpModel;
    private $donate_supplies_model;

   
    public function __construct(AdminComponent $admin) {
        $this->admin = $admin;
        $this->patientModel = new PatientModel();
        $this->comorbidityModel = new ComorbidityModel();
        $this->complicationModel = new ComplicationModel();
        $this->bmiModel = new BMIModel();
        $this->typesModel = new TypesModel();
        $this->courseModel = new CourseModel();
        $this->adminModel = new AdminModel();
        $this->followUpModel = new FollowUpModel();
        $this->donate_supplies_model = new DonateSuppliesModel();
    }

    
    public function addPatient($data, $adminId) {
       
        // Log that the addPatient function is called
        error_log("[DEBUG] Admin: addPatient function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }

        $addPatientCommand = new AddPatientCommand($this->patientModel, $data, $adminId);
            error_log("[DEBUG] AddPatientCommand created.");

            // Execute the command
            $patientId = $addPatientCommand->execute();

        // Log the result
        if ($patientId) {
            error_log("[INFO] Patient added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add patient by admin ID: $adminId.");
        }

        return $patientId;
    }

   

    public function addComorbidity($data, $adminId) {
        
        // Log that the addComorbidity function is called
        error_log("[DEBUG] Admin: addComorbidity function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }

        // Call the PatientModel's addPatient function
        $success = $this->comorbidityModel->insertComorbidity($data, $adminId);

        // Log the result
        if ($success) {
            error_log("[INFO] Comobidity added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add comorbidity by admin ID: $adminId.");
        }

        return $success;
    }

    public function addComplication($data, $adminId) {
        // Log that the addComplication function is called
        error_log("[DEBUG] Admin: addComplication function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }

        // Call the PatientModel's addPatient function
        $success = $this->complicationModel->insertComplication($data, $adminId);

        // Log the result
        if ($success) {
            error_log("[INFO] Complication added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add complication by admin ID: $adminId.");
        }

        return $success;
    }

    public function addBMI($data, $adminId) {
        // Log that the addBMI function is called
        error_log("[DEBUG] Admin: addBMI function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }

        // Call the PatientModel's addPatient function
        $success = $this->bmiModel->insertBMI($data, $adminId);

        // Log the result
        if ($success) {
            error_log("[INFO] BMI added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add BMI by admin ID: $adminId.");
        }

        return $success;
    }

    public function addSurgery($data, $adminId) {
         // Log that the addSurgery function is called
         error_log("[DEBUG] Admin: addSurgery function called.");

         // Validate the admin ID
         if (!$adminId) {
             error_log("[ERROR] Admin ID is missing.");
             return false;
         }
 
         // Call the PatientModel's addPatient function
         $success = $this->typesModel->insertType($data, $adminId);
 
         // Log the result
         if ($success) {
             error_log("[INFO] Surgery added successfully by admin ID: $adminId.");
         } else {
             error_log("[ERROR] Failed to add Surgery by admin ID: $adminId.");
         }
 
         return $success;
    }

    public function approveAccount($username) {
        
         // Log that the approveAccount function is called
         error_log("[DEBUG] Admin: approveAccount function called.");

        
         $this->adminModel->approveAccount($username);
 
 
     }
 
        
    
    public function addCourse($data, $adminId) {
        
        // Log that the addCourse function is called
        error_log("[DEBUG] Admin: addPatient function called.");

        // Validate the admin ID
        if (!$adminId) {
            error_log("[ERROR] Admin ID is missing.");
            return false;
        }
        $command = new AddCourseCommand($this->courseModel, $data, $adminId);
        error_log("[DEBUG] AddCourseCommand created.");

        // Execute the command
        $courseId = $command->execute();


        // Log the result
        if ($courseId) {
            error_log("[INFO] Course added successfully by admin ID: $adminId.");
        } else {
            error_log("[ERROR] Failed to add course by admin ID: $adminId.");
        }

        return $courseId;
    }

    public function followUpPatient() { 
        error_log("[DEBUG] Admin: followUpPatient function called.");
        $patients= $this->followUpModel->getPatients(); 
        return $patients;
    }

    public function getDonationSupplies() {
        error_log("[DEBUG] DonationAdmin: listDonationItems function called.");
        try {
            $items = $this->donate_supplies_model->getAllDonations();
            error_log("[INFO] Retrieved donation items successfully.");
            return $items;
        } catch (Exception $e) {
            error_log("[ERROR] Failed to retrieve donation items: " . $e->getMessage());
            return [];
        }
    }

    public function approveDonationSupply($itemId): array|bool {
        error_log("[DEBUG] DonationAdmin: approveDonationItem function called.");
        try {
            $items = $this->donate_supplies_model->approveDonationItem($itemId);
            error_log("[INFO] Approved donation items successfully.");
            return $items;
        } catch (Exception $e) {
            error_log("[ERROR] Failed to approve donation items: " . $e->getMessage());
            return [];
        }
    }

    public function rejectDonationSupply($itemId) {
        error_log("[DEBUG] DonationAdmin: rejectDonationItem function called.");
        try {
            $items = $this->donate_supplies_model->rejectDonationItem($itemId);
            error_log("[INFO] Rejected donation items successfully.");
            return $items;
        } catch (Exception $e) {
            error_log("[ERROR] Failed to reject donation items: " . $e->getMessage());
            return [];
        }
    }
    public function checkDeliveryStatus($itemId) {
        error_log("[DEBUG] DonationAdmin: checkDeliveryStatus function called.");
        try {
            $deliveryStatus = $this->donate_supplies_model->checkDeliveryStatus($itemId);
            error_log("[INFO] Checked delivery status of donation item successfully.");
            return $deliveryStatus;
        } catch (Exception $e) {
            error_log("[ERROR] Failed to check delivery status of donation items: " . $e->getMessage());
            return [];
        }
    }

}

?>
