<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__. '/../../includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

requireAdmin();

$conn = $db->getConnection();

// Lấy dữ liệu dropdown
$tau_list = $conn->query("SELECT id, ma_tau, ten_tau FROM tau ORDER BY ma_tau")->fetchAll(PDO::FETCH_ASSOC);
$loai_toa_list = $conn->query("SELECT id, ten_loai FROM loai_toa ORDER BY ten_loai")->fetchAll(PDO::FETCH_ASSOC);

$ma_toa = $id_tau = $id_loai_toa = '';
$error_message = $success_message = '';

if (isset($_POST['btnAdd'])) {
    $ma_toa      = trim($_POST['ma_toa']);
    $id_tau      = $_POST['id_tau'];
    $id_loai_toa = $_POST['id_loai_toa'];

    if (empty($ma_toa) || empty($id_tau) || empty($id_loai_toa)) {
        $error_message = "Vui lòng nhập đầy đủ thông tin!";
    } else {
        // KIỂM TRA TRÙNG: (Mã toa + ID Tàu) phải là duy nhất
        $stmt_check = $conn->prepare("SELECT COUNT(*) FROM toa_tau WHERE ma_toa = ? AND id_tau = ?");
        $stmt_check->execute([$ma_toa, $id_tau]);
        
        if ($stmt_check->fetchColumn() > 0) {
            $error_message = "Mã toa '$ma_toa' đã tồn tại trên con tàu này rồi!";
        } else {
            $stmt = $conn->prepare("INSERT INTO toa_tau (ma_toa, id_tau, id_loai_toa) VALUES (?, ?, ?)");
            if ($stmt->execute([$ma_toa, $id_tau, $id_loai_toa])) {
                $success_message = "Thêm toa tàu thành công!";
                $ma_toa = $id_tau = $id_loai_toa = ''; // Reset form
            } else {
                $error_message = "Lỗi hệ thống, vui lòng thử lại!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm toa tàu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7fb; }
        .form-box {
            max-width: 700px; margin: 50px auto;
            background-color: #81aad3ff; padding: 35px;
            border-radius: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; font-weight: 700; color: #1f3a5a; margin-bottom: 20px; }
        .form-label { font-weight: 600; }
    </style>
</head>
<body>
<div class="container">
    <div class="form-box">
        <h2>THÊM TOA TÀU MỚI</h2>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Mã toa <span class="text-danger">*</span></label>
                <input type="text" name="ma_toa" class="form-control" value="<?= htmlspecialchars($ma_toa) ?>" placeholder="VD: Toa 01, Toa A">
            </div>

            <div class="mb-3">
                <label class="form-label">Thuộc tàu <span class="text-danger">*</span></label>
                <select name="id_tau" class="form-select">
                    <option value="">-- Chọn tàu --</option>
                    <?php foreach ($tau_list as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= $id_tau == $t['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['ma_tau'] . ' - ' . $t['ten_tau']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-white">Lưu ý: Dữ liệu này lấy từ bảng 'tau'</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Loại toa <span class="text-danger">*</span></label>
                <select name="id_loai_toa" class="form-select">
                    <option value="">-- Chọn loại toa --</option>
                    <?php foreach ($loai_toa_list as $l): ?>
                        <option value="<?= $l['id'] ?>" <?= $id_loai_toa == $l['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($l['ten_loai']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-white">Lưu ý: Dữ liệu này lấy từ bảng 'loai_toa'</small>
            </div>

            <div class="text-center mt-4">
                <button name="btnAdd" class="btn btn-primary px-4">Thêm mới</button>
                <a href="index.php" class="btn btn-secondary px-4">Quay lại</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
<?php 
require_once __DIR__. '/../../includes/footer.php'; ?>