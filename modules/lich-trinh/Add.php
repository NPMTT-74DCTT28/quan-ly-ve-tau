<?php
require_once '../../config/database.php';
require_once '../../includes/header.php';



// Lấy danh sách tàu từ database
$sql_tau = "SELECT id, ma_tau, ten_tau FROM tau ORDER BY ma_tau";
$stmt_tau = $pdo->query($sql_tau);
$tau_list = $stmt_tau->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách tuyến đường từ database
$sql_tuyen_duong = "SELECT id, ma_tuyen, ten_tuyen FROM tuyen_duong ORDER BY ma_tuyen";
$stmt_tuyen_duong = $pdo->query($sql_tuyen_duong);
$tuyen_duong_list = $stmt_tuyen_duong->fetchAll(PDO::FETCH_ASSOC);

// Khởi tạo biến
$ma_lich_trinh = $id_tau = $id_tuyen_duong = $ngay_di = $ngay_den = $trang_thai = '';
$show_success = false;
$show_error = false;
$success_message = '';
$error_message = '';

// Chỉ xử lý khi có submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['btnAdd'])) {
        $ma_lich_trinh = $_POST['ma_lich_trinh'];
        $id_tau = $_POST['id_tau'];
        $id_tuyen_duong = $_POST['id_tuyen_duong'];
        $ngay_di = $_POST['ngay_di'];
        $ngay_den = $_POST['ngay_den'];
        $trang_thai = $_POST['trang_thai'];

        // Kiểm tra dữ liệu đầu vào
        if (empty($ma_lich_trinh) || empty($id_tau) || empty($id_tuyen_duong) || empty($ngay_di) || empty($ngay_den) || empty($trang_thai)) {
            $error_message = "Vui lòng điền đầy đủ thông tin bắt buộc!";
            $show_error = true;
        } else {
            // Validate ngày đến không được trước ngày đi
            if (strtotime($ngay_den) < strtotime($ngay_di)) {
                $error_message = "Ngày đến không được trước ngày đi!";
                $show_error = true;
            } else {
                // Kiểm tra mã lịch trình đã tồn tại chưa
                $sql_check = "SELECT * FROM lich_trinh WHERE ma_lich_trinh = ?";
                $stmt_check = $pdo->prepare($sql_check);
                $stmt_check->execute([$ma_lich_trinh]);

                if ($stmt_check->rowCount() > 0) {
                    $error_message = "Mã lịch trình đã tồn tại! Vui lòng nhập mã khác.";
                    $show_error = true;
                } else {
                    // Thêm mới
                    $sql_insert = "INSERT INTO lich_trinh (ma_lich_trinh, id_tau, id_tuyen_duong, ngay_di, ngay_den, trang_thai) 
                                   VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt_insert = $pdo->prepare($sql_insert);
                    if ($stmt_insert->execute([$ma_lich_trinh, $id_tau, $id_tuyen_duong, $ngay_di, $ngay_den, $trang_thai])) {
                        $success_message = "Thêm thông tin lịch trình thành công!";
                        $show_success = true;
                        // Reset form
                        $ma_lich_trinh = $id_tau = $id_tuyen_duong = $ngay_di = $ngay_den = $trang_thai = '';
                    } else {
                        $error_message = "Thêm thông tin thất bại! Vui lòng thử lại.";
                        $show_error = true;
                    }
                }
            }
        }
    }

    if (isset($_POST['btnBack'])) {
        header('location:index.php');
        exit();
    }
}

