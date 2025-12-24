<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__. '/../../includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

requireAdmin();

$conn = $db->getConnection();

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: index.php'); exit; }

// 1. Lấy thông tin ghế hiện tại
$stmt_ghe = $conn->prepare("SELECT * FROM ghe WHERE id = ?");
$stmt_ghe->execute([$id]);
$ghe = $stmt_ghe->fetch();

if (!$ghe) { header('Location: index.php'); exit; }

// 2. Lấy danh sách toa để người dùng có thể đổi toa cho ghế
$toa_list = $conn->query("SELECT id, ma_toa FROM toa_tau")->fetchAll();

$error_message = '';

// 3. Xử lý khi nhấn nút Lưu thay đổi
if (isset($_POST['btnEdit'])) {
    $so_ghe = trim($_POST['so_ghe']);
    $id_toa_tau = $_POST['id_toa_tau'];

    if (empty($so_ghe) || empty($id_toa_tau)) {
        $error_message = "Vui lòng nhập đầy đủ thông tin!";
    } else {
        try {
            // LOGIC CHECK TRÙNG:
            // Tìm xem có ghế nào trùng số ghế VÀ trùng toa tàu, nhưng ID phải KHÁC ID đang sửa
            $sql_check = "SELECT id FROM ghe WHERE so_ghe = ? AND id_toa_tau = ? AND id <> ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->execute([$so_ghe, $id_toa_tau, $id]);

            if ($stmt_check->rowCount() > 0) {
                $error_message = "Số ghế '$so_ghe' đã tồn tại trong toa tàu này rồi!";
            } else {
                // Cập nhật dữ liệu
                $sql = "UPDATE ghe SET so_ghe = ?, id_toa_tau = ? WHERE id = ?";
                if ($conn->prepare($sql)->execute([$so_ghe, $id_toa_tau, $id])) {
                    echo "<script>alert('Cập nhật thành công!'); window.location='index.php';</script>";
                    exit;
                }
            }
        } catch (PDOException $e) {
            $error_message = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Thông Tin Ghế</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card p-4 shadow" style="background-color: #81aad3ff;">
            <h2 class="text-center mb-4 text-white">SỬA THÔNG TIN GHẾ</h2>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?= $error_message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="id" value="<?= $ghe['id'] ?>">
                
                <div class="mb-3">
                    <label class="form-label fw-bold text-white">Thuộc Toa Tàu:</label>
                    <select name="id_toa_tau" class="form-select" required>
                        <?php foreach($toa_list as $toa): ?>
                            <option value="<?= $toa['id'] ?>" <?= $toa['id'] == $ghe['id_toa_tau'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($toa['ma_toa']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-white">Số Ghế:</label>
                    <input type="text" name="so_ghe" class="form-control" 
                           value="<?= htmlspecialchars($ghe['so_ghe']) ?>" required>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" name="btnEdit" class="btn btn-primary px-4">
                        <i class="bi bi-save"></i> Lưu thay đổi
                    </button>
                    <a href="index.php" class="btn btn-light px-4">Hủy</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>