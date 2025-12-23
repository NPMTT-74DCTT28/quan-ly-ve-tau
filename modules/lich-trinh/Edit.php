<?php
include_once '../../config/database.php';
require_once '../../includes/header.php';


// Lấy danh sách tàu từ database
$sql_tau = "SELECT id, ma_tau, ten_tau FROM tau ORDER BY ma_tau";
$stmt_tau = $pdo->query($sql_tau);
$tau_list = $stmt_tau->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách tuyến đường từ database
$sql_tuyen_duong = "SELECT id, ma_tuyen, ten_tuyen FROM tuyen_duong ORDER BY ma_tuyen";
$stmt_tuyen_duong = $pdo->query($sql_tuyen_duong);
$tuyen_duong_list = $stmt_tuyen_duong->fetchAll(PDO::FETCH_ASSOC);

$id = $ma_lich_trinh = $id_tau = $id_tuyen_duong = $ngay_di = $ngay_den = $trang_thai = '';
$error_message = '';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql_select = "SELECT * FROM lich_trinh WHERE id = ?";
    $stmt = $pdo->prepare($sql_select);
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        $id = $row['id'];
        $ma_lich_trinh = $row['ma_lich_trinh'];
        $id_tau = $row['id_tau'];
        $id_tuyen_duong = $row['id_tuyen_duong'];
        $ngay_di = date('Y-m-d\TH:i', strtotime($row['ngay_di']));
        $ngay_den = date('Y-m-d\TH:i', strtotime($row['ngay_den']));
        $trang_thai = $row['trang_thai'];
    } else {
        echo "<script>alert('Không tìm thấy lịch trình!'); window.location='index.php';</script>";
    }
} else {
    echo "<script>alert('Không có ID lịch trình!'); window.location='index.php';</script>";
}

// Xử lý khi người dùng nhấn nút Lưu
if (isset($_POST['btnEdit'])) {
    $id = $_POST['id'];
    $ma_lich_trinh = $_POST['ma_lich_trinh'];
    $id_tau = $_POST['id_tau'];
    $id_tuyen_duong = $_POST['id_tuyen_duong'];
    $ngay_di = $_POST['ngay_di'];
    $ngay_den = $_POST['ngay_den'];
    $trang_thai = $_POST['trang_thai'];

    // Validate ngày đến không được trước ngày đi
    if (strtotime($ngay_den) < strtotime($ngay_di)) {
        $error_message = "Ngày đến không được trước ngày đi!";
    } else {
        // Cập nhật thông tin lịch trình
        $sql_update = "UPDATE lich_trinh SET 
                       ma_lich_trinh = ?, 
                       id_tau = ?, 
                       id_tuyen_duong = ?, 
                       ngay_di = ?, 
                       ngay_den = ?, 
                       trang_thai = ? 
                       WHERE id = ?";
        $stmt_update = $pdo->prepare($sql_update);
        if ($stmt_update->execute([$ma_lich_trinh, $id_tau, $id_tuyen_duong, $ngay_di, $ngay_den, $trang_thai, $id])) {
            echo "<script>alert('Cập nhật thông tin thành công!'); window.location='index.php';</script>";
        } else {
            $error_message = "Cập nhật thông tin thất bại!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật lịch trình</title>
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
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
        .form-control[readonly] {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }
        .form-container{
            background-color: #81aad3ff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>CẬP NHẬT THÔNG TIN LỊCH TRÌNH</h2>
            
            <?php if(isset($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <form method="post" action="">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ma_lich_trinh" class="form-label required">Mã Lịch Trình:</label>
                            <input type="text" class="form-control" name="ma_lich_trinh" value="<?php echo htmlspecialchars($ma_lich_trinh); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_tau" class="form-label required">Tàu:</label>
                            <select class="form-control" name="id_tau" required>
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
                            <select class="form-control" name="id_tuyen_duong" required>
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
                            <select class="form-control" name="trang_thai" required>
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
                    <button type="submit" class="btn btn-primary btn-custom" name="btnEdit">
                        <i class="bi bi-save"></i> Cập nhật
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
    });
    </script>
</body>
</html>