<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__. '/../../includes/header.php';

$conn = $db->getConnection();

$ma_tau = $ten_tau = '';
$show_success = false;
$show_error = false;
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['btnAdd'])) {
        $ma_tau = trim($_POST['ma_tau']);
        $ten_tau = trim($_POST['ten_tau']);

        if (empty($ma_tau) || empty($ten_tau)) {
            $error_message = "Vui lòng điền đầy đủ thông tin mã tàu và tên tàu!";
            $show_error = true;
        } else {
            $sql_check = "SELECT * FROM tau WHERE ma_tau = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->execute([$ma_tau]);

            if ($stmt_check->rowCount() > 0) {
                $error_message = "Mã tàu này đã tồn tại! Vui lòng nhập mã khác.";
                $show_error = true;
            } else {
                $sql_insert = "INSERT INTO tau (ma_tau, ten_tau) VALUES (?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                
                if ($stmt_insert->execute([$ma_tau, $ten_tau])) {
                    $success_message = "Thêm mới tàu thành công!";
                    $show_success = true;
                    $ma_tau = $ten_tau = '';
                } else {
                    $error_message = "Thêm thông tin thất bại! Vui lòng thử lại.";
                    $show_error = true;
                }
            }
        }
    }

    if (isset($_POST['btnBack'])) {
        header('location:index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Tàu Mới</title>
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
        h2 { color: #2c3e50; text-align: center; margin-bottom: 30px; font-weight: 700; }
        .form-label { font-weight: 600; color: #34495e; }
        .btn-container { display: flex; justify-content: center; gap: 20px; margin-top: 30px; }
        .btn-custom { min-width: 150px; padding: 10px 25px; font-weight: 600; }
        .required::after { content: " *"; color: #dc3545; }
        .alert:not(.show) { display: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>THÊM TÀU MỚI</h2>

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
                    <label for="ma_tau" class="form-label required">Mã Tàu:</label>
                    <input type="text" class="form-control" name="ma_tau" 
                           value="<?php echo htmlspecialchars($ma_tau); ?>" 
                           required placeholder="VD: T01, SE1...">
                </div>

                <div class="mb-3">
                    <label for="ten_tau" class="form-label required">Tên Tàu:</label>
                    <input type="text" class="form-control" name="ten_tau" 
                           value="<?php echo htmlspecialchars($ten_tau); ?>" 
                           required placeholder="VD: Tàu Thống Nhất">
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