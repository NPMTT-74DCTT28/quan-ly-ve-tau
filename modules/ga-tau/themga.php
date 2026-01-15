<?php
require_once __DIR__ . '/../../bootstrap.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

requireAdmin(); 

require_once __DIR__ . '/../../includes/header.php';

$conn = $db->getConnection();
$error_message = '';
$success_message = '';
$ma = '';
$ten = '';
$dc = '';
$tp = '';

if (isset($_POST['btnthem'])) {
    $ma = trim($_POST['txtmaga']);
    $ten = trim($_POST['txttenga']);
    $dc = trim($_POST['txtdiachi']);
    $tp = trim($_POST['txtthanhpho']);

    if (empty($ma) || empty($ten) || empty($dc) || empty($tp)) {
        $error_message = "Vui lòng điền đầy đủ thông tin!";
    } else {
        $sql_check = "SELECT ma_ga FROM ga_tau WHERE ma_ga = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([$ma]);

        if ($stmt_check->rowCount() > 0) {
            $error_message = "Mã ga '$ma' đã tồn tại!";
        } else {
            $sql = "INSERT INTO ga_tau (ma_ga, ten_ga, dia_chi, thanh_pho) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt->execute([$ma, $ten, $dc, $tp])) {
                $success_message = "Thêm ga tàu thành công!";
                $ma = $ten = $dc = $tp = '';
            } else {
                $error_message = "Lỗi hệ thống, vui lòng thử lại sau.";
            }
        }
    }
}
?>

<div class="container" style="padding: 20px; max-width: 800px; margin: 0 auto;">
    <h2>Thêm Ga Mới</h2>

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
            <label>Mã Ga (*)</label>
            <input type="text" name="txtmaga" class="form-control"
                value="<?= htmlspecialchars($ma) ?>" required
                style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Tên Ga (*)</label>
            <input type="text" name="txttenga" class="form-control"
                value="<?= htmlspecialchars($ten) ?>" required
                style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Địa chỉ (*)</label>
            <input type="text" name="txtdiachi" class="form-control"
                value="<?= htmlspecialchars($dc) ?>" required
                style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label>Thành phố (*)</label>
            <input type="text" name="txtthanhpho" class="form-control"
                value="<?= htmlspecialchars($tp) ?>" required
                style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <button type="submit" name="btnthem" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Thêm Ga</button>
        <a href="index.php" style="margin-left: 10px; color: #666; text-decoration: none;">Quay lại</a>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>