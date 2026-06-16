<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

$conn = $db->getConnection();

// Khởi tạo các biến lưu thông báo
$msg = "";
$msg_type = "";

if (isset($_POST['btnthem'])) {
    $ma  = trim($_POST['txtmaga']);
    $ten = trim($_POST['txttenga']);
    $dc  = trim($_POST['txtdiachi']);
    $tp  = trim($_POST['txtthanhpho']);

    // Kiểm tra dữ liệu đầu vào cơ bản (Tránh gửi chuỗi rỗng)
    if (empty($ma) || empty($ten) || empty($dc) || empty($tp)) {
        $msg = "Vui lòng điền đầy đủ tất cả các trường thông tin!";
        $msg_type = "error";
    } else {
        $sql = "INSERT INTO ga_tau (ma_ga, ten_ga, dia_chi, thanh_pho) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        try {
            $stmt->execute([$ma, $ten, $dc, $tp]);
            $msg = "Thêm ga tàu thành công!";
            $msg_type = "success";
            
            // Xóa dữ liệu cũ sau khi thêm thành công để người dùng không bấm lặp lại
            $ma = $ten = $dc = $tp = ""; 
        } catch (PDOException $e) {
            $msg = "Lỗi: Không thể thêm ga. Có thể mã ga đã tồn tại!";
            $msg_type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Ga</title>
    <link rel="stylesheet" href="../../modules/ga-tau/stylethemga.css">
</head>
<body>

<div class="add-wrapper">

    <h2 class="add-title">THÊM GA</h2>

    <?php if (!empty($msg)): ?>
        <div class="alert-box <?= $msg_type ?>" style="padding: 12px; margin-bottom: 20px; border-radius: 4px; text-align: center; font-weight: bold; <?= $msg_type === 'success' ? 'background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;' : 'background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;' ?>">
            <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="add-form">

        <div class="form-row">
            <label>Mã Ga</label>
            <input type="text" name="txtmaga" value="<?= isset($ma) ? htmlspecialchars($ma) : '' ?>" required>
        </div>

        <div class="form-row">
            <label>Tên Ga</label>
            <input type="text" name="txttenga" value="<?= isset($ten) ? htmlspecialchars($ten) : '' ?>" required>
        </div>

        <div class="form-row">
            <label>Địa chỉ</label>
            <input type="text" name="txtdiachi" value="<?= isset($dc) ? htmlspecialchars($dc) : '' ?>" required>
        </div>

        <div class="form-row">
            <label>Thành phố</label>
            <input type="text" name="txtthanhpho" value="<?= isset($tp) ? htmlspecialchars($tp) : '' ?>" required>
        </div>

        <div class="form-actions">
            <button type="submit" name="btnthem" class="btn-submit">
                Thêm Ga
            </button>
        </div>

    </form>

</div>

</body>
</html>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>