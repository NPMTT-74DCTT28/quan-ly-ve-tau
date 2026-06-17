<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

$conn = $db->getConnection();

// Khởi tạo các biến lưu thông báo cho Cách 2
$msg = "";
$msg_type = "";

// 1. Lấy dữ liệu cũ để hiển thị lên Form
$current_ga = null;
if (isset($_GET['ma_ga'])) {
    $ma_edit = trim($_GET['ma_ga']);
    $stmt = $conn->prepare("SELECT * FROM ga_tau WHERE ma_ga = ?");
    $stmt->execute([$ma_edit]);
    $current_ga = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$current_ga) {
        header("Location: index.php");
        exit();
    }
}

if (isset($_POST['btnsua'])) {
    $ma  = trim($_POST['txtmaga']); 
    $ten = trim($_POST['txttenga']);
    $dc  = trim($_POST['txtdiachi']);
    $tp  = trim($_POST['txtthanhpho']);

    if (empty($ma) || empty($ten) || empty($dc) || empty($tp)) {
        $msg = "Vui lòng điền đầy đủ tất cả các trường thông tin!";
        $msg_type = "error";
    } else {
        try {
            $sql = "UPDATE ga_tau SET ten_ga = ?, dia_chi = ?, thanh_pho = ? WHERE ma_ga = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$ten, $dc, $tp, $ma]);
            
            $msg = "Cập nhật thông tin ga tàu thành công!";
            $msg_type = "success";
            
            // Cập nhật lại mảng $current_ga để giao diện hiển thị ngay dữ liệu mới vừa sửa
            $current_ga['ten_ga'] = $ten;
            $current_ga['dia_chi'] = $dc;
            $current_ga['thanh_pho'] = $tp;
            
        } catch (PDOException $e) {
            $msg = "Lỗi cập nhật: " . $e->getMessage();
            $msg_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sửa Ga</title>
    <link rel="stylesheet" href="../../modules/ga-tau/stylethemga.css">
</head>
<body>

<div class="add-wrapper">
    <h2 class="add-title">SỬA GA</h2>

    <?php if (!empty($msg)): ?>
        <div class="alert-box <?= $msg_type ?>" style="padding: 12px; margin-bottom: 20px; border-radius: 4px; text-align: center; font-weight: bold; <?= $msg_type === 'success' ? 'background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;' : 'background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;' ?>">
            <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="add-form">

        <div class="form-row">
            <label>Mã Ga</label>
            <input type="text" name="txtmaga" value="<?= htmlspecialchars($current_ga['ma_ga'] ?? '') ?>" required readonly style="background-color: #e9ecef; cursor: not-allowed;">
        </div>

        <div class="form-row">
            <label>Tên Ga</label>
            <input type="text" name="txttenga" value="<?= htmlspecialchars($current_ga['ten_ga'] ?? '') ?>" required>
        </div>

        <div class="form-row">
            <label>Địa chỉ</label>
            <input type="text" name="txtdiachi" value="<?= htmlspecialchars($current_ga['dia_chi'] ?? '') ?>" required>
        </div>

        <div class="form-row">
            <label>Thành phố</label>
            <input type="text" name="txtthanhpho" value="<?= htmlspecialchars($current_ga['thanh_pho'] ?? '') ?>" required>
        </div>

        <div class="form-actions">
            <button type="submit" name="btnsua" class="btn-submit">Sửa Ga</button>
        </div>

    </form>
</div>

</body>
</html>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>