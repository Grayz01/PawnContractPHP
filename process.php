<?php
// Tắt hiển thị lỗi ra HTML
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Set header JSON ngay từ đầu
header('Content-Type: application/json; charset=utf-8');

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
    $filename = 'HopDong_' . preg_replace('/[^a-zA-Z0-9]/', '', $ho_ten) . '_' . $timestamp;
    
    // Nếu preview = 0 (không xem trước), tạo PDF để in
    if ($preview == '0') {
        // Tạo file DOCX tạm
        $tempDocxPath = $dataDir . '/' . $filename . '_temp.docx';
        $templateProcessor->saveAs($tempDocxPath);
        
        // Chuyển đổi sang PDF
        $pdfFilename = $filename . '.pdf';
        $pdfPath = $dataDir . '/' . $pdfFilename;
        
        // Sử dụng PhpWord để convert sang PDF (cần cài đặt thêm: composer require dompdf/dompdf)
        try {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($tempDocxPath);
            $pdfWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
            $pdfWriter->save($pdfPath);
            
            // Xóa file DOCX tạm
            if (file_exists($tempDocxPath)) {
                unlink($tempDocxPath);
            }
            
            // Trả về thông tin file PDF
            echo json_encode([
                'success' => true,
                'message' => 'Tạo hợp đồng thành công',
                'filename' => $pdfFilename,
                'file_url' => 'data/' . $pdfFilename,
                'timestamp' => $timestamp,
                'type' => 'pdf',
                'action' => 'print'
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            // Nếu không thể convert sang PDF, vẫn trả về DOCX
            if (file_exists($tempDocxPath)) {
                rename($tempDocxPath, $dataDir . '/' . $filename . '.docx');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Không thể tạo PDF, đã tạo file DOCX',
                'filename' => $filename . '.docx',
                'file_url' => 'data/' . $filename . '.docx',
                'timestamp' => $timestamp,
                'type' => 'docx',
                'action' => 'download',
                'warning' => 'Không hỗ trợ chuyển đổi PDF. Cài đặt: composer require dompdf/dompdf'
            ], JSON_UNESCAPED_UNICODE);
        }
        
    } else {
        // Preview = 1, tạo file DOCX để tải về
        $docxFilename = $filename . '.docx';
        $outputPath = $dataDir . '/' . $docxFilename;
        $templateProcessor->saveAs($outputPath);
        
        if (!file_exists($outputPath)) {
            throw new Exception('Không thể tạo file hợp đồng. Vui lòng kiểm tra quyền ghi trên server');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Tạo hợp đồng thành công',
            'filename' => $docxFilename,
            'file_url' => 'data/' . $docxFilename,
            'timestamp' => $timestamp,
            'type' => 'docx',
            'action' => 'download'
        ], JSON_UNESCAPED_UNICODE);
    }
    
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