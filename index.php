<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hợp Đồng Cầm Cố Tài Sản - Bản quyền: Vmtech - 0944947411</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .loading-content {
            background: white;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 90%;
        }
        
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading-text {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .loading-subtext {
            font-size: 14px;
            color: #666;
        }
        
        /* Button with loading state */
        .btn-loading {
            position: relative;
            color: transparent !important;
        }
        
        .btn-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid #fff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }

        /* Printer selection */
        .printer-section {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .printer-section-title {
            font-weight: bold;
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
        }

        .printer-info {
            font-size: 12px;
            color: #666;
            font-style: italic;
            margin-top: 5px;
        }

        /* Print Options */
        .print-options {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #ccc;
        }

        .option-label {
            font-weight: normal;
            font-size: 14px;
            margin-left: 5px;
        }

        /* Modal */
        .print-modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .print-modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 500px;
            max-width: 90%;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .print-modal-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }

        .print-actions {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">ĐANG XỬ LÝ...</div>
            <div class="loading-subtext">Vui lòng chờ trong giây lát</div>
            <div class="loading-progress mt-3">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" 
                         style="width: 0%" 
                         id="loadingProgress"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Options Modal -->
    <div class="print-modal" id="printModal">
        <div class="print-modal-content">
            <div class="print-modal-title">TÙY CHỌN IN</div>
            
            <div class="mb-3">
                <label for="printCopies" class="form-label">Số bản in:</label>
                <input type="number" class="form-control" id="printCopies" value="1" min="1" max="10">
            </div>

            <div class="mb-3">
                <label class="form-label">Chất lượng:</label>
                <select class="form-control" id="printQuality">
                    <option value="standard">Tiêu chuẩn</option>
                    <option value="high">Chất lượng cao</option>
                    <option value="draft">Nháp</option>
                </select>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="printDuplex" checked>
                <label class="form-check-label" for="printDuplex">In hai mặt (nếu máy hỗ trợ)</label>
            </div>

            <div class="print-actions">
                <button type="button" class="btn btn-secondary" id="cancelPrintBtn">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmPrintBtn">In ngay</button>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="form-container">
            <h2 class="text-center mb-4 form-title">HỢP ĐỒNG CẦM CỐ TÀI SẢN</h2>

            <!-- Printer Selection Section -->
            <div class="printer-section">
                <div class="printer-section-title">THIẾT LẬP MÁY IN</div>
                
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label fw-bold">Chọn máy in:</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="printerSelect">
                            <option value="">-- Chọn máy in --</option>
                            <option value="default">Máy in mặc định hệ thống</option>
                            <option value="pdf">Lưu thành file PDF</option>
                            <!-- Danh sách máy in sẽ được tải qua JavaScript -->
                        </select>
                        <div class="printer-info">
                            Máy in mặc định: <span id="defaultPrinterInfo">Đang tải...</span>
                        </div>
                    </div>
                </div>

                <div class="print-options">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="printAction" id="printDirect" value="direct" checked>
                        <label class="form-check-label option-label" for="printDirect">
                            In trực tiếp
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="printAction" id="printPreview" value="preview">
                        <label class="form-check-label option-label" for="printPreview">
                            Xem trước rồi in
                        </label>
                    </div>
                </div>
            </div>

            <form id="contractForm">
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label fw-bold">HỌ VÀ TÊN</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="ho_ten" id="ho_ten" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label fw-bold">NĂM SINH</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="nam_sinh" id="nam_sinh" required>
                    </div>
                    <label class="col-sm-2 col-form-label fw-bold">Số ĐT</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="so_dt" id="so_dt" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label fw-bold">CCCD Số</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="cccd_so" id="cccd_so" required>
                    </div>
                    <label class="col-sm-2 col-form-label fw-bold">NGÀY CẤP</label>
                    <div class="col-sm-4">
                        <input type="date" class="form-control" name="ngay_cap" id="ngay_cap" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label fw-bold">NƠI CẤP</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="noi_cap" id="noi_cap" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label fw-bold">NƠI ĐKTT</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="noi_dktt" id="noi_dktt" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label fw-bold">TÊN TÀI SẢN CẦM</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="ten_tai_san" id="ten_tai_san" rows="2" required></textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label fw-bold">SỐ TIỀN</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="so_tien" id="so_tien" placeholder="VD: 50000000" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label fw-bold">BẰNG CHỮ:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="bang_chu" id="bang_chu" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label fw-bold">THỜI ĐIỂM CẦM:</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" name="gio" id="gio" placeholder="GIỜ" required>
                    </div>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" name="phut" id="phut" placeholder="PHÚT" required>
                    </div>
                    <label class="col-sm-2 col-form-label fw-bold">NGÀY CẦM</label>
                    <div class="col-sm-4">
                        <input type="date" class="form-control" name="ngay_cam" id="ngay_cam" required>
                    </div>
                </div>

                <h5 class="text-center mb-3 section-title">THỜI GIAN GIA HẠN HỢP ĐỒNG</h5>

                <div class="row mb-3">
                    <div class="col-sm-6">
                        <div class="row">
                            <label class="col-sm-4 col-form-label fw-bold">Kỳ 1</label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control" name="ky_1" id="ky_1">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <label class="col-sm-4 col-form-label fw-bold">Kỳ 2</label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control" name="ky_2" id="ky_2">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="button" class="btn btn-primary btn-lg" id="printBtn">IN HỢP ĐỒNG</button>
                    <div class="mt-3">
                        <div id="resultMessage" style="display: none;"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script/main.js"></script>
</body>
</html>
