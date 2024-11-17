<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

interface FollowUpReportStrategy {
    public function generateReport($patients);
}

class PDFReportStrategy implements FollowUpReportStrategy {
    public function generateReport($patients) {
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename='FollowUpReport.pdf'");
        header("Cache-Control: no-cache");
        header("Pragma: no-cache");

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Patient Follow-Up Report', 0, 1, 'C');
        $pdf->Ln(10);

        foreach ($patients as $patient) {
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(40, 10, 'Patient ID: ' . $patient['PatientId']);
            $pdf->Ln();
            $pdf->Cell(40, 10, 'Surgery Date: ' . $patient['SurgeryDate']);
            $pdf->Ln();
            $pdf->Cell(40, 10, 'Type of Surgery: ' . $patient['TypeOfSurgery']);
            $pdf->Ln();
            $pdf->Cell(40, 10, 'Hospital Name: ' . $patient['HospitalName']);
            $pdf->Ln();
            $pdf->Cell(40, 10, 'Member ID (MID): ' . $patient['MID']);
            $pdf->Ln(10);
        }

        $pdf->Output('D', 'FollowUpReport.pdf');
        exit;
    }
}

class WordReportStrategy implements FollowUpReportStrategy {
    public function generateReport($patients) {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        
        $section->addTitle("Patient Follow-Up Report", 1);
        
        foreach ($patients as $patient) {
            $section->addText("Patient ID: " . $patient['PatientId']);
            $section->addText("Surgery Date: " . $patient['SurgeryDate']);
            $section->addText("Type of Surgery: " . $patient['TypeOfSurgery']);
            $section->addText("Hospital Name: " . $patient['HospitalName']);
            $section->addText("Member ID (MID): " . $patient['MID']);
            $section->addTextBreak();
        }

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $filename = 'FollowUpReport.docx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
    }
}

class ExcelReportStrategy implements FollowUpReportStrategy {
    public function generateReport($patients) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Patient ID');
        $sheet->setCellValue('B1', 'Surgery Date');
        $sheet->setCellValue('C1', 'Type of Surgery');
        $sheet->setCellValue('D1', 'Hospital Name');
        $sheet->setCellValue('E1', 'Member ID (MID)');

        $row = 2;
        foreach ($patients as $patient) {
            $sheet->setCellValue("A$row", $patient['PatientId']);
            $sheet->setCellValue("B$row", $patient['SurgeryDate']);
            $sheet->setCellValue("C$row", $patient['TypeOfSurgery']);
            $sheet->setCellValue("D$row", $patient['HospitalName']);
            $sheet->setCellValue("E$row", $patient['MID']);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'FollowUpReport.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
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
