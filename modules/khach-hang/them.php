<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';
$conn = $db->getConnection();

$cccd = $ho_ten = $ngay_sinh = $gioi_tinh = $sdt = $dia_chi = '';
$show_success = false;
$show_error = false;
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['btnAdd'])) {
        $cccd = $_POST['cccd'];
        $ho_ten = $_POST['ho_ten'];
        $ngay_sinh = $_POST['ngay_sinh'] ? $_POST['ngay_sinh'] . ' 00:00:00' : null;
        $gioi_tinh = $_POST['gioi_tinh'];
        $sdt = $_POST['sdt'];
        $dia_chi = $_POST['dia_chi'];

        if (empty($ho_ten) || empty($gioi_tinh) || empty($sdt) || empty($dia_chi)) {
            $error_message = "Vui lòng điền đầy đủ thông tin bắt buộc!";
            $show_error = true;
        } else {
            $sql_check_sdt = "SELECT * FROM khach_hang WHERE sdt = ?";
            $stmt_check_sdt = $conn->prepare($sql_check_sdt);
            $stmt_check_sdt->execute([$sdt]);

            if ($stmt_check_sdt->rowCount() > 0) {
                $error_message = "Số điện thoại đã tồn tại! Vui lòng nhập số khác.";
                $show_error = true;
            } else {
                if (!empty($cccd)) {
                    $sql_check_cccd = "SELECT * FROM khach_hang WHERE cccd = ?";
                    $stmt_check_cccd = $conn->prepare($sql_check_cccd);
                    $stmt_check_cccd->execute([$cccd]);

                    if ($stmt_check_cccd->rowCount() > 0) {
                        $error_message = "Số CCCD đã tồn tại! Vui lòng nhập số khác.";
                        $show_error = true;
                    }
                }

                if (!$show_error) {
                    $sql_insert = "INSERT INTO khach_hang (cccd, ho_ten, ngay_sinh, gioi_tinh, sdt, dia_chi) 
                                   VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    if ($stmt_insert->execute([$cccd, $ho_ten, $ngay_sinh, $gioi_tinh, $sdt, $dia_chi])) {
                        $success_message = "Thêm thông tin khách hàng thành công!";
                        $show_success = true;
                        $cccd = $ho_ten = $ngay_sinh = $gioi_tinh = $sdt = $dia_chi = '';
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
    <title>Thêm khách hàng mới</title>
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
        .alert:not(.show) {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>THÊM KHÁCH HÀNG MỚI</h2>

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
                            <label for="cccd" class="form-label">CCCD:</label>
                            <input type="text" class="form-control" name="cccd" value="<?php echo htmlspecialchars($cccd); ?>" placeholder="VD: 012345678901">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ho_ten" class="form-label required">Họ và Tên:</label>
                            <input type="text" class="form-control" name="ho_ten" value="<?php echo htmlspecialchars($ho_ten); ?>" required placeholder="VD: Nguyễn Văn A">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ngay_sinh" class="form-label">Ngày sinh:</label>
                            <input type="date" class="form-control" name="ngay_sinh" value="<?php echo htmlspecialchars($ngay_sinh); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="gioi_tinh" class="form-label required">Giới tính:</label>
                            <select class="form-control" name="gioi_tinh" required>
                                <option value="">-- Chọn giới tính --</option>
                                <option value="Nam" <?php echo $gioi_tinh == 'Nam' ? 'selected' : ''; ?>>Nam</option>
                                <option value="Nữ" <?php echo $gioi_tinh == 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
                                <option value="Khác" <?php echo $gioi_tinh == 'Khác' ? 'selected' : ''; ?>>Khác</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sdt" class="form-label required">Số điện thoại:</label>
                            <input type="text" class="form-control" name="sdt" value="<?php echo htmlspecialchars($sdt); ?>" required placeholder="VD: 0912345678">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="dia_chi" class="form-label required">Địa chỉ:</label>
                            <input type="text" class="form-control" name="dia_chi" value="<?php echo htmlspecialchars($dia_chi); ?>" required placeholder="VD: 123 Đường ABC, Quận 1, TP.HCM">
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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script>
    
    document.addEventListener('DOMContentLoaded', function() {
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