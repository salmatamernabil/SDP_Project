<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../Design Patterns/Template.php';
require '../vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

interface FollowUpReportStrategy {
    public function generateReport($patients);
}

class PDFReportStrategy extends ReportTemplate implements FollowUpReportStrategy {
    private $pdf;

    protected function initializeReport() {
        $this->pdf = new FPDF();
        $this->pdf->AddPage();
        $this->pdf->SetFont('Arial', 'B', 16);
    }

    
    protected function addTitle() {
        $this->pdf->Cell(0, 10, 'Patient Follow-Up Report', 0, 1, 'C');
        $this->pdf->Ln(10);
    }

    protected function addPatientData($patients) {
        foreach ($patients as $patient) {
            $this->pdf->SetFont('Arial', '', 12);
            foreach ($patient as $key => $value) {
                $this->pdf->Cell(0, 10, "$key: " . ($value ?? 'N/A'), 0, 1);
            }
            $this->pdf->Ln();
        }
    }
    

    protected function finalizeReport() {
        $this->pdf->Output('D', 'FollowUpReport.pdf');
    }

    public function generateReport($patients) {
        $this->generateTemplate($patients); // Call Template Method
    }
}

class WordReportStrategy extends ReportTemplate implements FollowUpReportStrategy {
    private $phpWord;
    private $section;

    protected function initializeReport() {
        $this->phpWord = new PhpWord();
        $this->section = $this->phpWord->addSection();
    }

    protected function addTitle() {
        $this->section->addTitle("Patient Follow-Up Report", 1);
    }

    protected function addPatientData($patients) {
        foreach ($patients as $patient) {
            foreach ($patient as $key => $value) {
                $this->section->addText("$key: " . ($value ?? 'N/A'));
            }
            $this->section->addTextBreak();
        }
    }
    

    protected function finalizeReport() {
        $writer = IOFactory::createWriter($this->phpWord, 'Word2007');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment;filename="FollowUpReport.docx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

    public function generateReport($patients) {
        $this->generateTemplate($patients); // Call Template Method
    }
}

class ExcelReportStrategy extends ReportTemplate implements FollowUpReportStrategy {
    private $spreadsheet;
    private $sheet;

    protected function initializeReport() {
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
        $this->sheet->setCellValue('A1', 'Patient ID');
        $this->sheet->setCellValue('B1', 'Surgery Date');
        $this->sheet->setCellValue('C1', 'Type of Surgery');
    }

    protected function addTitle() {
        // Title logic can be skipped if headers already added in initializeReport
    }

    protected function addPatientData($patients) {
        $headerSet = false;
        $row = 2;
    
        foreach ($patients as $patient) {
            $col = 'A';
    
            if (!$headerSet) {
                foreach (array_keys($patient) as $header) {
                    $this->sheet->setCellValue($col . '1', $header);
                    $col++;
                }
                $headerSet = true;
            }
    
            $col = 'A';
            foreach ($patient as $value) {
                $this->sheet->setCellValue($col . $row, $value ?? 'N/A');
                $col++;
            }
            $row++;
        }
    }
    

    protected function finalizeReport() {
        $writer = new Xlsx($this->spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="FollowUpReport.xlsx"');
        $writer->save('php://output');
    }

    public function generateReport($patients) {
        $this->generateTemplate($patients); // Call Template Method
    }
}

class ContextGenerator {
    private $strategy;

    public function __construct(FollowUpReportStrategy $strategy) {
        $this->strategy = $strategy;
    }

    public function setStrategy(FollowUpReportStrategy $strategy) {
        $this->strategy = $strategy;
    }

    public function generateReport($patients) {
        $this->strategy->generateReport($patients);
    }
}
?>
