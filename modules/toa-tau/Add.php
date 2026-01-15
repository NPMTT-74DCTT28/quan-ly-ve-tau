<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

requireLogin();
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

<div class="main-content">
    <h1>THÊM TOA TÀU MỚI</h1>

    <?php if ($error_message): ?>
        <div class="alert alert-danger" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="alert alert-success" style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($success_message) ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="row" style="display: flex; flex-wrap: wrap; margin-right: -15px; margin-left: -15px;">
            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%; padding-right: 15px; padding-left: 15px; box-sizing: border-box;">
                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Mã toa (*):</label>
                    <input type="text" name="ma_toa" value="<?= htmlspecialchars($ma_toa) ?>" placeholder="VD: Toa 01, Toa A"
                        class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Loại toa (*):</label>
                    <select name="id_loai_toa" class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                        <option value="">-- Chọn loại toa --</option>
                        <?php foreach ($loai_toa_list as $l): ?>
                            <option value="<?= $l['id'] ?>" <?= $id_loai_toa == $l['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($l['ten_loai']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%; padding-right: 15px; padding-left: 15px; box-sizing: border-box;">
                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Thuộc tàu (*):</label>
                    <select name="id_tau" class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                        <option value="">-- Chọn tàu --</option>
                        <?php foreach ($tau_list as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= $id_tau == $t['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['ma_tau'] . ' - ' . $t['ten_tau']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <button type="submit" name="btnAdd" style="background: #28a745; color: white; padding: 10px 25px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">
                <i class="bi bi-plus-circle"></i> Thêm mới
            </button>
            <a href="index.php" style="margin-left: 15px; color: #333; text-decoration: none; padding: 10px 20px; background: #e2e6ea; border-radius: 4px; display: inline-block;">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </form>
</div>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>