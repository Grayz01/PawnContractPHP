<?php
require_once __DIR__ . '/vendor/autoload.php';

$templatePath = __DIR__ . '/templates/contract_template.docx';

echo "Checking template file...\n";
echo "Path: $templatePath\n";
echo "Exists: " . (file_exists($templatePath) ? "Yes" : "No") . "\n";
echo "Readable: " . (is_readable($templatePath) ? "Yes" : "No") . "\n";
echo "Size: " . filesize($templatePath) . " bytes\n";

// Kiểm tra signature ZIP
$handle = fopen($templatePath, 'r');
$firstBytes = fread($handle, 4);
fclose($handle);
echo "First 4 bytes: " . bin2hex($firstBytes) . " (should be 504b0304 for DOCX)\n";

// Thử mở bằng ZipArchive
$zip = new ZipArchive();
$result = $zip->open($templatePath);
echo "ZipArchive open result: " . $result . "\n";
if ($result === true) {
    echo "Zip file is valid!\n";
    $zip->close();
} else {
    echo "Error opening zip file: " . $zip->getStatusString() . "\n";
}
