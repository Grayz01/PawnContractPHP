<?php
// Tắt hiển thị lỗi ra HTML
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Set header JSON ngay từ đầu
header('Content-Type: application/json; charset=utf-8');

// Tăng thời gian thực thi và bộ nhớ
set_time_limit(90); // Tăng thêm 30 giây
ini_set('memory_limit', '512M'); // Tăng bộ nhớ

// Bắt mọi lỗi PHP
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

try {
    // Kiểm tra file autoload
    if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
        throw new Exception('Chưa cài đặt PHPWord. Vui lòng chạy: composer require phpoffice/phpword');
    }
    
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Kiểm tra phương thức POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Phương thức không hợp lệ. Chỉ chấp nhận POST request.');
    }
    
    // Lấy dữ liệu từ form
    $ho_ten = isset($_POST['ho_ten']) ? trim($_POST['ho_ten']) : '';
    $nam_sinh = isset($_POST['nam_sinh']) ? trim($_POST['nam_sinh']) : '';
    $so_dt = isset($_POST['so_dt']) ? trim($_POST['so_dt']) : '';
    $cccd_so = isset($_POST['cccd_so']) ? trim($_POST['cccd_so']) : '';
    $ngay_cap = isset($_POST['ngay_cap']) ? trim($_POST['ngay_cap']) : '';
    $noi_cap = isset($_POST['noi_cap']) ? trim($_POST['noi_cap']) : '';
    $noi_dktt = isset($_POST['noi_dktt']) ? trim($_POST['noi_dktt']) : '';
    $ten_tai_san = isset($_POST['ten_tai_san']) ? trim($_POST['ten_tai_san']) : '';
    $so_tien = isset($_POST['so_tien']) ? trim($_POST['so_tien']) : '';
    $bang_chu = isset($_POST['bang_chu']) ? trim($_POST['bang_chu']) : '';
    $gio = isset($_POST['gio']) ? trim($_POST['gio']) : '';
    $phut = isset($_POST['phut']) ? trim($_POST['phut']) : '';
    $ngay_cam = isset($_POST['ngay_cam']) ? trim($_POST['ngay_cam']) : '';
    $ky_1 = isset($_POST['ky_1']) ? trim($_POST['ky_1']) : '';
    $ky_2 = isset($_POST['ky_2']) ? trim($_POST['ky_2']) : '';
    $ky_3 = isset($_POST['ky_3']) ? trim($_POST['ky_3']) : '';
    $ky_4 = isset($_POST['ky_4']) ? trim($_POST['ky_4']) : '';
    $preview = isset($_POST['preview']) ? $_POST['preview'] : '0';
    
    // Kiểm tra các trường bắt buộc
    if (empty($ho_ten)) {
        throw new Exception('Vui lòng nhập họ tên');
    }
    if (empty($cccd_so)) {
        throw new Exception('Vui lòng nhập số CCCD');
    }
    if (empty($so_tien)) {
        throw new Exception('Vui lòng nhập số tiền');
    }
    
    // Đường dẫn template
    $templatePath = __DIR__ . '/templates/contract_template.docx';
    
    // Kiểm tra template có tồn tại không
    if (!file_exists($templatePath)) {
        throw new Exception('Không tìm thấy file template. Vui lòng đảm bảo file contract_template.docx có trong thư mục templates/');
    }
    
    // Kiểm tra và tạo thư mục data nếu chưa có
    $dataDir = __DIR__ . '/data';
    if (!is_dir($dataDir)) {
        if (!mkdir($dataDir, 0755, true)) {
            throw new Exception('Không thể tạo thư mục data. Vui lòng kiểm tra quyền ghi trên server');
        }
    }
    
    // Kiểm tra quyền ghi
    if (!is_writable($dataDir)) {
        throw new Exception('Thư mục data không có quyền ghi. Vui lòng chạy: chmod 755 data');
    }
    
    // Format ngày tháng
    function formatDate($date) {
        if (empty($date)) return '';
        $timestamp = strtotime($date);
        if ($timestamp === false) return $date;
        return date('d/m/Y', $timestamp);
    }
    
    function formatDateFull($date) {
        if (empty($date)) return '';
        $timestamp = strtotime($date);
        if ($timestamp === false) return $date;
        return 'Ngày ' . date('d', $timestamp) . ' tháng ' . date('m', $timestamp) . ' năm ' . date('Y', $timestamp);
    }
    
    // Tạo TemplateProcessor
    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
    
    // Điền dữ liệu vào template
    $templateProcessor->setValue('ho_ten', $ho_ten);
    $templateProcessor->setValue('nam_sinh', $nam_sinh);
    $templateProcessor->setValue('so_dt', $so_dt);
    $templateProcessor->setValue('cccd_so', $cccd_so);
    $templateProcessor->setValue('ngay_cap', formatDate($ngay_cap));
    $templateProcessor->setValue('noi_cap', $noi_cap);
    $templateProcessor->setValue('noi_dktt', $noi_dktt);
    $templateProcessor->setValue('ten_tai_san', $ten_tai_san);
    $templateProcessor->setValue('so_tien', number_format($so_tien, 0, ',', '.'));
    $templateProcessor->setValue('bang_chu', $bang_chu);
    $templateProcessor->setValue('gio', str_pad($gio, 2, '0', STR_PAD_LEFT));
    $templateProcessor->setValue('phut', str_pad($phut, 2, '0', STR_PAD_LEFT));
    $templateProcessor->setValue('ngay_cam', formatDate($ngay_cam));
    $templateProcessor->setValue('ngay_cam_full', formatDateFull($ngay_cam));
    
    // Điền các kỳ gia hạn
    $templateProcessor->setValue('ky_1', !empty($ky_1) ? formatDate($ky_1) : '');
    $templateProcessor->setValue('ky_2', !empty($ky_2) ? formatDate($ky_2) : '');
    $templateProcessor->setValue('ky_3', !empty($ky_3) ? formatDate($ky_3) : '');
    $templateProcessor->setValue('ky_4', !empty($ky_4) ? formatDate($ky_4) : '');
    
    // Tạo tên file mới
    $timestamp = date('YmdHis');
    $cleanName = preg_replace('/[^a-zA-Z0-9_\x{00C0}-\x{1EF9}\s]/u', '', $ho_ten);
    $cleanName = str_replace(' ', '_', $cleanName);
    $filename = 'HopDong_' . $cleanName . '_' . $timestamp;
    
    // Tạo file Word
    $docxFilename = $filename . '.docx';
    $docxPath = $dataDir . '/' . $docxFilename;
    $templateProcessor->saveAs($docxPath);
    
    // Kiểm tra file Word đã tạo
    if (!file_exists($docxPath)) {
        throw new Exception('Không thể tạo file hợp đồng Word. Vui lòng kiểm tra quyền ghi trên server');
    }
    
    // Khởi tạo biến PDF
    $pdfFilename = $filename . '.pdf';
    $pdfPath = $dataDir . '/' . $pdfFilename;
    $pdfCreated = false;
    
    // Hàm chuyển đổi DOCX sang PDF bằng LibreOffice
    function convertDocxToPDF($docxPath, $pdfPath) {
        // Kiểm tra xem LibreOffice có sẵn không
        $libreofficePath = '';
        
        // Thử tìm LibreOffice ở các vị trí thông thường
        $possiblePaths = [
            'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
            'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe',
            'soffice.exe',
            'soffice',
            'libreoffice',
            '/usr/bin/libreoffice',
            '/usr/local/bin/libreoffice',
            '/snap/bin/libreoffice'
        ];
        
        $isWindows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
        
        foreach ($possiblePaths as $path) {
            // Kiểm tra nếu file tồn tại
            if (file_exists($path)) {
                // Thử lệnh thông thường
                $command = '"' . $path . '" --version 2>&1';
                exec($command, $output, $returnCode);
                
                if ($returnCode === 0) {
                    $libreofficePath = $path;
                    break;
                }
                
                // Trên Windows, thử với & (cho PowerShell)
                if ($isWindows) {
                    $commandWithAmpersand = '&"' . $path . '" --version 2>&1';
                    exec($commandWithAmpersand, $output2, $returnCode2);
                    
                    if ($returnCode2 === 0) {
                        $libreofficePath = $path;
                        break;
                    }
                }
            }
        }
        
        if (empty($libreofficePath)) {
            throw new Exception('Không tìm thấy LibreOffice. Vui lòng cài đặt LibreOffice để chuyển đổi sang PDF.');
        }
        
        // Tạo output directory và input file paths
        $outputDir = dirname($pdfPath);
        $inputFile = $docxPath;
        
        // Thử nhiều cách để chạy LibreOffice
        $commands = [];
        
        // Cách 1: Thông thường (dùng cho cả Windows và Linux)
        $commands[] = sprintf(
            '"%s" --headless --convert-to pdf --outdir "%s" "%s" 2>&1',
            $libreofficePath,
            $outputDir,
            $inputFile
        );
        
        // Cách 2: Với & (cho PowerShell) - chỉ trên Windows
        if ($isWindows) {
            $commands[] = sprintf(
                '&"%s" --headless --convert-to pdf --outdir "%s" "%s" 2>&1',
                $libreofficePath,
                $outputDir,
                $inputFile
            );
        }
        
        // Cách 3: Sử dụng cd đến thư mục chứa LibreOffice (cho Windows)
        if ($isWindows) {
            $libreofficeDir = dirname($libreofficePath);
            $commands[] = sprintf(
                'cd "%s" && soffice.exe --headless --convert-to pdf --outdir "%s" "%s" 2>&1',
                $libreofficeDir,
                $outputDir,
                $inputFile
            );
        }
        
        $success = false;
        $lastError = '';
        
        foreach ($commands as $command) {
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0) {
                $success = true;
                break;
            } else {
                $lastError = 'Return code: ' . $returnCode . '. Output: ' . implode(' ', $output);
            }
        }
        
        if (!$success) {
            throw new Exception('LibreOffice chuyển đổi thất bại. ' . $lastError);
        }
        
        // Kiểm tra file PDF đã được tạo chưa
        $pdfDir = dirname($pdfPath);
        $pdfNameWithoutExt = pathinfo($pdfPath, PATHINFO_FILENAME);
        
        // LibreOffice có thể tạo file với tên hơi khác, tìm file PDF mới nhất
        $pdfFiles = glob($pdfDir . '/*.pdf');
        $latestPdf = '';
        $latestTime = 0;
        
        foreach ($pdfFiles as $pdfFile) {
            $fileTime = filemtime($pdfFile);
            if ($fileTime > $latestTime) {
                $latestTime = $fileTime;
                $latestPdf = $pdfFile;
            }
        }
        
        $expectedPdfName = basename($pdfPath);
        if (!empty($latestPdf) && (
            basename($latestPdf) === $expectedPdfName ||
            strpos(basename($latestPdf), $pdfNameWithoutExt) !== false
        )) {
            if (basename($latestPdf) !== $expectedPdfName) {
                rename($latestPdf, $pdfPath);
            }
            return $pdfPath;
        }
        
        throw new Exception('File PDF không được tạo sau khi chuyển đổi.');
    }
    
    // Thử chuyển đổi sang PDF
    try {
        $pdfPath = convertDocxToPDF($docxPath, $pdfPath);
        $pdfCreated = true;
    } catch (Exception $e) {
        // Ghi log lỗi nhưng không dừng chương trình
        error_log('LibreOffice conversion error: ' . $e->getMessage());
        $pdfCreated = false;
        
        // Tạo file PDF bằng cách khác (sử dụng PHPWord nếu có dompdf)
        try {
            if (class_exists('\\PhpOffice\\PhpWord\\IOFactory')) {
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($docxPath);
                if (class_exists('\\PhpOffice\\PhpWord\\Writer\\PDF')) {
                    $pdfWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
                    $pdfWriter->save($pdfPath);
                    $pdfCreated = true;
                }
            }
        } catch (Exception $pdfException) {
            // Không thể tạo PDF bằng bất kỳ phương pháp nào
            $pdfCreated = false;
        }
    }
    
    // Chuẩn bị response dựa trên lựa chọn preview
    if ($preview == '0') {
        // Chế độ in - ưu tiên PDF
        if ($pdfCreated && file_exists($pdfPath)) {
            $response = [
                'success' => true,
                'message' => 'Tạo hợp đồng thành công. File PDF đã sẵn sàng để in.',
                'filename' => $pdfFilename,
                'filename_pdf' => $pdfFilename,
                'filename_docx' => $docxFilename,
                'file_url' => 'data/' . $pdfFilename,
                'file_url_pdf' => 'data/' . $pdfFilename,
                'file_url_docx' => 'data/' . $docxFilename,
                'timestamp' => $timestamp,
                'type' => 'pdf',
                'action' => 'print',
                'pdf_created' => $pdfCreated
            ];
        } else {
            // Nếu không có PDF, trả về DOCX
            $response = [
                'success' => true,
                'message' => 'Tạo hợp đồng thành công. Không thể tạo PDF, sử dụng file Word để in.',
                'filename' => $docxFilename,
                'filename_pdf' => null,
                'filename_docx' => $docxFilename,
                'file_url' => 'data/' . $docxFilename,
                'file_url_pdf' => null,
                'file_url_docx' => 'data/' . $docxFilename,
                'timestamp' => $timestamp,
                'type' => 'docx',
                'action' => 'print',
                'pdf_created' => false
            ];
        }
    } else {
        // Chế độ xem trước - trả về cả 2 file
        $response = [
            'success' => true,
            'message' => 'Tạo hợp đồng thành công.',
            'filename' => $docxFilename,
            'filename_pdf' => $pdfCreated ? $pdfFilename : null,
            'filename_docx' => $docxFilename,
            'file_url' => 'data/' . $docxFilename,
            'file_url_pdf' => $pdfCreated ? 'data/' . $pdfFilename : null,
            'file_url_docx' => 'data/' . $docxFilename,
            'timestamp' => $timestamp,
            'type' => 'docx',
            'action' => 'download',
            'pdf_created' => $pdfCreated
        ];
    }
    
    // Trả về response JSON
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'line' => $e->getLine(),
        'file' => basename($e->getFile())
    ], JSON_UNESCAPED_UNICODE);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
        'line' => $e->getLine(),
        'file' => basename($e->getFile())
    ], JSON_UNESCAPED_UNICODE);
}