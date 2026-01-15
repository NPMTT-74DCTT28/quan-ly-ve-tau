<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

requireLogin();
requireAdmin();

$conn = $db->getConnection();

// Lấy dữ liệu dropdown
$tau_list = $conn->query("SELECT id, ma_tau, ten_tau FROM tau ORDER BY ma_tau")->fetchAll(PDO::FETCH_ASSOC);
$loai_toa_list = $conn->query("SELECT id, ten_loai FROM loai_toa ORDER BY ten_loai")->fetchAll(PDO::FETCH_ASSOC);

$error_message = '';

// Kiểm tra ID
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$id = $_GET['id'];

// Lấy dữ liệu hiện tại
$stmt = $conn->prepare("SELECT * FROM toa_tau WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo "<script>alert('Toa tàu không tồn tại!'); window.location='index.php';</script>";
    exit;
}

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

<div class="main-content">
    <h1>CẬP NHẬT TOA TÀU</h1>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

        <div class="row" style="display: flex; flex-wrap: wrap; margin-right: -15px; margin-left: -15px;">
            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%; padding-right: 15px; padding-left: 15px; box-sizing: border-box;">
                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Mã toa:</label>
                    <input type="text" class="form-control" name="ma_toa" value="<?= htmlspecialchars($ma_toa) ?>" required
                        style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Loại toa:</label>
                    <select class="form-control" name="id_loai_toa" required style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                        <option value="">-- Chọn loại toa --</option>
                        <?php foreach ($loai_toa_list as $l): ?>
                            <option value="<?= $l['id'] ?>" <?= $id_loai_toa == $l['id'] ? 'selected' : '' ?>>
                                <?= $l['ten_loai'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%; padding-right: 15px; padding-left: 15px; box-sizing: border-box;">
                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Thuộc tàu:</label>
                    <select class="form-control" name="id_tau" required style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                        <option value="">-- Chọn tàu --</option>
                        <?php foreach ($tau_list as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= $id_tau == $t['id'] ? 'selected' : '' ?>>
                                <?= $t['ma_tau'] . ' - ' . $t['ten_tau'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <button type="submit" name="btnEdit" style="background: #007bff; color: white; padding: 10px 25px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">
                <i class="bi bi-save"></i> Cập nhật
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