<?php
session_start();
require_once '../Model/follow_up_model.php';
require_once '../Design Patterns/ReportStrategy.php';

class FollowUpController {
    private $model;
    private $reportGenerator;
    private $reportType;

    public function __construct() {
        $this->model = new FollowUpModel();
        $this->reportGenerator = new ContextGenerator(new PDFReportStrategy());
        $this->reportType = 'pdf';
    
        // Initialize allPatients in session only if not already set
        if (empty($_SESSION['allPatients'])) {
            $_SESSION['allPatients'] = $this->model->getPatients();
            error_log("Initialized allPatients in session: " . print_r($_SESSION['allPatients'], true)); // Debugging line
        }
    
        // Initialize searchResults to allPatients if not already set
        if (empty($_SESSION['searchResults'])) {
            $_SESSION['searchResults'] = $_SESSION['allPatients'];
            error_log("Initialized searchResults in session: " . print_r($_SESSION['searchResults'], true)); // Debugging line
        }
    }
    

    // Function to change report type
    public function setReportStrategy($format) {
        switch ($format) {
            case 'pdf':
                $this->reportGenerator->setStrategy(new PDFReportStrategy());
                $this->reportType = 'pdf';
                break;
            case 'excel':
                $this->reportGenerator->setStrategy(new ExcelReportStrategy());
                $this->reportType = 'excel';
                break;
            case 'word':
                $this->reportGenerator->setStrategy(new WordReportStrategy());
                $this->reportType = 'word';
                break;
            default:
                throw new Exception("Invalid report format");
        }
        error_log("Report type set to: " . $this->reportType);
    }

    // Function to search patients
    public function searchPatients($query) {
        $patients = $_SESSION['allPatients'];

        // Clear previous search results
        $_SESSION['searchResults'] = [];

        // Filter patients based on the query
        $results = array_filter($patients, function($patient) use ($query) {
            return stripos($patient['TypeOfSurgery'], $query) !== false || stripos($patient['HospitalName'], $query) !== false;
        });

        // Store search results in session
        $_SESSION['searchResults'] = array_values($results);
        $_SESSION['searchQuery'] = $query;

        // Logging to confirm searchResults contains filtered data
        error_log("Search results after filtering: " . print_r($_SESSION['searchResults'], true));
    }

    // Generate report using the current search results
    public function generateReport() {
        // Access session data directly
        $patients = $_SESSION['searchResults'] ?? [];
    
        // Log session data to debug
        error_log("Session searchResults data before report generation: " . print_r($patients, true));
    
        if (empty($patients)) {
            error_log("No patients found for report generation.");
        } else {
            error_log("Generating report with the following data: " . print_r($patients, true));
        }
    
        // Proceed to generate the report
        $this->reportGenerator->generateReport($patients);
    
        // Do not unset session data until after successful report generation
         unset($_SESSION['searchResults']);
    }
    
    
    
}

// Instantiate the controller
$controller = new FollowUpController();

// Handle AJAX requests for report format selection
if (isset($_POST['report_format']) && isset($_POST['ajax'])) {
    $format = $_POST['report_format'];
    try {
        $controller->setReportStrategy($format);
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        error_log("Error in setReportStrategy: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'searchPatients':
                $searchQuery = $_POST['search'] ?? '';
                $controller->searchPatients($searchQuery);
                header("Location: ../View/follow_up_view.php");
                exit;

            case 'generateReport':
                $format = $_POST['report_format'] ?? 'pdf';
                try {
                    $controller->setReportStrategy($format);
                    $controller->generateReport();
                } catch (Exception $e) {
                    error_log("Error in generateReport: " . $e->getMessage());
                    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                }
                break;
        }
    }
}
?>