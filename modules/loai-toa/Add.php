<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__. '/../../includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

requireAdmin();

$conn = $db->getConnection();

// Khởi tạo biến theo tên trường mới
$ten_loai = $he_so_gia = '';
$show_success = false;
$show_error = false;
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['btnAdd'])) {
        $ten_loai = trim($_POST['ten_loai']);
        $he_so_gia = trim($_POST['he_so_gia']);

        if (empty($ten_loai) || empty($he_so_gia)) {
            $error_message = "Vui lòng điền đầy đủ thông tin tên loại và hệ số giá!";
            $show_error = true;
        } else {
            $sql_check = "SELECT * FROM loai_toa WHERE ten_loai = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->execute([$ten_loai]);

            if ($stmt_check->rowCount() > 0) {
                $error_message = "Tên loại này đã tồn tại!";
                $show_error = true;
            } else {
                // Thêm mới vào bảng loai_toa
                $sql_insert = "INSERT INTO loai_toa (ten_loai, he_so_gia) VALUES (?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                
                if ($stmt_insert->execute([$ten_loai, $he_so_gia])) {
                    $success_message = "Thêm mới loại toa thành công!";
                    $show_success = true;
                    $ten_loai = $he_so_gia = ''; 
                } else {
                    $error_message = "Thêm thông tin thất bại! Vui lòng thử lại.";
                    $show_error = true;
                }
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
    <title>Thêm Loại Toa Mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .container { max-width: 600px; margin-top: 50px; }
        .form-container {
            background-color: #7ca3c9; 
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        h2 { color: #2c3e50; text-align: center; margin-bottom: 30px; font-weight: 700; text-transform: uppercase; }
        .form-label { font-weight: 600; color: #34495e; }
        .btn-container { display: flex; justify-content: center; gap: 20px; margin-top: 30px; }
        .btn-custom { min-width: 150px; padding: 10px 25px; font-weight: 600; }
        .required::after { content: " *"; color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>THÊM LOẠI TOA MỚI</h2>

            <?php if ($show_success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($show_error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="mb-3">
                    <label for="ten_loai" class="form-label required">Tên Loại:</label>
                    <input type="text" class="form-control" name="ten_loai" 
                           value="<?php echo htmlspecialchars($ten_loai); ?>" 
                           required placeholder="VD: Ngồi mềm điều hòa, Giường nằm...">
                </div>

                <div class="mb-3">
                    <label for="he_so_gia" class="form-label required">Hệ Số Giá:</label>
                    <input type="number" step="0.01" class="form-control" name="he_so_gia" 
                           value="<?php echo htmlspecialchars($he_so_gia); ?>" 
                           required placeholder="VD: 1.25">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tự động ẩn thông báo sau 4 giây
        setTimeout(function() {
            let alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 4000);
    </script>
</body>
<?php require_once '../../includes/footer.php'; ?>
</html>