<?php
// AdminComponent Interface
interface AdminComponent {
    public function addPatient();
    public function addComorbidity();
    public function addComplication();
    public function addBMI();
    public function addSurgery();
    public function approveAccount();
    public function addCourse();
    public function followUpPatient(); // New method added
}

// BaseAdmin Class
class BaseAdmin implements AdminComponent {
    public function addPatient() {
        return "Patient added.";
    }

    public function addComorbidity() {
        return "Comorbidity added.";
    }

    public function addComplication() {
        return "Complication added.";
    }

    public function addBMI() {
        return "BMI added.";
    }

    public function addSurgery() {
        return "Surgery added.";
    }

    public function approveAccount() {
        return "Account approved.";
    }

    public function addCourse() {
        return "Course added.";
    }

    public function followUpPatient() { // New method implementation
        return "Follow-up for patient completed.";
    }
}

// AdminDecorator Abstract Class
abstract class AdminDecorator implements AdminComponent {
    protected $admin;

    public function __construct(AdminComponent $admin) {
        $this->admin = $admin;
    }

    abstract public function addPatient();
    abstract public function addComorbidity();
    abstract public function addComplication();
    abstract public function addBMI();
    abstract public function addSurgery();
    abstract public function approveAccount();
    abstract public function addCourse();
    abstract public function followUpPatient(); // New method declaration
}

// SuperAdminDecorator Class
class SuperAdmin extends AdminDecorator {
    public function addPatient() {
        return $this->admin->addPatient() . " Super Admin: Additional permissions granted.";
    }

    public function addComorbidity() {
        return $this->admin->addComorbidity();
    }

    public function addComplication() {
        return $this->admin->addComplication();
    }

    public function addBMI() {
        return $this->admin->addBMI();
    }

    public function addSurgery() {
        return $this->admin->addSurgery();
    }

    public function approveAccount() {
        return $this->admin->approveAccount() . " Super Admin: Can approve all requests.";
    }

    public function updatePatient() {
        return "Patient updated by Super Admin.";
    }

    public function addCourse() {
        return "Course added.";
    }

    public function followUpPatient() { // New method implementation
        return $this->admin->followUpPatient() . " Follow-up managed by Super Admin.";
    }
}

// ChiefAdminDecorator Class
class ChiefAdmin extends AdminDecorator {
    public function addPatient() {
        return $this->admin->addPatient() . " Chief Admin: Enhanced management features.";
    }

    public function addCourse() {
        return "Course added.";
    }

    public function addComorbidity() {
        return $this->admin->addComorbidity();
    }

    public function addComplication() {
        return $this->admin->addComplication();
    }

    public function addBMI() {
        return $this->admin->addBMI();
    }

    public function addSurgery() {
        return $this->admin->addSurgery();
    }

    public function approveAccount() {
        return $this->admin->approveAccount() . " Chief Admin: Full oversight.";
    }

    public function updatePatient() {
        return "Patient updated by Chief Admin.";
    }

    public function deletePatient() {
        return "Patient deleted by Chief Admin.";
    }

    public function followUpPatient() { // New method implementation
        return $this->admin->followUpPatient() . " Follow-up managed by Chief Admin.";
    }



    
    public function upgradeAdmin(AdminComponent $adminToUpgrade) {
        if ($adminToUpgrade instanceof BaseAdmin) {
            // Upgrade BaseAdmin to SuperAdmin
            return new SuperAdmin($adminToUpgrade); // Return a new instance of SuperAdmin
        } elseif ($adminToUpgrade instanceof SuperAdmin) {
            return new ChiefAdmin($adminToUpgrade); // Return a new instance of SuperAdmin
        }
        return "Chief Admin: Unable to upgrade this admin.";
    }
}
/* 
// Example usage
$baseAdmin = new BaseAdmin();
$superAdmin = new SuperAdmin($baseAdmin);
$chiefAdmin = new ChiefAdmin($superAdmin);

//echo $baseAdmin->addPatient() . "<br>";
//echo $superAdmin->updatePatient() . "<br>"; // Ensure this method exists in SuperAdmin
echo $chiefAdmin->deletePatient() . "<br>"; // Ensure this method exists in ChiefAdmin
echo $chiefAdmin->followUpPatient() . "<br>"; // Call follow-up method

// Example usage
$baseAdmin = new BaseAdmin();
$chiefAdmin = new ChiefAdmin($baseAdmin);

// Upgrading the base admin to a Super Admin
$upgradedAdmin = $chiefAdmin->upgradeAdmin($baseAdmin);

if ($upgradedAdmin instanceof SuperAdmin) {
    echo "Admin successfully upgraded to Super Admin.<br>";
    echo $upgradedAdmin->addPatient(); // Test new behavior
} else {
    echo $upgradedAdmin; // Display the message if not upgraded
}

 */?>
