<?php
session_start();
require_once '../Model/follow_up_model.php';
require_once '../Design Patterns/ReportStrategy.php';
require_once '../Design Patterns/Decorater.php';
require_once '../Model/admin_model.php'; 

class FollowUpController {
    private $adminComponent; // Use AdminComponent instead of FollowUpModel directly
    private $reportGenerator;
    private $reportType;

    public function __construct() {
        // Get the current admin instance from the session
        $adminModel = new AdminModel();
        $this->adminComponent = $adminModel->getCurrentAdminInstance(); // Get the appropriate admin instance (BaseAdmin, SuperAdmin, or ChiefAdmin)

        // Initialize the reportGenerator with a default strategy (e.g., PDF)
        $this->reportGenerator = new ContextGenerator(new PDFReportStrategy());
        $this->reportType = 'pdf'; // Default report type

        // Initialize allPatients in session only if not already set
        if (empty($_SESSION['allPatientsForFollowUp'])) {
            $_SESSION['allPatientsForFollowUp'] = $this->adminComponent->followUpPatient(); // Use the AdminComponent's followUpPatient method
            error_log("Initialized allPatients in session: " . print_r($_SESSION['allPatientsForFollowUp'], true)); // Debugging line
        }

        // Initialize searchResults to allPatients if not already set
        if (empty($_SESSION['searchResults'])) {
            $_SESSION['searchResults'] = $_SESSION['allPatientsForFollowUp'];
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
        error_log("Search Query: " . $query); // Log the search query
    
        $patients = $_SESSION['allPatientsForFollowUp'];
        error_log("All Patients: " . print_r($patients, true)); // Log all patients
    
        $_SESSION['searchResults'] = array_filter($patients, function($patient) use ($query) {
            $match = stripos($patient['FullName'], $query) !== false;
            error_log("Patient: " . $patient['FullName'] . " - Match: " . ($match ? "Yes" : "No")); // Log match status
            return $match;
        });
    
        error_log("Search Results: " . print_r($_SESSION['searchResults'], true)); // Log search results
    }

    // Function to filter the first record per patient name
    public function filterFirstRecordPerName($patients) {
        $filtered = [];
        $seenNames = [];
        foreach ($patients as $patient) {
            if (!in_array($patient['FullName'], $seenNames)) {
                $filtered[] = $patient;
                $seenNames[] = $patient['FullName'];
            }
        }
        return $filtered;
    }

    // Generate report using the current search results
    public function generateReport() {
        // Access session data directly
        $patients = $_SESSION['searchResults'] ?? [];

        // Filter data for the report using $this->filterFirstRecordPerName
        $filteredPatients = $this->filterFirstRecordPerName($patients);

        // Log session data to debug
        error_log("Filtered data for report generation: " . print_r($filteredPatients, true));

        if (empty($filteredPatients)) {
            error_log("No patients found for report generation.");
        } else {
            error_log("Generating report with the following data: " . print_r($filteredPatients, true));
        }

        // Generate the report
        $this->reportGenerator->generateReport($filteredPatients);
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