// Nếu không phải POST request, reset các biến thông báo
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $show_success = false;
    $show_error = false;
    $success_message = '';
    $error_message = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm lịch trình mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .container {
            max-width: 800px;
            margin-top: 50px;
        }
        .form-container {
            background-color: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
        }
        .form-label {
            font-weight: 600;
            color: #34495e;
        }
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }
        .btn-custom {
            min-width: 150px;
            padding: 10px 25px;
            font-weight: 600;
        }
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .form-control:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
        }
        .required::after {
            content: " *";
            color: #dc3545;
        }
        .form-container {
            background-color: #7ca3c9ff;
        }
        /* Ẩn alert mặc định */
        .alert:not(.show) {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>THÊM LỊCH TRÌNH MỚI</h2>

            <?php if ($show_success && !empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($show_error && !empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ma_lich_trinh" class="form-label required">Mã Lịch Trình:</label>
                            <input type="text" class="form-control" name="ma_lich_trinh" id="ma_lich_trinh" value="<?php echo htmlspecialchars($ma_lich_trinh); ?>" required placeholder="VD: LT001">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_tau" class="form-label required">Tàu:</label>
                            <select class="form-control" name="id_tau" id="id_tau" required>
                                <option value="">-- Chọn tàu --</option>
                                <?php foreach ($tau_list as $tau): ?>
                                    <option value="<?php echo $tau['id']; ?>" <?php echo $id_tau == $tau['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($tau['ma_tau'] . ' - ' . $tau['ten_tau']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_tuyen_duong" class="form-label required">Tuyến Đường:</label>
                            <select class="form-control" name="id_tuyen_duong" id="id_tuyen_duong" required>
                                <option value="">-- Chọn tuyến đường --</option>
                                <?php foreach ($tuyen_duong_list as $tuyen): ?>
                                    <option value="<?php echo $tuyen['id']; ?>" <?php echo $id_tuyen_duong == $tuyen['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($tuyen['ma_tuyen'] . ' - ' . $tuyen['ten_tuyen']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="trang_thai" class="form-label required">Trạng Thái:</label>
                            <select class="form-control" name="trang_thai" id="trang_thai" required>
                                <option value="Chưa chạy" <?php echo $trang_thai == 'Chưa chạy' ? 'selected' : ''; ?>>Chưa chạy</option>
                                <option value="Đang chạy" <?php echo $trang_thai == 'Đang chạy' ? 'selected' : ''; ?>>Đang chạy</option>
                                <option value="Đã hoàn thành" <?php echo $trang_thai == 'Đã hoàn thành' ? 'selected' : ''; ?>>Đã hoàn thành</option>
                                <option value="Hủy" <?php echo $trang_thai == 'Hủy' ? 'selected' : ''; ?>>Hủy</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ngay_di" class="form-label required">Ngày Giờ Đi:</label>
                            <input type="datetime-local" class="form-control" name="ngay_di" id="ngay_di" value="<?php echo htmlspecialchars($ngay_di); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ngay_den" class="form-label required">Ngày Giờ Đến:</label>
                            <input type="datetime-local" class="form-control" name="ngay_den" id="ngay_den" value="<?php echo htmlspecialchars($ngay_den); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="btn-container">
                    <button type="submit" class="btn btn-success btn-custom" name="btnAdd">
                        <i class="bi bi-plus-circle"></i> Thêm mới
                    </button>
                    <a href="index.php" class="btn btn-secondary btn-custom">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Thêm Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script>
    // Validate ngày đến không được trước ngày đi
    document.addEventListener('DOMContentLoaded', function() {
        const ngayDiInput = document.getElementById('ngay_di');
        const ngayDenInput = document.getElementById('ngay_den');
        const form = document.querySelector('form');
        
        function validateDates() {
            const ngayDi = new Date(ngayDiInput.value);
            const ngayDen = new Date(ngayDenInput.value);
            
            if (ngayDen < ngayDi) {
                alert('Ngày đến không được trước ngày đi!');
                ngayDenInput.value = '';
                ngayDenInput.focus();
                return false;
            }
            return true;
        }
        
        // Validate khi thay đổi ngày đến
        ngayDenInput.addEventListener('change', function() {
            if (ngayDiInput.value && ngayDenInput.value) {
                validateDates();
            }
        });
        
        // Validate khi submit form
        form.addEventListener('submit', function(e) {
            if (ngayDiInput.value && ngayDenInput.value) {
                if (!validateDates()) {
                    e.preventDefault();
                    return false;
                }
            }
            return true;
        });
        
        // Set min date cho ngày đến = ngày đi
        ngayDiInput.addEventListener('change', function() {
            if (ngayDiInput.value) {
                ngayDenInput.min = ngayDiInput.value;
                
                // Nếu ngày đến đã chọn mà trước ngày đi mới
                if (ngayDenInput.value && new Date(ngayDenInput.value) < new Date(ngayDiInput.value)) {
                    ngayDenInput.value = '';
                    alert('Vui lòng chọn ngày đến sau ngày đi mới!');
                }
            }
        });
        
        // Xử lý alert
        var alertElements = document.querySelectorAll('.alert');
        alertElements.forEach(function(alert) {
            var closeButton = alert.querySelector('.btn-close');
            if (closeButton) {
                closeButton.addEventListener('click', function() {
                    alert.classList.remove('show');
                    alert.classList.add('hide');
                });
            }
        });
        
        // Tự động ẩn alert sau 5 giây
        setTimeout(function() {
            alertElements.forEach(function(alert) {
                if (alert.classList.contains('show')) {
                    alert.classList.remove('show');
                    alert.classList.add('hide');
                }
            });
        }, 5000);
    });
    </script>
</body>
<?php
require_once '../../includes/footer.php';
?>
</html>