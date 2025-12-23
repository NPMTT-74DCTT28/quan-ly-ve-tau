<?php
include_once '../../config/database.php';
require_once '../../includes/header.php';

$id = $cccd = $ho_ten = $ngay_sinh = $gioi_tinh = $sdt = $dia_chi = '';
$error_message = '';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql_select = "SELECT * FROM khach_hang WHERE id = ?";
    $stmt = $pdo->prepare($sql_select);
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        $id = $row['id'];
        $cccd = $row['cccd'];
        $ho_ten = $row['ho_ten'];
        $ngay_sinh = !empty($row['ngay_sinh']) ? date('Y-m-d', strtotime($row['ngay_sinh'])) : '';
        $gioi_tinh = $row['gioi_tinh'];
        $sdt = $row['sdt'];
        $dia_chi = $row['dia_chi'];
    } else {
        echo "<script>alert('Không tìm thấy khách hàng!'); window.location='index.php';</script>";
    }
} else {
    echo "<script>alert('Không có ID khách hàng!'); window.location='index.php';</script>";
}

// Xử lý khi người dùng nhấn nút Cập nhật
if (isset($_POST['btnsua'])) {
    $id = $_POST['id'];
    $cccd = $_POST['cccd'];
    $ho_ten = $_POST['ho_ten'];
    $ngay_sinh = $_POST['ngay_sinh'] ? $_POST['ngay_sinh'] . ' 00:00:00' : null;
    $gioi_tinh = $_POST['gioi_tinh'];
    $sdt = $_POST['sdt'];
    $dia_chi = $_POST['dia_chi'];

    // Kiểm tra số điện thoại đã tồn tại chưa (trừ khách hàng hiện tại)
    $sql_check_sdt = "SELECT * FROM khach_hang WHERE sdt = ? AND id != ?";
    $stmt_check_sdt = $pdo->prepare($sql_check_sdt);
    $stmt_check_sdt->execute([$sdt, $id]);

    if ($stmt_check_sdt->rowCount() > 0) {
        $error_message = "Số điện thoại đã tồn tại! Vui lòng nhập số khác.";
    } else {
        // Kiểm tra CCCD nếu có
        if (!empty($cccd)) {
            $sql_check_cccd = "SELECT * FROM khach_hang WHERE cccd = ? AND id != ?";
            $stmt_check_cccd = $pdo->prepare($sql_check_cccd);
            $stmt_check_cccd->execute([$cccd, $id]);

            if ($stmt_check_cccd->rowCount() > 0) {
                $error_message = "Số CCCD đã tồn tại! Vui lòng nhập số khác.";
            }
        }

        if (empty($error_message)) {
            // Cập nhật thông tin khách hàng
            $sql_update = "UPDATE khach_hang SET 
                           cccd = ?, 
                           ho_ten = ?, 
                           ngay_sinh = ?, 
                           gioi_tinh = ?, 
                           sdt = ?, 
                           dia_chi = ? 
                           WHERE id = ?";
            $stmt_update = $pdo->prepare($sql_update);
            if ($stmt_update->execute([$cccd, $ho_ten, $ngay_sinh, $gioi_tinh, $sdt, $dia_chi, $id])) {
                echo "<script>alert('Cập nhật thông tin thành công!'); window.location='index.php';</script>";
            } else {
                $error_message = "Cập nhật thông tin thất bại!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật khách hàng</title>
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
            <h2>CẬP NHẬT THÔNG TIN KHÁCH HÀNG</h2>
            
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
                            <label for="cccd" class="form-label">CCCD:</label>
                            <input type="text" class="form-control" name="cccd" value="<?php echo htmlspecialchars($cccd); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ho_ten" class="form-label required">Họ và Tên:</label>
                            <input type="text" class="form-control" name="ho_ten" value="<?php echo htmlspecialchars($ho_ten); ?>" required>
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
                            <input type="text" class="form-control" name="sdt" value="<?php echo htmlspecialchars($sdt); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="dia_chi" class="form-label required">Địa chỉ:</label>
                            <input type="text" class="form-control" name="dia_chi" value="<?php echo htmlspecialchars($dia_chi); ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="btn-container">
                    <button type="submit" class="btn btn-primary btn-custom" name="btnsua">
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
</body>
<?php
require_once '../../includes/footer.php';
?>
</html>