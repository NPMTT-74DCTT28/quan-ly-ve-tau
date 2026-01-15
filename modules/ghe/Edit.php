<?php
require_once __DIR__ . '/../../bootstrap.php';

requireLogin();
requireAdmin();

$conn = $db->getConnection();

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

// 1. Lấy thông tin ghế hiện tại
$stmt_ghe = $conn->prepare("SELECT * FROM ghe WHERE id = ?");
$stmt_ghe->execute([$id]);
$ghe = $stmt_ghe->fetch();

if (!$ghe) {
    header('Location: index.php');
    exit;
}

// 2. Lấy danh sách toa
$toa_list = $conn->query("SELECT toa_tau.id, toa_tau.ma_toa, tau.ten_tau 
                          FROM toa_tau 
                          INNER JOIN tau ON toa_tau.id_tau = tau.id")->fetchAll();

$error_message = '';

// 3. Xử lý lưu thay đổi
if (isset($_POST['btnEdit'])) {
    $so_ghe = trim($_POST['so_ghe']);
    $id_toa_tau = $_POST['id_toa_tau'];

    if (empty($so_ghe) || empty($id_toa_tau)) {
        $error_message = "Vui lòng nhập đầy đủ thông tin!";
    } else {
        try {
            // Check trùng
            $sql_check = "SELECT id FROM ghe WHERE so_ghe = ? AND id_toa_tau = ? AND id <> ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->execute([$so_ghe, $id_toa_tau, $id]);

            if ($stmt_check->rowCount() > 0) {
                $error_message = "Số ghế '$so_ghe' đã tồn tại trong toa tàu này rồi!";
            } else {
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

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container" style="padding: 20px; max-width: 800px; margin: 0 auto;">
    <h2>Sửa Thông Tin Ghế</h2>

    <?php if ($error_message): ?>
        <div style="color: red; background: #f8d7da; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
            <?= $error_message ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <input type="hidden" name="id" value="<?= $ghe['id'] ?>">

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Thuộc Toa Tàu (*)</label>
            <select name="id_toa_tau" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;">
                <?php foreach ($toa_list as $toa): ?>
                    <option value="<?= $toa['id'] ?>" <?= $toa['id'] == $ghe['id_toa_tau'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($toa['ma_toa'] . " (" . $toa['ten_tau'] . ")") ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label>Số Ghế (*)</label>
            <input type="text" name="so_ghe" class="form-control"
                value="<?= htmlspecialchars($ghe['so_ghe']) ?>" required
                style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <button type="submit" name="btnEdit" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Lưu thay đổi</button>
        <a href="index.php" style="margin-left: 10px; color: #666; text-decoration: none;">Hủy bỏ</a>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>