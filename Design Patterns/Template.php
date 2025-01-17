<?php
abstract class ReportTemplate {
    public function generateTemplate($patients) {
        error_log("[" . date('Y-m-d H:i:s') . "] Initializing report generation.", 0);
        $this->initializeReport();
        
        error_log("[" . date('Y-m-d H:i:s') . "] Adding report title.", 0);
        $this->addTitle();
        
        error_log("[" . date('Y-m-d H:i:s') . "] Adding patient data.", 0);
        $this->addPatientData($patients);
        
        error_log("[" . date('Y-m-d H:i:s') . "] Finalizing the report.", 0);
        $this->finalizeReport();
        
        error_log("[" . date('Y-m-d H:i:s') . "] Report generation completed.", 0);
    }

    protected abstract function initializeReport();
    protected abstract function addTitle();
    protected abstract function addPatientData($patients);
    protected abstract function finalizeReport();
}
