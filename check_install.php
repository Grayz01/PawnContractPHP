<?php
echo "<h2>Kiểm tra cài đặt hệ thống</h2>";

// Kiểm tra PHP version
echo "<h3>PHP Version: " . phpversion() . "</h3>";

// Kiểm tra thư viện PHPWord
echo "<h3>Kiểm tra PHPWord:</h3>";
if (class_exists('PhpOffice\PhpWord\TemplateProcessor')) {
    echo "✓ PHPWord đã được cài đặt<br>";
} else {
    echo "✗ PHPWord chưa được cài đặt<br>";
    echo "Chạy lệnh: composer require phpoffice/phpword<br>";
}

// Kiểm tra thư mục và quyền
echo "<h3>Kiểm tra thư mục:</h3>";
$folders = ['templates', 'output', 'vendor'];
foreach ($folders as $folder) {
    if (file_exists($folder)) {
        echo "✓ Thư mục '$folder' tồn tại<br>";
        if (is_writable($folder)) {
            echo "  - Có quyền ghi<br>";
        } else {
            echo "  - Không có quyền ghi<br>";
        }
    } else {
        echo "✗ Thư mục '$folder' không tồn tại<br>";
    }
}

// Kiểm tra template file
echo "<h3>Kiểm tra file template:</h3>";
$templateFile = 'templates/hop_dong_template.docx';
if (file_exists($templateFile)) {
    echo "✓ File template tồn tại<br>";
    echo "  - Kích thước: " . filesize($templateFile) . " bytes<br>";
} else {
    echo "✗ File template không tồn tại<br>";
    echo "  - Tạo file: templates/hop_dong_template.docx<br>";
}

// Kiểm tra LibreOffice
echo "<h3>Kiểm tra LibreOffice:</h3>";
if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
    $libreoffice = shell_exec('which soffice 2>/dev/null');
} else {
    $libreoffice = shell_exec('where soffice 2>nul');
}

if (!empty($libreoffice)) {
    echo "✓ LibreOffice đã được cài đặt: " . trim($libreoffice) . "<br>";
} else {
    echo "✗ LibreOffice chưa được cài đặt (chỉ ảnh hưởng đến chuyển đổi PDF)<br>";
}

// Kiểm tra JSON extension
echo "<h3>Kiểm tra JSON extension:</h3>";
if (extension_loaded('json')) {
    echo "✓ JSON extension đã được cài đặt<br>";
} else {
    echo "✗ JSON extension chưa được cài đặt<br>";
}
