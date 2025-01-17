<?php
session_start();
require_once '../Model/compare_patient_data_model.php';
require_once '../Model/follow_up_model.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'importFile':
            try {
                if (isset($_FILES['importFile']) && $_FILES['importFile']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['importFile']['tmp_name'];
                    $fileName = $_FILES['importFile']['name'];
                    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    $uploadDir = '../uploads/';
                    $destinationPath = $uploadDir . basename($fileName);

                    // Ensure the upload directory exists
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    // Move the uploaded file
                    if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                        // Initialize FileParserModel
                        $fileParser = new FileParserModel();

                        // Set appropriate extractor based on file type
                        switch ($fileExtension) {
                            case 'pdf':
                                $fileParser->setExtractor(new ParsingAdapter(new PDFParser()));
                                break;
                            case 'csv':
                                $fileParser->setExtractor(new ParsingAdapter(new ExcelParser()));
                                break;
                            case 'docx':
                                $fileParser->setExtractor(new ParsingAdapter(new WordParser()));
                                break;
                            case 'txt':
                                $fileParser->setExtractor(new ExtractNormally());
                                break;
                            default:
                                throw new Exception("Unsupported file format: $fileExtension");
                        }

                        // Parse the file and store the result in session
                        $_SESSION['parsedContent'] = $fileParser->parseFile($destinationPath);
                        $_SESSION['uploadSuccess'] = "File successfully uploaded and parsed!";
                    } else {
                        throw new Exception("Error moving the uploaded file.");
                    }
                } else {
                    throw new Exception("No file uploaded or file upload error.");
                }
            } catch (Exception $e) {
                $_SESSION['uploadError'] = $e->getMessage();
            }

            // Retrieve PatientId from parsed content
            try {
                $parsedContent = $_SESSION['parsedContent'] ?? '';
                $patientId = null;

                if (is_string($parsedContent)) {
                    // Example regex; adjust based on actual parsed content format
                    preg_match('/PatientId\s*[:\-]\s*(\d+)/i', $parsedContent, $matches);
                    if (!empty($matches[1])) {
                        $patientId = $matches[1];
                    }
                }

                if (is_array($parsedContent)) {
                    foreach ($parsedContent as $record) {
                        if (isset($record['PatientId'])) {
                            $patientId = $record['PatientId'];
                            break; // Use the first PatientId found
                        }
                    }
                }
            
                error_log("Extracted PatientId: " . ($patientId ?? 'None'));
            
                if ($patientId !== null) {
                    error_log("Extracted HI: " . ($patientId ?? 'None'));
                    $followUpModel = new FollowUpModel();
                    $followUpData = $followUpModel->getFollowUpDataByPatientId($patientId);
                    error_log("Parsed Content: " . print_r($_SESSION['parsedContent'], true));

                    error_log("Extracted Follow up data: " . print_r($followUpData, true));

                    if (is_array($followUpData) && count($followUpData) > 0) {
                        $_SESSION['followUpData'] = $followUpData[0]; // Store as associative array
                    } else {
                        $_SESSION['followUpDataError'] = "No follow-up data found.";
                    }
                } else {
                    $_SESSION['followUpDataError'] = "No PatientId found in parsed content.";
                }
            } catch (Exception $e) {
                $_SESSION['followUpDataError'] = "Error fetching follow-up data: " . $e->getMessage();
            }
            // Redirect back to the view
            header("Location: ../View/compare_patient_data_view.php");
            exit;

        case 'clear':
            // Clear session data
            unset($_SESSION['parsedContent']);
            unset($_SESSION['uploadSuccess']);
            unset($_SESSION['uploadError']);
            unset($_SESSION['followUpData']);
            unset($_SESSION['followUpDataError']);
            header("Location: ../View/compare_patient_data_view.php");
            exit;
    }
}
?>
