document.addEventListener('DOMContentLoaded', function() {
    // Hàm hiển thị loading
    function showLoading() {
        const overlay = document.getElementById('loadingOverlay');
        const progressBar = document.getElementById('loadingProgress');
        const printBtn = document.getElementById('printBtn');
        
        // Hiển thị overlay
        overlay.style.display = 'flex';
        
        // Đặt trạng thái loading cho nút
        printBtn.disabled = true;
        printBtn.classList.add('btn-loading');
        
        // Tăng progress bar
        let progress = 0;
        const progressInterval = setInterval(() => {
            if (progress < 90) {
                progress += 10;
                progressBar.style.width = progress + '%';
            }
        }, 500);
        
        // Lưu interval để xóa sau
        overlay.dataset.progressInterval = progressInterval;
    }
    
    // Hàm ẩn loading
    function hideLoading() {
        const overlay = document.getElementById('loadingOverlay');
        const progressBar = document.getElementById('loadingProgress');
        const printBtn = document.getElementById('printBtn');
        
        // Hoàn thành progress bar
        progressBar.style.width = '100%';
        
        // Xóa interval
        if (overlay.dataset.progressInterval) {
            clearInterval(overlay.dataset.progressInterval);
        }
        
        // Ẩn overlay sau 0.5 giây
        setTimeout(() => {
            overlay.style.display = 'none';
            progressBar.style.width = '0%';
            
            // Khôi phục nút
            printBtn.disabled = false;
            printBtn.classList.remove('btn-loading');
        }, 500);
    }
    
    // Hàm hiển thị thông báo
    function showMessage(message, type = 'success') {
        const messageDiv = document.getElementById('resultMessage');
        messageDiv.style.display = 'block';
        messageDiv.className = 'alert alert-' + (type === 'success' ? 'success' : 'danger');
        messageDiv.innerHTML = message;
        
        // Tự động ẩn sau 5 giây
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 5000);
    }
    
    // Hàm chuyển đổi số thành chữ
    function numberToWords(num) {
        const ones = ['', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];
        const tens = ['', '', 'hai mươi', 'ba mươi', 'bốn mươi', 'năm mươi', 'sáu mươi', 'bảy mươi', 'tám mươi', 'chín mươi'];
        const teens = ['mười', 'mười một', 'mười hai', 'mười ba', 'mười bốn', 'mười lăm', 'mười sáu', 'mười bảy', 'mười tám', 'mười chín'];
        
        function convertLessThanOneThousand(n) {
            if (n == 0) return '';
            
            let result = '';
            
            if (n >= 100) {
                result += ones[Math.floor(n / 100)] + ' trăm ';
                n %= 100;
            }
            
            if (n >= 20) {
                result += tens[Math.floor(n / 10)] + ' ';
                n %= 10;
            } else if (n >= 10) {
                result += teens[n - 10] + ' ';
                return result;
            }
            
            if (n > 0) {
                if (n == 5) {
                    result += 'lăm ';
                } else {
                    result += ones[n] + ' ';
                }
            }
            
            return result;
        }
        
        if (num == 0) return 'không';
        
        let result = '';
        const billion = Math.floor(num / 1000000000);
        const million = Math.floor((num % 1000000000) / 1000000);
        const thousand = Math.floor((num % 1000000) / 1000);
        const remainder = num % 1000;
        
        if (billion > 0) {
            result += convertLessThanOneThousand(billion) + 'tỷ ';
        }
        
        if (million > 0) {
            result += convertLessThanOneThousand(million) + 'triệu ';
        }
        
        if (thousand > 0) {
            result += convertLessThanOneThousand(thousand) + 'nghìn ';
        }
        
        result += convertLessThanOneThousand(remainder);
        
        return result.trim() + ' đồng';
    }
    
    // Hàm hiển thị modal in
    function showPrintModal(printFunction) {
        const modal = document.getElementById('printModal');
        modal.style.display = 'block';
        
        // Lưu hàm in
        modal.dataset.printFunction = printFunction;
    }
    
    // Hàm ẩn modal in
    function hidePrintModal() {
        const modal = document.getElementById('printModal');
        modal.style.display = 'none';
    }
    
    // Hàm in file PDF
    function printPDF(pdfUrl, options = {}) {
        return new Promise((resolve, reject) => {
            try {
                // Tạo iframe ẩn để in PDF
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.style.position = 'fixed';
                iframe.style.top = '-10000px';
                iframe.style.left = '-10000px';
                iframe.src = pdfUrl;
                document.body.appendChild(iframe);
                
                iframe.onload = function() {
                    try {
                        // Chờ một chút để PDF tải đầy đủ
                        setTimeout(() => {
                            try {
                                // In nội dung iframe
                                iframe.contentWindow.focus();
                                iframe.contentWindow.print();
                                
                                // Xóa iframe sau khi in
                                setTimeout(() => {
                                    document.body.removeChild(iframe);
                                    resolve(true);
                                }, 1000);
                            } catch (e) {
                                document.body.removeChild(iframe);
                                reject(e);
                            }
                        }, 1000);
                    } catch (e) {
                        document.body.removeChild(iframe);
                        reject(e);
                    }
                };
                
                iframe.onerror = function() {
                    document.body.removeChild(iframe);
                    reject(new Error('Không thể tải file PDF'));
                };
            } catch (error) {
                reject(error);
            }
        });
    }
    
    // Hàm lấy danh sách máy in (mô phỏng - trong thực tế cần ActiveX hoặc WebUSB)
    function loadPrinters() {
        const printerSelect = document.getElementById('printerSelect');
        
        try {
            // Trên trình duyệt hiện đại, JavaScript không thể truy cập trực tiếp vào máy in
            // Chỉ có thể in thông qua hộp thoại in mặc định của trình duyệt
            
            // Thêm tùy chọn in mặc định
            printerSelect.innerHTML = `
                <option value="">-- Chọn máy in --</option>
                <option value="default">Máy in mặc định hệ thống</option>
                <option value="pdf">Lưu thành file PDF</option>
                <option value="dialog">Hiển thị hộp thoại in</option>
            `;
            
            // Cập nhật thông tin máy in mặc định
            document.getElementById('defaultPrinterInfo').textContent = 'Sử dụng hộp thoại in của trình duyệt';
            
        } catch (error) {
            console.error('Không thể tải danh sách máy in:', error);
            document.getElementById('defaultPrinterInfo').textContent = 'Không thể phát hiện máy in';
        }
    }
    
    // Xử lý thay đổi số tiền
    document.getElementById('so_tien').addEventListener('input', function() {
        const soTien = parseInt(this.value.replace(/\D/g, '')) || 0;
        const bangChu = numberToWords(soTien);
        document.getElementById('bang_chu').value = bangChu;
        
        // Format số tiền với dấu chấm
        if (soTien > 0) {
            this.value = soTien.toLocaleString('vi-VN');
        }
    });
    
    // Đặt ngày hiện tại cho các trường ngày
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('ngay_cap').value = today;
    document.getElementById('ngay_cam').value = today;
    document.getElementById('ky_1').value = today;
    document.getElementById('ky_2').value = today;
    
    // Đặt giờ phút hiện tại
    const now = new Date();
    document.getElementById('gio').value = now.getHours().toString().padStart(2, '0');
    document.getElementById('phut').value = now.getMinutes().toString().padStart(2, '0');
    
    // Tải danh sách máy in khi trang được tải
    loadPrinters();
    
    // Xử lý nút hủy in modal
    document.getElementById('cancelPrintBtn').addEventListener('click', hidePrintModal);
    
    // Xử lý nút xác nhận in modal
    document.getElementById('confirmPrintBtn').addEventListener('click', function() {
        hidePrintModal();
        
        // Lấy tùy chọn in
        const copies = parseInt(document.getElementById('printCopies').value) || 1;
        const quality = document.getElementById('printQuality').value;
        const duplex = document.getElementById('printDuplex').checked;
        
        // Gọi hàm in đã lưu
        const printFunction = document.getElementById('printModal').dataset.printFunction;
        if (printFunction && window[printFunction]) {
            window[printFunction](copies, quality, duplex);
        }
    });
    
    // Xử lý nút in hợp đồng
    document.getElementById('printBtn').addEventListener('click', function() {
        // Validate form
        const requiredFields = [
            'ho_ten', 'nam_sinh', 'so_dt', 'cccd_so', 
            'ngay_cap', 'noi_cap', 'noi_dktt', 
            'ten_tai_san', 'so_tien', 'gio', 'phut', 'ngay_cam'
        ];
        
        let isValid = true;
        let firstInvalidField = null;
        
        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (!field.value.trim()) {
                isValid = false;
                if (!firstInvalidField) {
                    firstInvalidField = field;
                }
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            alert('Vui lòng điền đầy đủ các trường bắt buộc!');
            if (firstInvalidField) {
                firstInvalidField.focus();
            }
            return;
        }
        
        // Kiểm tra lựa chọn máy in
        const printerSelect = document.getElementById('printerSelect');
        const selectedPrinter = printerSelect.value;
        const printAction = document.querySelector('input[name="printAction"]:checked').value;
        
        if (!selectedPrinter) {
            alert('Vui lòng chọn máy in hoặc tùy chọn in!');
            printerSelect.focus();
            return;
        }
        
        // Xác định hành động in
        const printProcess = function() {
            // Hiển thị loading
            showLoading();
            
            // Tạo dữ liệu form
            const formData = new FormData();
            const formFields = [
                'ho_ten', 'nam_sinh', 'so_dt', 'cccd_so', 'ngay_cap', 
                'noi_cap', 'noi_dktt', 'ten_tai_san', 'so_tien', 
                'bang_chu', 'gio', 'phut', 'ngay_cam', 'ky_1', 'ky_2'
            ];
            
            formFields.forEach(field => {
                const element = document.getElementById(field);
                if (element) {
                    // Format số tiền: bỏ dấu chấm trước khi gửi
                    if (field === 'so_tien') {
                        const rawValue = element.value.replace(/\./g, '');
                        formData.append(field, rawValue);
                    } else {
                        formData.append(field, element.value);
                    }
                }
            });
            
            // Thêm preview option dựa trên lựa chọn
            const preview = (printAction === 'preview' || selectedPrinter === 'dialog') ? '1' : '0';
            formData.append('preview', preview);
            
            // Gửi request đến server
            fetch('process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                hideLoading();
                
                if (data.success) {
                    // Xử lý theo lựa chọn máy in
                    if (selectedPrinter === 'pdf' || selectedPrinter === 'dialog') {
                        // Mở file PDF để xem hoặc lưu
                        const pdfUrl = data.file_url_pdf || data.file_url;
                        if (pdfUrl) {
                            window.open(pdfUrl, '_blank');
                            showMessage('✅ Tạo hợp đồng thành công! Đang mở file PDF...', 'success');
                        }
                    } else if (selectedPrinter === 'default') {
                        // In trực tiếp
                        const pdfUrl = data.file_url_pdf || data.file_url;
                        if (pdfUrl) {
                            if (printAction === 'preview') {
                                // Xem trước rồi in
                                window.open(pdfUrl, '_blank');
                                showMessage('✅ Tạo hợp đồng thành công! Đang mở file xem trước...', 'success');
                            } else {
                                // In trực tiếp
                                showPrintModal('startPrinting');
                                
                                // Lưu URL PDF để in
                                window.currentPdfUrl = pdfUrl;
                                showMessage('✅ Tạo hợp đồng thành công! Sẵn sàng in...', 'success');
                            }
                        }
                    }
                } else {
                    showMessage('❌ Lỗi: ' + data.message, 'error');
                    console.error('Server error:', data);
                }
            })
            .catch(error => {
                hideLoading();
                showMessage('❌ Lỗi kết nối: ' + error.message, 'error');
                console.error('Fetch error:', error);
            });
        };
        
        // Hàm bắt đầu in
        window.startPrinting = function(copies, quality, duplex) {
            if (window.currentPdfUrl) {
                // In file PDF
                printPDF(window.currentPdfUrl, {
                    copies: copies,
                    quality: quality,
                    duplex: duplex
                })
                .then(() => {
                    showMessage('✅ Đã gửi lệnh in thành công!', 'success');
                })
                .catch(error => {
                    showMessage('❌ Lỗi khi in: ' + error.message, 'error');
                    console.error('Print error:', error);
                    
                    // Nếu không in được, mở file PDF để in thủ công
                    window.open(window.currentPdfUrl, '_blank');
                });
            }
        };
        
        // Hiển thị modal in nếu chọn in trực tiếp
        if (selectedPrinter === 'default' && printAction === 'direct') {
            showPrintModal('startPrinting');
            
            // Lưu hàm xử lý in
            document.getElementById('printModal').dataset.printFunction = 'printProcess';
            
            // Gán lại hàm in để có thể gọi từ modal
            window.printProcess = printProcess;
        } else {
            // Thực hiện ngay
            printProcess();
        }
    });
    
    // Thêm style cho invalid fields
    const style = document.createElement('style');
    style.textContent = `
        .is-invalid {
            border-color: #dc3545 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        
        .is-invalid:focus {
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        }
        
        /* Print button animation */
        @keyframes printPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .print-animating {
            animation: printPulse 0.5s ease-in-out;
        }
    `;
    document.head.appendChild(style);
    
    // Thêm hiệu ứng cho nút in
    const printBtn = document.getElementById('printBtn');
    printBtn.addEventListener('click', function() {
        this.classList.add('print-animating');
        setTimeout(() => {
            this.classList.remove('print-animating');
        }, 500);
    });
});