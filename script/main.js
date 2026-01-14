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
    
    // Hàm in file PDF trực tiếp
    function printPDF(pdfUrl) {
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
    
    // Xử lý nút in hợp đồng - LUÔN IN TRỰC TIẾP
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
        
        // LUÔN SỬ DỤNG MÁY IN MẶC ĐỊNH VÀ IN TRỰC TIẾP
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
            
            // LUÔN đặt preview = 0 để in trực tiếp
            formData.append('preview', '0');
            
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
                    // LUÔN in trực tiếp với máy in mặc định
                    const pdfUrl = data.file_url_pdf || data.file_url;
                    if (pdfUrl) {
                        printPDF(pdfUrl)
                            .then(() => {
                                showMessage('✅ Hợp đồng đã được gửi đến máy in mặc định!', 'success');
                            })
                            .catch(error => {
                                console.error('Print error:', error);
                                // Nếu không in được, mở file PDF để in thủ công
                                window.open(pdfUrl, '_blank');
                                showMessage('✅ Đã mở file PDF. Vui lòng in thủ công từ trình duyệt.', 'success');
                            });
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
        
        // Thực hiện in trực tiếp
        printProcess();
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