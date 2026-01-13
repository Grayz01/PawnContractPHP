<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hợp Đồng Cầm Cố Tài Sản - Bản quyền: Vmtech - 0944947411</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container-fluid">
        <div class="form-container">
            <h2 class="text-center mb-4 form-title">HỢP ĐỒNG CẦM CỐ TÀI SẢN</h2>

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

                <!-- <div class="row mb-4">
                    <div class="col-sm-6">
                        <div class="row">
                            <label class="col-sm-4 col-form-label fw-bold">Kỳ 3</label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control" name="ky_3" id="ky_3">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <label class="col-sm-4 col-form-label fw-bold">Kỳ 4</label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control" name="ky_4" id="ky_4">
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- Checkbox và nút xem trước -->
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="previewCheck">
                            <label class="form-check-label fw-bold" for="previewCheck">
                                Xem trước trước khi in
                            </label>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="button" class="btn btn-primary btn-lg" id="printBtn">IN HỢP ĐỒNG</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script/main.js"></script>
</body>
</html>