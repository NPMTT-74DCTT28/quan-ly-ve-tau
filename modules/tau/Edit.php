<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__. '/../../includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

$conn = $db->getConnection();

$id = $ma_tau = $ten_tau = '';
$error_message = '';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql_select = "SELECT * FROM tau WHERE id = ?";
    $stmt = $conn->prepare($sql_select);
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        $ma_tau = $row['ma_tau'];
        $ten_tau = $row['ten_tau'];
    } else {
        echo "<script>alert('Không tìm thấy thông tin tàu!'); window.location='index.php';</script>";
        exit;
    }
} else {
    header('location:index.php');
    exit;
}

if (isset($_POST['btnEdit'])) {
    $id = $_POST['id'];
    $ma_tau = trim($_POST['ma_tau']);
    $ten_tau = trim($_POST['ten_tau']);

    if (empty($ma_tau) || empty($ten_tau)) {
        $error_message = "Vui lòng nhập đầy đủ mã tàu và tên tàu!";
    } else {
        try {
            $sql_check = "SELECT id FROM tau WHERE ma_tau = ? AND id <> ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->execute([$ma_tau, $id]);

            if ($stmt_check->rowCount() > 0) {
                $error_message = "Mã tàu '$ma_tau' đã tồn tại ở một tàu khác! Vui lòng chọn mã khác.";
            } else {
                $sql_update = "UPDATE tau SET ma_tau = ?, ten_tau = ? WHERE id = ?";
                $stmt_update = $conn->prepare($sql_update);
                
                if ($stmt_update->execute([$ma_tau, $ten_tau, $id])) {
                    echo "<script>alert('Cập nhật thông tin tàu thành công!'); window.location='index.php';</script>";
                } else {
                    $error_message = "Cập nhật thất bại! Vui lòng thử lại.";
                }
            }
        } catch (PDOException $e) {
            $error_message = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật thông tin Tàu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .container { max-width: 600px; margin-top: 50px; }
        .form-container {
            background-color: #81aad3ff; 
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h2 { color: #2c3e50; text-align: center; margin-bottom: 30px; font-weight: 700; }
        .form-label { font-weight: 600; color: #34495e; }
        .btn-container { display: flex; justify-content: center; gap: 20px; margin-top: 30px; }
        .btn-custom { min-width: 150px; padding: 10px 25px; font-weight: 600; }
        .required::after { content: " *"; color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>CẬP NHẬT THÔNG TIN TÀU</h2>
            
            <?php if(!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <form method="post" action="">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                
                <div class="mb-3">
                    <label for="ma_tau" class="form-label required">Mã Tàu:</label>
                    <input type="text" class="form-control" name="ma_tau" 
                           value="<?php echo htmlspecialchars($ma_tau); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="ten_tau" class="form-label required">Tên Tàu:</label>
                    <input type="text" class="form-control" name="ten_tau" 
                           value="<?php echo htmlspecialchars($ten_tau); ?>" required>
                </div>
                
                <div class="btn-container">
                    <button type="submit" class="btn btn-primary btn-custom" name="btnEdit">
                        <i class="bi bi-save"></i> Lưu thay đổi
                    </button>
                    <a href="index.php" class="btn btn-secondary btn-custom">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?php require_once '../../includes/footer.php'; ?>
</html>