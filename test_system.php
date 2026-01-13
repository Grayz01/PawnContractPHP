<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test PDF Đơn Giản - Windows</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .result { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .btn { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
        .btn:hover { background: #0056b3; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow: auto; }
        .test-step { margin: 15px 0; padding: 10px; border-left: 4px solid #3498db; background: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📄 Test Tạo PDF Đơn Giản - Windows</h1>
        
        <?php
        // Tạo file test PDF trực tiếp
        echo "<h2>Bước 1: Kiểm tra PHP và môi trường</h2>";
        echo "<pre>";
        echo "PHP Version: " . phpversion() . "\n";
        echo "OS: " . PHP_OS . "\n";
        echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
        echo "Memory Limit: " . ini_get('memory_limit') . "\n";
        echo "Max Execution Time: " . ini_get('max_execution_time') . "s\n";
        echo "Current Directory: " . __DIR__ . "\n";
        echo "</pre>";
        
        echo "<h2>Bước 2: Kiểm tra LibreOffice (Windows)</h2>";
        $libreofficePath = null;
        
        // Danh sách đường dẫn trên Windows
        $windowsPaths = [
            'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
            'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe',
            'soffice.exe',
            'soffice',
            'libreoffice'
        ];
        
        // Danh sách đường dẫn trên Linux
        $linuxPaths = [
            'libreoffice',
            '/usr/bin/libreoffice',
            '/usr/local/bin/libreoffice',
            '/snap/bin/libreoffice',
            'soffice'
        ];
        
        // Chọn danh sách đường dẫn phù hợp với hệ điều hành
        $paths = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? $windowsPaths : $linuxPaths;
        
        echo "<div class='test-step'>";
        echo "<strong>Đang kiểm tra các đường dẫn:</strong><br>";
        foreach ($paths as $path) {
            // Kiểm tra file có tồn tại trước khi chạy lệnh
            if (!file_exists($path)) {
                echo "Checking: $path -> <span class='error'>File không tồn tại</span><br>";
                continue; 
            }
            
            echo "Checking: $path -> <span class='success'>File tồn tại</span><br>";
            
            // Thử chạy lệnh
            // Trên Windows, cần thêm & trước đường dẫn nếu dùng PowerShell
            $command = '"' . $path . '" --version 2>&1';
            
            echo "Thử lệnh: " . htmlspecialchars($command) . "<br>";
            
            exec($command, $output, $returnCode);
            
            echo "Kết quả: Return code: $returnCode<br>";
            
            if ($returnCode === 0) {
                $libreofficePath = $path;
                echo "<div class='result success'>✓ LibreOffice tìm thấy tại: $path</div>";
                echo "<pre>" . implode("\n", $output) . "</pre>";
                break;
            } else {
                // Thử với & trước đường dẫn (cho PowerShell)
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $commandWithAmpersand = '&"' . $path . '" --version 2>&1';
                    echo "Thử lệnh với &: " . htmlspecialchars($commandWithAmpersand) . "<br>";
                    exec($commandWithAmpersand, $output2, $returnCode2);
                    
                    if ($returnCode2 === 0) {
                        $libreofficePath = $path;
                        echo "<div class='result success'>✓ LibreOffice tìm thấy tại: $path (với &)</div>";
                        echo "<pre>" . implode("\n", $output2) . "</pre>";
                        break;
                    }
                }
                echo "<div class='result error'>✗ Lỗi thực thi tại: $path</div>";
            }
            echo "<br>";
        }
        echo "</div>";
            
        if (!$libreofficePath) {
            echo "<div class='result error'>✗ Không tìm thấy LibreOffice ở bất kỳ đường dẫn nào</div>";
            
            echo "<h3>Hướng dẫn cài đặt LibreOffice trên Windows:</h3>";
            echo "<div class='test-step'>";
            echo "1. Tải LibreOffice từ: <a href='https://www.libreoffice.org/' target='_blank'>https://www.libreoffice.org/</a><br>";
            echo "2. Cài đặt với tất cả options mặc định<br>";
            echo "3. Thêm vào PATH hoặc sử dụng đường dẫn đầy đủ<br>";
            echo "<br>";
            echo "<strong>Đường dẫn thường thấy:</strong><br>";
            echo "- C:\\Program Files\\LibreOffice\\program\\soffice.exe<br>";
            echo "- C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe<br>";
            echo "</div>";
            
            // Test thủ công
            echo "<h3>Test thủ công:</h3>";
            echo "<div class='test-step'>";
            echo "<pre>";
            // Thử mở LibreOffice trực tiếp
            $testPath = 'C:\\Program Files\\LibreOffice\\program\\soffice.exe';
            if (file_exists($testPath)) {
                echo "File tồn tại: $testPath\n";
                echo "Kích thước: " . filesize($testPath) . " bytes\n";
                
                // Thử chạy với &
                $testCommand = '&"' . $testPath . '" --version 2>&1';
                exec($testCommand, $testOutput, $testReturn);
                echo "Return code: $testReturn\n";
                if (!empty($testOutput)) {
                    echo "Output: " . implode("\n", $testOutput) . "\n";
                }
            } else {
                echo "File không tồn tại: $testPath\n";
            }
            echo "</pre>";
            echo "</div>";
        }
        
        echo "<h2>Bước 3: Tạo file Word test</h2>";
        
        // Tạo thư mục data nếu chưa có
        if (!is_dir(__DIR__ . '/data')) {
            mkdir(__DIR__ . '/data', 0755, true);
            echo "<div class='result info'>✓ Đã tạo thư mục data</div>";
        }
        
        // Kiểm tra quyền ghi
        $dataDir = __DIR__ . '/data';
        if (!is_writable($dataDir)) {
            echo "<div class='result error'>✗ Thư mục data không có quyền ghi</div>";
            echo "<p>Vui lòng kiểm tra quyền thư mục trên Windows</p>";
        } else {
            echo "<div class='result success'>✓ Thư mục data có quyền ghi</div>";
        }
        
        // Tạo file Word test
        $docxPath = $dataDir . '/test_simple.docx';
        $pdfPath = $dataDir . '/test_simple.pdf';
        
        try {
            // Kiểm tra PHPWord
            if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
                throw new Exception("vendor/autoload.php không tồn tại. Chạy: composer require phpoffice/phpword");
            }
            
            require_once __DIR__ . '/vendor/autoload.php';
            
            if (!class_exists('\\PhpOffice\\PhpWord\\PhpWord')) {
                throw new Exception("PHPWord chưa được cài đặt");
            }
            
            echo "<div class='test-step'>";
            echo "<strong>Kiểm tra PHPWord:</strong><br>";
            echo "✓ PHPWord đã được tải<br>";
            echo "</div>";
            
            // Tạo file Word đơn giản
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $section = $phpWord->addSection();
            
            $section->addText(
                'TEST TẠO FILE PDF TRÊN WINDOWS',
                ['bold' => true, 'size' => 16],
                ['alignment' => 'center']
            );
            
            $section->addTextBreak(2);
            $section->addText('Ngày test: ' . date('d/m/Y H:i:s'));
            $section->addText('Hệ điều hành: ' . PHP_OS);
            $section->addText('Đường dẫn LibreOffice: ' . ($libreofficePath ? $libreofficePath : 'Không tìm thấy'));
            $section->addTextBreak(1);
            $section->addText('Nếu bạn thấy file PDF này, hệ thống đã hoạt động tốt!', ['bold' => true, 'color' => 'FF0000']);
            
            // Xóa file cũ nếu có
            if (file_exists($docxPath)) unlink($docxPath);
            if (file_exists($pdfPath)) unlink($pdfPath);
            
            // Lưu file Word
            $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($docxPath);
            
            if (file_exists($docxPath)) {
                $docxSize = filesize($docxPath);
                echo "<div class='result success'>✓ Đã tạo file Word: test_simple.docx (" . round($docxSize/1024, 2) . " KB)</div>";
            } else {
                echo "<div class='result error'>✗ Không thể tạo file Word</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='result error'>✗ Lỗi tạo file Word: " . $e->getMessage() . "</div>";
        }
        
        echo "<h2>Bước 4: Chuyển đổi Word sang PDF</h2>";
        
        if ($libreofficePath && file_exists($docxPath)) {
            // Tạo thư mục output nếu chưa có
            if (!is_dir($dataDir)) {
                mkdir($dataDir, 0755, true);
            }
            
            // Thử nhiều cách để chạy LibreOffice
            $commands = [];
            
            // Cách 1: Thông thường
            $commands[] = sprintf(
                '"%s" --headless --convert-to pdf --outdir %s %s 2>&1',
                $libreofficePath,
                $dataDir,
                $docxPath
            );
            
            // Cách 2: Với & (cho PowerShell)
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $commands[] = sprintf(
                    '&"%s" --headless --convert-to pdf --outdir %s %s 2>&1',
                    $libreofficePath,
                    $dataDir,
                    $docxPath
                );
            }
            
            // Cách 3: Sử dụng cd đến thư mục chứa LibreOffice
            $libreofficeDir = dirname($libreofficePath);
            $commands[] = sprintf(
                'cd "%s" && soffice.exe --headless --convert-to pdf --outdir %s %s 2>&1',
                $libreofficeDir,
                $dataDir,
                $docxPath
            );
            
            $success = false;
            
            foreach ($commands as $index => $command) {
                echo "<div class='test-step'>";
                echo "<strong>Thử cách " . ($index + 1) . ":</strong><br>";
                echo "<pre>" . htmlspecialchars($command) . "</pre>";
                
                echo "<strong>Đang chạy LibreOffice...</strong><br>";
                
                $startTime = microtime(true);
                exec($command, $output, $returnCode);
                $endTime = microtime(true);
                $executionTime = round($endTime - $startTime, 2);
                
                echo "<strong>Thời gian thực thi:</strong> {$executionTime}s<br>";
                echo "<strong>Mã trả về:</strong> $returnCode<br>";
                
                echo "<h3>Kết quả chuyển đổi:</h3>";
                if (!empty($output)) {
                    echo "<pre>" . implode("\n", $output) . "</pre>";
                } else {
                    echo "<pre>(Không có output)</pre>";
                }
                
                if ($returnCode === 0) {
                    echo "<div class='result success'>✓ Chuyển đổi thành công với cách " . ($index + 1) . "</div>";
                    $success = true;
                    break;
                } else {
                    echo "<div class='result error'>✗ Lỗi với cách " . ($index + 1) . "</div>";
                }
                echo "</div>";
            }
            
            if ($success) {
                // Tìm file PDF đã tạo
                echo "<div class='test-step'>";
                echo "<strong>Tìm file PDF đã tạo...</strong><br>";
                
                $pdfFiles = glob($dataDir . '/*.pdf');
                $found = false;
                
                echo "Tìm thấy " . count($pdfFiles) . " file PDF trong thư mục data<br>";
                
                foreach ($pdfFiles as $pdfFile) {
                    echo "Kiểm tra file: " . basename($pdfFile) . "<br>";
                    
                    // Trên Windows, LibreOffice có thể tạo file với tên khác
                    // Tìm file PDF mới nhất
                    if (strpos(basename($pdfFile), 'test_simple') !== false || 
                        filemtime($pdfFile) > (time() - 60)) { // File tạo trong 60 giây qua
                        
                        // Đổi tên file cho đúng
                        if (basename($pdfFile) !== 'test_simple.pdf') {
                            $newName = $dataDir . '/test_simple.pdf';
                            if (file_exists($newName)) {
                                unlink($newName);
                            }
                            rename($pdfFile, $newName);
                            $pdfFile = $newName;
                        }
                        
                        $found = true;
                        break;
                    }
                }
                
                if ($found && file_exists($pdfPath)) {
                    $pdfSize = filesize($pdfPath);
                    echo "<div class='result success'>✓ Đã tạo file PDF: test_simple.pdf (" . round($pdfSize/1024, 2) . " KB)</div>";
                    
                    // Hiển thị link
                    echo "<p><a href='data/test_simple.pdf' class='btn' target='_blank'>📄 Mở file PDF</a>";
                    echo "<a href='data/test_simple.pdf' class='btn' download>Tải file PDF</a></p>";
                    
                    // Kiểm tra nội dung PDF
                    echo "<h3>Kiểm tra file PDF:</h3>";
                    $pdfContent = file_get_contents($pdfPath, false, null, 0, 10);
                    echo "<pre>4 byte đầu tiên: " . bin2hex($pdfContent) . "</pre>";
                    
                    if (strpos($pdfContent, '%PDF') === 0) {
                        echo "<div class='result success'>✓ File PDF hợp lệ (có header PDF)</div>";
                    } else {
                        echo "<div class='result error'>✗ File PDF không hợp lệ (không có header PDF)</div>";
                        echo "<p>Dữ liệu đầu file: " . htmlspecialchars($pdfContent) . "</p>";
                    }
                } else {
                    echo "<div class='result error'>✗ Không tìm thấy file PDF sau khi chuyển đổi</div>";
                    
                    // Liệt kê các file trong thư mục data
                    echo "<h3>Các file trong thư mục data:</h3>";
                    $files = scandir($dataDir);
                    echo "<ul>";
                    foreach ($files as $file) {
                        if ($file !== '.' && $file !== '..') {
                            $filePath = $dataDir . '/' . $file;
                            $size = filesize($filePath);
                            $ext = pathinfo($file, PATHINFO_EXTENSION);
                            $modified = date('Y-m-d H:i:s', filemtime($filePath));
                            echo "<li>" . htmlspecialchars($file) . " (" . round($size/1024, 2) . " KB, $modified)";
                            if ($ext === 'pdf') {
                                echo " - <a href='data/$file' target='_blank'>Mở PDF</a>";
                            }
                            echo "</li>";
                        }
                    }
                    echo "</ul>";
                }
                echo "</div>";
            } else {
                echo "<div class='result error'>✗ Tất cả các cách đều thất bại</div>";
            }
        } elseif (!$libreofficePath) {
            echo "<div class='result error'>✗ Không thể chuyển đổi vì không có LibreOffice</div>";
        } else {
            echo "<div class='result error'>✗ Không có file Word để chuyển đổi</div>";
        }
        
        echo "<h2>Bước 5: Test thay thế bằng TCPDF/DOMPDF</h2>";
        echo "<div class='test-step'>";
        echo "<p>Nếu LibreOffice không hoạt động, có thể thử sử dụng thư viện PHP thuần:</p>";
        echo "<pre>";
        echo "// Cài đặt TCPDF hoặc DOMPDF\n";
        echo "composer require tecnickcom/tcpdf\n";
        echo "// hoặc\n";
        echo "composer require dompdf/dompdf\n";
        echo "</pre>";
        echo "<button onclick='showPhpPdfExample()' class='btn'>Xem code mẫu</button>";
        echo "<div id='phpPdfExample' style='display:none; margin-top:10px;'>";
        echo "<pre>";
        echo "&lt;?php\n";
        echo "// Sử dụng TCPDF\n";
        echo "require_once('vendor/autoload.php');\n";
        echo "\$pdf = new TCPDF();\n";
        echo "\$pdf->AddPage();\n";
        echo "\$pdf->SetFont('dejavusans', '', 14);\n";
        echo "\$pdf->Write(0, 'Nội dung hợp đồng');\n";
        echo "\$pdf->Output('contract.pdf', 'D');\n";
        echo "?&gt;";
        echo "</pre>";
        echo "</div>";
        echo "</div>";
        
        echo "<h2>Bước 6: Kiểm tra trực tiếp từ command line</h2>";
        echo "<div class='test-step'>";
        echo "<p>Mở Command Prompt và thử các lệnh sau:</p>";
        echo "<pre>";
        echo "cd \"" . __DIR__ . "\"\n";
        echo "&\"C:\\Program Files\\LibreOffice\\program\\soffice.exe\" --version\n";
        echo "&\"C:\\Program Files\\LibreOffice\\program\\soffice.exe\" --headless --convert-to pdf --outdir data data\\test_simple.docx\n";
        echo "</pre>";
        echo "<p>Nếu lệnh trên hoạt động, hệ thống có thể tạo PDF.</p>";
        echo "</div>";
        ?>
        
        <script>
        function showPhpPdfExample() {
            document.getElementById('phpPdfExample').style.display = 'block';
        }
        </script>
        
        <div style="margin-top: 30px; padding: 20px; background: #e9ecef; border-radius: 5px;">
            <h2>🎯 Tóm tắt kết quả</h2>
            <p>Sau khi chạy test, bạn sẽ biết:</p>
            <ol>
                <li>LibreOffice có được tìm thấy không</li>
                <li>Có thể tạo file Word không</li>
                <li>Có thể chuyển đổi Word sang PDF không</li>
                <li>File PDF có hợp lệ không</li>
            </ol>
            
            <h3>📋 Hành động tiếp theo:</h3>
            <ul>
                <li>Nếu LibreOffice không tìm thấy: Kiểm tra đường dẫn cài đặt</li>
                <li>Nếu không tạo được PDF: Thử chạy lệnh từ Command Prompt</li>
                <li>Nếu file PDF không hợp lệ: Kiểm tra quyền thư mục</li>
                <li>Vẫn không được: Xem xét dùng TCPDF/DOMPDF thay thế</li>
            </ul>
        </div>
    </div>
    
    <script>
    // Tự động chạy test sau 2 giây
    setTimeout(() => {
        console.log('Test PDF trên Windows đã sẵn sàng');
        
        // Kiểm tra xem có LibreOffice không
        const hasLibreOffice = <?php echo $libreofficePath ? 'true' : 'false'; ?>;
        if (!hasLibreOffice) {
            alert('⚠ Không tìm thấy LibreOffice! Vui lòng cài đặt LibreOffice và chạy lại test.');
        }
    }, 2000);
    </script>
</body>
</html>