// Hàm chuyển số thành chữ tiếng Việt
function numberToVietnameseWords(num) {
    if (num == 0) return "Không đồng";
    
    const ones = ["", "một", "hai", "ba", "bốn", "năm", "sáu", "bảy", "tám", "chín"];
    const teens = ["mười", "mười một", "mười hai", "mười ba", "mười bốn", "mười lăm", 
                   "mười sáu", "mười bảy", "mười tám", "mười chín"];
    const tens = ["", "", "hai mươi", "ba mươi", "bốn mươi", "năm mươi", 
                  "sáu mươi", "bảy mươi", "tám mươi", "chín mươi"];
    const scales = ["", "nghìn", "triệu", "tỷ"];
    
    function convertThreeDigits(n) {
        let result = "";
        const hundred = Math.floor(n / 100);
        const remainder = n % 100;
        const ten = Math.floor(remainder / 10);
        const one = remainder % 10;
        
        if (hundred > 0) {
            result += ones[hundred] + " trăm";
            if (remainder > 0) result += " ";
        }
        
        if (remainder >= 10 && remainder < 20) {
            result += teens[remainder - 10];
        } else {
            if (ten > 0) {
                result += tens[ten];
                if (one > 0) result += " ";
            } else if (hundred > 0 && one > 0) {
                result += "linh ";
            }
            
            if (one > 0) {
                if (ten > 1 && one == 1) {
                    result += "mốt";
                } else if (ten > 0 && one == 5) {
                    result += "lăm";
                } else {
                    result += ones[one];
                }
            }
        }
        
        return result.trim();
    }
    
    let result = "";
    let scaleIndex = 0;
    
    while (num > 0) {
        const threeDigits = num % 1000;
        if (threeDigits > 0) {
            const converted = convertThreeDigits(threeDigits);
            if (scaleIndex > 0) {
                result = converted + " " + scales[scaleIndex] + " " + result;
            } else {
                result = converted;
            }
        }
        num = Math.floor(num / 1000);
        scaleIndex++;
    }
    
    result = result.trim();
    result = result.charAt(0).toUpperCase() + result.slice(1);
    return result + " đồng";
}

// Tự động chuyển đổi số tiền sang chữ
document.getElementById('so_tien').addEventListener('input', function(e) {
    const soTien = this.value.replace(/\D/g, '');
    if (soTien) {
        const bangChu = numberToVietnameseWords(parseInt(soTien));
        document.getElementById('bang_chu').value = bangChu;
    } else {
        document.getElementById('bang_chu').value = '';
    }
});

// Xử lý nút in hợp đồng
document.getElementById('printBtn').addEventListener('click', function(e) {
    e.preventDefault();
    
    const form = document.getElementById('contractForm');
    
    // Kiểm tra validation
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Lấy dữ liệu từ form
    const formData = new FormData(form);
    
    // Kiểm tra checkbox xem trước
    const previewCheck = document.getElementById('previewCheck').checked;
    formData.append('preview', previewCheck ? '1' : '0');
    
    // Hiển thị loading
    const btnPrint = document.getElementById('printBtn');
    const originalText = btnPrint.textContent;
    btnPrint.disabled = true;
    btnPrint.textContent = 'Đang xử lý...';
    
    // Gửi dữ liệu đến server
    // Debug: Hiển thị URL
    const baseUrl = window.location.origin + window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/') + 1);
    const processUrl = baseUrl + 'process.php';
    console.log('Sending request to:', processUrl);
    
    fetch(processUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Kiểm tra content-type
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            // Nếu không phải JSON, đọc text để debug
            return response.text().then(text => {
                console.error('Response không phải JSON:', text);
                throw new Error('Server trả về HTML thay vì JSON. Kiểm tra file process.php có lỗi.');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            if (previewCheck) {
                // Mở file preview trong tab mới
                window.open(data.file_url, '_blank');
                alert('File hợp đồng đã được tạo và mở để xem trước!');
            } else {
                // Tự động tải file
                const link = document.createElement('a');
                link.href = data.file_url;
                link.download = data.filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                alert('File hợp đồng đã được tạo và tải xuống!');
            }
        } else {
            // Hiển thị lỗi chi tiết
            let errorMsg = 'Lỗi: ' + data.message;
            if (data.file && data.line) {
                errorMsg += '\nFile: ' + data.file + ' (Line: ' + data.line + ')';
            }
            alert(errorMsg);
            console.error('Error details:', data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi tạo hợp đồng: ' + error.message);
    })
    .finally(() => {
        // Khôi phục trạng thái button
        btnPrint.disabled = false;
        btnPrint.textContent = originalText;
    });
});

// Format số điện thoại khi nhập
document.getElementById('so_dt').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^\d]/g, '');
});

// Format CCCD khi nhập
document.getElementById('cccd_so').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^\d]/g, '');
});

// Format giờ và phút
document.getElementById('gio').addEventListener('input', function(e) {
    let value = this.value.replace(/[^\d]/g, '');
    if (value.length > 2) value = value.slice(0, 2);
    if (parseInt(value) > 23) value = '23';
    this.value = value;
});

document.getElementById('phut').addEventListener('input', function(e) {
    let value = this.value.replace(/[^\d]/g, '');
    if (value.length > 2) value = value.slice(0, 2);
    if (parseInt(value) > 59) value = '59';
    this.value = value;
});