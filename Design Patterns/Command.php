<?php

require_once '../Model/course_model.php';

// Command Interface
interface Command {
    public function execute();
    public function undo();
}

// AddCourseCommand
class AddCourseCommand implements Command {
    private $courseModel;
    private $courseData;
    private $adminId;
    private $courseId;
    

    public function __construct($courseModel, $courseData, $adminId) {
        $this->courseModel = $courseModel;
        $this->courseData = $courseData;
        $this->adminId = $adminId;
    }

    public function execute() {
        try {
            // Add the course
            $courseId = $this->courseModel->addCourse($this->courseData);
            if (!$courseId) {
                throw new Exception("Failed to add course.");
            }


            $this->courseId = $courseId;
            // Log the add course verb
            $verbId = $this->courseModel->logAddCourseVerb($this->adminId);
            if (!$verbId) {
                throw new Exception("Failed to log course addition action.");
            }

            // Log the add course details
            $this->courseModel->logAddCourseDetail($verbId, $courseId, "Added course: {$this->courseData['course_name']}");

            return $courseId;
        } catch (Exception $e) {
            error_log("AddCourseCommand failed: " . $e->getMessage());
            return null; // Return null or handle as appropriate
        }
    }


    public function undo() {
        try {
            if (!$this->courseId) {
                throw new Exception("No course ID found to undo.");
            }

            // Delete the course
            $success = $this->courseModel->deleteCourse($this->courseId);
            if (!$success) {
                throw new Exception("Failed to delete course during undo.");
            }

            // Log the undo action (optional)
            error_log("[INFO] Undo: Deleted course with ID: {$this->courseId}");

            return true;
        } catch (Exception $e) {
            error_log("AddCourseCommand undo failed: " . $e->getMessage());
            return false;
        }
    }
}

// AddPatientCommand
class AddPatientCommand implements Command {
    private $model;
    private $data;
    private $adminId;
    private $patientId;

    public function __construct($model, $data, $adminId) {
        $this->model = $model;
        $this->data = $data;
        $this->adminId = $adminId;
    }

    public function execute() {
        try {
            // Log the execution of the command
            error_log("AddPatientCommand: execute() called");

            // Call the model's addPatient function
            $patientId = $this->model->addPatient($this->data, $this->adminId);
            if (!$patientId) {
                throw new Exception("Failed to add patient.");
            }
            $this->patientId = $patientId;

            return $patientId;
        } catch (Exception $e) {
            error_log("AddPatientCommand failed: " . $e->getMessage());
            return null; // Return null or handle as appropriate
        }
    }

    public function undo() {
        try {
            if (!$this->patientId) {
                throw new Exception("No patient ID found to undo.");
            }

            // Delete the patient
            $success = $this->model->deletePatient($this->patientId);
            if (!$success) {
                throw new Exception("Failed to delete patient during undo.");
            }

            // Log the undo action (optional)
            error_log("[INFO] Undo: Deleted patient with ID: {$this->patientId}");

            return true;
        } catch (Exception $e) {
            error_log("AddPatientCommand undo failed: " . $e->getMessage());
            return false;
        }
    }
}
class CommandInvoker {
    private $commands = [];
    private $executedCommands = []; // Stack to track executed commands

    public function addCommand(Command $command) {
        $this->commands[] = $command;
        error_log("[DEBUG] Command added to CommandInvoker. Total commands: " . count($this->commands));
    }

    public function executeCommands() {
        $results = [];
        foreach ($this->commands as $command) {
            $results[] = $command->execute();
            $this->executedCommands[] = $command; // Track executed commands
            error_log("[DEBUG] Command executed and added to executedCommands stack. Total executed commands: " . count($this->executedCommands));
        }
        $this->commands = []; // Clear the commands after execution
        return $results;
    }

    public function undoLastCommand() {
        if (empty($this->executedCommands)) {
            error_log("[INFO] No commands to undo.");
            return false;
        }

        // Pop the last executed command
        $lastCommand = array_pop($this->executedCommands);
        error_log("[DEBUG] Undoing last command. Remaining executed commands: " . count($this->executedCommands));

        // Undo the command
        return $lastCommand->undo();
    }
}

class AddPlanCommand implements Command {
    private $planModel;
    private $planData;
    private $adminId;
    private $planId;

    public function __construct($planModel, $planData, $adminId) {
        $this->planModel = $planModel;
        $this->planData = $planData;
        $this->adminId = $adminId;
    }

    public function execute() {
        try {
            // Add the plan
            $planId = $this->planModel->addPlan($this->planData);
            if (!$planId) {
                throw new Exception("Failed to add plan.");
            }

            $this->planId = $planId;

            // Log the add plan verb
            $verbId = $this->planModel->logAddPlanVerb($this->adminId);
            if (!$verbId) {
                throw new Exception("Failed to log plan addition action.");
            }

            // Log the add plan details
            $this->planModel->logAddPlanDetail($verbId, $planId, "Added plan: {$this->planData['course_name']}");

            return $planId;
        } catch (Exception $e) {
            error_log("AddPlanCommand failed: " . $e->getMessage());
            return null; // Return null or handle as appropriate
        }
    }

    public function undo() {
        try {
            if (!$this->planId) {
                throw new Exception("No plan ID found to undo.");
            }

            // Delete the plan
            $success = $this->planModel->deletePlan($this->planId);
            if (!$success) {
                throw new Exception("Failed to delete plan during undo.");
            }

            // Log the undo action (optional)
            error_log("[INFO] Undo: Deleted plan with ID: {$this->planId}");

            return true;
        } catch (Exception $e) {
            error_log("AddPlanCommand undo failed: " . $e->getMessage());
            return false;
        }
    }
}

?>
