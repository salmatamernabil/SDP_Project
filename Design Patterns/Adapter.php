<?php
require_once '../vendor/autoload.php';
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Text;
use setasign\Fpdi\Fpdi;
use Smalot\PdfParser\Parser;

// IExtractData Interface
interface IExtractData {
    public function extractData($filePath);
}

// IParsing Interface
interface IParsing {
    public function parse($filePath);
}

// PDF Parser
class PDFParser implements IParsing {
    public function parse($filePath) {
        error_log("[PARSER] PDFParser parsing file: $filePath");
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($filePath);
        return $pdf->getText();
    }
}

// Excel Parser
class ExcelParser implements IParsing {
    public function parse($filePath) {
        error_log("[PARSER] ExcelParser parsing file: $filePath");
        $data = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                $data[] = $row;
            }
            fclose($handle);
        }
        return $data;
    }
}

// Word Parser
class WordParser implements IParsing {
    public function parse($filePath) {
        error_log("[PARSER] WordParser parsing file: $filePath");
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
        $content = '';
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
                    $content .= $element->getText() . "\n";
                }
            }
        }
        return $content;
    }
}

// ExtractNormally Class
class ExtractNormally implements IExtractData {
    public function extractData($filePath) {
        // Direct file reading logic, e.g., plain text files
        return file_get_contents($filePath);
    }
}

// ParsingAdapter Class
class ParsingAdapter implements IExtractData {
    private $parser;

    public function __construct(IParsing $parser) {
        $this->parser = $parser;
        error_log("[ADAPTER] ParsingAdapter initialized with parser: " . get_class($parser));
    }

    public function extractData($filePath) {
        error_log("[ADAPTER] extractData called for file: $filePath using parser: " . get_class($this->parser));
        return $this->parser->parse($filePath);
    }
}


?>
