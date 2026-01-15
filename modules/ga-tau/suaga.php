<?php
require_once __DIR__ . '/../../bootstrap.php';

requireLogin();
requireAdmin();

require_once __DIR__ . '/../../includes/header.php';

$conn = $db->getConnection();
$error_message = '';
$success_message = '';

// 1. Lấy dữ liệu cũ
$current_ga = null;
if (isset($_GET['ma_ga'])) {
    $ma_edit = $_GET['ma_ga'];
    $stmt = $conn->prepare("SELECT * FROM ga_tau WHERE ma_ga = ?");
    $stmt->execute([$ma_edit]);
    $current_ga = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$current_ga) {
        header("Location: index.php");
        exit();
    }
}

// 2. Xử lý Sửa
if (isset($_POST['btnsua'])) {
    $ma  = $_POST['txtmaga'];
    $ten = trim($_POST['txttenga']);
    $dc  = trim($_POST['txtdiachi']);
    $tp  = trim($_POST['txtthanhpho']);

    if (empty($ten) || empty($dc) || empty($tp)) {
        $error_message = "Vui lòng không để trống thông tin!";
    } else {
        try {
            $sql = "UPDATE ga_tau SET ten_ga = ?, dia_chi = ?, thanh_pho = ? WHERE ma_ga = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$ten, $dc, $tp, $ma]);

            $success_message = "Cập nhật thành công!";
            // Cập nhật lại dữ liệu hiển thị
            $current_ga['ten_ga'] = $ten;
            $current_ga['dia_chi'] = $dc;
            $current_ga['thanh_pho'] = $tp;
        } catch (PDOException $e) {
            $error_message = "Lỗi cập nhật: " . $e->getMessage();
        }
    }
}
?>

<div class="container" style="padding: 20px; max-width: 800px; margin: 0 auto;">
    <h2>Sửa Thông Tin Ga</h2>

    <?php if ($error_message): ?>
        <div style="color: red; background: #f8d7da; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
            <?= $error_message ?>
        </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div style="color: #155724; background-color: #d4edda; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
            <?= $success_message ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Mã Ga (Không thể sửa)</label>
            <input type="text" name="txtmaga" class="form-control"
                value="<?= htmlspecialchars($current_ga['ma_ga']) ?>"
                readonly style="width: 100%; padding: 8px; margin-top: 5px; background-color: #e9ecef;">
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Tên Ga</label>
            <input type="text" name="txttenga" class="form-control"
                value="<?= htmlspecialchars($current_ga['ten_ga']) ?>" required
                style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Địa chỉ</label>
            <input type="text" name="txtdiachi" class="form-control"
                value="<?= htmlspecialchars($current_ga['dia_chi']) ?>" required
                style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label>Thành phố</label>
            <input type="text" name="txtthanhpho" class="form-control"
                value="<?= htmlspecialchars($current_ga['thanh_pho']) ?>" required
                style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <button type="submit" name="btnsua" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Cập nhật</button>
        <a href="index.php" style="margin-left: 10px; color: #666; text-decoration: none;">Quay lại</a>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>