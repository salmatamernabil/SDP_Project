
<?php
require_once '../Design Patterns/Adapter.php';
// FileParser Model

class FileParserModel {
    private $extractor;

    /**
     * Set the data extraction strategy.
     * 
     * @param IExtractData $extractor
     */
    public function setExtractor(IExtractData $extractor) {
        $this->extractor = $extractor;
        error_log("[FILE PARSER] Extractor set to: " . get_class($extractor));
    }

    /**
     * Parse a file using the selected extraction strategy.
     * 
     * @param string $filePath
     * @return mixed
     * @throws Exception
     */
    public function parseFile($filePath) {
        if (!$this->extractor) {
            throw new Exception("No extractor set for file type.");
        }
        error_log("[FILE PARSER] File parsed successfully: $filePath");
        return $this->extractor->extractData($filePath);
    }

    
}
?>
