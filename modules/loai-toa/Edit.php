<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__. '/../../includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

$conn = $db->getConnection();

$id = $ten_loai = $he_so_gia = '';
$error_message = '';

// 1. LẤY THÔNG TIN CŨ ĐỂ ĐỔ VÀO FORM
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql_select = "SELECT * FROM loai_toa WHERE id = ?";
    $stmt = $conn->prepare($sql_select);
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        $ten_loai = $row['ten_loai'];
        $he_so_gia = $row['he_so_gia'];
    } else {
        echo "<script>alert('Không tìm thấy thông tin loại toa!'); window.location='index.php';</script>";
        exit;
    }
} else {
    header('location:index.php');
    exit;
}

// 2. XỬ LÝ KHI NGƯỜI DÙNG NHẤN NÚT LƯU THAY ĐỔI
if (isset($_POST['btnEdit'])) {
    $id = $_POST['id'];
    $ten_loai = trim($_POST['ten_loai']);
    $he_so_gia = trim($_POST['he_so_gia']);

    if (empty($ten_loai) || empty($he_so_gia)) {
        $error_message = "Vui lòng nhập đầy đủ tên loại và hệ số giá!";
    } else {
        try {
            // Kiểm tra xem tên loại mới có bị trùng với loại khác đã có trong DB không
            $sql_check = "SELECT id FROM loai_toa WHERE ten_loai = ? AND id <> ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->execute([$ten_loai, $id]);

            if ($stmt_check->rowCount() > 0) {
                $error_message = "Tên loại '$ten_loai' đã tồn tại! Vui lòng chọn tên khác.";
            } else {
                // Cập nhật vào bảng loai_toa
                $sql_update = "UPDATE loai_toa SET ten_loai = ?, he_so_gia = ? WHERE id = ?";
                $stmt_update = $conn->prepare($sql_update);
                
                if ($stmt_update->execute([$ten_loai, $he_so_gia, $id])) {
                    echo "<script>alert('Cập nhật thông tin thành công!'); window.location='index.php';</script>";
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
    <title>Cập nhật Loại Toa</title>
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
            <h2>CẬP NHẬT LOẠI TOA</h2>
            
            <?php if(!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <form method="post" action="">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                
                <div class="mb-3">
                    <label for="ten_loai" class="form-label required">Tên Loại:</label>
                    <input type="text" class="form-control" name="ten_loai" 
                           value="<?php echo htmlspecialchars($ten_loai); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="he_so_gia" class="form-label required">Hệ Số Giá:</label>
                    <input type="number" step="0.01" class="form-control" name="he_so_gia" 
                           value="<?php echo htmlspecialchars($he_so_gia); ?>" required>
                </div>
                
                <div class="btn-container">
                    <button type="submit" class="btn btn-primary btn-custom" name="btnEdit">
                        <i class="bi bi-save"></i> Lưu thay đổi