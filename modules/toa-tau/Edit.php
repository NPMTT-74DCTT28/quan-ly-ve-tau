<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__. '/../../includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

$conn = $db->getConnection();

// Lấy dữ liệu dropdown
$tau_list = $conn->query("SELECT id, ma_tau, ten_tau FROM tau ORDER BY ma_tau")->fetchAll(PDO::FETCH_ASSOC);
$loai_toa_list = $conn->query("SELECT id, ten_loai FROM loai_toa ORDER BY ten_loai")->fetchAll(PDO::FETCH_ASSOC);

$error_message = '';

// Kiểm tra ID
if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = $_GET['id'];

// Lấy dữ liệu hiện tại
$stmt = $conn->prepare("SELECT * FROM toa_tau WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) { echo "<script>alert('Toa tàu không tồn tại!'); window.location='index.php';</script>"; exit; }

$ma_toa      = $row['ma_toa'];
$id_tau      = $row['id_tau'];
$id_loai_toa = $row['id_loai_toa'];

// Xử lý cập nhật
if (isset($_POST['btnEdit'])) {
    $ma_toa_new      = trim($_POST['ma_toa']);
    $id_tau_new      = $_POST['id_tau'];
    $id_loai_toa_new = $_POST['id_loai_toa'];

    if (empty($ma_toa_new) || empty($id_tau_new) || empty($id_loai_toa_new)) {
        $error_message = "Vui lòng nhập đầy đủ thông tin!";
    } else {
        // Check trùng: Mã toa + ID Tàu trùng, NHƯNG trừ ID hiện tại ra
        $sql_check = "SELECT COUNT(*) FROM toa_tau WHERE ma_toa = ? AND id_tau = ? AND id <> ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([$ma_toa_new, $id_tau_new, $id]);

        if ($stmt_check->fetchColumn() > 0) {
            $error_message = "Mã toa '$ma_toa_new' đã tồn tại trên tàu đã chọn!";
        } else {
            $sql_update = "UPDATE toa_tau SET ma_toa = ?, id_tau = ?, id_loai_toa = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            if ($stmt_update->execute([$ma_toa_new, $id_tau_new, $id_loai_toa_new, $id])) {
                echo "<script>alert('Cập nhật thành công!'); window.location='index.php';</script>";
                exit;
            } else {
                $error_message = "Có lỗi xảy ra!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật toa tàu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .custom-card {
            background-color: #81aad3ff;
            border-radius: 15px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.15);
            max-width: 700px; margin: 50px auto;
        }
        h3 { color: #1f3a5a; font-weight: 600; }
        .form-label { font-weight: 600; }
    </style>
</head>
<body>
<div class="container">
    <div class="card custom-card p-4">
        <h3 class="text-center mb-4">CẬP NHẬT TOA TÀU</h3>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($error_message) ?>
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Mã toa</label>
                <input type="text" class="form-control" name="ma_toa" value="<?= htmlspecialchars($ma_toa) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Thuộc tàu</label>
                <select class="form-control" name="id_tau" required>
                    <option value="">-- Chọn tàu --</option>
                    <?php foreach ($tau_list as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= $id_tau == $t['id'] ? 'selected' : '' ?>>
                            <?= $t['ma_tau'] . ' - ' . $t['ten_tau'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Loại toa</label>
                <select class="form-control" name="id_loai_toa" required>
                    <option value="">-- Chọn loại toa --</option>
                    <?php foreach ($loai_toa_list as $l): ?>
                        <option value="<?= $l['id'] ?>" <?= $id_loai_toa == $l['id'] ? 'selected' : '' ?>>
                            <?= $l['ten_loai'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="text-center mt-4">
                <button class="btn btn-primary" name="btnEdit">Cập nhật</button>
                <a href="index.php" class="btn btn-secondary">Quay lại</a>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php 
require_once __DIR__. '/../../includes/footer.php'; ?>